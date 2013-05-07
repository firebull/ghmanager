#!/usr/bin/env python2.7
# coding: UTF-8

'''
***********************************************
Get attack info from IPTABLES log.
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


import redis
import os
import re
import sys
import ConfigParser
from flock import flock
from time import time, mktime, strptime
from datetime import datetime, date

print datetime.now().strftime("%A, %d. %B %Y %H:%M:%S")

config = ConfigParser.RawConfigParser()
config.read('/etc/hosting/scripts.cfg')

# ID сервера, на котором пускается скрипт
# Брать его из поля id в админке

thisServerId = config.getint('server', 'serverID')

r = redis.Redis(host='',
                port=6379,
                db=10,
                password='')

logPath = '/var/log/firewall'

lastLogPos = r.get('lastlogpos:' + str(thisServerId))
lastLogSize = r.get('lastlogsize:' + str(thisServerId))

if lastLogPos:
    lastLogPos = int(lastLogPos)
else:
    lastLogPos = 0

if lastLogSize:
    lastLogSize = int(lastLogSize)
else:
    lastLogSize = 0


lastAttackerIp = ''
lastAttackedIp = ''
lastAttackedPort = 0
lastAttackRule = ''

# Blocking the process
lock = flock('iptables_log_checker.lock', True).acquire()

if lock:
    if os.path.exists(logPath):
        log = open(logPath, 'r')

        if os.path.getsize(logPath) > lastLogSize:
            if lastLogPos > 0:
                log.seek(lastLogPos)

        elif os.path.getsize(logPath) == lastLogSize:
            print "Новых записей нет."
            sys.exit(0)

        j = 0
        pipe = r.pipeline()

        for i, line in enumerate(log):
            s = re.match(
                '(?P<time>.{,3}\s+\d{1,2}\s+\d{,2}\:\d{,2}\:\d{,2})\s.*(?P<rule>(?:UDP-FLOOD|TCP-FLOOD|SRCDS-FLOOD|SAMP-FLOOD))\:.*SRC\=(?P<src>\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})\s+DST\=(?P<dst>\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})\s+.*DPT\=(?P<dpt>\d{1,6})', line)

            if s:
                # Не пишем в базу повторяющиеся строки
                if not (lastAttackerIp == s.group('src') and
                        lastAttackedIp == s.group('dst') and
                        lastAttackedPort == int(s.group('dpt')) and
                        lastAttackRule == s.group('rule')):

                    j += 1
                    lastAttackerIp = s.group('src')
                    lastAttackedIp = s.group('dst')
                    lastAttackedPort = int(s.group('dpt'))
                    lastAttackRule = s.group('rule')

                    # Добавить год во время лога
                    curDate = date.today()
                    logDate = strptime(s.group('time'), "%b %d %H:%M:%S")

                    if curDate.month == 1 and logDate.tm_mon == 12:
                        trueLogDate = str(curDate.year - 1) + " " + str(s.group('time'))
                    else:
                        trueLogDate = str(curDate.year) + " " + str(s.group('time'))

                    key = str(r.incr('attackerActionId'))

                    pipe.rpush('log:' + key, abs(int(mktime(strptime(trueLogDate, "%Y %b %d %H:%M:%S")))))
                    pipe.rpush('log:' + key, lastAttackerIp)
                    pipe.rpush('log:' + key, lastAttackedIp + ':' + str(lastAttackedPort))
                    pipe.rpush('log:' + key, lastAttackRule)
                    pipe.expire('log:' + key, 2592000)

                    pipe.rpush('src:' + lastAttackerIp, key)
                    pipe.expire('src:' + lastAttackerIp, int(time()) + 2592000)

                    pipe.rpush('dst:' + lastAttackedIp + ':' + str(lastAttackedPort), key)
                    pipe.expire('dst:' + lastAttackedIp + ':' + str(lastAttackedPort), int(time()) + 2592000)

        if j > 0 and pipe.execute():
            print 'Добавлено в базу %s записей' % str(j)
            r.set('lastlogpos:' + str(thisServerId), log.tell())
            r.set('lastlogsize:' + str(thisServerId), os.path.getsize(logPath))
        elif j == 0:
            print 'Нет новых записей'
        else:
            print 'Ошибка при добавлении новых записей в базу'

        log.close()
