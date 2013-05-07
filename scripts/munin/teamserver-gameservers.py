#!/usr/bin/env python2
# coding: UTF-8

import sys, os

if sys.argv[-1] == 'config':
    print "graph_title Gameservers at current server"
    print "graph_category GameServers"
    print "graph_vlabel gameservers"
    print "graph_printf %12.0lf"
    print "gameservers.label Gameservers per 5 minutes"
    print "gameservers.draw AREA"
    print "gameservers.colour FF9900"
    print "errors.label Unresponsive gameservers"
    print "errors.draw AREA"
    print "errors.colour CC6600"
    print "graph_info The number of game servers running at current server"

else:
    statsFile = '/var/log/teamserver-stats.txt'
    if os.path.exists(statsFile):
        stats = open(statsFile, 'r')
        for line in stats:
            values = line.split(':')
            break
        print "gameservers.value " + values[0]
        print "errors.value " + values[3]
    else:
        print "gameservers.value 0"
        print "errors.value 0"