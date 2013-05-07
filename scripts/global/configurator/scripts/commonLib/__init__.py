#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Common library for configurator scripts.
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
import re
import pwd
import time
import tempfile
from string import letters
from glob import glob
from random import choice
from datetime import datetime, date
from zipfile import *


def xmlLog(message, level='log'):
    print '<' + level + '>' + str(message) + '</' + level + '>'


def readAndSetParamFromConfig(param, value, desc, config, path, action='read', delim='space'):
    '''
    delim - разделитель параметра и его значения в конфиге
    space - пробел, значение в кавычках, комментарии //
    eq - знак =, значение без кавычек, комментарии ;
    '''

    configWithPath = os.path.join(path, config)

    if action == 'read':
        xmlLog('Попытка прочесть параметр ' + param + ' из конфига ' + config)
        if os.path.exists(configWithPath):
            try:
                r = open(configWithPath, 'r')

                for line in r:
                    if re.match(param, line, flags=re.IGNORECASE):
                        xmlLog('Строка с параметром найдена')
                        # выдергиваем параметр из строки вида sv_password "pass" или GamePassword=pass
                        if delim != 'eq':
                            s = re.findall('(?:' + param + ')\s+(?:\"|\')?(.*)(?:\"|\')', line, flags=re.IGNORECASE)
                        else:
                            s = re.findall('(?:' + param + ')=(\S*)\s+?(?://|;)?(?:.*)', line, flags=re.IGNORECASE)

                        if s == '':
                            xmlLog('Строка найдена, но параметр задан некорректно', warn)
                            r.close()
                            return False
                        else:
                            xmlLog(s[0], 'paramValue')
                            r.close()
                            return True
                            break

                xmlLog('Строка с параметром не найдена')
                r.close()
                return False

            except EnvironmentError, e:
                xmlLog(str(e), 'error')
                return False
        else:
            xmlLog('Конфига не существует', 'error')
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

        # Отформатируем комментарий
        if desc != None and desc != '' and desc != 'None':
            desc = '// ' + desc
        else:
            desc = ''

        try:
            if os.path.exists(configWithPath):
                tmp = tempfile.TemporaryFile()

                r = open(configWithPath, 'r')

                paramIsFound = False

                for line in r:
                    if re.search(param, line, flags=re.IGNORECASE):
                        xmlLog('Строка с параметром найдена')
                        if paramIsFound == False:
                            if delim != 'eq':
                                tmp.write('%s "%s" %s\n' % (param, value, desc))
                            else:
                                tmp.write('%s=%s\n' % (param, value))

                            paramIsFound = True  # Параметр найден, дальше пропускаем все строки с ним
                    else:
                        tmp.write(line.strip() + '\n')

                r.close()

                w = open(configWithPath, 'w')

                if paramIsFound == False:
                    # Если параметр не найден, то сначала в первую строку конфига записать его
                    # Для конфигов стиля param=value нельзя писать в начало, потому пока просто блокирую
                    if delim != 'eq':
                        xmlLog('Параметр найден не был. Записываю его в начало конфига.', 'log')
                        w.write('%s "%s" %s\n' % (param, value, desc))
                    else:
                        xmlLog('Параметр найден не был. Не знаю, куда записать параметр.', 'error')

                tmp.seek(0)
                for line in tmp:
                    w.write(line)

                w.close()
                tmp.close()

            else:
                if delim != 'eq':
                    xmlLog('Конфиг не найден, создаю пустой', 'warn')
                    w = open(configWithPath, 'w')
                    w.write('%s "%s" %s\n' % (param, value, desc))
                    w.close()
                else:
                    xmlLog('Конфиг не найден. Не знаю, куда записать параметр.', 'error')

            xmlLog('Параметр успешно записан в конфиг.', 'log')
            return True

        except EnvironmentError, e:
            xmlLog(str(e), 'error')
            return False


def screenLogRotate(serverRunPath):
    # Если есть screenlog.0 в корне сервера,
    # провести его ротацию, чтобы при следующем запуске
    # в режиме ротации, лог писался в другой файл.
    if os.path.exists(serverRunPath + '/screenlog.0'):
        screenLogsList = glob(serverRunPath + '/screenlog.*')

        # Если логов уже несколько, произвести ротацию всех
        screenLogsNum = len(screenLogsList)
        try:
            # Если логов больше 9, то удалить самый старый
            if (screenLogsNum > 9):
                os.remove(screenLogsList[screenLogsNum - 1])
                screenLogsNum = screenLogsNum - 1

            # Сдвинуть индексы всех на единицу
            while screenLogsNum > 0:
                os.rename(screenLogsList[screenLogsNum - 1], serverRunPath + '/screenlog.' + str(screenLogsNum))
                screenLogsNum = screenLogsNum - 1

            return True

        except OSError, e:
            print "При ротации логов SCREEN возникла ошибка", e

            return False
    else:

        return True


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


'''
Добавить админа в Maniadmin
Из конфига вычленить всех админов и группы
Создать новый, записать туда старых админов и нового,
дописать группы
'''


