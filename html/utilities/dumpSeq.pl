#use strict;

my $inFilePath = shift(@ARGV);

if(!($inFilePath =~ /\.fasta/)) {
    die "needs to be a fasta file";
}

my $outFilePath = shift(@ARGV);

if(!($inFilePath)) {
    die "outfile name";
}

open(OUTFILE, ">".$outFilePath);
open(INFILE, "<".$inFilePath);
my @infile = <INFILE>;
close(INFILE);

use DBI;

$dbuser="lcgerke";
$database="gfp";

### Make connection to database
#
$dbh=DBI->connect("DBI:mysql:$database", $dbuser, "", {
        PrintError => 1,
	RaiseError => 1,
    });

$maxLength = 0;

my $firstOrf = 1;
my $currentOrfNumber;
my $currentOrfSeq;
for $_ (@infile) {
    if(/>ORFN:(\S+)/) {
	if(!$firstOrf) {
	    print OUTFILE $currentOrfSeq."\n";
#	    printout();
	}
	$currentOrfNumber = $1;
	$currentOrfSeq = "";
	$firstOrf = 0;
    } else {
	chomp;
	$currentOrfSeq .= $_;
    }
}
if(!$firstOrf) {
    print OUTFILE "\n".$currentOrfNumber.", ".$currentOrfSeq."/n";
    printout();
#    print $orfid
    
#    $insertdata->execute($currentOrfSeq, 
}

close(OUTFILE);
print $maxLength;


sub printout() {
    if(length($currentOrfSeq) > $maxLength) {
	$maxLength = length($currentOrfSeq);
    }my $orfid = $dbh->selectrow_array("SELECT orfid FROM orfs WHERE orfnumber='$currentOrfNumber'");
    if(!$orfid) {
	print $currentOrfNumber."\n";
    } else {
	$sql = qq( UPDATE orfs SET sequence='$currentOrfSeq' WHERE orfid=$orfid );
#	print $sql;
	$sth = $dbh->prepare($sql);
	$sth->execute();
    }
}




