#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Checke game server health and for correct payed params.
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


'''
    1) Скрипт проверки параметров серверов:
        Если купленные слоты и текущие отличаются,
        отправить предупреждение и глушить сервер.
    2) Одновременно с проверкой, писать в файл директории,
        в которых надо искать демки, чтобы автоматом
        сжимать их и перебрасывать в отдельную директорию.
    3) Проверка на падение сервера:
        - Если сервер отзывается:
            - Проверить наличие прошлых падений в БД.
              Если было падение больше часа назад, обнулить счётчик
        - Если сервер не отзывается
            - Если для сервера разрешён перезапуск в случае падения
                - Внести его ID в отдельный массив

            - После прохода всех серверов, если массив непустой,
              сделать паузу 1 минуту

            - Повторно пройтись по серверам без отклика
                - Если сервер не отзывается:
                    - Если счётчик падений равен нулю или счётчик больше 1 и с момента
                      первого падения прошло больше 1 часа:
                        - Поставить счётчик на 1 и прописать текущее время
                        - Внести в лог клиента сообщение о падении

                    - Если счётчик больше 1 и меньше 3, а также с момента первого падения
                      прошло меньше часа
                        - Увеличить счётчик на 1
                        - Внести в лог клиента сообщение о падении

                    - Если счётчки больше или равен 3
                        - Остановить сервер
                        - Внести в лог клиента сообщение об отключении
                        - Внести в БД в поле сервера сообщение о падении

'''


import MySQLdb
import os
import sys
sys.path.append("/images/scripts/global")
from db_queries import *
from common import *
# import string
import redis
from subprocess import *
from time import mktime, sleep
from flock import flock
from datetime import datetime, date, time
from SourceLib import SourceQuery
from SRCDS import SRCDS
from COD import getCodServerInfo
import rrdtool
import ConfigParser


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
lock = flock('servers_params_checker.lock', True).acquire()

