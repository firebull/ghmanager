#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Dir list script.
Executes with clients rights.
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
from datetime import datetime, date, time
from optparse import OptionParser

parser = OptionParser()
parser.add_option("-p", "--path", action="store", type="string", dest="path")

(options, args) = parser.parse_args(args=None, values=None)
path = options.path

if os.path.exists(path):
    print '<list>'
    for root, dirs, files in os.walk(path, topdown=True):
        for dir in dirs:
            print '<dir>' + dir + '</dir>'
        break
    print '</list>'
else:
    print '<error>Путь не найден либо нет прав доступа.</error>'
