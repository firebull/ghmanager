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
"cfg/cspl1on1aim.cfg" "CSPL 1x1 AIM"
"cfg/cspl1on1awp.cfg" "CSPL 1x1 AWP"
"cfg/cspl1on1hg.cfg" "CSPL 1x1 HG"
"cfg/cspl1on1single.cfg" "CSPL 1x1 Single"
"cfg/cspl2on2aim.cfg" "CSPL 2x2 AIM"
"cfg/cspl2on2awp.cfg" "CSPL 2x2 AWP"
"cfg/cspl2on2hg.cfg" "CSPL 2x2 HG"
"cfg/cspl2on2.cfg" "CSPL 2x2"
"cfg/cspl3on3.cfg" "CSPL 3x3"
"cfg/cspl5on5.cfg" "CSPL 5x5"
'''

addInfoToConfig(serverCfg, text, 'Configs')

serverCfg = serverPath + "/" + addonPath + "/cfg/mani_admin_plugin/rconlist.txt"

text = '''
"CSPL 1x1 AIM" exec cspl1on1aim.cfg // Запустить конфиг CSPL 1x1 AIM
"CSPL 1x1 AWP" exec cspl1on1awp.cfg // Запустить конфиг CSPL 1x1 AWP
"CSPL 1x1 HG" exec cspl1on1hg.cfg // Запустить конфиг CSPL 1x1HG
"CSPL 1x1 Single" exec cspl1on1single.cfg // Запустить конфиг CSPL 1x1 Single
"CSPL 2x2 AIM" exec cspl2on2aim.cfg // Запустить конфиг CSPL 2x2 AIM
"CSPL 2x2 AWP" exec cspl2on2awp.cfg // Запустить конфиг CSPL 2x2 AWP
"CSPL 2x2 HG" exec cspl2on2hg.cfg // Запустить конфиг CSPL 2x2HG
"CSPL 2x2" exec cspl2on2.cfg // Запустить конфиг CSPL 2x2
"CSPL 3x3" exec cspl3on3.cfg // Запустить конфиг CSPL 3x3
"CSPL 5x5" exec cspl5x5.cfg // Запустить конфиг CSPL 5x5
'''

addInfoToConfig(serverCfg, text)
