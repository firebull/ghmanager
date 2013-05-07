#!/usr/bin/perl
#             rrd_health.pl  sensorname     system health(temperature,fan)
#               or
#             rrd_health.pl  <without parameters>
#                           in this case we get the sensornames from
#                           /etc/sysconfig/systemgraph.sysconfig
#
#            example:
#             rrd_health.pl  acpitz-virtual-0 ...
#                            adm1027-i2c-0-2e
#                            asb100-i2c-0-2d
#                            coretemp-isa-0000
#                            coretemp-isa-0001
#                            coretemp-isa-0002
#                            coretemp-isa-0003
#                            it8712-isa-0290
#                            it8716-isa-0290
#                            it8718-isa-0228
#                            it8718-isa-0290
#                            it8720-isa-0228
#                            it87-isa-0290
#                            it87-i2c-0-2d
#                            k8temp-pci-00c3
#                            lm85b-i2c-0-2e
#                            ne1619-i2c-0-2d
#                            smsc47b397-isa-0480
#                            smsc47m1-isa-0800
#                            via686a-isa-6000
#                            w83627dhg-isa-0290
#                            w83627ehf-isa-0290
#                            w83627hf-i2c-0-2d
#                            w83627hf-isa-0290
#                            w83697hf-isa-0290
#                            w83627thf-isa-0290
#                            w83781d-i2c-0-2d
#                            w83791d-i2c-1-2d
#                            w83792d-i2c-0-2f
#
# NOTE: this script needs a running lm-sensors package
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
# $Id: rrd_health.pl,v 1.40 2010/02/13 20:05:09 cvslasan Exp $
##############################################################################
use strict;
use RRDs;

my $rrdFanDatabaseName  = 'fan.rrd.';
my $rrdTempDatabaseName = 'temp.rrd.';
my $sysconfigFile       = '/etc/sysconfig/systemgraph.sysconfig';

my $pgm                 = '/usr/bin/sensors';
my $testmode            = 0;

#..............................................
my @rrdSensors;
if ((scalar @ARGV) == 0) {

  # open input file
  open(inFH, "< $sysconfigFile")  or usage();

  while (defined (my $inLine = <inFH>)) {

    #print STDERR "$inLine";

    if ($inLine =~/^SENSOR=/o) {

      (my $sensorname = $inLine) =~ s/^SENSOR=//og;
      chomp $sensorname;

      push(@rrdSensors, $sensorname);
    }
  }

  close(inFH);

} else {

  @rrdSensors = @ARGV;

  if ($rrdSensors[0] eq 'TEST') {
    shift @rrdSensors;

    # instead of calling /usr/bin/sensors we want now a text file
    # with the name the health chip which contains the output of
    # /usr/bin/sensors. This enables testing on platforms where
    # the health chip not exist.
    #
    $pgm                = '/bin/cat'; #only used for local testing
    $testmode           = 1;
  }
}

#print STDERR @rrdSensors     if $testmode == 1;


# to avoid a lot of emails from cron when not configured
if (exists $ENV{'RRD_SILENT'}) {
  exit 0   if ((scalar @rrdSensors) == 0);
} else {
  usage()  if ((scalar @rrdSensors) == 0);
}

# check whether nothing to do or not
exit 0   if not -x $pgm;


#..............................................................................
# health chip definitions
##########################


# Add here new health chip functions ...


##########################
#acpitz-virtual-0
#Adapter: Virtual device
#temp1:       +46.0 C  (crit = +110.0 C)
#
#...............................
#acpitz-virtual-0   specify this one with acpitz-virtual-0::6
#Adapter: Virtual device
#temp1: +43.0C (crit = +107.0C)
#temp2: +49.0C (crit = +106.0C)
#temp3: +44.0C (crit = +106.0C)
#temp4: +40.0C (crit = +106.0C)
#temp5: +41.0C (crit = +106.0C)
#temp6: +40.0C (crit = +106.0C)
#
sub fun_acpitzvirtual0
  {
    my $rrdSensor = shift;
    my $fan       = shift;	# reference to fan array
    my $temp      = shift;	# reference to temp array
    my $valid     = 0;

    foreach (`$pgm $rrdSensor 2>/dev/null`) {
      #print STDERR "[$_]";

      if (/temp(\d+)\:\s+\+(\d+)\.{0,1}(\d){0,1}/o) {
	@$temp[$1 - 1]  = $2 * 10 + $3;
	++$valid;
      }
    }
    return $valid;
  }


