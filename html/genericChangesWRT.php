<?php
require("locInclude.php");
require("$include_dir/include.php");

$sql = "select * FROM orfs INNER JOIN strains ON strains.orfid=orfs.orfid INNER JOIN sets ON sets.strainid=strains.strainid INNER JOIN localization ON localization.setid=sets.setid";
$sql2 = $sql." LIMIT 1";
//$sql = "select * from localization";
$res = dbquery($sql2);
$row = mysqli_fetch_assoc($res);

$columnList = array();
$i = 0;
foreach($row as $key=>$val) {
  $columnList[$key] = $key;
}

print("<form name='beer' method=post>");
print(makeMultipleSelectFromAssocArray($columnList, "sameList[]"));
print(makeMultipleSelectFromAssocArray($columnList, "changesList[]"));
print(makeSelectFromAssocArray($columnList, "wrtCol"));
print("<input type=submit name=\"submit\">");
print("<form>");

if(isset($_POST['submit'])) {
  $sameColNames = $_POST['sameList'];
  $changesColNames = $_POST['changesList'];
  $WRTColName = $_POST['wrtCol'];

  $res = dbquery($sql);
  $WRTValsOfInterest = makeArrayFromResColumn($res, $WRTColName);
  $WRTValsOfInterest = array_unique($WRTValsOfInterest);
//print_r($sameColNames);
print_r($changesColNames);
//print_r($WRTColName);
//print_r($WRTValsOfInterest);
  $list = getChangesWRTList($sql, $sameColNames, $changesColNames, $WRTColName, $WRTValsOfInterest, "localizeid");
  print_r($list);

  $list = getLocChangesWRTUserid();
  print_r($list);

}





?>

