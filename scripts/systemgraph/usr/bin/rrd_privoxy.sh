#!/bin/sh
#            rrd_privoxy.sh  get from a running privoxy
#                            the current status.
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
# $Id: rrd_privoxy.sh,v 1.9 2009/08/06 23:36:35 cvslasan Exp $
###############################################################################

SYSCONFIG=/etc/sysconfig/systemgraph.sysconfig

# pull in sysconfig settings
[ -f "${SYSCONFIG}" ] && . ${SYSCONFIG}

# privoxy proxy set ?
[ -n "${PRIVOXY_RRD_PROXY}" ] || exit 0

#echo ${PRIVOXY_RRD_PROXY}

WGET=/usr/bin/wget
AWK=/usr/bin/awk

# check if wget,awk exists
[ -x "${WGET}" ] ||  exit 0
[ -x "${AWK}"  ] ||  exit 0

# rrdtool
if   [ -x   "/usr/bin/rrdtool" ];       then
    RRDTOOL="/usr/bin/rrdtool"
elif [ -x   "/usr/local/bin/rrdtool" ]; then
    RDDTOOL="/usr/local/bin/rrdtool"
else
    exit 0
fi

#................................................................
# force wget to use the proxy by setting env variable http_proxy
# awk recipe decides to print either 0:0 or the actual blocking statistics
PRIVOXY_STAT=`http_proxy=${PRIVOXY_RRD_PROXY} \
             ${WGET} --quiet --timeout=10 http://p.p/show-status -O - | \
	     ${AWK} '/requests have been blocked/ {print $4 ":" $1}'`

#echo "[${PRIVOXY_STAT}]"


DATABASENAME=privoxy.rrd
DATABASEPATH=${DATABASEDIR:="/var/lib/systemgraph"}/$DATABASENAME


#................................................................
# fill database
if [ -f "${DATABASEPATH}" ]; then
    [ -n "${PRIVOXY_STAT}" ] || PRIVOXY_STAT="0:0"

    #echo "update [N:${PRIVOXY_STAT}]"
    ${RRDTOOL} update ${DATABASEPATH} N:${PRIVOXY_STAT}

else
    [ -n "${PRIVOXY_STAT}" ] || exit 0

    #echo "create [N:${PRIVOXY_STAT}]"

    # awaiting an update every 300 secs
    # data source = requests, blocked requests, DERIVE, max 600 sec wait
    # before UNKNOWN, 0 min, no max
    # 0.5:1: average value calc. with 1 entry = 300sec
    # 0.5:5: average value calc. with 5 entries = 5*300sec

    # h36    = 3600*36     => 129600sec/300      => 432
    # d7a2   = 3600*24*7   => 604800sec/300 /2   => 1008; 10min average
    # d30a6  = 3600*24*30  => 2592000sec/300 /6  => 1440; 30min average
    # d365a12= 3600*24*365 => 31536000sec/300 /12=> 8760; 1h    average

    # d7a6   = 3600*24*7   => 604800sec/300 /6   => 336;  30min max
    # d30a12 = 3600*24*30  => 2592000sec/300 /12 => 720;  1h    max
    # d365a24= 3600*24*365 => 31536000sec/300 /24=> 4380; 2h    max

    ${RRDTOOL} create ${DATABASEPATH} --step 300 \
	DS:requests:DERIVE:600:0:U \
	DS:blocked:DERIVE:600:0:U  \
	RRA:AVERAGE:0.5:1:432 \
	RRA:AVERAGE:0.5:2:1008 \
	RRA:AVERAGE:0.5:6:1440 \
	RRA:AVERAGE:0.5:12:8760 \
	RRA:MAX:0.5:6:336 \
	RRA:MAX:0.5:12:720 \
	RRA:MAX:0.5:24:4380
	 
    ${RRDTOOL} update ${DATABASEPATH} N:${PRIVOXY_STAT}
fi

# check error status
if [ $? -ne 0 ]; then
    echo ERROR: rrdtool update of ${DATABASENAME} failed.
fi
