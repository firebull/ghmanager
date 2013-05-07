#!/usr/bin/env python2
# coding: UTF-8

from zipfile import *
from optparse import OptionParser
from datetime import datetime, date, time
import os

print datetime.now().strftime("%H:%M:%S")

parser = OptionParser()

parser.add_option("-z", "--zip", action="store", type="string", dest="zip")
parser.add_option("-p", "--extr-to", action="store", type="string", dest="path")

(options, args) = parser.parse_args(args=None, values=None)

extractTo = options.path
zip = options.zip

if extractTo and zip:
    if os.path.exists(extractTo):
        if is_zipfile(zip):

            zip = ZipFile(zip, 'r')
            for mapFile in zip.namelist():
                mapFileSplit = os.path.split(mapFile)

                extractToFile = extractTo + '/' + mapFile

                # Имя файла не должно начинаться точками или слэшом
                if not mapFile.startswith('..') and not mapFile.startswith('./') and not mapFile.startswith('/'):
                    print "Распаковываю %s" % mapFile

                    zip.extract(mapFile, extractTo)

                    if os.path.isdir(extractToFile):
                        os.chmod(extractToFile, 0770)

                    elif os.path.isfile(extractToFile):
                        os.chmod(extractToFile, 0660)
                else:
                    print "Файл %s имеет некорректное имя, пропускаю его" % mapFile

            zip.close()

        else:
            raise Exception("Переданный файл не является архивом zip или данный тип архива не поддерживается")
    else:
        raise Exception("Не найден путь для разархивации")
else:
        raise Exception("Не заданы входные параметры")
