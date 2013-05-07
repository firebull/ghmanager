#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Installs mods and plugins to clients SRCDS/HLDS server.
Runs plugins_install.py with clients rights.
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
print                               # blank line, end of headers

import MySQLdb
import ConfigParser
import cgi
import cgitb
import sys
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

parser.add_option("-s", "--server", action="store", type="int", dest="serverID")
parser.add_option("-p", "--plugin", action="store", type="int", dest="plugin")
parser.add_option("-t", "--type", action="store", type="string", dest="addonType")

(options, args) = parser.parse_args(args=None, values=None)

if options.serverID and options.plugin and options.addonType:
    serverID = options.serverID
    serverAddon = options.plugin
    addonType = options.addonType
else:
    server = cgi.FieldStorage()
    serverID = server["id"].value
    serverAddon = server["plugin"].value
    addonType = server["type"].value

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

        if user['steam_id'] == '' or user['steam_id'] == 'None':
            userSteamId = 'none'
        else:
            userSteamId = user['steam_id']

        if user['guid'] == '' or user['guid'] == 'None':
            userGuid = 'none'
        else:
            userGuid = user['guid']

         # Теперь получим из базы шаблон сервера и его вариант

        # Шаблон:
        template = defineTemplate(commonCursor, serverID)

        print '        Шаблон сервера: ', template['name'], '<br/>'

        serverPath = serversPath + "/" + template['name'] + "_" + str(serverID)
        installPath = template['addonsPath']

        # Попытка скопировать плагин

        # Установка пути источника
        # Делаю вот так вот в лоб, чтобы нельзя было
        # подставить любое другое значение и получить
        # доступ к другим папкам на сервере

        # плагин
        if str(addonType) == 'plugin':
             # Определяем пользователя

            pluginCursor.execute("""SELECT `name`,`version`,`moreParams` FROM `plugins` where `id`=%s""", serverAddon)
            plugin = pluginCursor.fetchone()
            addonPath = "/images/plugins/" + plugin['name']
            if plugin['version']:
                addonPath += '-' + plugin['version']
        # или мод
        elif str(addonType) == 'mod':
            pluginCursor.execute("""SELECT `name`,`version`,`moreParams` FROM `mods` where `id`=%s""", serverAddon)
            plugin = pluginCursor.fetchone()
            addonPath = "/images/mods/" + plugin['name']
            if plugin['version']:
                addonPath += '-' + plugin['version']

        # Теперь запускаем корневой инсталятор плагинов,
        # который, в свою очередь, считает всю информацию о
        # плагине, сервере и пользователе. Потом запустит
        # инсталятор конкретного плагина.
        #
        # Зачем так сложно?
        # Для безопасности. Чтобы все операции на уровне
        # физического сервера производились от имени пользователя,
        # а не Апачи или, Боже упаси, от рута.
        # А второе, скрипт инсталлятора также используется при
        # инициализации и потому его надо отделить.
        #
        # Передавать будем такие параметры:
        # -s serverPath - расположение сервера
        # -a addonPath - источник мода/плагина
        # -i installPath - куда инсталлировать относительно serverPath
        # -с configurator - имя конфигуратора мода/плагина
        # -p - Steam Id
        # -g - CoD GUID
        # -m - дополнительные параметры

        moreParams = ''
        if plugin['moreParams'] != None:
            more = plugin['moreParams'].strip(',').split(',')

            for param in more:
                if server[param] != None:
                    moreParams += str(server[param]) + ':'
                else:
                    moreParams += 'None:'

        if moreParams == '':
            moreParams = 'None:'

        try:
            retcode = Popen("sudo -u " + userName
                            + " /images/scripts/global/plugin_install.py "
                            + " -a " + addonPath
                            + " -s " + serverPath
                            + " -i " + installPath
                            + " -c " + plugin['name']
                            + " -p " + str(userSteamId)
                            + " -g " + str(userGuid)
                            + " -m " + str(moreParams.rstrip(':')),
                            shell=True,
                            stdin=PIPE,
                            stdout=PIPE,
                            stderr=PIPE)
            (out, err) = retcode.communicate()
            print out
            if err < 0:
                print "Не удалось запустить скрипт установки мода/плагина: ", err
            elif err == 0 or err == "":
                print "Работа скрипта скрипта установки мода/плагина завершена успешно."

            else:
                print "При попытке запуска скрипта установки мода/плагина возникла ошибка: ", err

        except OSError, e:
            print "При попытке запуска скрипта установки мода/плагина возникла ошибка: ", e

    else:
        print "Игровой сервер #%s привязан к другому физическому <br/>" % serverID
    # Конец проверки привязки
    #


else:
    print "Нет сервера, не инициализирован или заблокирован <br/>"


 # Закрываем все соединения и базу
serverCursor.close()
userCursor.close()
pluginCursor.close()
templateCursor.close()
rootServerCursor.close()

db.commit()
db.close()