##########################
#adm1027-i2c-0-2e
#V1.5:      +1.471 V  (min =  +0.00 V, max =  +3.32 V)
#VCore:     +1.515 V  (min =  +0.00 V, max =  +2.99 V)
#V3.3:      +3.386 V  (min =  +0.00 V, max =  +4.38 V)
#V5:       +5.111 V  (min =  +0.00 V, max =  +6.64 V)
#V12:      +11.828 V  (min =  +0.00 V, max = +15.94 V)
#CPU_Fan:   2510 RPM  (min =    0 RPM)
#CPU:      +36.75 C  (low  =  -127 C, high =  +127 C)
#Board:    +34.75 C  (low  =  -127 C, high =  +127 C)
#Remote:   +36.25 C  (low  =  -127 C, high =  +127 C)
#CPU_PWM:   255
#Fan2_PWM:  255
#Fan3_PWM:   77
#vid:      +1.525 V    (VRM Version  9.1)
#
sub fun_adm1027i2c02e
  {
    my $rrdSensor = shift;
    my $fan       = shift;	# reference to fan array
    my $temp      = shift;	# reference to temp array
    my $valid     = 0;

    foreach (`$pgm $rrdSensor 2>/dev/null`) {

      #print STDERR "[$_]";
      if (/CPU_Fan:\s*(\d+)\s/o) {
	@$fan[0]   = $1;
	$valid++;
      } elsif (/CPU:\s*\+(\d+)\.{0,1}(\d){0,1}/o) {
	@$temp[0]  = $1* 10 + $2;
	$valid++;
      } elsif (/Board:\s*\+(\d+)\.{0,1}(\d){0,1}/o) {
	@$temp[1]  = $1* 10 + $2;
	$valid++;
      } elsif (/Remote:\s*\+(\d+)\.{0,1}(\d){0,1}/o) {
	@$temp[2]  = $1* 10 + $2;
	$valid++;
      }
    }
    return $valid;
  }


##########################
#asb100-i2c-0-2d
#Adapter: SMBus I801 adapter at e800
#VCore 1:   +1.81 V  (min =  +1.39 V, max =  +2.08 V)
#+3.3V:     +3.25 V  (min =  +2.96 V, max =  +3.63 V)
#+5V:       +5.13 V  (min =  +4.49 V, max =  +5.51 V)
#+12V:     +11.49 V  (min =  +9.55 V, max = +14.41 V)
#-12V (reserved):
#          -12.01 V  (min =  -0.00 V, max =  -0.00 V)
#-5V (reserved):
#           -5.04 V  (min =  -0.00 V, max =  -0.00 V)
#CPU Fan:  2777 RPM  (min = 9246 RPM, div = 2)
#Chassis Fan:
#          0 RPM  (min = 1155 RPM, div = 8)
#Power Fan:   0 RPM  (min = 1308 RPM, div = 8)
#M/B Temp:    +35캜  (high =   +80캜, hyst =   +75캜)
#CPU Temp (Intel):
#            +33캜  (high =  +100캜, hyst =   +90캜)
#Power Temp:
#            -0캜  (high =   +80캜, hyst =   +75캜)
#CPU Temp (AMD):
#              +25캜  (high =   +80캜, hyst =   +75캜)
#vid:      +1.750 V  (VRM Version 9.0)
sub fun_asb100i2c02d
  {
    my $rrdSensor = shift;
    my $fan       = shift;	# reference to fan array
    my $temp      = shift;	# reference to temp array
    my $valid     = 0;

    my $nextLine   = 0;

    foreach (`$pgm $rrdSensor 2>/dev/null`) {

      #print STDERR "[$_]";

      if      ($nextLine == 1) {
	# chassis fan
	if   (/\s*(\d+)\s/o) {
	  @$fan[1]       = $1;
	  $valid++;
	}
	$nextLine        = 0;

      } elsif ($nextLine == 10) {
	# CPU temp intel
	if (/\s*[\+\-](\d+)/o) {
	  @$temp[1]      = $1 * 10;
	  $valid++;
	}
	$nextLine        = 0;

      } elsif ($nextLine == 11) {
	# power temp
	if (/\s*[\+\-](\d+)/o) {
	  @$temp[2]      = $1 * 10;
	  $valid++;
	}
	$nextLine        = 0;

      } elsif ($nextLine == 12)  {
	# cpu temp amd
	if   (/\s*[\+\-](\d+)/o) {
	  @$temp[3]      = $1 * 10;
	  $valid++;
	}
	$nextLine        = 0;
      }

      #..............
      if       (/CPU Fan\:\s*(\d+)\s/o) {
	@$fan[0]         = $1;
	$valid++;
      } elsif (/Chassis Fan\:/o) {
	$nextLine        = 1;
      } elsif  (/Power Fan\:\s*(\d+)\s/o) {
	@$fan[2]        = $1;
	$valid++;
      }
      elsif   (/M\/B Temp\:\s*[\+\-](\d+)/o) {
	@$temp[0]       = $1 * 10;
	$valid++;
      } elsif (/CPU Temp \(Intel\)\:/o) {
	$nextLine       = 10;
      } elsif (/Power Temp:/o) {
	$nextLine       = 11;
      } elsif (/CPU Temp \(AMD\)\:/o) {
	$nextLine       = 12;
      }

    }
    return $valid;
  }


