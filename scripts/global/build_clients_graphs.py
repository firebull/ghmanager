#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Generate load graphs of clients servers.
Copyright (C) 2013 Nikita Bulaev

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.
***********************************************
'''

'''
Скрипт генерации графиков загруженности серверов игроками

1) Получить список привязанных к физическому серверу игровых серверов.
   Они же должны быть оплачены и инициализированы.
2) По ID сервера определить наличие базы RRD
3) Если есть, то генерировать из неё график. Имя файла закодировать с помощью MD5.

'''

import MySQLdb
import os
import sys
sys.path.append("/images/scripts/global")
from db_queries import *
from common import *
import string
from subprocess import *
from time import sleep
from flock import flock
from datetime import datetime, date, time, timedelta
import rrdtool
import md5
import ConfigParser

config = ConfigParser.RawConfigParser()
config.read('/etc/hosting/scripts.cfg')

# ID сервера, на котором пускается скрипт
# Брать его из поля id в админке

thisServerId = config.getint('server', 'serverID')
mysqlHost = config.get('db', 'host')
mysqlUser = config.get('db', 'user')
mysqlPass = config.get('db', 'pass')
mysqlDb = config.get('db', 'db')

# Blocking the process
lock = flock('servers_gen_graphs.lock', True).acquire()

if lock:
    # host, user, pass, db
    db = MySQLdb.connect(host=mysqlHost, user=mysqlUser, passwd=mysqlPass, db=mysqlDb)

    # Create cursor with row names as array arguments
    cursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)
    commonCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)

    servers = getRootServerPayedServers(cursor, thisServerId)

    numrows = int(servers.rowcount)

    logPrint('OK', 'Получены данные на %s серверов' % numrows)

    rrdDbPath = "/images/stat/bases/"
    graphsPath = "/images/stat/graphs/"
    graphKey = 'sjadKJHQWdhhwoepf'

    # Создать директорию для картинок, если её ещё нет
    if not os.path.exists(graphsPath):
        os.makedirs(graphsPath)
        os.chmod(graphsPath, 0755)
        logPrint('OK', 'Директория под графики создана')

    for x in range(0, numrows):

        server = servers.fetchone()
        serverId = str(server['id'])

        rrdDb = rrdDbPath + serverId + '.rrd'

        # Дальнейшие танцы, только если база существует
        if os.path.exists(rrdDb):

            period = "24h"

            m = md5.new()
            # Имя файла будет строка из секретного ключа + ID сервера + период,
            # закодированные в MD5
            m.update(graphKey + serverId + period)

            graphName = graphsPath + m.hexdigest() + '.png'

            ret = rrdtool.graph(graphName, "--start", "-" + period,  # "--vertical-label=Игроков",
                                "--color=BACK#fdfdfd", "--color=FONT#020202", "--color=AXIS#970405",
                                "--color=SHADEA#555", "--color=SHADEB#555",
                                "--x-grid=HOUR:2:HOUR:8:HOUR:8:0:%H:%M", "--alt-y-grid", "--border=1",
                                "--full-size-mode", "--width=260", "--height=130",
                                "DEF:players=" + rrdDb + ":players:AVERAGE",
                                # "CDEF:plrs=players,UN,0,players,IF", # Если данных нет, считать их 0. Тут ошибка, надо разобраться.
                                "CDEF:plrs=players",
                                "AREA:plrs#FF8214",
                                "LINE:plrs#FF8214",
                                "COMMENT:      ",
                                "GPRINT:plrs:LAST:Сейчас\: %3.0lf чел.",
                                #"GPRINT:plrs:AVERAGE:В среднем\: %3.0lf чел.",
                                "GPRINT:plrs:MAX:Макс\: %3.0lf чел.\\r"
                                )

            period = "7d"

            m = md5.new()
            # Имя файла будет строка из секретного ключа + ID сервера + период,
            # закодированные в MD5
            m.update(graphKey + serverId + period)

            graphName = graphsPath + m.hexdigest() + '.png'

            ret = rrdtool.graph(graphName, "--start", "-" + period,  # "--vertical-label=Игроков",
                                "--color=BACK#fdfdfd", "--color=FONT#020202", "--color=AXIS#970405",
                                "--color=SHADEA#555", "--color=SHADEB#555",
                                "--x-grid=HOUR:8:DAY:1:DAY:2:86400:%d.%m", "--alt-y-grid", "--border=1",
                                "--full-size-mode", "--width=260", "--height=130",
                                "DEF:players=" + rrdDb + ":players:AVERAGE",
                                # "CDEF:plrs=players,UN,0,players,IF", # Если данных нет, считать их 0. Тут ошибка, надо разобраться.
                                "CDEF:plrs=players",
                                "AREA:plrs#FF8214",
                                "LINE:plrs#FF8214",
                                "COMMENT:      ",
                                "GPRINT:plrs:LAST:Сейчас\: %3.0lf чел.",
                                #"GPRINT:plrs:AVERAGE:В среднем\: %3.0lf чел.",
                                "GPRINT:plrs:MAX:Макс\: %3.0lf чел.\\r"
                                )
