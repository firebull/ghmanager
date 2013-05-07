#!/usr/bin/perl
#            rrd_hdtemp.pl  devicename1 [devicename2 ....]
#               or
#
#            rrd_hdtemp.pl  <without parameters>
#                           in this case we get the devicenames from
#                           /etc/sysconfig/systemgraph.sysconfig
#
#              get the current temperature from
#              specified harddisks by using the
#              hddtemp program (from the hddtemp
#              package)
#
#            example:
#             rrd_disk.pl  hda sda
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
# $Id: rrd_hdtemp.pl,v 1.9 2009/09/18 01:31:09 cvslasan Exp $
###############################################################################
use strict;
use RRDs;

my $rrdDatabaseName = 'hdtemp.rrd.';
my $sysconfigFile   = '/etc/sysconfig/systemgraph.sysconfig';
my $pgm             = '/usr/bin/hddtemp';


#..............................................................................
# database dir env exist ?
my $rrdDatabasePath;

if (exists $ENV{'DATABASEDIR'} ) {
  $rrdDatabasePath = $ENV{'DATABASEDIR'} . "/" . "$rrdDatabaseName";
} else {
  $rrdDatabasePath = '/var/lib/systemgraph/' . "$rrdDatabaseName";
}
#print STDERR "$rrdDatabasePath";


#..............................................................................
# check whether nothing to do or not
if (not -x $pgm) {
  # opensuse
  $pgm = '/usr/sbin/hddtemp';
  exit(0) if not -x $pgm;
}

#..............................................
my @rrdDevs;
if ((scalar @ARGV) > 0) {
  @rrdDevs = @ARGV;
} else {

  # open input file
  open(inFH, "< $sysconfigFile")  or usage();

  while (defined (my $inLine = <inFH>)) {

    #print STDERR "$inLine";

    if ($inLine =~/^HDTEMPDEV=/o) {

      (my $devname = $inLine) =~ s/^HDTEMPDEV=//og;
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
sub create_rrd ($) {

  my $rrdFile = "$rrdDatabasePath" . $_[0];
  #print STDERR "create: $rrdFile\n";

  # awaiting an update every 300 secs
  my $rrdStep = 300;

  # data source = ...GAUGE, max 600 sec wait before UNKNOWN,
  #   no min, no max
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

	       'DS:t:GAUGE:'     .($rrdStep*2).':0:U',

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
sub update_rrd ($$$) {

  my $rrdFile  = "$rrdDatabasePath" . $_[0];

  #my $dataTime = $_[1];
  #my $temp     = $_[2];

  #print STDERR "update: $rrdFile, dataTime=$_[1], temp=$_[2]\n";

  RRDs::update("$rrdFile", $_[1].':'.$_[2]);
  my $ERR = RRDs::error;
  die "ERROR while updating $rrdFile: $ERR\n" if $ERR;
}

#..............................................................................
sub fill_rrd () {

  #print STDERR @rrdDevs;

  # extract the data from df
  my $rrdTime = time;

  foreach my $rrdDev (@rrdDevs) {

    my $pgmRes = `$pgm -n -u=C /dev/$rrdDev 2>&1`;

    my $rrdFile  = "$rrdDatabasePath" . $rrdDev;
    #print STDERR $rrdFile;
    #print STDERR "[$pgmRes]";

    if (substr($pgmRes, 0, 3) eq '/de')  {
      # error: we get no temperature from this device !!
      print STDERR "Error: $pgmRes";

    } else {

      chomp $pgmRes;

      # fill database
      create_rrd($rrdDev)      if not -w $rrdFile;
	
      update_rrd($rrdDev,
		 $rrdTime,
		 $pgmRes)  if -w $rrdFile;
    }
  }
}

#..............................................................................

