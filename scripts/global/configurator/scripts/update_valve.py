#!/usr/bin/env python
# coding: UTF-8

'''
***********************************************
SRCDS/HLDS update main script.
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


from optparse import OptionParser
import subprocess
import shlex


def main():
    parser = OptionParser()
    parser.add_option("-c", "--command", action="store", type="string", dest="command")
    parser.add_option("-t", "--token", action="store", type="string", dest="token")
    options, args = parser.parse_args()
    # print options.command
    mycommandstr = options.command + " | /home/configurator/public_html/scripts/filter.py --token=" + options.token
    # print mycommandstr
    args = shlex.split(mycommandstr)
    # print args
    # p = subprocess.Popen(args, bufsize=1, stdout=subprocess.PIPE, stderr=subprocess.STDOUT)
    subprocess.call(mycommandstr, shell=True)
    # p.communicate()
    return 0


if __name__ == "__main__":
    main()
