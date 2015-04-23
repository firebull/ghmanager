#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Executes with clients rights.
Copyright (C) 2015 Nikita Bulaev

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
from common import readAndSetParamFromConfig, xmlLog
import json

gettext.install('ghmanager', '/images/scripts/i18n', unicode=1)

parser = OptionParser()
parser.add_option("-i", "--param",  action="store", type="string", dest="param")
parser.add_option("-v", "--value",  action="store", type="string", dest="value")
parser.add_option("-d", "--desc",   action="store", type="string", dest="description")
parser.add_option("-c", "--config", action="store", type="string", dest="config_name")
parser.add_option("-p", "--path",   action="store", type="string", dest="config_path")
parser.add_option("-a", "--action", action="store", type="string", dest="action")
parser.add_option("-w", "--delim",  action="store", type="string", dest="delim")
parser.add_option("-l", "--lang",    action="store", type="string", dest="lang")

(options, args) = parser.parse_args(args=None, values=None)
param = options.param.strip("'")
value = options.value.strip("'")
desc = options.description.strip("'")
config = options.config_name.strip("'")
path = options.config_path.strip("'")
action = options.action.strip("'")
delim = options.delim


try:
    readAndSetParamFromConfig(param, value, desc, config, path, action, delim)
except Exception, e:
    process = {'error' : [], 'log' : []}
    process['log']   += ["ERROR: " + _("While running the script an error occured: %s") % e]
    process['error'] += [_("While running the script an error occured. Read log.")]
    print json.dumps(process)



