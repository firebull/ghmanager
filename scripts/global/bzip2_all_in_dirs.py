#!/usr/bin/env python2
# coding: UTF-8

'''
Скрипт, который архивирует все файлы в указанной директории,
включая во вложенных. Нужен, прежде всего, для архивирования
карт для FastDL
'''

import os, re, bz2
from optparse import OptionParser
from datetime import datetime, date, time

parser = OptionParser()

parser.add_option("-d", "--dir",  action="store", type="string", dest="dir")

(options, args) = parser.parse_args(args=None, values=None)

dir     = options.dir

print datetime.now().strftime("%A, %d. %B %Y %H:%M:%S%p")

for root, dirs, filenames in os.walk(dir):
    for file in filenames:
        if not re.match('^.*(bz2|jpg|gif|png)$', file, re.I):
            fileToArch = os.path.join(root, file)
            data = open(fileToArch, 'r').read()
            output = bz2.BZ2File(fileToArch + ".bz2", 'wb', compresslevel=5)
            try:
                print datetime.now().strftime("%H:%M:%S") + ": Архивирую " + fileToArch 
                output.write(data)
                print "          Успешно"
            finally:
                output.close()
                try:
                    print "          Удаляю исходный"
                    os.remove(fileToArch)
                except OSError, e:
                    print "Не удалось удалить исходный файл: ", e
                
print datetime.now().strftime("%A, %d. %B %Y %H:%M:%S%p")
                