#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Additional initial script for COD2/4.
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
 Скрипт инициализации PunkBuster
 1) Прописать игру и путь
 2) Обновить punkbuster
'''

import os
import re
from shutil import *
from subprocess import *
from datetime import datetime
from optparse import OptionParser


parser = OptionParser()

parser.add_option("-m", "--server", action="store", type="int", dest="serverID")
parser.add_option("-u", "--user", action="store", type="string", dest="userID")
parser.add_option("-t", "--tmp", action="store", type="string", dest="template")
parser.add_option("-i", "--ip", action="store", type="string", dest="ip")
parser.add_option("-p", "--port", action="store", type="string", dest="port")
parser.add_option("-s", "--slots", action="store", type="string", dest="slots")
parser.add_option("-r")
parser.add_option("-c")

(options, args) = parser.parse_args(args=None, values=None)


serverID = str(options.serverID)
userID = str(options.userID)
template = options.template
serverIp = options.ip
serverPort = options.port
slots = options.slots

# Генерация рабочих переменных
userName = "client" + userID
serverPath = "/home/" + userName + "/servers/" + template + "_" + serverID  # Путь к серверу

# Установить правильное имя шаблона
if re.match('cod4', template):
    template = 'cod4'

# Активировать PunkBuster
os.chdir(serverPath)  # Переход в директорию сервера
try:
    retcode = call(str(serverPath) + "/pbsetup.run" +
                   " -ag " + str(template) +
                   " -ap \"" + str(serverPath) + "\" --i-accept-the-pb-eula", shell=True)
    if retcode < 0:
        print "Команда была прервана с кодом: ", retcode
    elif retcode == 0:
        print "PunkBuster активирован успешно. Теперь попытаюсь его обновить."
        #
        # Обновить PunkBuster
        try:
            retcode = call(serverPath + "/pbsetup.run --i-accept-the-pb-eula -u", shell=True)
            if retcode < 0:
                print "Команда была прервана с кодом: ", retcode
            elif retcode == 0:
                print "PunkBuster обновлён успешно."
            elif retcode == 1:
                print "Команда вернула ошибку - License agreement failure: ", retcode
            elif retcode == 2:
                print "Команда вернула ошибку - Command-line parse failure: ", retcode
            elif retcode == 3:
                print "Команда вернула ошибку - Update download failure: ", retcode
            elif retcode == 4:
                print "Команда вернула ошибку - File access/permission failure: ", retcode
            else:
                print "Команда вернула код: ", retcode
        except OSError, e:
            print "Команда завершилась неудачей:", e

        #

except OSError, e:
    print "Команда завершилась неудачей:", e
