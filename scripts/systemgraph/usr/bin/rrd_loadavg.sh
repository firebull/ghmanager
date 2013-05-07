#!/bin/sh
#             rrd_loadavg.sh   gets the system load
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
# $Id: rrd_loadavg.sh,v 1.12 2009/08/07 19:18:36 cvslasan Exp $
##############################################################################

DATABASENAME=loadavg.rrd
DATABASEPATH=${DATABASEDIR:="/var/lib/systemgraph"}/${DATABASENAME}

#......................................................
AWK=/usr/bin/awk

# check if awk exists
[ -x "${AWK}"  ] ||  exit 0

# rrdtool
if   [ -x   "/usr/bin/rrdtool" ];       then
    RRDTOOL="/usr/bin/rrdtool"
elif [ -x   "/usr/local/bin/rrdtool" ]; then
    RDDTOOL="/usr/local/bin/rrdtool"
else
    exit 0
fi

#........................................................
if [ "${OS_SYSTEM}" != "OpenBSD" ]; then
 #>cat /proc/loadavg
 #0.11 0.29 0.24 1/171 19111

 LOAD=`${AWK} '{print $1 ":" $2 ":" $3}' < /proc/loadavg`
else

 #Tested On OpenBSD 4.4
 LOAD=`sysctl vm.loadavg | cut -d '=' -f 2 | ${AWK} '{print $1 ":" $2 ":" $3}'`
fi


#........................................................
# don't know why, but under some circumstances the result of
# this is "::" - really strange, this happens once per week.
if [ "${LOAD}" != "::" ]; then

 # fill database
 if [ -f "${DATABASEPATH}" ]; then
    ${RRDTOOL} update ${DATABASEPATH} N:${LOAD}
 else
    # awaiting an update every 60 secs
    # data source = loadxx, GAUGE, max 120 sec wait before UNKNOWN,
    #   0 min, no max
    # 0.5:1: average value calc. with 1 entry = 300sec
    # 0.5:5: average value calc. with 5 entries = 5*300sec

    # h36    = 3600*36     => 129600sec/60       => 2160
    # d7a5   = 3600*24*7   => 604800sec/60 /5    => 2016; 5min average
    # d30a15 = 3600*24*30  => 2592000sec/60 /15  => 2880; 15min average
    # d365a60= 3600*24*365 => 31536000sec/60 /60 => 8760; 1h    average

    ${RRDTOOL} create ${DATABASEPATH} --step 60 \
	DS:load1:GAUGE:120:0:U \
	DS:load5:GAUGE:120:0:U \
	DS:load15:GAUGE:120:0:U \
	RRA:AVERAGE:0.5:1:2160 \
	RRA:AVERAGE:0.5:5:2016 \
	RRA:AVERAGE:0.5:15:2880 \
	RRA:AVERAGE:0.5:60:8760

    ${RRDTOOL} update ${DATABASEPATH} N:${LOAD}
 fi

 # check error status
 if [ $? -ne 0 ]; then
    echo ERROR: rrdtool update of ${DATABASENAME} failed.
 fi

fi
