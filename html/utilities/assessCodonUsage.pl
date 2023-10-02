#!/usr/bin/perl -w

use DBI;

$dbuser="lcgerke";
$database="gfp";

### Make connection to database
#
$dbh=DBI->connect("DBI:mysql:$database", $dbuser, "", {
    PrintError => 1,
    RaiseError => 1,
});


$statement = "SELECT orfs.orfid, sagedata.messagecount FROM orfs inner join sagedata on orfs.orfid=sagedata.orfid WHERE conditionid=5";
$sth = $dbh->prepare($statement);
$sth->execute( );

@names = @{$sth->{NAME}};
for($i=0; $i<@names; $i++) {
    if ($names[$i] eq "messagecount") {
	$messagecountColNum = $i;
    }
    if ($names[$i] eq "orfid") {
	$orfidColNum = $i;
    }
}

%indVarHash = ();

$constraint = " WHERE ";
$first = 1;
$indVarStr = "msgConcDivByExpDotBlot <- c(";
$sth->execute( );
while (@columns = $sth->fetchrow ) {
    $orfid = $columns[$orfidColNum];
    $subStatement = "select dotblot.dotblotscore from strains inner join dotblot on strains.strainid=dotblot.strainid where strains.tag='TAP' and strains.orfid=$orfid";
    $sthSub = $dbh->prepare($subStatement);
    $sthSub->execute();
    @subcolumns = $sthSub->fetchrow;
#    print $subcolumns[0]."\n";
    $dotblot = $subcolumns[0];

	
    if($columns[$messagecountColNum] > 2 &&
       $dotblot >0) {
	$badOrfAry[$columns[$orfidColNum]] = 1;
    } else {
	if(!$first) {
	    $constraint .= " OR ";
	}
	$first = 0;
	$constraint .= " orfs.orfid=".$columns[$orfidColNum];
	$indVarStr .= $columns[$messagecountColNum]/(2**$dotblot).", ";
	$badOrfAry[$columns[$orfidColNum]] = -1;
	$indVarHash{$columns[$orfidColNum]} = $columns[$messagecountColNum]/(2**$dotblot);
    }
}
$indVarStr = substr($indVarStr, 0, length($indVarStr)-2);
$indVarStr .= ")\n";




my %codonHash = ();

@nucleotides = ("A", "T", "G", "C");
for($i=0; $i<@nucleotides; $i++) {
    for($j=0; $j<@nucleotides; $j++) {
	for($k=0; $k<@nucleotides; $k++) {
	    my $name = $nucleotides[$i].$nucleotides[$j].$nucleotides[$k];
	    $codonHash{$name} = 0;
	}
    }
}






#$statement = "SELECT * FROM orfs".$constraints;

#@row_ary = $dbh->selectrow_array("SELECT * FROM orfs");
#foreach $row (@row_ary) {
#    print $row;
#}

$sequenceColNum = 0;
$orfidColNum = 0;

$sth = $dbh->prepare("SELECT * FROM orfs");
$sth->execute( );
@names = @{$sth->{NAME}};
for($i=0; $i<@names; $i++) {
    if ($names[$i] eq "sequence") {
	$sequenceColNum = $i;
    }
    if ($names[$i] eq "orfid") {
	$orfidColNum = $i;
    }

}



%orfHash = ();

while (@columns = $sth->fetchrow ) {
#    $orfHash{$columns[$orfidColNum]} = ();
    for($i=0; $i<@nucleotides; $i++) {
	for($j=0; $j<@nucleotides; $j++) {
	    for($k=0; $k<@nucleotides; $k++) {
		my $name = $nucleotides[$i].$nucleotides[$j].$nucleotides[$k];
		$orfHash{$columns[$orfidColNum]}{$name} = 0;
	    }
	}
    }

}


open(NEWOUT, ">newout");
print NEWOUT "indvar\t";

for($i=0; $i<@nucleotides; $i++) {
    for($j=0; $j<@nucleotides; $j++) {
	for($k=0; $k<@nucleotides; $k++) {
	    my $name = $nucleotides[$i].$nucleotides[$j].$nucleotides[$k];
	    print NEWOUT $name."\t";
	}
    }
}
print NEWOUT "\n";


$sth->execute( );
while (@columns = $sth->fetchrow ) {
    if(!$badOrfAry[$columns[$orfidColNum]] || $badOrfAry[$columns[$orfidColNum]] == 1) {
	next;
    }

    print NEWOUT $indVarHash{$columns[$orfidColNum]}."\t";
    
    $seq = $columns[$sequenceColNum];
    if($seq ne "") {
	if(length($seq) - length($seq)/3*3 != 0) {
	    print "ALERT:".length($seq);
	    print length($seq) - length($seq)/3*3;
	    die;
	}
	for($i=0; $i<length($seq); $i+=3) {
	    $codon = substr($seq, $i, 3);
	    $orfHash{$columns[$orfidColNum]}{$codon}++;
	}
    }
    
    for($i=0; $i<@nucleotides; $i++) {
	for($j=0; $j<@nucleotides; $j++) {
	    for($k=0; $k<@nucleotides; $k++) {
		my $name = $nucleotides[$i].$nucleotides[$j].$nucleotides[$k];
		print NEWOUT $orfHash{$columns[$orfidColNum]}{$name}."\t";
	    }
	}
    }
    print NEWOUT "\n";
	
#    $orfHash[$orfidColNum][
}

open(OUTFILE, ">outfile");
print OUTFILE $indVarStr."\n";
for($i=0; $i<@nucleotides; $i++) {
    for($j=0; $j<@nucleotides; $j++) {
	for($k=0; $k<@nucleotides; $k++) {
	    my $name = $nucleotides[$i].$nucleotides[$j].$nucleotides[$k];
	    $retstr = $name." <- c(";
	    $sth->execute( );
	    while (@columns = $sth->fetchrow ) {
		if(!$badOrfAry[$columns[$orfidColNum]] || $badOrfAry[$columns[$orfidColNum]] == 1) {
		    next;
		}
		$retstr .= $orfHash{$columns[$orfidColNum]}{$name}.", ";
	    }
	    chomp $retstr;
	    chomp $retstr;
	    $retstr = substr($retstr, 0, length($retstr)-2);
	    $retstr .= ")\n";
	    print OUTFILE $retstr;
	}
    }
}




	    
exit;

