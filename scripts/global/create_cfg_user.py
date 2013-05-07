#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Creates Configurator user wich has all scripts and rights to control servers.
This script must run every change in scripts/configurator dir.
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

import os
import shlex
import sys
import pwd
import string
from random import choice
from subprocess import *
from shutil import *

userName = 'configurator'
homeDir = "/home/%s" % userName
scriptDir = "/home/%s/public_html" % userName

print 'New user name: ', userName

# Создаем сначала группу пользователя
try:
    retcode = call("groupadd" + " " + userName, shell=True)
    if retcode < 0:
        print >>sys.stderr, "Команда была прервана с кодом: ", retcode
    elif retcode == 0:
        print >>sys.stderr, "Группа пользователя успешно создана"
    else:
        print >>sys.stderr, "Команда вернула код: ", retcode
except OSError, e:
    print >>sys.stderr, "Команда завершилась неудачей:", e

# Создаем пользователя
# Если пользователь уже существует, то считаем это
# нормальным - пользователь уже есть, идем дальше
# и создаем дополнительный сервер в его каталоге.

# Проверка на наличие уже сгенерированного пароля:
# если таковой существует, то используем его далее,
# т.к. пользователеь может иметь игровые серверы на
# разных физических


# Генерация пароля из 25-ти символов
# Алгоритм подсмотрен http://snipplr.com/view/3677/quick-simple-password-generator/
passwordSize = 25
userPassword = ''.join([choice(string.letters + string.digits) for i in range(passwordSize)])

# Создание пользователя
try:
    retcode = call("useradd" + " -m " + userName + " -g " + userName + " -p " + userPassword + " -s /bin/false", shell=True)
    if retcode < 0:
        print >>sys.stderr, "Команда была прервана с кодом: ", retcode
    elif retcode == 0:

        # Устанавливаем права на домашний каталог 0751
        os.chmod(homeDir, 0751)
        os.chmod(scriptDir, 0751)

        print >>sys.stderr, "Пользователь успешно создан"
    else:
        print >>sys.stderr, "Команда вернула код: ", retcode
except OSError, e:
    print >>sys.stderr, "Команда завершилась неудачей:", e

# Создание конфигурационного файла Apache
print "Создаю конфиг Apache."
configTemplate = open("../root_config/configurator.conf", "r")
configUser = open("/etc/apache2/conf.d/" + userName + ".conf", "w")

for line in configTemplate:

    line = line.replace("%s", userName)
    configUser.write(line)

configTemplate.close()
configUser.close()
try:
    print "Перезапуск сервера Apache"
    call(['rcapache2', 'restart'])
except OSError, e:
    print "Не удалось перезапустить Apache:", e
# Конец создания конфига Apache
#

# Установим пользователя, от имени которого копируем файлы

# Теперь получим uid и gid
pw = pwd.getpwnam('configurator')
userUid = pw.pw_uid
userGid = pw.pw_gid

os.setegid(userGid)
os.seteuid(userUid)

# Попытка скопировать скрипты, запускаемые от имени клиента
scriptsSrc = "/images/scripts/global/configurator/"
scriptsDest = "/home/" + userName + "/public_html/"
if os.path.exists(scriptsDest):
    print "Директория существует. Перезапись скриптов на последнюю версию."
    try:
        rmtree(scriptsDest)
        print "Директория удалена. Начинаю копировать скрипты."
    except OSError, e:
        print "Не удалось удалить директорию:", e
try:
    copytree(scriptsSrc, scriptsDest, symlinks=True)

    os.chown(scriptsDest, userUid, userGid)
    os.chmod(scriptsDest, 0751)
    retcode = call("chmod" + " -R 751 " + scriptsDest, shell=True)
    os.chmod(scriptsDest + "/scripts/start_stop_valve.py", 0755)
    os.chmod(scriptsDest + "/scripts/start_stop_ueds.py", 0755)
    os.chmod(scriptsDest + "/scripts/start_stop_cod.py", 0755)
    os.chmod(scriptsDest + "/scripts/start_stop_mumble.py", 0755)
    os.chmod(scriptsDest + "/scripts/plugin_check.py", 0755)
    os.chmod(scriptsDest + "/scripts/plugin_delete.py", 0755)
    os.chmod(scriptsDest + "/mapsUploader/uploader.php", 0755)
    os.chmod(scriptsDest + "/scripts/dir_list.py", 0755)
    os.chmod(scriptsDest + "/scripts/files_list.py", 0755)
    os.chmod(scriptsDest + "/scripts/read_log.py", 0755)
    os.chmod(scriptsDest + "/scripts/read_write_param.py", 0755)
    os.chmod(scriptsDest + "/scripts/write_admin_to_mod.py", 0755)
    os.chmod(scriptsDest + "/scripts/map_install.py", 0755)
    os.chmod(scriptsDest + "/scripts/update_valve.py", 0755)
    os.chmod(scriptsDest + "/scripts/filter.py", 0755)
    os.chmod(scriptsDest + "/scripts/commonLib", 0755)
    os.chmod(scriptsDest + "/scripts/commonLib/__init__.py", 0755)
    print "Копирование скриптов успешно."
except OSError, e:
    print "Не удалось скопировать скрипты:", e
# Попытка создать директорию для конфигурация запуска серверов клиентов
if not os.path.exists("/home/" + userName + "/startCfgs"):
    os.makedirs("/home/" + userName + "/startCfgs")
    os.chown("/home/" + userName + "/startCfgs", userUid, userGid)
    os.chmod("/home/" + userName + "/startCfgs", 0755)

os.seteuid(0)
os.setegid(0)
