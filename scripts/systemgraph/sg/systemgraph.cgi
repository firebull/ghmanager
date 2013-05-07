#!/usr/bin/perl -T

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
###############################################################################
#
# systemgraph.cgi is based on mailgraph's mailgraph.cgi (c) 2000-2004
# David Schweikert <dws@ee_ethz_ch>
#
#
# $Id: systemgraph.cgi,v 1.104 2010/02/13 20:17:53 cvslasan Exp $
###############################################################################
use strict;

use RRDs;
use POSIX qw(uname);

###############################################################################

# should we die when we get invalid cgi parameters ? if 0 then systemgraph's
# main page will be printed on detected parameter errors instead
# NOTE: rpm-spec-file is able to modify this param during installation
my $dieOnParamErrors = 1;

# normally it's a good idea to print Tobi Oetiker's rrdtool logo/banner on the
# pages like mailgraph and other rrdtool programs. But this results in some
# corporate networks in blocking of the host oss.oetiker.ch after some hours
# NOTE: rpm-spec-file is able to modify this param during installation
my $withRRDTOOL_GIF = 1;

# temporary directory. this is the place where systemgraph.cgi stores the
# images
# NOTE: the rpm-spec-file is able to modify this param during installation
#       the gentoo-ebuild modifies this too
my $systemgraphTmpDir = '/tmp/systemgraph';

# directory of the rrd database files, this is the place where systemgraph.cgi
# reads the rrd files.
# NOTE: the rpm-spec-file is able to modify this param during installation
#       the gentoo-ebuild modifies this too
my $systemgraphRRDDir = '/var/lib/systemgraph';

# the width of the created images
# NOTE: rpm-spec-file is able to modify this param
my $pngXPoints = 500;

# when the time range is greater then the specified value in seconds then the
# line with the max-values will be printed in the graph
my $rangeSecMaxPrint = 36000;

#.............................................................................
# font settings in the created graph pngs

# font which is part of the rrdtool package (1.2.x). This is no longer an
# option for current rrdtool releases !!! because it contains no longer its
# own font (opensuse 10.3)
#my @FFont = ('--font=TITLE:6:/usr/share/rrdtool/fonts/DejaVuSansMono-Roman.ttf', '--font=LEGEND:6:/usr/share/rrdtool/fonts/DejaVuSansMono-Roman.ttf', '--font=AXIS:7:/usr/share/rrdtool/fonts/DejaVuSansMono-Roman.ttf', '--font=UNIT:8:/usr/share/rrdtool/fonts/DejaVuSansMono-Roman.ttf');

# looks nice on Fedora Core <= 5
#my @FFont = ('--font=TITLE:7:/usr/share/fonts/bitstream-vera/VeraMono.ttf', '--font=LEGEND:7:/usr/share/fonts/bitstream-vera/VeraMono.ttf', '--font=AXIS:7:/usr/share/fonts/bitstream-vera/Vera.ttf', '--font=UNIT:8:/usr/share/fonts/bitstream-vera/Vera.ttf');

# looks nice on Fedora Core >5
#my @FFont = ('--font=TITLE:6:/usr/share/fonts/dejavu-lgc/DejaVuLGCSansMono.ttf', '--font=LEGEND:6:/usr/share/fonts/dejavu-lgc/DejaVuLGCSansMono.ttf', '--font=AXIS:7:/usr/share/fonts/dejavu-lgc/DejaVuLGCSans.ttf', '--font=UNIT:8:/usr/share/fonts/dejavu-lgc/DejaVuLGCSans.ttf');

# known to work on ubuntu 7.0.4
#my @FFont = ('--font=TITLE:6:/usr/share/fonts/truetype/ttf-dejavu/DejaVuSansMono.ttf', '--font=LEGEND:6:/usr/share/fonts/truetype/ttf-dejavu/DejaVuSansMono.ttf', '--font=AXIS:7:/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans.ttf', '--font=UNIT:8:/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans.ttf');

# known to work on opensuse 10.3, fedora 8 test3 (part of xorg-x11-fonts-7.2-85....rpm)
#my @FFont = ('--font=TITLE:6:/usr/share/fonts/truetype/luximr.ttf', '--font=LEGEND:6:/usr/share/fonts/truetype/luximr.ttf', '--font=AXIS:7:/usr/share/fonts/truetype/luximr.ttf', '--font=UNIT:8:/usr/share/fonts/truetype/luximr.ttf');

# known to work on gentoo
#my @FFont = ('--font=TITLE:6:/usr/share/fonts/dejavu/DejaVuMonoSans.ttf', '--font=LEGEND:6:/usr/share/fonts/dejavu/DejaVuMonoSans.ttf', '--font=AXIS:7:/usr/share/fonts/dejavu/DejaVuMonoSans.ttf', '--font=UNIT:8:/usr/share/fonts/dejavu/DejaVuMonoSans.ttf');


# using default fonts (no own font settings)
my @FFont = ();


###############################################################################

my $VERSION = "0.9.7.6" ;

my $host = (POSIX::uname())[1];
my $scriptname = 'systemgraph.cgi';

# necessary because of tainting !
$ENV{PATH} = '/bin:/usr/bin';



###############################################################################

my $dataPtr = {
	       'memory'   => { rrd   => "${systemgraphRRDDir}/cpumem2.rrd",
			       title => 'RAM and SWAP',
			     },
	       'cpu'      => { rrd   => "${systemgraphRRDDir}/cpumem2.rrd",
			       title => 'CPU info',
			     },
	       'cpufreq'  => { rrd   => "${systemgraphRRDDir}/cpufreq.rrd",
			       title => 'CPU MHz',
			     },
	       'cpustat'  => { rrd   => "${systemgraphRRDDir}/cpustat.rrd",
			       title => 'CPU activity',
			     },
	       'gameLoad'  => { rrd   => "${systemgraphRRDDir}/gameLoad.rrd",
			       title => 'Players activity',
			     },
	       'ntpdrift' => { rrd   => "${systemgraphRRDDir}/ntpdrift.rrd",
			       title => 'NTP - drift',
			     },
	       'process'  => { rrd   => "${systemgraphRRDDir}/process.rrd",
			       title => 'Number of Processes',
			     },
	       'users'    => { rrd   => "${systemgraphRRDDir}/users.rrd",
			       title => 'Number of Users',
			     },
	       'lsof'     => { rrd   => "${systemgraphRRDDir}/lsof.rrd",
			       title => 'Number of Open Files',
			     },
	       'connlsof' => { rrd   => "${systemgraphRRDDir}/lsof.rrd",
			       title => 'Number of Open TCP/UDP Connections',
			     },
	       'loadavg'  => { rrd   => "${systemgraphRRDDir}/loadavg.rrd",
			       title => 'Load Average',
			     },
	       'disk'     => { rrd   => "${systemgraphRRDDir}/disk.rrd",
			       title => 'Disk Usage',
			     },
	       'hdstat'   => { rrd   => "${systemgraphRRDDir}/hdstat2.rrd",
			       title => 'Disk IO',
			     },
	       'hdtmp'    => { rrd   => "${systemgraphRRDDir}/hdtemp.rrd",
			       title => 'Disk Temperatures',
			     },
	       'fan'      => { rrd   => "${systemgraphRRDDir}/fan.rrd",
			       title => 'FAN Status',
			     },
	       'temp'     => { rrd   => "${systemgraphRRDDir}/temp.rrd",
			       title => 'System Temperatures',
			     },
	       'privoxy'  => { rrd   => "${systemgraphRRDDir}/privoxy.rrd",
			       title => 'Privoxy Blocking Stat',
			     },
	       'net'      => { rrd   => "${systemgraphRRDDir}/net.rrd",
			       title => 'Interface Traffic',
			     },
	       # NOTE: net has one subgraphic: netp (packet statistic) and
	       #       optionally (if concerning iptraf.rrd.xxx or shorewallstats.rrd.xxx
	       #       exist) a neti or nets (detailed protocol statistic)

	       'neti'     => { rrd   => "${systemgraphRRDDir}/iptraf.rrd",
                               title => 'Interface Traffic (iptraf)',
			     },
	       'nets'     => { rrd   => "${systemgraphRRDDir}/shorewallstats.rrd",
                               title => 'Interface Traffic (shorewall)',
			     },

	      };


#............................................................................
# the UDP/TCP ports of interest to be displayed in "createGraphNetIOrS"
# NOTE: these port numbers here must also be defined in the concerning
#       rrd_shorewallstats.pl/rrd_iptraf.pl
#22  ssh
#25  smtp
#53  dns
#80  http
#110 pop3
#119 nntp
#143 imap
#443 https
#873 rsync
my @graphNetPorts = ('22','25','53','80','110', '119','143','443','873');
my @graphNetPortsColors= ('#AFFF54', '#E0D025', '#E04D25', '#200EE0', '#971240', '#A0936F', '#33ADFF', '#AD63B9', '#38A02E');



#............................................................................
# lists of colors used for system health(fan, temperature status)
# index 0 is unused
# 4 elements
my @fanColors  = ('#9E5FEB', '#C09858', '#1E5F8A', '#615136');
my @tempColors = ('#7BA24F', '#69805F', '#904022', '#908722');
my @cpuColors  = ('#4E715F', '#495471', '#5A4371', '#714161');
# 8 elements
my @hdtmpColors= ('#B49A89', '#98B86C', '#69B892', '#9171B8', '#B86993', '#4AB846', '#B88048', '#9FB82E');



#............................................................................
# time ranges used in detail view
my @detailTimeList = ( '4h','36h','7d', '30d','365d');

my @graphs = (
	      { title => 'Last 4 hours',   id => "4h",   },
	      { title => 'Last 36 hours',  id => "36h",  },
	      { title => 'Last Week',      id => "7d",   },
	      { title => 'Last Month',     id => "30d",  },
	      { title => 'Last Year',      id => "365d", },
	     );



#............................................................................
# common defaults of every created rrd graph
my @graphDefaults = (@FFont,
		     '--imgformat', 'PNG',
		     '--lazy',
		     '--width', $pngXPoints,
		     '--color=BACK#DDDDDD',
		     '--color=CANVAS#EBEEDB',
		    );


#'--color=BACK#E0DAC6',
#'--color=CANVAS#E0DCB3',

#'--color=BACK#EBEEDB',
#'--color=CANVAS#DDDDDD',



###############################################################################

sub basicCheckParam ($) {

  # basic cgi parameter checking [thanks to Vincent Deffontaines]
  # max 44 chars,

  #print STDERR "[$_[0]]\n";

  $_[0] =~ /^[\w\-\.\,\|]{0,44}$/o  and return 0;

  print STDERR "basicCheckParam: [$_[0]]\n";

  return -1;
}


