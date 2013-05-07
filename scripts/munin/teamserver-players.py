#!/usr/bin/env python2
# coding: UTF-8

import sys, os

if sys.argv[-1] == 'config':
    print "graph_title Players at current server"
    print "graph_category GameServers"
    print "graph_vlabel players"
    print "graph_printf %12.0lf"
    print "players.label Active players"
    print "players.draw AREA"
    print "players.colour CC6600"
    print "graph_info The number of players  at current server"

else:
    statsFile = '/var/log/teamserver-stats.txt'
    if os.path.exists(statsFile):
        stats = open(statsFile, 'r')
        for line in stats:
            values = line.split(':')
            break
        print "players.value " + values[2]
    else:
        print "players.value 0"
