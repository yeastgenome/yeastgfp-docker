<?php
require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/secure.php");
require("$include_dir/projects_inc.php");


/* GLOBAL SET PRUNING INFO */
$numSetsPrunedScore = "";
$numSetsPrunedDont = "";
$numSetsPrunedNoGFP = "";
$numSetsUnpruned = "";
$numSetsTotal = "";

$arrayPruneData = array();


/* GLOBAL ORF PRUNING INFO */
$numOrfsPartiallyPruned = "";
$numOrfsTotallyUnpruned = "";
$numOrfsNoSetPresent = "";
$numOrfsSetsPresentAllNoGFP = "";
$numOrfsSetsPresentNoneScoreNotAllNoGFP = "";
$numOrfsSetsPresentAtLeastOnePrunedScore = "";
$numOrfsExpected = "";

/* GLOBAL SET x USER-WISE SCORING INFO */
$numSetsMarkedScoredByPerson = array();
$averageNumLocalizationsPerSetByPerson = array();

/* GLOBAL LOCALIZATION x USER-WISE SCORING INFO */
$numLocalizationsScoredByPerson = array();
$numLocalizationsBySubcellByPerson = array();
$sqlGetUsers = "SELECT * FROM users";
$resGetUsers = dbquery($sqlGetUsers);
/* MAKE THE 2-D ARRAYS THAT WILL HOLD THE BIN INFO PER USER AND PER CLASS */
while ($row = mysqli_fetch_assoc($resGetUsers)) {
    $numLocalizationsBySubcellByPerson[$row['userid']] = array();
}


/* TOTAL PRUNED TO SCORE SETS */
$sql = "SELECT * FROM sets WHERE prune_complete = 'T' AND score_this = 'T'";
$res = dbquery($sql);
$numSetsPrunedScore = mysqli_num_rows($res);

/* TOTAL PRUNED TO NOT SCORE SETS */
$sql = "SELECT * FROM sets WHERE prune_complete = 'T' AND score_this = 'F'
        AND no_gfp_visible = 'F'";
$res = dbquery($sql);
$numSetsPrunedDont = mysqli_num_rows($res);

/* TOTAL PRUNED NOGFP SETS */
$sql = "SELECT * FROM sets WHERE prune_complete = 'T' AND no_gfp_visible = 'T'";
$res = dbquery($sql);
$numSetsPrunedNoGFP = mysqli_num_rows($res);

/* MAKE SURE THERE ARE NO SCORE BUT NO GFP SETS */
$sql = "SELECT * FROM sets WHERE prune_complete = 'T'
        AND no_gfp_visible = 'T'
        AND score_this = 'T'";
$res = dbquery($sql);
assert(mysqli_num_rows($res) == 0);

/* TOTAL UNPRUNED SETS */
$sql = "SELECT * FROM sets WHERE prune_complete = 'F'";
$res = dbquery($sql);
$numSetsUnpruned = mysqli_num_rows($res);

/* TOTAL NUMBER OF SETS */
$sql = "SELECT * FROM sets";
$res = dbquery($sql);
$numSetsTotal = mysqli_num_rows($res);
assert($numSetsTotal == $numSetsPrunedScore + $numSetsPrunedDont +
       $numSetsPrunedNoGFP + $numSetsUnpruned);


/* TOTAL NUM TOTALLY PRUNED ORFS */
$sqlGetOrfs = "SELECT * FROM orfs";
$resGetOrfs = dbquery($sqlGetOrfs);

$numOrfsPartiallyPruned = 0;
$numOrfsTotallyUnpruned = 0;
$numOrfsNoSetPresent = 0;
$numOrfsSetsPresentAllNoGFP = 0;
$numOrfsSetsPresentNoneScoreNotAllNoGFP = 0;
$numOrfsSetsPresentAtLeastOnePrunedScore = 0;
$numOrfsExpected = 0;


