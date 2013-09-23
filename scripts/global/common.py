# coding: UTF-8

'''
***********************************************
Common library.
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


from datetime import datetime, date, time
from subprocess import *
from random import choice
import string
import smtplib
import socket
from email.mime.text import MIMEText
from email.header import Header
from email.Utils import parseaddr, formataddr
import os
import re
import stat


def setExecFileOwner(uid, gid, dir):
    try:
        for root, dirs, files in os.walk(dir):
            for file in files:
                if not re.search('-update.sh', file):  # Не менять права на скрипт обновления
                    fileName = file
                    file = root + '/' + file
                    st = os.stat(file)
                    if bool(st.st_mode & stat.S_IXUSR) == True:
                        print "Устанавливаю владельца на файл: " + file

                        os.seteuid(0)
                        if fileName in ['nemrun', 'srcupdatecheck']:  # Снять блок на nemrun
                            # os.chown(file, uid, gid)
                            os.chown(file, 0, gid)
                            os.chmod(file, 0751)
                        else:
                            os.chown(file, 0, gid)
                            os.chmod(file, 0751)

                        os.seteuid(uid)
        return True

    except OSError, e:
        return e


def checkOnPort(ip, port):
    print "Проверка занят ли порт сервера"
    s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    s.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
    try:
        s.bind((ip, port))
        s.close()
        print "Порт свободен"
        return True
    except socket.error, e:
        print "Порт занят", e
        return False


def srcdsTypeToGame(type):
    # Ассоциации тип сервера - игра для командной строки srcds
    if type == 'l4d' or type == 'l4d-t100':
        return 'left4dead'
    elif type == 'l4d2' or type == 'l4d2-t100':
        return 'left4dead2'
    elif type == 'tf':
        return 'tf'
    elif type == 'dods':
        return 'dod'
    elif type == 'css' or type == 'cs16' or type == 'cs16-old':
        return 'cstrike'
    elif type == 'csgo' or type == 'csgo-t128':
        return 'csgo'
    elif type == 'cssv34':
        return 'cstrike'
    elif type == 'dmc':
        return 'dmc'
    elif type == 'hl1':
        return 'valve'
    elif type == 'hl2mp':
        return 'hl2mp'
    elif type == 'zps':
        return 'zps'


def srcdsTypeToGameForUpdate(type):
    # Ассоциации тип сервера - игра для командной строки srcds
    if type == 'l4d' or type == 'l4d-t100':
        return 'left4dead'
    elif type == 'l4d2' or type == 'l4d2-t100':
        return 'left4dead2'
    elif type == 'tf':
        return 'tf'
    elif type == 'dods':
        return 'dods'
    elif type == 'css':
        return '"Counter-Strike Source"'
    elif type == 'cs16' or type == 'cs16-old':
        return 'cstrike'
    elif type == 'dmc':
        return 'dmc'
    elif type == 'hl1':
        return 'valve'
    elif type == 'hl2mp':
        return 'hl2mp'
    elif type == 'killingfloor':
        return 'killingfloor'
    elif type == 'zps':
        return 'zps'


def logPrint(level, msg):
    print datetime.now().strftime("%H:%M:%S") + ' ' + level + ': ' + msg


def stopUnpayedServer(serverID, type, scriptsPath, isHltv=False):
    try:
        if type == 'radio':
            print "Запуск скрипта остановки из директории пользователя"
            os.chdir(scriptsPath)
            retcode = Popen("./.server_stop_" + serverID + ".sh",
                            shell=True,
                            stdin=PIPE,
                            stdout=PIPE,
                            stderr=PIPE)

        else:
            os.chdir("/home/configurator/public_html/scripts")
            if isHltv == True:
                action = 'stopHltv'
            else:
                action = 'stop'

            retcode = Popen("/home/configurator/public_html/scripts/subscript_start_stop.py "
                            + " -a " + action
                            + " -s " + serverID,
                            shell=True,
                            stdin=PIPE,
                            stdout=PIPE,
                            stderr=PIPE)

        (out, err) = retcode.communicate()
        print out
        if err < 0:
            print "Не удалось остановить сервер: ", err
            return False
        elif err == 0 or err == "":
            print "Сервер остановлен. Продолжаю дальше."
            return True

        else:
            print "Не удалось остановить сервер: ", err
            return False

    except OSError, e:
        print "Не удалось остановить сервер: ", e
        return False


def restartServer(serverID, type, runWithMod, isHltv=False):
    try:
        os.chdir("/home/configurator/public_html/scripts")
        if isHltv == True:
            action = 'restartHltv'
        else:
            action = 'restart'

        if runWithMod != 'none':
            action = 'restartWith' + runWithMod

        retcode = Popen("sudo -u configurator /home/configurator/public_html/scripts/subscript_start_stop.py "
                        + " -a " + action
                        + " -s " + serverID,
                        shell=True,
                        stdin=PIPE,
                        stdout=PIPE,
                        stderr=PIPE)

        (out, err) = retcode.communicate()
        print out
        if err < 0:
            print "Не удалось остановить сервер: ", err
            return False
        elif err == 0 or err == "":
            print "Сервер остановлен. Продолжаю дальше."
            return True

        else:
            print "Не удалось остановить сервер: ", err
            return False

    except OSError, e:
        print "Не удалось остановить сервер: ", e
        return False


def sendEmail(title, text, sendTo, sendFrom=u'Робот GH Manager <robot-no-answer@teamserver.ru>'):

    senderName, senderAddr = parseaddr(sendFrom)
    senderName = str(Header(unicode(senderName), "UTF-8"))
    senderAddr = senderAddr.encode('ascii')

    msg = MIMEText(text, "plain", "UTF-8")
    msg['Subject'] = Header(title, "UTF-8")
    msg['From'] = formataddr((senderName, senderAddr))
    msg['To'] = formataddr((senderName, senderAddr))
    msg['xMailer'] = 'GH Manager Email Robot'

    # Send the message via our own SMTP server, but don't include the
    # envelope header.

    s = smtplib.SMTP('localhost')
    s.sendmail(sendFrom, [sendTo], msg.as_string())
    s.quit()


def genPass(lenth=9):
    # Генерация пароля из 9-ти символов
    # Алгоритм подсмотрен http://snipplr.com/view/3677/quick-simple-password-generator/

    return ''.join([choice(string.letters + string.digits) for i in range(lenth)])
