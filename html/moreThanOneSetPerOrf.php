<html>
<head>
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="expires" content="0">
</head>

<?
require("locInclude.php");
require("$include_dir/include.php");
//require("$include_dir/secure.php");
//require("$include_dir/projects_inc.php");


$orfList = getAllOrfs();
foreach($orfList as $orf) {
  $setList = array();
  $locList = orfidToLocMap($orf);
  foreach($locList as $loc) {
    $set = getOneToOneMatch("localization", "localizeid", $loc, "setid");
    $setList[] = $set;
  }
  $setList = array_unique($setList);
  if(count($setList)>1) {
    printWB(getOneToOneMatch("orfs", "orfid",$orf, "orfnumber"));
    //    print_r($setList);
  }
}

/*
$orf = 1583;
  $setList = array();
  $locList = orfidToLocMap($orf);
  foreach($locList as $loc) {
    ph();
    $set = getOneToOneMatch("localization", "localizeid", $loc, "setid");
    $setList[] = $set;
  }
  $setList = array_unique($setList);
  if(count($setList)>1) {
    printWB(getOneToOneMatch("orfs", "orfid",$orf, "orfnumber"));
    //    print_r($setList);
  }
*/


?>
