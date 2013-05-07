#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Turn off unpayed servers.
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


# Скрипт остановки неоплаченных серверов

import MySQLdb
import os
import shlex
import sys
sys.path.append("/images/scripts/global")
from db_queries import *
from common import *
import string
from subprocess import *
from shutil import *
from time import sleep
from flock import flock
from datetime import datetime, date, time
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
lock = flock('servers_cleaner.lock', True).acquire()

if lock:

    presentTime = datetime.now().strftime("%A, %d. %B %Y %H:%M:%S")
    print presentTime
    print 'Запрос данных из БД:'

    # host, user, pass, db

    db = MySQLdb.connect(host=mysqlHost, user=mysqlUser, passwd=mysqlPass, db=mysqlDb)

    # Create cursor with row names as array arguments
    cursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)
    commonCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)

    cursor.execute(
        """SELECT `id` FROM servers where payedTill < NOW() AND (initialised = 1) AND (status != 'stopped') LIMIT 10""")

    numrows = int(cursor.rowcount)

    logPrint('OK', 'Получены данные на %s серверов' % numrows)

    for x in range(0, numrows):

            row = cursor.fetchone()
            serverID = str(row['id'])

            # Проверяем привязку игрового сервера к нашему физическому
            rootServer = defineRootServer(commonCursor, serverID)

            if rootServer['id'] != thisServerId:
                print "Игровой сервер #%s привязан к другому физическому" % serverID
                continue  # Переход к следующему игровому серверу

            # Определяем пользователя
            user = defineUser(commonCursor, serverID)

            # Теперь получим из базы шаблон и тип сервера
            # Шаблон:
            template = defineTemplate(commonCursor, serverID)

            # Тип - достаем его из шаблона:
            type = defineType(commonCursor, template['id'])

            logPrint('OK', 'Тип сервера:    %s' % type['longname'])
            logPrint('OK', 'Шаблон сервера: %s' % template['longname'])

            userName = "client%s" % user['id']
            homeDir = "/home/%s" % userName
            logsPath = homeDir + "/logs/" + template['name'] + "_" + serverID
            scriptsPath = homeDir + "/public_html/"
            destServer = homeDir + "/servers/" + template['name'] + "_" + serverID

            logPrint('WARN', "Принудительная остановка сервера #" + serverID)

            if stopUnpayedServer(serverID, type['name'], scriptsPath):

                if type['name'] == 'hlds':
                    stopUnpayedServer(serverID, type['name'], scriptsPath, True)

                logPrint('OK', "Сервер отключен")
                serverStatus = 'stopped'
                statusDateSql = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
                description = '''Сервер отключен автоматически из-за окончания срока аренды.'''

                saveServerStatus(db, commonCursor, serverID, serverStatus, description, statusDateSql)
                logPrint('OK', "Отправка уведомления об отключении: " + user['email'])

                emailText = '''

                Уведомляем вас, что ваш сервер %s ID %s
                был автоматически отключен в %s из-за
                окончания срока аренды.

                Вы можете продлить аренду в любое время в течение
                ближайшего месяца, пока ваш сервер и все его данные
                будут храниться у нас.

                Если вас по каким-либо причинам не устроило качество
                наших услуг и вы не планируете продлевать аренду,
                сильнейше просим сообщить нам о проблемах и недочётах,
                или просто о пожеланиях на e-mail admin@teamserver.ru

                Мы приложим все усилия, чтобы исправить эти проблемы
                в самые кратчайшие сроки.

                Надеемся на дальнейшее сотрудничество!

                С уважением,
                TeamServer.ru

                ''' % (template['longname'], serverID, presentTime)

                sendEmail('Отключение сервера', emailText, user['email'])

                journalText = 'Отключение сервера %s #%s из-за окончания срока аренды' % (template['name'].upper(), serverID)
                writeJournal(db, commonCursor, user['id'], journalText, 'warn')  # Сохранить статус в журнал

            else:
                logPrint('ERR', "Ошибка отключения сервера")

    # Закрываем все соединения и базу
    cursor.close()
    commonCursor.close()
    db.commit()
    db.close()

else:
    print 'locked!'
