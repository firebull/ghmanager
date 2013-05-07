#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Cleans demos older then 2 weeks.
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
Очистка всех демо, старше двух недель.
Все демо, сташе двух недель, переносить в archieve
Все демо, старше 6 недель - удалять.
'''

import os
from shutil import move
import re
import time
from datetime import datetime

curTime = time.time()
curDate = datetime.now().strftime("%d-%b-%Y")

for root, dirs, filenames in os.walk('/home/'):
    if 'servers' in dirs:
            dirs.remove('servers')  # don't visit servers directories

    if 'logs' in dirs:
            dirs.remove('logs')  # don't visit logs directories

    if re.search('/home/client\d{1,8}/public_html/dems', root, re.I):
        splitPath = root.split('/')
        for file in filenames:
            demo = os.path.join(root, file)
            if re.search('\w*\.zip$', file, re.I):
                demoTime = os.path.getmtime(demo)

                if (curTime - demoTime) >= 3628800:  # Удаляем демки старше шести недель

                    try:
                        print 'DELETE: ' + demo + ' (%s)' % str(time.strftime("%d %b %Y %H:%M", time.gmtime(demoTime)))
                        os.remove(demo)
                        a = 1
                    except OSError, e:
                        print "Не удалось удалить файл:", e

                elif not re.search('/home/client\d{1,8}/public_html/dems/archieve', root, re.I) \
                        and (curTime - demoTime) < 3628800 and (curTime - demoTime) >= 1209600:

                    demoGmTime = time.gmtime(os.path.getmtime(demo))  # Получить время последней записи в файл
                    timeDir = "%s-%s-%s" % (str(demoGmTime.tm_year), str(
                        demoGmTime.tm_mon).zfill(2), str(demoGmTime.tm_mday).zfill(2))
                    toPath = '/home/%s/public_html/dems/archieve/%s/%s' % (splitPath[2], splitPath[5], timeDir)

                    try:
                        print 'MOVE: ' + demo + ' (%s)' % str(time.strftime("%d %b %Y %H:%M", time.gmtime(demoTime)))

                        if not os.path.exists(toPath):
                            os.makedirs(toPath)
                        move(demo, toPath + '/' + file)

                    except OSError, e:
                        print "Не удалось переместить файл:", e

        if not os.listdir(root) and not re.search('^/home/client\d{1,8}/public_html/dems$', root, re.I):
            print 'DELETE: Удаляю пустую директорию: ' + root
            os.rmdir(root)
