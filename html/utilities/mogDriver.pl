use strict;
use File::Basename;
use File::Copy;
my $adjust = 0;
my $origFileName;

my $mode = shift(@ARGV);

my $verbose = 0;
#my $logname = "/home/gfp/images/1/mogDriver.log";
#open(LOG, ">>$logname") or die "can't open log";
#print LOG "argv0 = ".$ARGV[0]."\n";

die "usage: perl mogDriver.pl filename.tif lowVal Gamma highVal\n"
    if !($ARGV[0] =~ /tif/);

if($mode eq "-adjust1") {
    $adjust = 1;
    die if $ARGV[0] eq "";    

    my $dir;
    my $base;
    my $ext;
    my $fullPath = $ARGV[0];
    ($base,$dir,$ext) = fileparse($fullPath, qr/\..*/);

    die if $ARGV[1] eq "";    
    die if $ARGV[2] eq "";    
    die if $ARGV[3] eq "";    
    my $lowVal = $ARGV[1];
    my $gamma = $ARGV[2];
    my $highVal = $ARGV[3];

    my $newName = $dir.$base."_mogrified_level_".$lowVal."_".$gamma."_".$highVal.$ext;
    
    runLevel($fullPath, $newName, $lowVal, $gamma, $highVal);
} else {
    if ($mode eq "-adjustAuto") {
	die if $ARGV[0] eq "";
	my $fullPath = $ARGV[0];
	my $dir;
	my $base;
	my $ext;
	($base,$dir,$ext) = fileparse($fullPath, qr/\..*/);
	
	my $command = "/usr/X11R6/bin/mogrify -format histogram ".$fullPath;
	print "<br> making hist: $command <br>\n";
	system($command);
	
	my $histogram_name = $dir.$base.".histogram";
	
	my $minBrightness = 65535;
	my $maxBrightness = 0;
	
	open(HISTO, "<$histogram_name") or die "$histogram_name";
	while(<HISTO>) {
	    if(/(\d+)\)(\s+)#/) {
		$minBrightness = min($minBrightness, $1);
		$maxBrightness = max($maxBrightness, $1);
	    }
	}
	close(HISTO);
	
	system("rm -f ".$histogram_name);
	
	my $newName = $dir.$base."_mogrified_level_".$minBrightness."_".
	    "1"."_".$maxBrightness."_autoLevel".$ext;

	runLevel($fullPath, $newName, $minBrightness, 1, $maxBrightness);
	
	
    } else {
	die "bad invocation";
    }
}
    

sub min() {
    my($a, $b) = @_;
    if ($a>$b) {
	return $b;
    }
    return $a;
}
sub max() {
    my($a, $b) = @_;
    if ($a>$b) {
	return $a;
    }
    return $b;
}
    
sub runLevel() {
    my ($origName, $destName, $lowLevel, $gamma, $highLevel) = @_;

    my ($base,$dir,$ext) = fileparse($origName, qr/\..*/);
#    copy($dir.$base.$ext,$destName);
    my $command = "/usr/X11R6/bin/mogrify -level ".$lowLevel.",".$gamma.",".$highLevel." -depth 8 -format png ";
#    $command .= $destName." >> adjust.log 2>&1";
    $command .= $origName;


    
    if ($verbose) {
	print "new file name:  ".$destName."\n";
    }

    print "<br>".$command;

    exec($command);

    my $removeOrig = 1;

    if ($removeOrig) {
	system("rm -f ".$origName);
    }




}