##########################
#coretemp-isa-0000
#Adapter: ISA adapter
#Core 0:      +36째C  (high =  +100째C)
#coretemp-isa-0001
#Adapter: ISA adapter
#Core 1:      +37째C  (high =  +100째C)
#
# new:
#coretemp-isa-0000
#Adapter: ISA adapter
#Core 0:      +43.0 C  (high = +100.0 C, crit = +100.0 C)
#
sub fun_coretempisa000
  {
    my $rrdSensor = shift;
    my $fan       = shift;	# reference to fan array
    my $temp      = shift;	# reference to temp array
    my $valid     = 0;

    foreach (`$pgm $rrdSensor 2>/dev/null`) {

      if (/Core \d+\:\s+\+(\d+)\.{0,1}(\d){0,1}/o) {
	@$temp[0]       = $1 * 10 + $2;
	++$valid;
      }
    }
    return $valid;
  }



##########################
#k8temp-pci-00c3
#Adapter: PCI adapter
#Core0 Temp:
#             +29 C
#Core1 Temp:
#             +29 C
#
###########################
#k8temp-pci-00c3
#Adapter: PCI adapter
#Core0 Temp:  +22.0C
#
#...............................
#k8temp-pci-00c3  specify this one as k8temp-pci-00c3::2
#
# debian:
#k8temp-pci-00c3
#Adapter: PCI adapter
#Core0 Temp:  +38.0캜
#Core1 Temp:  +45.0캜
#
# opensuse:
#k8temp-pci-00c3
#Adapter: PCI adapter
#Core0 Temp:  +27.0 C
#Core1 Temp:  +26.0 C
#
#...............................
#k8temp-pci-00c3 specify this one as k8temp-pci-00c3::4
#k8temp-pci-00c3
#Adapter: PCI adapter
#Core0 Temp:  +34.0C
#Core0 Temp:  +35.0C
#Core1 Temp:  +28.0C
#Core1 Temp:  +27.0C
#
sub fun_k8temppci00c3
  {
    my $rrdSensor = shift;
    my $fan       = shift;	# reference to fan array
    my $temp      = shift;	# reference to temp array
    my $valid     = 0;

    foreach (`$pgm $rrdSensor 2>/dev/null`) {

      if      (/Core\d+ Temp\:\s*\+(\d+)\.{0,1}(\d){0,1}/o) {
	@$temp[$valid]  = $1 * 10 + $2;
	++$valid;

	# non debian
      } elsif (/\s*\+(\d+)\sC/o) {
	@$temp[$valid]  = $1 * 10;
	++$valid;
      }
    }
    return $valid;
  }



##########################
#smsc47b397-isa-0480
#Adapter: ISA adapter
#temp1:       +30째C
#temp2:      -128째C
#temp3:       +29째C
#temp4:      -128째C
#fan1:     1100 RPM
#fan2:        0 RPM
#fan3:      944 RPM
#fan4:        0 RPM
#
sub fun_smsc47b397isa0480
  {
    my $rrdSensor = shift;
    my $fan       = shift;	# reference to fan array
    my $temp      = shift;	# reference to temp array
    my $valid     = 0;

    foreach (`$pgm $rrdSensor 2>/dev/null`) {

      #print STDERR "[$_]";
      if (/temp(\d+)\:\s*([\+\-])(\d+)/o) {

	#print STDERR "raw: $1 $2 $3\n";
	if ($1>0 && $1<=4) {
	  # ignore negative values !!!
	  @$temp[$1 - 1] = $3 * 10 if ( $2 ne '-' );
	  ++$valid;
	}
      } elsif (/fan(\d+)\:\s*(\d+)\s/o) {
	#print STDERR "raw: $1 $2";
	if ($1>0 && $1<=4) {
	  @$fan[$1 - 1]  = $2;
	  ++$valid;
	}
      }
    }
    return $valid;
  }