/* CALCULATE THE COVERAGE OF THE DIFFERENT LIBRARIES */

$doCoverage = 1;
if($doCoverage) {
while ($rowOrfs = mysqli_fetch_assoc($resGetOrfs)) {

    $atLeastOnePruned = 'F';
    $allPruned = 'T';
    $atLeastOneSetExists = 'F';
    $atLeastOnePrunedScore = 'F';
    $allNoGFP = 'T';

    $sqlGetStrains = "SELECT * FROM strains WHERE tag='GFP' AND tag_success='T' AND orfid=".$rowOrfs['orfid'];
    $resGetStrains = dbquery($sqlGetStrains);
    if(mysqli_num_rows($resGetStrains) == 0) {
        printWB($rowOrfs['orfnumber']."wasn't successfully tagged in GFP\n");
    }

    while ($rowStrains = mysqli_fetch_assoc($resGetStrains)) {
	$sqlGetSets = "SELECT * FROM sets WHERE strainid =".
	    $rowStrains['strainid'];
	$resGetSets = dbquery($sqlGetSets);
	while ($rowGetSets = mysqli_fetch_assoc($resGetSets)) {
	    $atLeastOneSetExists = 'T';
	    if($rowGetSets['prune_complete'] == 'T') {
		$atLeastOnePruned = 'T';
		if($rowGetSets['score_this'] == 'T') {
		    $atLeastOnePrunedScore = 'T';
		}
		if($rowGetSets['no_gfp_visible'] == 'F') {
		    $allNoGFP = 'F';
		}
	    } else {
		$allPruned = 'F';
	    }
	}
    }

    if($atLeastOneSetExists = 'T') {
	if($atLeastOnePruned == 'F') {
	    $numOrfsTotallyUnpruned++;
//            printWB($rowOrfs['orfid']);
	} else if($atLeastOnePruned == 'T' && $allPruned == 'F') {
	    $numOrfsPartiallyPruned++;
	} else {
	    assert($atLeastOnePruned == 'T' && $allPruned == 'T');
	    if($atLeastOnePrunedScore == 'T' && $allNoGFP == 'F') {
		$numOrfsSetsPresentAtLeastOnePrunedScore++;
	    } else if ($atLeastOnePrunedScore == 'T' && $allNoGFP == 'T') {
		assert(0);
	    } else if ($atLeastOnePrunedScore == 'F' && $allNoGFP == 'T') {
		$numOrfsSetsPresentAllNoGFP++;
	    } else {
		assert($atLeastOnePrunedScore == 'F' && $allNoGFP == 'F');
		$numOrfsSetsPresentNoneScoreNotAllNoGFP++;
	    }
	}
    } else {
	$numOrfsNoSetPresent++;
    }
    $numOrfsExpected++;
    if($allNoGFP == 'T') {
        $noGFPOrfList[] = $rowOrfs['orfid'];
	printWB($rowOrfs['orfnumber']." NO SIGNAL");
    } else {
      printWB($rowOrfs['orfnumber']." PRESENT");
    }

}


$orfList = getAllOrfs();
foreach($orfList as $orfid) {
  $signalPresent = false;
  $tagSuccess = false;
  $strainList = getOneToMany("strains", "orfid", $orfid, "strainid");
  foreach($strainList as $strainid) {
    if(getOneToOneMatch("strains", "strainid", $strainid, "tag_success") == "F") {
      assert(getOneToOneMatch("dotblot", "strainid", $strainid, "dotblotscore") == 0);
    } else {
      if(getOneToOneMatch("dotblot", "strainid", $strainid, "dotblotscore") > 1) {
          $signalPresent = true;
      }
    }
  }
  if($signalPresent != true) {
    $noTAPOrfList[] = $orfid;
  }
}

$GFPMissingList = array_diff($noGFPOrfList, $noTAPOrfList);
$TAPMissingList = array_diff($noTAPOrfList, $noGFPOrfList);
$noneMissingList = getAllOrfs();
$noneMissingList = array_diff($noneMissingList, $noGFPOrfList);
$noneMissingList = array_diff($noneMissingList, $noTAPOrfList);
$bothMissingList = getAllOrfs();
$bothMissingList = array_diff($bothMissingList, $noneMissingList);
$bothMissingList = array_diff($bothMissingList, $GFPMissingList);
$bothMissingList = array_diff($bothMissingList, $TAPMissingList);

printWB("totals: nonemissing".count($noneMissingList).", bothmissing".count($bothMissingList).", gfpmissing".count($GFPMissingList).", tapmissing".count($TAPMissingList).", ");

printWB("MISSING IN TAP ONLY");
foreach($TAPMissingList as $orfid) {
//  printWB(getOneToOneMatch("orfs", "orfid", $orfid, "orfnumber")." :: ".$orfid);
}
printWB("TOTAL: ".count($TAPMissingList));
printWB("*********");
printWB("MISSING IN GFP ONLY");
foreach($GFPMissingList as $orfid) {
  $sqlGetDB = "select * from orfs inner join strains on strains.orfid=orfs.orfid left join sets on sets.strainid=strains.strainid left join usersxscorecomplete on usersxscorecomplete.setid=sets.setid inner join dotblot on dotblot.strainid=strains.strainid where orfs.orfid=$orfid";
  $resGetDB = dbquery($sqlGetDB);
  assert(mysqli_num_rows($resGetDB) == 1);
  $row = mysqli_fetch_assoc($resGetDB);
  $dbscore = $row['dotblotscore'];
  printWB(getOneToOneMatch("orfs", "orfid", $orfid, "orfnumber")." :: ".$orfid." :: db $dbscore");



}
printWB("TOTAL: ".count($GFPMissingList));
printWB("*********");
printWB("MISSING IN BOTH");
foreach($bothMissingList as $orfid) {
//  printWB(getOneToOneMatch("orfs", "orfid", $orfid, "orfnumber")." :: ".$orfid);
}
printWB("TOTAL: ".count($GFPMissingList));
printWB("*********");
printWB("MISSING IN NONE");
foreach($noneMissingList as $orfid) {
//  printWB(getOneToOneMatch("orfs", "orfid", $orfid, "orfnumber")." :: ".$orfid);
}
printWB("TOTAL: ".count($GFPMissingList));

}



