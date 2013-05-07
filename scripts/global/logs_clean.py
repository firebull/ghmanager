#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Deletes game servers logs older then 2 weeks.
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
Очистка всех логов, старше двух недель.
Исключение составляет games_mp.log у COD2/4,
его переименовываем и сохраняем с расширением bak
'''

import os
import re
import time
from datetime import datetime

curTime = time.time()
curDate = datetime.now().strftime("%d-%b-%Y")

for root, dirs, filenames in os.walk('/home/'):
    for file in filenames:
        log = os.path.join(root, file)
        if re.search('\w*\.log$', file, re.I):
            logTime = os.path.getmtime(log)

            if (curTime - logTime) >= 1209600:  # Удаляем логи старше двух недель
                if re.search('games_mp\.log$', file, re.I):
                    bakLogName = 'games_mp_' + curDate + '.log.bak'
                    bakLog = os.path.join(root, bakLogName)
                    os.rename(log, bakLog)
                else:
                    try:
                        print log + ' (%s)' % str(time.strftime("%d %b %Y %H:%M", time.gmtime(logTime)))
                        os.remove(log)
                    except OSError, e:
                        print "Не удалось удалить файл:", e
