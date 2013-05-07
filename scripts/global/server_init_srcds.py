#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Additional initial script for SRCDS.
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
 Скрипт инициализации SRCDS
 1) Прописать игру и путь
 2) Создать конфиг
'''

import os
import re
from shutil import *
from subprocess import *
from datetime import datetime
from optparse import OptionParser
import sys
sys.path.append("/images/scripts/global/configurator/scripts")
from commonLib import readAndSetParamFromConfig


parser = OptionParser()

parser.add_option("-m", "--server", action="store", type="int", dest="serverID")
parser.add_option("-u", "--user", action="store", type="string", dest="userID")
parser.add_option("-t", "--tmp", action="store", type="string", dest="template")
parser.add_option("-i", "--ip", action="store", type="string", dest="ip")
parser.add_option("-p", "--port", action="store", type="string", dest="port")
parser.add_option("-s", "--slots", action="store", type="string", dest="slots")
parser.add_option("-r", "--rcon", action="store", type="string", dest="rcon")
parser.add_option("-c", "--cfg-dir", action="store", type="string", dest="cfgDir")

(options, args) = parser.parse_args(args=None, values=None)

serverID = str(options.serverID)
userID = str(options.userID)
template = options.template
ip = options.ip
rconPassword = str(options.rcon)
cfgDir = str(options.cfgDir)

# Генерация рабочих переменных
userName = "client" + userID
serverPath = "/home/" + userName + "/servers/" + template + "_" + serverID  # Путь к серверу

if template in ('l4d-t100', 'l4d2-t100'):
    if template == 'l4d-t100':
        confTemplate = 'l4d'
    else:
        confTemplate = 'l4d2'

    iniPath = "/images/scripts/servers_configs/srcds/%s/" % confTemplate

else:
    iniPath = "/images/scripts/servers_configs/srcds/%s/" % template

serverCfgPath = serverPath + '/' + cfgDir + '/'
iniCfgPath = iniPath + '/' + cfgDir + '/'

if os.path.exists(iniCfgPath + 'server.cfg'):
    try:
        #
        iniCfg = iniCfgPath + 'server.cfg'
        newCfg = serverCfgPath + 'server.cfg'

        print "Копирую основной конфиг"
        ps = open(iniCfg, 'r')
        pn = open(newCfg, 'w')

        for line in ps:
            if re.search('rcon_password', line):
                line = 'rcon_password "%s"' % rconPassword

            pn.write(line)

        pn.close()
        ps.close()
        os.chmod(newCfg, 0660)
        print "Успешно!"
        #

    except OSError, e:
        print "Команда завершилась неудачей: ", e

if template == 'tf':

    iniNew = os.path.join(serverPath, 'orangebox/tf/cfg/replay_local_http.cfg')
    iniSrc = iniPath + 'orangebox/tf/cfg/replay_local_http.cfg'

    os.chdir("/home/" + userName)  # Переход в директорию сервера

    try:
        #

        print "Копирую конфиг Replay"
        ps = open(iniSrc, 'r')
        pn = open(iniNew, 'w')

        for line in ps:
            line = line.replace("%s", userName)
            line = line.replace("%i", ip)
            pn.write(line)

        pn.close()
        ps.close()
        os.chmod(iniNew, 0660)
        print "Успешно!"
        #

    except OSError, e:
        print "Команда завершилась неудачей: ", e

if template == 'csgo':

    try:
        #
        print "Создаю конфиг обновления"
        updateConfig = os.path.join(serverPath, 'csgo_update.txt')
        uc = open(updateConfig, 'w')

        uc.write('''
// csgo_update.txt
// Ничего здесь менять НЕ НАДО! Только, если будут
// проблемы после какого-либо обновления!
login anonymous
force_install_dir ./csgo_ds
app_update 740 validate
exit
                            ''')

        uc.close()
        os.chmod(updateConfig, 0660)

    except OSError, e:
        print "Команда завершилась неудачей: ", e

elif template == 'css':

    try:
        #
        print "Создаю конфиг обновления"
        updateConfig = os.path.join(serverPath, 'css_update.txt')
        uc = open(updateConfig, 'w')

        uc.write('''
// css_update.txt
// Ничего здесь менять НЕ НАДО! Только, если будут
// проблемы после какого-либо обновления!
login anonymous
force_install_dir ./css_ds
app_update 232330 validate
exit
                            ''')

        uc.close()
        os.chmod(updateConfig, 0660)

    except OSError, e:
        print "Команда завершилась неудачей: ", e

elif template == 'dods':

    try:
        #
        print "Создаю конфиг обновления"
        updateConfig = os.path.join(serverPath, 'dods_update.txt')
        uc = open(updateConfig, 'w')

        uc.write('''
// dods_update.txt
// Ничего здесь менять НЕ НАДО! Только, если будут
// проблемы после какого-либо обновления!
login anonymous
force_install_dir ./css_ds
app_update 232290 validate
exit
                            ''')

        uc.close()
        os.chmod(updateConfig, 0660)

    except OSError, e:
        print "Команда завершилась неудачей: ", e

if template in ('l4d-t100', 'l4d2-t100'):

    try:
        readAndSetParamFromConfig('sv_maxrate', '0', 'Значение для tickrate 100', 'server.cfg', serverCfgPath, 'write')
        readAndSetParamFromConfig('sv_maxcmdrate', '100', 'Значение для tickrate 100', 'server.cfg', serverCfgPath, 'write')
        readAndSetParamFromConfig('sv_maxupdaterate', '100', 'Значение для tickrate 100', 'server.cfg', serverCfgPath, 'write')
        readAndSetParamFromConfig('hostname', 'TeamServer.RU ! Tick 100 !', 'Имя сервера', 'server.cfg', serverCfgPath, 'write')
    except Exception, e:
        print "Команда завершилась неудачей: ", e
