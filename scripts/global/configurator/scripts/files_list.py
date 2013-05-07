#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Files list script.
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
import re
from datetime import datetime, date, time
from optparse import OptionParser

parser = OptionParser()
parser.add_option("-p", "--path", action="store", type="string", dest="path")
parser.add_option("-e", "--extension", action="store", type="string", dest="ext")
parser.add_option("-m", "--mask", action="store", type="string", dest="mask")
parser.add_option("-w", "--walkdirs", action="store", type="string", dest="walkdirs")

(options, args) = parser.parse_args(args=None, values=None)
path = options.path
ext = options.ext
mask = options.mask
walkdirs = options.walkdirs

if mask == '*':
    mask = '\w*'

# print '<log>Маска >>' + options.mask + '</log>'
if os.path.exists(path):
    print '<list>'
    for root, dirs, files in os.walk(path, topdown=True):

        if walkdirs == 'yes':
            roots = re.split(path, root)
            for file in files:
                if re.search(mask + '\.' + ext + '$', file):
                    print '<file>' + os.path.join(roots[1], file) + '</file>'
        else:
            for file in files:
                if re.search(mask + '\.' + ext + '$', file):
                    print '<file>' + file + '</file>'
        break
    print '</list>'
else:
    print '<error>Путь не найден либо нет прав доступа.</error>'
