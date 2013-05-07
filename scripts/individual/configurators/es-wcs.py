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
parser.add_option("-p", "--steam-id", action="store", type="string", dest="steamId")
parser.add_option("-g")
parser.add_option("-m")

(options, args) = parser.parse_args(args=None, values=None)
serverPath = options.serverPath
addonPath = options.addonPath
steamId = options.steamId

autoexecCfg = serverPath + "/" + addonPath + "/cfg/autoexec.cfg"
adminCfg = serverPath + "/" + addonPath + "/addons/eventscripts/wcs/data/es_WCSadmins_db.txt"

text = '''\nes_load wcs // WarCraft Source Mod'''
addInfoToConfig(autoexecCfg, text)

text = '''"WCSadmins"
{
    "%s"
    {
	"wcsadmin"      "1"
	"wcsadmin_addadmins"      "1"
	"wcsadmin_removeadmins"      "1"
	"wcsadmin_editadmins"      "1"
	"wcsadmin_settings"      "1"
	"wcsadmin_givexp"      "1"
	"wcsadmin_givelevels"      "1"
	"wcsadmin_givecash"      "1"
	"wcsadmin_resetrace"      "1"
	"wcsadmin_resetplayer"      "1"
	"wcscadmin"      "1"
	"wcscadmin_give_self"      "1"
	"wcscadmin_give_players"      "1"
	"wcscadmin_give_cash"      "1"
	"wcscadmin_force_changerace"      "1"
	"wcscadmin_settings"   "1"
	"wcscadmin_players"      "1"
	"wcscadmin_admins"      "1"
	"wcscadmin_add_new_admin"      "1"
	"wcscadmin_update_admin"      "1"
	"wcscadmin_delete_admin"      "1"
    }
}
''' % steamId

if steamId != '' and steamId != 'none':
    addInfoToConfig(adminCfg, text)
