#!/usr/bin/perl
#                    rrd_cpustat.pl    fills the cpustat.rrd
#
#                                -  cpu activity stats: user, nice,
#                                   system, idle, iowait, irq, softirq
#                                   values in percent
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
# $Id: rrd_cpustat.pl,v 1.8 2007/11/01 22:21:07 cvspassaun Exp $
##############################################################################
use strict;
use RRDs;

# path to procstat
my $procstat = 'cat /proc/stat';	

my $rrdDatabaseName = 'cpustat.rrd';

#...............................................
#
#cpu  1227054 78431 449961 34720928 149180 9702 40672 0
#cpu0 1227054 78431 449961 34720928 149180 9702 40672 0
#intr 153356093 91684099 113020 0 3 3 0 5 0 1 1 0 0 4053454 0 24475545 3284702 15936 98482 7618628 0 3762 22008452 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0
#ctxt 262895731
#btime 1165824469
#processes 350685
#procs_running 1
#procs_blocked 0
#
#
#...............................................
#cpu  2896294 26375 1895248 273757666 612708 27096 727795
#cpu0 792314 15972 507005 68270148 152786 20957 226639
#cpu1 702834 5857 456278 68525660 164925 2790 127440
#cpu2 712775 2913 479415 68360474 173358 2817 254032
#cpu3 688369 1631 452549 68601383 121639 530 119682
#intr 748218256 699842353 10 0 0 1 0 6 0 1 0 1 0 0 0 494 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 4165 0 0 0 0 0 0 0 349837 0 0 0 0 0 0 0 349836 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 43667815 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 3998366 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 255 0 0 0 0 0 0 0 5116 0 0 0 0 0
#ctxt 1929207493
#btime 1165491562
#processes 639062
#procs_running 1
#procs_blocked 0

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
# fill the database
fill_rrd ();

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

	       'DS:user:GAUGE:'   .($rrdStep*2).':0:U',
	       'DS:nice:GAUGE:'   .($rrdStep*2).':0:U',
	       'DS:system:GAUGE:' .($rrdStep*2).':0:U',
	       'DS:idle:GAUGE:'   .($rrdStep*2).':0:U',
	       'DS:iowait:GAUGE:' .($rrdStep*2).':0:U',
	       'DS:irq:GAUGE:'    .($rrdStep*2).':0:U',
	       'DS:softirq:GAUGE:'.($rrdStep*2).':0:U',

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
sub update_rrd ($$$$$$$$){

  #my $dataTime   = $_[0];
  #my $user       = $_[1];
  #my $nice       = $_[2];
  #my $system     = $_[3];
  #my $idle       = $_[4];
  #my $iowait     = $_[5];
  #my $irq        = $_[6];
  #my $softirq    = $_[7];

  #print STDERR "update: dataTime=$_[0], user=$_[1], nice=$_[2], system=$_[3], idle=$_[4], iowait=$_[5], irq=$_[6], softirq=$_[7]\n";

  RRDs::update("$rrdFile",
	       $_[0].':'.
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

  my $rrdTime     = time;

  my $user        = 0;
  my $nice        = 0;
  my $system      = 0;
  my $idle        = 0;
  my $iowait      = 0;
  my $irq         = 0;
  my $softirq     = 0;

  my $valid       = 0;

  foreach (`$procstat 2>/dev/null`) {

    #print STDERR "[$_]";
    next if (/^[^c]/o);
    #print STDERR "[$_]";
    
    my ($v, $t1, $t2, $t3, $t4, $t5, $t6, $t7) = split;

    if ($v eq 'cpu') {

      #print STDERR "$t1, $t2, $t3, $t4, $t5, $t6, $t7\n";
      $valid  = 1;

      $user    = $t1;
      $nice    = $t2;
      $system  = $t3;
      $idle    = $t4;
      $iowait  = $t5;
      $irq     = $t6;
      $softirq = $t7;

      last;
    }
  }

  # sleep one second and read proc/stat again
  select(undef, undef, undef, 1);
  #sleep 1;

  foreach (`$procstat 2>/dev/null`) {

    #print STDERR "[$_]";

    my ($v, $t1, $t2, $t3, $t4, $t5, $t6, $t7) = split;

    if ($v eq 'cpu') {

      #print STDERR "$t1, $t2, $t3, $t4, $t5, $t6, $t7\n";
      $valid  += 1;

      # suboptimal solution on overruns (if someone has a better solution,
      # please let me know !!!)
      if ($t1 >= $user )   { $t1  -= $user; }
      if ($t2 >= $nice )   { $t2  -= $nice; }
      if ($t3 >= $system ) { $t3  -= $system; }
      if ($t4 >= $idle )   { $t4  -= $idle; }
      if ($t5 >= $iowait ) { $t5  -= $iowait; }
      if ($t6 >= $irq )    { $t6  -= $irq; }
      if ($t7 >= $softirq) { $t7  -= $softirq; }

      my $sum  = $t1 + $t2 + $t3 + $t4 + $t5 + $t6 + $t7;
      #print STDERR "$t1, $t2, $t3, $t4, $t5, $t6, $t7\n";

      $user    = $t1 * 100 / $sum;
      $nice    = $t2 * 100 / $sum;
      $system  = $t3 * 100 / $sum;
      $idle    = $t4 * 100 / $sum;
      $iowait  = $t5 * 100 / $sum;
      $irq     = $t6 * 100 / $sum;
      $softirq = $t7 * 100 / $sum;

      last;
    }
  }


  # fill database
  if ($valid > 1) {

    create_rrd()            if not -w $rrdFile;

    update_rrd($rrdTime,
	       $user,
	       $nice,
	       $system,
	       $idle,
	       $iowait,
	       $irq,
	       $softirq) if -w $rrdFile;
  }
}

#..............................................................................
