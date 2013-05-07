#!/usr/bin/perl
#                    rrd_ntpdrift.pl    fills the ntpdrift.rrd. How far is
#                                       the clock is on average from
#                                       the network sources.
#
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
# $Id: rrd_ntpdrift.pl,v 1.12 2007/11/01 22:21:07 cvspassaun Exp $
##############################################################################
use strict;
use RRDs;

my $rrdDatabaseName = 'ntpdrift.rrd';
my $sysconfigFile   = '/etc/sysconfig/systemgraph.sysconfig';


# path to ntpq
my $ntpq             = '/usr/sbin/ntpq';

# fall back to sntpclock and clockview from DJB
my $clockview        = '/bin/clockview';
my $sntpclock        = '/bin/sntpclock';
# DJB dns tools (djbdns[tinydns/dnscache] package from DJB)
my $dnsip            = '/bin/dnsip';

# on gentoo the executables are in /usr/bin !!
if ( -f '/etc/gentoo-release' ) {
  $ntpq              = '/usr/bin/ntpq';
  $clockview         = '/usr/bin/clockview';
  $sntpclock         = '/usr/bin/sntpclock';
  $dnsip             = '/usr/bin/dnsip';
}

#..............................................................................
# check whether nothing to do or not
my $usentpq   = 1;
my $sntpserver= 'n';

if (not -x $ntpq) {
  # test whether fallback is available
  exit 0                             if not -x $clockview;
  exit 0                             if not -x $sntpclock;

  # try to read sysconfig file 
  open(inFH, "< $sysconfigFile")  or exit 1;

  while (defined (my $inLine = <inFH>)) {
    #print STDERR "$inLine";

    if     ($inLine =~/^SNTPCLOCK_NTPSERVER=\s*(\d[\.\d]+)\s*$/o) {
      $sntpserver      = $1;
      #print STDERR "dotted:$sntpserver\n";

    } elsif($inLine =~/^SNTPCLOCK_NTPSERVER=\s*([\w\.\-]+)\s$/o) {

      my $nondotserver = $1;
      #print STDERR "nondotted:$nondotserver\n";
      exit 0                         if not -x $dnsip;

      $sntpserver = `$dnsip $nondotserver 2>/dev/null`;
      chomp $sntpserver;
      #print STDERR "sntpserver: $nondotserver\[all ips: $sntpserver\]\n";

      $sntpserver =~ s/ .*//o;
      #print STDERR "sntpserver: $nondotserver\[first ip:$sntpserver\]\n";
    }
  }



  close(inFH);

  $usentpq = 0 if not $sntpserver eq 'n';
}

#print STDERR "use ntpq=$usentpq, sntpserver=$sntpserver\n";


#..............................................................................
# database dir env exist ?
my $rrdFile;

if (exists $ENV{'DATABASEDIR'} ) {
  $rrdFile = $ENV{'DATABASEDIR'} . "/" . "$rrdDatabaseName";
} else {
  $rrdFile = '/var/lib/systemgraph/' . "$rrdDatabaseName";
}
#print STDERR "$rrdFile";

#......................................................................
# fill the database
fill_rrd ();

#..............................................................................
sub create_rrd() {

  # awaiting an update every 300 secs
  my $rrdStep = 300;

  # data source = drift values GAUGE, max 600 sec wait before UNKNOWN,
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

	       'DS:neg_offset:GAUGE:'.($rrdStep*2).':0:U',
	       'DS:pos_offset:GAUGE:'.($rrdStep*2).':0:U',
	       'DS:reachables:GAUGE:'.($rrdStep*2).':0:U',

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

  #my $dataTime   = $_[0];
  #my $neg_offset = $_[1];
  #my $pos_offset = $_[2];
  #my $reachables = $_[3];

  #print STDERR "update: dataTime=$_[0], neg_offset=$_[1], pos_offset=$_[2], reachables=$_[3]\n";

  RRDs::update("$rrdFile",
	       $_[0].':'.
	       $_[1].':'.
	       $_[2].':'.
	       $_[3]);
  my $ERR = RRDs::error;
  die "ERROR while updating $rrdFile: $ERR\n" if $ERR;
}

#..............................................................................
sub fill_rrd () {

  my $rrdTime     = time;

  my $neg_count   = 0;
  my $pos_count   = 0;
  my $neg_offset  = 0;
  my $pos_offset  = 0;
  my $reachables  = 0;

  if ( $usentpq == 1) {
    foreach (`$ntpq -pn 2>/dev/null`) {

      # extract the offset values from the ntpq output
      my ($remote,$ref,$st,$t,$w,$p,$reach,$delay,$o,$d) = split;

      #print "[remote=$remote]\n";

      next if ($remote =~ /===/o);
      next if ($remote eq "remote");

      # host must be reachable
      if ($reach > 0) {

	$reachables++;

	#print $o;
	if      ($o > 0) {
	  $pos_offset = $pos_offset + $o;
	  $pos_count++;

	} elsif ($o < 0) {
	  $neg_offset = $neg_offset - $o;
	  $neg_count++;
	}
      }
    }

    # Now we will get the average from our ntp sources
    $pos_offset /= $pos_count if ($pos_count > 0);
    $neg_offset /= $neg_count if ($neg_count > 0);
  }
  else {
    # sntpclock and clockview as fallback

    my $loc;
    my $rem;
    my $d1;
    my $d2;

    foreach (`$sntpclock $sntpserver 2>/dev/null | $clockview 2>/dev/null`) {

      #>sntpclock 141.82.30.251 | clockview
      #before: 2006-08-24 01:27:41.027106000000000000
      #after:  2006-08-24 01:27:42.641315722686767578

      if      (/^before:\s*([0-9\-]+)\s([0-9]+):([0-9]+):([0-9\.]+)/o) {
	# all values in msec !!!
	$d1    = $1;
	$loc   = (($2*60 + $3)*60)*1000 + int ($4*1000);

	#printf STDERR "oc:$_ $loc\n";
	
      } elsif (/^after:\s*([0-9\-]+)\s([0-9]+):([0-9]+):([0-9\.]+)/o) {
	$d2    = $1;
	$rem   = (($2*60 + $3)*60)*1000 + int($4*1000);
	
	#printf STDERR "rem:$_ $rem\n";
      }
    }

    if ($d1 eq $d2) {

      # offset
      if ($rem > $loc) {
	$pos_count  = 1;
	$pos_offset = $rem - $loc;
      } else {
	$neg_count  = 1;
	$neg_offset = $loc - $rem;
      }
      $reachables++;
    }
  }

  # fill database
  if (($pos_count > 0) or ($neg_count > 0)) {

    create_rrd()            if not -w $rrdFile;

    update_rrd($rrdTime,
	       $neg_offset,
	       $pos_offset,
	       $reachables) if -w $rrdFile;
  }
}

#..............................................................................