###############################################################################
# used for the summary page                  #
##############################################


sub printTitleAndRefHtml ($$) {

  #my $key         = shift;
  #my $timeRange   = shift;

  print '<div style="background: #dddddd; width: 589px">';
  print "<H2>$dataPtr->{$_[0]}{'title'} ($_[1])</H2>\n";
  print "</div>\n";
  print "<P><a href='$scriptname?$_[0]'> <IMG BORDER=\"0\" SRC=\"$scriptname?$_[1]-$_[0]\" ALT=\"systemgraph\"></a>\n";
}

sub printTitleAndRef2Html ($$$$) {

  #my $key         = shift;  # 'net'
  #my $shortcut    = shift;  # 'eth0'
  #my $titlePrefix = shift;  # 'eth0: '
  #my $timeRange   = shift;  #

  #print STDERR  "script=$_[3]-$_[0]-$_[1],$_[2]\n";

  print '<div style="background: #dddddd; width: 589px">';
  print "<H2>$_[2]$dataPtr->{$_[0]}{'title'} ($_[3])</H2>\n";
  print "</div>\n";
  print "<P><a href='$scriptname?$_[0]-$_[1]'> <IMG BORDER=\"0\" SRC=\"$scriptname?$_[3]-$_[0]-$_[1]\" ALT=\"systemgraph\"></a>\n";
}

sub printTitleAndRef3Html ($$$$$) {

  #my $key         = shift;  # 'fan'
  #my $cnt         = shift;  # 3
  #my $shortcut    = shift;  # 'via686a_isa_6000'   we don't like '-'
  #my $titlePrefix = shift;  # 'via686a_isa_6000: '
  #my $timeRange   = shift;  #

  print '<div style="background: #dddddd; width: 589px">';
  print "<H2>$_[3]$dataPtr->{$_[0]}{'title'} ($_[4])</H2>\n";
  print "</div>\n";
  print "<P><a href='$scriptname?$_[0]-$_[1]-$_[2]'> <IMG BORDER=\"0\" SRC=\"$scriptname?$_[4]-$_[0]-$_[1]-$_[2]\" ALT=\"systemgraph\"></a>\n";
}


#..............................................................
sub printSummaryHtml ($) {

  my $timeRange = shift; # '4h';

  #print STDERR  "printSummaryHtml:$_[0]\n";

  print "<H1>System Statistics for $host</H1>\n";

  print "<ul id=\"jump\">\n";
  for my $n (0..$#graphs) {
    print "  <li><a href=\"$scriptname?$graphs[$n]{id}\">$graphs[$n]{title}</a>&nbsp;</li>\n";
  }
  print "</ul>\n";

  # print graphs
  printTitleAndRefHtml('memory',   $timeRange)    if -f $dataPtr->{'memory'}{'rrd'};
  printTitleAndRefHtml('cpu',      $timeRange)    if -f $dataPtr->{'cpu'}{'rrd'};
  printTitleAndRefHtml('cpustat',  $timeRange)    if -f $dataPtr->{'cpustat'}{'rrd'};
  printTitleAndRefHtml('gameLoad',  $timeRange)    if -f $dataPtr->{'gameLoad'}{'rrd'};

  # how many cpus (for cpu frequency)
  {
    my @cpufreqs    = `ls $dataPtr->{'cpufreq'}{'rrd'}* 2>/dev/null`;
    my $cpufreqsrrd =  $dataPtr->{'cpufreq'}{'rrd'};
    foreach my $cpufreq ( @cpufreqs ) {
      if ($cpufreq =~ /^$cpufreqsrrd\.(\d+)$/o ) {
	printTitleAndRef2Html('cpufreq',  $1, "$1 CPUs: ", $timeRange);
      }
    }
  }

  # which sensor/health devices to display ? fan status
  {
    my @fans   = `ls $dataPtr->{'fan'}{'rrd'}* 2>/dev/null`;
    my $fanrrd =  $dataPtr->{'fan'}{'rrd'};
    foreach my $fan ( @fans ) {
      if ($fan =~ /^$fanrrd\.(\d+)\.(\w+)$/o ) {
	printTitleAndRef3Html('fan',  $1, $2, "$2: ", $timeRange);
      }
    }
  }

  # which sensor/health devices to display ? temperature status
  {
    my @temps   = `ls $dataPtr->{'temp'}{'rrd'}* 2>/dev/null`;
    my $temprrd =  $dataPtr->{'temp'}{'rrd'};
    foreach my $temp ( @temps ) {
      if ($temp =~ /^$temprrd\.(\d+)\.(\w+)$/o ) {
	printTitleAndRef3Html('temp',  $1, $2, "$2: ", $timeRange);
      }
    }
  }

  # which disk temperatures to display ?
  {
    my @hdtmps   = `ls $dataPtr->{'hdtmp'}{'rrd'}* 2>/dev/null`;
    my $hdtmprrd = $dataPtr->{'hdtmp'}{'rrd'};
    my $hdtmpStr;
    my $hdtmpTit;
    my $hdtmpNum = 0;
    foreach my $hdtmp ( @hdtmps ) {
      if ($hdtmp =~ /^$hdtmprrd\.(\w+)$/o ) {
	$hdtmpStr= $hdtmpStr . $1 . '.';
	
	if ($hdtmpNum == 0) { $hdtmpTit = $1; }
	else                { $hdtmpTit = $hdtmpTit . ', ' . $1; }
	$hdtmpNum++;
      }
    }
    if ($hdtmpNum > 0) {
      #print STDERR "hdtmpNum=$hdtmpNum, hdtmpStr=$hdtmpStr\n";
      printTitleAndRef3Html('hdtmp', $hdtmpNum, $hdtmpStr, "$hdtmpTit: ", $timeRange);
    }
  }

  # which hdstat devices to display ?
  {
    my @hdstats   = `ls $dataPtr->{'hdstat'}{'rrd'}* 2>/dev/null`;
    my $hdstatrrd =  $dataPtr->{'hdstat'}{'rrd'};
    foreach my $hdstat ( @hdstats ) {
      if ($hdstat =~ /^$hdstatrrd\.(\S+)$/o ) {
	#print STDERR "hdstat=$hdstat, dev=$1\n";
	printTitleAndRef2Html('hdstat',  $1, "$1: ", $timeRange);
      }
    }
  }


  printTitleAndRefHtml('process',  $timeRange)    if -f $dataPtr->{'process'}{'rrd'};

  # exists users database
  printTitleAndRefHtml('users',    $timeRange)    if -f $dataPtr->{'users'}{'rrd'};

  # exists lsof database
  printTitleAndRefHtml('lsof',     $timeRange)    if -f $dataPtr->{'lsof'}{'rrd'};


  printTitleAndRefHtml('loadavg',  $timeRange)    if -f $dataPtr->{'loadavg'}{'rrd'};

  # exists lsof database
  printTitleAndRefHtml('connlsof', $timeRange)    if -f $dataPtr->{'connlsof'}{'rrd'};

  # exists ntp-drift database ?
  printTitleAndRefHtml('ntpdrift', $timeRange)    if -f $dataPtr->{'ntpdrift'}{'rrd'};

  # exists privoxy database ?
  printTitleAndRefHtml('privoxy',  $timeRange)    if -f $dataPtr->{'privoxy'}{'rrd'};

  # which net devices to display ?
  {
    my @nets   = `ls $dataPtr->{'net'}{'rrd'}* 2>/dev/null`;
    my $netrrd =  $dataPtr->{'net'}{'rrd'};
    foreach my $net ( @nets ) {
      if ($net =~ /^$netrrd\.([\w\.]+)$/o ) {
	printTitleAndRef2Html('net',  $1, "$1: ", $timeRange);
      }
    }
  }

  # which disks to display ?
  {
    my @disks   = `ls $dataPtr->{'disk'}{'rrd'}* 2>/dev/null`;
    my $diskrrd = $dataPtr->{'disk'}{'rrd'};
    foreach my $disk ( @disks ) {
      if ($disk =~ /^$diskrrd\.([\w\-\,\|]+)\.(.+)$/o ) {
	my $fDev   = "$1.$2";
	my $dev    = $1;
	my $extDev = $2;
	my $diskTit= "$extDev ($dev)";

	# convert ',' back to '/'
	$diskTit   =~ s:\,:/:og;
	# convert '|' back to ':'
	$diskTit   =~ s/\|/:/og;
	#printf STDERR "[$dev][extDev=$extDev][fDev=$fDev][diskTit=$diskTit]\n";
	printTitleAndRef2Html('disk', $fDev, "$diskTit: ", $timeRange);
	
      } elsif ($disk =~ /^$diskrrd\.([\w\-\,\|]+)$/o ) {
	my $fDev   = $1;
	my $diskTit= $fDev;

	# convert ',' back to '/'
	$diskTit   =~ s:\,:/:og;
	# convert '|' back to ':'
	$diskTit   =~ s/\|/:/og;
	#printf STDERR "[fDev=$fDev][diskTit=$diskTit]\n";
	printTitleAndRef2Html('disk', $fDev, "$diskTit: ", $timeRange);
      }
    }
  }
}



###############################################################################
# used for the concerning detail pages    #
###########################################


sub printDetail1Html ($) {
  #my $key            = shift;

  print "<H1>System Statistics for $host</H1>\n";

  for my $n (0..$#detailTimeList) {

    print '<div style="background: #dddddd; width: 589px">';
    print "<H2>$dataPtr->{$_[0]}{'title'} ($detailTimeList[$n])</H2>\n";
    print "</div>\n";
    print "<P><IMG BORDER=\"0\" SRC=\"$scriptname?${detailTimeList[$n]}-$_[0]\" ALT=\"systemgraph\">\n";

  }
  # script params:
  # <timerange>-<key>
  #
  # <timerange>-lsof
  # <timerange>-privoxy ...
}


sub printDetail2Html ($$) {
  #my $key         = shift;   # 'cpufreq'
  #my $shortcut    = shift    # '2'  number of Cpus

  print "<H1>System Statistics for $host</H1>\n";

  for my $n (0..$#detailTimeList) {
    print '<div style="background: #dddddd; width: 589px">';
    print "<H2>$dataPtr->{$_[0]}{'title'} ($detailTimeList[$n])</H2>\n";
    print "</div>\n";
    print "<P><IMG BORDER=\"0\" SRC=\"$scriptname?${detailTimeList[$n]}-$_[0]-$_[1]\" ALT=\"systemgraph\">\n";

  }
  # script params:
  # <timerange>-<key>-<shortcut>
  #
}

