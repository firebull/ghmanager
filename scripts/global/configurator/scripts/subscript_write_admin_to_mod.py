#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Writes admin to mods (SM, AMX and so on).
Runs write_admin_to_mod.py with clients rights.
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
'''
 Скрипт для формирования запроса к скрипту записи
 админов в моды от имени пользователя владельца сервера.
'''

import MySQLdb
import ConfigParser
import cgi
import cgitb
import os
import pwd
from subprocess import *
from optparse import OptionParser
import sys
import re
from commonLib import xmlLog
sys.path.append("/images/scripts/global")
from db_queries import *

cgitb.enable()  # Debug

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

parser.add_option("-s", "--server", action="store", type="int", dest="serverID")
parser.add_option("-m", "--mod",  action="store", type="string", dest="mod")
parser.add_option("-t", "--adminType",  action="store", type="string", dest="adminType")
parser.add_option("-n", "--adminString",  action="store", type="string", dest="adminStr")

(options, args) = parser.parse_args(args=None, values=None)

if options.serverID:
    serverID = options.serverID
    mod = options.mod.strip("'")
    adminType = options.adminType.strip("'")
    adminStr = options.adminStr.strip("'")

else:
    server = cgi.FieldStorage()
    serverID = server["id"].value
    mod = server["mod"].value
    adminType = server["adminType"].value
    adminStr = server["adminStr"].value

# Получить данные сервера из базы
db = MySQLdb.connect(host=mysqlHost, user=mysqlUser, passwd=mysqlPass, db=mysqlDb)

# Create cursor with row names as array arguments
serverCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)
commonCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)

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
        userEmail = user['email']
        homeDir = "/home/%s" % userName
        serversPath = homeDir + "/servers"

        # Теперь получим из базы шаблон сервера
        template = defineTemplate(commonCursor, serverID)

        xmlLog("Шаблон сервера: " + template['name'])

        serverPath = serversPath + "/" + template['name'] + "_" + str(serverID)

        if mod == 'sourcemod':
            config = 'addons/sourcemod/configs/admins_simple.ini'

        elif mod == 'amxmodx':
            config = 'addons/amxmodx/configs/users.ini'

        elif mod == 'maniadmin':
            config = 'cfg/mani_admin_plugin/clients.txt'
        else:
            xmlLog('Не знаю такой мод', 'error')
            print '</response>'
            raise

        config = os.path.join(serverPath, template['addonsPath'], config)

        pw = pwd.getpwnam(userName)
        userUid = pw.pw_uid

        # Запуск скрипта проверки от имени пользователя
        try:
            retcode = Popen("sudo -u " + userName
                            + ' ./write_admin_to_mod.py -a \'%s\' -t "%s" -m "%s" -c "%s"'
                            % (adminStr, adminType, mod, config),
                            shell=True,
                            stdin=PIPE,
                            stdout=PIPE,
                            stderr=PIPE)
            (out, err) = retcode.communicate()
            print out
            print xmlLog(err, 'error')
            if err < 0:
                xmlLog("При попытке чтения/записи конфига возникла ошибка: " + err, 'error')
            elif err == 0 or err == "":
                xmlLog("Операция завершена.")

        except OSError, e:
            xmlLog("При попытке чтения/записи конфига возникла ошибка: " + e, 'error')

    else:
        xmlLog("Игровой сервер привязан к другому физическому.", 'error')


print '</response>'
