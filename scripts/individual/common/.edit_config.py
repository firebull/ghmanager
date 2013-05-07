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

import cgi
import cgitb
import sys
import os
import re
from subprocess import *
from shutil import *


cgitb.enable()  # Debug

server = cgi.FieldStorage()

template = str(server["server"].value).strip()
serverId = str(server["serverId"].value).strip()
serverType = str(server["serverType"].value).strip()
action = str(server["action"].value).strip()
rootPath = str(server["rootPath"].value).strip()
configPath = str(server["configPath"].value).strip()
configName = str(server["configName"].value).strip()
configText = server["configText"].value

serverName = template + '_' + serverId

configPathFull = "../../servers/" + serverName + "/" + rootPath + "/" + configPath + "/" + configName

excludeText = ['set net_ip',
               'set net_port',
               'set sv_maxclients',
               'set ui_maxclients',
               'pingboost',
               'tickrate',
               'sys_tickrate',
               'systickrate']

if action == "read":

    try:
        if os.path.exists(configPathFull):
            print "<!-- CONFIG START -->"

            for line in open(configPathFull):
                print line.strip()

            print "<!-- CONFIG END -->"

    except OSError, e:
        print "Команда завершилась неудачей", e

# Тут правка уже существующего плагина или создание пустого
elif action == "write":

    try:
        if os.path.exists(configPathFull):
            # сохранить резервную копию
            os.rename(configPathFull, configPathFull + ".bak")
        # Новый с тем же именем
        newConfig = open(configPathFull, "w")

        for line in configText.splitlines():
            excludeMe = False  # Исключать или нет строку

            for excludeLine in excludeText:
                if re.match(excludeLine, line.strip().lower()):
                    excludeMe = True

            if excludeMe == False:
                newConfig.write(line.strip() + "\n")

        newConfig.close()
        os.chmod(configPathFull, 0640)

        print "<!-- RESULT START -->"
        print "success"
        print "<!-- RESULT END -->"

    except OSError, e:
        print "<!-- RESULT START -->"
        print "Команда завершилась неудачей", e
        print "<!-- RESULT END -->"

# Создать мод из шаблона
elif action == "create":
    configTemplatePathFull = "/images/scripts/servers_configs/" + serverType + \
        "/" + template + "/" + rootPath + "/" + configPath + "/" + configName
    try:
        if os.path.exists(configPathFull):
            # сохранить резервную копию
            os.rename(configPathFull, configPathFull + ".bak")

        # Новый с тем же именем
        newConfig = open(configPathFull, "w")

        # Если шаблона конфига нет - впишем сверху коммент об этом

        if not os.path.exists(configTemplatePathFull):
            newConfig.write("// Типовой шаблон конфига <" + configName +
                            "> для этого сервера отсутствует.\n" +
                            "// Можете написать нам об этом и предложить свой вариант типового конфига =)\n")
        else:
            configTemplate = open(configTemplatePathFull, "r")

            for line in configTemplate:

                newConfig.write(line.strip() + "\n")

            configTemplate.close()

        newConfig.close()
        os.chmod(configPathFull, 0640)

        print "<!-- RESULT START -->"
        print "success"
        print "<!-- RESULT END -->"

    except OSError, e:
        print "<!-- RESULT START -->"
        print "Команда завершилась неудачей", e
        print "<!-- RESULT END -->"
