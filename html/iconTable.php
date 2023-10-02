<?php

require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/projects_inc.php");
?>

<html>
<head>
<title>Subcellular Localizations and Icons</title>
<link rel="stylesheet" href="imagedb.css">
</head>
<body>


<?php
// build up a table of the subcell icons and names
// make it three wide

$width=3;


$sqlIcon = "SELECT * FROM subcell ORDER BY subcellname";
$resIcon = dbquery($sqlIcon);

$tableStr = "<table border=1 cellspacing=0 cellpadding=5><tr>";

$i=0;
while ($row = mysqli_fetch_assoc($resIcon)) {
  if ($i == $width) {
    $i = 0;
    $tableStr .= "</tr><tr>";
  }

  $tableCell = "<td><img src=\"orfIcons/".$row['icon']."\"></td><td width=100>".$row['subcellname']."</td>\n";
  $tableStr .= $tableCell;
  $i++;

}

if ($i < $width) {
  $colSpan = ($width - $i) * 2;
  $tableStr .= "<td colspan=".$colSpan.">&nbsp;</td>\n";
}

$tableStr .= "</tr></table>";
print $tableStr;

?>

</body>
</html>
