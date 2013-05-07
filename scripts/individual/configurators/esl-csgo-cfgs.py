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
"cfg/csgo_comp.cfg" "ESL 5on5"
"cfg/esl1on1aml.cfg" "ESL 1on1/2on2 AIM/AWP"
"cfg/esl1on1rl.cfg" "ESL 1on1 Rush"
"cfg/esl2on2aim.cfg" "ESL 1on1/2on2 AIM Ladder"
"cfg/esl2on2.cfg" "ESL 1on1/2on2 Ladder"
"cfg/esl2on2rl.cfg" "ESL 2on2 Rush"
"cfg/esl5on5.cfg" "ESL 5on5 Ladder"
"cfg/esl5on5rl.cfg" "ESL 5on5 Rush"
'''

addInfoToConfig(serverCfg, text, 'Configs')

serverCfg = serverPath + "/" + addonPath + "/cfg/mani_admin_plugin/rconlist.txt"

text = '''
"ESL 5on5" exec csgo_comp.cfg // ESL 5on5
"ESL 1on1/2on2 AIM/AWP" exec esl1on1aml.cfg // ESL 1on1/2on2 AIM/AWP
"ESL 1on1 Rush" exec esl1on1rl.cfg // Запустить конфиг ESL 1on1 Rush
"ESL 1on1/2on2 AIM Ladder" exec esl2on2aim.cfg // Запустить конфиг ESL 1on1/2on2 AIM Ladder
"ESL 1on1/2on2 Ladder" exec esl2on2.cfg // Запустить конфиг ESL 1on1/2on2 Ladder
"ESL 2on2 Rush" exec esl2on2rl.cfg // Запустить конфиг ESL 2on2 Rush
"ESL 5on5 Ladder" exec esl5on5.cfg // Запустить конфиг ESL 5on5 Ladder
"ESL 5on5 Rush" exec esl5on5rl.cfg // Запустить конфиг ESL 5on5 Rush
'''

addInfoToConfig(serverCfg, text)

serverCfg = serverPath + "/" + addonPath + "/cfg/server.cfg"

text = '''
// Установка начальных параметров сервера по правилам ESL
exec "csgo_comp.cfg"

'''

addInfoToConfig(serverCfg, text)