#########################
#lm85b-i2c-0-2e
#Adapter: SMBus I801 adapter at c800
#
#Volt1_5:    +1.48 V  (min =  +1.42 V, max =  +1.58 V)
#VoltCore:   +1.50 V  (min =  +1.45 V, max =  +1.60 V)
#Volt3_3:    +3.33 V  (min =  +3.13 V, max =  +3.47 V)
#Volt5:     +5.10 V  (min =  +4.74 V, max =  +5.26 V)
#Volt12:   +12.25 V  (min = +11.38 V, max = +12.62 V)
#fan1:   3377 RPM  (min = 3000 RPM)
#fan2:         0 RPM  (min =    0 RPM)
#fan3:         0 RPM  (min =    0 RPM)
#fan4:         0 RPM  (min =    0 RPM)
#temp1:     +32 C  (low  =   +10 C, high =   +50 C)
#temp2:     +30 C  (low  =   +10 C, high =   +45 C)
#temp3:     +30 C  (low  =   +10 C, high =   +40 C)
#pwm1:      255
#pwm2:      255
#pwm3:      255
#vid:      +0.275 V  (VRM Version 9.1)
#
#########################
#w83781d-i2c-0-2d
#Adapter: SMBus PIIX4 adapter at 5000
#VCore 1:   +2.03 V  (min =  +1.90 V, max =  +2.10 V)
#VCore 2:   +2.05 V  (min =  +1.90 V, max =  +2.10 V)
#+3.3V:     +3.44 V  (min =  +3.14 V, max =  +3.46 V)       ALARM
#+5V:       +5.05 V  (min =  +4.73 V, max =  +5.24 V)
#+12V:     +11.67 V  (min = +11.37 V, max = +12.59 V)
#-12V:     -12.74 V  (min = -12.57 V, max = -11.35 V)       ALARM
#-5V:       -4.91 V  (min =  -5.25 V, max =  -4.74 V)
#fan1:     5232 RPM  (min = 2657 RPM, div = 2)
#fan2:        0 RPM  (min = 2657 RPM, div = 2)              ALARM
#fan3:        0 RPM  (min = 2657 RPM, div = 2)              ALARM
#temp1:       +21 C  (high =  +127 C, hyst =   +31 C)
#temp2:     +27.0 C  (high =   +63 C, hyst =   +62 C)
#temp3:     +24.0 C  (high =   +63 C, hyst =   +62 C)
#vid:      +2.000 V  (VRM Version 8.2)
#alarms:
#beep_enable:
#          Sound alarm disabled
#
##################
#w83627hf-isa-0290
#Adapter: ISA adapter
#VCore 1:   +4.08 V  (min =  +1.34 V, max =  +1.49 V)       ALARM
#VCore 2:   +4.08 V  (min =  +1.34 V, max =  +1.49 V)       ALARM
#+3.3V:     +4.08 V  (min =  +3.14 V, max =  +3.46 V)       ALARM
#+5V:       +5.11 V  (min =  +4.73 V, max =  +5.24 V)
#+12V:     +11.73 V  (min = +10.82 V, max = +13.19 V)
#-12V:      +1.29 V  (min = -13.18 V, max = -10.88 V)       ALARM
#-5V:       +2.24 V  (min =  -5.25 V, max =  -4.75 V)       ALARM
#V5SB:      +5.48 V  (min =  +4.73 V, max =  +5.24 V)       ALARM
#VBat:      +0.54 V  (min =  +2.40 V, max =  +3.60 V)       ALARM
#fan1:        0 RPM  (min =    0 RPM, div = 2)
#fan2:        0 RPM  (min = 2689 RPM, div = 2)              ALARM
#fan3:        0 RPM  (min = 6553 RPM, div = 2)              ALARM
#temp1:       -48캜  (high =    -1캜, hyst =   -25캜)   sensor = thermistor
#temp2:     -48.0캜  (high =   +80캜, hyst =   +75캜)   sensor = thermistor
#temp3:     -48.0캜  (high =   +80캜, hyst =   +75캜)   sensor = thermistor
#vid:      +1.419 V  (VRM Version 11.0)
#alarms:
#beep_enable:
#          Sound alarm enabled
#
#################
sub fun_generic1
  {
    my $rrdSensor = shift;
    my $fan       = shift;	# reference to fan array
    my $temp      = shift;	# reference to temp array
    my $valid     = 0;

    foreach (`$pgm $rrdSensor 2>/dev/null`) {
	
      #print STDERR "[$_]";
      if (/fan(\d+):\s*(\d+)\s/o) {
	@$fan[$1 - 1]  = $2;
	$valid++;
	
      } elsif (/temp(\d+):\s*[\+\-](\d+)\.{0,1}(\d){0,1}/o) {
	@$temp[$1 - 1] = $2 * 10 + $3;
	$valid++;
      }
    }

    return $valid;
  }



