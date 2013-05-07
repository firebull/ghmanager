#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Main initial script which creates ang configurate game or voice server.
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

import MySQLdb
import os
import stat
import shlex
import sys
import pwd
import pexpect
sys.path.append("/images/scripts/global")
from db_queries import *
from common import *
from subprocess import *
from shutil import *
from time import sleep
from flock import flock
from datetime import datetime, date, time
import ConfigParser


def typeBasePort(type, template):
    if type == 1 or type == 5:  # srcds или hlds
        return '27016'
    elif type == 2:  # voice
        if template == 'mumble':
            return '64739'
    elif type == 4:  # radio
        return '8002'
    elif type == 6:  # COD
        return '28981'
    elif type == 7:  # Unreal Engine
        return '7709'

config = ConfigParser.RawConfigParser()
config.read('/etc/hosting/scripts.cfg')

# ID сервера, на котором пускается скрипт
# Брать его из поля id в админке

thisServerId = config.getint('server', 'serverID')
mysqlHost = config.get('db', 'host')
mysqlUser = config.get('db', 'user')
mysqlPass = config.get('db', 'pass')
mysqlDb = config.get('db', 'db')

# Blocking the process
lock = flock('server_init.lock', True).acquire()

if lock:

    # print 'Получение данных из БД:'
    # host, user, pass, db

    db = MySQLdb.connect(host=mysqlHost, user=mysqlUser, passwd=mysqlPass, db=mysqlDb)

    # Create cursor with row names as array arguments
    cursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)
    commonCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)
    userCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)
    modCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)
    rootServerCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)
    rootServerIpCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)
    serverAddressCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)
    serverInitCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)

    # Получить список неинициализированных серверов,
    # привязанных на этот физический сервер
    servers = getRootServerInitServers(cursor, thisServerId)
    numrows = int(servers.rowcount)

    if numrows > 0:
        print "###############################################"
        print datetime.now().strftime("%A, %d. %B %Y %H:%M:%S")
        print 'Получены данные на ', numrows, 'серверов'
        print 'PID процесса', os.getpid()

    for x in range(0, numrows):

            row = servers.fetchone()
            serverID = row['id']

            # Определяем пользователя
            user = defineUser(commonCursor, serverID)

            # Конец опеределения пользователя
            #

            userName = "client%s" % user['id']
            userNik = user['username']
            homeDir = "/home/%s" % userName
            serversPath = homeDir + "/servers"
            logsPath = homeDir + "/logs"

            if user['steam_id'] == '':
                userSteamId = 'none'
            else:
                userSteamId = user['steam_id']

            # Теперь получим из базы шаблон и тип сервера

            # Шаблон:
            template = defineTemplate(commonCursor, serverID)

            # Тип - достаем его из шаблона:
            type = defineType(commonCursor, str(template['id']))

            print '        Тип сервера: ', type['longname']
            print '        Шаблон сервера: ', template['longname']

            #
            # Выбор IP и порта
            # Запуск подбора IP и порта, только если к серверу уже они не привязаны
            # Это необходимо при переинициализации серверов

            if not row['address']:

                # Получаем все IP, привязанные к этому физическому серверу
                rootServerIpCursor.execute("""SELECT `RootServerIp`.`id`, `RootServerIp`.`ip`, `RootServerIp`.`type`,
                                                `RootServerIp`.`created`,
                                               `RootServerIpsRootServer`.`root_server_ip_id`,
                                               `RootServerIpsRootServer`.`root_server_id`
                                               FROM
                                               `root_server_ips`
                                               AS
                                               `RootServerIp`
                                               JOIN
                                               `root_server_ips_root_servers`
                                               AS
                                               `RootServerIpsRootServer`
                                               ON
                                               (`RootServerIpsRootServer`.`root_server_id` IN (%s)
                                               AND `RootServerIpsRootServer`.`root_server_ip_id` = `RootServerIp`.`id`
                                               AND `RootServerIp`.`type` = 'public')""", thisServerId)

                ipNumrows = int(rootServerIpCursor.rowcount)
                print "На сервер привязано", ipNumrows, "IP"

                # Проходим все IP и выбираем с наименьшим количеством инициализированных серверов
                #

                for i in range(0, ipNumrows):

                    ipRow = rootServerIpCursor.fetchone()

                    serverAddressCursor.execute("""SELECT COUNT(*), `ServersType`.`server_id`,
                                                    `ServersType`.`type_id`
                                                    FROM
                                                    `servers`
                                                    AS
                                                    `Server`
                                                    JOIN
                                                    `servers_types`
                                                    AS
                                                    `ServersType`
                                                    ON
                                                    (`ServersType`.`type_id` = %s
                                                    AND
                                                    NOT ISNULL(address)
                                                    AND
                                                    `ServersType`.`server_id` = `Server`.`id`
                                                    AND
                                                    (initialised = 1)
                                                    AND `address`=%s)""", (type['id'], ipRow['ip']))
                    serverIpNum = serverAddressCursor.fetchone()
                    print "На IP ", ipRow['ip'], " запущено ", serverIpNum['COUNT(*)'], type['name'], " серверов"

                    # Первый ip принимаем за базовый
                    if i == 0:
                        count = serverIpNum['COUNT(*)']
                        serverIp = ipRow['ip']

                    # Дальше сравниваем основным. Если на текущем запущено меньше серверов,
                    # принимаем за базовый его.
                    # Прекратить перебор, если на IP нет серверов

                    if serverIpNum['COUNT(*)'] < count or serverIpNum['COUNT(*)'] == 0:
                        serverIp = ipRow['ip']
                        break

                print "Выбираем IP ", serverIp
            else:
                print "Серверу уже назначен IP -> " + str(row['address'])
                serverIp = row['address']

            if not row['port']:

                # Выбор порта
                # Алгоритм:
                # Запрашиваю минимальное значение  порта для искомого IP:
                #  Если полученное значение меньше минимально допустимого для данного типа серверов,
                #  то считаем, что нужный тип серверов вообще не запущен на этом IP;
                #  - либо полученное значение больше минимально допустимого, то считаем, что
                #    минимально допустимый порт свободен
                #  - либо получено ноль строк
                # Соответсвенно выбираем минимально допустимый порт, определенный в функции typeBasePort
                #  Если полученное значение равно минимально допустимому, то запрашиваем все порты,
                #  привязанные к искомому IP, отсортированные от минимального к максимальному.
                #  И далее перебором смотрим, свободен ли порт. А для некоторых типов серверов - и несколько
                #  портов рядом.
                print "Выбор оптимального порта сервера"
                minimalPort = int(typeBasePort(type['id'], template['name']))  # Минимально допустимый порт

                serverAddressCursor.execute("""SELECT `port` FROM `servers`
                                                        WHERE
                                                        `port` IS NOT NULL
                                                        AND
                                                        `address` = %s
                                                        AND
                                                        `port` >= %s
                                                        ORDER BY `port` ASC
                                                        LIMIT 1""", (serverIp, minimalPort))

                serverPortRow = serverAddressCursor.fetchone()
                if int(serverAddressCursor.rowcount) == 0 or int(serverPortRow['port']) != minimalPort and type['name'] != "radio":
                    print "Минимальный порт свободен."
                    serverPort = minimalPort
                elif int(serverAddressCursor.rowcount) == 0 or \
                    (int(serverPortRow['port']) > (minimalPort + 2) or
                     int(serverPortRow['port']) < minimalPort) and \
                        (type['name'] == "radio" or type['name'] == "ueds"):

                    print "Минимальные два порта свободны."
                    serverPort = minimalPort
                else:
                    print "Минимальный порт занят. Ищу свободный порт."
                    serverAddressCursor.execute("""SELECT `port` FROM `servers`
                                                            WHERE
                                                            `port` IS NOT NULL
                                                            AND
                                                            `port` >= %s
                                                            AND
                                                            `address` = %s
                                                            ORDER BY `port` ASC
                                                            """, (minimalPort, serverIp))
                    numPorts = int(serverAddressCursor.rowcount)

                    for y in range(0, numPorts):

                        serverPortRow = serverAddressCursor.fetchone()

                        if y > 0:  # Проверять порты будем только со второй строки, первую уже проверили
                            if type['name'] == "radio" or type['name'] == "ueds":
                                # Для радио и Unreal Engine нужны два порта рядом, а также убедиться,
                                # что и перед ним порт свободен для предыдущего сервера
                                if int(serverPortRow['port']) > (pastPort + 3):
                                    break
                            else:
                                if int(serverPortRow['port']) > (pastPort + 1):
                                    break

                        pastPort = int(serverPortRow['port'])

                    # Исходя из прошлых поисков прописываем порт
                    if type['name'] == "radio" or type['name'] == "ueds":
                        serverPort = str(pastPort + 2)  # Для радио и Unreal Engine нужны два порта рядом
                    else:
                        serverPort = str(pastPort + 1)

                print "Выбираю порт ", serverPort

                # Конец выбора порта
                #
            else:
                print "Серверу уже назначен port -> " + str(row['port'])
                serverPort = row['port']

            # Пароль RCON
            rconPassword = None
            if type['name'] in ('srcds', 'cod', 'hlds'):
                if row['rconPassword']:
                    rconPassword = row['rconPassword']
                else:
                    rconPassword = genPass()

            print 'Имя создаваемого пользователя: ', userName

            # Создаем сначала группу пользователя
            try:
                retcode = call("groupadd" + " " + userName, shell=True)
                if retcode < 0:
                    print "Команда была прервана с кодом: ", retcode
                elif retcode == 0:
                    print "Группа пользователя успешно создана"
                else:
                    print "Команда вернула код: ", retcode
            except OSError, e:
                print "Команда завершилась неудачей:", e

            # Создаем пользователя
            # Если пользователь уже существует, то считаем это
            # нормальным - пользователь уже есть, идем дальше
            # и создаем дополнительный сервер в его каталоге.

            # Проверка на наличие уже сгенерированного пароля:
            # если таковой существует, то используем его далее,
            # т.к. пользователеь может иметь игровые серверы на
            # разных физических

            if user['ftppassword']:
                userPassword = user['ftppassword']
            else:
                userPassword = genPass()
            # Создание пользователя
            try:
                retcode = call("useradd" + " -m " + userName + " -g " + userName + " -p " + userPassword, shell=True)
                if retcode < 0:
                    print "Команда была прервана с кодом: ", retcode
                elif retcode == 0:

                    # Устанавливаем права на домашний каталог 0751
                    os.chmod(homeDir, 0751)

                    print "Пользователь успешно создан"

                    # Внести ограничение пользователю на запуск не более 50 процессов
                    print "Вношу ограничения в limits.conf"
                    try:
                        if os.path.exists('/etc/security/limits.conf'):
                            limitsConf = open('/etc/security/limits.conf', 'a')
                        else:
                            limitsConf = open('/etc/security/limits.conf', 'w')

                        limitsConf.write('\n%s  hard  nproc  80' % userName)
                        limitsConf.write('\n%s  hard  fsize  2097152' % userName)
                        limitsConf.close()

                    except OSError, e:
                        print "Команда завершилась неудачей:", e

                else:
                    print "Команда вернула код: ", retcode
            except OSError, e:
                print "Команда завершилась неудачей:", e

            # Если пользователь существует, но нет пароля в базе,
            # то надо перезаписать пароль в системе

            if retcode == 0 or retcode == 9 and not user['ftppassword']:
                print "Перезаписываю пароль пользователя"
                try:
                    child = pexpect.spawn('passwd -q %s' % userName)
                    child.expect(['New Password:', 'Новый пароль:'])
                    child.sendline(userPassword)
                    child.expect(['Reenter New Password:', 'Повторите Новый пароль:'])
                    child.sendline(userPassword)

                    print child.before
                    child.expect(pexpect.EOF)
                except OSError, e:
                    # Сообщение об ошибке и переход к следующему серверу
                    print "Команда завершилась неудачей:", e
                    continue

            # Теперь получим uid и gid
            pw = pwd.getpwnam(userName)
            nb = pwd.getpwnam("nobody")
            apachePw = pwd.getpwnam("wwwrun")
            nobodyUid = nb.pw_uid
            userUid = pw.pw_uid
            userGid = pw.pw_gid
            apacheGid = apachePw.pw_gid
            # Если пользователь успешно создан,
            # то создаем директорию, в которую
            # в дальнейшем скрипт будет устанавливать
            # все серверы этого пользователя.
            try:
                print "Создаю директорию для серверов"
                if not os.path.exists(serversPath):
                    os.makedirs(serversPath)
                    os.chown(serversPath, userUid, userGid)
                    os.chmod(serversPath, 0771)
            except OSError, e:
                print "Команда завершилась неудачей:", e
            #
            # Создаем директорию, в которую
            # в дальнейшем будут писаться логи
            try:
                print "Создаю директорию для логов данного пользователя"
                if not os.path.exists(logsPath):
                    os.makedirs(logsPath)
                    os.chown(logsPath, userUid, userGid)
                    os.chmod(logsPath, 0771)
            except OSError, e:
                print "Команда завершилась неудачей:", e
            #

            # Попытка создать директории для карт, файлов motd и демок
            userDirs = ["/home/" + userName + "/public_html/files/",
                        "/home/" + userName + "/public_html/dems/",
                        "/home/" + userName + "/public_html/replay/"]

            for webDir in userDirs:
                if not os.path.exists(webDir):
                    print "Создаю директорию %s:" % webDir
                    try:
                        os.makedirs(webDir)
                        os.chown(webDir, userUid, apacheGid)
                        os.chmod(webDir, 0750)
                        print "Успешно."
                    except OSError, e:
                        print "Не удалось создать директорию:", e
                        continue  # Переход к следующему игровому серверу

            try:
                pubDir = "/home/" + userName + "/public_html/"
                if os.path.exists(pubDir):
                    os.chown(pubDir, userUid, userGid)
            except OSError, e:
                    print "Не удалось установить права на public_html:", e
                    continue  # Переход к следующему игровому серверу

            # Конец создания пользователя и группы
            #

            print 'Текущий ID сервера', serverID

            # Копируем шаблон из репозитория

            # Установим пользователя, от имени которого копируем файлы
            os.setegid(userGid)
            os.seteuid(userUid)

            sourceTemplate = "/images/servers/" + type['name'] + "/" + template['name'] + "/current/"
            destServer = serversPath + "/" + template['name'] + "_%s" % serverID
            # Создание директории логов конкретного сервера
            try:
                print "Создаю директории для логов данного сервера"
                serverLogsDir = logsPath + "/" + template['name'] + "_" + str(serverID)
                if not os.path.exists(serverLogsDir):
                    os.makedirs(serverLogsDir)
                    os.chown(serverLogsDir, userUid, userGid)
                    os.chmod(serverLogsDir, 0771)

                    os.makedirs(serverLogsDir + "/run")
                    os.chown(serverLogsDir + "/run", userUid, userGid)
                    os.chmod(serverLogsDir + "/run", 0771)

                    os.makedirs(serverLogsDir + "/startup")
                    os.chown(serverLogsDir + "/startup", userUid, userGid)
                    os.chmod(serverLogsDir + "/startup", 0771)

                    os.makedirs(serverLogsDir + "/update")
                    os.chown(serverLogsDir + "/update", userUid, userGid)
                    os.chmod(serverLogsDir + "/update", 0771)

            except OSError, e:
                print "Команда завершилась неудачей:", e

            try:
                print "Начинается копирование из " + sourceTemplate + " в " + destServer

                # Если директория с ID сервера уже существует, то считаем это
                # сигналом к полной переинициализации сервера.
                # Т.е. полностью удаляем старую директорию и
                # перезаписываем её чистым шаблоном.

                if os.path.exists(destServer):
                    print "Директория существует. Осущеcтвляю переинициализацию сервера."
                    try:
                        rmtree(destServer)
                        # Для CoD и KillingFloor надо удалить и служебные директории из корня домашней директории
                        if template['name'] == 'cod2':
                            rmtree(homeDir + '/.callofduty2')
                        elif template['name'] == 'cod4':
                            rmtree(homeDir + '/.callofduty4')
                        print "Директория удалена. Начинаю копировать шаблон."
                    except OSError, e:
                        print "Не удалось удалить директорию:", e
                        continue  # Переход к следующему игровому серверу

                # Попытка скопировать шаблон
                try:
                    copytree(sourceTemplate, destServer, symlinks=False)
                    os.chown(destServer, userUid, userGid)
                    os.chmod(destServer, 0771)
                    print "Копирование шаблона завершено."
                    print "Установка владельца запускаемых файлов"
                    checkPermsDirs = [destServer, destServer + '/' + template['addonsPath']]
                    for checkDir in checkPermsDirs:
                        r = setExecFileOwner(userUid, userGid, checkDir)
                        if r != True:
                            raise r
                        else:
                            print r

                except OSError, e:
                    print "Не удалось скопировать шаблон:", e
                    continue  # Переход к следующему игровому серверу

                # Попытка скопировать скрипты, запускаемые от имени клиента
                commonScriptsSrc = "/images/scripts/individual/common/"
                commonScriptsDest = "/home/" + userName + "/public_html/common/"
                if os.path.exists(commonScriptsDest):
                    print "Директория существует. Перезапись общих скриптов на последнюю версию."
                    try:
                        rmtree(commonScriptsDest)
                        print "Директория удалена. Начинаю копировать скрипты."
                    except OSError, e:
                        print "Не удалось удалить директорию:", e
                        continue  # Переход к следующему игровому серверу
                try:
                    copytree(commonScriptsSrc, commonScriptsDest, symlinks=False)
                    if os.path.exists(commonScriptsDest + ".svn"):
                        try:
                            rmtree(commonScriptsDest + ".svn")
                            print "Директория .svn удалена."
                        except OSError, e:
                            print "Не удалось удалить директорию .svn:", e

                    os.chown(commonScriptsDest, userUid, userGid)
                    os.chmod(commonScriptsDest, 0751)
                    retcode = call("chmod" + " -R 751 " + commonScriptsDest, shell=True)
                    print "Копирование общих скриптов успешно."
                except OSError, e:
                    print "Не удалось скопировать общие скрипты:", e
                    continue  # Переход к следующему игровому серверу

                os.seteuid(0)
                os.setegid(0)
                # Создание директории для PID
                if not os.path.exists("/home/pid"):
                    print "Поптыка создать ообщую директорию для PID."
                    try:
                        os.mkdir("/home/pid", 0755)
                        os.chown("/home/pid", 0, 0)
                        print "Директория создана. Двигаюсь дальше."
                    except OSError, e:
                        print "Не удалось создать директорию:", e
                        continue  # Переход к следующему игровому серверу
                else:
                    print "Директорию для PID уже существует. Двигаюсь дальше."

                pidPath = "/home/pid/" + userName
                if not os.path.exists(pidPath):
                    print "Поптыка создать директорию для PID пользователя."
                    try:
                        os.mkdir(pidPath, 0771)
                        os.chown(pidPath, userUid, userGid)
                        print "Директория создана. Двигаюсь дальше."
                    except OSError, e:
                        print "Не удалось создать директорию:", e
                        continue  # Переход к следующему игровому серверу
                else:
                    print "Директорию для PID уже существует. Двигаюсь дальше."

                # Конец создания директории для PID
                #
                # Создание директории для для stdout скриптов

                stdoutPath = "/home/" + userName + "/public_html/output"
                if not os.path.exists(stdoutPath):
                    print "Попытка создать директорию для stdout."
                    try:
                        os.mkdir(stdoutPath, 0755)
                        os.chown(stdoutPath, userUid, userGid)
                        print "Директория создана. Двигаюсь дальше."
                    except OSError, e:
                        print "Не удалось создать директорию:", e
                        continue  # Переход к следующему игровому серверу
                else:
                    print "Директорию для stdout уже существует. Двигаюсь дальше."

                # Конец создания директории для PID

                # Создание конфигурационного файла Apache

                if not os.path.exists("/etc/apache2/conf.d/" + userName + ".conf"):
                    print "Создаю конфиг Apache."
                    configTemplate = open("/images/scripts/root_config/apache_template_user.conf", "r")
                    configUser = open("/etc/apache2/conf.d/" + userName + ".conf", "w")

                    for line in configTemplate:

                        line = line.replace("%s", userName)
                        line = line.replace("%c", userNik)
                        configUser.write(line)

                    configTemplate.close()
                    configUser.close()
                    try:
                        print "Перезапуск сервера Apache"
                        call(['rcapache2', 'restart'])
                    except OSError, e:
                        print "Не удалось перезапустить Apache:", e
                else:
                    print "Конфиг Apache для данного пользователя уже существует. Двигаюсь дальше."
                # Конец создания конфига Apache
                #

                print "Инициализация собственных параметров типов сервера"
                if os.path.exists("/images/scripts/global/server_init_" + type['name'] + ".py"):
                    try:
                        # Для всех серверов, кроме голосовых и радио,
                        # дополнительный скрипт пускать от имени пользователя
                        if type['name'] != 'voice' and type['name'] != 'radio':
                            retcode = Popen("sudo -u " + userName +
                                            " /images/scripts/global/server_init_" + type['name'] + ".py" +
                                            " -m " + str(serverID) +
                                            " -u " + str(user['id']) +
                                            " -t " + str(template['name']) +
                                            " -i " + str(serverIp) +
                                            " -p " + str(serverPort) +
                                            " -s " + str(row['slots']) +
                                            " -r " + str(rconPassword) +
                                            " -c " + str(template['configPath']),
                                            shell=True,
                                            stdin=PIPE,
                                            stdout=PIPE,
                                            stderr=PIPE)
                        else:
                            retcode = Popen(
                                " /images/scripts/global/server_init_" + type['name'] + ".py" +
                                " -m " + str(serverID) +
                                " -u " + str(user['id']) +
                                " -t " + str(template['name']) +
                                " -i " + str(serverIp) +
                                " -p " + str(serverPort) +
                                " -s " + str(row['slots']),
                                shell=True,
                                stdin=PIPE,
                                stdout=PIPE,
                                stderr=PIPE)
                        (out, err) = retcode.communicate()
                        print out
                        if err < 0:
                            print "Не удалось запустить скрипт установки параметров сервера: ", err
                        elif err == 0 or err == "":
                            print "Работа скрипта скрипта установки мода/плагина завершена успешно."

                        else:
                            print "При попытке запуска скрипта установки мода/плагина возникла ошибка: ", err

                    except OSError, e:
                        print "Команда завершилась неудачей:", e

                # Вносим изменения в базу
                print "Сохраняю параметры пользователя в базу"
                userCursor.execute("""UPDATE  `teamserver`.`users`
                                      SET  `ftppassword` =  %s
                                      WHERE  `users`.`id` = %s LIMIT 1""",
                                  (userPassword, user['id']))
                print "Обновлено строк: %d" % userCursor.rowcount

                print "Сохраняю параметры сервера в базу"
                serverInitCursor.execute("""UPDATE  `teamserver`.`servers` SET
                                            `address` =  %s,
                                            `port` =  %s,
                                            `rconPassword` = %s,
                                            `initialised` =  '1'
                                            WHERE  `servers`.`id` = %s LIMIT 1""",
                                        (serverIp, serverPort, rconPassword, serverID))
                print "Обновлено строк: %d" % serverInitCursor.rowcount

                if template['name'] in ('l4d-t100', 'l4d2-t100', 'cssv34'):
                    print "Прописать Tickrate"

                    serverInitCursor.execute("""UPDATE  `teamserver`.`servers` SET
                                            `tickrate` =  '100',
                                            `fpsmax` = '300'
                                            WHERE  `servers`.`id` = %s LIMIT 1""",
                                             serverID)

                    print "Обновлено строк: %d" % serverInitCursor.rowcount

                if type['id'] == 1:
                    print "Обновляю количество купленных слотов на физическом сервере"
                    rootServerCursor.execute("""UPDATE  `root_servers`
                                                SET  `slotsBought` =  slotsBought + %s
                                                WHERE  `root_servers`.`id` = %s LIMIT 1""",
                                            (str(row['slots']), thisServerId))
                    print "Обновлено строк: %d" % rootServerCursor.rowcount

                db.commit()

                print "Установка мода, если требуется."
                # Мод:
                modCursor.execute("""SELECT `Mod`.`id`, `Mod`.`name`, `Mod`.`version`, `Mod`.`created`,
                                            `Mod`.`modified`, `ModsServer`.`mod_id`,
                                            `ModsServer`.`server_id` FROM `mods` AS `Mod`
                                            JOIN
                                            `mods_servers` AS `ModsServer`
                                            ON
                                            (`ModsServer`.`server_id` IN (%s)
                                            AND `ModsServer`.`mod_id` = `Mod`.`id`)
                                            LIMIT 1""", serverID)

                mod = modCursor.fetchone()

                if int(modCursor.rowcount) > 0:
                    print '        Мод сервера: ', mod['name']

                    addonPath = "/images/mods/" + mod['name']
                    if mod['version']:
                        addonPath += '-' + mod['version']

                    try:
                        retcode = Popen("sudo -u " + userName
                                        + " /images/scripts/global/plugin_install.py "
                                        + " -a " + addonPath
                                        + " -s " + destServer
                                        + " -i " + template['addonsPath']
                                        + " -c " + mod['name']
                                        + " -p " + str(userSteamId),
                                        shell=True,
                                        stdin=PIPE,
                                        stdout=PIPE,
                                        stderr=PIPE)

                        (out, err) = retcode.communicate()
                        print out
                        if err < 0:
                            print "Не удалось запустить скрипт установки мода/плагина: ", err
                        elif err == 0 or err == "":
                            print "Работа скрипта скрипта установки мода/плагина завершена успешно."

                        else:
                            print "При попытке запуска скрипта установки мода/плагина возникла ошибка: ", err

                    except OSError, e:
                        print "При попытке запуска скрипта установки мода/плагина возникла ошибка: ", e

                else:
                    print 'Установка мода не требуется.'

            except OSError, e:
                print "Инициализация завершилось неудачей:", e
                continue  # Переход к следующему игровому серверу

    # Закрываем все соединения и базу
    cursor.close()
    commonCursor.close()
    userCursor.close()
    modCursor.close()
    rootServerCursor.close()
    rootServerIpCursor.close()
    serverAddressCursor.close()
    db.commit()
    db.close()
else:
    print 'locked!'
