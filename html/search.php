<?php

require("locInclude.php");
require("$include_dir/include.php");
//require("$include_dir/secure.php");
require("$include_dir/projects_inc.php");

?>

<html>

<?php

// initialize 
$orfIdToSearchList = NULL;
$orfsToSearchFinal = Array();

print "<head>\n";

dumpStyleForHeader();
dumpStyleForPopover();
dumpFunctionForPreload();
$frame = FALSE;
dumpOrfDisplayHeaderJs($frame);
dumpExampleWindowJsFunctions();

print "<link rel=\"stylesheet\" href=\"formatQuery.css\">";
print "<meta http-equiv=\"Pragma\" content=\"no-cache\">";
print "<meta http-equiv=\"expires\" content=\"0\">";
print "<meta name=\"robots\" content=\"index,nofollow\">";

print "</head>\n";

// function required for clip switching
dumpBodyForJsIcons();

//pass($priv['User']);

// GET ALL THE ORFS TO SEARCH FROM POST VARS
foreach ($_POST as $key => $val) {
//	print "K:$key-> V:$val<br>";
//	print "$_POST<br>";

  if (preg_match("/gene_\w+/", $key)) {
    // resolved ambiguous gene names
    $orfnumber = getOneToOneMatch("orfs","orfid",$val,"orfnumber");
    $orfsToSearch[] = $orfnumber;
	
  } elseif (preg_match("/searchedOrf\d+/", $key)) {
    // already verified
    $orfsToSearch[] = $val;

  } elseif (preg_match("/orfid_/", $key)) {
    // orfs coming from query.php call from the summary table form
    // do not need to verify these, because they come from query.php
    $orfIdToSearchList[] = $val;   

  } elseif (preg_match("/orfsNotFound/", $key)) {
    // orfs that were searched and not found....
    $orfsNotFound[] = $val;
 }
}

//print "ORF IDs: $orfIdToSearchList[0]<br>";

// GET ALL NEW ORFS TO SEARCH FROM THE TEXT FIELD
if(isset($_POST['orf_number'])) {

  // CHECK FOR BLANKS
  if ($_POST['orf_number'] == "") {
    $orfsNotFound[] = "empty";
  } else {    
    $results = checkAliases(rtrim($_POST['orf_number']));
 
    $orfsToSearch = $results[0];
    $genesWithAliasIssues = $results[1];
    $orfsNotFound = $results[2];
  }
}

// REMOVE DUPLICATES

if ($orfsToSearch) {
	$orfsToSearchFinal = array_unique($orfsToSearch);
} elseif ($orfIdToSearchList) {
	$orfsToSearchFinal = array_unique($orfIdToSearchList);
}