/* LOCALIZATIONS BY USER */
/* INITIALIZE THE 2-D ARRAY */
$sqlGetUsers = "SELECT * FROM users";
$resGetUsers = dbquery($sqlGetUsers);
while ($row = mysqli_fetch_assoc($resGetUsers)) {
  $sqlGetSubcells = "SELECT * FROM subcell";
  $resGetSubcells = dbquery($sqlGetSubcells);
  while ($row2 = mysqli_fetch_assoc($resGetSubcells)) {
    $numLocalizationsBySubcellByPerson[$row['userid']][$row2['subcellid']] = 0;
  }
}

/* LOOP THROUGH EACH USER */
$sqlGetUsers = "SELECT * FROM users";
$resGetUsers = dbquery($sqlGetUsers);
while ($row = mysqli_fetch_assoc($resGetUsers)) {
  $user = $row['userid'];
  $sqlGetSets = "SELECT * FROM usersxscorecomplete WHERE userid = ".$user;
  $sqlGetLocs = "SELECT * FROM localization WHERE userid = ".$user;

  $numSetsMarkedScoredByPerson[$user] = mysqli_num_rows(dbquery($sqlGetSets));
  $resGetLocs = dbquery($sqlGetLocs);
  $averageNumLocalizationsPerSetByPerson[$user] = mysqli_num_rows($resGetLocs) /
    $numSetsMarkedScoredByPerson[$user];
  
  //  print "<br>".mysqli_num_rows(dbquery($sqlGetLocs))."<br>";
  //print "<br>".$numSetsMarkedScoredByPerson[$user]."<br>";
  
  while ($rowLocs = mysqli_fetch_assoc($resGetLocs)) {
    $numLocalizationsBySubcellByPerson[$user][$rowLocs['subcellid']]++;
    //    printWB("user:".$user);
    //    printWB($rowLocs['subcellid']);
  }
}

