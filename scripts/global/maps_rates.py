#!/usr/bin/env python2
# coding: UTF-8
# print "Content-Type: text/xml"     # XML is following
# print                               # blank line, end of headers

'''
 Скрипт для составления списка используемых карт
 на серверах клиентов. Для статистики.
'''

import MySQLdb
import ConfigParser
import os
import pwd
import re
from subprocess import *
from optparse import OptionParser
import sys
sys.path.append("/images/scripts/global")
from db_queries import *


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

parser.add_option("-t", "--type", action="store", type="int", dest="typeID")

(options, args) = parser.parse_args(args=None, values=None)

typeID = options.typeID

# Получить данные сервера из базы
db = MySQLdb.connect(host=mysqlHost, user=mysqlUser, passwd=mysqlPass, db=mysqlDb)

# Create cursor with row names as array arguments
serverCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)
commonCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)

servers = getRootServerServersByType(serverCursor, thisServerId, typeID)

numrows = int(serverCursor.rowcount)

maps = {}

if numrows > 0:
    for x in range(0, numrows):
        server = servers.fetchone()

        if server != None:
            serverID = server['id']
            # print "Данные о сервере #%s получены" % serverID

            # Определяем пользователя
            user = defineUser(commonCursor, serverID)

            userName = "client%s" % user['id']
            userEmail = user['email']
            homeDir = "/home/%s" % userName
            serversPath = homeDir + "/servers"

            # Теперь получим из базы шаблон сервера
            template = defineTemplate(commonCursor, serverID)

            if template['mapsPath'] != 'None' and not template['name'] in ['l4d', 'l4d2']:
                # print 'Шаблон сервера: ' + template['name'] + ""

                serverPath = serversPath + "/" + template['name'] + "_" + str(serverID)

                # print 'Ищу карты по пути: ' + serverPath + '/' + template['mapsPath']

                mapsPath = serverPath + '/' + template['mapsPath']
                extension = template['mapExt']

                pw = pwd.getpwnam(userName)
                userUid = pw.pw_uid

                if not template['name'] in maps:
                    maps[template['name']] = {}

                curMaps = []
                try:
                    if os.path.exists(mapsPath):
                        for root, dirs, files in os.walk(mapsPath, topdown=True):
                            for file in files:
                                if re.search('^\S*\.' + extension + '$', file):
                                    curMaps.append(file)
                            break
                    else:
                        print 'Путь не найден либо нет прав доступа.'

                except OSError, e:
                    print "При попытке получения списка карт возникла ошибка: ", e

                for map in curMaps:
                    if map in maps[template['name']]:
                        maps[template['name']][map] += 1
                    else:
                        maps[template['name']][map] = 1

            else:
                print 'Не указан путь поиска карт или игра L4D1/2.'

for t in maps.keys():
    print "############################"
    print "Игра: %s" % t
    print "############################"
    keys = maps[t].keys()
    keys.sort()
    for map in keys:
        print '%s: %s' % (map, str(maps[t][map]))