if lock:
    # Задать переменные для внесения статистики
    numServers = 0
    numSlots = 0
    numPlayers = 0
    numErrors = 0
    numVoice = 0
    fmt = '%Y-%m-%d %H:%M:%S'  # Формат даты в БД
    crashedServers = []

    rrdDb = '/var/lib/systemgraph/gameLoad.rrd'
    userRrdDbPath = '/images/stat/bases/'

    # RRD - убрать после того, как сделаю новую статистику
    # Создать директорию для баз графиков, если её ещё нет
    if not os.path.exists(userRrdDbPath):
        os.makedirs(userRrdDbPath)
        os.chmod(userRrdDbPath, 0755)
        logPrint('OK', 'Директория под базы графиков создана')

    # Если нет, создать БД для RRDTool
    if os.path.exists('/var/lib/systemgraph/') and not os.path.exists(rrdDb):
        ret = rrdtool.create(rrdDb, "--step", "300", "--start", '0',
                             "DS:servers:GAUGE:600:U:U",
                             "DS:slots:GAUGE:600:U:U",
                             "DS:players:GAUGE:600:U:U",
                             "RRA:AVERAGE:0.5:1:600",
                             "RRA:AVERAGE:0.5:6:700",
                             "RRA:AVERAGE:0.5:24:775",
                             "RRA:AVERAGE:0.5:288:797",
                             "RRA:MAX:0.5:1:600",
                             "RRA:MAX:0.5:6:700",
                             "RRA:MAX:0.5:24:775",
                             "RRA:MAX:0.5:444:797")
    # RRD

    print datetime.now().strftime("%A, %d. %B %Y %H:%M:%S")
    print 'Запрос данных из БД:'

    # host, user, pass, db

    db = MySQLdb.connect(host=mysqlHost, user=mysqlUser, passwd=mysqlPass, db=mysqlDb)

    # Create cursor with row names as array arguments
    cursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)
    commonCursor = db.cursor(cursorclass=MySQLdb.cursors.DictCursor)

    servers = getRootServerExecutedServers(cursor, thisServerId)

    numrows = int(servers.rowcount)

    logPrint('OK', 'Получены данные на %s серверов' % numrows)

    try:
        dirsList = open('/images/scripts/global/watchForDems/dirs.lst', 'w')
    except:
        logPrint('WARN', 'Не удалось открыть файл watchForDems/dirs.lst на запись')

    try:
        cs16LogDirs = open('/images/scripts/global/watchForLogs/cs16logs.lst', 'w')
    except:
        logPrint('WARN', 'Не удалось открыть файл watchForLogs/cs16logs.lst на запись')

    for x in range(0, numrows):

        server = servers.fetchone()
        # Теперь получим из базы шаблон и тип сервера
        # Шаблон:
        template = defineTemplate(commonCursor, str(server['id']))

        type = defineType(commonCursor, str(template['id']))

        serverID = str(server['id'])
        serverIP = str(server['address'])
        serverPort = int(server['port'])

        logPrint('OK', "Проверка сервера %s #%s %s:%s" % (template['longname'], serverID, serverIP, serverPort))

        try:
            if type['name'] == 'srcds':
                sq = SourceQuery.SourceQuery(serverIP, serverPort)
                info = sq.info()
            elif type['name'] == 'hlds':
                sq = SRCDS(serverIP, serverPort)
                info = sq.details()
            elif type['name'] == 'cod':
                sq = getCodServerInfo(serverIP, serverPort)
            elif type['name'] == 'voice':
                numVoice += 1
                sq = False
            else:
                sq = False
        except:
            logPrint('WARN', '  >>>> Не удалось подключиться к серверу %s:%s' % (serverIP, serverPort))
            numErrors += 1
            crashedServers.append(server)

            continue

        if sq:
            # Проверить предыдущие падения
            if server['crashCount']:

                # convert to unix timestamp
                crashTimeStamp = mktime(server['crashTime'].timetuple())
                nowTimeStamp = mktime(datetime.now().timetuple())

                if (nowTimeStamp - crashTimeStamp) / 60 > 60:
                    # Если было падение больше часа назад, обнулить счётчик
                    setServerCrushStatus(db, commonCursor, serverID, '0')
                    logPrint('OK', "  >>>> Обнуляю счётик падений")

            if type['name'] == 'srcds':
                currentSlots = int(info['maxplayers'])
                numSlots += currentSlots
                curPlayers = (int(info['numplayers']) - int(info['numbots']))
                numPlayers += curPlayers
                # params = sq.rules() # Отключил для ускорения запроса, пока не требуется
            elif type['name'] == 'hlds':
                currentSlots = int(info['maxplayers'])
                numSlots += currentSlots
                numPlayers += int(info['numplayers'])
                curPlayers = int(info['numplayers'])
            elif type['name'] == 'cod':
                if template['name'] == 'cod2':
                    currentSlots = int(sq['sv_maxclients']) - int(sq['bots'])
                else:
                    currentSlots = int(sq['ui_maxclients']) - int(sq['bots'])
                numSlots += currentSlots
                numPlayers += int(sq['numplayers'])
                curPlayers = int(sq['numplayers'])

            # print info, server['slots']#params # debug
            statusDate = datetime.now().strftime("%d %B %Y %H:%M:%S")
            statusDateSql = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
            user = defineUser(commonCursor, serverID)
            demDirs = [False, False]

            # Обновление статистических данных
            numServers += 1

            # Запись статистики
            # Если нет, создать БД для RRDTool
            userRrdDb = userRrdDbPath + serverID + '.rrd'
            if not os.path.exists(userRrdDb):
                ret = rrdtool.create(userRrdDb, "--step", "300", "--start", '0',
                                     "DS:players:GAUGE:600:U:U",
                                     "RRA:AVERAGE:0.5:1:600",
                                     "RRA:AVERAGE:0.5:6:700",
                                     "RRA:AVERAGE:0.5:24:775",
                                     "RRA:AVERAGE:0.5:288:797",
                                     "RRA:MAX:0.5:1:600",
                                     "RRA:MAX:0.5:6:700",
                                     "RRA:MAX:0.5:24:775",
                                     "RRA:MAX:0.5:444:797")

            rrdtool.update(userRrdDb, 'N:' + `curPlayers`)

            # Проверки соответствия слотов
            # Лишний слот добавить на SourceTV
            # Не проверять Left 4 Dead, т.к. у него нет ограничения слотов
            if template['name'] != 'l4d' and template['name'] != 'cod4fixed' and template['name'] != 'mumble' and currentSlots > (int(server['slots']) + 2):
                logPrint('WARN', "  >>>> На сервере #" + serverID + " установлено превышение слотов - " + str(
                    currentSlots) + ' вместо ' + str(server['slots']))
                logPrint('WARN', "  >>>> Принудительная остановка сервера #" + serverID)
                logPrint('WARN', "  >>>> Отправка уведомления об отключении: " + user['email'])
                emailText = '''

                Уведомляем вас, что при последней проверке произведённой
                %s было обнаружено превышение слотов на вашем сервере #%s,
                что является прямым нарушением условий Договора-оферты.
                При регулярном нарушении условий предоставления услуг с
                вашей сотороны, мы будем вправе расторгнуть договор.

                На вашем сервере доступно %s слотов, при проверке
                обнаружено %s игроков.

                Приносим свои извинения, если это произошло вследствие
                ошибки. В этом случае свяжитесь с техподдержкой в
                ближайшее время.

                Сервер был автоматически выключен.

                С уважением,
                TeamServer.ru

                            ''' % (statusDate, serverID, str(server['slots']), str(currentSlots))
                sendEmail('Отключение сервера', emailText, user['email'])

                if type['name'] == 'hlds':  # Отключить HLTV
                    if stopUnpayedServer(serverID, 'game', 'none', True):
                        logPrint('OK', "Сервер HLTV отключен")

                if template['name'] != 'cod4fixed':  # Временно остановить отключение Rotu2

                    if stopUnpayedServer(serverID, 'game', 'none'):
                        logPrint('OK', "Сервер отключен")
                        serverStatus = 'stopped'
                        description = '''
                                Сервер отключен автоматически
                                из-за превышения слотов.
                                      '''

                        saveServerStatus(db, commonCursor, serverID, serverStatus, description, statusDateSql)
                    else:
                        logPrint('ERR', "Возникла ошибка")

            # Тут запишем ключевые директории сервера, в которых искать демки
            elif template['name'] not in ('l4d', 'l4d-t100', 'l4d2', 'l4d2-t100') and type['name'] != 'cod':
                clientDir = 'client' + str(user['id'])
                serverDir = template['name'] + "_" + serverID
                templateDemsDir = template['addonsPath']

                demDirs[0] = '/home/%s/servers/%s/%s' % (clientDir, serverDir, templateDemsDir)

                # Дополнительные директории, где могут быть демки
                if template['name'] == 'css' or template['name'] == 'cssv34':
                    demDirs[1] = demDirs[0] + '/warmod'
                elif template['name'] == 'cs16':
                    demDirs[1] = demDirs[0] + '/demos/HLTV'

                    # Файл директорий наблюдения за логами CS 1.6
                    cs16LogsDir = '/home/%s/servers/%s/%s/logs' % (clientDir, serverDir, templateDemsDir)
                    try:
                        cs16LogDirs.write(cs16LogsDir + "\n")
                    except OSError, e:
                        logPrint('WARN', 'Не удалось обновить список директорий наблюдения за логами: ' + e)

                try:
                    for dir in demDirs:
                        if dir != False:
                            dirsList.write(dir + "\n")

                except OSError, e:
                    logPrint('WARN', 'Не удалось обновить список директорий наблюдения: ' + e)

    # Запись статистики
    statsFile = '/var/log/teamserver-stats.txt'  # Статистика для Munin
    stats = open(statsFile, 'w')
    stats.write("%s:%s:%s:%s" % (numServers, numSlots, numPlayers, numErrors))
    stats.close()
    os.chmod(statsFile, 0644)

    if os.path.exists(rrdDb):
        rrdtool.update(rrdDb, 'N:' + `numServers` + ':' + `numSlots` + ':' + `numPlayers`)

    # Новая статистика
    # Вычислить точку графика с точностью в 5 минут
    curHour = int(datetime.now().strftime("%H"))  # Часы
    curMinutes = str(datetime.now().strftime("%M"))  # Минуты
    lastMinuteDig = int(curMinutes[1])  # Последняя цифра минут

    if not lastMinuteDig in [0, 5]:
        if lastMinuteDig > 0 and lastMinuteDig < 5:  # Округлить до нуля
            lastMinuteDig = 0
        elif lastMinuteDig > 5 and lastMinuteDig <= 9:  # Округлить до 5
            lastMinuteDig = 5

    graphTime = datetime.combine(date.today(),
                                 time(curHour, int(curMinutes[0] + str(lastMinuteDig))))  # Дата с точностью до 5 минут

    graphTimestamp = int(mktime(graphTime.timetuple()))  # unixtime

    '''
        Формат базы такой:

            stat:ServerID:
                            time: ZADD(timestamp)
                |           players: rpush
                |           servers: rpush
                |           voice: rpush
                |           slots: rpush
                |           errors: rpush
                Sum:
                            time: ZADD(timestamp)
                            players: rpush
                            servers: rpush
                            voice: rpush

        Каждые 10 минут должен запускаться скрипт, который
        будет пересчитывать сумму параметров за последний час

    '''

    r = redis.Redis(host='',
                    port=6379,
                    db=0,
                    password='')

    pipe = r.pipeline()

    rootKey = 'stat:' + str(thisServerId)

    if not r.zrank(rootKey + ':time', graphTimestamp):
        pipe.zadd(rootKey + ':time', graphTimestamp, -1)
        pipe.rpush(rootKey + ':players', numPlayers)
        pipe.rpush(rootKey + ':servers', numServers)
        pipe.rpush(rootKey + ':voice', numVoice)
        pipe.rpush(rootKey + ':slots', numSlots)
        pipe.rpush(rootKey + ':errors', numErrors)

        pipe.execute()

    # Повторно пройтись по серверам без отклика
    if crashedServers:
        logPrint('WARN', "Есть серверы без отклика, пауза 3 минуты для повторного опроса")
        sleep(180)

        for server in crashedServers:
            template = defineTemplate(commonCursor, str(server['id']))

            type = defineType(commonCursor, str(template['id']))

            serverID = str(server['id'])
            serverIP = str(server['address'])
            serverPort = int(server['port'])

            logPrint('OK', "Повторная проверка сервера %s #%s %s:%s" % (template['longname'], serverID, serverIP, serverPort))

            try:
                if type['name'] == 'srcds':
                    sq = SourceQuery.SourceQuery(serverIP, serverPort)
                    info = sq.info()
                elif type['name'] == 'hlds':
                    sq = SRCDS(serverIP, serverPort)
                    info = sq.details()
                elif type['name'] == 'cod':
                    sq = getCodServerInfo(serverIP, serverPort)
                else:
                    sq = False
            except:
                logPrint('WARN', '  >>>> Неудачно :(')
                user = defineUser(commonCursor, serverID)

                if int(server['crashReboot']) == 1:
                    crashCount = int(server['crashCount'])
                    statusDateSql = datetime.now().strftime('%Y-%m-%d %H:%M:%S')

                    if server['crashTime']:
                        # convert to unix timestamp
                        crashTimeStamp = mktime(server['crashTime'].timetuple())
                        nowTimeStamp = mktime(datetime.now().timetuple())

                        timeFromFirstCrash = (nowTimeStamp - crashTimeStamp) / 60

                    else:
                        timeFromFirstCrash = 0

                    # Первое падение
                    if crashCount == 0 or (crashCount > 0 and timeFromFirstCrash > 60):
                        logPrint('WARN', "  >>>> Ребут сервера и запись 1 падения!")
                        setServerCrushStatus(db, commonCursor, serverID, '1', statusDateSql)

                        if restartServer(serverID, type['name'], 'none', False):
                            logPrint('OK', "    >>>> Сервер перезапущен")
                            journalText = 'Перезапуск сервера %s #%s после падения' % (template['name'].upper(), serverID)
                            status = 'warn'

                        else:
                            logPrint('ERROR', " >>>> Ошибка перезапуска")
                            journalText = 'Ошибка при попытке перезапуска сервера %s #%s после падения' % (
                                template['name'].upper(), serverID)
                            status = 'error'

                        writeJournal(db, commonCursor, user['id'], journalText, status)  # Сохранить статус в журнал

                    # Повторное падение
                    elif crashCount >= 1 and crashCount < 3 and timeFromFirstCrash <= 60:
                        logPrint('WARN', "  >>>> Ребут сервера и запись +1 падения!")
                        setServerCrushStatus(db, commonCursor, serverID, str(crashCount + 1), 'leave')

                        if restartServer(serverID, type['name'], 'none', False):
                            logPrint('OK', "    >>>> Сервер перезапущен повторно")
                            journalText = 'Повторный перезапуск сервера %s #%s после %s-го падения' % (
                                template['name'].upper(), serverID, str(crashCount + 1))
                            status = 'warn'

                        else:
                            logPrint('ERROR', " >>>> Ошибка повторного перезапуска")
                            journalText = 'Ошибка при попытке перезапуска сервера %s #%s после повторного падения' % (
                                template['name'].upper(), serverID)
                            status = 'error'

                        writeJournal(db, commonCursor, user['id'], journalText, status)  # Сохранить статус в журнал

                    # Последнее падение - отключение сервера
                    elif crashCount >= 3:
                        logPrint('WARN', "  >>>> Остановка сервера!")
                        setServerCrushStatus(db, commonCursor, serverID, str(crashCount + 1), 'leave')

                        if type['name'] == 'hlds':  # Отключить HLTV
                            if stopUnpayedServer(serverID, 'game', 'none', True):
                                logPrint('OK', "    >>>> Сервер HLTV отключен")

                        if stopUnpayedServer(serverID, 'game', 'none'):
                            logPrint('OK', "    >>>> Сервер отключен")
                            serverStatus = 'exec_error'
                            description = '''
                                    Сервер отключен после 3-х неудачных попыток запуска в течение часа.
                                    Запустите сервер в режиме отладки и исправьте ошибки, препятствующие
                                    нормальной работе сервера. Или сообщите в техподдержку о проблеме.
                                          '''

                            saveServerStatus(db, commonCursor, serverID, serverStatus, description, statusDateSql)
                            journalText = 'Отключение сервера %s #%s после 3-х неудачных попыток запуска.' % (
                                template['name'].upper(), serverID)
                            writeJournal(db, commonCursor, user['id'], journalText, 'error')  # Сохранить статус в журнал
                        else:
                            logPrint('ERR', "Возникла ошибка")

                continue

            if sq:
                logPrint('OK', "    >>>> Успешно!")
                continue

    cursor.close()
    commonCursor.close()
    db.commit()
    db.close()
    dirsList.close()
    cs16LogDirs.close()

else:
    logPrint('ERR', "locked!")
