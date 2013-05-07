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
"cfg/apofig_1x1.cfg" "Apofig (CSPL) 1x1" // Запустить конфиг Apofig 1x1
"cfg/apofig_2x2.cfg" "Apofig (CSPL) 2x2" // Запустить конфиг Apofig 2x2
"cfg/apofig_5x5.cfg" "Apofig (CSPL) 5x5" // Запустить конфиг Apofig 5x5
"cfg/apofig_aim.cfg" "Apofig (CSPL) AIM" // Запустить конфиг Apofig AIM
"cfg/apofig_awp.cfg" "Apofig (CSPL) AWP" // Запустить конфиг Apofig AWP
'''

addInfoToConfig(serverCfg, text, 'Configs')

serverCfg = serverPath + "/" + addonPath + "/cfg/mani_admin_plugin/rconlist.txt"

text = '''
"Apofig (CSPL) 1x1" exec apofig_1x1.cfg // Запустить конфиг Apofig 1x1
"Apofig (CSPL) 2x2" exec apofig_2x2.cfg // Запустить конфиг Apofig 2x2
"Apofig (CSPL) 5x5" exec apofig_5x5.cfg // Запустить конфиг Apofig 5x5
"Apofig (CSPL) AIM" exec apofig_aim.cfg // Запустить конфиг Apofig AIM
"Apofig (CSPL) AWP" exec apofig_awp.cfg // Запустить конфиг Apofig AWP
'''

addInfoToConfig(serverCfg, text)
