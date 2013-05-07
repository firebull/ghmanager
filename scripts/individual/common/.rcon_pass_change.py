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

action = server["action"].value
config = server["config"].value
newPass = server["pass"].value


def getPass(config):
    try:
        if os.path.exists(config):
            for line in open(config):
                if re.match("rcon_password", line) or re.match("set rcon_password", line) or re.match("adminpassword", line):  # Искать надо только по началу строки
                    password = line.split('"')
                    return password[1].strip()
                    break
            return "nopass"
        else:
            return "nofile"
    except:
        print "Команда завершилась неудачей"
        return False


def setPass(config, passCur, passNew):
    try:
        newConfig = config + ".tmp"
        if os.path.exists(config):
            tmpConfig = open(newConfig, "w")
            for line in open(config):

                line = line.replace(passCur, passNew)
                tmpConfig.write(line)

            tmpConfig.close()
            os.remove(config)
            os.rename(newConfig, config)
            os.chmod(newConfig, 0660)

            return str(config)

        else:
            return False
    except OSError, e:
        print "Команда завершилась неудачей ", e
        return False

if action == "get":
    print "Попытка узнать текущий пароль RCON<br>\n"
    print "<!-- PASS START -->"
    password = getPass(config)
    if password != False:
        print password
    elif password == False:
        print 'error'

    print "<!-- PASS END -->\n"

elif action == "set":
    print "Попытка смены пароля RCON<br>\n"
    print "<!-- RESULT START -->"
    curPass = getPass(config)
    if curPass != False:
        if setPass(config, curPass, newPass) != False:
            print "success"
    else:
        print "error"
    print "<!-- RESULT END -->\n"
