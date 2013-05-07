#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Restarts servers which are online more then 24 hours.
Restart only if a server is empty.
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
    Скрипт перезагрузки серверов, запущенных больше суток:
        Если сервер имеет статус exec_success и прошло больше суток,
        а также он пуст, перезапустить его.
'''


import MySQLdb
import os
import sys
sys.path.append("/images/scripts/global")
from db_queries import *
from common import *
# import string
from subprocess import *
from time import sleep
from flock import flock
from datetime import datetime, date, time, timedelta
from SourceLib import SourceQuery
from SRCDS import SRCDS
from COD import getCodServerInfo
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
lock = flock('servers_rebooter.lock', True).acquire()

if lock:

    print datetime.now().strftime("%A, %d. %B %Y %H:%M:%S")
    # print 'Запрос данных из БД:'
    # host, user, pass, db

    db = MySQLdb.connect(host=mysqlHost, user=mysqlUser, passwd=mysqlPass, db=mysqlDb)

    # Create cursor with row names as array arguments
    cursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)
    commonCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)

    servers = getRootServerExecutedServersToReboot(cursor, thisServerId)

    numrows = int(servers.rowcount)

    if numrows > 0:
        # logPrint('OK', 'Получены данные на %s серверов' % numrows)

        for x in range(0, numrows):

            server = servers.fetchone()

            # Определяем пользователя
            user = defineUser(commonCursor, server['id'])

            userName = "client%s" % user['id']
            homeDir = "/home/%s" % userName
            serversPath = homeDir + "/servers"

            # Теперь получим из базы шаблон и тип сервера
            # Шаблон:
            template = defineTemplate(commonCursor, str(server['id']))

            type = defineType(commonCursor, str(template['id']))

            serverID = str(server['id'])
            serverIP = str(server['address'])
            serverPort = int(server['port'])
            serverRootPath = serversPath + "/" + template['name'] + "_" + str(serverID) + "/" + template['rootPath']

            # Определяем пользователя
            user = defineUser(commonCursor, serverID)

            try:
                if type['name'] == 'srcds':
                    sq = SourceQuery.SourceQuery(serverIP, serverPort)
                elif type['name'] == 'hlds':
                    sq = SRCDS(serverIP, serverPort)
                elif type['name'] == 'cod':
                    sq = getCodServerInfo(serverIP, serverPort)
                else:
                    sq = False

            except:
                logPrint('WARN', "Не удалось подключиться к серверу %s #%s" % (template['longname'], serverID))
                continue

            if sq:
                logPrint('OK', "Проверка сервера %s #%s" % (template['longname'], serverID))
                execWithMod = 'none'
                try:
                    if type['name'] == 'srcds':
                        info = sq.info()
                        numPlayers = int(info['numplayers']) - int(info['numbots'])
                    elif type['name'] == 'hlds':
                        info = sq.details()
                        numPlayers = info['numplayers']
                    elif type['name'] == 'cod':
                        info = sq
                        numPlayers = info['numplayers']
                        if os.path.exists(serverRootPath + '/mods/manuadmin'):
                            execWithMod = 'Manu'
                except:
                    logPrint('WARN', "Не удалось получить состояние сервера %s #%s" % (template['longname'], serverID))
                    continue

                if int(numPlayers) == 0:
                    logPrint('OK', "Сервер пуст. Попытка перезапуска.")
                    if restartServer(serverID, type['name'], execWithMod, False):
                        logPrint('OK', "Сервер перезапущен")
                        journalText = 'Плановый автоматический перезапуск сервера %s #%s' % (template['name'].upper(), serverID)
                        status = 'ok'

                    else:
                        logPrint('ERROR', "Ошибка перезапуска")
                        journalText = 'Ошибка при попытке планового перезапуска сервера %s #%s' % (
                            template['name'].upper(), serverID)
                        status = 'error'

                    if type['name'] == 'hlds' and server['hltvStatus'] == 'exec_success':
                        logPrint('OK', "Попытка перезапуска HLTV.")
                        if restartServer(serverID, type['name'], 'none', True):
                            logPrint('OK', "Сервер HLTV перезапущен")
                            journalText = 'Плановый автоматический перезапуск HLTV %s #%s' % (
                                template['name'].upper(), serverID)
                            status = 'ok'

                        else:
                            logPrint('ERROR', "Ошибка перезапуска HLTV")
                            journalText = 'Ошибка при попытке планового перезапуска HLTV %s #%s' % (
                                template['name'].upper(), serverID)
                            status = 'error'

                    writeJournal(db, commonCursor, user['id'], journalText, status)  # Сохранить статус в журнал

                    logPrint('OK', "Пауза 30 секунд")
                    sleep(20)

                else:
                    logPrint('DELAY', 'На сервере есть игроки, откладываю перезапуск до следуюущей проверки.')

    # Закрываем все соединения и базу
    cursor.close()
    commonCursor.close()
    db.commit()
    db.close()