sub printDetail2ExtHtml ($$) {
  #my $key         = shift;   # 'hdstat'
  #my $shortcut    = shift    # 'sda'  device name

  print "<H1>System Statistics for $host</H1>\n";

  for my $n (0..$#detailTimeList) {
    print '<div style="background: #dddddd; width: 589px">';
    print "<H2>$_[1]: $dataPtr->{$_[0]}{'title'} ($detailTimeList[$n])</H2>\n";
    print "</div>\n";
    print "<P><IMG BORDER=\"0\" SRC=\"$scriptname?${detailTimeList[$n]}-$_[0]-$_[1]\" ALT=\"systemgraph\">\n";

  }
  # script params:
  # <timerange>-<key>-<shortcut>
  #
}

sub printDetail3Html ($$$) {
  #my $key         = shift;   # 'fan' or 'temp'
  #my $num         = shift;   # number of fans/temps
  #my $shortcut    = shift    # 'via686a_isa_6000'

  #print STDERR join(':', @_), "\n";

  print "<H1>System Statistics for $host</H1>\n";

  for my $n (0..$#detailTimeList) {
    print '<div style="background: #dddddd; width: 589px">';
    print "<H2>$_[2]: $dataPtr->{$_[0]}{'title'} ($detailTimeList[$n])</H2>\n";
    print "</div>\n";
    print "<P><IMG BORDER=\"0\" SRC=\"$scriptname?${detailTimeList[$n]}-$_[0]-$_[1]-$_[2]\" ALT=\"systemgraph\">\n";

  }
  # script params:
  # <timerange>-<key>-<num>-<shortcut>
  #
  # <timerange>-fan-4-<shortcut>
  # <timerange>-temp-2-<shortcut>
  #
}


sub printDetailNetHtml ($) {
  #my $shortcut    = shift    # 'eth0'

  print "<H1>System Statistics for $host</H1>\n";

  for my $n (0..$#detailTimeList) {

    # print bit/s statistic
    print '<div style="background: #dddddd; width: 589px">';
    print "<H2>$_[0]: $dataPtr->{'net'}{'title'} ($detailTimeList[$n])</H2>\n";
    print "</div>\n";
    print "<P><IMG BORDER=\"0\" SRC=\"$scriptname?${detailTimeList[$n]}-net-$_[0]\" ALT=\"systemgraph\">\n";

    # print packet statistic
    print '<div style="background: #dddddd; width: 100px">';
    print "</div>\n";
    print "<P><IMG BORDER=\"0\" SRC=\"$scriptname?${detailTimeList[$n]}-netp-$_[0]\" ALT=\"systemgraph\">\n";

    # print shorewall statistic when available
    #print STDERR "$dataPtr->{'nets'}{'rrd'}.$_[0]\n";
    # otherwise print iptraf statistic when available
    #print STDERR "$dataPtr->{'neti'}{'rrd'}.$_[0]\n";

    if ( -r "$dataPtr->{'nets'}{'rrd'}.$_[0]" ) {
      print '<div style="background: #dddddd; width: 200px">';
      print "</div>\n";
      print "<P><IMG BORDER=\"0\" SRC=\"$scriptname?${detailTimeList[$n]}-nets-$_[0]\" ALT=\"systemgraph\">\n";
    } elsif ( -r "$dataPtr->{'neti'}{'rrd'}.$_[0]" ) {
      print '<div style="background: #dddddd; width: 200px">';
      print "</div>\n";
      print "<P><IMG BORDER=\"0\" SRC=\"$scriptname?${detailTimeList[$n]}-neti-$_[0]\" ALT=\"systemgraph\">\n";
    }
  }
  # script params:
  # <timerange>-net-<shortcut>
  # <timerange>-netp-<shortcut>
  # <timerange>-nets-<shortcut>
  # <timerange>-neti-<shortcut>
}

sub printDetailDiskHtml ($) {
  my $disk     = shift;    # 'hda5.,usr'

  print "<H1>System Statistics for $host</H1>\n";

  my $diskTit;
  # create title and extract partition
  if      ($disk =~ /^([\w\-\,\|]+)\.(.+)$/o ) {
    $diskTit     = "$2 ($1)";
  } elsif ($disk =~ /^([\w\-\,\|]+)$/o ) {
    $diskTit     = $1;
  }

  # convert ',' back to '/'
  $diskTit   =~ s:\,:/:og;
  # convert '|' back to ':'
  $diskTit   =~ s/\|/:/og;

  for my $n (0..$#detailTimeList) {
    print '<div style="background: #dddddd; width: 589px">';
    print "<H2>$diskTit: $dataPtr->{'disk'}{'title'} ($detailTimeList[$n])</H2>\n";
    print "</div>\n";
    print "<P><IMG BORDER=\"0\" SRC=\"$scriptname?${detailTimeList[$n]}-disk-$disk\" ALT=\"systemgraph\">\n";

  }
  # script params:
  # <timerange>-disk-<diskdev/extended diskdev>
  #
  # <timerange>-disk-hda1.,boot        --> /boot (hda1)
  # <timerange>-disk-hda2.,Special-FS  --> /Special-Fs (hda2)
}

sub printDetailHdtmpHtml ($$) {
  my $num          = shift;   # number of hds
  my $dotted       = shift;   # 'hda.hdb.'

  #print STDERR join(':', @_), "\n";

  print "<H1>System Statistics for $host</H1>\n";

  my $hdtmpTit     = $dotted;
  $hdtmpTit        =~ s/\.$//og;
  $hdtmpTit        =~ s/\./\,/og;

  for my $n (0..$#detailTimeList) {
    print '<div style="background: #dddddd; width: 589px">';
    print "<H2>$hdtmpTit: $dataPtr->{'hdtmp'}{'title'} ($detailTimeList[$n])</H2>\n";
    print "</div>\n";
    print "<P><IMG BORDER=\"0\" SRC=\"$scriptname?${detailTimeList[$n]}-hdtmp-$num-$dotted\" ALT=\"systemgraph\">\n";

  }
  # script params:
  # <timerange>-hdtmp-<num>-<dottedhds>
  #
  # <timerange>-hdtmp-2-hda.sda.
  # <timerange>-hdtmp-1-sda.
  #
}





###############################################################################
sub printHtml ($) {

  print "Content-Type: text/html\n\n";

  print <<HEADER;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<HTML>
    <HEAD>
     <TITLE>$host: systemgraph</TITLE>
     <META NAME="description"   CONTENT="systemgraph">
     <META NAME="ROBOTS"        CONTENT="NOARCHIVE">
     <META HTTP-EQUIV="Refresh" CONTENT="300">
     <META HTTP-EQUIV="Pragma"  CONTENT="no-cache">
     <META NAME="keywords"      CONTENT="Systemgraph">
     <META NAME="keywords"      CONTENT="http://www.decagon.de/sw/systemgraph/">
     <STYLE TYPE="text/css">
       #jump    { margin: 0 0 10px 4px }
       #jump li { list-style: none; display: inline;
                  font-size: 90%; }
       #jump li:after            { content: "|"; }
       #jump li:last-child:after { content: ""; }
     </STYLE>


</HEAD>
<BODY BGCOLOR="#DBD8B6">
HEADER

  #...................................................................
  if ($_[0] eq '') {

    #overall statistic summary
    printSummaryHtml('4h');
  }
  elsif ($_[0] =~ /^XX-(\d+[smhd])/o) {
    #overall statistic summary specific timerange
    printSummaryHtml($1);
  }
  elsif ($_[0] eq 'users' )                 {    printDetail1Html('users');   }
  elsif ($_[0] eq 'process' )               {    printDetail1Html('process'); }
  elsif ($_[0] eq 'memory' )                {    printDetail1Html('memory');  }
  elsif ($_[0] eq 'cpu' )                   {    printDetail1Html('cpu');     }
  elsif ($_[0] eq 'gameLoad' )              {    printDetail1Html('gameLoad'); }
  elsif ($_[0] eq 'cpustat' )               {    printDetail1Html('cpustat'); }
  elsif ($_[0] eq 'lsof' )                  {    printDetail1Html('lsof');    }
  elsif ($_[0] eq 'loadavg' )               {    printDetail1Html('loadavg',);}
  elsif ($_[0] eq 'connlsof' )              {    printDetail1Html('connlsof');}
  elsif ($_[0] eq 'ntpdrift' )              {    printDetail1Html('ntpdrift');}
  elsif ($_[0] eq 'privoxy' )               {    printDetail1Html('privoxy'); }

  elsif ($_[0] =~ /cpufreq-(\d+)$/o )         {    printDetail2Html('cpufreq', $1);}

  elsif ($_[0] =~ /hdstat-(\S+)$/o )          {    printDetail2ExtHtml('hdstat', $1);}

  elsif ($_[0] =~ /net-([\w\.]+)$/o )         {    printDetailNetHtml($1);  }
  elsif ($_[0] =~ /disk-([\w\.\-\,\|]+)$/o )  {    printDetailDiskHtml($1); }

  elsif ($_[0] =~ /fan-(\d+)-(\w+)$/o )       {    printDetail3Html('fan',   $1,$2);}
  elsif ($_[0] =~ /temp-(\d+)-(\w+)$/o )      {    printDetail3Html('temp',  $1,$2);}

  elsif ($_[0] =~ /hdtmp-(\d+)-([\w\.]+)$/o ) {    printDetailHdtmpHtml($1,$2);}

  #...................................................................

  print <<FOOTER1;

<hr width="589" align="left" size="1" noshade>

<table border="0" width="589" cellpadding="0" cellspacing="0" background="#dddddd">
 <tr>
  <td ALIGN="left">
    <A href="http://www.decagon.de/sw/systemgraph" target="_blank">Systemgraph</A>
    $VERSION by
    <A href="mailto:j.schlick_at_decagon.de">Jochen Schlick</A>
 </td>
FOOTER1

  if ($withRRDTOOL_GIF == 1) {

    ################################################################################
    # Thanx to our corporate firewall/viruswall ...the host oss.oetiker.ch will    #
    # be automatically blocked after some hours....                                #
    ################################################################################
    print <<FOOTER2A;

<td ALIGN="right">
    <A HREF="http://oss.oetiker.ch/rrdtool/">
    <img border="0" src="http://oss.oetiker.ch/rrdtool/.pics/rrdtool.gif" alt="" width="120" height="34">
    </A>
</td>
FOOTER2A

  }
  else {
    print <<FOOTER2B;

<td ALIGN="right">
    <A HREF="http://oss.oetiker.ch/rrdtool/" target="_blank">RRDTool</a>
    </A>
</td>
FOOTER2B

  }
  print <<FOOTER3;

 </tr>
</table>

</BODY>
</HTML>
FOOTER3

}

########################################################################
# conveniance stuff

sub getLocalTimeForComment()
{
  my $date = localtime(time);
  $date =~ s|:|\\:|og unless $RRDs::VERSION < 1.199908;

  return $date;
}

########################################################################


