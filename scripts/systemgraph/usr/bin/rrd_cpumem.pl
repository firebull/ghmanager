#!/usr/bin/perl
#                    rrd_cpumem.pl    fills the cpumem.rrd
#
#                                -  how many context switches, interrupts...
#                                -  memory statistic
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
# $Id: rrd_cpumem.pl,v 1.11 2009/09/08 22:51:40 cvslasan Exp $
##############################################################################
use strict;
use RRDs;

my $rrdDatabaseName = 'cpumem2.rrd';

# path to vmstat
my $pgm = '/usr/bin/vmstat';

#..............................................................................
# database dir env exist ?
my $rrdFile;

if (exists $ENV{'DATABASEDIR'} ) {
  $rrdFile = $ENV{'DATABASEDIR'} . "/" . "$rrdDatabaseName";
} else {
  $rrdFile = '/var/lib/systemgraph/' . "$rrdDatabaseName";
}
#print STDERR "$rrdFile";

#..............................................................................
sub create_rrd() {

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

	       'DS:uram:GAUGE:'   .($rrdStep*2).':0:U',
	       'DS:fram:GAUGE:'   .($rrdStep*2).':0:U',
	       'DS:buf:GAUGE:'    .($rrdStep*2).':0:U',
	       'DS:cached:GAUGE:' .($rrdStep*2).':0:U',

	       'DS:uswap:GAUGE:'  .($rrdStep*2).':0:U',
	       'DS:fswap:GAUGE:'  .($rrdStep*2).':0:U',
	       'DS:active:GAUGE:' .($rrdStep*2).':0:U',

	       'DS:interrupts:COUNTER:'.($rrdStep*2).':0:U',
	       'DS:switches:COUNTER:'  .($rrdStep*2).':0:U',
	       'DS:ppi:COUNTER:'       .($rrdStep*2).':0:U',
	       'DS:ppo:COUNTER:'       .($rrdStep*2).':0:U',

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


#..............................................................................
sub update_rrd ($$$$$$$$$$$$){

  #my $dataTime   = $_[0];
  #my $uram       = $_[1];
  #my $fram       = $_[2];
  #my $buf        = $_[3];
  #my $cached     = $_[4];
  #my $uswap      = $_[5];
  #my $fswap      = $_[6];
  #my $active     = $_[7];
  #my $interrupts = $_[8];
  #my $switches   = $_[9];
  #my $ppi        = $_[10];
  #my $ppo        = $_[11];

  #print STDERR "update: dataTime=$_[0], uram=$_[1], fram=$_[2], buf=$_[3], cached=$_[4], uswap=$_[5], fswap=$_[6], active=$_[7], interrupts=$_[8], switches=$_[9], ppi=$_[10], ppo=$_[11]\n";


  RRDs::update("$rrdFile",
	       $_[0].':'.
	       $_[1].':'.
	       $_[2].':'.
	       $_[3].':'.
	       $_[4].':'.
	       $_[5].':'.
	       $_[6].':'.
	       $_[7].':'.
	       $_[8].':'.
	       $_[9].':'.
	       $_[10].':'.
	       $_[11]);
  my $ERR = RRDs::error;
  die "ERROR while updating $rrdFile: $ERR\n" if $ERR;
}

#..............................................................................
# Linux:
#/usr/bin/vmstat -s -S K
#
#      1026564 K total memory
#       567244 K used memory
#       210948 K active memory
#       284096 K inactive memory
#       459320 K free memory
#       184412 K buffer memory
#       265188 K swap cache
#      1951888 K total swap
#            0 K used swap
#      1951888 K free swap
#       239734 non-nice user cpu ticks
#        26141 nice user cpu ticks
#        87973 system cpu ticks
#     15080400 idle cpu ticks
#         6800 IO-wait cpu ticks
#          415 IRQ cpu ticks
#         1313 softirq cpu ticks
#            0 stolen cpu ticks
#       274216 pages paged in
#      1417430 pages paged out
#            0 pages swapped in
#            0 pages swapped out
#     12654732 interrupts
#     24882611 CPU context switches
#   1246918588 boot time
#       152349 forks

sub fill_rrdLinux () {

  my $rrdTime     = time;

  my $uram        = 0;
  my $fram        = 0;
  my $buf         = 0;
  my $cached      = 0;
  my $uswap       = 0;
  my $fswap       = 0;
  my $active      = 0;
  my $interrupts  = 0;
  my $switches    = 0;
  my $ppi         = 0;
  my $ppo         = 0;

  my $valid       = 0;

  foreach (`$pgm -s -S K 2>/dev/null`) {

    #    print STDERR "[$_]";

    #     my ($v, $t1, $t2, $t3) = split;
    #     #print STDERR $v,$t1,$t2,"\n";

    if (/(\d+)\s+K*\s*used memory/o) {
      $uram       = $1;
      $valid++;
    } elsif (/(\d+)\s+K*\s*active memory/o) {
      $active     = $1;
      $valid++;
    } elsif (/(\d+)\s+K*\s*free memory/o) {
      $fram       = $1;
      $valid++;
    } elsif (/(\d+)\s+K*\s*buffer memory/o) {
      $buf        = $1;
      $valid++;
    } elsif (/(\d+)\s+K*\s*swap cache/o) {
      $cached     = $1;
      # used ram correction. comes after used memory, buffer memory, swap cache
      $uram       = $uram - $buf - $cached;
      $valid++;
    } elsif (/(\d+)\s+K*\s*used swap/o) {
      $uswap      = $1;
      $valid++;
    } elsif (/(\d+)\s+K*\s*free swap/o) {
      $fswap      = $1;
      $valid++;
    } elsif (/(\d+)\sinterrupts/o) {
      $interrupts = $1;
      $valid++;
    } elsif (/(\d+)\sCPU context/o) {
      $switches   = $1;
      $valid++;
    } elsif (/(\d+)\spages paged in/o) {
      $ppi        = $1;
      $valid++;
    } elsif (/(\d+)\spages paged out/o) {
      $ppo        = $1;
      $valid++;
    }

    last if ($valid>10);
  }

  # fill database
  if ($valid > 0) {

    create_rrd()            if not -w $rrdFile;

    update_rrd($rrdTime,
	       $uram,
	       $fram,
	       $buf,
	       $cached,
	       $uswap,
	       $fswap,
	       $active,
	       $interrupts,
	       $switches,
	       $ppi,
	       $ppo) if -w $rrdFile;
  }
}

#..............................................................................
# openbsd 4.4
# /usr/bin/vmstat -s
#        4096 bytes per page
#      511916 pages managed
#      424148 pages free
#       27420 pages active
#         629 pages inactive
#           0 pages being paged out
#           1 pages wired
#           0 pages zeroed
#           4 pages reserved for pagedaemon
#           6 pages reserved for kernel
#           0 swap pages
#           0 swap pages in use
#           0 total anon's in system
#           0 free anon's
#    25493360 page faults
#    24898240 traps
#   583192502 interrupts
#   123079479 cpu context switches
#      615006 fpu context switches
#   253130413 software interrupts
#   510493143 syscalls
#           0 pagein operations
#           0 swap ins
#           0 swap outs
#      101105 forks
#          17 forks where vmspace is shared
#         101 kernel map entries
#           0 number of times the pagedaemon woke up
#           0 revolutions of the clock hand
#           0 pages freed by pagedaemon
#           0 pages scanned by pagedaemon
#           0 pages reactivated by pagedaemon
#           0 busy pages found by pagedaemon
#    74365905 total name lookups
#             cache hits (94% pos + 1% neg) system 0% per-directory
#             deletions 0%, falsehits 0%, toolong 0%
#           0 select collisions

sub fill_rrdOpenBSD () {

  my $rrdTime     = time;

  my $uram        = 0;
  my $fram        = 0;
  my $buf         = 0;
  my $cached      = 0;
  my $uswap       = 0;
  my $fswap       = 0;
  my $active      = 0;
  my $interrupts  = 0;
  my $switches    = 0;
  my $ppi         = 0;
  my $ppo         = 0;

  my $KbytesPg    = 4;		
  my $swap        = 0;
  my $valid       = 0;

  foreach (`$pgm -s  2>/dev/null`) {

    #    print STDERR "[$_]";

    #     my ($v, $t1, $t2, $t3) = split;
    #     #print STDERR $v,$t1,$t2,"\n";

    if (/(\d+)\sbytes per page/o) {
      $KbytesPg   = $1/1024;
      $valid++;
    } elsif (/(\d+)\spages managed/o) {
      $uram        = $1 * $KbytesPg;
      $valid++;
    } elsif (/(\d+)\spages free/o) {
      $fram        = $1 * $KbytesPg;
      $valid++;
    } elsif (/(\d+)\spages active/o) {
      $active      = $1 * $KbytesPg;
      $valid++;
    } elsif (/(\d+)\spages inactive/o) {
      $buf         = $1 * $KbytesPg;
      $valid++;
    } elsif (/(\d+)\sswap pages in use/o) {
      # comes after 'swap pages'
      $uswap       = $1 * $KbytesPg;
      $fswap       = $swap - $uswap;
      $valid++;
    } elsif (/(\d+)\sswap pages/o) {
      $swap        = $1 * $KbytesPg;
      $valid++;
    } elsif (/(\d+)\sinterrupts/o) {
      $interrupts = $1;
      $valid++;
    } elsif (/(\d+)\scpu context/o) {
      $switches   = $1;
      $valid++;
    } elsif (/(\d+)\spagein operations/o) {
      $ppi        = $1;
      $valid++;
      #openbsd: no pageout operations
    }

    last if ($valid>9);
  }


  # fill database
  if ($valid > 0) {

    create_rrd()            if not -w $rrdFile;

    update_rrd($rrdTime,
	       $uram,
	       $fram,
	       $buf,
	       $cached,
	       $uswap,
	       $fswap,
	       $active,
	       $interrupts,
	       $switches,
	       $ppi,
	       $ppo) if -w $rrdFile;
  }
}


#..............................................................................
# run

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
