#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Read log script.
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


print "Content-Type: text/html; charset=UTF-8"     # HTML is following
print                                              # blank line, end of headers

from optparse import OptionParser
import sys
import os
import re
import fnmatch
from subprocess import *
from shutil import *


parser = OptionParser()

parser.add_option("-a", "--action",  action="store", type="string", dest="action")
parser.add_option("-r", "--pattern", action="store", type="string", dest="pattern")
parser.add_option("-l", "--lines",   action="store", type="int",    dest="lines")
parser.add_option("-p", "--path",    action="store", type="string", dest="path")
parser.add_option("-n", "--name",    action="store", type="string", dest="name")

(options, args) = parser.parse_args(args=None, values=None)

if options.action and options.path:
    action = options.action
    pattern = options.pattern
    numOfLines = options.lines
    logPath = options.path
    logName = options.name

# Вырезать обратные переходы вида ' ../ ', дабы нельзя было изменить путь
if logPath:
    logPath = re.sub('(\.{2}/)', '', logPath)
if logName:
    logName = re.sub('(\.{2}/)', '', logName)

if action == "list":
    print "Чтение списка логов"
    print "<!-- LIST START -->"
    try:
        listDir = {}
        for file in os.listdir(logPath):
            if fnmatch.fnmatch(file, pattern):
                listDir[file] = os.path.getmtime(os.path.join(logPath, file))
        logs = listDir.keys()
        logs.sort(lambda x, y: cmp(listDir[x], listDir[y]))

        for log in logs:
            print log + ";"
    except OSError, e:
        print "Команда завершилась неудачей:", e

    print "<!-- LIST END -->"

elif action == "read":

    try:
        print "Попытка прочесть лог ", logPath
        log = open(logPath + "/" + logName, "r")
        print "<!-- LOG START -->"

        # Не знаю, как считать нужное количество строк.
        # Потому сделал тупой цикл. Стыдно, да =(
        i = 1
        for line in reversed(log.readlines()):
            print line.strip()
            i = i + 1
            if i > numOfLines:
                break

        print "<!-- LOG END -->"

        log.close()
    except OSError, e:
        print "Команда завершилась неудачей:", e
