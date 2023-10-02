<?php

require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/secure.php");
require("$include_dir/projects_inc.php");

?>


<html>
<head>
	<link rel="stylesheet" href="imagedb.css">
</head>

<body>

<?

$outFile = "infoTableOut.txt";

print "<p>Getting ORF, Gene, Localization, and Size";
print "<p>working...";

$sql = "SELECT orfs.size, subcell.subcellname, orfs.orfnumber, orfs.orfname
        FROM localization 
        INNER JOIN sets ON sets.setid = localization.setid
        INNER JOIN strains ON sets.strainid = strains.strainid
        INNER JOIN orfs ON strains.orfid = orfs.orfid
        INNER JOIN subcell ON localization.subcellid = subcell.subcellid
        WHERE most_current = 'T'
        ORDER BY orfs.size";

$res = dbquery($sql);

if (!$fp = fopen($outFile, 'w')) {
  print "fukacta file";
  exit;
}

while ($row = mysqli_fetch_assoc($res)) {

  $writeStr = "";
  $writeStr .= $row['orfnumber'];
  $writeStr .= "\t";
  $writeStr .= $row['orfname'];
  $writeStr .= "\t";
  $writeStr .= $row['subcellname'];
  $writeStr .= "\t";
  $writeStr .= $row['size'];
  $writeStr .= "\n";

  if (!fwrite($fp,$writeStr)) {
    print "failed to write";
    exit;
  }

}

fclose($fp);

print "<p>download your information ";
print "<a href=\"".$outFile."\">here</a>";

?>

</body>
</html>
