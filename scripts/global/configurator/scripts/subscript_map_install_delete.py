#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Read/write maps names to configs.
Runs maps_install.py with clients rights.
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

print "Content-Type: text/xml"     # XML is following
print                               # blank line, end of headers
print '<?xml version = "1.0" encoding="UTF-8"?>'
print '<response>'

import MySQLdb
import ConfigParser
import cgi
# import cgitb
import os
import pwd
from subprocess import *
from optparse import OptionParser
import sys
import re
from commonLib import xmlLog
sys.path.append("/images/scripts/global")
from db_queries import *

# cgitb.enable() # Debug

config = ConfigParser.RawConfigParser()
config.read('/etc/hosting/scripts.cfg')

# ID сервера, на котором пускается скрипт
# Брать его из поля id в админке

thisServerId = config.getint('server', 'serverID')
mysqlHost = config.get('db', 'host')
mysqlUser = config.get('db', 'user')
mysqlPass = config.get('db', 'pass')
mysqlDb = config.get('db', 'db')

parser = OptionParser()

parser.add_option("-s", "--server", action="store", type="int",    dest="serverID")
parser.add_option("-m", "--map",    action="store", type="int",    dest="mapID")
parser.add_option("-a", "--action", action="store", type="string", dest="action")

(options, args) = parser.parse_args(args=None, values=None)

if options.serverID:
    serverID = options.serverID
    mapID = options.mapID
    action = options.action
else:
    server = cgi.FieldStorage()
    serverID = server["id"].value
    mapID = server["map"].value
    action = server["a"].value

# Получить данные сервера из базы
db = MySQLdb.connect(host=mysqlHost, user=mysqlUser, passwd=mysqlPass, db=mysqlDb)

# Create cursor with row names as array arguments
serverCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)
commonCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)

commonCursor.execute("""SELECT `id`, `name` FROM `maps` WHERE `id` = %s""", mapID)

if int(commonCursor.rowcount) == 1:

    # Теперь данные о карте
    map = commonCursor.fetchone()

    serverCursor.execute("""SELECT * FROM servers where payedTill > NOW() AND initialised = 1 AND id = %s
                        ORDER BY `servers`.`created`  DESC LIMIT 1""", serverID)

    numrows = int(serverCursor.rowcount)

    if numrows > 0:
        xmlLog("Данные о сервере получены")

        server = serverCursor.fetchone()

        # Проверяем привязку игрового сервера к нашему физическому
        rootServer = defineRootServer(commonCursor, serverID)

        if rootServer['id'] == thisServerId:
            xmlLog("Сервер привязан на этот физический сервер. Продолжаю.")

            # Определяем пользователя
            user = defineUser(commonCursor, serverID)

            userName = "client%s" % user['id']
            homeDir = "/home/%s" % userName
            serversPath = homeDir + "/servers"

            # Теперь получим из базы шаблон сервера
            template = defineTemplate(commonCursor, serverID)

            # Тип - достаем его из шаблона:
            type = defineType(commonCursor, str(template['id']))

            xmlLog('Шаблон сервера: ' + template['name'])

            serverPath = serversPath + "/" + template['name'] + "_" + str(serverID)

            pw = pwd.getpwnam(userName)
            userUid = pw.pw_uid

            # Экранировать вероятный $ в начале названия карты
            if map['name'].startswith('$'):
                map['name'] = '\\' + map['name']

            mapPath = os.path.join('/images/maps', type['name'], template['name'], map['name'])

            # Запуск скрипта проверки от имени пользователя
            try:
                retcode = Popen("sudo -u " + userName
                                + ' ./map_install.py -a %s -s %s -m %s -n %s -i %s -t %s'
                                % (action, serverPath, mapPath, map['name'], template['addonsPath'], template['name']),
                                shell=True,
                                stdin=PIPE,
                                stdout=PIPE,
                                stderr=PIPE)
                (out, err) = retcode.communicate()
                print out
                print xmlLog(err, 'error')
                if err < 0:
                    xmlLog("Возникла ошибка: " + err, 'error')
                elif err == 0 or err == "":
                    xmlLog("Операция завершена.")

            except OSError, e:
                xmlLog("Возникла ошибка: " + e, 'error')

        else:
            xmlLog('Игровой сервер привязан к другому физическому.', 'error')

    else:
        xmlLog('Такого сервера не существует либо он заблокирован', 'error')

    serverCursor.close()

else:
    xmlLog('Я не знаю такую карту', 'error')

commonCursor.close()
db.close()

print '</response>'
