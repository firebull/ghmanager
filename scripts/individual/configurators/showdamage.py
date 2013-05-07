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

serverCfg = serverPath + "/" + addonPath + "/cfg/server.cfg"

text = '''

// Показывать повреждения противника
sm_show_damage 1

// Показ повреждения от дружественного огня (Friendly fire)
sm_show_damage_ff 0

// Показ собственных повреждений
sm_show_damage_own_dmg 0

// Где показывать повреждения:
// 1 -> Центр экрана
// 2 -> Область сообщений
// 3 -> Чат
sm_show_damage_text_area 1

'''

addInfoToConfig(serverCfg, text)
