#!/usr/bin/perl
#
#             rrd_shorewallstats.pl  devicename1 [devicename2 ....]
#               or
#             rrd_shorewallstats.pl  <without parameters>
#                           in this case we get the devicenames from
#                           /etc/sysconfig/systemgraph.sysconfig
#
#            example:
#             rrd_shorewallstats.pl  eth0 ppp0
#
#              - connection tracking....
#
##############################################################################
#
#    This file is part of systemgraph.
#
#    Copyright (C) 2004-2010 Jochen Schlick
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
# $Id: rrd_shorewallstats.pl,v 1.15 2010/02/13 19:51:47 cvslasan Exp $
#############################################################################
use strict;
use RRDs;

my $rrdDatabaseName = 'shorewallstats.rrd';
my $sysconfigFile   = '/etc/sysconfig/systemgraph.sysconfig';

# path to shorewall
my $pgm     = '/sbin/shorewall';	
my $cmdStat = "$pgm status";
my $cmdRun  = "$pgm show accounting 2>/dev/null";	
#my $cmdRun = "cat shorewall.out2";  # only for tests


#..............................................................................
# check whether nothing to do or not
exit 0      if not -x $pgm;


#..............................................................................
# the ports of interest
#22  ssh
#25  smtp
#53  dns
#80  http
#110 pop3
#119 nntp
#143 imap
#443 https
#873 rsync

my @ports    = ('22','25','53','80','110','119','143','443','873');

# minumum number of shorewall accounting counters per interface
# (iptable counters):
# in the current configuration with 9 ports of interest the minimum
# number number of accounting counters is 72.
#
# 9 [ports] x 8 [4 in and 4 out queues] = 72  (see accounting file)
#
my $minCounters = 72;



#..............................................................................
my @rrdDevs;
if ((scalar @ARGV) > 0) {
  @rrdDevs = @ARGV;
} else {

  # open input file
  open(inFH, "< $sysconfigFile")  or usage();

  while (defined (my $inLine = <inFH>)) {

    #print STDERR "$inLine";

    if ($inLine =~/^SHOREWALL_NETDEV=/o) {

      (my $devname = $inLine) =~ s/^SHOREWALL_NETDEV=//og;
      chomp $devname;

      push(@rrdDevs, $devname);
    }
  }

  close(inFH);
}

#print STDERR @rrdDevs,"\n";

# to avoid a lot of emails from cron when not configured
if (exists $ENV{'RRD_SILENT'}) {
  exit 0  if ((scalar @rrdDevs) == 0);
} else {
  usage() if ((scalar @rrdDevs) == 0);
}

#.............................................................................
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
# database dir env exist ?
my $rrdDatabasePath;

if (exists $ENV{'DATABASEDIR'} ) {
  $rrdDatabasePath = $ENV{'DATABASEDIR'} . "/" . "$rrdDatabaseName";
} else {
  $rrdDatabasePath = '/var/lib/systemgraph/' . "$rrdDatabaseName";
}
#print STDERR "$rrdDatabasePath\n";

# fill the database
fill_rrd ();

#..............................................................................
# create new database file
#
sub create_rrd($) {

  # awaiting an update every 300 secs
  my $rrdStep = 300;

  # data source = xxx GAUGE, max 600 sec wait before UNKNOWN,
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

  my @createPar = ("$_[0]",
		   '--step', $rrdStep);

  foreach my $port (@ports) {
    push (@createPar, 'DS:'. $port . '_in:DERIVE:'.($rrdStep*2).':0:U');
    push (@createPar, 'DS:'. $port . '_ou:DERIVE:'.($rrdStep*2).':0:U');
  }

  push (@createPar, 'RRA:AVERAGE:0.5:1:432');
  push (@createPar, 'RRA:AVERAGE:0.5:2:1008');
  push (@createPar, 'RRA:AVERAGE:0.5:6:1440');
  push (@createPar, 'RRA:AVERAGE:0.5:12:8760');
  push (@createPar, 'RRA:MAX:0.5:6:336');
  push (@createPar, 'RRA:MAX:0.5:12:720');
  push (@createPar, 'RRA:MAX:0.5:24:4380');

  print STDERR join("\n", @createPar). "\n";

  RRDs::create @createPar;
  my $ERR = RRDs::error;
  die "ERROR while creating $_[0]: $ERR\n" if $ERR;
}


