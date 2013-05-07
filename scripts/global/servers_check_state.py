#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Checks private servers params.
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
    Скрипт проверки статуса приватных серверов:
        1) Если сервер приватный с паролем - проверка
           наличия установленного пароля. Если не установлен,
           отправка предупреждения владельцу, установка
           флага в базу и отключение сервера, если пароль
           не будет установлен при следующей проверке.
        2) Если сервер приватный с паролем и автоотключением,
           то пункт 1, а также внесение в базу отметок в каждый
           запуск скрипта - включён или нет.
               - Если включён и пустой более 30 минут - выключить;
                 (только добавить 5 минут запас)
               - TODO: Если за 3 последних суток сервер включён более
                 36 часов, то перевести сервер в категорию
                 приватных с паролем и пересчитать срок аренды.
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
lock = flock('servers_checker.lock', True).acquire()

if lock:

    print datetime.now().strftime("%A, %d. %B %Y %H:%M:%S")
    print 'Запрос данных из БД:'

    # host, user, pass, db

    db = MySQLdb.connect(host=mysqlHost, user=mysqlUser, passwd=mysqlPass, db=mysqlDb)

    # Create cursor with row names as array arguments
    cursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)
    commonCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)

    servers = getRootServerPrivateServers(cursor, thisServerId)

    numrows = int(servers.rowcount)

    logPrint('OK', 'Получены данные на %s серверов' % numrows)

    for x in range(0, numrows):

        server = servers.fetchone()
        # Теперь получим из базы шаблон и тип сервера
        # Шаблон:
        template = defineTemplate(commonCursor, str(server['id']))

        type = defineType(commonCursor, str(template['id']))

        serverID = str(server['id'])
        serverIP = str(server['address'])
        serverPort = int(server['port'])

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
            logPrint('WARN', 'Не удалось подключиться к серверу')
            continue

        if sq:
            logPrint('OK', "Проверка сервера %s #%s" % (template['longname'], serverID))

            try:
                if type['name'] == 'srcds':
                    info = sq.info()
                    # params = sq.rules() # Отключил для ускорения запроса, пока не требуется
                elif type['name'] == 'hlds':
                    info = sq.details()
                elif type['name'] == 'cod':
                    info = sq
            except:
                logPrint('WARN', 'Не удалось подключиться к серверу')
                continue

            # print info, params # debug
            statusDate = datetime.now().strftime("%d %B %Y %H:%M:%S")
            statusDateSql = datetime.now().strftime('%Y-%m-%d %H:%M:%S')

            if template['name'] in ('l4d2', 'l4d2-t100'):
                '''
                    К сожалению, sv_password не работает в L4D2,
                    мало того, вообще крашит сервер. Потому пока
                    валве не соблогоизваолит это исправить, для
                    этих серверов будет доступен только приватный
                    с авто-отключением.
                '''
                logPrint('OK', 'sv_password пока не работает на L4D2')
            elif int(info['passworded']) >= 1:  # Пароль установлен
                logPrint('OK', 'Пароль установлен')
                if server['privateStatus'] != 'ok':
                    setServerPrivateStatus(db, commonCursor, serverID, 'ok')
            else:
                logPrint('WARN', 'Пароль не установлен')
                # Определяем пользователя
                user = defineUser(commonCursor, serverID)

                if server['privateStatus'] == 'warn':
                    logPrint('WARN', "Отправка уведомления об отключении: " + user['email'])

                    emailText = '''

                    Уведомляем вас, что при последней проверке произведённой
                    %s было обнаружено, что на ваш сервер
                    не установлен пароль, что является нарушением условия
                    "Приватного сервера с паролем" или "Приватного сервера
                    с паролем и автоотключением". Т.к. нарушение так и
                    не было устранено с момента предудущей проверки,
                    в ближайшее время будет произведено отключение сервера.

                    Вы сможете использовать сервер только после установки
                    на него пароля.

                    С уважением,
                    TeamServer.ru

                                ''' % statusDate
                    sendEmail('Отключение сервера', emailText, user['email'])

                    logPrint('WARN', "Принудительная остановка сервера #" + serverID)

                    if type['name'] == 'hlds':  # Отключить HLTV
                        if stopUnpayedServer(serverID, 'game', 'none', True):
                            logPrint('OK', "Сервер HLTV отключен")

                    if stopUnpayedServer(serverID, 'game', 'none'):
                        logPrint('OK', "Сервер отключен")
                        serverStatus = 'stopped'
                        description = 'Сервер отключен автоматически, т.к. не установлен пароль.'

                        saveServerStatus(db, commonCursor, serverID, serverStatus, description, statusDateSql)
                        journalText = 'Отключение сервера %s #%s, т.к. не установлен пароль' % (
                            template['name'].upper(), serverID)
                        writeJournal(db, commonCursor, user['id'], journalText, 'warn')  # Сохранить статус в журнал

                    else:
                        print "Ошибка"

                else:
                    setServerPrivateStatus(db, commonCursor, serverID, 'warn')
                    logPrint('WARN', "Отправка предупреждения: " + user['email'])
                    if int(server['privateType']) == 1:
                        emailText = '''

                        Уведомляем вас, что при последней проверке произведённой
                        %s было обнаружено, что на ваш сервер
                        не установлен пароль, что является нарушением условия
                        "Приватного сервера с паролем". Просим установить пароль
                        на сервер, иначе он будет выключен при следующей проверке,
                        которая производится каждые 15 минут.

                        С уважением,
                        TeamServer.ru

                                    ''' % statusDate

                    elif int(server['privateType']) == 2:
                        emailText = '''

                        Уведомляем вас, что при последней проверке произведённой
                        %s было обнаружено, что на ваш сервер
                        не установлен пароль, что является нарушением условия
                        "Приватного сервера с паролем и автоотключением".
                        Обращаем ваше внимение, что пароль ДОЛЖЕН быть установлен
                        на ваш сервер, не смотря на то, что он автоматически
                        выключается при простое более 30 минут.

                        Просим установить пароль на сервер, иначе он будет выключен
                        при следующей проверке, которая производится каждые 15 минут.

                        С уважением,
                        TeamServer.ru

                                    ''' % statusDate

                    sendEmail('Предупреждение', emailText, user['email'])

                continue  # Перейти к следующему серверу

            # Проверки сервера с автоотключением
            if int(server['privateType']) == 2:
                if server['emptySince']:  # Если уже установлено время простоя
                    if int(info['numplayers']) == 0:
                        # Вычислить сколько времени прошло с момента установки статуса
                        turnOffTime = server['emptySince'] + timedelta(minutes=35)
                        logPrint('OK', "Сервер должен быть отключен в: " + str(turnOffTime))

                        if turnOffTime <= datetime.now():  # Если сервер пуст более установленного времени
                            logPrint('WARN', "Принудительная остановка сервера #" + serverID)

                            if type['name'] == 'hlds':  # Отключить HLTV
                                if stopUnpayedServer(serverID, 'game', 'none', True):
                                    logPrint('OK', "Сервер HLTV отключен")

                            if stopUnpayedServer(serverID, 'game', 'none'):
                                logPrint('OK', "Сервер отключен")
                                # Установить emptySince в Null
                                setServerEmptyTime(db, commonCursor, serverID)
                                serverStatus = 'stopped'
                                description = '''
                                        Сервер отключен автоматически
                                        из-за простоя более 30 минут.
                                              '''

                                saveServerStatus(db, commonCursor, serverID, serverStatus, description, statusDateSql)

                                journalText = 'Отключение сервера %s #%s из-за простоя более 30 минут' % (
                                    template['name'].upper(), serverID)
                                writeJournal(db, commonCursor, user['id'], journalText, 'ok')  # Сохранить статус в журнал

                            else:
                                logPrint('ERR', "Возникла ошибка")
                    else:
                        # Установить emptySince в Null
                        setServerEmptyTime(db, commonCursor, serverID)
                else:
                    if int(info['numplayers']) == 0:
                        # Установить emptySince на текущее время
                        setServerEmptyTime(db, commonCursor, serverID, datetime.now().strftime('%Y-%m-%d %H:%M:%S'))

    cursor.close()
    commonCursor.close()
    db.commit()
    db.close()
    flock('servers_checker.lock', True).release()

else:
    logPrint('ERR', "locked!")
