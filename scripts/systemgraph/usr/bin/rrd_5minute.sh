#!/bin/sh
#            rrd_5minute.sh  - runs all rrd_xxxx scripts
#                              which should be started every 5 minutes 
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
# $Id: rrd_5minute.sh,v 1.14 2009/08/06 23:06:46 cvslasan Exp $
##############################################################################

# fill the disk.rrd.xxx ...................................
/usr/bin/rrd_disk.pl

# fill the net.rrd.xxx ....................................
/usr/bin/rrd_net.pl

# fill the ntpdrift.rrd ...................................
/usr/bin/rrd_ntpdrift.pl

# fill the fan.rrd.xxx , temp.rrd.xxx .....................
/usr/bin/rrd_health.pl

# fill the privoxy.rrd ....................................
/usr/bin/rrd_privoxy.sh

# Network Traffic Accounting: .............................
#
# fill the shorewallstats.rrd
#
# NOTE: needs /usr/sbin/shorewall to run
/usr/bin/rrd_shorewallstats.pl

# fill the iptraf.rrd
#
# NOTE: needs /usr/bin/iptraf to run
/usr/bin/rrd_iptraf.pl

# fill the hdtemp.rrd .....................................
#
# NOTE: needs /usr/bin/hddtemp to run
/usr/bin/rrd_hdtemp.pl

########################################################################