sub createGraphMemory ($$$) {
  my $timeRange = shift;
  my $rangeSec  = shift;
  my $pngDir    = shift;

  my $filerrd = $dataPtr->{'memory'}{'rrd'};
  my $filepng = "$pngDir" . 'memory' . "$timeRange" . '.png';

  #print STDERR "timeRange=$timeRange, [pngDir=$pngDir], [filepng=$filepng], [rangeSec=$rangeSec]";

  #NOTE: memory data is in kB

  my $date = localtime(time);
  my ($graphret,$xs,$ys) = RRDs::graph("$filepng",
				       @graphDefaults,
				       '--height', 95,
				       '--start', "-$rangeSec",
				       '--vertical-label', 'bytes (MB)',
				       '--watermark', "$date",
				       '-b', 1024,
			
					 "DEF:uram=$filerrd:uram:AVERAGE",
					 "DEF:fram=$filerrd:fram:AVERAGE",
					 "DEF:buf=$filerrd:buf:AVERAGE",
					 "DEF:cached=$filerrd:cached:AVERAGE",
					 "DEF:uswap=$filerrd:uswap:AVERAGE",
					 "DEF:fswap=$filerrd:fswap:AVERAGE",
					
					 "DEF:maxuram=$filerrd:uram:MAX",
					 "DEF:maxfram=$filerrd:fram:MAX",
					 "DEF:maxbuf=$filerrd:buf:MAX",
					 "DEF:maxcached=$filerrd:cached:MAX",
					 "DEF:maxuswap=$filerrd:uswap:MAX",
					 "DEF:maxfswap=$filerrd:fswap:MAX",

					 'CDEF:uramM=uram,1024,/,1000,*,1024,/',
					 'CDEF:framM=fram,1024,/,1000,*,1024,/',
					 'CDEF:bufM=buf,1024,/,1000,*,1024,/',
					 'CDEF:cachedM=cached,1024,/,1000,*,1024,/',
					 'CDEF:uswapM=uswap,1024,/,1000,*,1024,/',
					 'CDEF:fswapM=fswap,1024,/,1000,*,1024,/',

					 'CDEF:gsumM=uramM,framM,+,bufM,+,cachedM,+,uswapM,+,fswapM,+',
					 'CDEF:usedsumM=uramM,uswapM,+,',

					 'CDEF:maxuramM=maxuram,1024,/,1000,*,1024,/',
					 'CDEF:maxframM=maxfram,1024,/,1000,*,1024,/',
					 'CDEF:maxbufM=maxbuf,1024,/,1000,*,1024,/',
					 'CDEF:maxcachedM=maxcached,1024,/,1000,*,1024,/',
					 'CDEF:maxuswapM=maxuswap,1024,/,1000,*,1024,/',
					 'CDEF:maxfswapM=maxfswap,1024,/,1000,*,1024,/',


					 'COMMENT:                        Avg\:          Max\:         Last\:\n',

					 'AREA:uramM#fc816e:used RAM\:    :STACK',
					 'GPRINT:uramM:AVERAGE: %12.2lf',
					 'GPRINT:maxuramM:MAX:%12.2lf',
					 'GPRINT:uramM:LAST:%12.2lf MB\l',
					
					 'AREA:uswapM#f4b37a:used SWAP\:    :STACK',
					 'GPRINT:uswapM:AVERAGE:%12.2lf',
					 'GPRINT:maxuswapM:MAX:%12.2lf',
					 'GPRINT:uswapM:LAST:%12.2lf MB\l',

					 'AREA:framM#A2E973:free RAM\:    :STACK',
					 'GPRINT:framM:AVERAGE: %12.2lf',
					 'GPRINT:maxframM:MAX:%12.2lf',
					 'GPRINT:framM:LAST:%12.2lf MB\l',

					 'AREA:bufM#D9D9A5:buffers\:     :STACK',
					 'GPRINT:bufM:AVERAGE: %12.2lf',
					 'GPRINT:maxbufM:MAX:%12.2lf',
					 'GPRINT:bufM:LAST:%12.2lf MB\l',

					 'AREA:cachedM#B5CB7F:cached\:      :STACK',
					 'GPRINT:cachedM:AVERAGE: %12.2lf',
					 'GPRINT:maxcachedM:MAX:%12.2lf',
					 'GPRINT:cachedM:LAST:%12.2lf MB\l',

					 'AREA:fswapM#C7EDBF:free SWAP\:    :STACK',
					 'GPRINT:fswapM:AVERAGE:%12.2lf',
					 'GPRINT:maxfswapM:MAX:%12.2lf',
					 'GPRINT:fswapM:LAST:%12.2lf MB\l',

					 'LINE1:usedsumM#f78b50:',
					 'LINE1:gsumM#95B18F:',				
					);
    my $ERR=RRDs::error;
    die "ERROR: $ERR\n" if $ERR;

    #return the png file
    return $filepng;
  }



#########################################################################
sub createGraphCpu($$$)
  {
    my $timeRange = shift;
    my $rangeSec  = shift;
    my $pngDir    = shift;

    my $filerrd = $dataPtr->{'cpu'}{'rrd'};
    my $filepng = "$pngDir" . 'cpu' . "$timeRange" . '.png';

    #print STDERR "timeRange=$timeRange, [pngDir=$pngDir], [filepng=$filepng], [rangeSec=$rangeSec]";

    my $date = localtime(time);
    my ($graphret,$xs,$ys) = RRDs::graph("$filepng",
					 @graphDefaults,
					 '--height', 95,
					 '--start', "-$rangeSec",
					 '--vertical-label', 'per sec',
					 '--watermark', "$date",
					 '-b', 1000,
					
					 "DEF:sw=$filerrd:switches:AVERAGE",
					 "DEF:maxsw=$filerrd:switches:MAX",
					 "DEF:inter=$filerrd:interrupts:AVERAGE",
					 "DEF:maxinter=$filerrd:interrupts:MAX",
					 "DEF:ppi=$filerrd:ppi:AVERAGE",
					 "DEF:maxppi=$filerrd:ppi:MAX",
					 "DEF:ppo=$filerrd:ppo:AVERAGE",
					 "DEF:maxppo=$filerrd:ppo:MAX",

					 'COMMENT:                        Avg\:          Max\:         Last\:\n',


					 'AREA:sw#EEAF6C:ctx-switch\:',
					 'GPRINT:sw:AVERAGE:%12.2lf',
					 'GPRINT:maxsw:MAX:%12.2lf',
					 'GPRINT:sw:LAST:%12.2lf\l',
					
					 'AREA:inter#E0DD8D:interrupts\:',
					 'GPRINT:inter:AVERAGE:%12.2lf',
					 'GPRINT:maxinter:MAX:%12.2lf',
					 'GPRINT:inter:LAST:%12.2lf\l',

					 'LINE1:inter#C3BF46:',
					 'LINE1:sw#BE7344:',

					 'LINE1:ppi#4B5DFF:pages in\:  ',
					 'GPRINT:ppi:AVERAGE:%12.2lf',
					 'GPRINT:maxppi:MAX:%12.2lf',
					 'GPRINT:ppi:LAST:%12.2lf\l',
					
					 'LINE1:ppo#35FF1A:pages out\: ',
					 'GPRINT:ppo:AVERAGE:%12.2lf',
					 'GPRINT:maxppo:MAX:%12.2lf',
					 'GPRINT:ppo:LAST:%12.2lf\l',
					);
    my $ERR=RRDs::error;
    die "ERROR: $ERR\n" if $ERR;

    #return the png file
    return $filepng;
  }


#########################################################################
sub createGraphCpuStat($$$)
  {
    my $timeRange = shift;
    my $rangeSec  = shift;
    my $pngDir    = shift;

    my $filerrd = $dataPtr->{'cpustat'}{'rrd'};
    my $filepng = "$pngDir" . 'cpustat' . "$timeRange" . '.png';

    #print STDERR "timeRange=$timeRange, [pngDir=$pngDir], [filepng=$filepng], [rangeSec=$rangeSec]";

    my $date = localtime(time);
    my ($graphret,$xs,$ys) = RRDs::graph("$filepng",
					 @graphDefaults,
					 '--height', 100,
					 '--start', "-$rangeSec",
					 '--vertical-label', 'CPU Activity %',
					 '--watermark', "$date",
					 '-b', 1000,

					 "DEF:user=$filerrd:user:AVERAGE",
					 "DEF:maxuser=$filerrd:user:MAX",
					 "DEF:nice=$filerrd:nice:AVERAGE",
					 "DEF:maxnice=$filerrd:nice:MAX",
					 "DEF:system=$filerrd:system:AVERAGE",
					 "DEF:maxsystem=$filerrd:system:MAX",
					 "DEF:idle=$filerrd:idle:AVERAGE",
					 "DEF:maxidle=$filerrd:idle:MAX",
					 "DEF:iowait=$filerrd:iowait:AVERAGE",
					 "DEF:maxiowait=$filerrd:iowait:MAX",
					 "DEF:irq=$filerrd:irq:AVERAGE",
					 "DEF:maxirq=$filerrd:irq:MAX",
					 "DEF:softirq=$filerrd:softirq:AVERAGE",
					 "DEF:maxsoftirq=$filerrd:softirq:MAX",

					 'COMMENT:                        Avg\:          Max\:         Last\:\n',

					 'AREA:system#F68C65:system\:    ',
					 'GPRINT:system:AVERAGE:%12.2lf',
					 'GPRINT:maxsystem:MAX:%12.2lf',
					 'GPRINT:system:LAST:%12.2lf\l',


					 'AREA:user#DABE73:user\:      :STACK',
					 'GPRINT:user:AVERAGE:%12.2lf',
					 'GPRINT:maxuser:MAX:%12.2lf',
					 'GPRINT:user:LAST:%12.2lf\l',

					 'AREA:nice#B18460:nice\:      :STACK',
					 'GPRINT:nice:AVERAGE:%12.2lf',
					 'GPRINT:maxnice:MAX:%12.2lf',
					 'GPRINT:nice:LAST:%12.2lf\l',


					
					 'AREA:iowait#3BA1F5:iowait\:    :STACK',
					 'GPRINT:iowait:AVERAGE:%12.2lf',
					 'GPRINT:maxiowait:MAX:%12.2lf',
					 'GPRINT:iowait:LAST:%12.2lf\l',

					 'AREA:irq#EDAAEA:irq\:       :STACK',
					 'GPRINT:irq:AVERAGE:%12.2lf',
					 'GPRINT:maxirq:MAX:%12.2lf',
					 'GPRINT:irq:LAST:%12.2lf\l',

					 'AREA:softirq#E372B4:softirq\:   :STACK',
					 'GPRINT:softirq:AVERAGE:%12.2lf',
					 'GPRINT:maxsoftirq:MAX:%12.2lf',
					 'GPRINT:softirq:LAST:%12.2lf\l',

					 'AREA:idle#ACDC68:idle\:      :STACK',
					 'GPRINT:idle:AVERAGE:%12.2lf',
					 'GPRINT:maxidle:MAX:%12.2lf',
					 'GPRINT:idle:LAST:%12.2lf\l',

					 'LINE1:system#B16549:',
					
					 'LINE1:user#988550::STACK',
					 'LINE1:nice#997253::STACK',

					 'LINE1:iowait#2D7CBC::STACK',
					 'LINE1:irq#AC7BAA::STACK',
					 'LINE1:softirq#B859A5::STACK',
					 'LINE1:idle#69B52B::STACK',
                                       );
    my $ERR=RRDs::error;
    die "ERROR: $ERR\n" if $ERR;

    #return the png file
    return $filepng;
  }