// HANDLE ALL AMBIGUOUS GENE NAMES
if ($genesWithAliasIssues != "") {

  $radio = TRUE;
  
  // print alias display for selection
  print "<h1>One or more gene names entered is not unique...</h1>";
  print "<p>Please select the genes of interest from the tables below.<br>";
  print "<form method='post' action='".$_SERVER['PHP_SELF']."' target='scoring'>";
  
  foreach ($genesWithAliasIssues as $inputGene) {

    makeAliasTable($inputGene,$radio);

  }
  
  // RETAIN PREVIOUSLY VALIDATED ORFS AS HIDDEN VARIABLES
  if(isset($orfsToSearch)) {
    foreach ($orfsToSearch as $key => $orfnumber) {
      print "<input type=hidden name='searchedOrf".$key."' value='".$orfnumber."'>";
    }
  }

  if(isset($orfIdToSearchList)) {
    foreach ($orfIdToSearchList as $key => $orfid) {	
      print "<input type=hidden name='searchedOrf".$key."' value='".$orfid."'>";
    }
  }

  // RETAIN PREVIOUSLY NOT FOUND ORFS FOR USER NOTIFICATION
  if(isset($orfsNotFound)) {
     foreach ($orfsNotFound as $key => $search) {
       print "<input type=hidden name='orfsNotFound".$key."' value='".$search."'>";
     }
  }
  
  print "<input type=submit value='go'>";
  print "</form>";
  
} else {

 if (count($orfsToSearchFinal) == 0) {  // NO results matching

 } else {

  // NO GENES WITH ALIAS ISSUES
  
  //  print_r($orfsToSearchFinal);
  //  print_r($genesWithAliasIssues);
  
  print "<table border=0><tr><td><h1>Search results... </h1></td><td valign=top>&nbsp;&nbsp;<a href=\"javascript: openOrfExample();\"> &lt;example&gt; </a>\n";
  print "&nbsp;&nbsp;<a href=\"javascript: openLegend();\">&lt;legend&gt;</a>";
  print "&nbsp;&nbsp;<a href=\"javascript: openAbundance();\">&lt;abundance description&gt;</a></td></tr></table>";


  foreach ($orfsToSearchFinal as $orf) {

    if (preg_match("/(\w+)\sNOT_FOUND/",$orf,$match)) {
      $orfsNotFound[] = $match[1];
	print "$orf is not found<br>";
      continue;
    }

    $orfid = convertOrfnumberOrOrfnameToOrfid($orf);
    if (preg_match("/\D/",$orfid)) {
      $notFoundList[] = $orf;
    } else {
      $orfIdToSearchList[] = $orfid;
    }
    
  }

  // PRINT THE GRAPHICAL ORF DISPLAY
  displayBestLocs($orfIdToSearchList);

  }  // End else statement

  // NOTIFY THE USER IF THERE ARE ANY UNFOUND SEARCH TERMS
  if (isset($orfsNotFound)) {
    print "<br><br><h1>The following search terms did not match anything in the database....</h1>";
    print "<ul>";
    foreach ($orfsNotFound as $val) {
      print "<li>".$val;
    }
    print "</ul>";
  }
  

  // CURRENT IMPLEMENTATION OF PAGE ENDS HERE
  exit;
  
  
  // DEPRECATED GRAPH DRAWING BEGINS HERE

  $outFile = "/tmp/orfs.txt";
  if (!$fp = fopen($outFile, 'w')) {
    print "cannot open file ".$outFile;
    exit;
  }
  
$orfString = "";

foreach ($orfsToSearch as $orf) {
  //  printWB($orf);
  $orfString .= $orf . " ";
}
if (!fwrite($fp,$orfString)) {
  print "failed to write";
  exit;
}
fclose($fp);

system("rm -f /tmp/err /tmp/out /tmp/temp.ps /tmp/temp.pdf /home/gfp/images/tmp/temp.pdf /tmp/runR.rout");

$cmd = "/usr/java/j2re1.4.1_01/bin/java -classpath /home/lcgerke/javaWork:/home/lcgerke/javaWork/makeRBarplot:/home/lcgerke/ parseResults -opt 0x47 -rScript /tmp/runR.R -geneFile /tmp/orfs.txt -outFile /tmp/out  -tempDir /tmp -psFile temp.ps";
//print $cmd;
$a = system($cmd . " &> /tmp/err");
if($a != "") {
  print("error in the java step.  Check /tmp/err");
  exit;
}
/*
$cmd = "cp /home/lcgerke/javaWork/makeRBarplot/runR.R  /tmp/runR.R";
$a = system($cmd . " &> /tmp/err");
if($a != "") {
  print("error in cp1 step.  Check /tmp/err");
  exit;
}
*/
$cmd = "/usr/bin/R --no-save < /tmp/runR.R > /tmp/runR.rout";
$a = exec($cmd);
if($a != "") {
    print("error in the R step." . $a);
  exit;
}
//system($cmd);
$cmd = "ps2pdf /tmp/temp.ps /home/gfp/images/tmp/temp.pdf";
$a = exec($cmd);
if($a != "") {
    print("error in the pdf step." . $a);
  exit;
}

$thisTime = time();
$cmd = "convert -rotate 90 /tmp/temp.ps /home/gfp/images/tmp/temp".$thisTime.".png";
$a = exec($cmd);
if($a != "") {
    print("error in the png step." . $a);
  exit;
}

//system($cmd . " &> /tmp/err");
 print("<a href=/images/tmp/temp.pdf target=_new2> get the simple-view pdf </a>");
 print("<img src=\"/images/tmp/temp".$thisTime.".png\">");

}



?>
</body>
</html>







