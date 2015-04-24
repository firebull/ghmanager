#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Common library for configurator scripts.
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

import os
import sys
import re
import pwd
import time
import tempfile
from string import letters
from glob import glob
from random import choice
from datetime import datetime, date
from zipfile import *
import gettext
import json

gettext.install('ghmanager', '/images/scripts/i18n', unicode=1)

def xmlLog(message, level='log'):
    print '<' + level + '>' + str(message) + '</' + level + '>'


def readAndSetParamFromConfig(param, value, desc, config, path, action='read', delim='space'):
    '''
    delim - param separator between name and value
    space - пробел, значение в кавычках, комментарии //
    eq - знак =, значение без кавычек, комментарии ;
    '''

    configWithPath = os.path.join(path, config)
    # Log array for JSON output


    if action == 'read':
        process = {'error' : [], 'log' : [], 'data' : {'paramValue': None}}

        process['log'] += ['INFO: ' + _('Trying to read param "%s" from config "%s/%s"') % (param, path, config)]

        if os.path.exists(configWithPath):
            try:
                r = open(configWithPath, 'r')

                for line in r:
                    if re.match(param, line, flags=re.IGNORECASE):

                        #xmlLog('Строка с параметром найдена')
                        process['log'] += ['OK: ' + _('String with param is found')]
                        # выдергиваем параметр из строки вида sv_password "pass" или GamePassword=pass
                        if delim != 'eq':
                            s = re.findall('(?:' + param + ')\s+(?:\"|\')?(.*)(?:\"|\')', line, flags=re.IGNORECASE)
                        else:
                            s = re.findall('(?:' + param + ')=(\S*)\s+?(?://|;)?(?:.*)', line, flags=re.IGNORECASE)

                        if s == '':
                            #xmlLog('Строка найдена, но параметр задан некорректно', warn)
                            process['log'] += ['WARN: ' + _('String is found, but parameter is not set correctly')]
                            r.close()
                            break
                        else:
                            #xmlLog(s[0], 'paramValue')
                            process['data']['paramValue'] = s[0]
                            r.close()
                            print json.dumps(process)
                            return True
                            break

                #xmlLog('Строка с параметром не найдена')
                process['log'] += ['INFO: ' + _('String with param NOT found')]
                r.close()


            except EnvironmentError, e:
                process['log']   += ["ERROR: " + _("While running the script an error occured: %s") % e]
                process['error'] += [_("While running the script an error occured. Read log.")]


        else:
            process['log'] += ['ERROR: ' + _('Config NOT found')]
            #xmlLog('Конфига не существует', 'error')
            #

        print json.dumps(process)
        return False

    elif action == 'write':
        '''
        Алгоритм записи параметра:
        - Создать временный файл
        - Открыть конфиг и считывать из него построчно во временный файл
        - Если найден параметр в любом виде, в том числе и закомментированном,
          то заменить эту строку на строку с нужным значением параметра и описанием desc
        - Если найдена строка еще раз или даже несколько - пропускать
        - Если строка не найдена, то закрыть конфиг и открыть его на запись.
          В первой строке записать нужный параметр и следом перенести все из временного файла
        '''
        process = {'error' : [], 'log' : [], 'data' : ''}

        # Отформатируем комментарий
        if desc != None and desc != '' and desc != 'None':
            desc = '// ' + desc
        else:
            desc = ''

        try:
            error = False
            if os.path.exists(configWithPath):
                tmp = tempfile.TemporaryFile()

                r = open(configWithPath, 'r')

                paramIsFound = False

                for line in r:
                    if re.search(param, line, flags=re.IGNORECASE):
                        process['log'] += ['OK: ' + _('String with param is found')]
                        if paramIsFound == False:
                            if delim != 'eq':
                                tmp.write('%s "%s" %s\n' % (param, value, desc))
                            else:
                                tmp.write('%s=%s\n' % (param, value))

                            paramIsFound = True  # Param is found, bypass other strings with it
                    else:
                        tmp.write(line.strip() + '\n')

                r.close()

                w = open(configWithPath, 'w')

                if paramIsFound == False:
                    # Если параметр не найден, то сначала в первую строку конфига записать его
                    # Для конфигов стиля param=value нельзя писать в начало, потому пока просто блокирую
                    if delim != 'eq':
                        process['log'] += ['INFO: ' + _('String with param is not found, will write it to the first line')]
                        #xmlLog('Параметр найден не был. Записываю его в начало конфига.', 'log')
                        w.write('%s "%s" %s\n' % (param, value, desc))
                    else:
                        error = True
                        process['log'] += ['ERROR: ' + _('Param is not found, dont know, where to write it')]
                        #xmlLog('Параметр найден не был. Не знаю, куда записать параметр.', 'error')

                tmp.seek(0)
                for line in tmp:
                    w.write(line)

                w.close()
                tmp.close()

            else:
                if delim != 'eq':
                    process['log'] += ['WARN: ' + _('Config is NOT found, create new one')]
                    #xmlLog('Конфиг не найден, создаю пустой', 'warn')
                    w = open(configWithPath, 'w')
                    w.write('%s "%s" %s\n' % (param, value, desc))
                    w.close()
                else:
                    error = True
                    process['log'] += ['ERROR: ' + _('Config is not found, dont know, where to write param')]
                    #xmlLog('Конфиг не найден. Не знаю, куда записать параметр.', 'error')

            if error == False:
                process['data'] = 'success'
                process['log'] += ['OK: ' + _('Param is written to config successfully')]
            else:
                process['data'] = 'error'
                process['log'] += ['ERROR: ' + _('Could not write param to config')]

            #xmlLog('Параметр успешно записан в конфиг.', 'log')
            print json.dumps(process)
            return True

        except EnvironmentError, e:
            process['error'] += ['ERROR: %s' % e]
            print json.dumps(process)
            return False



def removeParamFromConfig(config, param):

    # Экранировать вероятные $
    param = re.sub('\$', '\\\$', param)

    if os.path.exists(config):
        try:
            os.rename(config, config + '.bak')

            configOld = open(config + '.bak', 'r')
            configNew = open(config, 'w')

            for line in configOld:
                if not re.match('^' + param + '\s*', line, re.I):
                    configNew.write(line.strip() + '\n')

            configOld.close()
            configNew.close()

            xmlLog('Параметр удалён успешно')
            return True

        except OSError, e:
            xmlLog("При удалении параметра возникла ошибка: " + e, 'error')
            return False

    else:
        xmlLog('Конфиг не найден', 'warn')
        return False



