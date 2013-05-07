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

parser = OptionParser()
parser.add_option("-s", "--server-path", action="store", type="string", dest="serverPath")
parser.add_option("-a", "--addon-path", action="store", type="string", dest="addonPath")
parser.add_option("-p")
parser.add_option("-g")
parser.add_option("-m")

(options, args) = parser.parse_args(args=None, values=None)
serverPath = options.serverPath
addonPath = options.addonPath

serverCfg = serverPath + "/" + addonPath + "/addons/sourcemod/configs/adminmenu_cfgs.txt"

text = '''
"cfg/FGCL-1vs1-awp.cfg" "FGCL 1x1 AWP" // Запустить конфиг FGCL 1x1 AWP
"cfg/FGCL-1vs1.cfg" "FGCL 1x1" // Запустить конфиг FGCL 1x1
"cfg/FGCL-2vs2.cfg" "FGCL 2x2" // Запустить конфиг FGCL 2x2
"cfg/FGCL-3vs3.cfg" "FGCL 3x3" // Запустить конфиг FGCL 3x3
"cfg/FGCL-5vs5.cfg" "FGCL 5x5" // Запустить конфиг Apofig AWP
'''

addInfoToConfig(serverCfg, text, 'Configs')

serverCfg = serverPath + "/" + addonPath + "/cfg/mani_admin_plugin/rconlist.txt"

text = '''
"FGCL 1x1 AWP" exec FGCL-1vs1-awp.cfg // Запустить конфиг FGCL 1x1 AWP
"FGCL 1x1" exec FGCL-1vs1.cfg // Запустить конфиг FGCL 1x1
"FGCL 2x2" exec FGCL-2vs2.cfg // Запустить конфиг FGCL 2x2
"FGCL 3x3" exec FGCL-3vs3.cfg // Запустить конфиг FGCL 3x3
"FGCL 5x5" exec FGCL-5vs5.cfgg // Запустить конфиг FGCL 5x5
'''

addInfoToConfig(serverCfg, text)

serverCfg = serverPath + "/" + addonPath + "/cfg/server.cfg"

text = '''// Показывать сообщения FGCL об игроках (забанен или нет)
fgcl_ban_announce 1
'''

addInfoToConfig(serverCfg, text)
