#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Map install to client's server script.
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

import distutils.dir_util
import os
import sys
import re
from datetime import datetime   # , date, time
from subprocess import *
from shutil import *
from optparse import OptionParser
from commonLib import xmlLog, removeParamFromConfig
sys.path.append("/images/scripts/individual/configurators")
from common import addInfoToConfig


# Внесение карты в конфиги для Source
def valveMapInsert(path, mapName, game='css'):

    mapList = path + '/maplist.txt'
    mapCycle = path + '/mapcycle.txt'

    if game in ['cs16', 'cs16-old']:
        serverCfg = path + '/server.cfg'
    else:
        serverCfg = path + '/cfg/server.cfg'

    try:
        # Добавить карты в конфиги
        if game not in ['cs16', 'cs16-old']:
            if (addInfoToConfig(mapList, mapName) == True):
                xmlLog('Карта успешно добавлена в maplist.txt')
            else:
                xmlLog('Возникла ошибка при добавлении карты в maplist.txt', 'error')

        if (addInfoToConfig(mapCycle, mapName) == True):
            xmlLog('Карта успешно добавлена в mapcycle.txt')
        else:
            xmlLog('Возникла ошибка при добавлении карты в mapcycle.txt', 'error')

        if (addInfoToConfig(serverCfg, 'sv_downloadurl "http://fdl1.teamserver.ru/fastdl/%s/"' % game) == True):
            xmlLog('FastDL успешно прописан в server.cfg')
        else:
            xmlLog('Возникла ошибка при добавлении FastDL в server.cfg', 'error')

        return True

    except OSError, e:
        xmlLog("Не удалось установить карту из-за ошибки: " + e, 'error')
        return False


# Удаление карты из конфигов для Source
def valveMapRemove(path, mapName, game='css'):

    mapList = path + '/maplist.txt'
    mapCycle = path + '/mapcycle.txt'
    try:
        # Удалить карту из конфигов
        if game != 'cs16':
            if (removeParamFromConfig(mapList, mapName) == True):
                xmlLog('Карта успешно удалена из maplist.txt')
            else:
                xmlLog('Возникла ошибка при удалении карты из maplist.txt', 'error')

        if (removeParamFromConfig(mapCycle, mapName) == True):
            xmlLog('Карта успешно удалена из mapcycle.txt')
        else:
            xmlLog('Возникла ошибка при удалении карты из mapcycle.txt', 'error')

        return True

    except OSError, e:
        xmlLog("Не удалось удалить карту из-за ошибки: " + e, 'error')
        return False


#

xmlLog(datetime.now().strftime("%A, %d. %B %Y %H:%M:%S%p"))
parser = OptionParser()

parser.add_option("-a", "--action",      action="store", type="string", dest="action")
parser.add_option("-s", "--serverpath",  action="store", type="string", dest="serverPath")
parser.add_option("-m", "--mappath",     action="store", type="string", dest="mapPath")
parser.add_option("-n", "--name",        action="store", type="string", dest="mapName")
parser.add_option("-i", "--installpath", action="store", type="string", dest="installPath")
parser.add_option("-t", "--template",    action="store", type="string", dest="gameTemplate")

(options, args) = parser.parse_args(args=None, values=None)

action = options.action
mapPath = re.sub('(\.{1,2}/)', '', options.mapPath)
serverPath = re.sub('(\.{1,2}/)', '', options.serverPath)
mapName = options.mapName
installPath = re.sub('(\.{1,2}/)', '', options.installPath)
gameTemplate = options.gameTemplate


installTo = os.path.join(serverPath, installPath)

if action == 'install':
    xmlLog('Попытка установить карту: ' + mapPath)
    xmlLog('Путь для установки карты: ' + installTo)

    try:
        distutils.dir_util.copy_tree(mapPath, installTo, preserve_symlinks=1)
        xmlLog("Копирование карты успешно завершено.")

        # В зависимости от игры, могут потребоваться дополнительные действия
        if gameTemplate in ('css', 'cssv34', 'tf', 'dods', 'hl2mp', 'cs16', 'cs16-old'):
            valveMapInsert(installTo, mapName, gameTemplate)

    except OSError, e:
        xmlLog("Не удалось установить карту из-за ошибки:" + e, 'error')

    '''
    Алгоритм схожий с удалением плагинов
    Сканировать директорию с картой и удалять аналогичный файл на сервере.
    Потом удалить строки из конфигов
    '''
elif action == 'delete':

    xmlLog('Попытка удалить карту ' + mapName)

    try:
        for root, dirs, files in os.walk(mapPath, topdown=True):

            subTree = root.split(mapPath)
            for file in files:
                dest = os.path.join(installTo, subTree[1].lstrip('/'), file)
                try:
                    if os.path.exists(dest):
                        xmlLog('Удаляю файл: ' + subTree[1] + '/' + file)
                        os.remove(dest)
                    else:
                        xmlLog('Файл %s не найден' % dest, 'warn')
                except OSError, e:
                    xmlLog("Не удалось удалить файл из-за ошибки: " + e, 'error')

        xmlLog('Подчищаю конфиги')

        # В зависимости от игры, могут потребоваться дополнительные действия
        if gameTemplate in ('css', 'cssv34', 'tf', 'dods', 'hl2mp', 'cs16', 'cs16-old'):

            valveMapRemove(installTo, mapName, gameTemplate)

    except OSError, e:
        xmlLog("Не удалось удалить карту из-за ошибки: " + e, 'error')

    '''
    Карта может быть установлена (наличие файлов), но отключена в конфиге
    '''

elif action == 'turnOn':

    xmlLog('Попытка включить карту ' + mapName)
    # В зависимости от игры, карта включается в разных конфигах

    if gameTemplate in ('css', 'cssv34', 'tf', 'dods', 'hl2mp', 'cs16', 'cs16-old'):

        valveMapInsert(installTo, mapName, gameTemplate)

    '''
    Карта может быть установлена (наличие файлов), но удалять её нельзя, только отключить
    '''
elif action == 'turnOff':

    xmlLog('Попытка отключить карту ' + mapName)
    # В зависимости от игры, карта отключается в разных конфигах

    if gameTemplate in ('css', 'cssv34', 'tf', 'dods', 'hl2mp', 'cs16', 'cs16-old'):

        valveMapRemove(installTo, mapName, gameTemplate)
