#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Delete servers which are marked to delete in DB.
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


# Скрипт физического удаления серверов, которые в базе помечены на удаление

import MySQLdb
import os
import shlex
import sys
sys.path.append("/images/scripts/global")
from db_queries import *
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

    print datetime.now().strftime("%A, %d. %B %Y %H:%M:%S")
    # print 'Загрузка данных из БД:'

    # host, user, pass, db

    db = MySQLdb.connect(host=mysqlHost, user=mysqlUser, passwd=mysqlPass, db=mysqlDb)

    # Create cursor with row names as array arguments
    cursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)
    commonCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)

    cursor.execute("""SELECT * FROM servers WHERE `action` = 'delete'
                        ORDER BY `servers`.`created`  DESC LIMIT 10""")

    numrows = int(cursor.rowcount)

    if numrows > 0:
        print 'Получены данные на ', numrows, 'серверов'

    for x in range(0, numrows):

        row = cursor.fetchone()
        serverID = str(row['id'])

        # Проверяем привязку игрового сервера к нашему физическому
        rootServer = defineRootServer(commonCursor, serverID)

        if rootServer and (rootServer['id'] != thisServerId):
            print "Игровой сервер #%s привязан к другому физическому" % serverID
            continue  # Переход к следующему игровому серверу

        # Определяем пользователя
        user = defineUser(commonCursor, serverID)

        # Теперь получим из базы шаблон и тип сервера
        # Шаблон:
        template = defineTemplate(commonCursor, serverID)

        # Тип - достаем его из шаблона:
        type = defineType(commonCursor, template['id'])

        print '   Тип сервера:    ', type['longname']
        print '   Шаблон сервера: ', template['longname']

        userName = "client%s" % user['id']
        homeDir = "/home/%s" % userName
        logsPath = homeDir + "/logs/" + template['name'] + "_" + serverID
        scriptsPath = homeDir + "/public_html/"
        destServer = homeDir + "/servers/" + template['name'] + "_" + serverID

        print "Запуск процедуры удаления сервера #" + serverID
        print "Принудительная остановка сервера: "

        try:
            if type['name'] == 'radio':
                print "Запуск скрипта остановки из директории пользователя"
                if os.path.exists(scriptsPath):
                    retcode = Popen(os.path.join(scriptsPath, ".server_stop_" + serverID + ".sh"),
                                    shell=True,
                                    stdin=PIPE,
                                    stdout=PIPE,
                                    stderr=PIPE)
                    (out, err) = retcode.communicate()
                    print out
                else:
                    err = ""

            else:
                os.chdir("/home/configurator/public_html/scripts")
                retcode = Popen("/home/configurator/public_html/scripts/subscript_start_stop.py "
                                + " -a stop"
                                + " -s " + serverID,
                                shell=True,
                                stdin=PIPE,
                                stdout=PIPE,
                                stderr=PIPE)

                (out, err) = retcode.communicate()
                print out

            if err < 0:
                print "Не удалось остановить сервер: ", err
            elif err == 0 or err == "":
                print "Сервер остановлен. Продолжаю дальше."

                # Процедура очистки
                # - Удалить директорию сервера
                # - Удалить директорию с логами или отдельные логи сервера
                # - Удалить скрипты сервера
                # - Удалить данные сервера из базы
                print "Удаляю директорию сервера."
                if os.path.exists(destServer):
                    try:
                        rmtree(destServer)
                        print "Директория удалена."
                    except OSError, e:
                        print "Не удалось удалить директорию:", e
                        continue  # Переход к следующему игровому серверу
                else:
                    print "Директория не найдена. Уже удалена?"

                print "Удаляю директорию логов сервера."
                if os.path.exists(logsPath):
                    try:
                        rmtree(logsPath)
                        print "Директория удалена."
                    except OSError, e:
                        print "Не удалось удалить директорию:", e
                        continue  # Переход к следующему игровому серверу
                else:
                    print "Директория логов сервера не найдена. Уже удалена?"

                print "Удаляю скрипты запуска/остановки/рестарта"
                if type['name'] == 'radio':
                    try:
                        os.remove("./.server_stop_" + serverID + ".sh")
                        os.remove("./.server_start_" + serverID + ".sh")
                        os.remove("./.server_restart_" + serverID + ".sh")
                        print "Скрипты удалены."
                    except OSError, e:
                        print "Не удалось удалить все скрипты:", e

                # Очитска БД
                # Т.к. данные сервера находятся в связанных таблицах,
                # считаем, что PHP-скрипт, который ставит пометку на удаление
                # в БД, удаляет все связи сервера, кроме связей с
                # пользователем и шаблоном. Потому удалять только их.

                if cleanServerFromDb(db, commonCursor, serverID):
                    print "Данные сервера удалены из базы."
                else:
                    print "Не удалось удалить данные сервера из базы"

                print "Конец процедуры очистки сервера #" + serverID
                # Конец процедуры очистки
            else:
                print "Не удалось остановить сервер: ", err

        except OSError, e:
            print "Не удалось остановить сервер: ", e

    # Закрываем все соединения и базу
    cursor.close()
    commonCursor.close()
    db.commit()
    db.close()
    flock('servers_cleaner.lock', True).release()

else:
    print 'locked!'
