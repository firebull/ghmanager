#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Check if plugin is installed at client's server script.
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


import cgi
import cgitb
import sys
import pwd
import os
import re
from os.path import join, getsize
from datetime import datetime, date, time
from subprocess import *
from optparse import OptionParser


def scan(pluginDir, serverDir):
    mustBeExts = ['.smx', '.so', '.sp', '.mp3']
        # Файлы с этими расширениями должны быть + проверка размера. Если такого фала нет = плагин не установлен
    mustBeDirs = ['gamedata', 'extensions', 'plugins', 'scripts',
                  'scripting']  # Файлы в этих директориях должны быть + проверка размера
    checkExts = ['.cfg', '.ini']  # только проверить размер, на признак инсталлирован не влияет
    toPass = ['.txt', 'readme', 'copyright', 'credits', 'changelog']  # Пропускать такие файлы без проверки
    desc = ''
    cycle = 'go'
    for root, dirs, files in os.walk(pluginDir, topdown=True):
        if cycle == 'break':
            return 'not installed'
            break

        subTree = root.split(pluginDir)
        critFilesInDir = False
        for mustBeDir in mustBeDirs:
            if re.search(mustBeDir, subTree[1]):
                critFilesInDir = True  # Ставим ключ, что директория содержит критичные файлы

        for name in files:
            critFile = False
            checkSize = False
            if critFilesInDir == False:  # Если файл содержится не в критичной директории
                    for mustBeExt in mustBeExts:  # Сначала проверить расширение на принадлежность к критичным
                        if re.search(mustBeExt, name):
                            critFile = True  # ключ - файл критичен
                        else:   # Если нет, то проверить на принадлженость к необходимости проверить только размер, если он есть
                            for checkExt in checkExts:
                                if re.search(checkExt, name):
                                    checkSize = True  # Ключ - проверить размер, если файл существует



            if not os.path.exists(serverDir + subTree[1] + "/" + name):
                if critFilesInDir == True or critFile == True:
                    desc += "%s%s/%s - Не найден критичный файл\n" % (serverDir, subTree[1], name)
                    desc += "Проверка остановлена, плагин не установлен.\n"

                    print desc
                    cycle = 'break'
                    break

            else:
                ourSize = getsize(join(root, name))
                realSize = getsize(serverDir + subTree[1] + "/" + name)

                if ourSize != realSize and (critFilesInDir == False or critFile == False and checkSize == True):
                    desc += "%s%s/%s - Установлена другая версия\n" % (serverDir, subTree[1], name)
                elif ourSize != realSize and (critFilesInDir == True or critFile == True):
                    desc += "%s%s/%s - Установлена другая версия критичного файла\n" % (serverDir, subTree[1], name)
                    print desc
                    cycle = 'break'
                    break

    if cycle == 'break':  # Иногда, когда обрывается цикл на последнем файле, выход из фуекции надо осуществялть тут
        return 'not installed'
    else:
        return 'installed'

parser = OptionParser()

parser.add_option("-r", "--resource", action="store", type="string", dest="resource")
parser.add_option("-d", "--dest", action="store", type="string", dest="dest")

(options, args) = parser.parse_args(args=None, values=None)

if options.resource and options.dest:
    resource = options.resource
    dest = options.dest

else:
    print "Content-Type: text/html"     # HTML is following
    print                               # blank line, end of headers
    params = cgi.FieldStorage()
    resource = str(params["r"].value).strip()
    dest = str(params["d"].value).strip()
    cgitb.enable()  # Debug


print "<desc>"
res = scan(resource, dest)
print "</desc>"
if res == 'installed':
        print '<result>installed</result>'
else:
        print '<result>none</result>'
