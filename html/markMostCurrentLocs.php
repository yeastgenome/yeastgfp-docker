<?
require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/secure.php");
require("$include_dir/projects_inc.php");


$sql = "update localization set most_current='F'";
dbquery($sql);

$sql = "select setid from sets order by setid";
$res = dbquery($sql);

$setList = makeArrayFromResColumn($res,"setid");
foreach($setList as $set) {
  //  $set = 1776;
  $locList = getOneToMany("localization", "setid", $set, "localizeid");
  //  print_r($locList);
  $locList = filterLocListToMakeValidAllFromSameSet($locList);
  print ".";
  if($set % 100 == 0) {
    printWB($set);
  }
  //  print_r($locList);
  //  exit;
  $retList = array_merge($retList, $locList);
}

foreach($retList as $locid) {
  $sql = "update localization set most_current='T' where localizeid=$locid";
  dbquery($sql);

 
}

print_r($retList);

?>
