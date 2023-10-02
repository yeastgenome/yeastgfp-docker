<?
require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/secure.php");
require("$include_dir/projects_inc.php");

pass($priv['Superuser']);


$listEncounteredPlates = "";

/* BUILD UP A LIST OF PRESENT PLATES */
$sqlGetPlateInfo = "SELECT * FROM plateinfo";
$qryGetPlateInfo = dbquery($sqlGetPlateInfo);
while($plateInfo = mysqli_fetch_assoc($qryGetPlateInfo)) {
  if(in_array($plateInfo['platenumber'], $listEncounteredPlates)) {
    continue;
  }
  else {
    $listEncounteredPlates[] = $plateInfo['platenumber'];
  }
}

/* BUILD THE OPTIONS STRING */
$selectOptionsString= "";

/* MAKE THE OPTIONS IN A LOGICAL ORDER */
sort($listEncounteredPlates);
foreach($listEncounteredPlates as $currentPlate) {
  $selectOptionsString .= "<option value=\"".$currentPlate."\">Plate".$currentPlate.
                          "</option>\n";
}


?>
<link href="prune.css" rel="stylesheet" type="text/css">
<p class="titleText">   
<!--
<form name="pruneSpecificPlateForm" method="post" action="pruneLibraries.php">
<select name="specificPlateSelect">
<?=$selectOptionsString?>

<input type="submit" name="buttonSpecificPlateSubmit" value="Prune This Plate!!!">
</select>
</form>
-->
<form name="pruneSkippedOrfs" method="post" action="pruneLibraries.php">
<input type="submit" name="buttonSkippedOrfsSubmit" value="Prune Skipped Orfs!!!">
</form>

<form name="pruneUnprunedOrfs" method="post" action="pruneLibraries.php">
<input type="submit" name="buttonUnprunedOrfsSubmit" value="Prune Next Available Orf!!!">
</form>

