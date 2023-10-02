<?
require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/secure.php");
require("$include_dir/projects_inc.php");

$completeOrfArray = getAllOrfs();
foreach($completeOrfArray as $orfid) {
  $sql = "select * from strains where orfid=".$orfid." AND tag='GFP'";
  $res = dbquery($sql);
  assert(mysqli_num_rows($res) == 2);


  $sql = "select * from orfs
          inner join strains on strains.orfid=orfs.orfid
          inner join gfpvisible on gfpvisible.strainid=strains.strainid
          where orfs.orfid=".$orfid." AND tag='GFP'";
  $res = dbquery($sql);
  



  
  $gfpSignalExists = false;
  while($row = mysqli_fetch_assoc($res)) {
    if($row['gfpvisible']) {
      $gfpSignalExists = true;
    }
  }

  if($gfpSignalExists == true) {
    // printWB("T:".$orfid);
  } else {
    printWB("F:".$orfid);
  }
  //  $sql = "select * from dotblot


  
}

?>

