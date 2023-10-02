use strict;

my $oldName;
my $verbose = 0;
my $rename = 0;
my $mode = shift(@ARGV);
my $done = 0;
my $munge = 0;
my $unmunge = 0;

if($mode eq "-verifymungev") {
    print "-> VERIFYING THE MUNGING OF FILENAMES (VERBOSE)\n";
    $verbose = 1;
    $munge = 1;
} elsif($mode eq "-verifymunge") {
    $munge = 1;
} elsif($mode eq "-verifyunmungev") {
   print "-> VERIFYING THE MUNGING OF FILENAMES (VERBOSE)\n";
   $verbose = 1;
   $unmunge = 1;
} elsif($mode eq "-verifyunmunge") {
   $unmunge = 1;
} elsif($mode eq "-renamemunge") {
    $rename = 1;
    $munge = 1;
} elsif($mode eq "-renamemungev") {
    print "-> RENAMING FILES (VERBOSE)\n";
    $verbose = 1;
    $rename = 1;
    $munge = 1;
} elsif($mode eq "-renameunmunge") {
    $rename = 1;
    $unmunge = 1;
} elsif($mode eq "-renameunmungev") {
    print "-> RENAMING FILES (VERBOSE)\n";
    $verbose = 1;
    $rename = 1;
    $unmunge = 1;
} elsif($mode eq "--help") {
    print "usage: perl dvdMunge.pl [-verify,-verifyv,-rename,renamev,--help] filename1 filename2 ...\n";
    exit(0);
} else {
    print "invalid usage.  Try --help for help\n";
    die;
}


foreach $_ (@ARGV) {
    $oldName = $_;

    
    my $prefix = "";
    my $varX = "";
    my $varA = "";
    my $varQ = "";
    my $varY = "";
    my $varZ = "";
    my $varC = "";
    my $varB = "";
    my $varImgType = "";
    my $ext = "";
    $done = 0;

    if($verbose) {
	print "OLD NAME: ".$_."\n";
    }
    
    if($munge) {
	if(/(.*)?Plate(\d{2})([A-B])-(\d{2})_.(1DAPI|2DIC|3GFP)_ver(\d{2})-([A-D])-(0{0,3}[1-9]0{0,3})ms.(.*)/) {
	    if($verbose) {
		print "\tFOUND PATTERN 50\n";
	    }
	    $prefix = $1;
	    $varX = $2;
	    $varA = $3;
	    $varQ = $4;
	    $varImgType = $5;
	    $varZ = $6;
	    $varB = $7;
	    $varY = $8;
	    $ext = $9;
	    setDone();
	}
    }

    if($unmunge) {
	if(/(.*)?(\d{2})([A-B])-(\d{2})_(1DAPI|2DIC|3GFP)_(\d{2})-([A-B])-(0{0,3}[1-9]0{0,3}).(.*)/) {
	    if($verbose) {
		print "\tFOUND PATTERN 50\n";
	    }
	    $prefix = $1;
	    $varX = $2;
	    $varA = $3;
	    $varQ = $4;
	    $varImgType = $5;
	    $varZ = $6;
	    $varB = $7;
	    $varY = $8;
	    $ext = $9;
	    setDone();
	}

    }
    
    if(!isDone()) {
	if($verbose) {
	    print "couldn'`t find match for ".$_."\n";
	}
	open(MISNAMED_LIST, ">>misnamed.txt");
	print MISNAMED_LIST $_."\n";
	exit(0);
    }

    open(RENAMED_LIST, ">>renamed.txt");
    print RENAMED_LIST $_."\n";

    my $formatName;
    
    if($munge) {
	$formatName = sprintf "%s%02d%s-%02d_%s_%02d-%s-%04d.%s", $prefix, $varX, $varA, $varQ, $varImgType, $varZ, $varB, $varY, $ext;
    }

    if($unmunge) {
	$formatName = sprintf "%sPlate%02d%s-%02d_w%s_ver%02d-%s-%04dms.%s", $prefix, $varX, $varA, $varQ, $varImgType, $varZ, $varB, $varY, $ext;
    }


    if ($verbose) {
	print "NEW NAME: ".$formatName."\n----------------------\n";
    }
    
    if($rename == 1) {
	my $temp = "renamed/".$formatName;
	if (-e $temp) {
	    open(ERRORS, ">>errors.txt");
	    print ERRORS "DUPLICATE MAPPING: ".$temp."\n";
	    exit(1);
	} else {
	    $temp = "mv ".$oldName." ".$formatName;
	    if($varQ == 1) {
		print ".";
	    }
	    system($temp);
	}
    }

}

sub setDone() {
    if($done == 1) {
	print "ERROR -- FOUND MULTIPLE MATCHING PATTERNS FOR:\n".$oldName."\n";
	die;
    }
    $done = 1;
}

sub isDone() {
    if($done == 1) {
	return 1;
    } else {
	return 0;
    }
}


