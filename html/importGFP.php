<?php
require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/secure.php");
require("$include_dir/projects_inc.php");


$sql = "CREATE TABLE gfpvisible (
          gfpvisibleid mediumint(9) NOT NULL auto_increment,
          strainid mediumint(9) NOT NULL,
          gfpvisible enum('T','F') default 'F',
          PRIMARY KEY (gfpvisibleid),
          KEY strainid (strainid)
        ) TYPE=InnoDB";
dbquery($sql);
	
	
	
	
  $fd = fopen ("imports/GFPScore.txt", "r");
while (!feof ($fd)) {
  $buffer = fgets($fd, 4096);
  if(preg_match("/^Y/", $buffer, $match)) {
    $resultArray = explode(",", $buffer);
    //    printWB($resultArray[0].$resultArray[4].$resultArray[6]);
    assert($resultArray[4] !== NULL);
    assert($resultArray[6] !== NULL);
    
    if($resultArray[4] == 1) {
      $aLibTaggedSuccess = "T";
      //      printWB("gotcha");
    } else {
      $aLibTaggedSuccess = "F";
    }
    
    if($resultArray[6] == 1) {
      //      printWB("gotcha2");
      $bLibTaggedSuccess = "T";
    } else {
      $bLibTaggedSuccess = "F";
    }
    //    print_r(array_values($resultArray));
    $sqlGetStrains = "SELECT * FROM strains INNER JOIN orfs ON orfs.orfid = strains.orfid
                    WHERE orfs.orfnumber='".$resultArray[0]."' AND tag='GFP'";
    $res = dbquery($sqlGetStrains);
    while ($row = mysqli_fetch_assoc($res)) {
    
      if($row['library'] == "A") {
	$sqlInsert = "UPDATE strains SET tag_success='".$aLibTaggedSuccess."' WHERE strainid=".$row['strainid'];
	//	printWB($sqlInsert);
	dbquery($sqlInsert);
      }
      if($row['library'] == "B") {
	$sqlInsert = "UPDATE strains SET tag_success='".$bLibTaggedSuccess."' WHERE strainid=".$row['strainid'];
	//	printWB($sqlInsert);
	dbquery($sqlInsert);
      }
      
    }
  }
}

fclose ($fd);

$orfArray = getAllOrfs();
foreach ($orfArray as $orfid) {

  
  $sqlCheck = "SELECT * FROM strains WHERE orfid = ".$orfid." AND tag='GFP'";
  $resCheck = dbquery($sqlCheck);
  assert(mysqli_num_rows($resCheck) == 2);
  while($rowCheck = mysqli_fetch_assoc($resCheck)) {

    $strainHasGFP = "F";

    $sqlGetSets = "SELECT * FROM sets WHERE strainid=".$rowCheck['strainid'];
    $resGetSets = dbquery($sqlGetSets);
    while($rowGetSets = mysqli_fetch_assoc($resGetSets)) {
      if($rowGetSets['no_gfp_visible'] == "F") {
	$strainHasGFP = "T";
      }	
    }	
    
    $sqlInsert = "INSERT INTO gfpvisible (strainid, gfpvisible) VALUES (".$rowCheck['strainid'].", '".$strainHasGFP."')";
    printWB($sqlInsert);

dbquery($sqlInsert);

  }
  
  
}
?>
