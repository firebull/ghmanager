#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Read/write param to config.
Runs read_write_param.py with clients rights.
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
 Скрипт для чтения и записи параметров в конфиг
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
parser.add_option("-i", "--param",  action="store", type="string", dest="param")
parser.add_option("-v", "--value",  action="store", type="string", dest="value")
parser.add_option("-d", "--desc",   action="store", type="string", dest="description")
parser.add_option("-c", "--config", action="store", type="string", dest="config_name")
parser.add_option("-p", "--path",   action="store", type="string", dest="config_path")
parser.add_option("-a", "--action", action="store", type="string", dest="action")
parser.add_option("-w", "--delim", action="store", type="string", dest="delim")

(options, args) = parser.parse_args(args=None, values=None)

if options.serverID:
    serverID = options.serverID
    param = options.param.strip("'")
    value = options.value.strip("'")
    desc = options.description.strip("'")
    config = options.config_name.strip("'")
    path = options.config_path
    action = options.action.strip("'")
    delim = options.delim.strip("'")
else:
    server = cgi.FieldStorage()
    serverID = server["id"].value
    param = server["p"].value
    value = server["val"].value
    desc = server["desc"].value
    config = server["conf"].value
    path = server["path"].value
    action = server["a"].value
    delim = server["d"].value

path = re.sub('(\.{1,2}/)', '', path)  # Убрать все обратные переходы
config = re.sub('(/|\.{1,2}/)', '', config)  # Удалить ../ в имени конфига

if value == 'None':
    value = ''
if desc == 'None':
    desc = None

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

        if path != None:
            print '<log>Шаблон сервера: ' + template['name'] + "</log>"

            serverPath = serversPath + "/" + template['name'] + "_" + str(serverID)
            configPath = os.path.join(homeDir, path)

            pw = pwd.getpwnam(userName)
            userUid = pw.pw_uid

            # Запуск скрипта проверки от имени пользователя
            try:
                retcode = Popen("sudo -u " + userName
                                + ' ./read_write_param.py -i "%s" -v "%s" -d "%s" -c "%s" -p "%s" -a %s -w %s'
                                % (param, value, desc, config, configPath, action, delim),
                                shell=True,
                                stdin=PIPE,
                                stdout=PIPE,
                                stderr=PIPE)
                (out, err) = retcode.communicate()
                print out
                print xmlLog(err, 'error')
                if err < 0:
                    print "<error>При попытке чтения/записи конфига возникла ошибка: ", err, '</error>'
                elif err == 0 or err == "":
                    print "<log>Операция завершена.</log>"

            except OSError, e:
                print "<error>При попытке чтения/записи конфига возникла ошибка: ", e, '</error>'

        else:
            print '<error>Не указан путь конфига.</error>'
    else:
        print '<error>Игровой сервер привязан к другому физическому.</error>'

print '</response>'
