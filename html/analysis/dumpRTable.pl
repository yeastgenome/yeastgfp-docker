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


%indVarHash = ();
%dbScoreHash = ();
%proteinHash = ();
%messageHash = ();



$statement = "SELECT orfs.orfid, greenbaumdata.messagecount, gelwesterntapdata.proteincount FROM orfs inner join greenbaumdata on orfs.orfid=greenbaumdata.orfid inner join gelwesterntapdata on orfs.orfid=gelwesterntapdata.orfid WHERE greenbaumdata.conditionid=5";
$sth = $dbh->prepare($statement);
$sth->execute( );

#while( $row = $sth->fetchrow_hash( ))#{
#    echo "Data: ".$row[orfid]."<br>\n";
#}


while($hash_ref = $sth->fetchrow_hashref) {
#    print $hash_ref->{'orfid'};
}


$sth->execute( );
while ($hash_ref = $sth->fetchrow_hashref ) {


    $orfid = $hash_ref->{'orfid'};
    $messageCount = $hash_ref->{'messagecount'};
    $proteinCount = $hash_ref->{'proteincount'};
    $subStatement = "select dotblot.dotblotscore from strains inner join dotblot on strains.strainid=dotblot.strainid where strains.tag='TAP' and strains.orfid=$orfid";
    $sthSub = $dbh->prepare($subStatement);
    $sthSub->execute();
    $sub_hash_ref = $sthSub->fetchrow_hashref;
    $dotblot = $sub_hash_ref->{'dotblotscore'};
    $dbScoreHash{$orfid} = $dotblot;
    $messageHash{$orfid} = $messageCount;
    $proteinHash{$orfid} = $proteinCount;
}

my %codonHash = ();

@nucleotides = ("A", "T", "G", "C");

$sth = $dbh->prepare("SELECT * FROM orfs INNER JOIN orfsequence on orfsequence.orfid=orfs.orfid");
$sth->execute( );

%orfHash = ();
while ($hash_ref = $sth->fetchrow_hashref ) {
    $orfid = $hash_ref->{'orfid'};
    for($i=0; $i<@nucleotides; $i++) {
	for($j=0; $j<@nucleotides; $j++) {
	    for($k=0; $k<@nucleotides; $k++) {
		my $name = $nucleotides[$i].$nucleotides[$j].$nucleotides[$k];
		$orfHash{$orfid}{$name} = 0;
	    }
	}
    }
}


open(NEWOUT, ">newout");
print NEWOUT "dotblotscore\tmessagecount\tproteincount\t";

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
while ($hash_ref = $sth->fetchrow_hashref ) {
    $orfid = $hash_ref->{'orfid'};
    if($dbScoreHash{$orfid}) {
	print NEWOUT $dbScoreHash{$orfid}."\t";
    } else {
	print NEWOUT "NA\t";

    }
    if($messageHash{$orfid}) {
	print NEWOUT $messageHash{$orfid}."\t";
    } else {
	print NEWOUT "NA\t";
    }

    if($proteinHash{$orfid}) {
	print NEWOUT $proteinHash{$orfid}."\t";
    } else {
	print NEWOUT "NA\t";
    }
    
    $seq = $hash_ref->{'sequence'};;
    if($seq ne "") {
	if(length($seq) - length($seq)/3*3 != 0) {
	    print "ALERT:".length($seq);
	    print length($seq) - length($seq)/3*3;
	    die;
	}
	for($i=0; $i<length($seq); $i+=3) {
	    $codon = substr($seq, $i, 3);
	    $orfHash{$orfid}{$codon}++;
	}
    }
    
    for($i=0; $i<@nucleotides; $i++) {
	for($j=0; $j<@nucleotides; $j++) {
	    for($k=0; $k<@nucleotides; $k++) {
		my $name = $nucleotides[$i].$nucleotides[$j].$nucleotides[$k];
		print NEWOUT $orfHash{$orfid}{$name}."\t";
	    }
	}
    }
    print NEWOUT "\n";
	
#    $orfHash[$orfidColNum][
}

	    
exit;