#######################
#w83792d-i2c-0-2f
#Adapter: SMBus nForce2 adapter at 2a00
#VCoreA:      +1.31 V  (min =  +1.20 V, max =  +1.60 V)
#VCoreB:      +0.35 V  (min =  +0.00 V, max =  +2.04 V)
#VIN0:        +3.25 V  (min =  +0.00 V, max =  +4.08 V)
#VIN1:        +3.16 V  (min =  +0.00 V, max =  +4.08 V)
#VIN2:        +1.48 V  (min =  +0.00 V, max =  +4.08 V)
#VIN3:        +1.80 V  (min =  +0.00 V, max =  +4.08 V)
#5VCC:        +4.91 V  (min =  +0.00 V, max =  +6.12 V)
#5VSB:        +4.91 V  (min =  +0.00 V, max =  +6.12 V)
#VBAT:        +3.01 V  (min =  +0.00 V, max =  +4.08 V)
#Fan1:       1607 RPM  (min =    0 RPM, div = 8)
#Fan2:       2191 RPM  (min =    0 RPM, div = 4)
#Fan3:          0 RPM  (min =    0 RPM, div = 32)
#Fan4:          0 RPM  (min =    0 RPM, div = 32)
#Fan5:       1125 RPM  (min =    0 RPM, div = 8)
#Fan7:          0 RPM  (min =    0 RPM, div = 32)
#Temp1:       +51.0캜  (high = +127.0캜, hyst =  +0.0캜)
#Temp2:       +52.0캜  (high = +80.0캜, hyst = +75.0캜)
#Temp3:       +39.0캜  (high = +80.0캜, hyst = +75.0캜)
#
#######################
#w83627dhg-isa-0290
#Adapter: ISA adapter
#VCore:     +1.16 V  (min =  +0.00 V, max =  +1.74 V) 
#in1:      +12.46 V  (min =  +1.69 V, max =  +0.53 V) ALARM
#AVCC:      +3.22 V  (min =  +1.58 V, max =  +0.26 V) ALARM
#3VCC:      +3.20 V  (min =  +0.64 V, max =  +2.05 V) ALARM
#in4:       +0.74 V  (min =  +0.26 V, max =  +0.89 V) 
#in5:       +1.62 V  (min =  +0.59 V, max =  +0.51 V) ALARM
#in6:       +2.20 V  (min =  +2.05 V, max =  +4.22 V) 
#VSB:       +3.22 V  (min =  +2.72 V, max =  +2.19 V) ALARM
#VBAT:      +2.05 V  (min =  +1.54 V, max =  +0.51 V) ALARM
#Case Fan: 2445 RPM  (min = 3879 RPM, div = 4) ALARM
#CPU Fan:     0 RPM  (min =  458 RPM, div = 128) ALARM
#Aux Fan:     0 RPM  (min = 3515 RPM, div = 128) ALARM
#fan4:        0 RPM  (min = 10546 RPM, div = 128) ALARM
#fan5:        0 RPM  (min = 10546 RPM, div = 128) ALARM
#Sys Temp:    +31C  (high =   +33C, hyst =    +0C)   ALARM
#CPU Temp:  +33.0C  (high = +80.0C, hyst = +75.0C)  
#AUX Temp: +124.5C  (high = +80.0C, hyst = +75.0C)   ALARM
#
##########
#w83627ehf-isa-0290
#Adapter: ISA adapter
#VCore:     +1.22 V  (min =  +0.00 V, max =  +1.74 V)
#in1:       +8.08 V  (min = +11.56 V, max = +13.46 V) ALARM
#AVCC:      +3.36 V  (min =  +2.96 V, max =  +3.82 V)
#3VCC:      +3.36 V  (min =  +3.63 V, max =  +2.24 V) ALARM
#in4:       +1.70 V  (min =  +1.95 V, max =  +1.53 V) ALARM
#in5:       +1.71 V  (min =  +1.71 V, max =  +2.04 V)
#in6:       +6.07 V  (min =  +5.48 V, max =  +2.79 V) ALARM
#VSB:       +3.36 V  (min =  +3.82 V, max =  +3.44 V) ALARM
#VBAT:      +3.97 V  (min =  +2.90 V, max =  +4.02 V)
#in9:       +1.58 V  (min =  +1.13 V, max =  +1.84 V)
#Case Fan:    0 RPM  (min =  669 RPM, div = 16) ALARM
#CPU Fan:  1548 RPM  (min = 1074 RPM, div = 8)
#Aux Fan:     0 RPM  (min =  709 RPM, div = 16) ALARM
#fan4:        0 RPM  (min = 1562 RPM, div = 16) ALARM
#Sys Temp:    +33 C  (high =    -6 C, hyst =   -25 C)   ALARM
#CPU Temp:  +47.5 C  (high = +80.0 C, hyst = +75.0 C)
#AUX Temp:  +46.5 C  (high = +80.0 C, hyst = +75.0 C)
#
# w83627ehf-isa-0290
#Adapter: ISA adapter
#Vcore:       +1.11 V  (min =  +0.00 V, max =  +1.74 V)
#in1:         +1.11 V  (min =  +2.04 V, max =  +2.04 V)   ALARM
#AVCC:        +3.39 V  (min =  +2.83 V, max =  +3.82 V)
#VCC:         +3.41 V  (min =  +2.99 V, max =  +4.05 V)
#in4:         +1.74 V  (min =  +2.03 V, max =  +1.53 V)   ALARM
#in5:         +1.70 V  (min =  +2.04 V, max =  +2.04 V)   ALARM
#in6:         +1.90 V  (min =  +1.71 V, max =  +1.00 V)   ALARM
#3VSB:        +3.41 V  (min =  +3.95 V, max =  +4.08 V)   ALARM
#Vbat:        +3.02 V  (min =  +2.90 V, max =  +4.08 V)
#in9:         +1.55 V  (min =  +1.38 V, max =  +1.46 V)   ALARM
#fan1:          0 RPM  (min = 10546 RPM, div = 128)  ALARM
#fan2:       1360 RPM  (min =  892 RPM, div = 8)
#fan3:          0 RPM  (min = 10546 RPM, div = 128)  ALARM
#fan5:          0 RPM  (min =    0 RPM, div = 128)
#temp1:       +31.0 C  (high =  -6.0 C, hyst =  -9.0 C)  ALARM  sensor = thermistor
#temp2:       +31.0 C  (high = +80.0 C, hyst = +75.0 C)  sensor = thermistor
#temp3:       +30.0 C  (high = +80.0 C, hyst = +75.0 C)  sensor = thermistor
#cpu0_vid:   +0.375 V
#
########
#w83627thf-isa-0290
#Adapter: ISA adapter
#VCore:     +1.62 V  (min =  +1.47 V, max =  +1.63 V)
#+12V:     +11.98 V  (min = +10.82 V, max = +13.19 V)
#+3.3V:     +3.38 V  (min =  +3.14 V, max =  +3.47 V)
#+5V:       +5.07 V  (min =  +4.75 V, max =  +5.25 V)
#-12V:      +0.47 V  (min = -13.18 V, max = -10.80 V)       ALARM
#V5SB:      +5.11 V  (min =  +4.76 V, max =  +5.24 V)
#VBat:      +0.00 V  (min =  +2.40 V, max =  +3.60 V)       ALARM
#fan1:        0 RPM  (min = 10546 RPM, div = 128)              ALARM
#CPU Fan:  4856 RPM  (min = 168750 RPM, div = 2)              ALARM
#fan3:      869 RPM  (min = 8437 RPM, div = 16)              ALARM
#M/B Temp:    +32캜  (high =   +81캜, hyst =    +0캜)   sensor =thermistor
#CPU Temp:  +36.5캜  (high =   +80캜, hyst =   +75캜)   sensor =thermistor
#temp3:     -48.0캜  (high =   +80캜, hyst =   +75캜)   sensor =thermistor
#vid:      +1.550 V  (VRM Version 9.0)
#alarms:
#beep_enable:
#          Sound alarm enabled
#
##################
#via686a-isa-6000
#Adapter: ISA adapter
#CPU core:  +1.67 V  (min =  +0.06 V, max =  +3.10 V)
#+2.5V:     +0.30 V  (min =  +2.36 V, max =  +2.61 V)   ALARM
#I/O:       +3.36 V  (min =  +3.12 V, max =  +3.45 V)
#+5V:       +5.18 V  (min =  +4.73 V, max =  +5.20 V)
#+12V:     +11.95 V  (min = +11.35 V, max = +12.48 V)
#CPU Fan:  6887 RPM  (min =    0 RPM, div = 2)
#P/S Fan:     0 RPM  (min =    0 RPM, div = 2)
#SYS Temp:  +40.2캜  (high =  +146캜, hyst =   -71캜)
#CPU Temp:  +53.0캜  (high =  +146캜, hyst =   -71캜)
#SBr Temp:  +24.8캜  (high =  +146캜, hyst =  +146캜)
#

