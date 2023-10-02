<?
require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/secure.php");
require("$include_dir/projects_inc.php");
require("$include_dir/javascript.php");

pass($priv['Superuser']);


$orfList = getAllOrfs();
$subcellList = getOrderedSubcellList();

$phaseList = array();
$phaseList[] = "F";
$phaseList[] = "C";
$phaseList[] = "I";

dbquery("delete from todo");

/////  SETUP THE TODO LIST
foreach($orfList as $orfid) {

  $locList = orfidToLocMap($orfid);  // most_current and not superceded
  foreach($locList as $locid) {
    $subcellid = getOneToOneMatch("localization", "localizeid", $locid, "subcellid");


    // THESE ARE NEVER DISPLAYED, SO WE DON'T NEED TO SELECT THE BEST EXPOSURE FOR THEM
    if(isLocColoc($locid) && $subcellid==3) {

      continue;

    } else {

      if(isLocMostFinal($locid)) {  
	printWB("final phase orfid=".$orfid." and localizeid=".$locid." and subcellid=".$subcellid);
	$sql = "select * from todo where orfid=".$orfid." and subcellid=".$subcellid." and phase='F'";
	$res = dbquery($sql);
	if(mysqli_num_rows($res) == 0) {
	  $sql2 = "insert into todo (orfid, subcellid, phase) values (".$orfid.",".$subcellid.",'F')";
	  //      printWB($sql2);
	  dbquery($sql2);
	}
      }
      printWB("nonfinal");
      
      // now do the coloc/init descrimination 
      if(isLocColoc($locid)) {
	$sql = "select * from todo where orfid=".$orfid." and subcellid=".$subcellid." and phase='C'";
	$res = dbquery($sql);
	if(mysqli_num_rows($res) == 0) {
	  $sql2 = "insert into todo (orfid, subcellid, phase) values (".$orfid.",".$subcellid.",'C')";
	  //	printWB($sql2);
	  dbquery($sql2);
	}
      } else {
	$sql = "select * from todo where orfid=".$orfid." and subcellid=".$subcellid." and phase='I'";
	$res = dbquery($sql);
	if(mysqli_num_rows($res) == 0) {
	  
	  $sql2 = "insert into todo (orfid, subcellid, phase) values (".$orfid.",".$subcellid.",'I')";
	  //	printWB($sql2);
	  dbquery($sql2);
	}
      }
    }
  }
}
printWB("complete!");


?>
