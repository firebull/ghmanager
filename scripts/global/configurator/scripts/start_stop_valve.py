#!/usr/bin/env python2
# coding: UTF-8
# Скипт предназначен для запуска/остановки/перезапуска srcds-серверов

'''
***********************************************
Valve SRCDS/HLDS Start/Stop script with clients rights.
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
import pwd
import sys
sys.path.append("/images/scripts/global")
from common import *
from commonLib import demoZip
import distutils.dir_util
import cgi
import cgitb
import ConfigParser
import socket
import start_strings   # Командные строки запуска
import update_strings  # Командные строки обновления
from time import sleep
from glob import glob
from subprocess import *
from shutil import *
from shutil import ignore_patterns
from optparse import OptionParser
from datetime import datetime, date, time


parser = OptionParser()

parser.add_option("-c", "--config", action="store", type="string", dest="config")
parser.add_option("-a", "--action", action="store", type="string", dest="action")

(options, args) = parser.parse_args(args=None, values=None)

if options.config and options.action:
    serverConfig = options.config
    action = options.action

else:
    print "Content-Type: text/html"     # HTML is following
    print                               # blank line, end of headers
    params = cgi.FieldStorage()
    config = str(params["c"].value).strip()
    action = str(params["a"].value).strip()
    cgitb.enable()  # Debug

# Остальные параметры брать из конфига, на который права только на чтение
config = ConfigParser.RawConfigParser()
config.read('/home/configurator/startCfgs/' + serverConfig)

serverID = config.get('server', 'id')
serverIP = config.get('server', 'ip')
serverPort = config.getint('server', 'port')
serverSlots = config.get('server', 'slots')
slotsMax = config.get('server', 'slotsMax')
tvSlots = config.get('server', 'tvSlots')
serverMap = config.get('server', 'map')
mapGroup = config.get('server', 'mapGroup')
gameMode = config.get('server', 'csgoGameMode')
autoUpdate = config.get('server', 'autoUpdate')
vac = config.get('server', 'vac')
fpsmax = config.get('server', 'fpsmax')
nomaster = config.get('server', 'nomaster')
tickrate = config.get('server', 'tickrate')
debug = config.get('server', 'debug')
token = config.get('server', 'token')
hostmap = config.get('server', 'hostmap')
hostcollection = config.get('server', 'hostcollection')
authkey = config.get('server', 'authkey')

userName = config.get('server', 'user')
userEmail = config.get('server', 'email')
typeName = config.get('server', 'type')
templateName = config.get('server', 'template')
templateRootPath = config.get('server', 'templateRootPath')
templateAddonPath = config.get('server', 'templateAddonPath')

homeDir = "/home/%s" % userName
serversPath = homeDir + "/servers"
serverRootPath = serversPath + "/" + templateName + "_" + str(serverID) + "/"
serverRunPath = serversPath + "/" + templateName + "_" + str(serverID) + "/" + templateRootPath
serverAddonPath = os.path.join(serverRootPath, templateAddonPath)
serverPidPath = "/home/pid/" + userName

if action == 'startHltv' or action == 'stopHltv':
    pidScreenName = templateName + "-tv-" + serverID + "-screen.pid"
    pidServerName = templateName + "-tv-" + serverID + ".pid"
    pidName = templateName + "-tv-" + serverID
else:
    pidScreenName = templateName + "-" + serverID + "-screen.pid"
    pidServerName = templateName + "-" + serverID + ".pid"
    pidName = templateName + "-" + serverID

pidScreenUpdateName = templateName + "-" + serverID + "-update-screen.pid"
pidUpdateName = templateName + "-" + serverID + "-update"

print datetime.now().strftime("%A, %d. %B %Y %H:%M:%S%p")
#
# Если есть screenlog.0 в корне сервера,
# провести его ротацию, чтобы при следующем запуске
# в режиме ротации, лог писался в другой файл.
if os.path.exists(serverRunPath + '/screenlog.0'):
    screenLogsList = glob(serverRunPath + '/screenlog.*')

    # Если логов уже несколько, произвести ротацию всех
    screenLogsNum = len(screenLogsList)
    try:
        # Если логов больше 9, то удалить самый старый
        if (screenLogsNum > 9):
            os.remove(screenLogsList[screenLogsNum - 1])
            screenLogsNum = screenLogsNum - 1

        # Сдвинуть индексы всех на единицу
        while screenLogsNum > 0:
            os.rename(screenLogsList[screenLogsNum - 1], serverRunPath + '/screenlog.' + str(screenLogsNum))
            screenLogsNum = screenLogsNum - 1

    except OSError, e:
        print "При ротации логов SCREEN возникла ошибка", e

#
# Установка новой утилиты обновления
if templateName in ['cs16']:
    steamcmdPath = '/images/mods/steamcmd-1.1'
    serverSteamcmd = os.path.join(serverRootPath, 'steamcmd/')
    if not os.path.exists(serverSteamcmd + 'steamcmd.sh'):
        print "Копирую SteamCMD..."
        try:
            distutils.dir_util.copy_tree(steamcmdPath, serverSteamcmd, preserve_symlinks=1)
        except OSError, e:
            print "Не удалось скопировать SteamCMD:", e
#

#
# SERVER START

if action == "start" or action == 'startHltv':
    # 1) Сначала выполняем условия ошибок, потом действий
    # 2) Проверить, занят ли порт сервера. Если да - выдать сообщение и прекратить дальнейшие операции
    # 3) Если порт свободен, проверить наличие pid - SCREEN и сервера
    # 4) Если существует pid сервера, пытаемся его прочесть
    #    - Если пустой - выдать сообщение о вероятной проблеме запуска и рекомендации принудительной остановки
    #    - Если непустой, проверить, запущен ли такой процесс.
    #      - Если запущен, выдать сообщение, что сервер запущен с ошибкой, либо занял другой порт. Рекомендация перезапуска.
    #      - Если процесса нет - удалить pid сервера и SCREEN и продолжить запуск
    # 5) Если pid сервера не существует, но существует pid SCREEN, пытаемся его прочесть
    #
    #    - Если непустой, проверить, запущен ли такой процесс
    #      - Если запущен:
    #        - Если существуют nemrun и nemrun.lock, выдать сообщение, что, вероятно, идёт процесс запуска либо обновления
    #        - Если существует update SCREEN, выдать сообщение, что идёт процесс обновления с просьбой подождать
    #        - Если не существует nemrun, выдать сообщение, что идёт процесс запуска сервера и просьба подождать, а также
    #          при длительном ожидании, принудительная остановка
    #      - Если не запущен или пустой pid-файл, удалить pid SCREEN и продолжить запуск
    # 6) Если в директории сервера есть nemrun, то выполнить запуск с помощью него
    #    - Иначе выполнить обычный запуск
    # 7) Получить pid запущенного процесса и записать его в pid SCREEN
    # 8) Выдать соответсвущее сообщение

    #
    # Удалить не позднее 5 марта 2012 и вернуть полную установку
    # прав в setExecFileOwner!!!
    '''
    if typeName == 'srcds' and not serverID in ('1597', '1747'):
        fromPath = '/images/scripts/nemrun/1.8.5'

        for file in os.listdir(fromPath):
            try:
                fromFile = os.path.join(fromPath, file)
                toFile = os.path.join(serverRunPath, file)
                if not os.path.exists(toFile) or os.path.getsize(fromFile) != os.path.getsize(toFile):
                    # Скопировать новые скрипты запуска
                    if os.path.exists(toFile):
                        os.remove(toFile)

                    copyfile(fromFile, toFile)
                    os.chmod(toFile, 0770)

            except Exception, e:
                print "Ошибка при создании скриптов запуска", e
    '''
    #

    #
    # Костыль на баг с CS:GO и CS:S при котором сервер
    # не включается, если есть два файла стима в корне
    if typeName == 'srcds' and templateName in ('css', 'csgo', 'csgo-t128'):
        print "Удаляю проблемные файлы Steam"
        steampathFile = homeDir + '/.steampath'
        steampidFile = homeDir + '/.steampid'

        if os.path.lexists(steampathFile):
            os.unlink(steampathFile)

        if os.path.lexists(steampidFile):
            os.unlink(steampidFile)

    print "Попытка запуска сервера"

    '''
    Проверка на потерянные демо

    '''
    warmodDemsPath = os.path.join(serverAddonPath, 'warmod/')

    lostDems = []

    if os.path.exists(warmodDemsPath):
        for dem in os.listdir(warmodDemsPath):
            if os.path.isfile(os.path.join(warmodDemsPath, dem)) and dem.endswith('.dem'):
                lostDems += ['warmod/' + dem]

    for dem in os.listdir(serverAddonPath):
        if os.path.isfile(os.path.join(serverAddonPath, dem)) and dem.endswith('.dem'):
            lostDems += [dem]

    if len(lostDems) > 0:
        print "<span style='color: #FF6600;'>Найдены потеряные демо, попытка их архивации и переноса:</span>"
        for lostDem in lostDems:

            lostDemPath = os.path.join(serverAddonPath, lostDem)

            if os.path.exists(lostDemPath):
                print "<span style='color: #FF6600;'> >>>" + str(lostDem) + ': ' + str(os.path.getsize(lostDemPath)) + " байт </span>"
                if demoZip(lostDemPath) == True:
                    print "<span style='color: #FF6600;'> >>>" + str(lostDem) + " -> Успешно</span>"
                else:
                    print "<span style='color: #FF6600;'> >>>" + str(lostDem) + " -> Ошибка</span>"
            else:
                print "<span style='color: #FF6600;'> >>>" + str(lostDem) + " -> Закрыт на запись и перенесён</span>"

    '''
    Конец поиска потерянных демо
    '''
    # Проверка, занят ли порт сервера
    portStatus = checkOnPort(serverIP, serverPort)
    # portStatus = checkOnPort('192.168.1.201', serverPort) #debug
    if portStatus == False:
        # Порт занят
        print "Порт сервера занят. Если сервер недоступен, попробуйте принудительный перезапуск."

    elif portStatus == True:
        # Порт свободен
        # Проверка pid сервера
        errors = 0
        if os.path.exists(serverPidPath + "/" + pidServerName):
            print "PID сервера существует, но порт сервера открыт. Проверка статуса"
            if (os.path.getsize(serverPidPath + "/" + pidServerName) == 0):
                print 'Сервер не запущен. Скорее всего возникла проблема.'
                print 'Проверьте конфигурационные файлы, установленные плагины.'
                print 'Возможно, что проблема возникла после обновления.'
                print 'Тогда рекомендуем обновить установленные моды и плагины и '
                print 'повторить попытку, совершив принудительный запуск.'
                errors += 1

            else:
                print "Проверка процесса:"
                serverPidFile = open(serverPidPath + "/" + pidServerName, "r")
                for line in serverPidFile:
                    # Проверка форков, если процессов в файле несколько
                    if os.path.exists("/proc/%s" % line):
                        print "Сервер с pid", line, "запущен. Возможно он занял другой порт. Выполните перезапуск"
                        errors += 1
                        break
                    else:
                        print "Очистка после неудачного запуска"
                        try:
                            os.remove(serverPidPath + "/" + pidServerName)
                            if os.path.exists(serverPidPath + "/" + pidScreenName):
                                os.remove(serverPidPath + "/" + pidScreenName)
                            errors = 0
                            break
                        except OSError, e:
                            print e, "- Не удалось совершить очистку. Свяжитесь с техподдержкой."
                            errors += 1
                            break
                serverPidFile.close()
        elif (not os.path.exists(serverPidPath + "/" + pidServerName)) and os.path.exists(serverPidPath + "/" + pidScreenName):
            if action == 'startHltv':
                print "Pid Screen уже существует, проверка запущен ли сервер."
            else:
                print "Сервер не запущен, но есть pid Screen"
            if (os.path.getsize(serverPidPath + "/" + pidScreenName) > 0):
                serverScreenFile = open(serverPidPath + "/" + pidScreenName, "r")
                for line in serverScreenFile:
                    # print "Проверка процесса:", line
                    if os.path.exists("/proc/%s" % line.strip()):
                        # Проверка на наличие nemrun и pid SCREEN
                        if os.path.exists(serverRunPath + "/nemrun") and os.path.exists(serverRunPath + "/update.lock"):
                            print "<span style='color: #FF6600;'><strong>Сервер в процессе запуска и/или автоматического обновления.</strong></span>"
                            print "Вы можете прочесть статус обновления в лог файлах "
                            print "из панели управления или в директории logs\\server\\startup. "
                            print "Если процесс обновления происходит излишне долго, "
                            print "можете попробовать принудительный перезапуск.  "
                            print "Если в лог файле есть ошибки, свяжитесь с техподдержкой."

                        elif action == 'startHltv':
                            print "Сервер HLTV Proxy уже запущен."
                        else:
                            print "<span style='color: #FF6600;'><strong>Сервер в процессе запуска. Пожалуйста, подождите.</strong></span>"
                            print "Если процесс запуска происходит излишне долго, "
                            print "можете попробовать принудительный перезапуск. "
                            print "Если по-прежнему сервер не запустится, проверьте файлы конфигурации, "
                            print "установленные плагины. "
                            print "Возможно, что проблема возникла после обновления. "
                            print "Тогда рекомендуем обновить установленные моды и плагины и "
                            print "повторить попытку, совершив принудительный перезапуск."
                        errors += 1
                        break
                    else:
                        print "Очистка после неудачного запуска"
                        try:
                            os.remove(serverPidPath + "/" + pidScreenName)
                            errors = 0
                            break
                        except OSError, e:
                            print e, "- Не удалось совершить очистку. Свяжитесь с техподдержкой."
                            print "EXEC_STATUS:error:" + str(e)
                            errors += 1
                            break
                serverScreenFile.close()
            else:
                print "Очистка после неудачного запуска"
                try:
                    os.remove(serverPidPath + "/" + pidScreenName)
                    errors = 0
                except OSError, e:
                    print e, "- Не удалось совершить очистку. Свяжитесь с техподдержкой."
                    print "EXEC_STATUS:error:" + str(e)
                    errors += 1

        # Проверка на запущенное обновление
        if os.path.exists(serverPidPath + "/" + pidScreenUpdateName) and (os.path.getsize(serverPidPath + "/" + pidScreenUpdateName) > 0):
            serverUpdateScreenFile = open(serverPidPath + "/" + pidScreenUpdateName, "r")
            for procLine in serverUpdateScreenFile:
                # print "Проверка процесса:", line
                if os.path.exists("/proc/%s" % procLine.strip()):
                    print "<span style='color: #FF6600;'><strong>Сервер в процессе обновления.</strong></span>"
                    print "Вы можете прочесть статус обновления в лог файлах "
                    print "из панели управления или в директории logs\\server\\update. "
                    print "Если процесс обновления происходит излишне долго, "
                    print "или в лог файле есть ошибки, свяжитесь с техподдержкой."
                    errors += 1
                    serverUpdateScreenFile.close()
                    break

            serverUpdateScreenFile.close()
            # Если нет процесса обновления, удалить старый pid
            print "Очистка старого Update PID"
            try:
                os.remove(serverPidPath + "/" + pidScreenUpdateName)
            except OSError, e:
                print e, "- Не удалось совершить очистку. Свяжитесь с техподдержкой."
                print "EXEC_STATUS:error:" + str(e)
                errors += 1

        if errors > 0:
            print "<span style='color: #FF6600;'><strong>В процессе попытки запуска возникло %s ошибок требующих устранения. <br/>Запуск сервера невозможен.</strong></span>" % errors
        elif errors == 0:
            print "Проверка была успешной. Выполняю запуск сервера."

            if action == 'start':
                command = start_strings.dedicatedStart(serverID,
                                                       userName,
                                                       userEmail,
                                                       serverIP,
                                                       serverPort,
                                                       serverSlots,
                                                       slotsMax,
                                                       templateName,
                                                       serverMap,
                                                       mapGroup,
                                                       hostmap,
                                                       hostcollection,
                                                       authkey,
                                                       gameMode,
                                                       autoUpdate,
                                                       vac,
                                                       fpsmax,
                                                       nomaster,
                                                       tickrate,
                                                       debug)
            elif action == 'startHltv':
                command = start_strings.hltvStart(serverID,
                                                  userName,
                                                  serverIP,
                                                  serverPort,
                                                  tvSlots,
                                                  templateName)
            # print "Строка запуска:\n", command #debug
            os.chdir(serverRunPath)  # Переход в директорию сервера
            try:
                # Удалить скрипт обновления, если есть
                updateScriptName = templateName + "-" + serverID + "-update.sh"
                updateScriptPath = serverRootPath + updateScriptName

                if os.path.exists(updateScriptPath):
                    os.remove(updateScriptPath)

                retcode = call(command, shell=True)
                if retcode < 0:
                    print "Команда была прервана с кодом: ", str(retcode)
                    print "EXEC_STATUS:error:" + str(retcode)
                elif retcode == 0:
                    print "Процесс запуска сервера инициирован. Это может занять <br/>продолжительное время, если сервер требует обновления."
                    if debug == '1':
                        print "<span style='color: #FF6600;'><strong>ВНИМАНИЕ! Сервер запущен в режиме отладки и будет выключен через 30 минут!</strong></span>"
                    # Запись PID Screen
                    # ps -ef | grep SCREEN | grep "$NAME" | grep -v grep | awk '{print $2}' > $PID/$NAME-screen.pid
                    try:
                        retcode = call("ps -ef | grep SCREEN | grep \"" + pidName +
                                       "\" | grep -v grep | awk '{print $2}' > " + serverPidPath + "/" + pidScreenName, shell=True)
                        if retcode < 0:
                            print "Не удалось создать screen pid: ", str(retcode)
                            print "EXEC_STATUS:error:" + str(retcode)
                        elif retcode == 0:
                            print "ID процесса записан в screen pid"
                            print "EXEC_STATUS:success"
                        else:
                            print "При создании pid screen возникла ошибка: ", str(retcode)
                            print "EXEC_STATUS:error:" + str(retcode)

                    except OSError, e:
                        print "При попытке создания pid screen возникла ошибка:", e
                        print "EXEC_STATUS:error:" + str(e)

                else:
                    print "Команда вернула код: ", str(retcode)

            except OSError, e:
                print "Команда завершилась неудачей:", e
                print "EXEC_STATUS:error:" + str(e)
#
# SERVER STOP

elif action == 'stop' or action == 'stopHltv':
# 1) Все системные операции совершать через sudo
# 2) - Если существует screen pid
#      - Если он не пустой, считать его содержимое
#        - Циклически kill 9 всё его содержимое
#        - Удалить все мёртвые сессии
#      - Если пустой, удалить и вывести соотв сообщение
#      - Удалить pid
#    - Если не существует, вывести сообщение
# 3) - Если существует pid сервера, удалить его

    print "Попытка остановки сервера"
    if os.path.exists(serverPidPath + "/" + pidScreenName):
        if (os.path.getsize(serverPidPath + "/" + pidScreenName) > 0):
            print "Проверка процесса"
            serverScreenFile = open(serverPidPath + "/" + pidScreenName, "r")
            for line in serverScreenFile:
                # print "Проверка процесса", line
                if os.path.exists("/proc/%s" % line.strip()):
                    print "Остановка процесса"
                    try:
                        retcode = call("kill -9 " + line.strip(), shell=True)
                        if retcode < 0:
                            print "Не удалось остановить screen: ", str(retcode)
                        elif retcode == 0:
                            print "Screen остановлен"
                            print "Удаление screen-сессий"
                            retcode = call("screen -wipe 1> /dev/null 2> /dev/null", shell=True)
                            print "EXEC_STATUS:success"

                        else:
                            print "При попытке остановить screen возникла ошибка: ", str(retcode)

                    except OSError, e:
                        print "При попытке остановить screen возникла ошибка: ", e
                else:
                    print "Такого процесса не существует"
            serverScreenFile.close()

        print "Удаление pid"
        try:
            retcode = call("rm -rf " + serverPidPath + "/" + pidScreenName, shell=True)
            if retcode < 0:
                print "Не удалось удалить screen pid: ", str(retcode)
            elif retcode == 0:
                print "Screen pid удален"

            else:
                print "При попытке удалить screen pid возникла ошибка: ", str(retcode)

        except OSError, e:
            print e, "- Не удалось совершить очистку. Свяжитесь с техподдержкой."

    else:
        print "Screen pid-файла не существует. Странно. Проверяю дальше."
    if os.path.exists(serverPidPath + "/" + pidServerName):
        print "Удаление pid сервера"
        try:
            os.remove(serverPidPath + "/" + pidServerName)
        except OSError, e:
            print e, "- Не удалось совершить очистку. Свяжитесь с техподдержкой."
    else:
        print "pid-файла сервера не существет. Вероятно возникла ошибка при запуске."

    '''
    Проверка, не подвис ли процесс
    Если да, то послать ему kill -9
    '''
    if checkOnPort(serverIP, serverPort) == False:
        # Порт занят
        print "Порт сервера всё еще занят. Попробую завершить процесс, его удерживающий."
        print "Подожду 5 секунд, возможно процесс завершится самостоятельно."
        sleep(5)
        try:
            retcode = Popen("lsof -n -i | grep %s:%s " % (serverIP, serverPort),
                            shell=True,
                            stdin=PIPE,
                            stdout=PIPE,
                            stderr=PIPE)
            (out, err) = retcode.communicate()
            if out != '' and out != None:
                pastPidToKill = ''
                for line in out.splitlines():
                    # Должны быть строки формата (кроме первой):
                    # COMMAND     PID     USER   FD  TYPE   DEVICE          SIZE/OFF NODE NAME
                    # srcds_lin  2862 client11   7u  IPv4   2938506788      0t0      UDP  212.24.32.137:27017
                    lsof = line.split(' ')
                    pidToKill = lsof[1].strip()
                    if pidToKill != pastPidToKill:
                        # Убить процесс
                        try:
                            retcode = call("kill -9 " + pidToKill, shell=True)
                            if retcode != 0:
                                print "Не удалось остановить процесс %s: " % pidToKill, str(retcode)
                                print "EXEC_STATUS:error:Не удалось остановить процесс, удерживающий порт сервера."
                            elif retcode == 0:
                                print "EXEC_STATUS:success:Процесс, удерживающий порт сервера, остановлен успешно."

                        except OSError, e:
                            print "Не удалось остановить процесс %s: " % pidToKill, e
                            print "EXEC_STATUS:error:<span style='color: #FF6600;'><strong>Не удалось остановить процесс, удерживающий порт сервера.</strong></span>"

            else:
                print "EXEC_STATUS:success:Процесс, удерживающий порт сервера, самостоятельно завершил работу."

        except OSError, e:
            print "EXEC_STATUS:error:Порт сервера еще занят. Не удалось остановить сервер. При попытке получения списка процессов на порту возникла ошибка >>> ", e


#
# SERVER UPDATE
elif action == 'update':
    # 1) Сначала выполняем условия ошибок, потом действий
    # 2) Проверить наличие pid SCREEN, пытаемся его прочесть
    #    - Если непустой, проверить, запущен ли такой процесс
    #      - Если запущен:
    #        - Выдать сообщение, что идёт процесс обновления
    #      - Если не запущен или пустой pid-файл, удалить pid SCREEN и продолжить
    # 3) Получить pid запущенного процесса и записать его в pid SCREEN
    # 4) Выдать соответсвущее сообщение
    print "Попытка обновления сервера"
    errors = 0
    if os.path.exists(serverPidPath + "/" + pidScreenUpdateName):
            print "Сервер не запущен, но есть pid Screen"
            if (os.path.getsize(serverPidPath + "/" + pidScreenUpdateName) > 0):
                serverScreenFile = open(serverPidPath + "/" + pidScreenUpdateName, "r")
                for line in serverScreenFile:
                    # print "Проверка процесса:", line
                    if os.path.exists("/proc/%s" % line.strip()):
                        print "Сервер в процессе обновления. "
                        print "Вы можете прочесть статус обновления в лог файлах "
                        print "из панели управления или в директории logs\\. "
                        print "Если процесс обновления происходит излишне долго, "
                        print "или в лог файле есть ошибки, свяжитесь с техподдержкой."

                        errors += 1
                        break
                    else:
                        print "Очистка после неудачного запуска"
                        try:
                            os.remove(serverPidPath + "/" + pidScreenUpdateName)
                            errors = 0
                            break
                        except OSError, e:
                            print e, "- Не удалось совершить очистку. Свяжитесь с техподдержкой."
                            print "EXEC_STATUS:error:" + e
                            errors += 1
                            break
                serverScreenFile.close()
            else:
                print "Очистка после неудачного запуска"
                try:
                    os.remove(serverPidPath + "/" + pidScreenUpdateName)
                    errors = 0
                except OSError, e:
                    print e, "- Не удалось совершить очистку. Свяжитесь с техподдержкой."
                    print "EXEC_STATUS:error:" + e
                    errors += 1

    if errors > 0:
        print "<span style='color: #FF6600;'><strong>В процессе попытки запуска обновления возникло %s ошибок <br/>требующих устранения. Запуск обновления невозможен.</strong></span>" % errors
    elif errors == 0:
        print "Проверка была успешной. Выполняю запуск обновления."
        print "<span style='color: #FF6600;'><strong>ОБРАЩАЕМ ВАШЕ ВНИМАНИЕ:</strong>"
        print "Иногда агент обновления сам себя обновляет, о чём и пишет в лог."
        print "В этом случае требуется повторный запуск обновления сервера!</span>"
        command = update_strings.dedicatedUpdate(serverID,
                                                 userName,
                                                 templateName,
                                                 token)
        # print "Строка запуска:\n", command #debug
        os.chdir(serverRootPath)  # Переход в директорию сервера

        # Необходимо создать отдельный скрипт обновления, который и подсунем screen'у
        try:
            updateScriptName = templateName + "-" + serverID + "-update.sh"
            updateScriptPath = serverRootPath + updateScriptName
            updateScript = open(updateScriptPath, "w")
            updateScript.write(command)
            updateScript.close()

            os.chmod(updateScriptPath, 0770)
        except OSError, e:
            print "Команда завершилась неудачей:", e
            print "EXEC_STATUS:error:" + e

        try:
            retcode = call("/usr/bin/screen -U -m -d -S " + pidUpdateName + " " + updateScriptPath, shell=True)
            if retcode < 0:
                print "Команда была прервана с кодом: ", str(retcode)
                print "EXEC_STATUS:error:" + str(retcode)
            elif retcode == 0:
                print "Процесс обновления сервера инициирован. Это может занять <br/>весьма продолжительное время, наберитесь терпения."

                # Запись PID Screen
                try:
                    retcode = call("ps -ef | grep SCREEN | grep \"" + pidUpdateName +
                                   "\" | grep -v grep | awk '{print $2}' > " + serverPidPath + "/" + pidScreenUpdateName, shell=True)
                    if retcode < 0:
                        print "Не удалось создать screen pid: ", str(retcode)
                        print "EXEC_STATUS:error:" + str(retcode)
                    elif retcode == 0:
                        print "ID процесса записан в screen update pid"
                        print "EXEC_STATUS:success"

                    else:
                        print "При создании pid screen update возникла ошибка: ", str(retcode)
                        print "EXEC_STATUS:error:" + str(retcode)

                except OSError, e:
                    print "При попытке создания pid screen update возникла ошибка:", e
                    print "EXEC_STATUS:error:" + e

            else:
                print "Команда вернула код: ", str(retcode)
        except OSError, e:
            print "Команда завершилась неудачей:", e
            print "EXEC_STATUS:error:" + e
