#!/usr/bin/perl
#                    rrd_cpufreq.pl    fills the cpufreq.rrd
#
#                                -  current cpu frequency in  MHz
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
# $Id: rrd_cpufreq.pl,v 1.8 2009/08/06 21:36:00 cvslasan Exp $
##############################################################################
use strict;
use RRDs;

# path to cpuinfo
my $cpuinfo         = 'cat /proc/cpuinfo';	

my $rrdDatabaseName = 'cpufreq.rrd.';
my $sysconfigFile   = '/etc/sysconfig/systemgraph.sysconfig';

#............................................................................
my $wantCpuFreq     = 0;

if (((scalar @ARGV) > 0) or ($ARGV[0] > 0)) {
  $wantCpuFreq      = 1;

} else {
  # open input file
  open(inFH, "< $sysconfigFile") or usage();

  while (defined (my $inLine = <inFH>)) {

    #print STDERR "$inLine";

    if ($inLine =~/^CPUFREQ_WANTED=\s*yes/o) {
      $wantCpuFreq  = 1;
    }
  }

  close(inFH);
}

#extract names

#print STDERR "#wantCpuFreq: $wantCpuFreq\n";
exit 0 if ($wantCpuFreq == 0);

#..............................................................................
# fill the database
fill_rrd ();

#..............................................................................
sub usage
  {
    my $pgmName = $0;
    $pgmName    =~ s/.*\///;  # remove path

    print STDERR <<ENDOFUSAGETEXT;

usage: $pgmName [dummy]
	or
       $pgmName <without parameters>
        in this case the CPUFREQ_WANTED must be defined in $sysconfigFile
        otherwise no output.

ENDOFUSAGETEXT

    exit 1;
  }


#..............................................................................
sub create_rrd($$) {

  my $rrdFile  = shift;
  my $numCpu   = shift;

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

  my @createPar = ("$rrdFile",
		   '--step', $rrdStep,
		  );

  # create as many cpus
  my $i = 0;
  while ($i  < $numCpu) {
    push(@createPar, "DS:cpu${i}:GAUGE:".($rrdStep*2).':0:U');
    $i++;
  }

  push(@createPar,   'RRA:AVERAGE:0.5:1:2160');
  push(@createPar,   'RRA:AVERAGE:0.5:5:2016');
  push(@createPar,   'RRA:AVERAGE:0.5:15:2880');
  push(@createPar,   'RRA:AVERAGE:0.5:60:8760');

  push(@createPar, 'RRA:MAX:0.5:30:336');
  push(@createPar, 'RRA:MAX:0.5:60:720');
  push(@createPar, 'RRA:MAX:0.5:120:4380');

  #print STDERR join("\n", @createPar). "\n";

  RRDs::create @createPar;
  my $ERR = RRDs::error;
  die "ERROR while creating $rrdFile: $ERR\n" if $ERR;
}


#..............................................................................
sub update_rrd ($$@)
  {
    #print STDERR "update_rrd: $_[0], $_[1], \n";

    my $rrdFile  = shift;
    my $dateTime = shift;
    my $updateStr = $dateTime . ':' . join(':', @{$_[0]});

    #print STDERR "update_rrd: $rrdFile $updateStr \n";
    RRDs::update($rrdFile, $updateStr);
    my $ERR = RRDs::error;
    die "ERROR while updating $rrdFile: $ERR\n" if $ERR;
  }


#..............................................................................
sub fill_rrd ()
  {
    my $rrdTime     = time;
    my $numCpu      = 0;
    my @cpuFreq;

    foreach (`$cpuinfo 2>/dev/null`) {

      #stepping        : 2
      #cpu MHz         : 1000.000
      #cache size      : 512 KB
      #physical id     : 0
      #siblings        : 2
      #core id         : 0
      #cpu cores       : 2

      #print STDERR "[$_]";
      next if (/^[^c]/o);
      #print STDERR "[$_]";

      if (/cpu MHz\s*:\s*(\d+)\.{0,1}/o) {
	$cpuFreq[$numCpu]        = $1;
	
	#print STDERR "cpu$numCpu = [$1]\n";
	$numCpu++;
      }
    }

  # fill database
  if ($numCpu>0) {

    # database dir env exist ?
    my $rrdFile;

    if (exists $ENV{'DATABASEDIR'} ) {
      $rrdFile = $ENV{'DATABASEDIR'} . "/" . "$rrdDatabaseName" . "$numCpu";
    } else {
      $rrdFile = '/var/lib/systemgraph/' . "$rrdDatabaseName" . "$numCpu";
    }
    #print STDERR "$rrdFile";


    create_rrd($rrdFile, $numCpu)         if not -w $rrdFile;

    update_rrd($rrdFile,
	       $rrdTime,
	       \@cpuFreq)                 if -w $rrdFile;
  }
}

#..............................................................................
