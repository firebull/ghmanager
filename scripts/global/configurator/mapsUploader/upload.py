#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Recieve and move maps to client's game server.
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

Скрипт для приёма и обработки карт.
Копирование их в директорию сервера производится отдельным скриптом.

1) По одноразовому токену проверить существование пользователя
2) Проверить существование сервера и его привязку на текущий физический,
   а также инициализацию сервера и срок оплаты
3) По ID пользователя проверить его права на сервер
4) Получить данные шаблона: путь самого сервера, а также путь к картам
5) Проверить расширение: пока будем принимать только в zip
6) Разархивировать карты в /tmp/mapsUploader/@userID@/@zipName@
7) Дать полные права на них соответств пользователю
8) Другим скриптом скопировать карты в нужное место и удалить исходники
9) Удалить закачанный архив
10) Сгенерировать и записать новый токен
'''

print "Content-Type: text/html"     # HTML is following
print                               # blank line, end of headers

import MySQLdb
import cgi
import os
import sys
import cgitb
cgitb.enable()
import ConfigParser
sys.path.append("/images/scripts/global")
from db_queries import *
# from optparse import OptionParser
# from datetime import datetime, date, time
from zipfile import *
from subprocess import *

try:
    form = cgi.FieldStorage()

    token = str(form['token'].value).strip()
    serverID = str(form['id'].value).strip()
    fileitem = form['file']
except:
    print '{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Нет полных данных"}, "id" : "id"}'
    raise


config = ConfigParser.RawConfigParser()
config.read('/etc/hosting/scripts.cfg')

# ID сервера, на котором пускается скрипт
# Брать его из поля id в админке

thisServerId = config.getint('server', 'serverID')
mysqlHost = config.get('db', 'host')
mysqlUser = config.get('db', 'user')
mysqlPass = config.get('db', 'pass')
mysqlDb = config.get('db', 'db')

# Получить данные сервера из базы
db = MySQLdb.connect(host=mysqlHost, user=mysqlUser, passwd=mysqlPass, db=mysqlDb)

# Create cursor with row names as array arguments
serverCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)
commonCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)

commonCursor.execute("""SELECT `id` FROM `users` WHERE `tokenhash` = %s LIMIT 2""", token)

# Тупой путь проверить попытку подмены токена в базе
if int(commonCursor.rowcount) == 1:
    userByToken = commonCursor.fetchone()
    userID = userByToken['id']

    # Теперь получить данные пользователя с учётом сервера
    user = defineUser(commonCursor, serverID)

    userName = "client%s" % user['id']
    userEmail = user['email']
    homeDir = "/home/%s" % userName
    serversPath = homeDir + "/servers"

    if userID == user['id']:
        # Всё окей, владелец сервера верный

        serverCursor.execute("""SELECT * FROM servers where payedTill > NOW() AND initialised = 1 AND id = %s
                            ORDER BY `servers`.`created`  DESC LIMIT 1""", serverID)

        if int(serverCursor.rowcount) > 0:
            # print "Данные о сервере получены"

            server = serverCursor.fetchone()

            # Проверяем привязку игрового сервера к нашему физическому
            rootServer = defineRootServer(commonCursor, serverID)

            if rootServer['id'] == thisServerId:
                # print "Сервер привязан на этот физический сервер. Продолжаю."

                # Теперь получим из базы шаблон сервера
                template = defineTemplate(commonCursor, serverID)

                # print 'Шаблон сервера: ', template['name']

                templateName = template['name']
                templateRootPath = template['rootPath']
                templateMapPath = template['addonsPath']

                # Проверка, загружен ли файл
                if fileitem.filename:

                    if not os.path.exists('/tmp/mapsUploader/' + str(userID) + '/'):
                        os.makedirs('/tmp/mapsUploader/' + str(userID) + '/', 0755)

                    normolisedZip = str(os.path.basename(fileitem.filename))
                    zipSaveTo = '/tmp/mapsUploader/' + str(userID) + '/' + normolisedZip
                    try:
                        open(zipSaveTo, 'wb').write(fileitem.file.read())
                    except:
                        print '{"jsonrpc" : "2.0", "error" : {"code": 107, "message": "Не удалось сохранить архив"}, "id" : "id"}'
                        raise

                    extractToDir = '%s/%s_%s/%s' % (serversPath, template['name'], serverID, templateMapPath)

                    # Кусок ниже надо передать уже другому скрипту,
                    # запускаемому от имени пользователя.
                    if is_zipfile(zipSaveTo):
                    #
                        # Сначала убедиться, что в архиве есть карты
                        zip = ZipFile(zipSaveTo, 'r')
                        hasMaps = False
                        for mapFile in zip.namelist():
                            #                      Valve                        COD
                            if mapFile.startswith('maps/') or mapFile.endswith('.iwd'):
                                hasMaps = True
                                break
                        zip.close()

                        if hasMaps == True:
                            retcode = Popen("sudo -u " + userName + " /images/scripts/global/unzip.py "
                                            + " -z " + zipSaveTo
                                            + " -p " + extractToDir,
                                            shell=True,
                                            stdin=PIPE,
                                            stdout=PIPE,
                                            stderr=PIPE)

                            (out, err) = retcode.communicate()

                            if err != "":
                                print '{"jsonrpc" : "2.0", "error" : {"code": 106, "message": "Не удалось разархивировать карты: ' + err.strip() + '"}, "id" : "id"}'
                            elif err == 0 or err == "":
                                res = "<br/>"
                                for line in out.split('\n'):
                                    if line != "":
                                        res += "&nbsp;>>> " + line + "<br/>"
                                print '{"jsonrpc" : "2.0", "error" : {"code": 0, "message": "&nbsp;Результат: ' + res.strip() + '"}, "id" : "id"}'
                        else:
                            print '{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Архив не содержит папку maps или она пуста"}, "id" : "id"}'

                     #

                    else:
                        print '{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Прежде чем закачивать карты, запакуйте их в один zip-архив"}, "id" : "id"}'

                    os.remove(zipSaveTo)

                else:
                    print '{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Вы не указали файл для загрузки."}, "id" : "id"}'

            else:
                print '{"jsonrpc" : "2.0", "error" : {"code": 105, "message": "Сервер не привязан на этот физический сервер, продолжение невозможно."}, "id" : "id"}'

    else:
        print '{"jsonrpc" : "2.0", "error" : {"code": 104, "message": "Неверный владелец сервера"}, "id" : "id"}'

elif int(commonCursor.rowcount) == 0:
    print '{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Неверный пользователь"}, "id" : "id"}'
