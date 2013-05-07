#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Read server log.
Runs read_log.py with clients rights.
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


print "Content-Type: text/html; charset=UTF-8"     # HTML is following
print                                              # blank line, end of headers

import MySQLdb
import ConfigParser
import cgi
import cgitb
import sys
import re
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

parser.add_option("-s", "--server",  action="store", type="int",    dest="serverID")
parser.add_option("-a", "--action",  action="store", type="string", dest="action")
parser.add_option("-r", "--pattern", action="store", type="string", dest="pattern")
parser.add_option("-l", "--lines",   action="store", type="int",    dest="lines")
parser.add_option("-p", "--path",    action="store", type="string", dest="path")
parser.add_option("-n", "--name",    action="store", type="string", dest="name")

(options, args) = parser.parse_args(args=None, values=None)

if options.serverID and options.action and options.path:
    serverID = options.serverID
    action = options.action
    pattern = options.pattern
    numOfLines = options.lines
    logPath = options.path
    logName = options.name
else:
    server = cgi.FieldStorage()

    serverID = server["id"].value
    action = str(server["action"].value)
    pattern = str(server["pattern"].value)
    numOfLines = int(server["lines"].value)
    logPath = str(server["logpath"].value).strip()
    logName = str(server["logname"].value).strip()

# Вырезать обратные переходы вида ' ../ ', дабы нельзя было изменить путь
if logPath:
    logPath = re.sub('(\.{2}/)', '', logPath)
if logName:
    logName = re.sub('(\.{2}/)', '', logName)

# Получить данные сервера из базы
db = MySQLdb.connect(host=mysqlHost, user=mysqlUser, passwd=mysqlPass, db=mysqlDb)

# Create cursor with row names as array arguments
serverCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)
commonCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)

userCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)
pluginCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)
templateCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)
rootServerCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)

# Сначала надо составить все параметры, для передачи скрипту

serverCursor.execute("""SELECT * FROM servers where payedTill > NOW() AND initialised = 1 AND id = %s
                    ORDER BY `servers`.`created`  DESC LIMIT 1""", serverID)

numrows = int(serverCursor.rowcount)

if numrows > 0:
    print "Данные о сервере получены<br/>"

    server = serverCursor.fetchone()

    # Проверяем привязку игрового сервера к нашему физическому
    rootServer = defineRootServer(commonCursor, serverID)

    if rootServer['id'] == thisServerId:
        print "Сервер привязан на этот физический сервер. Продолжаю.<br/>"

        print "Получаю данные о пользователе<br/>"

        # Определяем пользователя
        user = defineUser(commonCursor, serverID)

        # Конец опеределения пользователя
        #

        userName = "client%s" % user['id']
        homeDir = "/home/%s" % userName
        serversPath = homeDir + "/servers"

        # Путь к логам всегда от корня директории пользователя
        # Запуск скрипта проверки от имени пользователя
        try:
            retcode = Popen("sudo -u " + userName
                            + " ./read_log.py"
                            + " -a " + action
                            + " -r " + pattern
                            + " -p " + homeDir + '/' + logPath
                            + " -l " + str(numOfLines)
                            + " -n " + str(logName),
                            shell=True,
                            stdin=PIPE,
                            stdout=PIPE,
                            stderr=PIPE)
            (out, err) = retcode.communicate()
            print out
            print '<!-- Ошибка: %s -->' % err
            if err < 0:
                print "При попытке чтения логов возникла ошибка: ", err
            elif err == 0 or err == "":
                print "<log>Список получен</log>"

        except OSError, e:
            print "При попытке чтения логов возникла ошибка: ", e