sub fun_generic2
  {
    my $rrdSensor = shift;
    my $fan       = shift;	# reference to fan array
    my $temp      = shift;	# reference to temp array
    my $validF    = 0;
    my $validT    = 0;

    foreach (`$pgm $rrdSensor 2>/dev/null`) {
	
      #print STDERR "[$_]";
      if (/[Ff]an\d*:\s*(\d+)\s/o) {
	@$fan[$validF]  = $1;
	$validF++;
	
      } elsif (/[Tt]emp\d*:\s*[\+\-](\d+)\.{0,1}(\d){0,1}/o) {
	@$temp[$validT] = $1 * 10 + $2;
	$validT++;
      }
    }

    return $validT + $validF;
  }



#............................................................................

my $healthPtr       = {
		       'acpitz-virtual-0'    => { fans => 0,
						  temp => 1,  # can be overwritten
						  fun  => \&fun_acpitzvirtual0,
						},
		       #..................................................
		       'k8temp-pci-00c3'     => { fans => 0,
						  temp => 1,  # can be overwritten
						  fun  => \&fun_k8temppci00c3,
						},


		       #..................................................
		       'adm1027-i2c-0-2e'    => { fans => 1,
						  temp => 3,
						  fun  => \&fun_adm1027i2c02e,
						},
		       #..................................................
		       'asb100-i2c-0-2d'     => { fans => 3,
						  temp => 4,
						  fun  => \&fun_asb100i2c02d,
						},
		       #..................................................
		       'coretemp-isa-0000'   => { fans => 0,
						  temp => 1,
						  fun  => \&fun_coretempisa000,
						},
		       #..................................................
		       'coretemp-isa-0001'   => { fans => 0,
						  temp => 1,
						  fun  => \&fun_coretempisa000,
						},
		       #..................................................
		       'coretemp-isa-0002'   => { fans => 0,
						  temp => 1,
						  fun  => \&fun_coretempisa000,
						},
		       #..................................................
		       'coretemp-isa-0003'   => { fans => 0,
						  temp => 1,
						  fun  => \&fun_coretempisa000,
						},
		       #..................................................
		       'dme1737-i2c-0-2e'    => { fans => 3,
						  temp => 3,
						  fun  => \&fun_generic2,
						},
		       #..................................................
		       'f71862fg-isa-0220'   => { fans => 3,
						  temp => 3,
						  fun  => \&fun_generic1,
						},
		       #..................................................
		       'it8712-isa-0290'     => { fans => 3,
						  temp => 3,
						  fun  => \&fun_generic2,
						},
		       #..................................................
		       'it8716-isa-0228'     => { fans => 3,
						  temp => 3,
						  fun  => \&fun_generic1,
						},
		       #..................................................
		       'it8716-isa-0290'     => { fans => 3,
						  temp => 3,
						  fun  => \&fun_generic1,
						},
		       #..................................................
		       'it8718-isa-0228'     => { fans => 4,
						  temp => 3,
						  fun  => \&fun_generic2,
						},
		       #..................................................
		       'it8718-isa-0290'     => { fans => 3,
						  temp => 3,
						  fun  => \&fun_generic1,
						},
		       #..................................................
		       'it8720-isa-0228'     => { fans => 4,
						  temp => 3,
						  fun  => \&fun_generic2,
						},
		       #..................................................
		       'it87-i2c-0-2d'       => { fans => 3,
						  temp => 3,
						  fun  => \&fun_generic2,
						},
		       #..................................................
		       'it87-isa-0290'       => { fans => 3,
						  temp => 3,
						  fun  => \&fun_generic2,
						},
		       #..................................................
		       'lm85b-i2c-0-2e'      => { fans => 4,
						  temp => 3,
						  fun  => \&fun_generic1,
						},
		       #..................................................
		       'ne1619-i2c-0-2d'     => { fans => 0,
						  temp => 2,
						  fun  => \&fun_generic2,
						},
		       #..................................................
		       'smsc47b397-isa-0480' => { fans => 4,
						  temp => 4,
						  fun  => \&fun_smsc47b397isa0480,
						},
		       #..................................................
		       'smsc47m1-isa-0800'   => { fans => 2,
						  temp => 0,
						  fun  => \&fun_generic1,
						},
		       #..................................................
		       'via686a-isa-6000'    => { fans => 2,
						  temp => 3,
						  fun  => \&fun_generic2,
						},
		       #..................................................
		       'w83627dhg-isa-0290'  => { fans => 5,
						  temp => 3,
						  fun  => \&fun_generic2,
						},
		       #..................................................
		       'w83627ehf-isa-0290'  => { fans => 4,
						  temp => 3,
						  fun  => \&fun_generic2,
						},
		       #..................................................
		       'w83627hf-i2c-0-2d'   => { fans => 3,
						  temp => 3,
						  fun  => \&fun_generic1,
						},
		       #..................................................
		       'w83627hf-isa-0290'   => { fans => 3,
						  temp => 3,
						  fun  => \&fun_generic1,
						},
		       #..................................................
		       'w83627thf-isa-0290'  => { fans => 3,
						  temp => 3,
						  fun  => \&fun_generic2,
						},
		       #..................................................
		       'w83697hf-isa-0290'   => { fans => 2,
						  temp => 2,
						  fun  => \&fun_generic1,
						},
		       #..................................................
		       'w83781d-i2c-0-2d'    => { fans => 3,
						  temp => 3,
						  fun  => \&fun_generic1,
						},
		       #..................................................
		       'w83791d-i2c-1-2d'    => { fans => 5,
						  temp => 3,
						  fun  => \&fun_generic1,
						},
		       #..................................................
		       'w83792d-i2c-0-2f'    => { fans => 6,
						  temp => 3,
						  fun  => \&fun_generic2,
						},
		       #..................................................

		       # add here new health chip entries ......
		
		
		       #........................................
		      };






