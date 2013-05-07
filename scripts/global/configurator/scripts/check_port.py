#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Check either port free or not.
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


# Скрипт проверки IP порта - занят или нет

print "Content-Type: text/html; charset=UTF-8"     # HTML is following
print                                              # blank line, end of headers

import socket

import cgi
import cgitb

cgitb.enable()  # Debug

params = cgi.FieldStorage()

ip = str(params["ip"].value).strip()
port = int(params["port"].value)
s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)

try:
    s.bind((ip, port))
    s.close()
    print "<!-- RESULT START -->"
    print "open"
    print "<!-- RESULT END -->"
except socket.error, e:
    print "Port already in use", e
    print "<!-- RESULT START -->"
    print "used"
    print "<!-- RESULT END -->"
