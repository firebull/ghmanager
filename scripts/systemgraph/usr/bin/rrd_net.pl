#!/usr/bin/perl
#             rrd_net.pl  devicename1 [devicename2 ....]     disk usage
#               or
#             rrd_net.pl  <without parameters>
#                           in this case we get the devicenames from
#                           /etc/sysconfig/systemgraph.sysconfig
#
#            example:
#             rrd_net.pl  eth0 ppp0
#
##############################################################################
#
#    This file is part of systemgraph.
#
#    Copyright (C) 2004, 2005, 2006, 2007 Jochen Schlick
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
# $Id: rrd_net.pl,v 1.12 2007/11/01 22:21:07 cvspassaun Exp $
##############################################################################
use strict;
use RRDs;

my $rrdDatabaseName = 'net.rrd.';
my $sysconfigFile   = '/etc/sysconfig/systemgraph.sysconfig';

my $catProcNetDev   = '/bin/cat /proc/net/dev';

#..............................................................................
# database dir env exist ?
my $rrdDatabasePath;

if (exists $ENV{'DATABASEDIR'} ) {
  $rrdDatabasePath = $ENV{'DATABASEDIR'} . "/" . "$rrdDatabaseName";
} else {
  $rrdDatabasePath = '/var/lib/systemgraph/' . "$rrdDatabaseName";
}
#print STDERR "$rrdDatabasePath";

#..............................................
my @rrdDevs;
if ((scalar @ARGV) > 0) {
  @rrdDevs = @ARGV;
} else {

  # open input file
  open(inFH, "< $sysconfigFile")  or usage();

  while (defined (my $inLine = <inFH>)) {

    #print STDERR "$inLine";

    if ($inLine =~/^NETDEV=/o) {

      (my $devname = $inLine) =~ s/^NETDEV=//og;
      chomp $devname;

      push(@rrdDevs, $devname);
    }
  }

  close(inFH);
}

#print STDERR @rrdDevs;


# to avoid a lot of emails from cron when not configured
if (exists $ENV{'RRD_SILENT'}) {
  exit 0  if ((scalar @rrdDevs) == 0);
} else {
  usage() if ((scalar @rrdDevs) == 0);
}


# fill the databases
fill_rrd ();

#..............................................................................
sub usage {

  my $pgmName = $0;
  $pgmName    =~ s/.*\///;  # remove path

  print STDERR <<ENDOFUSAGETEXT;

usage: $pgmName devicename1 [devicename2 ....]
	or
       $pgmName <without parameters>
        in this case the devicenames must be defined in $sysconfigFile

ENDOFUSAGETEXT

  exit 1;
}

#..............................................................................
sub create_rrd($) {

  my $rrdFile = "$rrdDatabasePath" . $_[0];
  #print STDERR "create: $rrdFile\n";

  # awaiting an update every 300 secs
  my $rrdStep = 300;

  # data source = in/out COUNTER, max 600 sec wait before UNKNOWN,
  #   0 min, no max
  # 0.5:1: average value calc. with 1 entry = 300sec
  # 0.5:5: average value calc. with 5 entries = 5*300sec

  # h36    = 3600*36     => 129600sec/300      => 432
  # d7a2   = 3600*24*7   => 604800sec/300 /2   => 1008; 10min average
  # d30a6  = 3600*24*30  => 2592000sec/300 /6  => 1440; 30min average
  # d365a12= 3600*24*365 => 31536000sec/300 /12=> 8760; 1h    average

  # d7a6   = 3600*24*7   => 604800sec/300 /6   => 336;  30min max
  # d30a12 = 3600*24*30  => 2592000sec/300 /12 => 720;  1h    max
  # d365a24= 3600*24*365 => 31536000sec/300 /24=> 4380; 2h    max

  RRDs::create("$rrdFile",
	       '--step', $rrdStep,

	       'DS:inpack:COUNTER:'.($rrdStep*2).':0:U',
	       'DS:outpack:COUNTER:'.($rrdStep*2).':0:U',
	       'DS:inbytes:COUNTER:'.($rrdStep*2).':0:U',
	       'DS:outbytes:COUNTER:'.($rrdStep*2).':0:U',
	       'DS:inerrors:COUNTER:'.($rrdStep*2).':0:U',
	       'DS:outerrors:COUNTER:'.($rrdStep*2).':0:U',

	       'RRA:AVERAGE:0.5:1:432',
	       'RRA:AVERAGE:0.5:2:1008',
	       'RRA:AVERAGE:0.5:6:1440',
	       'RRA:AVERAGE:0.5:12:8760',

	       'RRA:MAX:0.5:6:336',
	       'RRA:MAX:0.5:12:720',
	       'RRA:MAX:0.5:24:4380'
	      );


  my $ERR = RRDs::error;
  die "ERROR while creating $rrdFile: $ERR\n" if $ERR;
}

