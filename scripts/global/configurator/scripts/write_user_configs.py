#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Creates configs with configurator owner and user group rights.
So client can only read config, but cannot edit.
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

# Скрипт предназначен для записи измененных конфигов клиентов
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
from random import choice

cgitb.enable()  # Debug

params = cgi.FieldStorage()

action = str(params["action"].value).strip()
cfgPath = str(params["cfgPath"].value).strip()
cfgText = str(params["cfgText"].value).strip()


# Тут правка уже существующего конфига или создание нового
if action == "write":

    try:
        # Новый с тем же именем
        newConfig = open(cfgPath, "w")

        for line in cfgText.splitlines():

            newConfig.write(line.strip() + "\n")

        newConfig.close()
        os.chmod(cfgPath, 0640)

        print "<!-- RESULT START -->"
        print "success"
        print "<!-- RESULT END -->"

    except OSError, e:
        print "<!-- RESULT START -->"
        print "Команда завершилась неудачей", e
        print "<!-- RESULT END -->"
