#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Get installed mods at COD2/4 server.
Runs dir_lists.py with clients rights.
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
import cgitb
import os
import pwd
from subprocess import *
from optparse import OptionParser
import sys
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

parser.add_option("-s", "--server", action="store", type="int", dest="serverID")

(options, args) = parser.parse_args(args=None, values=None)

if options.serverID:
    serverID = options.serverID
else:
    server = cgi.FieldStorage()
    serverID = server["id"].value

# Получить данные сервера из базы
db = MySQLdb.connect(host=mysqlHost, user=mysqlUser, passwd=mysqlPass, db=mysqlDb)

# Create cursor with row names as array arguments
serverCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)
commonCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)

serverCursor.execute("""SELECT * FROM servers where payedTill > NOW() AND initialised = 1 AND id = %s
                    ORDER BY `servers`.`created`  DESC LIMIT 1""", serverID)

numrows = int(serverCursor.rowcount)

if numrows > 0:
    print "<log>Данные о сервере получены</log>"

    server = serverCursor.fetchone()

    # Проверяем привязку игрового сервера к нашему физическому
    rootServer = defineRootServer(commonCursor, serverID)

    if rootServer['id'] == thisServerId:
        print "<log>Сервер привязан на этот физический сервер. Продолжаю.</log>"

        # Определяем пользователя
        user = defineUser(commonCursor, serverID)

        userName = "client%s" % user['id']
        userEmail = user['email']
        homeDir = "/home/%s" % userName
        serversPath = homeDir + "/servers"

        # Теперь получим из базы шаблон сервера
        template = defineTemplate(commonCursor, serverID)

        # Тип - достаем его из шаблона:
        type = defineType(commonCursor, str(template['id']))

        if type['name'] == 'cod':
            print '<log>Шаблон сервера: ' + template['name'] + "</log>"

            serverPath = serversPath + "/" + template['name'] + "_" + str(serverID)
            modsPath = 'mods'

            pw = pwd.getpwnam(userName)
            userUid = pw.pw_uid

            # Запуск скрипта проверки от имени пользователя
            try:
                retcode = Popen("sudo -u " + userName
                                + " ./dir_list.py"
                                + " -p " + serverPath
                                + '/' + modsPath,
                                shell=True,
                                stdin=PIPE,
                                stdout=PIPE,
                                stderr=PIPE)
                (out, err) = retcode.communicate()
                print out
                print '<error>%s</error>' % err
                if err < 0:
                    print "<error>При попытке получения списка модов возникла ошибка: ", err, '</error>'
                elif err == 0 or err == "":
                    print "<log>Список получен</log>"

            except OSError, e:
                print "<error>При попытке получения списка модов возникла ошибка: ", e, '</error>'

        else:
            print '<error>Неверный шаблон сервера: ' + type['name'] + ". Должен быть COD.</error>"
    else:
        print '<error>Игровой сервер привязан к другому физическому.</error>'

print '</response>'