#..............................................................................
sub update_rrd ($$$$$$$$){

  my $rrdFile  = "$rrdDatabasePath" . $_[0];

  #my $dataTime = $_[1];
  #my $inpack   = $_[2];
  #my $outpack  = $_[3];
  #my $inbytes  = $_[4];
  #my $outbytes = $_[5];
  #my $inerrors = $_[6];
  #my $outerrors= $_[7];

  #print STDERR "update: $rrdFile, dataTime=$_[1], inpack=$_[2], outpack=$_[3], inbytes=$_[4], outbytes=$_[5], inerrors=$_[6], outerrors=$_[7]\n";

  RRDs::update("$rrdFile",
	       $_[1].':'.
	       $_[2].':'.
	       $_[3].':'.
	       $_[4].':'.
	       $_[5].':'.
	       $_[6].':'.
	       $_[7]);
  my $ERR = RRDs::error;
  die "ERROR while updating $rrdFile: $ERR\n" if $ERR;
}

#..............................................................................
sub fill_rrd () {

  #print STDERR @rrdDevs;

  # extract the data from df
  my $rrdTime     = time;

  # the stat of all interfaces
  my @procStat    = `$catProcNetDev 2>/dev/null`;
  #print STDERR @procStat;

  #Inter-|   Receive                                                |  Transmit
  # face |bytes    packets errs drop fifo frame compressed multicast|bytes    packets errs drop fifo colls carrier compressed
  #    lo:702456144 1070053    0    0    0     0          0         0 702456144 1070053    0    0    0     0       0          0
  #  eth0:819868007 2321745    0    0    0     0          0         0 51490066  471709    0    0    0     0       0          0
  #  eth1:  379020    6317    0    0    0     0          0         0      828      12    0    0    0     0       0          0

  # the first two lines are comments:
  shift @procStat;
  shift @procStat;
  #print STDERR @procStat;

  foreach my $rrdDev (@rrdDevs) {

    my $rrdFile   = "$rrdDatabasePath" . $rrdDev;
    #print STDERR $rrdFile;

    my $inpack    = 0;
    my $outpack   = 0;
    my $inbytes   = 0;
    my $outbytes  = 0;
    my $inerrors  = 0;
    my $outerrors = 0;

    my $valid     = 0;

    foreach my $procStatLine (@procStat) {
	
      #print STDERR "[$procStatLine]";

      if ($procStatLine =~ /$rrdDev:\s*(\d+)\s+(\d+)\s+(\d+)\s+\d+\s+\d+\s+\d+\s+\d+\s+\d+\s+(\d+)\s+(\d+)\s+(\d+)\s+\d+\s+\d+\s+\d+\s+\d+\s+\d+/) {
	$inbytes  = $1;
	$inpack   = $2;
	$inerrors = $3;
	$outbytes = $4;
	$outpack  = $5;
	$outerrors= $6;
	$valid    = 1;
	
	#print STDERR "$rrdDev  inb=$inbytes, inp=$inpack, ine=$inerrors, outb=$outbytes, outp=$outpack, oute=$outerrors\n";
	last;
      }
    }

    # fill database when valid

    if ($valid > 0) {
      create_rrd($rrdDev) if not -w $rrdFile;
	
      update_rrd($rrdDev,
		 $rrdTime,
		 $inpack,$outpack,
		 $inbytes,$outbytes,
		 $inerrors, $outerrors)  if -w $rrdFile;
    }
  }
}

#..............................................................................

