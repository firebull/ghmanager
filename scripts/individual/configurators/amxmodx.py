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

from optparse import OptionParser
from shutil import *
import os
import sys
import re

parser = OptionParser()
parser.add_option("-s", "--server-path", action="store", type="string", dest="serverPath")
parser.add_option("-a", "--addon-path", action="store", type="string", dest="addonPath")
parser.add_option("-p", "--steam-id", action="store", type="string", dest="steamId")
parser.add_option("-g")
parser.add_option("-m")

(options, args) = parser.parse_args(args=None, values=None)
serverPath = options.serverPath
addonPath = options.addonPath
steamId = options.steamId

gamPath = serverPath + "/" + addonPath + "/liblist.gam"
adminsCfg = serverPath + "/" + addonPath + "/addons/amxmodx/configs/users.ini"

if os.path.exists(gamPath):
    if os.path.exists(gamPath + '.bak'):  # Необходимо удалить старый .bak, если есть
        os.remove(gamPath + '.bak')

    print "INFO: Вношу изменения в %s" % gamPath
    pattern_1 = "gamedll_linux"
    pattern_2 = ""

    try:
        tmpCfg = serverPath + "/" + addonPath + "/liblist.gam" + ".bak"
        copyfile(gamPath, tmpCfg)
        newCfg = open(gamPath, "w")
        for line in open(tmpCfg):
            if re.match(pattern_1, line):  # Искать надо только по началу строки
                line = 'gamedll_linux "addons/metamod/dlls/metamod_i386.so"' + "\n"
            newCfg.write(line)

        newCfg.close()
        os.chmod(gamPath, 0640)

        print "OK: Успешно"

    except OSError, e:
        print "ERROR: Команда завершилась неудачей: %s" % e
else:
    print "ERROR: %s не найден!" % gamPath

if steamId.lower() != 'none':
    print "INFO: Вношу админов"
    try:
        ini = open(adminsCfg, "a")
        text = '\n"%s" "" "abcdefghijklmnopqrstu" "ce" \n' % steamId
        ini.write(text)
        print "OK: Успешно"
    except OSError, e:
        print "ERROR: Команда завершилась неудачей: %s" % e
