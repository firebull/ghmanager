#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Watches for new demos in SRCDS/HLDS dirs, arch them
and move them to separate dirs for quick download.
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


'''
Скрипт для наблюдения за списком директорий,
в которых могут появляться записанные демки.
Как только демка обнаружена, она должна быть
заархивирована и перемещена в отдельную директорию,
из которой её потом могут скачивать.
'''

import pyinotify
import os
import pwd
from zipfile import *
from datetime import datetime, date, time


class demCreatedHandler(pyinotify.ProcessEvent):

    def process_IN_CLOSE_WRITE(self, event):
        if os.path.isfile(event.pathname) and os.path.getsize(event.pathname) > 0 and event.name.endswith('.dem'):
            log = open('/var/log/teamserver/demo-watcher.log', 'a')
            log.write(datetime.now().strftime("%d %B %Y %H:%M:%S") + ">>>\n")
            log.write("Обнаружено новое демо: " + event.pathname + "\n")

            path = event.path.split('/')
            demoName = event.name.split('.dem')
            demoName = demoName[0]
            logPathname = event.path + '/' + demoName + '.log'
            logName = demoName + '.log'
            rootToPath = '/%s/%s/public_html/dems' % (path[1], path[2])

            # Демки складировать в директорию с именем текущей даты
            toPath = '/%s/%s/public_html/dems/%s/%s' % (path[1], path[2], path[4], str(datetime.now().strftime("%Y-%m-%d")))
            log.write("Буду складировать демки в: " + toPath + "\n")
            try:
                # Теперь получим uid и gid
                pw = pwd.getpwnam(path[2])
                apachePw = pwd.getpwnam("wwwrun")
                userUid = pw.pw_uid
                apacheGid = apachePw.pw_gid

                if not os.path.exists(toPath):
                    os.makedirs(toPath)
                    # Дать права на чтение Апаче
                    os.chown(toPath, userUid, apacheGid)
                    os.chmod(toPath, 0750)
                    os.chown(rootToPath, userUid, apacheGid)
                    os.chmod(rootToPath, 0750)

                '''
                Раньше этой директории присваивались права рута. И теперь
                клиент ничего не может сам туда записать.
                Поэтому постепенно пусть скрипт по записи новых демо
                и выставит правильно права.
                '''
                toServerPath = '/%s/%s/public_html/dems/%s' % (path[1], path[2], path[4])
                os.chown(toServerPath, userUid, apacheGid)
                os.chmod(toServerPath, 0750)

                #

                toFile = toPath + '/' + event.name + '.zip'
                zipTo = ZipFile(toFile, 'w', ZIP_DEFLATED)
                zipTo.write(event.pathname, event.name)

                # Также добавить и лог демки
                if os.path.exists(logPathname):
                    zipTo.write(logPathname, logName)

                zipTo.close()
                log.write("Демо скопировано в архив: " + str(toFile) + "\n")
                # Дать права на чтение Апаче
                os.chmod(toFile, 0640)
                os.chown(toFile, userUid, apacheGid)

                # удалить оригинал в конце,
                # т.к. если ошибка случится выше,
                # исходная демка будет жива
                os.remove(event.pathname)
                log.write("Оригинал удален: " + event.pathname + "\n")
                # Удалить лог демки
                if os.path.exists(logPathname):
                    os.remove(logPathname)
                log.close()

            except OSError, e:
                    print "Команда завершилась неудачей:", e


if not os.path.exists('/var/log/teamserver/demo-watcher.log'):
    log = open('/var/log/teamserver/demo-watcher.log', 'w')
else:
    log = open('/var/log/teamserver/demo-watcher.log', 'a')

log.write("Запуск в: " + datetime.now().strftime("%d %B %Y %H:%M:%S") + ">>>" + "\n")
wm = pyinotify.WatchManager()
# notifier
notifier = pyinotify.Notifier(wm, demCreatedHandler())

for line in open('/images/scripts/global/watchForDems/dirs.lst'):
    dir = line.strip(' \n\r')
    if os.path.exists(dir):
        log.write("adding dir: " + dir + "\n")
        wm.add_watch(dir, pyinotify.IN_CLOSE_WRITE, rec=False)

log.close()

notifier.loop()