#..............................................................................
sub usage
  {
    my $pgmName = $0;
    $pgmName    =~ s/.*\///;  # remove path

    print STDERR <<ENDOFUSAGETEXT;

usage: $pgmName [TEST] sensorname1 [sensorname2 ....]
	or
       $pgmName <without parameters>
        in this case the sensornames must be defined in $sysconfigFile

        TEST  switches to testmode. Instead of calling /usr/bin/sensors
              $pgmName awaits the text output from the health chip in a
              file in the current directory with the name of the sensor.

ENDOFUSAGETEXT

    exit 1;
  }


#..............................................................................
# database dir env exist ?
my $rrdFanDatabasePath;
my $rrdTempDatabasePath;

if (exists $ENV{'DATABASEDIR'} ) {
  $rrdFanDatabasePath  = $ENV{'DATABASEDIR'} . "/" . "$rrdFanDatabaseName";
  $rrdTempDatabasePath = $ENV{'DATABASEDIR'} . "/" . "$rrdTempDatabaseName";
} else {
  $rrdFanDatabasePath  = '/var/lib/systemgraph/' . "$rrdFanDatabaseName";
  $rrdTempDatabasePath = '/var/lib/systemgraph/' . "$rrdTempDatabaseName";
}

#print STDERR "$rrdFanDatabasePath\n";
#print STDERR "$rrdTempDatabasePath\n";


