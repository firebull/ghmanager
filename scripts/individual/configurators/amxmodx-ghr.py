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
from common import *

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

serverCfg = serverPath + "/" + addonPath + "/cfg/server.cfg"

plugin = "GHW_GHR.amxx"
modules = ['engine', 'cstrike', 'fun', 'fakemeta']

setPluginForAmxmodx(serverPath + "/" + addonPath, plugin, modules)

text = '''
// Скорость перетаскивания по команде +grab
grab_speed "5"

// Скорость полета при команде +hook
hook_speed "5"

// Скорость полета при движении по команде +rope
rope_speed "5"

// Включить Grab
// 1 - для всех, 0 - только админам
grab_enabled "0"

// Включить Hook
// 1 - для всех, 0 - только админам
hook_enabled "1"

// Включить Rope
// 1 - для всех, 0 - только админам
rope_enabled "1"

'''

addInfoToConfig(serverCfg, text)
