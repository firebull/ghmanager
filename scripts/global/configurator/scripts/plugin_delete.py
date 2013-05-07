#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Plugin delete script.
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


def delete(pluginDir, serverDir):
    saveExts = ['.cfg', '.ini']  # только проверить размер, на признак инсталлирован не влияет
    desc = ''
    err = 0
    for root, dirs, files in os.walk(pluginDir, topdown=True):

        subTree = root.split(pluginDir)
        for name in files:
            toRename = False
            dest = serverDir + subTree[1] + "/" + name
            desc += dest
            if os.path.exists(dest):
                for saveExt in saveExts:
                    if re.search(saveExt, name):

                        ourSize = getsize(join(root, name))
                        checkSize = getsize(dest)
                        if ourSize != checkSize:
                            toRename = True

                if toRename == True:
                    # Переименовать файл
                    try:
                        os.rename(dest, dest + ".old")
                        desc += " - Файл переименован\n"
                    except OSError, e:
                        desc += " - Ошибка\n"
                else:
                    try:
                        os.remove(dest)
                        desc += " - Файл удален\n"
                    except OSError, e:
                        desc += " - Ошибка\n", e

            else:
                desc += " - Файл не найден\n"

    print desc
    if err == 0:
        return True
    else:
        return False

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
res = delete(resource, dest)
print "</desc>"

if res == True:
    print '<result>success</result>'
else:
    print '<result>error</result>'
