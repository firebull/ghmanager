#!/usr/bin/perl
#
#             rrd_iptraf.pl  devicename1 [devicename2 ....]
#               or
#             rrd_iptraf.pl  <without parameters>
#                           in this case we get the devicenames from
#                           /etc/sysconfig/systemgraph.sysconfig
#
#            example:
#             rrd_iptraf.pl  eth0 ppp0
#
#              - connection tracking....
#
# NOTE:
# 1)Before you want to use iptraf you have to run iptraf from the command
#   line to configure it through the curses interface. Since we'll be
#   reading the statistics every five minutes, you definitely have to
#   set the log interval to five minutes. There exists no command line
#   option for this.
# 2)For systems without pgrep:
#   For every IPTRAF_NETDEV defined in your /etc/sysconfig/systemgrah.sysconfig
#   you have to start one instance of iptraf as root. Use the following
#   command
#
#   /usr/bin/iptraf -s <IPTRAF_NETDEV> -B
#
#   the following line does not work, using -L seeems to reset the logging
#   timeout back to the default value of 60 minutes.
#   /usr/bin/iptraf -s eth0 -L /var/log/iptraf/tcp_udp_services-eth0.log -B
#
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
# $Id: rrd_iptraf.pl,v 1.13 2010/02/13 18:57:03 cvslasan Exp $
#############################################################################
use strict;
use RRDs;

use strict;
use warnings;

use Time::Local;

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

my @PORTS = qw ( 22 25 53 80 110 119 143 443 873 );



#..............................................................................

# path to iptraf
my $iptraf          = '/usr/bin/iptraf';	
my $rrdDatabaseName = 'iptraf.rrd';

my $sysconfigFile   = '/etc/sysconfig/systemgraph.sysconfig';

my $pgrep           = '/usr/bin/pgrep';

#..............................................................................
# database dir env exist ?
my $rrdDatabasePath;

if (exists $ENV{'DATABASEDIR'} ) {
  $rrdDatabasePath = $ENV{'DATABASEDIR'} . "/" . "$rrdDatabaseName";
} else {
  $rrdDatabasePath = '/var/lib/systemgraph/' . "$rrdDatabaseName";
}
#print STDERR "$rrdDatabasePath\n";


#..............................................................................
# check whether nothing to do or not
exit(0) if not -x $iptraf;

