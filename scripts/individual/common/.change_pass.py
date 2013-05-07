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
import pexpect
import string
from random import choice

cgitb.enable()  # Debug

params = cgi.FieldStorage()

oldPass = str(params["p"].value).strip()
newPass = str(params["n"].value).strip()

if newPass == 'none':
    passwordSize = 9
    userPassword = ''.join([choice(string.letters + string.digits) for i in range(passwordSize)])
else:
    userPassword = newPass

try:
    child = pexpect.spawn('passwd -q')
    child.expect('Old Password:')
    child.sendline(oldPass)
    next = child.expect(['New Password:', pexpect.EOF], timeout=5)
    if next == 0:
        print "Перезаписываю пароль пользователя\n<br/>"
        child.sendline(userPassword)
        child.expect('Reenter New Password:')
        child.sendline(userPassword)
        print child.before
        child.expect(pexpect.EOF)

        print "<!-- RESULT START -->"
        print userPassword
        print "<!-- RESULT END -->"

    elif next == 1:
        print "Неверен предыдущий пароль. Измените пароль вручную."
        print "<!-- RESULT START -->"
        print "error"
        print "<!-- RESULT END -->"
        child.kill(0)


except OSError, e:
    # Сообщение об ошибке и переход к следующему серверу
    print >>sys.stderr, "Команда завершилась неудачей:", e
    print "<!-- RESULT START -->"
    print "error"
    print "<!-- RESULT END -->"