#########################################################################
### Added by Bulkin ###
sub createGraphGameLoad($$$)
  {
    my $timeRange = shift;
    my $rangeSec  = shift;
    my $pngDir    = shift;

    my $filerrd = $dataPtr->{'gameLoad'}{'rrd'};
    my $filepng = "$pngDir" . 'gameLoad' . "$timeRange" . '.png';

    #print STDERR "timeRange=$timeRange, [pngDir=$pngDir], [filepng=$filepng], [rangeSec=$rangeSec]";

    my $date = localtime(time);
    my ($graphret,$xs,$ys) = RRDs::graph("$filepng",
					 @graphDefaults,
					 '--height', 100,
					 '--start', "-$rangeSec",
					 '--vertical-label', 'Players Activity',
					 '--watermark', "$date",
					 '-b', 1000,

					 "DEF:servers=$filerrd:servers:AVERAGE",
					 "DEF:maxservers=$filerrd:servers:MAX",
					 "DEF:slots=$filerrd:slots:AVERAGE",
					 "DEF:maxslots=$filerrd:slots:MAX",
					 "DEF:players=$filerrd:players:AVERAGE",
					 "DEF:maxplayers=$filerrd:players:MAX",

					 'COMMENT:                                    Avg\:          Max\:         Last\:\n',



					 #'AREA:slots#DABE73:Действительных слотов\:',
					 'COMMENT:  Действительных слотов\:',
					 'GPRINT:slots:AVERAGE:%12.0lf',
					 'GPRINT:maxslots:MAX:%12.0lf',
					 'GPRINT:slots:LAST:%12.0lf\l',

					 'AREA:servers#FFCC66:Серверов запущено\:    ',
					 #'LINE1:servers#FF1111:Серверов запущено\:    ',
					 'GPRINT:servers:AVERAGE:%12.0lf',
					 'GPRINT:maxservers:MAX:%12.0lf',
					 'GPRINT:servers:LAST:%12.0lf\l',


					 'AREA:players#FF9900:Игроков на серверах\:  ',
					 'GPRINT:players:AVERAGE:%12.0lf',
					 'GPRINT:maxplayers:MAX:%12.0lf',
					 'GPRINT:players:LAST:%12.0lf\l',


					 #'LINE1:slots#EAAE63:',
					 'LINE1:servers#FF6600:',
					 'LINE1:players#CC6600:'
					 

                                       );
    my $ERR=RRDs::error;
    die "ERROR: $ERR\n" if $ERR;

    #return the png file
    return $filepng;
  }



#########################################################################
sub createGraphProcess($$$)
  {
    my $timeRange = shift;
    my $rangeSec  = shift;
    my $pngDir    = shift;

    my $filerrd = $dataPtr->{'process'}{'rrd'};
    my $filepng = "$pngDir" . 'process' . "$timeRange" . '.png';

    #print STDERR "timeRange=$timeRange, [pngDir=$pngDir], [filepng=$filepng], [rangeSec=$rangeSec]";

    # parameters for RRDs::graph
    my $date = localtime(time);
    my   @graphParam = ("$filepng",
			@graphDefaults,
			'--height', 60,
			'--start', "-$rangeSec",
			'--vertical-label=processes',
			'--watermark', "$date",
			'-b', 1000,
			
			"DEF:proc=$filerrd:processes:AVERAGE",
			"DEF:maxproc=$filerrd:processes:MAX",

			'AREA:proc#B2B6E0:processes:',
			'GPRINT:proc:AVERAGE:Avg\: %.2lf',
			'GPRINT:maxproc:MAX:Max\: %.2lf',
			'GPRINT:proc:LAST:Last\: %.2lf\l',
		       );


    push(@graphParam, 'LINE1:maxproc#B2B6E0:') if ($rangeSec > $rangeSecMaxPrint);
    push(@graphParam, 'LINE1:proc#676FFF:');

    my ($graphret,$xs,$ys) = RRDs::graph @graphParam;
    my $ERR=RRDs::error;
    die "ERROR: $ERR\n" if $ERR;

    #return the png file
    return $filepng;
  }



#########################################################################
sub createGraphUsers($$$)
  {
    my $timeRange = shift;
    my $rangeSec  = shift;
    my $pngDir    = shift;

    my $filerrd = $dataPtr->{'users'}{'rrd'};
    my $filepng = "$pngDir" . 'users' . "$timeRange" . '.png';

    #print STDERR "timeRange=$timeRange, [pngDir=$pngDir], [filepng=$filepng], [rangeSec=$rangeSec]";

    # parameters for RRDs::graph
    my $date = localtime(time);
    my   @graphParam = ("$filepng",
			@graphDefaults,
			'--height', 60,
			'--start', "-$rangeSec",
			'--vertical-label=users',
			'--watermark', "$date",
			'-b', 1000,
			
			"DEF:us=$filerrd:users:AVERAGE",
			"DEF:maxus=$filerrd:users:MAX",

			'AREA:us#D1B6E0:users:',
			'GPRINT:us:AVERAGE:Avg\: %.2lf',
			'GPRINT:maxus:MAX:Max\: %.2lf',
			'GPRINT:us:LAST:Last\: %.2lf\l',
		       );


    push(@graphParam, 'LINE1:maxus#BC90E0:') if ($rangeSec > $rangeSecMaxPrint);
    push(@graphParam, 'LINE1:us#9F7ABE:');

    my ($graphret,$xs,$ys) = RRDs::graph @graphParam;
    my $ERR=RRDs::error;
    die "ERROR: $ERR\n" if $ERR;

    #return the png file
    return $filepng;
  }



#########################################################################
sub createGraphLsof($$$)
  {
    my $timeRange = shift;
    my $rangeSec  = shift;
    my $pngDir    = shift;

    my $filerrd = $dataPtr->{'lsof'}{'rrd'};
    my $filepng = "$pngDir" . 'lsof' . "$timeRange" . '.png';

    #print STDERR "timeRange=$timeRange, [pngDir=$pngDir], [filepng=$filepng], [rangeSec=$rangeSec]";

    # parameters for RRDs::graph
    my $date = localtime(time);
    my   @graphParam = ("$filepng",
			@graphDefaults,
			'--height', 80,
			'--start', "-$rangeSec",
			'--vertical-label=lsof',
			'--watermark', "$date",
			'-b', 1000,
			
			"DEF:ofiles=$filerrd:ofiles:AVERAGE",
			"DEF:maxofiles=$filerrd:ofiles:MAX",

			'AREA:ofiles#E0ACD0:open files\: ',
			'GPRINT:ofiles:AVERAGE:Avg\: %.2lf',
			'GPRINT:maxofiles:MAX:Max\: %.2lf',
			'GPRINT:ofiles:LAST:Last\: %.2lf\l',
		       );


    push(@graphParam, 'LINE1:maxofiles#C3A376:') if ($rangeSec > $rangeSecMaxPrint);
    push(@graphParam, 'LINE1:ofiles#AB8F67:');

    my ($graphret,$xs,$ys) = RRDs::graph @graphParam;
    my $ERR=RRDs::error;
    die "ERROR: $ERR\n" if $ERR;

    #return the png file
    return $filepng;
  }



#########################################################################
sub createGraphLoadavg($$$)
  {
    my $timeRange = shift;
    my $rangeSec  = shift;
    my $pngDir    = shift;

    my $filerrd = $dataPtr->{'loadavg'}{'rrd'};
    my $filepng = "$pngDir" . 'loadavg' . "$timeRange" . '.png';

    #print STDERR "timeRange=$timeRange, [pngDir=$pngDir], [filepng=$filepng], [rangeSec=$rangeSec]";

    my $date = localtime(time);
    my ($graphret,$xs,$ys) = RRDs::graph("$filepng",
					 @graphDefaults,
					 '--height', 90,
					 '--start', "-$rangeSec",
					 '--vertical-label', 'Average Load',
					 '--watermark', "$date",
					 '-b', 1000,
					
					 "DEF:load1=$filerrd:load1:AVERAGE",
					 "DEF:load5=$filerrd:load5:AVERAGE",
					 "DEF:load15=$filerrd:load15:AVERAGE",

					 'AREA:load1#ff0000:1 minute,   Last\:',
					 'GPRINT:load1:LAST:%4.2lf\l',
					 'AREA:load5#ff9900:5 minutes,  Last\:',
					 'GPRINT:load5:LAST:%4.2lf\l',
					 'AREA:load15#ffff00:15 minutes, Last\:',
					 'GPRINT:load15:LAST:%4.2lf\l',
					 'LINE1:load5#ff9900:',
					 'LINE1:load1#ff0000:',
					);
    my $ERR=RRDs::error;
    die "ERROR: $ERR\n" if $ERR;

    #return the png file
    return $filepng;
  }



#########################################################################
sub createGraphConnlsof($$$)
  {
    my $timeRange = shift;
    my $rangeSec  = shift;
    my $pngDir    = shift;

    my $filerrd = $dataPtr->{'connlsof'}{'rrd'};
    my $filepng = "$pngDir" . 'connlsof' . "$timeRange" . '.png';

    #print STDERR "timeRange=$timeRange, [pngDir=$pngDir], [filepng=$filepng], [rangeSec=$rangeSec]";

    # parameters for RRDs::graph
    my $date = localtime(time);
    my   @graphParam = ("$filepng",
			@graphDefaults,
			'--height', 80,
			'--start', "-$rangeSec",
			'--vertical-label=lsof TCP,UDP',
			'--watermark', "$date",
			'-b', 1000,
			
			"DEF:otcp=$filerrd:otcp:AVERAGE",
			"DEF:maxotcp=$filerrd:otcp:MAX",
			"DEF:oudp=$filerrd:oudp:AVERAGE",
			"DEF:maxoudp=$filerrd:oudp:MAX",

			'COMMENT:                           Avg\:          Max\:         Last\:\l',

			'AREA:otcp#C3F4F8:lsof\:open tcp\:',
			'GPRINT:otcp:AVERAGE:%12.2lf',
			'GPRINT:maxotcp:MAX:%12.2lf',
			'GPRINT:otcp:LAST:%12.2lf\l',
					
			'AREA:oudp#E6CBB7:lsof\:open udp\:',
			'GPRINT:oudp:AVERAGE:%12.2lf',
			'GPRINT:maxoudp:MAX:%12.2lf',
			'GPRINT:oudp:LAST:%12.2lf\l',
		       );


    if ( $rangeSec > $rangeSecMaxPrint) {
      push(@graphParam, 'LINE1:maxotcp#C3F4F8:');
      push(@graphParam, 'LINE1:maxoudp#E6CBB7:');
    }
    push(@graphParam, 'LINE1:otcp#676FFF:');
    push(@graphParam, 'LINE1:oudp#DB823F:');

    my ($graphret,$xs,$ys) = RRDs::graph @graphParam;
    my $ERR=RRDs::error;
    die "ERROR: $ERR\n" if $ERR;

    #return the png file
    return $filepng;
  }