#..............................................................................
sub create_rrd($$$)
  {
    #print STDERR "create_rrd: $_[0], $_[1], $_[2]\n";

    # awaiting an update every 300 secs
    my $rrdStep   = 300;

    # data source = in/out COUNTER, max 600 sec wait before UNKNOWN,
    #  0 min, no max
    # 0.5:1: average value calc. with 1 entry = 300sec
    # 0.5:5: average value calc. with 5 entries = 5*300sec

    # h36    = 3600*36     => 129600sec/300      => 432
    # d7a2   = 3600*24*7   => 604800sec/300 /2   => 1008; 10min average
    # d30a6  = 3600*24*30  => 2592000sec/300 /6  => 1440; 30min average
    # d365a12= 3600*24*365 => 31536000sec/300 /12=> 8760; 1h    average

    # d7a6   = 3600*24*7   => 604800sec/300 /6   => 336;  30min max
    # d30a12 = 3600*24*30  => 2592000sec/300 /12 => 720;  1h    max
    # d365a24= 3600*24*365 => 31536000sec/300 /24=> 4380; 2h    max
    {
      my @createPar = ("$_[0]", '--step', $rrdStep);

      # create as many fans/temps
      my $i = 0;
      while ($i<$_[2]) {
	$i++;
	push(@createPar, "DS:$_[1]${i}:GAUGE:".($rrdStep*2).':0:U');
      }

      push(@createPar,  'RRA:AVERAGE:0.5:1:432');
      push(@createPar,  'RRA:AVERAGE:0.5:2:1008');
      push(@createPar,  'RRA:AVERAGE:0.5:6:1440');
      push(@createPar,  'RRA:AVERAGE:0.5:12:8760');

      push(@createPar,  'RRA:MAX:0.5:6:336');
      push(@createPar,	'RRA:MAX:0.5:12:720');
      push(@createPar,  'RRA:MAX:0.5:24:4380');

      #print STDERR join("\n", @createPar). "\n";

      RRDs::create @createPar;
      my $ERR = RRDs::error;
      die "ERROR while creating $_[0]: $ERR\n" if $ERR;
    }
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
    #print STDERR join(':', @rrdSensors), "\n";

    # get current time
    my $rrdTime     = time;

    foreach my $raw_rrdSensor (@rrdSensors) {

      my ($rrdSensor, $numFan, $numTemp) = split(/:/, $raw_rrdSensor);

      $numFan       = $healthPtr->{$rrdSensor}{'fans'} if $numFan  == 0;
      $numTemp      = $healthPtr->{$rrdSensor}{'temp'} if $numTemp == 0;

      my @fan       = (0) x $numFan;
      my @temp      = (0) x $numTemp;

      #only supported sensors (valid or not)
      if ($testmode == 1) {
	next if (&{ $healthPtr->{$rrdSensor}{'fun'} } ( $raw_rrdSensor, \@fan, \@temp) != $numFan + $numTemp);
      } else {
	next if (&{ $healthPtr->{$rrdSensor}{'fun'} } ( $rrdSensor, \@fan, \@temp) != $numFan + $numTemp);
      }

      #eliminate '-' in the filenames, because systemgraph.cgi uses '-' too.
      $rrdSensor =~ s/-/_/g;

      # fill temp databases when valid
      if ($numTemp > 0) {

	my $rrdTempFile = "$rrdTempDatabasePath" . $numTemp . "." . $rrdSensor;

	if ($testmode == 1) {
	  print STDERR "tempFile=$rrdTempFile\n";
	  print STDERR "Temp:", join(':', @temp), "\n";
	}

	create_rrd($rrdTempFile,'temp', "$numTemp") if not -w $rrdTempFile;
	update_rrd($rrdTempFile,$rrdTime, \@temp )  if -w     $rrdTempFile;
      }

      # fill fan databases when valid
      if ($numFan > 0) {

	my $rrdFanFile  = "$rrdFanDatabasePath" .  $numFan .  "." . $rrdSensor;

	if ($testmode == 1) {
	  print STDERR "fanFile=$rrdFanFile\n";
	  print STDERR "Fans:", join(':', @fan), "\n";
	}

	create_rrd($rrdFanFile, 'fan' , "$numFan")  if not -w $rrdFanFile;
	update_rrd($rrdFanFile, $rrdTime, \@fan )   if -w     $rrdFanFile;
      }
    }
  }

#.......................................................................

# fill the databases
fill_rrd ();

#.......................................................................
