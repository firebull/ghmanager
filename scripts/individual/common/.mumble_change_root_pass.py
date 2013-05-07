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
import os
import shlex
import sys
import string
import pwd
from shutil import *
from subprocess import *
from random import choice

cgitb.enable()  # Debug

params = cgi.FieldStorage()

action = str(params["action"].value).strip()
serverID = str(params["id"].value).strip()

serverPath = "/home/" + pwd.getpwuid(os.getuid())[0] + "/servers/mumble_" + serverID

if action == 'change':
    passwordSize = 9
    newPass = ''.join([choice(string.letters + string.digits) for i in range(passwordSize)])

    try:
        retcode = call(serverPath + "/murmur.x86" + " -supw " + newPass, shell=True)
        if retcode < 0:
            print >>sys.stderr, "Команда была прервана с кодом: ", -retcode
        elif retcode == 0:
            print >>sys.stderr, "Успешно."
            print "<!-- RESULT START -->"
            print newPass
            print "<!-- RESULT END -->"

    except OSError, e:
        # Сообщение об ошибке
        print >>sys.stderr, "Команда завершилась неудачей:", e
        print "<!-- RESULT START -->"
        print "error"
        print "<!-- RESULT END -->"
