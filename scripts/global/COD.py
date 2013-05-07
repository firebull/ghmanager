#!/usr/bin/env python
# coding: UTF-8

#
# Stats Receiver Function     #
# by NeckCracker.de aka Kurbl #
# Modified by Nikita Bulaev   #
#

import socket
import re


def getCodServerInfo(ip, port=28960):
    # init socket
    sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
    sock.settimeout(2)
    sock.connect((ip, port))

    # send "handshake" and status request
    sock.send("\xFF\xFF\xFF\xFFgetstatus\x00")

    # receive data
    msg = ""
    try:
        msg = sock.recv(5000)

        last = msg

        if len(msg) == 5000:
            while last != '':
                last = sock.recv(5000)
                if last == '':
                    break
                else:
                    msg = msg + last

        # parse properties
        raw_properties = msg.split('\\')
        # print raw_properties
        properties = {}

        raw_properties = raw_properties[1:]
        items = range(len(raw_properties))

        for i in items:
            if i % 2 == 0:
                if raw_properties[i] != 'mod':
                    properties[raw_properties[i]] = raw_properties[i+1]
                else:
                    t = raw_properties[i+1].split('\n')
                    properties[raw_properties[i]] = t[0]
                    del t[0]
                    # Parse for players and bots
                    players = 0
                    bots = 0
                    for player in t:

                        if re.match('^[0]\s[9]{3}\s\"bot[\d]{1,5}\"$', player.strip().lower()):
                            bots += 1

                        elif re.match('^[0-9]{1,}\s[1-9]{1,3}\s\".{1,}\"$', player.strip().lower()):
                            players += 1

                    properties['numplayers'] = players
                    properties['bots'] = bots

        properties['passworded'] = properties['pswrd']
        del properties['pswrd']

        # return properties
        return properties

    except:
        raise
        return False

    # close socket
    sock.close()
