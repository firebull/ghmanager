#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Get installed plugins at SRCDS/HLDS server.
Runs plugin_check.py with clients rights.
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
1) Получаем список проверяемых плагинов
2) Идем в директорию каждого плагина
3) И проверяем есть ли каждый файл плагина в директории сервера
4) Выводим список того, что получилось
'''

import MySQLdb
import ConfigParser
import cgi
import cgitb
import sys
import pwd
import os
import re
from os.path import join, getsize
from datetime import datetime, date, time
from subprocess import *
from optparse import OptionParser
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
parser.add_option("-p", "--plugins", action="store", type="str", dest="plugins")
parser.add_option("-a", "--action", action="store", type="str", dest="action")

(options, args) = parser.parse_args(args=None, values=None)

if options.serverID and options.plugins:
    serverID = options.serverID
    serverAddons = options.plugins
    action = options.action
else:
    server = cgi.FieldStorage()
    serverID = server["id"].value
    serverAddons = server["plugins"].value
    action = server["action"].value

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

        print '<log>Шаблон сервера: ' + template['name'] + "</log>"

        serverPath = serversPath + "/" + template['name'] + "_" + str(serverID)
        installPath = template['addonsPath']

        pw = pwd.getpwnam(userName)
        userUid = pw.pw_uid

        if action == 'check':
            addonsList = serverAddons.split(':')

            for addonId in addonsList:

                addon = definePlugin(addonId, commonCursor)
                addonRows = int(commonCursor.rowcount)

                if addonRows > 0:

                    addonPath = "/images/plugins/" + addon['name']
                    if addon['version']:
                        addonPath += '-' + addon['version']
                    print "<plugin id='%s' name='%s'>" % (addonId, addon['name'])
                    try:
                        print "<log>Запусаю проверку</log>"
                        retcode = Popen("sudo -u " + userName
                                        + " ./plugin_check.py"
                                        + " -r " + addonPath
                                        + " -d " + serverPath + '/' + installPath,
                                        shell=True,
                                        stdin=PIPE,
                                        stdout=PIPE,
                                        stderr=PIPE)
                        (out, err) = retcode.communicate()
                        print out
                        print '<error>%s</error>' % err
                        if err < 0:
                            print "<error>Возникла ошибка: %s</error>" % err
                        elif err == 0 or err == "":
                            print "<log>Успешно</log>"

                    except OSError, e:
                        print "<error>Возникла ошибка: %s</error>" % e

                    print "</plugin>"

        elif action == 'delete':

            addon = definePlugin(serverAddons, commonCursor)
            addonRows = int(commonCursor.rowcount)

            if addonRows > 0:
                addonPath = "/images/plugins/" + addon['name']
                if addon['version']:
                    addonPath += '-' + addon['version']
                print "<plugin id='%s' name='%s'>" % (addon['id'], addon['name'])
                try:
                    retcode = Popen("sudo -u " + userName
                                    + " ./plugin_delete.py"
                                    + " -r " + addonPath
                                    + " -d " + serverPath + '/' + installPath,
                                    shell=True,
                                    stdin=PIPE,
                                    stdout=PIPE,
                                    stderr=PIPE)
                    (out, err) = retcode.communicate()
                    print out
                    print '<error>%s</error>' % err
                    if err < 0:
                        print "<error>При попытке удаления возникла ошибка: ", err, '</error>'
                    elif err == 0 or err == "":
                        print "<log>Данные получены</log>"

                except OSError, e:
                    print "<error>При попытке удаления возникла ошибка: ", e, '</error>'

                print "</plugin>"


print '</response>'