#..............................................................................
sub update_rrd ($$$)
  {
    #print STDERR "update_rrd: $_[0], $_[1], $_[2]\n";

    my $rrdFile  = shift;
    my $dateTime = shift;
    my $updateStr = $dateTime . shift;

    #print STDERR "update_rrd: [$rrdFile] $updateStr \n";

    RRDs::update($rrdFile, $updateStr);
    my $ERR = RRDs::error;
    die "ERROR while updating $rrdFile: $ERR\n" if $ERR;
  }


#..............................................................................
sub fill_rrd ()
  {
    #print STDERR join(':', @rrdDevs), "\n";

    # get current time
    my $rrdTime = time;

    my @data    = `$cmdRun`;

    foreach my $rrdDev (@rrdDevs) {

      my $valid   = 0;

      #Shorewall-3.2.1 Chains accounting at hellraiser.paragon - Fri Aug 18 01:46:49 CEST 2006
      #
      #Counters reset Fri Aug 18 01:36:57 CEST 2006
      #
      #Chain accounting (3 references)
      # pkts bytes target     prot opt in     out     source               destination
      #    0     0 ouSGeth0_22  tcp  --  *      eth0    0.0.0.0/0            0.0.0.0/0           multiport dports 22
      #    0     0 ouSGeth0_22  tcp  --  *      eth0    0.0.0.0/0            0.0.0.0/0           multiport sports 22
      #    0     0 ouSGeth0_22  udp  --  *      eth0    0.0.0.0/0            0.0.0.0/0           multiport dports 22
      #    0     0 ouSGeth0_22  udp  --  *      eth0    0.0.0.0/0            0.0.0.0/0           multiport sports 22
      #    0     0 inSGeth0_22  tcp  --  eth0   *       0.0.0.0/0            0.0.0.0/0           multiport dports 22
      #    0     0 inSGeth0_22  tcp  --  eth0   *       0.0.0.0/0            0.0.0.0/0           multiport sports 22
      #    0     0 inSGeth0_22  udp  --  eth0   *       0.0.0.0/0            0.0.0.0/0           multiport dports 22
      #    0     0 inSGeth0_22  udp  --  eth0   *       0.0.0.0/0            0.0.0.0/0           multiport sports 22
      #    0     0 ouSGeth0_25  tcp  --  *      eth0    0.0.0.0/0            0.0.0.0/0           multiport dports 25
      #    0     0 ouSGeth0_25  tcp  --  *      eth0    0.0.0.0/0            0.0.0.0/0           multiport sports 25
      #    0     0 ouSGeth0_25  udp  --  *      eth0    0.0.0.0/0            0.0.0.0/0           multiport dports 25
      #    0     0 ouSGeth0_25  udp  --  *      eth0    0.0.0.0/0            0.0.0.0/0           multiport sports 25
      #    0     0 inSGeth0_25  tcp  --  eth0   *       0.0.0.0/0            0.0.0.0/0           multiport dports 25
      #    0     0 inSGeth0_25  tcp  --  eth0   *       0.0.0.0/0            0.0.0.0/0           multiport sports 25
      #    0     0 inSGeth0_25  udp  --  eth0   *       0.0.0.0/0            0.0.0.0/0           multiport dports 25
      #    0     0 inSGeth0_25  udp  --  eth0   *       0.0.0.0/0            0.0.0.0/0           multiport sports 25
      #    0     0 ouSGeth0_53  tcp  --  *      eth0    0.0.0.0/0            0.0.0.0/0           multiport dports 53
      #    0     0 ouSGeth0_53  tcp  --  *      eth0    0.0.0.0/0            0.0.0.0/0           multiport sports 53
      #   39  2319 ouSGeth0_53  udp  --  *      eth0    0.0.0.0/0            0.0.0.0/0           multiport dports 53
      #    0     0 ouSGeth0_53  udp  --  *      eth0    0.0.0.0/0            0.0.0.0/0           multiport sports 53
      #    0     0 inSGeth0_53  tcp  --  eth0   *       0.0.0.0/0            0.0.0.0/0           multiport dports 53
      #    0     0 inSGeth0_53  tcp  --  eth0   *       0.0.0.0/0            0.0.0.0/0           multiport sports 53
      #    0     0 inSGeth0_53  udp  --  eth0   *       0.0.0.0/0            0.0.0.0/0           multiport dports 53
      #   34  6605 inSGeth0_53  udp  --  eth0   *       0.0.0.0/0            0.0.0.0/0           multiport sports 53
      #  158 13410 ouSGeth0_80  tcp  --  *      eth0    0.0.0.0/0            0.0.0.0/0           multiport dports 80
      #    0     0 ouSGeth0_80  tcp  --  *      eth0    0.0.0.0/0            0.0.0.0/0           multiport sports 80
      #    0     0 ouSGeth0_80  udp  --  *      eth0    0.0.0.0/0            0.0.0.0/0           multiport dports 80
      #    0     0 ouSGeth0_80  udp  --  *      eth0    0.0.0.0/0            0.0.0.0/0           multiport sports 80
      #    0     0 inSGeth0_80  tcp  --  eth0   *       0.0.0.0/0            0.0.0.0/0           multiport dports 80
      #  163  153K inSGeth0_80  tcp  --  eth0   *       0.0.0.0/0            0.0.0.0/0           multiport sports 80
      #    0     0 inSGeth0_80  udp  --  eth0   *       0.0.0.0/0            0.0.0.0/0           multiport dports 80
      #......


      my %ports;

      foreach my $line (@data) {

	#print STDERR "RAW1: $line";

	# filter out unwanted stuff
	next if ($line =~ /^[A-Za-z]/);
	
	#print STDERR "RAW2: $line";
	
	# extract the byte counters counters
	if ($line =~ /^\s*\d+[KMG]{0,1}\s+(\d+)([KMG]){0,1}\s+(in|ou)SG${rrdDev}_(\d+)\s/ ) {

	
	  #print STDERR "bytes=$1, $2, $3, $rrdDev, port=$4\n";
	  $valid++;

	  my $factor = 1;
	  if    ($2 eq 'M') { $factor = 1048576;   }
	  elsif ($2 eq 'K') { $factor = 1024;      }
          elsif ($2 eq 'G') { $factor = 1073741824;}

	  # fill the hash (bytes...
	  $ports{$4}{$3} += $1 * $factor;

	} # end if
      } # end foreach line

      #print STDERR "in/ou=$valid\n";

      # fill databases when valid for this interface
      if ($valid >= $minCounters) {
	
 	my $rrdFile     = "$rrdDatabasePath" .  "." . $rrdDev;

	create_rrd($rrdFile)                             if not -w $rrdFile;

	if (-w $rrdFile) {

	  # build the update string from the stored hash
	  my $updateStr = '';

	  foreach my $port (@ports) {

	    $updateStr .=  ':'. $ports{$port}{'in'} .':'. $ports{$port}{'ou'};

	    #print STDERR "port=$port [$updateStr]\n";
	
	    $ports{$port}{'in'} = 0;
	    $ports{$port}{'ou'} = 0;
	  }

	  update_rrd($rrdFile, $rrdTime, $updateStr);

	}
      } else {
	# invalid num -> cleanup
	
	# is shorewall stopped ?
	my $running  = 1;
	my @dataStat = `$cmdStat`;
	foreach my $statLine (@dataStat) {
	  if ($statLine =~ /is stopped/) {
	    $running = 0;
	    last;
	  }
	}
	
	if ($running > 0) {
	  print STDERR "invalid number of accounting lines: [${rrdDev}] $valid, should be $minCounters.\n";
	  print STDERR "probably a problem with your current /etc/shorewall/accounting file settings\n";
	}

	# cleanup
	foreach my $port (@ports) {
	  $ports{$port}{'in'} = 0;
	  $ports{$port}{'ou'} = 0;
	}
      } #end if

    } #end foreach rrdDev
  }

#................................................................
