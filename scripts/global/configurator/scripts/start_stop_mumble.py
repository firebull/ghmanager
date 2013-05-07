#!/usr/bin/env python2
# coding: UTF-8
# Скипт предназначен для запуска/остановки/перезапуска Mumble-серверов

'''
***********************************************
Mumble voice server Start/Stop script with clients rights.
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
import distutils.dir_util
import cgi
import cgitb
import ConfigParser
import socket
import start_strings   # Командные строки запуска
import update_strings  # Командные строки обновления
from time import sleep
from subprocess import *
from shutil import *
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

userName = config.get('server', 'user')
userEmail = config.get('server', 'email')
templateName = config.get('server', 'template')
templateRootPath = config.get('server', 'templateRootPath')

homeDir = "/home/%s" % userName
serversPath = homeDir + "/servers"
serverRootPath = serversPath + "/" + templateName + "_" + str(serverID) + "/"
serverRunPath = serversPath + "/" + templateName + "_" + str(serverID) + "/" + templateRootPath
serverPidPath = "/home/pid/" + userName
pidServerName = templateName + "_" + serverID + ".pid"
pidName = templateName + "_" + serverID


def checkOnPort(ip, port):
    print "Проверка занят ли порт сервера" + ip + ":" + str(port)
    s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    try:
        s.bind((ip, port))
        s.close()
        print "Порт свободен"
        return True
    except socket.error, e:
        print "Порт занят", e
        return False


print datetime.now().strftime("%A, %d. %B %Y %H:%M:%S%p")
#
# SERVER START

if action == "start":
    # 1) Сначала выполняем условия ошибок, потом действий
    # 2) Проверить, занят ли порт сервера. Если да - выдать сообщение и прекратить дальнейшие операции
    # 3) Если порт свободен, проверить наличие pid сервера
    # 4) Если существует pid сервера, пытаемся его прочесть
    #    - Если пустой - выдать сообщение о вероятной проблеме запуска и рекомендации принудительной остановки
    #    - Если непустой, проверить, запущен ли такой процесс.
    #      - Если запущен, выдать сообщение, что сервер запущен с ошибкой, либо занял другой порт. Рекомендация перезапуска.
    #      - Если процесса нет - удалить pid сервера и продолжить запуск
    # 5) Выдать соответсвущее сообщение

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
                            errors = 0
                            break
                        except OSError, e:
                            print "EXEC_STATUS:error:" + e
                            print "Не удалось совершить очистку. Свяжитесь с техподдержкой."
                            errors += 1
                            break
                serverPidFile.close()

        if errors > 0:
            print "В процессе попытки запуска возникло %s ошибок требующих устранения. <br/>Запуск сервера невозможен." % errors
        elif errors == 0:
            print "Проверка была успешной. Выполняю запуск сервера."
            command = start_strings.voiceMumbleStart(serverID,
                                                     userName,
                                                     templateName)
            # print "Строка запуска:\n", command #debug
            os.chdir(serverRunPath)  # Переход в директорию сервера
            try:
                retcode = call(command, shell=True)
                if retcode < 0:
                    print "Запуск прервался с ошибкой: ", -retcode
                    print "EXEC_STATUS:error:" + retcode
                elif retcode == 0:
                    print "Процесс запуска сервера инициирован. Это может занять некоторое время."
                    print "EXEC_STATUS:success"
                else:
                    print "Запуск прервался с ошибкой: ", retcode
                    print "EXEC_STATUS:error:" + retcode
            except OSError, e:
                print "Команда завершилась неудачей:", e
                print "EXEC_STATUS:error:" + e
#
# SERVER STOP

elif action == 'stop':
# 1) Все системные операции совершать через sudo
# 2) - Если существует pid
#      - Если он не пустой, считать его содержимое
#        - Циклически kill 9 всё его содержимое
#      - Если пустой, удалить и вывести соотв сообщение
#    - Если не существует, вывести сообщение
# 3) - Если существует pid сервера, удалить его

    print "Попытка остановки сервера"
    print "Проверка pid:" + serverPidPath + "/" + pidServerName
    if os.path.exists(serverPidPath + "/" + pidServerName):
        if (os.path.getsize(serverPidPath + "/" + pidServerName) > 0):
            print "Проверка процесса:"
            serverPidFile = open(serverPidPath + "/" + pidServerName, "r")
            for line in serverPidFile:
                print "Проверка процесса", line
                if os.path.exists("/proc/%s" % line.strip()):
                    print "Остановка процесса"
                    try:
                        retcode = call("kill -9 " + line.strip(), shell=True)
                        if retcode < 0:
                            print "Не удалось остановить сервер: ", retcode
                        elif retcode == 0:
                            print "Сервер остановлен"
                            print "EXEC_STATUS:success"

                        else:
                            print "При попытке остановить сервер возникла ошибка: ", retcode

                    except OSError, e:
                        print "При попытке остановить сервер возникла ошибка: ", e
                else:
                    print "Такого процесса не существует"
            serverPidFile.close()

        print "Удаление pid"
        try:
            retcode = call("rm -rf " + serverPidPath + "/" + pidServerName, shell=True)
            if retcode < 0:
                print "Не удалось удалить pid: ", retcode
            elif retcode == 0:
                print "pid удален"

            else:
                print "При попытке удалить pid возникла ошибка: ", retcode

        except OSError, e:
            print e, "- Не удалось совершить очистку. Свяжитесь с техподдержкой."

    else:
        print "pid-файла не существует. Странно. Проверяю дальше."