/* SEE HOW MANY HAVE BEEN SCORED ONCE/TWICE */
$numSetsScoredByNumberTimesScored = array();

$sqlGetSets = "SELECT * FROM sets WHERE score_this = 'T'";
$resGetSets = dbquery($sqlGetSets);
while ($row = mysqli_fetch_assoc($resGetSets)) {
  $setid = $row['setid'];
  $sqlGetCompleteRecords = "SELECT * FROM usersxscorecomplete where setid =".
     $setid;
  $numTimesScored = mysqli_num_rows(dbquery($sqlGetCompleteRecords));
  $numSetsScoredByNumberTimesScored[$numTimesScored]++;
  if (mysqli_num_rows(dbquery($sqlGetCompleteRecords)) > 2) {
//     printWB ($setid);
  }
}

class DayClass {

  var $year;
  var $month;
  var $day;

  
  function DayClass($timestamp) {
    $ymd = getYearMonthDay($timestamp);
    $this->year = $ymd[0];
    $this->month = $ymd[1];
    $this->day  = $ymd[2];
  }

  function sameDay($timestamp) {
    $ymd = getYearMonthDay($timestamp);
    if($this->year != $ymd[0]  ||
       $this->month != $ymd[1] ||
       $this->day != $ymd[2]) {
      return false;
    }
    return true;
  }

  function printDay() {
    print $this->year."/".$this->month."/".$this->day."<br>\n";
  }

  function sprintDay() {
    return $this->year."/".$this->month."/".$this->day;
  }
  
}

function getYearMonthDay($timestamp) {
  $date = getdate(time());
  $date = getdate($timestamp);
  $y = $date['year'];
  $m = $date['mon'];
  $d = $date['mday'];
  $list = array($y,$m,$d);
  //  print_r(array_values($list));
  return $list;
}

class DayStatsClass {
  var $dayObj;
  var $localizationsScoredByPerson;
  var $setsScoredByPerson;

  function DayStatsClass($timestamp) {
    $this->dayObj = new DayClass($timestamp);
//    print "making a new DayStatsClass";
    $this->localizationsScoredByPerson = array();
    $this->setsScoredByPerson = array();

    /* INITIALIZE THE ARRAYS */
    $sqlGetUsers = "SELECT * FROM users";
    $resGetUsers = dbquery($sqlGetUsers);
    while ($row = mysqli_fetch_assoc($resGetUsers)) {
      $this->localizationsScoredByPerson[$row['userid']] = 0;
      $this->setsScoredByPerson[$row['userid']] = 0;
    }

  }

  function sameDay($timestamp) {
    return $this->dayObj->sameDay($timestamp);
  }

  
  function incLocs($userid) {
    $this->localizationsScoredByPerson[$userid]++;
  }

  function incSets($userid) {
    $this->setsScoredByPerson[$userid]++;
  }

  function getLocs($userid) {
    return $this->localizationsScoredByPerson[$userid];
  }

  function getSets($userid) {
    return $this->setsScoredByPerson[$userid];
  }

  function printDayStats() {
    $this->dayObj->printDay();

    $sqlGetUsers = "SELECT * FROM users";
    $resGetUsers = dbquery($sqlGetUsers);
    
    while ($row = mysqli_fetch_assoc($resGetUsers)) {
      $user = $row['userid'];
    }
  }
}
/* LOCALIZATIONS BY DAY */
$dayStatsList = array();

