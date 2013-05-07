#!/usr/bin/env python
# coding: UTF-8

'''
***********************************************
Lib for game server updater.
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


import sys
from optparse import OptionParser
import httplib
import urllib
import re


def postdata(value, token):
    # params = urllib.urlencode({'@status': 'update', '@statusDesc': value, '@action_token': token, '@errorNum': 0})
    params = urllib.urlencode({'status': 'update', 'statusDesc': value, 'action_token': token, 'errorNum': 0})

    headers = {"Content-type": "application/x-www-form-urlencoded", "Accept": "text/plain"}
    conn = httplib.HTTPSConnection("panel.teamserver.ru")
    conn.request("POST", "/servers/actionStatus/write/update", params, headers)
    # response = conn.getresponse()
    # print response.read()
    # print response.status, response.reason
    return 0


def parse(line):
    result = False
    # Мозг кипит, не соображу как сделать по-человечески =) Делаю по индийски =)
    t = re.match('(^|^\d{1,2}\:\d{1,2}\s+)(?P<stage>\d{1,2}\.\d{1,2})\%\s+downloading.*$', line.strip())
    t2 = re.match('^App.*\s+progress\:\s+(?P<stage>\d{1,2}\.\d{1,2})\s.*$', line.strip())

    if t:
        value = t.group('stage')
        result = True
    # CS:GO
    elif t2:
        value = t2.group('stage')
        result = True
    # Итоговая строка. Также нужно отправлять 100%, если сервер и так обновлен.
    elif re.match('(HLDS installation up to date|Success! App \'\d{2,8}\' fully installed|Success! App \'\d{2,8}\' already up to date)', line.strip()):
        value = '100'
        result = True
    else:
        value = ""
        result = False
    return value, result


def main():
    parser = OptionParser()
    parser.add_option("-t", "--token", action="store", type="string", dest="token")
    options, args = parser.parse_args()
    postdata("0.00", options.token)
    for line in sys.__stdin__:
        print line.strip()  # Вывод строки для записи лога в файл
        value, result = parse(line)
        if result:
            postdata(value, options.token)
            print '  ( Save ' + str(value) + '% progress )'  # Дебаг парсера
    return 0


if __name__ == "__main__":
    main()
