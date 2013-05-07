#!/usr/bin/perl
#
#             rrd_hdstat.pl  devicename1 [devicename2 ....]
#               or
#
#             rrd_hdstat.pl  <without parameters>
#                            in this case we get the devicenames from
#                            /etc/sysconfig/systemgraph.sysconfig
#
#             fill the harddisk io statistic database.The devicenames
#             have to be same that you see when you call the iostat tool.
#
#             example:
#              rrd_hdstat.pl  hda sda
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
# $Id: rrd_hdstat.pl,v 1.13 2009/10/18 22:45:43 cvslasan Exp $
##############################################################################
use strict;
use RRDs;

my $rrdDatabaseName = 'hdstat2.rrd.';
my $sysconfigFile   = '/etc/sysconfig/systemgraph.sysconfig';

# path to iostat
my $pgm = '/usr/bin/iostat';	

#......................................................................
# check whether nothing to do or not
if (not -x $pgm) {
  $pgm = '/usr/sbin/iostat';
  exit(0) if not -x $pgm;
}

#.......................................................................
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

    if ($inLine =~/^HDSTATDEV=/o) {

      (my $devname = $inLine) =~ s/^HDSTATDEV=//og;
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



#..............................................................................
# some subprocedures

sub usage {

  my $pgmName = $0;
  $pgmName    =~ s/.*\///;	# remove path

  print STDERR <<ENDOFUSAGETEXT;

usage: $pgmName devicename1 [devicename2 ....]
        or
       $pgmName <without parameters>
        in this case the devicenames must be defined in $sysconfigFile

ENDOFUSAGETEXT

  exit 1;
}



#.......................................................................
sub create_rrd($) {

  my $rrdFile = $_[0];
  #print STDERR "create: [$rrdFile]\n";

  # awaiting an update every 60 secs
  my $rrdStep = 60;

  # data source = , GAUGE,COUNTER max 120 sec wait before UNKNOWN,
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

  RRDs::create("$rrdFile",
	       '--step', $rrdStep,

	       'DS:readkb:COUNTER:'   .($rrdStep*2).':0:U',
	       'DS:writkb:COUNTER:'   .($rrdStep*2).':0:U',

	       'RRA:AVERAGE:0.5:1:2160',
	       'RRA:AVERAGE:0.5:5:2016',
	       'RRA:AVERAGE:0.5:15:2880',
	       'RRA:AVERAGE:0.5:60:8760',

	       'RRA:MAX:0.5:30:336',
	       'RRA:MAX:0.5:60:720',
	       'RRA:MAX:0.5:120:4380'
	      );


  my $ERR = RRDs::error;
  die "ERROR while creating $rrdFile: $ERR\n" if $ERR;
}

#.......................................................................
sub update_rrd ($$$$){

  my $rrdFile   = $_[0];

  #my $dataTime = $_[1];
  #my $readkb   = $_[2];
  #my $writkb   = $_[3];

  #print STDERR "update: [$rrdFile], dataTime=$_[1], readkb=$_[2], writkb=$_[3]\n";

  RRDs::update("$rrdFile",
	       $_[1].':'.
	       $_[2].':'.
	       $_[3]);

  my $ERR = RRDs::error;
  die "ERROR while updating $rrdFile: $ERR\n" if $ERR;
}

#.........................................................................
sub fill_rrdLinux () {

  #print STDERR @rrdDevs;

  # extract the data
  my $rrdTime     = time;

  # the stat of all hds
  my @hdStat    = `$pgm -dk 2>/dev/null`;
  #print STDERR @hdStat;

  # iostat -dk
  #Linux 2.6.23-rc2 (rsstest1)     10/02/07
  #
  #Device:            tps    kB_read/s    kB_wrtn/s    kB_read    kB_wrtn
  #hda               1.41         5.03         8.22    9665323   15786461
  #sdb               1.90       159.15        47.32  361844140  107592492
  #md0               0.08         0.63         0.00    1436848          0
  #md3               0.00         0.00         0.00        192        256
  #md2               5.45        39.16        33.04   89038400   75108273
  #md1               1.33         4.26         9.53    9678994   21673137

  # the first three lines are comments:
  shift @hdStat;
  shift @hdStat;
  shift @hdStat;
  #print STDERR @hdStat;

  foreach my $hdStatLine (@hdStat) {

    if ($hdStatLine =~ /(\S+)\s+\d+\.\d+\s+\d+\.\d+\s+\d+\.\d+\s+(\d+)\s+(\d+)/) {
      my $rrdDev  = $1;
      my $readkb  = $2;
      my $writkb  = $3;

      #print STDERR "@rrdDevs [$rrdDev]  readkb=$readkb, writkb=$writkb\n";


      for (my $i=0; $i < @rrdDevs; $i++) {
	
	if ($rrdDevs[$i] eq $rrdDev) {
	
	  delete $rrdDevs[$i];
	
	  my $rrdDevFile = $rrdDev;
          $rrdDevFile =~ s/\//_/g;
	  my $rrdFile = "$rrdDatabasePath" . $rrdDevFile;

	  #print STDERR "[$rrdFile]  readkb=$readkb, writkb=$writkb\n";

	  create_rrd($rrdFile)               if not -w $rrdFile;
	
	  update_rrd($rrdFile,
		     $rrdTime,
		     $readkb,
		     $writkb)                if -w $rrdFile;

	}
      }
    }
  }
}



#..............................................................................
sub fill_rrdOpenBSD () {

  #print STDERR @rrdDevs;

  # extract the data
  my $rrdTime     = time;

  foreach my $rrdDev (@rrdDevs) {

    # the stat of all hds
    my @hdStat    = `$pgm -I -D $rrdDev 2>/dev/null`;
    #print STDERR @hdStat;

    #/usr/sbin/iostat -I -D sd0
    #           sd0
    #   KB xfr time
    # 21336 1419 5.18

    #/usr/sbin/iostat -I -d sd0
    #            sd0
    #  KB/t xfr MB
    # 15.00 1423 20.85


    # the first two lines are comments:
    shift @hdStat;
    shift @hdStat;
    #print STDERR @hdStat;

    foreach my $hdStatLine (@hdStat) {

      if ($hdStatLine  =~ /\s+(\d+\.{0,1}\d{0,2})\s+\d+\s+\d+/) {
	my $readkb     = $1;
	my $writkb     = $1;

	my $rrdDevFile = $rrdDev;
	$rrdDevFile    =~ s/\//_/g;
	my $rrdFile    = "$rrdDatabasePath" . $rrdDevFile;

	#print STDERR "$rrdDev [$rrdFile]  readkb=$readkb, writkb=$writkb\n";

	create_rrd($rrdFile)               if not -w $rrdFile;
	
	update_rrd($rrdFile,
		   $rrdTime,
		   $readkb,
		   $writkb)                if -w $rrdFile;
      }
    }
  }
}


#..............................................................................
# run...

if (exists $ENV{'OS_SYSTEM'} ) {
  if ($ENV{'OS_SYSTEM'} eq 'OpenBSD') {
    fill_rrdOpenBSD();
  } else {
    fill_rrdLinux();
  }
} else {
  fill_rrdLinux();
}

#..............................................................................

