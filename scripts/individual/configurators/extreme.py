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
import os
import re
import sys

parser = OptionParser()
parser.add_option("-s", "--server-path", action="store", type="string", dest="serverPath")
parser.add_option("-a", "--addon-path", action="store", type="string", dest="addonPath")
parser.add_option("-g", "--guid", action="store", type="string", dest="guid")
parser.add_option("-p")
parser.add_option("-m")

(options, args) = parser.parse_args(args=None, values=None)
serverPath = options.serverPath
addonPath = options.addonPath
guid = options.guid

configTemp = "/images/scripts/servers_configs/cod/cod4/mods/extreme/modserver.cfg"
config = serverPath + "/" + addonPath + "/mods/extreme/modserver.cfg"

if os.path.exists(configTemp):
    try:
        cfgTemp = open(configTemp, 'r')
        cfg = open(config, 'w')
        for line in cfgTemp:
            if guid.lower() != 'none' and re.match('set scr_admins', line):
                cfg.write('set scr_admins "' + guid + '"')
            else:
                cfg.write(line)
        cfgTemp.close()
        cfg.close()
        os.chmod(config, 0640)
    except OSError, e:
        print "Не удалось обновить конфиг из-за ошибки:", e
