#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Additional initial script for ShoutCAST.
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


# Инициализация сервера интернет-радио.
#
# 1) Опеределение шаблона
#     Shoutcast:
#     a) Скопировать сервер в домашний каталог пользователя ( основной скрипт)
#     b) Скопировать скрипты запуска/остановки (основной скрипт)
#     c) Скопировать типовой конфиг с правкой необходимых параметров
# 2)
# 3)
# 4)
# 5)

import MySQLdb
from time import sleep
from flock import flock
import distutils.dir_util
import os
import pwd
import cgi
import cgitb
import shlex
import sys
import ConfigParser
import string
from shutil import *
from subprocess import *
from random import choice
from datetime import datetime
from optparse import OptionParser

lock = flock('radio_istall.lock', True).acquire()

if lock:
    parser = OptionParser()

    parser.add_option("-m", "--server", action="store", type="int", dest="serverID")
    parser.add_option("-u", "--user", action="store", type="string", dest="userID")
    parser.add_option("-t", "--tmp", action="store", type="string", dest="template")
    parser.add_option("-i", "--ip", action="store", type="string", dest="ip")
    parser.add_option("-p", "--port", action="store", type="string", dest="port")
    parser.add_option("-s", "--slots", action="store", type="string", dest="slots")
   # parser.add_option("-b", "--bitrate", action="store", type="string", dest="bitrate")

    (options, args) = parser.parse_args(args=None, values=None)

    serverID = str(options.serverID)
    userID = str(options.userID)
    template = options.template
    serverIp = options.ip
    serverPort = options.port
    slots = options.slots
   # bitrate = options.bitrate
    bitrate = '128'

    # Генерация рабочих переменных

    userName = "client" + userID
    serverPath = "/home/" + userName + "/servers/" + template + "_" + serverID  # Путь к серверу
    pidDir = "/home/pid/" + userName
    logDir = "/home/" + userName + "/public_html/output"
    passwordSize = 9
    adminPassword = ''.join([choice(string.letters + string.digits) for i in range(passwordSize)])

    # Теперь получим uid и gid
    pw = pwd.getpwnam(userName)
    cpw = pwd.getpwnam('configurator')
    userUid = cpw.pw_uid
    userGid = pw.pw_gid

    config = ConfigParser.RawConfigParser()
    config.read('/etc/hosting/scripts.cfg')
    # ID сервера, на котором пускается скрипт
    # Брать его из поля id в админке

    thisServerId = config.getint('server', 'serverID')
    mysqlHost = config.get('db', 'host')
    mysqlUser = config.get('db', 'user')
    mysqlPass = config.get('db', 'pass')
    mysqlDb = config.get('db', 'db')

    db = MySQLdb.connect(host=mysqlHost, user=mysqlUser, passwd=mysqlPass, db=mysqlDb)

    # Create cursor with row names as array arguments
    joinCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)
    paramsCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)

    if template == 'shoutcast':
        print "Осуществляю настройку сервера SHOUTcast"

        iniPath = "/images/scripts/servers_configs/radio/shoutcast"
        iniName = "sc_serv.conf"
        # Скрипт запуска
        try:
            if os.path.exists(serverPath + "/" + iniName):
                # сохранить резервную копию
                os.rename(serverPath + "/" + iniName, serverPath + "/" + iniName + ".dist")

            iniTemplate = open(iniPath + "/" + iniName, "r")
            iniUser = open(serverPath + "/" + iniName, "w")

            for line in iniTemplate:

                line = line.replace("%ip", str(serverIp))
                line = line.replace("%port", str(serverPort))
                line = line.replace("%user", str(userName))
                line = line.replace("%slots", str(slots))
                # Сюда прописываем пароль, чтобы случайные люди не воспользовались сервером.
                # Владелец же потом через админку установит пароль.
                line = line.replace("%pass", str(adminPassword))
                line = line.replace("%log", str(logDir + "/shoutcast_" + serverID + ".log"))
                line = line.replace("%w3log", str(logDir + "/shoutcast_w3c_" + serverID + ".log"))
                line = line.replace("%pid", str(pidDir + "/shoutcast_" + serverID + ".pid"))
                line = line.replace("%id", str(serverID))

                iniUser.write(line)

            os.chmod(serverPath + "/" + iniName, 0640)
            os.chown(serverPath + "/" + iniName, userUid, userGid)

            iniTemplate.close()
            iniUser.close()
            try:
               # Создание типового конфига в базе
                paramsCursor.execute("""INSERT INTO `teamserver`.`radio_shoutcast_params`
                                        (`RealTime`, `W3CEnable`, `AutoDumpSourceTime`,
                                         `ContentDir`, `TitleFormat`, `PublicServer`,
                                         `AllowRelay`, `AllowPublicRelay`, `MetaInterval`,
                                         `CleanXML`, `ShowLastSongs`, `bitrate`,
                                         `created`, `modified`)
                                         VALUES
                                          ('1', 'yes', '30', 'content',
                                          %s, 'default', 'Yes', 'Yes',
                                          '32768', 'Yes', '10', '128',
                                           %s, %s);""",
                                    ('TeamServer.ru Radio #' + serverID + ': %s',
                                     datetime.now(), datetime.now()))

                # id созданой записи
                paramsID = db.insert_id()

                # Сохранение привязки параметров к серверу
                joinCursor.execute("""INSERT INTO `teamserver`.`radio_shoutcast_params_servers`
                                      (`radio_shoutcast_param_id`, `server_id`)
                                      VALUES
                                      (%s, %s);""", (paramsID, serverID))
                db.commit()

            except OSError, e:
                print "Команда завершилась неудачей:", e

        except OSError, e:
            print "Команда завершилась неудачей", e
        # Конец создания скрипта запуска
        #

    # Закрыть базу
    paramsCursor.close()
    joinCursor.close()
    db.close()


else:
    print 'locked!<br/>'
