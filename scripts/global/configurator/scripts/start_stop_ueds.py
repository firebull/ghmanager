#!/usr/bin/env python2
# coding: UTF-8
# Скипт предназначен для запуска/остановки/перезапуска Unreal Engine серверов

'''
***********************************************
Unreal engines based servers Start/Stop script with clients rights.
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
from optparse import OptionParser
from datetime import datetime, date, time
from commonLib import screenLogRotate


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
serverMap = config.get('server', 'map')
vac = config.get('server', 'vac')
setAdminPass = config.get('server', 'setAdminPass')
debug = config.get('server', 'debug')

userName = config.get('server', 'user')
userEmail = config.get('server', 'email')
templateName = config.get('server', 'template')
templateRootPath = config.get('server', 'templateRootPath')

homeDir = "/home/%s" % userName
serversPath = homeDir + "/servers"
serverRootPath = serversPath + "/" + templateName + "_" + str(serverID) + "/"
serverRunPath = serversPath + "/" + templateName + "_" + str(serverID) + "/" + templateRootPath
serverPidPath = "/home/pid/" + userName

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
screenLogRotate(serverRunPath)
#
# SERVER START

if action == "start":
    # 1) Сначала выполняем условия ошибок, потом действий
    # 2) Проверить, занят ли порт сервера. Если да - выдать сообщение и прекратить дальнейшие операции
    # 3) Если порт свободен, проверить наличие pid SCREEN
    # 4) Если существует pid SCREEN, пытаемся его прочесть
    #
    #    - Если непустой, проверить, запущен ли такой процесс
    #      - Если запущен:
    #        - Если существует update-pb SCREEN, выдать сообщение, что идёт процесс обновления с просьбой подождать
    #        - Если не существует update-pb, выдать сообщение, что идёт процесс запуска сервера и просьба подождать, а также
    #          при длительном ожидании, принудительная остановка
    #      - Если не запущен или пустой pid-файл, удалить pid SCREEN и продолжить запуск
    # 6) Выполнить обычный запуск
    # 7) Получить pid запущенного процесса и записать его в pid SCREEN
    # 8) Выдать соответсвущее сообщение

    print "Попытка запуска сервера"

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

        print "Pid Screen существует, проверка состояния."
        if (os.path.exists(serverPidPath + "/" + pidScreenName)):
            serverScreenFile = open(serverPidPath + "/" + pidScreenName, "r")
            for line in serverScreenFile:
                # print "Проверка процесса:", line
                if os.path.exists("/proc/%s" % line.strip()):
                    # Проверка на наличие pid SCREEN
                    if os.path.exists(serverPidPath + "/" + pidScreenUpdateName) and (os.path.getsize(serverPidPath + "/" + pidScreenUpdateName) > 0):
                        print "PunkBaster в процессе обновления. "
                        print "Вы можете прочесть статус обновления в лог файлах "
                        print "из панели управления или в директории logs\\=server=\\update. "
                        print "Если процесс обновления происходит излишне долго, "
                        print "или в лог файле есть ошибки, свяжитесь с техподдержкой."
                    else:
                        print "Сервер в процессе запуска. Пожалуйста, подождите. "
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

        if errors > 0:
            print "В процессе попытки запуска возникло %s ошибок требующих устранения. <br/>Запуск сервера невозможен." % errors
        elif errors == 0:
            print "Проверка была успешной. Выполняю запуск сервера."
            if action == 'start':
                command = start_strings.uedsStart(serverID,
                                                  userName,
                                                  serverIP,
                                                  serverPort,
                                                  serverSlots,
                                                  templateName,
                                                  serverMap,
                                                  vac,
                                                  setAdminPass,
                                                  debug)
            print "Строка запуска:\n", command  # debug
            os.chdir(serverRunPath)  # Переход в директорию сервера
            try:
                retcode = call(command, shell=True)
                if retcode < 0:
                    print "Команда была прервана с кодом: ", retcode
                    print "EXEC_STATUS:error:" + retcode
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
                            print "Не удалось создать screen pid: ", retcode
                            print "EXEC_STATUS:error:" + retcode
                        elif retcode == 0:
                            print "ID процесса записан в screen pid"
                            print "EXEC_STATUS:success"
                        else:
                            print "При создании pid screen возникла ошибка: ", retcode
                            print "EXEC_STATUS:error:" + retcode

                    except OSError, e:
                        print "При попытке создания pid screen возникла ошибка:", e
                        print "EXEC_STATUS:error:" + str(e)

                else:
                    print "Команда вернула код: ", retcode
            except OSError, e:
                print "Команда завершилась неудачей:", e
                print "EXEC_STATUS:error:" + str(e)
#
# SERVER STOP

elif action == 'stop':
# 1) Все системные операции совершать через sudo
# 2) - Если существует screen pid
#      - Если он не пустой, считать его содержимое
#        - Циклически kill 9 всё его содержимое
#        - Удалить все мёртвые сессии
#      - Если пустой, удалить и вывести соотв сообщение
#      - Удалить pid
#    - Если не существует, вывести сообщение

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
                            print "Не удалось остановить screen: ", retcode
                        elif retcode == 0:
                            print "Screen остановлен"
                            print "Удаление screen-сессий"
                            retcode = call("screen -wipe 1> /dev/null 2> /dev/null", shell=True)
                            print "EXEC_STATUS:success"

                        else:
                            print "При попытке остановить screen возникла ошибка: ", retcode

                    except OSError, e:
                        print "При попытке остановить screen возникла ошибка: ", e
                else:
                    print "Такого процесса не существует"
            serverScreenFile.close()

        print "Удаление pid"
        try:
            retcode = call("rm -rf " + serverPidPath + "/" + pidScreenName, shell=True)
            if retcode < 0:
                print "Не удалось удалить screen pid: ", retcode
            elif retcode == 0:
                print "Screen pid удален"

            else:
                print "При попытке удалить screen pid возникла ошибка: ", retcode

        except OSError, e:
            print e, "- Не удалось совершить очистку. Свяжитесь с техподдержкой."

    else:
        print "Screen pid-файла не существует. Странно. Проверяю дальше."


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
                            print "EXEC_STATUS:error:" + str(e)
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
                    print "EXEC_STATUS:error:" + str(e)
                    errors += 1

    if errors > 0:
        print "В процессе попытки запуска обновления возникло %s ошибок <br/>требующих устранения. Запуск обновления невозможен." % errors
    elif errors == 0:
        print "Проверка была успешной. Выполняю запуск обновления."
        print "ОБРАЩАЕМ ВАШЕ ВНИМАНИЕ:"
        print "Иногда агент обновления сам себя обновляет, о чём и пишет в лог."
        print "В этом случае требуется повторный запуск обновления сервера!"
        command = update_strings.dedicatedUpdate(serverID,
                                                 userName,
                                                 templateName)
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
            print "EXEC_STATUS:error:" + str(e)
        try:
            retcode = call("/usr/bin/screen -U -m -d -S " + pidUpdateName + " " + updateScriptPath, shell=True)
            if retcode < 0:
                print "Команда была прервана с кодом: ", retcode
                print "EXEC_STATUS:error:" + retcode
            elif retcode == 0:
                print "Процесс обновления сервера инициирован. Это может занять <br/>весьма продолжительное время, наберитесь терпения."

                # Запись PID Screen
                try:
                    retcode = call("ps -ef | grep SCREEN | grep \"" + pidUpdateName +
                                   "\" | grep -v grep | awk '{print $2}' > " + serverPidPath + "/" + pidScreenUpdateName, shell=True)
                    if retcode < 0:
                        print "Не удалось создать screen pid: ", retcode
                        print "EXEC_STATUS:error:" + retcode
                    elif retcode == 0:
                        print "ID процесса записан в screen update pid"
                        print "EXEC_STATUS:success"

                    else:
                        print "При создании pid screen update возникла ошибка: ", retcode
                        print "EXEC_STATUS:error:" + retcode

                except OSError, e:
                    print "При попытке создания pid screen update возникла ошибка:", e
                    print "EXEC_STATUS:error:" + str(e)

            else:
                print "Команда вернула код: ", retcode
        except OSError, e:
            print "Команда завершилась неудачей:", e
            print "EXEC_STATUS:error:" + str(e)