#########################################################################
sub createGraphNtpdrift($$$)
  {
    my $timeRange = shift;
    my $rangeSec  = shift;
    my $pngDir    = shift;

    my $filerrd = $dataPtr->{'ntpdrift'}{'rrd'};
    my $filepng = "$pngDir" . 'ntpdrift' . "$timeRange" . '.png';

    #print STDERR "timeRange=$timeRange, [pngDir=$pngDir], [filepng=$filepng], [rangeSec=$rangeSec]";

    my $date = localtime(time);
    my ($graphret,$xs,$ys) = RRDs::graph("$filepng",
					 @graphDefaults,
					 '--height', 80,
					 '--start', "-$rangeSec",
					 '--vertical-label', 'msec',
					 '--watermark', "$date",
					 '-b', 1000,
					
					 "DEF:pof=$filerrd:pos_offset:AVERAGE",
					 "DEF:maxpof=$filerrd:pos_offset:MAX",
					 "DEF:nof=$filerrd:neg_offset:AVERAGE",
					 "DEF:maxnof=$filerrd:neg_offset:MAX",

					 'COMMENT:                     Avg\:          Max\:          Last\:\l',

					 'AREA:pof#74D2F1:pos.drift\:',
					 'GPRINT:pof:AVERAGE:%12.3lf',
					 'GPRINT:maxpof:MAX:%12.3lf',
					 'GPRINT:pof:LAST:%12.3lf msec\l',

					 'AREA:nof#6DF8BE:neg.drift\:',
					 'GPRINT:nof:AVERAGE:%12.3lf',
					 'GPRINT:maxnof:MAX:%12.3lf',
					 'GPRINT:nof:LAST:%12.3lf msec\l',
					
					 'LINE1:pof#45709E:',
					 'LINE1:nof#4CAC84:',
					);
    my $ERR=RRDs::error;
    die "ERROR: $ERR\n" if $ERR;


    #return the png file
    return $filepng;

  }


#########################################################################
sub createGraphPrivoxy($$$)
  {
    my $timeRange = shift;
    my $rangeSec  = shift;
    my $pngDir    = shift;

    my $filerrd = $dataPtr->{'privoxy'}{'rrd'};
    my $filepng = "$pngDir" . 'privoxy' . "$timeRange" . '.png';

    #print STDERR "timeRange=$timeRange, [pngDir=$pngDir], [filepng=$filepng], [rangeSec=$rangeSec]";

    # parameters for RRDs::graph
    my $date = localtime(time);
    my   @graphParam = ("$filepng",
			@graphDefaults,
			'--height', 80,
			'--start', "-$rangeSec",
			'--vertical-label=requests/min',
			'--watermark', "$date",
			'-b', 1000,
			
			"DEF:requests=$filerrd:requests:AVERAGE",
			"DEF:maxrequests=$filerrd:requests:MAX",
			"DEF:blocked=$filerrd:blocked:AVERAGE",
			"DEF:maxblocked=$filerrd:blocked:MAX",

			'CDEF:requestsmin=requests,60,*',
			'CDEF:maxrequestsmin=maxrequests,60,*',
			'CDEF:blockedmin=blocked,60,*',
			'CDEF:maxblockedmin=maxblocked,60,*',

			'COMMENT:                     Avg\:          Max\:         Last\:\l',

			'AREA:requestsmin#B8F083:privoxy\:',
			'GPRINT:requestsmin:AVERAGE:%12.2lf',
			'GPRINT:maxrequestsmin:MAX:%12.2lf',
			'GPRINT:requestsmin:LAST:%12.2lf req/min\l',
			
			'AREA:blockedmin#FF9D79:blocked\:',
			'GPRINT:blockedmin:AVERAGE:%12.2lf',
			'GPRINT:maxblockedmin:MAX:%12.2lf',
			'GPRINT:blockedmin:LAST:%12.2lf req/min\l',
		       );

    if ( $rangeSec > $rangeSecMaxPrint) {
      push(@graphParam, 'LINE1:maxrequestsmin#B8F083:');
      push(@graphParam, 'LINE1:maxblockedmin#FF9D79:');
    }
    push(@graphParam,   'LINE1:requestsmin#84B73C:');
    push(@graphParam,   'LINE1:blockedmin#B24127:');

    my ($graphret,$xs,$ys) = RRDs::graph @graphParam;
    my $ERR=RRDs::error;
    die "ERROR: $ERR\n" if $ERR;

    #return the png file
    return $filepng;
  }

#########################################################################
sub createGraphHdStat($$$$)
  {
    my $dev       = shift;
    my $timeRange = shift;
    my $rangeSec  = shift;
    my $pngDir    = shift;

    my $filerrd   = "$dataPtr->{'hdstat'}{'rrd'}" . ".$dev";
    my $filepng   = "$pngDir" . "$dev". 'hdstat' . "$timeRange" . '.png';
    #print STDERR "timeRange=$timeRange, [pngDir=$pngDir], [filepng=$filepng], [rangeSec=$rangeSec]";

    # parameters for RRDs::graph
    my $date = localtime(time);
    my   @graphParam = ("$filepng",
			@graphDefaults,
			'--height', 80,
			'--start', "-$rangeSec",
			'--vertical-label', "(kB/s)",
			'--watermark', "$date",
			'-b', 1000,
			
			"DEF:readkb=$filerrd:readkb:AVERAGE",
			"DEF:writkb=$filerrd:writkb:AVERAGE",
			"DEF:maxreadkb=$filerrd:readkb:MAX",
			"DEF:maxwritkb=$filerrd:writkb:MAX",
			
			'CDEF:writ_negkb=writkb,-1,*',
			'CDEF:maxwrit_negkb=maxwritkb,-1,*',
			
			'COMMENT:                     Avg\:          Max\:         Last\:\l',

			'AREA:readkb#C7B890:read\:   ',
			'GPRINT:readkb:AVERAGE:%12.2lf',
			'GPRINT:maxreadkb:MAX:%12.2lf',
			'GPRINT:readkb:LAST:%12.2lf kB/s\l',
			
			'AREA:writ_negkb#E0C5B3:write\:  ',
			'GPRINT:writkb:AVERAGE:%12.2lf',
			'GPRINT:maxwritkb:MAX:%12.2lf',
			'GPRINT:writkb:LAST:%12.2lf kB/s\l',

			'HRULE:0#000000',
		       );

    if ( $rangeSec > $rangeSecMaxPrint) {
      push(@graphParam, 'LINE1:maxreadkb#9A8F70:');
      push(@graphParam, 'LINE1:maxwrit_negkb#FF9D79:');
    }
    push(@graphParam,   'LINE1:readkb#AC9F7D:');
    push(@graphParam,   'LINE1:writ_negkb#B24127:');


    my ($graphret,$xs,$ys) = RRDs::graph @graphParam;


					
    my $ERR=RRDs::error;
    die "ERROR: $ERR\n" if $ERR;

    #return the png file
    return $filepng;
  }


#########################################################################
sub createGraphDisk($$$$)
  {
    my $disk      = shift;
    my $timeRange = shift;
    my $rangeSec  = shift;
    my $pngDir    = shift;

    my $filerrd   = "$dataPtr->{'disk'}{'rrd'}" . ".$disk";
    my $filepng   = "$pngDir" . "$disk". '.disk' . "$timeRange" . '.png';

    #print STDERR "timeRange=$timeRange, [pngDir=$pngDir], [filepng=$filepng], [rangeSec=$rangeSec]";

    my $date = localtime(time);
    my ($graphret,$xs,$ys) = RRDs::graph("$filepng",
					 @graphDefaults,
					 '--height', 80,
					 '--start', "-$rangeSec",
					 '--vertical-label', "used bytes (MB)",
					 '--watermark', "$date",
					 '-b', 1024,
					
					 "DEF:usedduM=$filerrd:d:AVERAGE",
					 "DEF:availM=$filerrd:avail:AVERAGE",
					
					 'CDEF:availML=usedduM,availM,GT,availM,0,IF',
					 'CDEF:usedduML=usedduM,availM,LT,usedduM,0,IF',
					 'CDEF:availMG=usedduM,availM,LT,availM,0,IF',
					 'CDEF:usedduMG=usedduM,availM,GT,usedduM,0,IF',

					 'AREA:availMG#21FF85:',
					 'AREA:usedduMG#FFCB75:used\:     ',
					 'GPRINT:usedduM:MAX:Max\:%9.1lf',
					 'GPRINT:usedduM:LAST:Last\:%9.1lf MB\l',
					
					 'AREA:availML#21FF85:available\:',
					 'AREA:usedduML#FFCB75:',
					 'GPRINT:availM:MAX:Max\:%9.1lf',
					 'GPRINT:availM:LAST:Last\:%9.1lf MB\l',					

					 'LINE1:usedduM#C09858:',				
					 'LINE1:availM#47BE4F:',
					);
    my $ERR=RRDs::error;
    die "ERROR: $ERR\n" if $ERR;

    #return the png file
    return $filepng;
  }




