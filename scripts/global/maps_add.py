#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Copies maps to correct tree for install access and and add info to db.
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
Скрипт упорядочивания файлов карт по директориям, а также
внесения их в базу в сыром режиме.

- Сканировать указанную директорию на наличие .bsp
- по имени карт создать директории вида de_dust/maps
  - Скопировать туда карту
  - Сохранить имя карты в отдельный массив
- Сканировать оставшиеся файлы в директории повторно и при совпадении с с именем карты
  скопировать в соответсвующую директорию
- Перейти в общую директорию
- По именам директорий карт проверить их наличие в базе
  - Если в базе карты нет - внести её

-m: Директория, где навалены карты кучей
    none - только сканировать директорию репозитория
-r: Директория репозитория карт БЕЗ имени игры
-g: Имя игры, напр, cs16, css, tf и т.д.

'''

import os
import fnmatch
import MySQLdb
import ConfigParser
from shutil import *
from optparse import OptionParser
from datetime import datetime

parser = OptionParser()

parser.add_option("-m", "--maps", action="store", type="string", dest="mapsPath")
parser.add_option("-r", "--repo", action="store", type="string", dest="repoPath")
parser.add_option("-g", "--game", action="store", type="string", dest="game")

(options, args) = parser.parse_args(args=None, values=None)

repoPath = options.repoPath + '/' + options.game
mapsPath = options.mapsPath
game = options.game

config = ConfigParser.RawConfigParser()
config.read('/etc/hosting/scripts.cfg')

# ID сервера, на котором пускается скрипт
# Брать его из поля id в админке

thisServerId = config.getint('server', 'serverID')
mysqlHost = config.get('db', 'host')
mysqlUser = config.get('db', 'user')
mysqlPass = config.get('db', 'pass')
mysqlDb = config.get('db', 'db')


# Возможен вариант, когда скрипт пускается уже в репозитории,
# тогда просто пропускаем кусок с копированием файлов

if mapsPath != 'none':

    mapNames = []
    for file in os.listdir(mapsPath):
        mapName = file.split('.')
        if fnmatch.fnmatch(file, '*.bsp'):
            mapNames.append(mapName[0])

            repoDir = os.path.join('/', repoPath, str(mapName[0]), 'maps')

            try:
                if not os.path.exists(repoDir + '/' + file):
                    print 'Create dir ' + repoDir
                    os.makedirs(repoDir)

            except OSError, e:
                print "Произошла ошибка: ", e

    for file in os.listdir(mapsPath):
        mapName = file.split('.')
        repoDir = os.path.join('/', repoPath, str(mapName[0]), 'maps')
        if str(mapName[0]) in mapNames:
            try:
                print 'Copy ' + file
                copyfile(os.path.join(mapsPath, file), repoDir + '/' + file)
            except OSError, e:
                print "Произошла ошибка: ", e

# host, user, pass, db

db = MySQLdb.connect(host=mysqlHost, user=mysqlUser, passwd=mysqlPass, db=mysqlDb)

# Create cursor with row names as array arguments
cursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)

# Получить ID шаблона по имени игры
cursor.execute('''  SELECT id
                    FROM  `game_templates`
                    WHERE  `name` LIKE  %s
                    LIMIT 1''', game)

gameTemplateId = cursor.fetchone()

# Получить список привязанных к игре карт
cursor.execute(''' SELECT  `Map`.`id`, `Map`.`name`, `Map`.`map_type_id`,
                            `GameTemplatesMap`.`game_template_id`,
                            `GameTemplatesMap`.`map_id`
                    FROM `maps`
                    AS
                         `Map`
                    JOIN
                          `game_templates_maps`
                    AS    `GameTemplatesMap`
                    ON
                          (`GameTemplatesMap`.`game_template_id` = %s
                                  AND
                            `GameTemplatesMap`.`map_id` = `Map`.`id`)''', gameTemplateId['id'])

presentMaps = []

# Составим массив из привязанных к игре карт
for i in range(0, int(cursor.rowcount)):
    presentMap = cursor.fetchone()
    presentMaps.append(presentMap['name'])

# Получить список типов карт
cursor.execute('''  SELECT  `id` ,  `name`
                    FROM  `map_types`
                    LIMIT 1000 ''')

# Состоавить массив из name -> id
mapTypes = {}
for i in range(0, int(cursor.rowcount)):
    mapType = cursor.fetchone()
    mapTypes[mapType['name']] = mapType['id']


for map in os.listdir(repoPath):
    mapPath = os.path.join(repoPath, map)
    if os.path.isdir(mapPath) and not map in presentMaps:
        mapParts = map.split('_')
        if mapParts[0] in mapTypes.keys():
            mapTypeId = mapTypes[mapParts[0]]
        else:
            mapTypeId = None

        addDate = datetime.now().strftime('%Y-%m-%d %H:%M:%S')

        # Составить длинное имя карты из частей карты с заглавными буквами
        j = 0
        longMapName = ''
        for mapPart in mapParts:
            if j != 0:
                longMapName += mapPart.capitalize() + ' '
            j += 1

        print 'Добавляю предварительное описание карты %s в базу' % map

        cursor.execute('''      INSERT INTO  `teamserver`.`maps` (
                                                                    `id` ,
                                                                    `name` ,
                                                                    `longname` ,
                                                                    `desc` ,
                                                                    `official` ,
                                                                    `map_type_id` ,
                                                                    `created` ,
                                                                    `modified`
                                                                )
                                VALUES (
                                            NULL ,
                                            %s,
                                            %s,
                                            '',
                                            '0',
                                            %s,
                                            %s,
                                            '0000-00-00 00:00:00'
                                );''', (map, longMapName, mapTypeId, addDate))


cursor.close()
db.commit()
db.close()
