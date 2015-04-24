#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Copyright (C) 2015 Nikita Bulaev

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


from optparse import OptionParser
import sys
import os
import re
import json
from subprocess import *
from shutil import *
import gettext

gettext.install('ghmanager', '/images/scripts/i18n', unicode=1)

# Log array for JSON output
process = {'error' : [], 'log' : [], 'data' : { }}

try:
    parser = OptionParser()

    parser.add_option("-a", "--action", action="store", type="string", dest="action")
    parser.add_option("-t", "--text",   action="store", type="string", dest="text")
    parser.add_option("-r", "--root",   action="store", type="string", dest="root")
    parser.add_option("-p", "--path",   action="store", type="string", dest="path")
    parser.add_option("-n", "--name",   action="store", type="string", dest="name")
    parser.add_option("-l", "--lang",   action="store", type="string", dest="lang")

    (options, args) = parser.parse_args(args=None, values=None)

    if options.action and options.path:
        action = options.action
        rootPath = options.root
        configText = options.text
        configPath = options.path
        configName = options.name

    if options.lang:
        lang = options.lang
        langSet = gettext.translation('ghmanager', '/images/scripts/i18n', languages=[lang])
        langSet.install()

    configPathFull = os.path.join(rootPath, configPath, configName)

    excludeText = ['set net_ip',
                   'set net_port',
                   'set sv_maxclients',
                   'set ui_maxclients',
                   'pingboost',
                   'tickrate',
                   'sys_tickrate',
                   'systickrate']

    # Read content of config
    if action == "read":
        process['log']  += ["INFO: " + _("Trying to read config %s") % configPathFull]
        try:
            if os.path.exists(configPathFull):
                process['data'] = {'config' : ''}
                config = open(configPathFull, "r")

                for line in config.readlines():
                    process['data']['config'] += line.strip() + "\n"

                config.close()
                process['log']  += ["OK: " + _("Success")]

        except OSError, e:
            process['log']   += ["ERROR: " + _("While running the script an error occured: %s") % e]
            process['error'] += [_("While running the script an error occured. Read log.")]

    # Edit config
    elif action == "write":

        try:
            if os.path.exists(configPathFull):
                # Backup old config
                process['log']  += ["INFO: " + _("Backup config %s to %s.bak") % (configPathFull, configName)]
                os.rename(configPathFull, configPathFull + ".bak")
                process['log']  += ["OK: " + _("Backup Success")]

            # Create new config
            newConfig = open(configPathFull, "w")
            process['log']  += ["INFO: " + _("Writing data to config %s") % configPathFull]

            for line in configText.splitlines():
                excludeMe = False  # Exclude danger string or not

                for excludeLine in excludeText:
                    if re.match(excludeLine, line.strip().lower()):
                        excludeMe = True

                if excludeMe == False:
                    newConfig.write(line.strip() + "\n")

            newConfig.close()
            os.chmod(configPathFull, 0640)
            process['log']  += ["OK: " + _("Success")]

            process['data'] = "success"

        except OSError, e:
            process['log']   += ["ERROR: " + _("While running the script an error occured: %s") % e]
            process['error'] += [_("While running the script an error occured. Read log.")]

    # Create config from template
    elif action == "create":
        configTemplatePathFull = os.path.join("/images/scripts/servers_configs/", \
            configPath, configName)

        try:
            if os.path.exists(configTemplatePathFull):
                if os.path.exists(configPathFull):
                    # Backup current config
                    os.rename(configPathFull, configPathFull + ".bak")

                # Create new config
                newConfig = open(configPathFull, "w")

                configTemplate = open(configTemplatePathFull, "r")

                for line in configTemplate:

                    newConfig.write(line.strip() + "\n")

                configTemplate.close()

                newConfig.close()
                os.chmod(configPathFull, 0640)

                process['log']  += ["OK: " + _("Success")]
                process['data'] = "success"
            else:
                process['log']  += ["WARN: " + _("No template for config %s") % configName]
                process['data'] = "no_template"

        except OSError, e:
            process['log']   += ["ERROR: " + _("While running the script an error occured: %s") % e]
            process['error'] += [_("While running the script an error occured. Read log.")]

    print json.dumps(process)

except Exception, e:
    process['error']  += [_("While running the script an error occured: %s") % e]
    print json.dumps(process)

