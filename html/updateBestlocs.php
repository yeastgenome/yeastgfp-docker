<html>
<head>
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<link rel="stylesheet" href="imagedb.css">
</head>

<?


require("locInclude.php");
require("$include_dir/include.php");
//require("$include_dir/secure.php");
require("$include_dir/projects_inc.php");

$sql = "SELECT * FROM bestlocs WHERE subcellid=27 ORDER BY localizeid";
$res = dbquery($sql);

while ($row = mysqli_fetch_assoc($res)) {

  $gfpMogLevel = $row['gfpmogrifylevel'];
  $localizeId = $row['localizeid'];
  $derivedFromLocid = getOneToOneMatch("localization","localizeid",$localizeId,"derived_from_locid");

  if ($gfpMogLevel == 65535) {
    $newName = $derivedFromLocid."_clipPlus0.png";
  } elseif ($gfpMogLevel == 41400) {
    $newName = $derivedFromLocid."_clipPlus1.png";
  } else {
    $newName = $derivedFromLocid."_clipPlus2.png";
  }
    
  $sqlUpdate = "UPDATE bestlocs SET clipfilename='".$newName."' WHERE localizeid=".$localizeId;
  dbquery($sqlUpdate);
  print $sqlUpdate."<br>";


}

exit;



?>
