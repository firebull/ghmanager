#!/bin/sh
#             rrd_process.sh   counts the number of processes
#
#
##############################################################################
#
#    This file is part of systemgraph.
#
#    Copyright (C) 2004-2009 Jochen Schlick
#
#    systemgraph is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.
#
#    systemgraph is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with systemgraph.  If not, see <http://www.gnu.org/licenses/>.
#
# $Id: rrd_process.sh,v 1.11 2009/08/08 17:57:15 cvslasan Exp $
###############################################################################

DATABASENAME=process.rrd
DATABASEPATH=${DATABASEDIR:="/var/lib/systemgraph"}/$DATABASENAME

# rrdtool
if   [ -x   "/usr/bin/rrdtool" ];       then
    RRDTOOL="/usr/bin/rrdtool"
elif [ -x   "/usr/local/bin/rrdtool" ]; then
    RDDTOOL="/usr/local/bin/rrdtool"
else
    exit 0
fi



# wait 15 seconds, because we don't want to count the other
# rrd_xxx.sh script processes
sleep 15

if [ "${OS_SYSTEM}" != "OpenBSD" ]; then

    # get all processes, count the number of lines (measurements
    # show that the last one is the fastest one most of the time,
    # thanx to Thierry Daucourt)
    #PROCESSES=`ps hax|wc|tr -s [:blank:]| cut -f2 -d" "`
    #PROCESSES=`ps hax|wc|awk '{print $1}'`
    PROCESSES=`ps hax|wc -l`
else
    # h has a different meaning and doesn't mean 'NO HEADER'
    PROCESSES=`ps -axc|wc -l`
fi

# current time
#TODAY=$(/bin/date +%s)

# fill database
if [ -f "${DATABASEPATH}" ]; then
    ${RRDTOOL} update ${DATABASEPATH} N:${PROCESSES}
else
    # awaiting an update every 60 secs

    # data source = processes, GAUGE, max 120 sec wait before UNKNOWN,
    #   0 min, no max
    # 0.5:1: average value calc. with 1 entry = 300sec
    # 0.5:5: average value calc. with 5 entries = 5*300sec

    # h36    = 3600*36     => 129600sec/60       => 2160
    # d7a5   = 3600*24*7   => 604800sec/60 /5    => 2016; 5min average
    # d30a15 = 3600*24*30  => 2592000sec/60 /15  => 2880; 15min average
    # d365a60= 3600*24*365 => 31536000sec/60 /60 => 8760; 1h    average

    # d7a30   = 3600*24*7   => 604800sec/60 /30    => 336;  30min max
    # d30a60  = 3600*24*30  => 2592000sec/60 /60   => 720;  1h    max
    # d365a120= 3600*24*365 => 31536000sec/60 /120 => 4380; 2h    max

    ${RRDTOOL} create ${DATABASEPATH} --step 60 \
	DS:processes:GAUGE:120:0:U \
	RRA:AVERAGE:0.5:1:2160 \
	RRA:AVERAGE:0.5:5:2016 \
	RRA:AVERAGE:0.5:15:2880 \
	RRA:AVERAGE:0.5:60:8760 \
	RRA:MAX:0.5:30:336 \
	RRA:MAX:0.5:60:720 \
	RRA:MAX:0.5:120:4380

    ${RRDTOOL} update ${DATABASEPATH} N:${PROCESSES}
fi

# check error status
if [ $? -ne 0 ]; then
    echo ERROR: rrdtool update of ${DATABASENAME} failed.
fi