$sqlGetAllLocs = "SELECT UNIX_TIMESTAMP(timeofentry) AS unixtime, userid FROM localization";
$resGetAllLocs = dbquery($sqlGetAllLocs);
while ($row = mysqli_fetch_assoc($resGetAllLocs)) {
  $time = $row['unixtime'];
  $dayExisted = false;
  for($a=0; $a<count($dayStatsList); $a++) {
    $dayStats = &$dayStatsList[$a];
    if($dayStats->sameDay($time)) {
      $dayStats->incLocs($row['userid']);
      $dayExisted = true;
      break;
    }
  }
  if(!$dayExisted) {
    $newDayStats = new DayStatsClass($time);
    $newDayStats->incLocs($row['userid']);
    $dayStatsList[] = $newDayStats;
    
  }
}

/* NOW SETS THE SAME WAY */
$sqlGetAllSetsComplete = "SELECT UNIX_TIMESTAMP(timeofentry) AS unixtime, userid FROM usersxscorecomplete";
$resGetAllSetsComplete = dbquery($sqlGetAllSetsComplete);
while ($row = mysqli_fetch_assoc($resGetAllSetsComplete)) {
  $time = $row['unixtime'];
  $dayExisted = false;
  for($a=0; $a<count($dayStatsList); $a++) {
    $dayStats = &$dayStatsList[$a];
    if($dayStats->sameDay($time)) {
      $dayStats->incSets($row['userid']);
      $dayExisted = true;
      break;
    }
  }
  if(!$dayExisted) {
    assert(false);
  }
}

/* BUILD THE SET PROGRESS GRAPH */
/* FOR WON-KI */
$outFileBase = "setProgressGraphWonKi";
$epsFile = $tmp_dir.$outFileBase.".eps";
$outFile = $tmp_dir.$outFileBase.".jpg";

$RString = "postscript(\"".$epsFile."\")\n";
$RString .= "barplot(\n";
$RString .= "horiz=F,\n";
$RString .= "space=0.1,\n";
$RString .= "c(\n";
for($a=0; $a<count($dayStatsList); $a++) {
  $dayStats = &$dayStatsList[$a];
  //  $dayStats->printDayStats();
  $RString .= $dayStats->setsScoredByPerson[3];
  if($a != count($dayStatsList) - 1) {
    $RString .= ",";
  }
}
$RString .= "),\n";

$RString .= "names.arg =c(\n";
for($a=0; $a<count($dayStatsList); $a++) {
  $dayStats = &$dayStatsList[$a];
  //  $dayStats->printDayStats();
  
  $RString .= "\"".$dayStats->dayObj->sprintDay()."\"";
  if($a != count($dayStatsList) - 1) {
    $RString .= ",";
  }
}
$RString .= ")\n";
$RString .= ")\n";
$RString .= "title(main = \"Total Sets Scored By Date for Won-Ki (~= Strains ~= Orfs)\", font.main = 4)\n";
$RString .= "dev.off()\n";

drawGraphFromRString($RString,$tmp_dir,$outFileBase);

/* BUILD THE SET PROGRESS GRAPH */
/* FOR JAMES */
$outFileBase = "setProgressGraphJames";
$epsFile = $tmp_dir.$outFileBase.".eps";
$outFile = $tmp_dir.$outFileBase.".jpg";

$RString = "postscript(\"".$epsFile."\")\n";
$RString .= "barplot(\n";
$RString .= "horiz=F,\n";
$RString .= "space=0.1,\n";
$RString .= "c(\n";
for($a=0; $a<count($dayStatsList); $a++) {
  $dayStats = &$dayStatsList[$a];
  //  $dayStats->printDayStats();
  $RString .= $dayStats->setsScoredByPerson[4];
  if($a != count($dayStatsList) - 1) {
    $RString .= ",";
  }
}
$RString .= "),\n";

