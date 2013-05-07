#!/usr/bin/perl
#             rrd_disk.pl  devicename1 [devicename2 ....]     partition usage
#               or
#             rrd_disk.pl  <without parameters>
#                           in this case we get the devicenames from
#                           /etc/sysconfig/systemgraph.sysconfig
#
#
# extended devicename format:
#
#  <devName>:<extendedName=mountpoint or other useful name>
#
# Allowed chars for <devName>      a-zA-Z0-9-_/:
# Allowed chars for <extendedName> a-zA-Z0-9-_/. (no :)
#
# -where all '/' will be replaced internally by ','
# -where all ':' except the last ':' will be replaced by '|'
#
#
#
#            example: (without extended devicename format)
#             rrd_disk.pl  hda1 hda3 hda4
#
#
#            example: (with extended devicename format)
#             rrd_disk.pl  hda1:/ hda3:/usr  hda4:blablub
#
#
# NOTE: a too long extended name results in the
#       following error message in your http
#       server's errorlog file: 
#	             "basicCheckParam: [....]"
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
#
# $Id: rrd_disk.pl,v 1.23 2009/09/27 21:22:55 cvslasan Exp $
##############################################################################
use strict;
use RRDs;

my $rrdDatabaseName = 'disk.rrd.';
my $sysconfigFile   = '/etc/sysconfig/systemgraph.sysconfig';

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
my $onlyLocal = ' --local';
if (exists $ENV{'OS_SYSTEM'} ) {
  $onlyLocal = ' -l'  if $ENV{'OS_SYSTEM'} eq 'OpenBSD';
}
#print STDERR "$onlyLocal\n";

#..............................................
my @rrdDevs;
if ((scalar @ARGV) > 0) {
  @rrdDevs = @ARGV;
} else {

  # open input file
  open(inFH, "< $sysconfigFile")  or usage();

  while (defined (my $inLine = <inFH>)) {

    #print STDERR "$inLine";

    if      ($inLine =~/^DISKDEV=/o) {

      (my $devname = $inLine) =~ s/^DISKDEV=//og;
      chomp $devname;

      push(@rrdDevs, $devname);

    } elsif ($inLine =~/^WANT_NETWORK_FS=yes/o) {

      # potential blocking risk when connection disrupted
      $onlyLocal ='';
    }

  }

  close(inFH);
}

#extract names

#print STDERR join("\n", @rrdDevs). "\n";

# to avoid a lot of emails from cron when not configured
if (exists $ENV{'RRD_SILENT'}) {
  exit 0  if ((scalar @rrdDevs) == 0);
} else {
  usage() if ((scalar @rrdDevs) == 0);
}

#..............................................................................

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
        in this case the devicenames(partitionname) must be defined
        in $sysconfigFile

 extended devicename format:

  <device>:<mountpoint or other useful name>

            example: (without extended devicename format)
             rrd_disk.pl  hda1 hda3 hda4

            example: (with extended devicename format)
             rrd_disk.pl  hda1:/ hda3:/usr  hda4:blablub


ENDOFUSAGETEXT

  exit 1;
}
#..............................................................................
sub create_rrd($) {

  my $rrdFile = $_[0];
  #print STDERR "create: [$rrdFile]\n";

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

	       'DS:d:GAUGE:'     .($rrdStep*2).':0:U',
	       'DS:avail:GAUGE:' .($rrdStep*2).':0:U',

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
sub update_rrd ($$$$){

  my $rrdFile  = $_[0];

  #my $dataTime = $_[1];
  #my $used     = $_[2];
  #my $avail    = $_[3];

  #print STDERR "update: [$rrdFile], dataTime=$_[1], used=$_[2], avail=$_[3]\n";

  RRDs::update("$rrdFile", $_[1].':'.$_[2].':'.$_[3]);
  my $ERR = RRDs::error;
  die "ERROR while updating $rrdFile: $ERR\n" if $ERR;
}

#..............................................................................
sub fill_rrd () {

  #print STDERR @rrdDevs;

  # extract the data from df
  my $rrdTime = time;
  my @dfLines = `df -kP $onlyLocal`;

  #df -kP
  #Filesystem         1024-blocks      Used Available Capacity Mounted on
  #/dev/sda3             19510304   9359300  10151004      48% /
  #/dev/sda2               101105     39422     56462      42% /boot
  #/dev/sda4             15285208   7514428   7770780      50% /home
  #none                    385368         0    385368       0% /dev/shm
  #chef.paragon:/dat 629591808 366768768 230841664  62% /home1/dat
  #user@192.168.42.7:/home/user 1048576000  0 1048576000  0% /home/owner/OWN.net
  #/dev/sdb1               686684     29408    657276       5% /media/disk

  # first line is always obsolete
  shift @dfLines;
  #print STDERR @dfLines;

  # all remaining lines
  my $storedDfLine;
  foreach my $dfLine (@dfLines) {
    chomp $dfLine;

    #print STDERR "dfLine=[$dfLine]\n";

    foreach my $rrdDev (@rrdDevs) {

      my $realDev;
      my $extDev   ='';
      #                     ':' is  the last one in the line
      if ($rrdDev  =~ /^(.+)\:(.+)/o) {
	$realDev   = $1;
	$extDev    = '.'. $2;
      } else {
	$realDev   = $rrdDev;
      }

      if ($dfLine  =~ /$realDev\s+\d+\s+(\d+)+\s+(\d+)/ ) {

	my $usedM  = sprintf("%d",$1/1024);
	my $availM = sprintf("%d",$2/1024);
	
	#print STDERR "realDev=[$realDev] used:  $1  / 1024 = $usedM\n";
	#print STDERR "realDev=[$realDev] avail: $2  / 1024 = $availM\n";
	
	# convert '/' to ','
	$realDev   =~ s:/:\,:og;
	# convert ':' to '|'
	$realDev   =~ s/:/\|/og;

	# convert '/' to ','
	$extDev    =~ s:/:\,:og;

	my $rrdFile  = "$rrdDatabasePath" . $realDev . $extDev;
	#print STDERR "FND[$dfLine]: rrdFile=[$rrdFile], realDev=[$realDev], extDev=[$extDev]\n";

	# fill database
	create_rrd($rrdFile) if not -w $rrdFile;
	
	update_rrd($rrdFile,
		   $rrdTime,
		   $usedM,
		   $availM)  if -w $rrdFile;
      }
    }
  }
}

#..............................................................................

