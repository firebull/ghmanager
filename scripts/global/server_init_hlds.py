#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Additional initial script for HLDS.
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
 Скрипт инициализации HLDS
 1) Прописать игру и путь
 2) Создать конфиг
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
iniPath = "/images/scripts/servers_configs/hlds/%s/" % template
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

if template == 'cs16':
    if os.path.exists(iniCfgPath + '/steamcmd/cs16_update.txt'):
        try:
            #
            iniCfg = iniCfgPath + '/steamcmd/cs16_update.txt'
            newCfg = serverCfgPath + '/steamcmd/cs16_update.txt'

            print "Копирую конфиг обновления"
            ps = open(iniCfg, 'r')
            pn = open(newCfg, 'w')

            for line in ps:
                pn.write(line)

            pn.close()
            ps.close()
            os.chmod(newCfg, 0660)
            print "Успешно!"
            #

        except OSError, e:
            print "Команда завершилась неудачей: ", e