$RString .= "names.arg =c(\n";
for($a=0; $a<count($dayStatsList); $a++) {
  $dayStats = &$dayStatsList[$a];
  //  $dayStats->printDayStats();
  
  $RString .= "\"".$dayStats->dayObj->sprintDay()."\"";
  if($a != count($dayStatsList) - 1) {
    $RString .= ",";
  }
}
$RString .= ")\n";
$RString .= ")\n";
$RString .= "title(main = \"Total Sets Scored By Date for James (~= Strains ~= Orfs)\", font.main = 4)\n";
$RString .= "dev.off()\n";

drawGraphFromRString($RString,$tmp_dir,$outFileBase);






/* BUILD THE SETS SCORED BY NUMBER GRAPH */
$outFileBase = "setsScoredByNumberGraph";
$epsFile = $tmp_dir.$outFileBase.".eps";
$outFile = $tmp_dir.$outFileBase.".jpg";

$RString = "postscript(\"".$epsFile."\")\n";
$RString .= "barplot(\n";
$RString .= "horiz=F,\n";
$RString .= "space=0.1,\n";
$RString .= "c(\n";

ksort($numSetsScoredByNumberTimesScored);
$RString .= arrayToCommaList($numSetsScoredByNumberTimesScored,"");

$RString .= "),\n";

$RString .= "names.arg =c(\n";
$RString .= arrayToCommaListOfKeys($numSetsScoredByNumberTimesScored,"\"");

$RString .= ")\n";
$RString .= ")\n";

$RString .= "title(main = \"Number of Sets Scored by Number of Times Scored by Person\", font.main = 4)\n";
$RString .= "dev.off()\n";

drawGraphFromRString($RString,$tmp_dir,$outFileBase);















/* BUILD THE LOC PROGRESS GRAPH */
$outFileBase = "locProgressGraph";
$epsFile = $tmp_dir.$outFileBase.".eps";
$outFile = $tmp_dir.$outFileBase.".jpg";

$RString = "postscript(\"".$epsFile."\")\n";
$RString .= "barplot(\n";
$RString .= "horiz=F,\n";
$RString .= "space=0.1,\n";
$RString .= "c(\n";
for($a=0; $a<count($dayStatsList); $a++) {
  $dayStats = &$dayStatsList[$a];
//  $dayStats->printDayStats();
  $RString .= array_sum($dayStats->localizationsScoredByPerson);
  if($a != count($dayStatsList) - 1) {
    $RString .= ",";
  }
}
$RString .= "),\n";

$RString .= "names.arg =c(\n";
for($a=0; $a<count($dayStatsList); $a++) {
  $dayStats = &$dayStatsList[$a];
//  $dayStats->printDayStats();
  
  $RString .= "\"".$dayStats->dayObj->sprintDay()."\"";
  if($a != count($dayStatsList) - 1) {
    $RString .= ",";
  }
}
$RString .= ")\n";
$RString .= ")\n";
$RString .= "title(main = \"Total Localizations Scored By Date\", font.main = 4)\n";
$RString .= "dev.off()\n";

drawGraphFromRString($RString,$tmp_dir,$outFileBase);

/* BUILD THE PIE GRAPH OF LOCALIZATIONS */
$outFileBase = "localizationPieChart";
$epsFile = $tmp_dir.$outFileBase.".eps";
$outFile = $tmp_dir.$outFileBase.".jpg";

$locHist = array();

$sqlGetAllSubcells = "SELECT * FROM subcell";
$resGetAllSubcells = dbquery($sqlGetAllSubcells);
while ($row = mysqli_fetch_assoc($resGetAllSubcells)) {
  $locHist[$row['subcellname']] = 0.0001;
}

$sqlGetAllLocalization = "SELECT * FROM localization
                          INNER JOIN subcell ON subcell.subcellid=localization.subcellid";
$resGetAllLocalization = dbquery($sqlGetAllLocalization);
while ($row = mysqli_fetch_assoc($resGetAllLocalization)) {
  $locHist[$row['subcellname']]++;
}

//print_r(array_values($locHist));

arsort($locHist);

