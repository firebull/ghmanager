# systemgraph cron script
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
# $Id: systemgraph.cron.d,v 1.9 2009/08/07 19:19:19 cvslasan Exp $
###############################################################################
#
#
# Linux:
# every minute
*/1 * * * * root DATABASEDIR=/var/lib/systemgraph RRD_SILENT=1 OS_SYSTEM=Linux /usr/bin/rrd_1minute.sh
# every 5 minutes
*/5 * * * * root DATABASEDIR=/var/lib/systemgraph RRD_SILENT=1 OS_SYSTEM=Linux /usr/bin/rrd_5minute.sh
#
#.............................................................................
# OpenBSD (Tested On OpenBSD 4.4)
# every minute
#*/1     *       *       *       *       root; DATABASEDIR=/var/lib/systemgraph RRD_SILENT=1 OS_SYSTEM=OpenBSD /usr/bin/rrd_1minute.sh
# every 5 minutes
#*/5     *       *       *       *       root; DATABASEDIR=/var/lib/systemgraph RRD_SILENT=1 OS_SYSTEM=OpenBSD /usr/bin/rrd_5minute.sh
#
###############################################################################
