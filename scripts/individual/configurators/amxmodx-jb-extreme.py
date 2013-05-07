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

plugin = "jbextreme.amxx"
modules = ['engine', 'cstrike']
setPluginForAmxmodx(serverPath + "/" + addonPath, plugin, modules)

plugin = "jbextreme_es.amxx"
modules = ['engine', 'cstrike']
setPluginForAmxmodx(serverPath + "/" + addonPath, plugin, modules)

serverCfg = serverPath + "/" + addonPath + "/server.cfg"
mapCfg = serverPath + "/" + addonPath + "/mapcycle.txt"

text = '''
// Параметры мода в отдельном конфиге
exec jbextreme.cfg
sv_downloadurl "http://fdl1.teamserver.ru/fastdl/cs16/"
'''

addInfoToConfig(serverCfg, text)

# Карты
maps = '''
ba_jail_rofl_v2
ba_tamama_v2
jail_1337_break
jail_abc_outside_b2
jail_antique_v2
jail_antique_v-akolit3
jail_avanzar_v1
jailbreak_akg_maxx
jailbreak_final_1
jailbreak_recharged_remake2
jailbreak_revolution_rage_v5c
jailbreak_revolution_rage_v7
jailbreak_sneakpeek_v1
jail_city_b1
jail_colosseum
jail_d4x
jail_destiny_v1
jail_escape_v3
jail_flash_strike1_3
jail_kiwi_fixed
jail_millenium_b7
jail_millenium_issue_b2
jail_mrafortress_v2
jail_neon_new
Jail_OurPain_b1
jail_school
jail_shownback_fixed
jail_soon_fixed4
jail_thiebault_b4
jail_tod_storm
jail_white_new
jail_zow_dp
'''

addInfoToConfig(mapCfg, maps)
