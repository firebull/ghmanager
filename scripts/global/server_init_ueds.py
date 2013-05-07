#!/usr/bin/env python2
# coding: UTF-8


'''
***********************************************
Additional initial script for Unreal Engine servers.
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
port = options.port

# Генерация рабочих переменных
userName = "client" + userID
serverPath = "/home/" + userName + "/servers/" + template + "_" + serverID  # Путь к серверу
iniNew = "/home/" + userName + '/.killingfloor/System/KillingFloor-' + serverID + '.ini'
iniSrc = '/images/scripts/servers_configs/ueds/killingfloor/KillingFloor.ini'

listenPort = str((int(port) - 7707) + 8075)
gameSpyPort = str((int(port) - 7707) + 7917)

os.chdir("/home/" + userName)  # Переход в директорию сервера

try:
    #
    if not os.path.exists('.killingfloor/System'):
        os.makedirs('.killingfloor/System', 0700)

    print "Копирую конфиг"
    ps = open(iniSrc, 'r')
    pn = open(iniNew, 'w')

    for line in ps:
        if re.match('Port', line):
            pn.write('Port=%s\n' % port)
        elif re.match('ListenPort', line):
            pn.write('ListenPort=%s\n' % listenPort)
        elif re.match('OldQueryPortNumber', line):
            pn.write('OldQueryPortNumber=%s\n' % gameSpyPort)
        else:
            pn.write(line)

    pn.close()
    ps.close()
    os.chmod(iniNew, 0660)
    print "Успешно!"
    #

except OSError, e:
    print "Команда завершилась неудачей:", e
