#!/usr/bin/perl
#                    rrd_lsof.pl    fills the lsof.rrd
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
# $Id: rrd_lsof.pl,v 1.11 2009/08/07 00:09:21 cvslasan Exp $
##############################################################################
use strict;
use RRDs;

# path to lsof
my $lsof = '/usr/sbin/lsof';	

my $rrdDatabaseName = 'lsof.rrd';

#..............................................................................
# check whether nothing to do or not
if (not -x $lsof) {
  # opensuse
  $lsof = '/usr/bin/lsof';
  if (not -x $lsof) {
    $lsof = '/usr/local/bin/lsof';
    exit(0) if not -x $lsof;
  }
}


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

  # awaiting an update every 60 secs
  my $rrdStep = 60;

  # data source = processes, GAUGE, max 120 sec wait before UNKNOWN,
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
	       'DS:ofiles:GAUGE:'.($rrdStep*2).':0:U',
	       'DS:otcp:GAUGE:'  .($rrdStep*2).':0:U',
	       'DS:oudp:GAUGE:'  .($rrdStep*2).':0:U',

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
sub update_rrd ($$$$){

  #my $dataTime = $_[0];
  #my $ofiles   = $_[1];
  #my $otcp     = $_[2];
  #my $oudp     = $_[3];

  #print STDERR "update: dataTime=$_[0], ofiles=$_[1], otcp=$_[2], oudp=$_[3]\n";

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

  # extract the data from lsof
  my $rrdTime = time;

  #my $processes = 0;
  my $ofiles     = 0;
  my $otcp       = 0;
  my $oudp       = 0;

  open(LSOFPIPE, "$lsof -nPF0 -S 3 |") || die "can't open pipe to $lsof\n";

  # iterate through the output of lsof and count our stuff
  while (<LSOFPIPE>) {

    if (/^f/o) {

      # Count open files.
      $ofiles++;

	  # Count instances of TCP and UDP protocols.
	  if (/PTCP/o) {
	    $otcp++;
	  } elsif (/PUDP/o) {
	    $oudp++;
	  }
    }
    #elsif (/^p/o) {
    #
    # Count process.
    #  $processes++;
    #}
  }

  close (LSOFPIPE);

  # fill database
  create_rrd()         if not -w $rrdFile;
	
  update_rrd($rrdTime,
	     $ofiles,
	     $otcp,
	     $oudp)    if -w $rrdFile;

}

#..............................................................................
