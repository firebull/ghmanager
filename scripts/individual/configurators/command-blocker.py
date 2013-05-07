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
// Блокировка крэша сервера командой !settings
sm_blockcommand settings
sm_bancommand settings
sm_blockcommand sm_settings
sm_bancommand sm_settings

// Блокировка dump-атаки
sm_blockcommand dbghist_dump
sm_blockcommand dump_entity_sizes
sm_blockcommand dump_globals
sm_blockcommand dumpentityfactories
sm_blockcommand dumpeventqueue
sm_blockcommand groundlist
sm_blockcommand listmodels
sm_blockcommand mem_dump
sm_blockcommand physics_budget
sm_blockcommand physics_debug_entity
sm_blockcommand physics_report_active
sm_blockcommand physics_select
sm_blockcommand report_entities
sm_blockcommand report_touchlinks
sm_blockcommand snd_restart
sm_blockcommand soundscape_flush
sm_bancommand dbghist_dump
sm_bancommand dump_entity_sizes
sm_bancommand dump_globals
sm_bancommand dumpentityfactories
sm_bancommand dumpeventqueue
sm_bancommand groundlist
sm_bancommand listmodels
sm_bancommand mem_dump
sm_bancommand physics_budget
sm_bancommand physics_debug_entity
sm_bancommand physics_report_active
sm_bancommand physics_select
sm_bancommand report_entities
sm_bancommand report_touchlinks
sm_bancommand snd_restart
sm_bancommand soundscape_flush

'''

addInfoToConfig(serverCfg, text)