$locRString = "postscript(\"".$epsFile."\")\n";
$locRString .= "pie(\n";
$locRString .= "radius=0.9,\n";
$locRString .= "c(";

$listLocVals = arrayToCommaList($locHist,"");
$locRString .= $listLocVals;
$locRString .= "),\n";

$locRString .= "labels =c(\n";
$listLocKeys = arrayToCommaListOfKeys($locHist,"\"");
$locRString .= $listLocKeys."\n";
$locRString .= "))\n";
$locRString .= "title(main = \"Subcellular Localizations in Scored Sets To Date\", font.main = 4)\n";

$locRString .= "dev.off()\n";

//print $locRString;

drawGraphFromRString($locRString,$tmp_dir,$outFileBase);

$doPruning = 0;
if($doPruning) {
/* BUILD THE PRUNING PIE CHART */
$outFileBase = "pruningProgressPieChart";
$epsFile = $tmp_dir.$outFileBase.".eps";
$outFile = $tmp_dir.$outFileBase.".jpg";

$locRString = "postscript(\"".$epsFile."\")\n";
$locRString .= "pie(\n";
$locRString .= "radius=0.9,\n";
$locRString .= "c(";

$arrayPruneData["Number of Sets Pruned 'Score'"] = $numSetsPrunedScore + .0001;
$arrayPruneData["Number of Sets Pruned 'Don't Score'"] = $numSetsPrunedDont+ 0.0001;
$arrayPruneData["Number of Sets Pruned 'No GFP Visible'"] = $numSetsPrunedNoGFP + 0.0001;
$arrayPruneData["Number of Unpruned Sets"] = $numSetsUnpruned + 0.0001;

arsort($arrayPruneData);
//print_r(array_values($arrayPruneData));
$listLocVals = arrayToCommaList($arrayPruneData,"");
printWB($listLocVals);
$locRString .= $listLocVals;
$locRString .= "),\n";

$locRString .= "labels =c(\n";
$listLocKeys = arrayToCommaListOfKeys($arrayPruneData,"\"");
$locRString .= $listLocKeys."\n";
$locRString .= "))\n";
$locRString .= "title(main = \"Status of Set Pruning by Set\", font.main = 4)\n";

$locRString .= "dev.off()\n";

//print $locRString;

drawGraphFromRString($locRString,$tmp_dir,$outFileBase);




/* BUILD THE ORFWISE CHART */
$outFileBase = "orfInfoPieChart";
$epsFile = $tmp_dir.$outFileBase.".eps";
$outFile = $tmp_dir.$outFileBase.".jpg";

$locRString = "postscript(\"".$epsFile."\")\n";
$locRString .= "pie(\n";
$locRString .= "radius=0.9,\n";
$locRString .= "c(";

$arrayPruneData = array();
$arrayPruneData["Number of Orfs Partially Pruned"] = $numOrfsPartiallyPruned + .0001;
$arrayPruneData["Number of Orfs Totally UNPruned"] = $numOrfsTotallyUnpruned + .0001;
$arrayPruneData["Number of Orfs No Set Present"] = $numOrfsNoSetPresent + .0001;
$arrayPruneData["Number of Orfs With No GFP in Any Set"] = $numOrfsSetsPresentAllNoGFP + 0.0001;
$arrayPruneData["Number of Orfs With At Least One GFP-Present But Pruned 'don't score'"] = $numOrfsSetsPresentNoneScoreNotAllNoGFP + 0.0001;
$arrayPruneData["Number of Orfs With At Least One 'Score' Set"] = $numOrfsSetsPresentAtLeastOnePrunedScore + 0.0001;

arsort($arrayPruneData);
//print_r(array_values($arrayPruneData));
$listLocVals = arrayToCommaList($arrayPruneData,"");
printWB($listLocVals);
$locRString .= $listLocVals;
$locRString .= "),\n";

$locRString .= "labels =c(\n";
$listLocKeys = arrayToCommaListOfKeys($arrayPruneData,"\"");
$locRString .= $listLocKeys."\n";
$locRString .= "))\n";
$locRString .= "title(main = \"Status of Set Pruning by ORF\", font.main = 4)\n";

$locRString .= "dev.off()\n";

//print $locRString;

drawGraphFromRString($locRString,$tmp_dir,$outFileBase);

}




