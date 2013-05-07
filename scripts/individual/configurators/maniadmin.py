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
print                               # blank line, end of headers

from optparse import OptionParser
from string import lower
import os
import sys

parser = OptionParser()
parser.add_option("-s", "--server-path", action="store", type="string", dest="serverPath")
parser.add_option("-a", "--addon-path", action="store", type="string", dest="addonPath")
parser.add_option("-p", "--steam-id", action="store", type="string", dest="steamId")
parser.add_option("-g")
parser.add_option("-m")

(options, args) = parser.parse_args(args=None, values=None)
serverPath = options.serverPath
addonPath = options.addonPath
steamId = options.steamId

vdfPath = serverPath + "/" + addonPath + "/addons/maniadmin.vdf"
adminsCfg = serverPath + "/" + addonPath + "/cfg/mani_admin_plugin/clients.txt"

if not os.path.exists(vdfPath):
    print "Создаю maniadmin.vdf"
    try:
        vdf = open(vdfPath, "w")
        text = '"Plugin"\n \
                {\n   \
                "file" "../%s/addons/mani_admin_plugin_i486"\n \
                }\n' % addonPath
        vdf.write(text)
        os.chmod(vdfPath, 0640)
        vdf.close()
    except OSError, e:
                print "Команда завершилась неудачей:", e

adminTextEmpty = '''"clients.txt"
// Вы можете создать собственный список
// админов и групп используя, например, эту программу:
// http://www.mani-admin-plugin.com/joomla/index.php?option=com_kunena&func=view&catid=8&id=16791&Itemid=93

// Если вы добавите свой SteamID в профиль, то при установке любого мода,
// вы автоматически будете добавлены в его админы.
{
"version"    "1"

    "players"
    {
        // В этой категории вводите информацию обо всех админах
    }

    "groups"
    {
         // Пожалуйста, не удаляйте группу SuperAdmin!!!
        "Immunity"
        {
            "Superadmin" "autojoin grav ping afk a b c d e f h i k l m n o p q r s t u v w x y"
        }
        "Admin"
        {
            "Superadmin" "q2 q3 grav pban A B C D E F G H I J K L M N O P Q R S T U V"
            "Superadmin" "W X Y Z a b c d e f g i k l m o p q r s t v w x y z client"
            "Superadmin" "admin spray"
        }
    }
}
'''

adminText = '''"clients.txt"
// Это простейшая структура файла,
// c группой админа с максимальными правами
// Но вы можете создать собственный список
// админов и групп используя, например, эту программу:
// http://www.mani-admin-plugin.com/joomla/index.php?option=com_kunena&func=view&catid=8&id=16791&Itemid=93

{
    "version"    "1"

    "players"
    {
        // В этой категории вводите информацию обо всех админах
        // Здесь должно быть уникальное имя админа без пробелов
        "Admin#1"
        {
            // Настоящее имя админа
            "name"    "Admin#1"
            // Steam ID админа
            "steam"    "%s"
            // Персональные права доступа и иммунитет
            "groups"
            {
                "Immunity" "Superadmin"
                "Admin"    "Superadmin"
            }
        }

    }


    "groups"
    {
         // Пожалуйста, не удаляйте группу SuperAdmin!!!
        "Immunity"
        {
            "Superadmin" "autojoin grav ping afk a b c d e f h i k l m n o p q r s t u v w x y"
        }
        "Admin"
        {
            "Superadmin" "q2 q3 grav pban A B C D E F G H I J K L M N O P Q R S T U V"
            "Superadmin" "W X Y Z a b c d e f g i k l m o p q r s t v w x y z client"
            "Superadmin" "admin spray"
        }
    }
}


''' % steamId

try:
    if os.path.exists(adminsCfg):
        # сохранить резервную копию
        os.rename(adminsCfg, adminsCfg + ".bak")

    ini = open(adminsCfg, "w")
    if steamId and steamId.lower() != 'none':
        print "Вношу админов"
        ini.write(adminText)
    else:
        print "Создаю пустой конфиг"
        ini.write(adminTextEmpty)
except OSError, e:
            print "Команда завершилась неудачей:", e
