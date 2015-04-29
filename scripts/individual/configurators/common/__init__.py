#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Common library for plugins and mods configurators.
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


def addInfoToConfigWithoutCheck(file, text):
    print "Создаю %s" % file
    try:
        if not os.path.exists(file):
            cfg = open(file, "w")
        else:
            cfg = open(file, "a")

        cfg.write(text)
        os.chmod(file, 0640)
        cfg.close()

        return True
    except OSError, e:
        print "Не удалось обновить конфиг из-за ошибки:", e
        return False


'''
Пробная функция, которая проверяет наличие вводимого текста
дабы избежать дублирования

На самом деле, можно тут проводить многострочный поиск,
но может получиться так, что клиент руками удалит или
закоменнтит одну из строк, и тогда будет дубль.
Лучше пройтись по всему тексту и вычистить все остатки.

Если указан параметр treeParam, то искать первое вхождение {
после строки с этим параметром и вносить строки туда.

'''


def addInfoToConfig(file, text, treeParam=False):

    check = text.splitlines()

    try:
        if not os.path.exists(file):
            print "Создаю %s" % file
            cfg = open(file, "w")

            if treeParam != False:
                cfg.write('%s \n{\n    %s \n}\n' % (str(treeParam), text))
            else:
                cfg.write(text)

            cfg.close()
        else:
            print "Конфиг найден, провожу проверку:"
            # Сначала провести поиск
            os.rename(file, file + '.bak')
            cfgCur = open(file + '.bak', 'r')
            cfgNew = open(file, "w")
            lastLine = "\n"

            for line in cfgCur:
                lineFound = False
                lastLine = line
                for checkLine in check:
                    # print "Ищу строку >> " + checkLine
                    if checkLine != '' and re.search(re.escape(checkLine), line):
                        lineFound = True
                        print "Провожу очистку"
                        break
                if lineFound == False:
                    cfgNew.write(line.strip() + '\n')

            print "Вношу параметры"
            if not lastLine.endswith('\n'):
                cfgNew.write('\n')

            '''
            Если указан treeParam, то возможны варианты:
            1) Параметр в конфиге есть и нужно вставить после него
            2) TODO: Параметра в конфиге нет и нужно его создать.
            '''
            if treeParam != False:
                # Снова пройтись по файлу и искать параметр
                cfgCur.close()
                cfgNew.close()

                os.rename(file, file + '.bak.2')
                cfgCur = open(file + '.bak.2', 'r')
                cfgNew = open(file, "w")

                treeParamFound = False
                for line in cfgCur:
                    if line != '' and re.match('\s*' + treeParam, line):
                        # Параметр найден. Теперь искать {
                        treeParamFound = True
                        cfgNew.write(line.strip() + '\n')

                    elif treeParamFound == True and re.match('\s*\{', line):
                        cfgNew.write(line.strip() + '\n')
                        cfgNew.write(text.strip() + '\n')
                        treeParamFound = False
                    else:
                        cfgNew.write(line.strip() + '\n')

                cfgCur.close()
                cfgNew.close()
                os.remove(file + '.bak.2')

            else:
                cfgNew.write(text.strip() + '\n')
                cfgCur.close()
                cfgNew.close()

        os.chmod(file, 0640)

        return True

    except OSError, e:
        print "Не удалось обновить конфиг из-за ошибки:", e
        return False


def setPluginForAmxmodx(serverPath, plugin, neededModules, positionInFile='bottom'):
    pluginsIni = serverPath + "/addons/amxmodx/configs/plugins.ini"
    modulesIni = serverPath + "/addons/amxmodx/configs/modules.ini"

    try:
        # Сначала прописать плагин в конфиг
        os.rename(pluginsIni, pluginsIni + ".bak")

        pluginsIniBak = open(pluginsIni + ".bak")
        pluginsIniTmp = open(pluginsIni + ".tmp", 'w')

        pluginIsSet = False  # Ключ, указывающий прописан или нет плагин в конфиге

        for line in pluginsIniBak:
            if pluginIsSet == False and re.match(";" + plugin, line):  # Поиск закомментированной строки
                pluginsIniTmp.write(line.lstrip(';'))  # Удалить комментарий
                pluginIsSet = True  # Установить ключ, что плагин прописан в конфиге
            elif pluginIsSet == False and re.match(plugin, line):
                pluginsIniTmp.write(line)
                pluginIsSet = True  # Установить ключ, что плагин прописан в конфиге
            else:
                pluginsIniTmp.write(line)

        # Плагины могут прописываться как сверху,так и в конце конфига
        if pluginIsSet == False and positionInFile == 'bottom':
            pluginsIniTmp.write(plugin + "\n")
            pluginsIniTmp.close()
            os.rename(pluginsIni + ".tmp", pluginsIni)

        elif pluginIsSet == False and positionInFile == 'top':
            pluginsIniTmp.close()
            pluginsIniTmp = open(pluginsIni + ".tmp")

            pluginsIniNew = open(pluginsIni, 'w')
            pluginsIniNew.write(plugin + "\n")
            for line in pluginsIniTmp:
                pluginsIniNew.write(line)

            pluginsIniNew.close()
            pluginsIniTmp.close()
            os.remove(pluginsIni + ".tmp")
        elif pluginIsSet == True:
            pluginsIniTmp.close()
            os.rename(pluginsIni + ".tmp", pluginsIni)

        pluginsIniBak.close()

        os.chmod(pluginsIni, 0660)

        if neededModules != None:
            # Теперь включить необходимые плагину модули
            os.rename(modulesIni, modulesIni + ".bak")

            modulesIniBak = open(modulesIni + ".bak")
            modulesIniNew = open(modulesIni, 'w')

            moduleIsSet = False  # Ключ, указывающий прописан или нет модуль в конфиге

            for line in modulesIniBak:
                for module in neededModules:
                    if moduleIsSet == False and re.match(";" + str(module), line):
                        modulesIniNew.write(line.lstrip(';'))
                        moduleIsSet = True
                        continue

                if moduleIsSet == False:
                    modulesIniNew.write(line)

                moduleIsSet = False

            modulesIniBak.close()
            modulesIniNew.close()

            os.chmod(modulesIni, 0660)

        return True

    except OSError, e:
        return e


# Отключение плагинов AmxModx
# plugin - массив
def turnAmxPluginOff(serverPath, plugins):
    pluginsIni = serverPath + "/addons/amxmodx/configs/plugins.ini"

    try:
        # Сначала прописать плагин в конфиг
        os.rename(pluginsIni, pluginsIni + ".bak")

        pluginsIniBak = open(pluginsIni + ".bak")
        pluginsIniTmp = open(pluginsIni, 'w')

        # Перебор всех строк конфига
        for line in pluginsIniBak:
            # И поиск соответвия с плагином
            pluginIsSet = False
            for plugin in plugins:
                if re.match(plugin, line):
                    # Если плагин найден, то закомментировать строку
                    pluginIsSet = True  # Ключ, указывающий прописан или нет плагин в конфиге
                    break

            if pluginIsSet == True:
                pluginsIniTmp.write(';' + line)
            else:
                pluginsIniTmp.write(line)

        pluginsIniBak.close()
        pluginsIniTmp.close()
        os.chmod(pluginsIni, 0660)

        return True

    except OSError, e:
        return e