/* FUUUUNCTIONS! */
function drawGraphFromRString($RString,$tmp_dir,$outFileBase) {

//	system("chmod a+rw ".$tmp_dir."*");


$epsFile = $tmp_dir.$outFileBase.".eps";
  $outFile = $tmp_dir.$outFileBase.".jpg";

  $fp = fopen($tmp_dir.$outFileBase.".s", "w");
  fputs($fp, $RString);
  fclose($fp);
  
  $cmd = "/usr/bin/R BATCH --no-save --nogui ".$tmp_dir.$outFileBase.".s ".$tmp_dir.$outFileBase.".error > /home/gfp/err 2>&1";
  $err = system($cmd);
  
  $cmd = "convert -rotate 90 -size 1600x1600 ".$epsFile." ".$outFile;
  system($cmd);
//	print $cmd;


   $cmd = "/usr/bin/ps2pdf ".$epsFile;
   //system($cmd);

} 





?>
<!--
<table border="1">
    <tr>
        <td>
            $numSetsPrunedScore
        </td>
        <td>
            <?=$numSetsPrunedScore?>
        </td>
    </tr>
    <tr>
        <td>
            $numSetsPrunedDont
        </td>
        <td>
            <?=$numSetsPrunedDont?>
        </td>
    </tr>
    <tr>
        <td>
            $numSetsPrunedNoGFP
        </td>
        <td>
            <?=$numSetsPrunedNoGFP?>
        </td>
    </tr>
    <tr>
        <td>
            $numSetsUnpruned
        </td>
        <td>
            <?=$numSetsUnpruned?>
        </td>
    </tr>
</table>


<table border="2">
    <tr>
        <td>
            $numOrfsPartiallyPruned
        </td>
        <td>
            <?=$numOrfsPartiallyPruned?>
        </td>
    </tr>
    <tr>
        <td>
            $numOrfsTotallyUnpruned
        </td>
        <td>
            <?=$numOrfsTotallyUnpruned?>
        </td>
    </tr>
    <tr>
        <td>
            $numOrfsNoSetPresent
        </td>
        <td>
            <?=$numOrfsNoSetPresent?>
        </td>
    </tr>
    <tr>
        <td>
            $numOrfsSetsPresentAllNoGFP
        </td>
        <td>
            <?=$numOrfsSetsPresentAllNoGFP?>
        </td>
    </tr>
    <tr>
        <td>
            $numOrfsSetsPresentNoneScoreNotAllNoGFP
        </td>
        <td>
            <?=$numOrfsSetsPresentNoneScoreNotAllNoGFP?>
        </td>
    </tr>
    <tr>
        <td>
            $numOrfsSetsPresentAtLeastOnePrunedScore 
        </td>
        <td>
            <?=$numOrfsSetsPresentAtLeastOnePrunedScore?>
        </td>
    </tr>
    <tr>
        <td>
            $numOrfsExpected
        </td>
        <td>
            <?=$numOrfsExpected?>
        </td>
    </tr>
</table>
-->
<img src="images/tmp/localizationPieChart.jpg">
<img src="images/tmp/locProgressGraph.jpg">
<img src="images/tmp/setProgressGraphWonKi.jpg">
<img src="images/tmp/setProgressGraphJames.jpg">
<?
if($doPruning) {
print "<img src=\"images/tmp/pruningProgressPieChart.jpg\">";
print "<img src=\"images/tmp/orfInfoPieChart.jpg\">";
}
?>
<img src="images/tmp/setsScoredByNumberGraph.jpg">



