#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
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
print                                              # blank line, end of headers

from optparse import OptionParser
from common import addInfoToConfig
import os
import re
import sys

parser = OptionParser()
parser.add_option("-s", "--server-path", action="store", type="string", dest="serverPath")
parser.add_option("-a", "--addon-path", action="store", type="string", dest="addonPath")
parser.add_option("-g", "--guid", action="store", type="string", dest="guid")
parser.add_option("-p")
parser.add_option("-m", "--more", action="store", type="string", dest="more")

(options, args) = parser.parse_args(args=None, values=None)
serverPath = options.serverPath
addonPath = options.addonPath
guid = options.guid
more = options.more

# Получить нужные параметры

moreParams = more.split(':')

ip = moreParams[0]
port = moreParams[1]
rcon = moreParams[2]
mod = moreParams[3]

tcpAdminPort = str(int(port) + 2000)

serverPathSplit = serverPath.split('/')

if re.match('cod2', serverPathSplit[4]):
    commonCodDir = '.callofduty2'
    configTmp = serverPath + "/" + addonPath + "/mods/manuadmin/config/config_cod2.cfg"
elif re.match('cod4', serverPathSplit[4]):
    commonCodDir = '.callofduty4'
    configTmp = serverPath + "/" + addonPath + "/mods/manuadmin/config/config.cfg"
else:
    commonCodDir = '.callofduty'

if mod == 'None':
    logPath = "/home/%s/%s/main/%s" % (serverPathSplit[2], commonCodDir, serverPathSplit[4])
else:
    if commonCodDir == '.callofduty2':
        logPath = "/home/%s/%s/mods/%s/%s" % (serverPathSplit[2], commonCodDir, mod, serverPathSplit[4])
    else:
        logPath = "/home/%s/%s/mods/%s/%s" % (serverPathSplit[2], commonCodDir, mod.lower(), serverPathSplit[4])

modsPath = serverPath + "/mods"

config = serverPath + "/" + addonPath + "/mods/manuadmin/config/config.cfg"
script = serverPath + "/" + addonPath + "/mods/manuadmin/startscript"
adminConfig = serverPath + "/" + addonPath + "/mods/manuadmin/config/admins.cfg"

if mod == 'ModWarfare' or mod == 'main':
    serverConfig = serverPath + "/" + addonPath + "/mods/%s/server.cfg" % mod
elif mod == 'None':
    serverConfig = serverPath + "/" + addonPath + "/main/server.cfg"
else:
    serverConfig = serverPath + "/" + addonPath + "/mods/%s/modserver.cfg" % mod


try:
    if os.path.exists(config):
        # переименовать базовый конфиг
        os.rename(configTmp, config + ".tmp")

        cfgDist = open(config + ".tmp", 'r')
        cfg = open(config, 'w')

        for line in cfgDist:
            line = line.replace("%i", ip)
            line = line.replace("%p", port)
            line = line.replace("%t", tcpAdminPort)
            line = line.replace("%r", rcon)
            line = line.replace("%l", logPath)

            cfg.write(line)

        cfgDist.close()
        cfg.close()

        os.chmod(config, 0640)
        os.remove(config + ".tmp")

except OSError, e:
    print "Команда завершилась неудачей", e

# Прописать параметры скрипта запуска ManuAdmin
try:
    if os.path.exists(script):
        # переименовать базовый конфиг
        os.rename(script, script + ".tmp")

        scriptDist = open(script + ".tmp", 'r')
        scr = open(script, 'w')

        for line in scriptDist:
            line = line.replace("%i", serverPathSplit[4])
            line = line.replace("%p", modsPath)
            line = line.replace("%l", logPath)

            scr.write(line)

        scriptDist.close()
        scr.close()

        os.chmod(script, 0740)
        os.remove(script + ".tmp")

except OSError, e:
    print "Команда завершилась неудачей", e


# Добавить админа
if guid != 'none':

    text = '''
    [%s]
    group = "admin"
    protected = 1
    names = ""

    ''' % guid

    addInfoToConfig(adminConfig, text)

# Установить правильные параметры лога
text = '''
// включить запись лог-файла
set logfile "1"

// имя лог-файла
set g_log "%s/games_mp.log"

// 0=нет лога, 1=в буфер, 2=непрерывный, 3=добавлять
set g_logsync "2"

// вести лог по ущербу и попаданиям
set sv_log_damage "1"

''' % serverPathSplit[4]

addInfoToConfig(serverConfig, text)
