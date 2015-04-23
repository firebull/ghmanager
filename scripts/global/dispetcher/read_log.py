#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Read log script.
Executes with clients rights.
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
import fnmatch
import json
from subprocess import *
from shutil import *
import gettext

gettext.install('ghmanager', '/images/scripts/i18n', unicode=1)

# Log array for JSON output
process = {'error' : [], 'log' : [], 'data' : { }}

try:

    parser = OptionParser()

    parser.add_option("-a", "--action",  action="store", type="string", dest="action")
    parser.add_option("-r", "--pattern", action="store", type="string", dest="pattern")
    parser.add_option("-s", "--lines",   action="store", type="int",    dest="lines")
    parser.add_option("-p", "--path",    action="store", type="string", dest="path")
    parser.add_option("-n", "--name",    action="store", type="string", dest="name")
    parser.add_option("-l", "--lang",    action="store", type="string", dest="lang")

    (options, args) = parser.parse_args(args=None, values=None)

    if options.action and options.path:
        action = options.action
        pattern = options.pattern
        numOfLines = options.lines
        logPath = options.path
        logName = options.name

    if options.lang:
        lang = options.lang
        langSet = gettext.translation('ghmanager', '/images/scripts/i18n', languages=[lang])
        langSet.install()

    # Вырезать обратные переходы вида ' ../ ', дабы нельзя было изменить путь
    if logPath:
        logPath = re.sub('(\.{2}/)', '', logPath)
    if logName:
        logName = re.sub('(\.{2}/)', '', logName)

    if action == "list":
        process['data'] = {'list' : []}
        process['log']  += ["INFO: " + _("Creating list of logs")]
        try:
            listDir = {}
            for file in os.listdir(logPath):
                if fnmatch.fnmatch(file, pattern):
                    listDir[file] = os.path.getmtime(os.path.join(logPath, file))
            logsList = listDir.keys()
            logsList.sort(lambda x, y: cmp(listDir[x], listDir[y]))

            for logFileName in logsList:
                process['data']['list'] += [logFileName]

            process['log']  += ["OK: " + _("List of logs created successfully")]
        except OSError, e:
            process['log']   += ["ERROR: " + _("While running the script an error occured: %s") % e]
            process['error'] += [_("While running the script an error occured. Read log.")]

    elif action == "read":

        try:
            process['data'] = {'text' : ""}
            process['log']  += ["INFO: " + _("Try to read log %s") % logPath]
            log = open(logPath + "/" + logName, "r")
            text = []

            i = 1
            for line in reversed(log.readlines()):
                text += [line.strip()]
                i = i + 1
                if i > numOfLines:
                    break

            log.close()

            # Let's reverse text back
            for line in reversed(text):
                process['data']['text'] += line + "\n"

        except OSError, e:
            process['log']   += ["ERROR: " + _("While running the script an error occured: %s") % e]
            process['error'] += [_("While running the script an error occured. Read log.")]

    print json.dumps(process)
    sys.exit(0)

except Exception, e:
    process['error']  += [_("While running the script an error occured: %s") % e]
    print json.dumps(process)
