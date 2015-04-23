#!/usr/bin/env python2
# coding: UTF-8
# Скипт предназначен для запуска/остановки/перезапуска серверов

'''
***********************************************
Main script to start/stop/restart/update server.
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


import MySQLdb
import os
import pwd
import sys
import re
import cgi
import ConfigParser
sys.path.append("/images/scripts/global")
from db_queries import *
from common import *
from time import sleep
from subprocess import *
from shutil import *
from optparse import OptionParser


def serverAction(action, type, configName):
    retcode = Popen("sudo -u " + userName
                    + " ./start_stop_" + type + ".py "
                    + " -a " + action
                    + " -c " + configName,
                    shell=True,
                    stdin=PIPE,
                    stdout=PIPE,
                    stderr=PIPE)
    (out, err) = retcode.communicate()
    print out
    if err < 0:
        print "Не удалось запустить скрипт запуска/остановки сервера: ", err
        return False
    elif err == 0 or err == "":
        print "Работа скрипта скрипта запуска/остановки сервера завершена успешно."
        return True
    else:
        print "При попытке запуска скрипта запуска/остановки сервера возникла ошибка: ", err
        return False

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

parser.add_option("-a", "--action", action="store", type="string", dest="action")
parser.add_option("-s", "--server", action="store", type="int", dest="serverID")

(options, args) = parser.parse_args(args=None, values=None)

if options.serverID and options.action:
    serverID = str(options.serverID)
    action = options.action
else:
    params = cgi.FieldStorage()
    serverID = str(params["s"].value).strip()
    action = str(params["a"].value).strip()
    print "Content-Type: text/html"     # HTML is following
    print                               # blank line, end of headers

# Получить данные сервера из базы
db = MySQLdb.connect(host=mysqlHost, user=mysqlUser, passwd=mysqlPass, db=mysqlDb)

# Create cursor with row names as array arguments
serverCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)
commonCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)

if action == 'start':
    serverCursor.execute("""SELECT * FROM servers where payedTill > NOW() AND initialised = 1 AND id = %s
                        ORDER BY `servers`.`created`  DESC LIMIT 1""", [serverID])
else:
    serverCursor.execute("""SELECT * FROM servers where initialised = 1 AND id = %s
                        ORDER BY `servers`.`created`  DESC LIMIT 1""", [serverID])

numrows = int(serverCursor.rowcount)

if numrows > 0:
    print "Данные о сервере получены"

    server = serverCursor.fetchone()

    # Проверяем привязку игрового сервера к нашему физическому
    rootServer = defineRootServer(commonCursor, serverID)

    if rootServer['id'] == thisServerId:
        print "Сервер привязан на этот физический сервер. Продолжаю."

        print "Получаю данные о пользователе"

        # Определяем пользователя
        user = defineUser(commonCursor, serverID)

        userName = "client%s" % user['id']
        userEmail = user['email']
        homeDir = "/home/%s" % userName
        serversPath = homeDir + "/servers"

        pw = pwd.getpwnam(userName)
        userGid = pw.pw_gid

        # Теперь получим из базы шаблон сервера
        template = defineTemplate(commonCursor, serverID)

        # Тип - достаем его из шаблона:
        type = defineType(commonCursor, str(template['id']))

        print 'Шаблон сервера: ', template['name']

        templateName = template['name']
        templateRootPath = template['rootPath']
        templateAddonPath = template['addonsPath']
        serverPath = serversPath + '/' + template['name'] + '_' + serverID + '/' + template['rootPath']
        userName = "client%s" % user['id']
        userEmail = user['email']
        ip = str(server['address'])
        port = str(server['port'])
        slots = str(server['slots'])
        slotsMax = str(template['slots_max'])
        tvSlots = str(server['tvSlots'])
        map = str(server['map'])
        autoUpdate = str(server['autoUpdate'])
        vac = str(server['vac'])
        fpsmax = str(server['fpsmax'])
        nomaster = str(server['nomaster'])
        tickrate = str(server['tickrate'])
        punkbuster = str(server['punkbuster'])
        rconPassword = str(server['rconPassword'])
        debug = str(server['debug'])
        setAdminPass = str(server['setAdmPass'])
        token = str(server['action_token'])

        if fpsmax == '' or fpsmax.lower() == 'none':
            fpsmax = 'none'

        # Создание файла конфигурации, куда запишем все необходимые
        # параметры для запуска-остановки. Конфиг будет иметь права
        # только на чтение для других пользователей.
        try:
            configName = templateName + "-" + serverID + ".cfg"
            config = "/home/configurator/startCfgs/" + configName
            runScriptCfg = open(config, "w")

            runScriptCfg.write("[server]\n")
            runScriptCfg.write("id: %s\n" % serverID)
            runScriptCfg.write("type: %s\n" % type['name'])
            runScriptCfg.write("template: %s\n" % templateName)
            runScriptCfg.write("templateRootPath: %s\n" % templateRootPath)
            runScriptCfg.write("templateAddonPath: %s\n" % templateAddonPath)
            runScriptCfg.write("user: %s\n" % userName)
            runScriptCfg.write("email: %s\n" % userEmail)
            runScriptCfg.write("ip: %s\n" % ip)
            runScriptCfg.write("port: %s\n" % port)
            runScriptCfg.write("slots: %s\n" % slots)
            runScriptCfg.write("slotsMax: %s\n" % slotsMax)
            runScriptCfg.write("tvSlots: %s\n" % tvSlots)
            runScriptCfg.write("map: %s\n" % map)
            runScriptCfg.write("hostmap: %s\n" % server['hostmap'])
            runScriptCfg.write("authkey: %s\n" % server['authkey'])
            runScriptCfg.write("hostcollection: %s\n" % server['hostcollection'])
            runScriptCfg.write("mapGroup: %s\n" % server['mapGroup'])
            runScriptCfg.write("autoUpdate: %s\n" % autoUpdate)
            runScriptCfg.write("vac: %s\n" % vac)
            runScriptCfg.write("fpsmax: %s\n" % fpsmax)
            runScriptCfg.write("nomaster: %s\n" % nomaster)
            runScriptCfg.write("tickrate: %s\n" % tickrate)
            runScriptCfg.write("codMod: %s\n" % server['mod'])
            runScriptCfg.write("csgoGameMode: %s\n" % server['mod'])
            runScriptCfg.write("punkbuster: %s\n" % punkbuster)
            runScriptCfg.write("rconPass: %s\n" % rconPassword)
            runScriptCfg.write("debug: %s\n" % debug)
            runScriptCfg.write("setAdminPass: %s\n" % setAdminPass)
            runScriptCfg.write("token: %s\n" % token)
            runScriptCfg.close()

            '''
            Определим запускаемый скрипт
            Пока что поставлю тут костыль, но вообще
            необходимо определять скрипт автоматом
            по типу сервера
            '''

            if type['name'] == 'cod':
                script = "start_stop_cod.py"
            elif type['name'] == 'ueds':
                script = "start_stop_ueds.py"
            elif template['name'] == 'mumble':
                script = "start_stop_mumble.py"
            else:
                script = "start_stop_valve.py"

            # Сначала установить права на исполняемые файлы, потом залить последний nemrun
            if action in ('start', 'startHltv', 'startWithManu'):
                retcode = Popen("sudo -u root "
                                + " ./set_permissions.py"
                                + " -s " + serverPath
                                + " -a " + template['addonsPath']
                                + " -u " + userName,
                                shell=True,
                                stdin=PIPE,
                                stdout=PIPE,
                                stderr=PIPE)
                (out, err) = retcode.communicate()
                if err < 0:
                    print "Не удалось установить права на файлы сервера: ", err
                    raise
                elif err == 0 or err == "":
                   # print out
                    print "Права установлены успешно."

            # Теперь, наконец, совершаем запуск/стоп/обновление
            if action in ('start', 'startHltv', 'startWithManu', 'stop', 'stopHltv'):
                try:
                    retcode = Popen("sudo -u " + userName
                                    + " ./" + script
                                    + " -a " + action
                                    + " -c " + configName,
                                    shell=True,
                                    stdin=PIPE,
                                    stdout=PIPE,
                                    stderr=PIPE,
                                    env={'HOME': homeDir})
                    (out, err) = retcode.communicate()
                    for line in out.splitlines():
                        if re.match('EXEC_STATUS', line):  # Искать надо только по началу строки
                            # Обработка и запись статуса в базу
                            statusDate = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
                            status = line.split(":")
                            if status[1].strip() == "error":
                                serverStatus = "exec_error"
                                description = status[2] + ' >> ' + str(err)
                            elif status[1].strip() == "success":
                                if action == "start" or action == 'startHltv' or action == 'startWithManu':
                                    serverStatus = "exec_success"
                                elif action == "stop" or action == 'stopHltv':
                                    serverStatus = "stopped"
                                description = ''
                            try:
                                if action == 'start' or action == 'stop' or action == 'startWithManu':
                                    saveServerStatus(db, commonCursor, serverID, serverStatus, description, statusDate)
                                elif action == 'startHltv' or action == 'stopHltv':
                                    saveServerTvStatus(db, commonCursor, serverID, serverStatus, description, statusDate)
                            except Exception, e:
                                print "Возникла ошибка при сохранении статуса в БД: '" + e + "'. <br/>Обратитесь в техподдержку."

                        else:
                            print line

                    if err < 0:
                        print "Не удалось запустить скрипт запуска/остановки сервера: ", err
                    elif err == 0 or err == "":
                        print "Работа скрипта скрипта запуска/остановки сервера завершена успешно."

                    else:
                        print "При попытке запуска скрипта запуска/остановки сервера возникла ошибка: ", err

                except OSError, e:
                    print "При попытке запуска скрипта запуска/остановки сервера возникла ошибка: ", e
            elif action == 'update':
                if serverAction('stop', 'valve', configName):
                    try:
                        # Сначала восстановить все разрешения файлов
                        call("chown" + " -R " + userName + ':' + userName + " " + serverPath, shell=True)
                        retcode = Popen("sudo -u " + userName
                                        + " ./" + script
                                        + " -a " + action
                                        + " -c " + configName,
                                        shell=True,
                                        stdin=PIPE,
                                        stdout=PIPE,
                                        stderr=PIPE,
                                        env={'HOME': homeDir})
                        (out, err) = retcode.communicate()
                        for line in out.splitlines():
                            if re.match('EXEC_STATUS', line):  # Искать надо только по началу строки
                                # Обработка и запись статуса в базу
                                statusDate = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
                                status = line.split(":")
                                if status[1].strip() == "error":
                                    serverStatus = "update_error"
                                    description = status[2] + ' >> ' + str(err)
                                elif status[1].strip() == "success":
                                    serverStatus = "update_started"
                                    description = ''
                                try:
                                    saveServerStatus(db, commonCursor, serverID, serverStatus, description, statusDate)
                                except:
                                    print "Возникла ошибка при сохранении статуса в БД. <br/>Обратитесь в техподдержку."

                            else:
                                print line
                        if err < 0:
                            print "Не удалось запустить скрипт обновления сервера: ", err
                        elif err == 0 or err == "":
                            print "<strong>Запуск скрипта обновления произведен успешно. \nЧитайте соответсвующий лог с результатами.\n<span style='color: #FF6600;'>После обновления сервер потребуется запустить вручную.</span></strong>"

                        else:
                            print "При попытке запуска скрипта обнорвления сервера возникла ошибка: ", err

                    except OSError, e:
                        print "При попытке запуска скрипта обновления сервера возникла ошибка: ", e
                else:
                    print "Перед обновлением требуется остановка сервера, которая не удалась."

            elif action in ('restart', 'restartHltv', 'restartWithManu'):
                print "Перезапуск сервера"

                startAddon = ''
                if action in 'restart':
                    action = 'stop'
                elif action in 'restartWithManu':
                    action = 'stop'
                    startAddon = 'Manu'
                elif action == 'restartHltv':
                    action = 'stopHltv'

                try:
                    retcode = Popen("sudo -u " + userName
                                    + " ./" + script
                                    + " -a " + action
                                    + " -c " + configName,
                                    shell=True,
                                    stdin=PIPE,
                                    stdout=PIPE,
                                    stderr=PIPE)
                    (out, err) = retcode.communicate()
                    for line in out.splitlines():
                        if re.match('EXEC_STATUS', line):  # Искать надо только по началу строки
                            # Обработка и запись статуса в базу
                            statusDate = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
                            status = line.split(":")
                            if status[1].strip() == "error":
                                serverStatus = "exec_error"
                                description = status[2] + ' >> ' + str(err)
                            elif status[1].strip() == "success":
                                serverStatus = "stoped"
                                description = ''
                            try:
                                if action == 'stop':
                                    saveServerStatus(db, commonCursor, serverID, serverStatus, description, statusDate)
                                elif action == 'stopHltv':
                                    saveServerTvStatus(db, commonCursor, serverID, serverStatus, description, statusDate)
                            except:
                                print "Возникла ошибка при сохранении статуса в БД. <br/>Обратитесь в техподдержку."

                        else:
                            print line
                    if err < 0:
                        print "Не удалось остановить сервер: ", err
                    elif err == 0 or err == "":
                        print "Сервер остановлен"
                        #
                        print "Запуск сервера:"
                        if templateName in ('zps', 'cssv34', 'l4d'):
                            print "Подожду 5 секунд."
                            sleep(5)

                        if action == 'stop':
                            if startAddon != '':
                                action = 'startWith' + startAddon
                            else:
                                action = 'start'
                        elif action == 'stopHltv':
                            action = 'startHltv'

                        try:
                            retcode = Popen("sudo -u " + userName
                                            + " ./" + script
                                            + " -a " + action
                                            + " -c " + configName,
                                            shell=True,
                                            stdin=PIPE,
                                            stdout=PIPE,
                                            stderr=PIPE,
                                            env={'HOME': homeDir})
                            (out, err) = retcode.communicate()
                            for line in out.splitlines():
                                if re.match('EXEC_STATUS', line):  # Искать надо только по началу строки
                                    # Обработка и запись статуса в базу
                                    statusDate = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
                                    status = line.split(":")
                                    if status[1].strip() == "error":
                                        serverStatus = "exec_error"
                                        description = status[2] + ' >> ' + str(err)
                                    elif status[1].strip() == "success":
                                        serverStatus = "exec_success"
                                        description = ''
                                    try:
                                        if action == 'start' or re.match('startWith', action):
                                            saveServerStatus(db, commonCursor, serverID, serverStatus, description, statusDate)
                                        elif action == 'startHltv':
                                            saveServerTvStatus(
                                                db, commonCursor, serverID, serverStatus, description, statusDate)
                                    except:
                                        print "Возникла ошибка при сохранении статуса в БД. <br/>Обратитесь в техподдержку."

                                else:
                                    print line
                            if err < 0:
                                print "Не удалось запустить сервер: ", err
                            elif err == 0 or err == "":
                                print "Сервер запущен"
                            else:
                                print "При попытке запустить сервер возникла ошибка: ", err

                        except OSError, e:
                            print "При попытке запустить сервер возникла ошибка: ", e
                        #
                    else:
                        print "При попытке остановить сервер возникла ошибка: ", err

                except OSError, e:
                    print "При попытке остановить сервер возникла ошибка: ", e

            # Удалить конфиг запуска
            os.remove(config)

            # Конец запуск/стоп/обновление
            #

        except OSError, e:
                print "Ошибка создания конфига запуска: ", e

    else:
        print "Сервер привязан к другому физическому."

# Закрываем все соединения и базу
serverCursor.close()
commonCursor.close()

db.commit()
db.close()
