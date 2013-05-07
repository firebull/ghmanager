#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Updates servers in Repo.
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


import os
import re
import ConfigParser
import MySQLdb
import sys
sys.path.append("/images/scripts/global")
from db_queries import *
from subprocess import *
from datetime import datetime, date, time

config = ConfigParser.RawConfigParser()
config.read('/etc/hosting/scripts.cfg')

# ID сервера, на котором пускается скрипт
# Брать его из поля id в админке

thisServerId = config.getint('server', 'serverID')
mysqlHost = config.get('db', 'host')
mysqlUser = config.get('db', 'user')
mysqlPass = config.get('db', 'pass')
mysqlDb = config.get('db', 'db')

# Подключиться к базе
db = MySQLdb.connect(host=mysqlHost, user=mysqlUser, passwd=mysqlPass, db=mysqlDb)
commonCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)

p = re.compile('current')

print "Запуск скрипта обновления всех шаблонов серверов"
print datetime.now().strftime("%A, %d. %B %Y %H:%M:%S")

for dirname, dirnames, filenames in os.walk('/images/servers'):
    # Если текущая директория current, останавливаем рекурсию и идём дальше
    if not p.search(dirname):
        for subdirname in dirnames:
            if subdirname != 'current':
                scriptPath = os.path.join(dirname, subdirname)
                script = scriptPath + "/update.sh"
                print 'Поиск скрипта обновления: ' + scriptPath
                if os.path.exists(script):
                    print "   Скрипт найден, попытка запуска:"
                    os.chdir(scriptPath)
                    try:
                        retcode = Popen(script,
                                        shell=True,
                                        stdin=PIPE,
                                        stdout=PIPE,
                                        stderr=PIPE)
                        (out, err) = retcode.communicate()
                        print out

                        # Ищем версию сервера и сохраняем её в базу
                        paramsConfigPath = scriptPath + '/params.cfg'
                        if os.path.exists(paramsConfigPath):
                            paramsConfig = ConfigParser.RawConfigParser()
                            paramsConfig.read(paramsConfigPath)

                            versionGame = paramsConfig.get('version', 'name')
                            versionInfoFile = os.path.join(scriptPath, paramsConfig.get(
                                'version', 'path'), paramsConfig.get('version', 'file'))
                            versionField = paramsConfig.get('version', 'field')

                            if os.path.exists(versionInfoFile):
                                try:
                                    inf = open(versionInfoFile, 'r')

                                    for line in inf:
                                        if re.match(versionField, line, flags=re.IGNORECASE):
                                            versionLine = line.strip().split('=')
                                            print 'Текущая версия игры ' + versionGame + ': ' + versionLine[1]

                                            # Сохранить в базу
                                            # Сначала проверить, есть ли такой шаблон в базе
                                            check = checkGameTemplate(commonCursor, versionGame)
                                            if check != None:
                                                # Такой шаблон в базе есть, сохраняем версию
                                                print "Пишу версию сервера в базу"
                                                saveGameTemplateVersion(db, commonCursor, check['id'], versionLine[1])

                                            break

                                    inf.close()

                                except OSError, e:
                                    print "   При попытке обработки версии игры возникла ошибка: ", e

                    except OSError, e:
                        print "   При попытке запуска скрипта возникла ошибка: ", e
                else:
                    print "   Скрипт не найден, иду дальше."
            else:
                break

commonCursor.close()

db.commit()
db.close()
