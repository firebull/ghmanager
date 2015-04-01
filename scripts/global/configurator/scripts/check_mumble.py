#!/usr/bin/env python2
# coding: UTF-8

'''
***********************************************
Check basic Mumble params.
Mumble query example was taken at https://bitbucket.org/Svedrin/k10-plugins/src/tip/BwCalc/plugin.py#cl-120
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


# Basic Mumble params check

print "Content-Type: application/json; charset=UTF-8"     # HTML is following
print                                              # blank line, end of headers

from struct import pack, unpack
import socket
import sys
import datetime

import cgi
import cgitb

try:
    cgitb.enable()  # Debug

    params = cgi.FieldStorage()

    ip = str(params["ip"].value).strip()
    port = int(params["port"].value)

    try:
        addrinfo = socket.getaddrinfo(ip, port, 0, 0, socket.SOL_UDP)
    except socket.gaierror, e:
        print '{"error": "connection error", "desc": %s}' % e
        sys.exit()

    (family, socktype, proto, canonname, sockaddr) = addrinfo[0]
    s = socket.socket(family, socktype)
    s.settimeout(1)

    buf = pack(">iQ", 0, datetime.datetime.now().microsecond)
    try:
        s.sendto(buf, sockaddr)
    except socket.gaierror, e:
        print '{"error": "connection error", "desc": %s}' % e
        sys.exit()

    try:
        data, addr = s.recvfrom(1024)
    except socket.timeout:
        print '{"error": "timeout", "proto": "%s"}' % {10: "IPv6", 2: "IPv4"}[family]
        sys.exit()

    r = unpack(">bbbbQiii", data)

    version = "%d.%d.%d" % r[1:4]
    # r[0,1,2,3] = version
    # r[4] = ts
    # r[5] = users
    # r[6] = max users
    # r[7] = bandwidth
    ping = (datetime.datetime.now().microsecond - r[4]) / 1000.0
    if ping < 0: ping += 1000

    print '{"version": "%s", "users": %d, "slots": %d, "bandwidth": %d, "proto": "%s"}' % \
    (version, r[5], r[6], r[7]/1000, {10: "IPv6", 2: "IPv4"}[family])

except Exception, e:
    print '{"error": %s}' % e

