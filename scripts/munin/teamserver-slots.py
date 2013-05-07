#!/usr/bin/env python2
# coding: UTF-8

import sys, os

if sys.argv[-1] == 'config':
    print "graph_title GameServers active slots at current server"
    print "graph_category GameServers"
    print "graph_vlabel slots"
    print "graph_printf %12.0lf"
    print "slots.label Active slots"
    print "slots.draw AREA"
    print "slots.colour EAAE63"
    print "graph_info The number of active slots at current server"

else:
    statsFile = '/var/log/teamserver-stats.txt'
    if os.path.exists(statsFile):
        stats = open(statsFile, 'r')
        for line in stats:
            values = line.split(':')
            break
        print "slots.value " + values[1]
    else:
        print "s.value 0"