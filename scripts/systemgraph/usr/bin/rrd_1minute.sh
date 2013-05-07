#!/bin/sh
#            rrd_1minute.sh  - runs all rrd_xxxx scripts
#                              which should be started every minute 
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
#
# $Id: rrd_1minute.sh,v 1.16 2009/08/07 00:11:04 cvslasan Exp $
##############################################################################


# fill the hdstat.rrd.xxx ..........................
/usr/bin/rrd_hdstat.pl

# fill the cpufreq.rrd .............................
/usr/bin/rrd_cpufreq.pl

# fill the lsof.rrd ................................
/usr/bin/rrd_lsof.pl

# in case you have performance problems with perl version
# there is also a shell/awk version available which does
# the same thing.
# 
#. /usr/bin/rrd_lsof.sh


# fill the cpumem2.rrd ..............................
/usr/bin/rrd_cpumem.pl

# fill the cpustat.rrd .............................
/usr/bin/rrd_cpustat.pl

# fill the loadavg.rrd .............................
. /usr/bin/rrd_loadavg.sh

# fill the users.rrd ...............................
. /usr/bin/rrd_users.sh

# fill the process.rrd .............................
#
# NOTE: this script sleeps 15 secs 
. /usr/bin/rrd_process.sh

#########################################################################
