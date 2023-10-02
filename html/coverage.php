<html>
<head>
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="expires" content="0">
</head>

<?
require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/secure.php");
require("$include_dir/projects_inc.php");

/****  NEW TOTAL COVERAGE INFORMATION ****/
$TAPVis = array();
$TAPTag = array();
$GFPVis = array();
$GFPOldVis = array();
$GFPTag = array();

$orfList = getAllOrfs();
foreach($orfList as $orf) {

  /* DID GFP TAG SUCCESSFULLY? */
  $sql = "select * from orfs inner join strains on strains.orfid=orfs.orfid where strains.tag='GFP' and strains.tag_success='T' and orfs.orfid=$orf";
  $res = dbquery($sql);
  if(mysqli_num_rows($res)>0) {
    //    printWB("$orf was tagged by GFP");
    $GFPTag[] = $orf;
  }

  /* DID TAP TAG SUCCESSFULLY? */
  $sql = "select * from orfs inner join strains on strains.orfid=orfs.orfid where strains.tag='TAP' and strains.tag_success='T' and orfs.orfid=$orf";
  $res = dbquery($sql);
  if(mysqli_num_rows($res)>0) {
    //    printWB("$orf was tagged by TAP");
    $TAPTag[] = $orf;
  }

  /* DID GFP VISUALIZE SUCCESSFULLY? */
  $sql = "select * from orfs inner join strains on strains.orfid=orfs.orfid inner join sets on sets.strainid=strains.strainid where strains.tag='GFP' and sets.new_prune_complete='T' and new_no_gfp_visible='F' and orfs.orfid=$orf";
  $res = dbquery($sql);
  if(mysqli_num_rows($res)>0) {
    //    printWB("$orf was seen by GFP1");
    $GFPVis[] = $orf;
  }
  
  $sql = "select * from orfs inner join strains on strains.orfid=orfs.orfid inner join sets on sets.strainid=strains.strainid where strains.tag='GFP' and sets.new_prune_complete='F' and no_gfp_visible='F' and orfs.orfid=$orf";
  $res = dbquery($sql);
  if(mysqli_num_rows($res)>0) {
    //        printWB("$orf was seen by GFP2");
    $GFPVis[] = $orf;
  }



  /* DEBUG */
  $sql = "select * from orfs inner join strains on strains.orfid=orfs.orfid inner join sets on sets.strainid=strains.strainid where strains.tag='GFP' and no_gfp_visible='F' and orfs.orfid=$orf";
  $res = dbquery($sql);
  if(mysqli_num_rows($res)>0) {
    $GFPOldVis[] = $orf;
  }


  /* DID TAP VISUALIZE SUCCESSFULLY? */
  $sql = "select * from orfs inner join strains on strains.orfid=orfs.orfid inner join qualwestern on qualwestern.strainid=strains.strainid where strains.tag='TAP' and qualwestern.qualwesternscore=1 and orfs.orfid=$orf";
  $res = dbquery($sql);
  if(mysqli_num_rows($res)>0) {
    //    printWB("$orf was seen by TAP");
    $TAPVis[] = $orf;
  }








}


  $GFPVis = array_unique($GFPVis);
$GFPOldVis = array_unique($GFPOldVis);
printWB("count:".count($GFPVis)." ".count($GFPOldVis));

$diff = array_diff($GFPOldVis, $GFPVis);
foreach($diff as $diffff){
  print(",".$diffff);
}
     

/*
  printWB("GFP visible orfs");
  foreach($GFPVis as $orfid) {
    $orfnumber = getOneToOneMatch("orfs", "orfid", $orfid, "orfnumber");
    print($orfnumber.",");
  }

  printWB("GFP tagged orfs");
  foreach($GFPTag as $orfid) {
    $orfnumber = getOneToOneMatch("orfs", "orfid", $orfid, "orfnumber");
    print($orfnumber.",");
  }
*/


/*
$orfList = getAllOrfs();
foreach($orfList as $orfid) {
  $orfnumber = getOneToOneMatch("orfs", "orfid", $orfid, "orfnumber");
  print($orfnumber."\t");

  if(in_array($orfid, $GFPVis)) {
    print("1\t");
  } else {
    print("0\t");
  }

  if(in_array($orfid, $GFPTag)) {
    print("1\t");
  } else {
    print("0\t");
  }

  if(in_array($orfid, $TAPVis)) {
    print("1\t");
  } else {
    print("0\t");
  }

  if(in_array($orfid, $TAPTag)) {
    printWB("1");
  } else {
    printWB("0");
  }

}
*/

$allOrfList = getAllOrfs();
$x = array();
$x[0] = array();
$x[1] = array();
$x[2] = array();
$x[3] = array();


$x[0][0] = array_diff($allOrfList, $GFPVis);
$x[0][1] = $GFPVis;
$x[1][0] = array_diff($allOrfList, $GFPTag);
$x[1][1] = $GFPTag;
$x[2][0] = array_diff($allOrfList, $TAPVis);
$x[2][1] = $TAPVis;
$x[3][0] = array_diff($allOrfList, $TAPTag);
$x[3][1] = $TAPTag;

$mode = array();
for($i=0; $i<4; $i++) {
  
}

$t = 0;
printWB("(GFPVis, GFPTag, TAPVis, TAPTag)");
for($i=0; $i<2; $i++) {
  for($j=0; $j<2; $j++) {
    for($k=0; $k<2; $k++) {
      for($l=0; $l<2; $l++) {
	
	$answer = $x[0][$i];
	$answer = array_intersect($answer, $x[1][$j]);
	$answer = array_intersect($answer, $x[2][$k]);
	$answer = array_intersect($answer, $x[3][$l]);
	
	$t += count($answer);
	printWB($i.$j.$k.$l."->".count($answer));
	
      }
    }
  }
}

printWB("total".$t);

for($i=0; $i<2; $i++) {
  for($j=0; $j<2; $j++) {
    for($k=0; $k<2; $k++) {
      for($l=0; $l<2; $l++) {
	
	$answer = $x[0][$i];
	$answer = array_intersect($answer, $x[1][$j]);
	$answer = array_intersect($answer, $x[2][$k]);
	$answer = array_intersect($answer, $x[3][$l]);

	printWB($i.$j.$k.$l."->");
	foreach($answer as $orfid) {
	  $orfnumber = getOneToOneMatch("orfs", "orfid", $orfid, "orfnumber");
	  print($orfnumber.",");
	}
	printWB("");
	
      }
    }
  }
}







?>

