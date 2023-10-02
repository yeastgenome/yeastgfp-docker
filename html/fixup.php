<?php
require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/secure.php");
require("$include_dir/projects_inc.php");

/* set the sets that are marked scorecomplete to not be if they don't have any localizations */
//$sql = "select * from usersxscorecomplete where userid ='";//.$_SESSION['userid']."'";

$sql = "select * from usersxscorecomplete where userid ='".$_SESSION['userid']."'";
$res = dbquery($sql);
while ($row = mysqli_fetch_assoc($res)) {
	$sql2 = "select * from localization where userid='".$_SESSION['userid']."' AND setid = ".$row['setid'];
	$res2 = dbquery($sql2);
	if(mysqli_num_rows($res2) == 0) {
//		printWB($row['setid']." is a candidate");
		$sql3 = "delete from usersxscorecomplete where userid='".$_SESSION['userid']."' AND setid = ".$row['setid'];
		printWB("returning set#".$row['setid']." to the readylist");
		dbquery($sql3);
	}
}

/* UNLOCK ALL THE LOCKS AT ONCE...  DANGEROUS, BUT FUN */
printWB("\nUNLOCKING");
$sql = "select * from sets where locked='T'";
$res = dbquery($sql);
while ($row = mysqli_fetch_assoc($res)) {
	printWB("unlocking set#".$row['setid']);
	$sql2 = "update sets set locked='F' where setid = ".$row['setid'];
	dbquery($sql2);
}

/*
$completeOrfArray = getAllOrfs();
foreach($completeOrfArray as $orfid) {
  $sql = "select * from strains where orfid=".$orfid." AND tag='GFP'";
  $res = dbquery($sql);
	//  assert(mysqli_num_rows($res) == 2);
  $allNoGFP = true;
  $existsScoreMark = false;
  while($row = mysqli_fetch_assoc($res)) {
    $sqlGetSets = "select * from sets where strainid=".$row['strainid'];
    $resGetSets = dbquery($sqlGetSets);
    while($rowSets = mysqli_fetch_assoc($resGetSets)) {
      if($rowSets['score_this'] == "T") {
	$existsScoreMark = true;
      }
      if($rowSets['no_gfp_visible'] == "F") {
	$allNoGFP = false;
      }
    }

  }

  if(!$allNoGFP && !$existsScoreMark) {
    printWB("got one".$orfid);
    $sql = "select * from strains where orfid=".$orfid." AND tag='GFP'";
    $res = dbquery($sql);
	//    assert(mysqli_num_rows($res) == 2);
    while($row = mysqli_fetch_assoc($res)) {
      $sqlGetSets = "select * from sets where strainid=".$row['strainid'];
      $resGetSets = dbquery($sqlGetSets);
      while($rowSets = mysqli_fetch_assoc($resGetSets)) {
	printWB("set: ".$rowSets['setid']);
	$sqlUpdate = "update sets set prune_complete='F' where setid=".$rowSets['setid'];
	printWB($sqlUpdate);
dbquery($sqlUpdate);
      }
    }
    //  $sqlUpdate = "update s
  }
  
}

*/



?>