#########################################################################
sub createGraphNet($$$$)
  {
    my $net       = shift;
    my $timeRange = shift;
    my $rangeSec  = shift;
    my $pngDir    = shift;

    my $filerrd   = "$dataPtr->{'net'}{'rrd'}" . ".$net";
    my $filepng   = "$pngDir" . "$net". '.net' . "$timeRange" . '.png';

    #print STDERR "timeRange=$timeRange, [pngDir=$pngDir], [filepng=$filepng], [rangeSec=$rangeSec]";

    my $date = localtime(time);
    my ($graphret,$xs,$ys) = RRDs::graph("$filepng",
					 @graphDefaults,
					 '--height', 90,
					 '--start', "-$rangeSec",
					 '--vertical-label', "bits/sec",
					 '--watermark', "$date",

					 "DEF:inb=$filerrd:inbytes:AVERAGE",
					 "DEF:outb=$filerrd:outbytes:AVERAGE",

					 'CDEF:inbits=inb,8,*',
					 'CDEF:outbits=outb,8,*',
					 'CDEF:outbits_negb=outb,-8,*',

					 'COMMENT:                 Avg\:            Max\:           Last\:\l',

					 'AREA:inbits#74D2F1:in\: ',
					 'GPRINT:inbits:AVERAGE:%12.2lf %s',
					 'GPRINT:inbits:MAX:%12.2lf %s',
					 'GPRINT:inbits:LAST:%12.2lf %s bits/sec\l',
					 'LINE1:inbits#45709E:',
									
					 'AREA:outbits_negb#6DF8BE:out\:',
					 'GPRINT:outbits:AVERAGE:%12.2lf %s',
					 'GPRINT:outbits:MAX:%12.2lf %s',
					 'GPRINT:outbits:LAST:%12.2lf %s bits/sec\l',
					 'LINE1:outbits_negb#4CAC84:',

					 'HRULE:0#000000',
					);
    my $ERR=RRDs::error;
    die "ERROR: $ERR\n" if $ERR;

    #return the png file
    return $filepng;
  }



#########################################################################
sub createGraphNetP($$$$)
  {
    my $net       = shift;
    my $timeRange = shift;
    my $rangeSec  = shift;
    my $pngDir    = shift;

    my $filerrd   = "$dataPtr->{'net'}{'rrd'}" . ".$net";
    my $filepng   = "$pngDir" . "$net". '.netp' . "$timeRange" . '.png';

    #print STDERR "timeRange=$timeRange, [pngDir=$pngDir], [filepng=$filepng], [rangeSec=$rangeSec]";

    my $date = localtime(time);
    my ($graphret,$xs,$ys) = RRDs::graph("$filepng",
					 @graphDefaults,
					 '--height', 60,
					 '--start', "-$rangeSec",
					 '--vertical-label', 'packets/sec',
					 '--watermark', "$date",
					 '-b', 1000,
					
					 "DEF:inp=$filerrd:inpack:AVERAGE",
					 "DEF:outp=$filerrd:outpack:AVERAGE",
					 "DEF:inerr=$filerrd:inerrors:AVERAGE",
					 "DEF:outerr=$filerrd:outerrors:AVERAGE",
					
					 'CDEF:out_negp=outp,-1,*',
					 'CDEF:out_nege=outerr,-1,*',

					 'COMMENT:                     Avg\:            Max\:            Last\:\l',

					 'AREA:inp#92E0DF:in\:     ',
					 'GPRINT:inp:AVERAGE:%12.2lf %s',
					 'GPRINT:inp:MAX:%12.2lf %s',
					 'GPRINT:inp:LAST:%12.2lf %s pack/sec\l',
					 'LINE1:inp#45709E:',
									
					 'AREA:out_negp#9AE0A1:out\:    ',
					 'GPRINT:outp:AVERAGE:%12.2lf %s',
					 'GPRINT:outp:MAX:%12.2lf %s',
					 'GPRINT:outp:LAST:%12.2lf %s pack/sec\l',
					 'LINE1:out_negp#4CAC84:',

					 'LINE1:inerr#E07191:in.Err\: ',
					 'GPRINT:inerr:AVERAGE:%12.2lf %s',
					 'GPRINT:inerr:MAX:%12.2lf %s',
					 'GPRINT:inerr:LAST:%12.2lf %s pack/sec\l',
					
					 'LINE1:outerr#E07191:out.Err\:',
					 'GPRINT:outerr:AVERAGE:%12.2lf %s',
					 'GPRINT:outerr:MAX:%12.2lf %s',
					 'GPRINT:outerr:LAST:%12.2lf %s pack/sec\l',

					 'HRULE:0#000000',
					);
    my $ERR=RRDs::error;
    die "ERROR: $ERR\n" if $ERR;

    #return the png file
    #print STDERR $filepng;
    return $filepng;
  }


#########################################################################
sub createGraphNetIOrS($$$$$)
  {
    my $net       = shift;
    my $timeRange = shift;
    my $rangeSec  = shift;
    my $pngDir    = shift;

    my $netIOrS   = "net" . shift;
    my $filerrd   = "$dataPtr->{$netIOrS}{'rrd'}" . ".$net";
    my $filepng   = "$pngDir" . "$net". '.IOrS' . "$timeRange" . '.png';

    #print STDERR "timeRange=$timeRange, [pngDir=$pngDir], [filepng=$filepng], [rangeSec=$rangeSec]";

    my $date = localtime(time);
    my @graphParam = ("$filepng",
		      @graphDefaults,
		      '--height', 130,
		      '--start', "-$rangeSec",
		      '--vertical-label', "bits/sec",
		      '--watermark', "$date",
		      '-b', 1000,
		      'COMMENT:                      Avg\:            Max\:            Last\:\l',
		     );

    my $i = 0;
    foreach my $port (@graphNetPorts) {
      push(@graphParam, 'DEF:in' . $port .'=' . "$filerrd" .':' . $port .'_in:AVERAGE');
      push(@graphParam, 'DEF:ou' . $port .'=' . "$filerrd" .':' . $port .'_ou:AVERAGE');

      push(@graphParam, 'CDEF:in' . $port .'bits=in' . $port .',8,*');
      push(@graphParam, 'CDEF:ou' . $port .'bits=ou' . $port .',8,*');
      push(@graphParam, 'CDEF:ou' . $port .'bits_neg=ou' . $port .',-8,*');

      push(@graphParam, 'LINE1:in'  . $port .'bits' ."@graphNetPortsColors[$i]" .':in  P' . $port .'\: ');
      push(@graphParam, 'GPRINT:in' . $port .'bits:AVERAGE:%12.2lf %s');
      push(@graphParam, 'GPRINT:in' . $port .'bits:MAX:%12.2lf %s');
      push(@graphParam, 'GPRINT:in' . $port .'bits:LAST:%12.2lf %s bits/sec\l');
      push(@graphParam, 'LINE1:ou'  . $port .'bits_neg' ."@graphNetPortsColors[$i]" .':out P' . $port .'\: ');
      push(@graphParam, 'GPRINT:ou' . $port .'bits:AVERAGE:%12.2lf %s');
      push(@graphParam, 'GPRINT:ou' . $port .'bits:MAX:%12.2lf %s');

      push(@graphParam, 'GPRINT:ou' . $port .'bits:LAST:%12.2lf %s bits/sec\l');
      $i++;
    }

    push(@graphParam, 'HRULE:0#000000');
		

    my ($graphret,$xs,$ys) = RRDs::graph @graphParam;
    my $ERR=RRDs::error;
    die "ERROR: $ERR\n" if $ERR;

    #return the png file
    return $filepng;
  }





#########################################################################
sub createGraphFan($$$$$)
  {
    my $numFan    = shift;
    my $fan       = shift;
    my $timeRange = shift;
    my $rangeSec  = shift;
    my $pngDir    = shift;

    my $filerrd   = "$dataPtr->{'fan'}{'rrd'}" . ".$numFan" . ".$fan";
    my $filepng   = "$pngDir" . "$fan". '.fan' . "$timeRange" . '.png';

    #print STDERR "timeRange=$timeRange, [pngDir=$pngDir], [filepng=$filepng], [rangeSec=$rangeSec]";

    # parameters for RRDs::graph
    my $date = localtime(time);
    my   @graphParam = ( "$filepng",
			 @graphDefaults,
			 '--height', 100,
			 '--start', "-$rangeSec",
			 '--vertical-label=RPM',
			 '--watermark', "$date",
			 '-b', 1000,
		       );

    for (my $i = 1; $i <= $numFan; $i++) {
      push(@graphParam, "DEF:fan${i}=$filerrd:fan$i".':AVERAGE');
    }

    push(  @graphParam, 'AREA:fan1#CEC7AE');

    for (my $i = 1; $i <= $numFan; $i++) {
      push(@graphParam, "LINE1:fan${i}@fanColors[$i%4]:fan${i}\:");
      push(@graphParam, "GPRINT:fan$i".':MAX:Max\:%9.0lf,');
      push(@graphParam, "GPRINT:fan$i".':AVERAGE:Avg\: %9.1lf,');
      push(@graphParam, "GPRINT:fan$i".':LAST:Last\:%9.0lf RPM\l');
    }
    #print STDERR join("\n", @graphParam). "\n";

    my ($graphret,$xs,$ys) = RRDs::graph @graphParam;
    my $ERR=RRDs::error;
    die "ERROR: $ERR\n" if $ERR;

    #return the png file
    return $filepng;
  }


#########################################################################
sub createGraphTemp($$$$$)
  {
    my $numTemp   = shift;
    my $temp      = shift;
    my $timeRange = shift;
    my $rangeSec  = shift;
    my $pngDir    = shift;

    my $filerrd   = "$dataPtr->{'temp'}{'rrd'}" . ".$numTemp" . ".$temp";
    my $filepng   = "$pngDir" . "$temp". '.temp' . "$timeRange" . '.png';

    #print STDERR "numTemp=$numTemp, timeRange=$timeRange, [pngDir=$pngDir], [filepng=$filepng], [rangeSec=$rangeSec]";

    # parameters for RRDs::graph
    my $date = localtime(time);
    my   @graphParam = ( "$filepng",
			 @graphDefaults,
			 '--height', 100,
			 '--start', "-$rangeSec",
			 '--vertical-label=Celsius (C)',
			 '--watermark', "$date",
			 '-b', 1000,
		       );

    for (my $i = 1; $i <= $numTemp; $i++) {
      push(@graphParam, "DEF:m10temp${i}=$filerrd:temp$i".':AVERAGE');
      push(@graphParam, "CDEF:temp${i}=m10temp${i},10,/");
    }

    push(  @graphParam, 'AREA:temp1#C7D9B9');

    for (my $i = 1; $i <= $numTemp; $i++) {
      push(@graphParam, "LINE1:temp${i}@tempColors[$i%4]:temp${i}\:");
      push(@graphParam, "GPRINT:temp$i".':MAX:Max\:%9.1lf,');
      push(@graphParam,	"GPRINT:temp$i".':AVERAGE:Avg\:%9.1lf,');
      push(@graphParam, "GPRINT:temp$i".':LAST:Last\:%9.1lf C\l');
    }
    #print STDERR join("\n", @graphParam). "\n";
			
    my ($graphret,$xs,$ys) = RRDs::graph @graphParam;
    my  $ERR=RRDs::error;
    die "ERROR: $ERR\n" if $ERR;

    #return the png file
    return $filepng;
  }