#..............................................................................
my @rrdDevs;
if ((scalar @ARGV) > 0) {
  @rrdDevs = @ARGV;
} else {

  # open input file
  open(inFH, "< $sysconfigFile")  or usage();

  while (defined (my $inLine = <inFH>)) {

    #print STDERR "$inLine";

    if ($inLine =~/^IPTRAF_NETDEV=/o) {

      (my $devname = $inLine) =~ s/^IPTRAF_NETDEV=//og;
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
# starting iptraf in background if not running at the moment
# (this works only if you have pgrep)
sub start_iptraf() {

  # at least one instance running
  if (-x $pgrep) {

    my @p = `$pgrep iptraf`;

    #print STDERR "[@p]";
    if (scalar @p == 1) {

      foreach my $rrdDev (@rrdDevs) {
	
	my $ret = `$iptraf -s $rrdDev -B 2>/dev/null`;
      }
    }
  }
}

#..............................................................................
# create new database file
#
sub create_rrd($) {

  my $rrdFile = "$rrdDatabasePath" . '.' . $_[0];
  #print STDERR "create: $rrdFile\n";

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

  my @createPar = ("$rrdFile",
		   '--step', $rrdStep);

  foreach my $port (@PORTS) {
    push (@createPar, 'DS:'. "$port" . '_in:DERIVE:'.($rrdStep*2).':0:U');
    push (@createPar, 'DS:'. "$port" . '_ou:DERIVE:'.($rrdStep*2).':0:U');
  }

  push (@createPar, 'RRA:AVERAGE:0.5:1:432');
  push (@createPar, 'RRA:AVERAGE:0.5:2:1008');
  push (@createPar, 'RRA:AVERAGE:0.5:6:1440');
  push (@createPar, 'RRA:AVERAGE:0.5:12:8760');
  push (@createPar, 'RRA:MAX:0.5:6:336');
  push (@createPar, 'RRA:MAX:0.5:12:720');
  push (@createPar, 'RRA:MAX:0.5:24:4380');

  #print STDERR join("\n", @createPar). "\n";

  RRDs::create @createPar;
  my $ERR = RRDs::error;
  die "ERROR while creating $rrdFile: $ERR\n" if $ERR;
}


#.........................................................................
# parse the log file and try to interpret it
#
sub parseIptraf ($$$$) {

  my ($rrdDev, $LAST, $header, $inFH) = @_;


  my %hash;
  $hash{_time} = getIptrafTime( ($header =~ m/generated (.*)/)[0] );

  #print STDERR "parseIptraf1: [rrdDev=$rrdDev], [LAST=$LAST], [header=$header";

  return unless $hash{_time} > $LAST + 60;

  while (<$inFH>) {

    last if ( m/^Running/ );
    next if ( m/^\s*$/ );

    #print STDERR "parseIptraf2: [$_";


    # read data for tcp packets
    # example:
    # TCP/80: 636547 packets, 622367389 bytes total, 9,18 kbits/s; 421883 packets, 36665014 bytes incoming, 0,54 kbits/s; 214664 packets, 585702375 bytes outgoing, 8,64 kbits/s
    # UDP/53: 11891 packets, 894313 bytes total, 0,01 kbits/s; 5965 packets, 396676 bytes incoming, 0,01 kbits/s; 5926 packets, 497637 bytes outgoing, 0,01 kbits/s


    if      (/^TCP\/(\d+).*\s(\d+) bytes inc.*\s(\d+) bytes out/o) {
      my $port     = $1;
      my $byte_in  = $2;
      my $byte_out = $3;

      #print STDERR "parseIptraf3:   TCPport=$port, $byte_in, $byte_out\n";
      if (exists  $hash{$port}) {
	$hash{$port}[0] += $byte_in;
	$hash{$port}[1] += $byte_out;
	#print STDERR "parseIptraf3Sum:TCPport=$port, $hash{$port}[0], $hash{$port}[1]\n";
      } else {
	$hash{$port} = [$byte_in, $byte_out];
      }

    } elsif (/^UDP\/(\d+).*\s(\d+) bytes inc.*\s(\d+) bytes out/o) {
      my $port     = $1;
      my $byte_in  = $2;
      my $byte_out = $3;

      #print STDERR "parseIptraf3:   UDPport=$port, $byte_in, $byte_out\n";
      if (exists  $hash{$port}) {
	$hash{$port}[0] += $byte_in;
	$hash{$port}[1] += $byte_out;
	#print STDERR "parseIptraf3Sum:UDPport=$port, $hash{$port}[0], $hash{$port}[1]\n";
      } else {
	$hash{$port} = [$byte_in, $byte_out];
      }
    }
  }

  return \%hash;
}


#.........................................................................
# send update to the db
#
sub updateIptraf ($$$){

  my ($rrdDev, $LAST, $hash) = @_;

  #print STDERR "rrdDev=$rrdDev, LAST=$LAST\n";


  return unless $hash->{_time} > $LAST + 60;

  my $rrdFile  = "$rrdDatabasePath" . '.' . $rrdDev;

  if (-w $rrdFile) {

    my $rrdString = join(":", $hash->{_time},
		map { ref($hash->{$_}) ? @{$hash->{$_}} : qw( U U ) }
		@PORTS );


    RRDs::update("$rrdFile", $rrdString);
    my $ERR = RRDs::error;
    die "ERROR while updating $rrdFile: $ERR\n" if $ERR;

    #print STDERR "updateIptraf:[$rrdString]\n";
  }
}


#..........................................................................
# iptraf has restarted, put 'U' (unknown) in db.
#
sub resetIptraf ($$$){

  my ($rrdDev, $LAST, $line) = @_;

  #print STDERR "resetIptraf: [rrdDev=$rrdDev], [LAST=$LAST], [line=$line";

  my %hash;
  $hash{_time} = getIptrafTime( (split( /;/, $line))[0] );

  updateIptraf($rrdDev,
	       $LAST,
	       \%hash );
}

#..............................................................................
#
sub fill_rrd () {

  foreach my $rrdDev (@rrdDevs) {


    # which logfile
    my $logFile  = '/var/log/iptraf/tcp_udp_services-' . $rrdDev . '.log';
    my $rrdFile  = "$rrdDatabasePath" . '.' . $rrdDev;


    # fill database
    create_rrd("$rrdDev")    if not -w $rrdFile;


    my $LAST     =  RRDs::last("$rrdFile") or 0;

    #print STDERR "fillrrd0: [logFile=$logFile], [rrdFile=$rrdFile], [LAST=$LAST]\n";

    # open input file
    open(inFH, "< $logFile") or die;

    #my $linel = 0;
    while (<inFH>) {

      #$linel =  $linel + 1;

      if ( m/service monitor started/ ) {
	resetIptraf($rrdDev,
		    $LAST,
		    $_ );
      }

      next unless ( m/^\*\*\*/ );

      #print STDERR "fillrrd1:$linel [$_";

      my $hash = parseIptraf($rrdDev,
			     $LAST,
			     $_,
			     \*inFH);

      #print STDERR "fillrrd2:$linel [$_";


      updateIptraf($rrdDev,
		   $LAST,
		   $hash)                 if ($hash);

    }
  }
}






#.........................................................................
# translate iptraf's time string into unixtime
#
sub getIptrafTime ($) {
  my ($input) = @_;

  #print STDERR "getIptrafTime: [$input]";


  my ($day, $month, $date, $hour, $minute, $second, $year) = split( /\s+|:/, $input );

  $month = $month eq 'Jan' ? 0  :
    $month eq 'Feb' ? 1  :
      $month eq 'Mar' ? 2  :
	$month eq 'Apr' ? 3  :
	  $month eq 'May' ? 4  :
	    $month eq 'Jun' ? 5  :
	      $month eq 'Jul' ? 6  :
		$month eq 'Aug' ? 7  :
		  $month eq 'Sep' ? 8  :
		    $month eq 'Oct' ? 9  :
		      $month eq 'Nov' ? 10 :
			$month eq 'Dec' ? 11 : undef;


  #print STDERR " $year, $month, $date, $hour, $minute, $second\n";

  die "Bad date $input" unless defined ( $month );

  return timelocal( $second, $minute, $hour, $date, $month, $year );
}

#..............................................................................

start_iptraf();

# fill the databases
fill_rrd ();
