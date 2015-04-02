#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Install ang configurate plugin or mod.
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

print 'INFO: Попытка установить плагин: ', addonPath
print 'INFO: Путь для установки плагина: ', serverPath + "/" + installPath

try:
    distutils.dir_util.copy_tree(addonPath, installTo, preserve_symlinks=1)
    os.chmod(installTo, 0770)
    print "OK: Копирование аддона завершено"

    # Конфигуратор аддона
    confScript = "/images/scripts/individual/configurators/" + configurator + ".py"

    print "INFO: Запускаю конфигуратор"
    if os.path.exists(confScript):
        try:
            retcode = Popen(confScript
                            + " -s " + serverPath
                            + " -a " + installPath
                            + " -p " + str(steamId)
                            + " -g " + str(guid)
                            + " -m " + str(moreParams),
                            shell=True,
                            stdin=PIPE,
                            stdout=PIPE,
                            stderr=PIPE)
            retcode.wait()
            (out, err) = retcode.communicate()
            print out
            if err < 0:
                print "ERROR: Команда была прервана с кодом: %s" % err

            elif err == 0 or err == "":
                print "OK: Конфигурация успешна"

            else:
                print "ERROR: При попытке запуска конфигуратора мода/плагина возникла ошибка: %s" % err

        except OSError, e:
            print "ERROR: Команда завершилась неудачей: %s" % e

    else:
        print "INFO: Конфигурация не требуется"

except OSError, e:
    print "ERROR: Не удалось скопировать шаблон: %s " % e

except Exception, e:
    print "ERROR: Ошибка при попытке запуска скрипта: %s" % e