#########################################################################
sub createGraphHdtmp($$$$$)
  {
    my $numTemp   = shift;
    my $hdtmpStr  = shift;
    my $timeRange = shift;
    my $rangeSec  = shift;
    my $pngDir    = shift;

    my $filepng   = "$pngDir" . "$hdtmpStr" . 'hdtmp' . "$timeRange" . '.png';
    #print STDERR "numTemp=$numTemp, timeRange=$timeRange, [pngDir=$pngDir], [filepng=$filepng], [rangeSec=$rangeSec]";

    my $date = localtime(time);
    my   @graphParam = ( "$filepng",
			 @graphDefaults,
			 '--height', 100,
			 '--start', "-$rangeSec",
			 '--vertical-label=Celsius (C)',
			 '--watermark', "$date",
			 '-b', 1000,
		       );

    # which devices in this graph
    my @hdtmps = split(/\./, $hdtmpStr);
    my @filerrds;
    my $ni = 0;
    foreach my $hdtmp (@hdtmps) {
      push(@filerrds,    "$dataPtr->{'hdtmp'}{'rrd'}" . ".$hdtmp");
      push(@graphParam,  "DEF:temp${ni}=$filerrds[$ni]:t".':AVERAGE');
      $ni++;
    }

    push(  @graphParam,  'AREA:temp0#E7CBCA');

    for (my $i = 0; $i < $numTemp; $i++) {
      push(@graphParam,  "LINE1:temp${i}@hdtmpColors[$i%8]:@hdtmps[$i]\:");
      push(@graphParam,  "GPRINT:temp$i".':MAX:Max\:%9.1lf,');
      push(@graphParam,	 "GPRINT:temp$i".':AVERAGE:Avg\:%9.1lf,');
      push(@graphParam,  "GPRINT:temp$i".':LAST:Last\:%9.1lf C\l');
    }
    #print STDERR join("\n", @graphParam). "\n";

    my ($graphret,$xs,$ys) = RRDs::graph @graphParam;
    my  $ERR=RRDs::error;
    die "ERROR: $ERR\n" if $ERR;

    #return the png file
    return $filepng;

  }

#########################################################################
sub createGraphCpuFreq($$$$)
  {
    my $numCpu    = shift;
    my $timeRange = shift;
    my $rangeSec  = shift;
    my $pngDir    = shift;

    my $filerrd   = "$dataPtr->{'cpufreq'}{'rrd'}" . ".$numCpu";
    my $filepng   = "$pngDir" . "$numCpu." . 'cpufreq' . "$timeRange" . '.png';
    #print STDERR "numTemp=$numTemp, timeRange=$timeRange, [pngDir=$pngDir], [filepng=$filepng], [rangeSec=$rangeSec]";

    my $date      = localtime(time);
    my @graphParam= ( "$filepng",
		      @graphDefaults,
		      '--height', 100,
		      '--start', "-$rangeSec",
		      '--vertical-label=(MHz)',
		      '--watermark', "$date",
		      '-b', 1000,
		    );

    # which cpus in this graph
    for (my $ni = 0; $ni < $numCpu; $ni++) {
      push(@graphParam,  "DEF:cpu${ni}=$filerrd:cpu$ni".':AVERAGE');
    }

    push(  @graphParam,  'AREA:cpu0#AFFFD6');

    for (my $i = 0; $i < $numCpu; $i++) {
      push(@graphParam,  "LINE1:cpu${i}@cpuColors[$i%8]:Cpu[$i]\:");
      push(@graphParam,  "GPRINT:cpu$i".':MAX:Max\:%9.1lf,');
      push(@graphParam,	 "GPRINT:cpu$i".':AVERAGE:Avg\:%9.1lf,');
      push(@graphParam,  "GPRINT:cpu$i".':LAST:Last\:%9.1lf MHz\l');
    }
    #print STDERR join("\n", @graphParam). "\n";

    my ($graphret,$xs,$ys) = RRDs::graph @graphParam;
    my  $ERR=RRDs::error;
    die "ERROR: $ERR\n" if $ERR;

    #return the png file
    return $filepng;
  }




##############################################################################
sub sendImage($)
  {
    my $file = shift;
    -r $file or do {
      print "Content-type: text/plain\n\nERROR: can't find $file\n";
      exit 1;
    };

    print "Content-type: image/png\n";
    print "Content-length: ".((stat($file))[7])."\n";
    print "\n";
    open(IMG, $file) or die;
    my $dataIMG;
    print $dataIMG while read IMG, $dataIMG, 1024;

    close IMG;
  }


##############################################################################

sub main()
  {
    my $uri = $ENV{REQUEST_URI} || '';
    #print STDERR "main[uri=$uri]\n";

    $uri =~ s/\/[^\/]+$//o;
    $uri =~ s/\//,/og;
    $uri =~ s/(\~|\%7E)/tilde,/og;
    $uri =~ /^(.{0,32})/;
    # only the first 32 chars get away from tainted var...
    $uri = $1;


    umask(0027);
    #my $currumask = umask;
    #printf STDERR "current umask=%o\n", $currumask;

    mkdir $systemgraphTmpDir, 0777                        unless -d $systemgraphTmpDir;
    mkdir "$systemgraphTmpDir/$uri", 0777                 unless -d "$systemgraphTmpDir/$uri";
    #print STDERR "TMPDIR=$systemgraphTmpDir/$uri\n";

    my $query = $ENV{QUERY_STRING};
    #print STDERR "[query=$query]\n";

    my $pngDir = "$systemgraphTmpDir/$uri/";

    if(defined $query and $query =~ /\S/o) {

      # basic invalid parameter checking
      if (basicCheckParam($query)!=0) {
	#uncomment only for debug, do not send unchecked parameter
	#to apache log on a potentially attacked system
	#print STDERR "[1] invalid argument($query)\n";

	die "ERROR: bad parameter received (not displayed)\n" if ($dieOnParamErrors);
	printHtml('');
      }
      elsif ($query =~ /^\D/o ) {

	#print STDERR "main A[uri=$uri], query=$query\n";
	
	# print summary for processes,...
	# ex: 'disk-hda1'
	# ex: 'loadavg'
	printHtml($query);
      }
      elsif ($query =~ s/^(\d+)([smhd])-//o) {
	# print specific image
	# ex: '36h-memory'
	# ex: '365d-disk-hda1
	
	my $timeRange  = "$1$2";
	my $timeFactor = $2;
	my $rangeSec   = $1;
	if      ($timeFactor eq 'd') {
	  $rangeSec   *= 86400;
	} elsif ($timeFactor eq 'h') {
	  $rangeSec   *= 3600;
	} elsif ($timeFactor eq 'm') {
	  $rangeSec   *= 60;
	}
	#print STDERR "main B[uri=$uri], query=$query, timeRange=$timeRange, rangeSec=$rangeSec\n";

	if    ($query eq 'memory')        {  sendImage(createGraphMemory ( $timeRange,$rangeSec,$pngDir)); }
	elsif ($query eq 'process')       {  sendImage(createGraphProcess( $timeRange,$rangeSec,$pngDir)); }
	elsif ($query eq 'users')         {  sendImage(createGraphUsers  ( $timeRange,$rangeSec,$pngDir)); }
	elsif ($query eq 'cpu')           {  sendImage(createGraphCpu    ( $timeRange,$rangeSec,$pngDir)); }
	elsif ($query eq 'cpustat')       {  sendImage(createGraphCpuStat( $timeRange,$rangeSec,$pngDir)); }
	elsif ($query eq 'gameLoad')      {  sendImage(createGraphGameLoad( $timeRange,$rangeSec,$pngDir)); }
	elsif ($query eq 'lsof')          {  sendImage(createGraphLsof   ( $timeRange,$rangeSec,$pngDir)); }
	elsif ($query eq 'loadavg')       {  sendImage(createGraphLoadavg( $timeRange,$rangeSec,$pngDir)); }
	elsif ($query eq 'connlsof')      {  sendImage(createGraphConnlsof($timeRange,$rangeSec,$pngDir)); }
	elsif ($query eq 'ntpdrift')      {  sendImage(createGraphNtpdrift($timeRange,$rangeSec,$pngDir)); }
	elsif ($query eq 'privoxy')       {  sendImage(createGraphPrivoxy ($timeRange,$rangeSec,$pngDir)); }

	elsif ($query =~ /cpufreq-(\d+)$/o )       { sendImage(createGraphCpuFreq($1, $timeRange,$rangeSec,$pngDir)); }
	elsif ($query =~ /hdstat-(\S+)$/o  )       { sendImage(createGraphHdStat( $1, $timeRange,$rangeSec,$pngDir)); }
		
	elsif ($query =~ /net-([\w\.]+)$/o)        { sendImage(createGraphNet (   $1, $timeRange,$rangeSec,$pngDir)); }
	elsif ($query =~ /netp-([\w\.]+)$/o)       { sendImage(createGraphNetP(   $1, $timeRange,$rangeSec,$pngDir)); }
	elsif ($query =~ /neti-([\w\.]+)$/o)       { sendImage(createGraphNetIOrS($1, $timeRange,$rangeSec,$pngDir,'i')); }
	elsif ($query =~ /nets-([\w\.]+)$/o)       { sendImage(createGraphNetIOrS($1, $timeRange,$rangeSec,$pngDir,'s')); }

	elsif ($query =~ /disk-([\w\-\.\,\|]+)$/o) { sendImage(createGraphDisk(   $1, $timeRange,$rangeSec,$pngDir)); }

	elsif ($query =~ /fan-(\d+)-(\w+)$/o)       {  sendImage(createGraphFan($1, $2, $timeRange,$rangeSec,$pngDir)); }
	elsif ($query =~ /temp-(\d+)-(\w+)$/o)      {  sendImage(createGraphTemp($1,$2, $timeRange,$rangeSec,$pngDir)); }

	elsif ($query =~ /hdtmp-(\d+)-([\w\.]+)$/o) {  sendImage(createGraphHdtmp($1,$2,$timeRange,$rangeSec,$pngDir)); }

	else {
	  die "ERROR: invalid argument($query)\n";
	}
      }
      elsif ($query =~ /^\d+[smhd]$/o) {

	#print STDERR "main C[uri=$uri], query=$query\n";

	printHtml("XX-$query");
      }
      else {
	#print STDERR "main D\n";

	#print STDERR "[2] invalid argument($query)";
	die "ERROR: invalid argument (not displayed)\n" if ($dieOnParamErrors);
	printHtml('');
      }
    }
    else {
      #print STDERR "main E\n";

      # print summary for all
      # empty query
      #print STDERR "Received a empty query string";

      printHtml('');
    }
  }

##############################################################################


main;
