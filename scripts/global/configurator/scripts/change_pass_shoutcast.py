#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
ShoutCast pass change.
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


# Скрипт предназначен для смены паролей SHOUTcast, хранимых в текстовом конфиге,
# с правами пользователя Configurator и группы клиента,
# чтобы клиент мог только просматривать содержимое, но не редактировать.

print "Content-Type: text/html; charset=UTF-8"     # HTML is following
print                                              # blank line, end of headers

import cgi
import cgitb
import sys
import pexpect
import string
import os
import re
from shutil import *
from random import choice

cgitb.enable()  # Debug

params = cgi.FieldStorage()

action = str(params["action"].value).strip()
cfgPath = str(params["cfgPath"].value).strip()


def getPass(pattern):
    try:
        if os.path.exists(cfgPath):
            for line in open(cfgPath):
                if re.match(pattern, line):  # Искать надо только по началу строки
                    currentPass = line.split("=")
                    return currentPass[1].strip()
                    break
        else:
            return False
    except:
        print "Команда завершилась неудачей"
        return False


def changePass(pattern, newPass):
    try:
        if os.path.exists(cfgPath):
            hash = ''.join([choice(string.letters + string.digits) for i in range(20)])
            tmpCfg = '/home/configurator/sc_serv.conf_' + hash + ".tmp"
            copyfile(cfgPath, tmpCfg)
            newCfg = open(cfgPath, "w")
            for line in open(tmpCfg):
                if re.match(pattern, line):  # Искать надо только по началу строки
                    line = pattern + newPass + "\n"
                newCfg.write(line)

            newCfg.close()
            os.remove(tmpCfg)
            os.chmod(cfgPath, 0640)
            return True

        else:
            return False
    except OSError, e:
        print "Команда завершилась неудачей", e
        return False

passwordSize = 9
newPassword = ''.join([choice(string.letters + string.digits) for i in range(passwordSize)])

if action == 'view':
    password = getPass("Password=")
    adminPassword = getPass("AdminPassword=")
    print "<!-- RESULT START -->"
    # Вернём пароли в виде строки через запятую.
    # Если какой то из паролей будет с ошибкой,
    # вернуть вместо него error.
    if password:
        passwords = password.strip() + ","
    else:
        passwords = 'error,'
    if adminPassword:
        passwords = passwords + adminPassword.strip()
    else:
        passwords = passwords + 'error'

    print passwords

    print "<!-- RESULT END -->"


elif action == 'changePass':

    if changePass("Password=", newPassword):
        print "<!-- RESULT START -->"
        print "success"
        print "<!-- RESULT END -->"
    else:
        print "<!-- RESULT START -->"
        print "error"
        print "<!-- RESULT END -->"


elif action == 'changeAdminPass':

    if changePass("AdminPassword=", newPassword):
        print "<!-- RESULT START -->"
        print "success"
        print "<!-- RESULT END -->"
    else:
        print "<!-- RESULT START -->"
        print "error"
        print "<!-- RESULT END -->"
