#!/usr/bin/env python2
# coding: UTF-8

# print "Content-Type: text/html"     # HTML is following
# print                               # blank line, end of headers

'''
***********************************************
Install ang configurate plugin or mod.
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


import distutils.dir_util
import os
from datetime import datetime, date, time
from subprocess import *
from shutil import *
from optparse import OptionParser

print datetime.now().strftime("%A, %d. %B %Y %H:%M:%S%p")
parser = OptionParser()

parser.add_option("-s", "--serverpath", action="store", type="string", dest="serverPath")
parser.add_option("-a", "--addonpath", action="store", type="string", dest="addonPath")
parser.add_option("-i", "--installpath", action="store", type="string", dest="installPath")
parser.add_option("-c", "--configurator", action="store", type="string", dest="configurator")
parser.add_option("-p", "--steam-id", action="store", type="string", dest="steamId")
parser.add_option("-g", "--guid", action="store", type="string", dest="guid")
parser.add_option("-m", "--more", action="store", type="string", dest="moreParams")

(options, args) = parser.parse_args(args=None, values=None)

addonPath = options.addonPath
serverPath = options.serverPath
installPath = options.installPath
configurator = options.configurator
steamId = str(options.steamId)
guid = str(options.guid)
moreParams = str(options.moreParams)

installTo = serverPath + "/" + installPath

print 'Попытка установить плагин: ', addonPath, '<br/>'
print 'Путь для установки плагина: ', serverPath + "/" + installPath, '<br/>'

try:
    distutils.dir_util.copy_tree(addonPath, installTo, preserve_symlinks=1)
    os.chmod(installTo, 0770)
    print "Копирование аддона завершено.</br>\n"
    print "<!-- INSTALL RESULT START -->"
    print "success"
    print "<!-- INSTALL RESULT END -->"

    # Конфигуратор аддона
    confScript = "/images/scripts/individual/configurators/" + configurator + ".py"

    if os.path.exists(confScript):
        try:
            retcode = call(confScript + " -s " + serverPath + " -a " + installPath +
                           " -p " + steamId + " -g " + guid + " -m \"" + moreParams + "\"", shell=True)
            if retcode < 0:
                print "Команда была прервана с кодом: ", retcode
                print "<!-- CONFIG RESULT START -->"
                print "error"
                print "<!-- CONFIG RESULT END -->"
            elif retcode == 0:
                print "Запускаю конфигуратор\n"
                print "<!-- CONFIG RESULT START -->"
                print "success"
                print "<!-- CONFIG RESULT END -->"
            else:
                print "Команда вернула код: ", retcode
        except OSError, e:
            print "Команда завершилась неудачей:", e
            print "<!-- CONFIG RESULT START -->"
            print "error"
            print "<!-- CONFIG RESULT END -->"
    else:
        print "<!-- CONFIG RESULT START -->"
        print "success"
        print "<!-- CONFIG RESULT END -->"

except OSError, e:
    print "Не удалось скопировать шаблон:", e, '</br>\n'
    print "<!-- INSTALL RESULT START -->"
    print "error"
    print "<!-- INSTALL RESULT END -->"
    # continue # Переход к следующему игровому серверу


print "\n\n<br/><br/>"
