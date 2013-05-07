#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
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
import sys
import pwd

sys.path.append("/images/scripts/global")
from common import *
from optparse import OptionParser


parser = OptionParser()

parser.add_option("-a", "--addonsPath", action="store", type="string", dest="addonsPath")
parser.add_option("-s", "--serverPath", action="store", type="string", dest="serverPath")
parser.add_option("-u", "--userName", action="store", type="string", dest="userName")

(options, args) = parser.parse_args(args=None, values=None)

serverPath = options.serverPath
addonsPath = options.addonsPath
userName = options.userName


try:
    print "Установка прав запуска"
    checkPermsDirs = [serverPath, serverPath + '/' + addonsPath]
    pw = pwd.getpwnam(userName)
    userUid = pw.pw_uid
    userGid = pw.pw_gid

    for checkDir in checkPermsDirs:
        r = setExecFileOwner(userUid, userGid, checkDir)
        if r != True:
            raise r
        # debug
        # else:
            # print r

except OSError, e:
    print "При попытке установки прав на запускаемые файлы сервера возникла ошибка: ", e
