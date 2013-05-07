#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Writes admin to mods (SM, AMX and so on).
Executes with clients rights.
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


import sys
import re
from optparse import OptionParser
from commonLib import addAdminToMani, xmlLog
sys.path.append("/images/scripts/individual/configurators")
from common import addInfoToConfig

parser = OptionParser()
parser.add_option("-a", "--adminString",  action="store", type="string", dest="admStr")
parser.add_option("-t", "--adminType",    action="store", type="string", dest="admType")
parser.add_option("-m", "--mod",    action="store", type="string", dest="mod")
parser.add_option("-c", "--config", action="store", type="string", dest="config")

(options, args) = parser.parse_args(args=None, values=None)
admStr = options.admStr
admType = options.admType
mod = options.mod
config = options.config

'''

 Для ManiAdmin использовать функцию addAdminToMani (config, admin, adminType)
 Для SourceMod и Amxmodx использовать addInfoToConfig

'''
xmlLog('Попытка добавить админа в %s' % mod)

if mod == 'sourcemod':

    if admType == 'steam':
        text = '"%s" "99:z"' % admStr
    elif admType == 'ip':
        text = '"!%s" "99:z"' % admStr
    elif admType == 'userPass':
        text = '%s "99:z"' % admStr

    # Для безопасности надо проверить наличие имени конфига в пути
    if re.search('admins_simple.ini', config):
        print '<log>'
        addInfoToConfig(config, text)
        print '/<log>'
    else:
        xmlLog('Указан некорректный конфиг', 'error')

elif mod == 'amxmodx':

    if admType == 'steam':
        text = '"%s" "" "abcdefghijklmnopqrstu" "ce"' % admStr
    elif admType == 'ip':
        text = '"%s" "" "abcdefghijklmnopqrstu" "de"' % admStr
    elif admType == 'userPass':
        text = '%s "abcdefghijklmnopqrstu" "a"' % admStr

    if re.search('users.ini', config):
        print '<log>'
        addInfoToConfig(config, text)
        print '/<log>'
    else:
        xmlLog('Указан некорректный конфиг', 'error')

elif mod == 'maniadmin':

    if re.search('clients.txt', config):
        addAdminToMani(config, admStr, admType)
    else:
        xmlLog('Указан некорректный конфиг', 'error')