def addAdminToMani(config, admin, adminType):

    foundPlayers = False
    missBracket = False

    if adminType == 'steam':
        adminMani = '''
                "steam" "%s"
                    ''' % admin

    elif adminType == 'ip':
        adminMani = '''
                "ip"
                {
                    "IP1" "%s"
                }
                    ''' % admin

    elif adminType == 'userPass':

        userPass = re.split('"\s+"', admin.strip())

        adminMani = '''
                "nickname" "%s"
                "password" "%s"
                    ''' % (userPass[0].strip('"'), userPass[1].strip('"'))

    else:
        return False

    adminNameRand = ''.join([choice(letters) for i in range(5)])

    adminText = '''
            "%s"
            {
                "name" "%s"
                %s
                // Полные права доступа и иммунитет
                "flags"
                {
                    "Immunity"    "grav ping afk a b c d e f h i k l m n o p q r s t u v w x y"
                    "Immunity"    "autojoin"
                    "Admin"    "q2 q3 grav pban A B C D E F G H I J K L M N O P Q R S T U V"
                    "Admin"    "W X Y Z a b c d e f g i k l m o p q r s t v w x y z client"
                    "Admin"    "admin spray"
                }
            }
                ''' % ('Admin#' + adminNameRand, adminNameRand, adminMani)

    if os.path.exists(config):
        try:
            os.rename(config, config + '.bak')
            configOld = open(config + '.bak', 'r')

            for line in configOld:
                line = line.strip()

                if re.match('\"players\"', line):
                    foundPlayers = True
                    break

            configOld.seek(0)
            configNew = open(config, 'w')

            if foundPlayers == True:

                for line in configOld:
                    if re.match('^\"players\"$', line.strip()):
                        configNew.write(line)
                        configNew.write('        {\n')
                        configNew.write(adminText)
                        missBracket = True
                    elif re.match('{', line.strip()) and missBracket == True:
                        missBracket = False
                    else:
                        configNew.write(line)

            else:
                for line in configOld:
                    if re.match('\"version\"', line.strip()):
                        configNew.write(line)
                        configNew.write('\n"players"\n{\n%s}' % adminText)
                    else:
                        configNew.write(line)

            configOld.close()
            configNew.close()

        except OSError, e:
            xmlLog("При сохранении параметра возникла ошибка: " + e, 'error')
            return False
    else:
        xmlLog('Конфиг не найден', 'warn')
        return False


'''
    Архивирование демок
    dem    - имя и путь демки
'''


def demoZip(dem):

    try:

        pathToDemo = os.path.split(dem)  # Отделить путь и файл
        demo = pathToDemo[1]             # имя файла демки включая расширение
        demoName = demo.split('.dem')    # Имя демки
        demoName = demoName[0]
        logPathname = pathToDemo[0] + '/' + demoName + '.log'  # Имя файла лога демки, включая путь
        logName = demoName + '.log'     # Имя файла лога демки без пути

        path = pathToDemo[0].split('/')    # Разбить путь на отдельные директории

        rootToPath = '/%s/%s/public_html/dems' % (path[1], path[2])  # Куда складировать

        demTime = time.gmtime(os.path.getctime(dem))  # Получить время последней записи в файл
        timeDir = "%s-%s-%s" % (str(demTime.tm_year), str(demTime.tm_mon).zfill(2), str(demTime.tm_mday).zfill(2))

        # Демки складировать в директорию с именем текущей даты
        toPath = '/%s/%s/public_html/dems/%s/%s' % (path[1], path[2], path[4], timeDir)

        # Теперь получим uid и gid
        # pw = pwd.getpwnam(path[2])
        # apachePw = pwd.getpwnam("wwwrun")
        # userUid  = pw.pw_uid
        # apacheGid = apachePw.pw_gid

        if not os.path.exists(toPath):
            os.makedirs(toPath)
            # Дать права на чтение Апаче
            # TODO: Если скрипт стартует из под рута, то права добавить толлько 750
            #       и привязать к группе апачи
            # os.chown(toPath,userUid,apacheGid)
            os.chmod(toPath, 0755)
            # os.chown(rootToPath,userUid,apacheGid)
            os.chmod(rootToPath, 0755)

        # Есть шанс, что к этому времени автоматический скрипт уже перекинет демку.
        if not os.path.exists(dem):
            print 'Указанный файл не обнаружен, вероятно уже перенесен.'
            return False

        toFile = toPath + '/' + demoName + '.zip'
        zipTo = ZipFile(toFile, 'w', ZIP_DEFLATED)
        zipTo.write(dem, demo)

        # Также добавить и лог демки
        if os.path.exists(logPathname):
            zipTo.write(logPathname, logName)

        zipTo.close()

        # Дать права на чтение Апаче
        os.chmod(toFile, 0644)
        # os.chown(toFile, userUid, apacheGid)

        # удалить оригинал в конце,
        # т.к. если ошибка случится выше,
        # исходная демка будет жива
        os.remove(dem)

        # Удалить лог демки
        if os.path.exists(logPathname):
            os.remove(logPathname)

        return True

    except OSError, e:
        print "Команда завершилась неудачей:", e
        return False
