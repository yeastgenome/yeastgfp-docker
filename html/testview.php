<!-- THE BROWSER DOESN'T SEEM TO LIKE IT WHEN PICTURES BY THE SAME NAME
ARE ACTUALLY CHANGING IN THEIR CONTENT.   THIS NO-CACHE BIT SEEMS TO FIX
THINGS UP, BUT WE SHOULD REALLY HAVE A PHP ROUTINE THAT DUMPS APPROPRIATE
HEADERS AND TAKES NO-CACHE AS AN ARG -->
<html>
<head>
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="expires" content="0">
</head>


<?
require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/secure.php");
require("$include_dir/projects_inc.php");

// ENTER SQL STATEMENT HERE:
// $sql = "SELECT * FROM images, projectsximages
//	WHERE images.imageid = projectsximages.imageid
//		AND	projectsximages.projectid =".$_SESSION['projectid'];	
// 

//$sql = "SELECT *
//	   FROM plateinfo
//	   INNER JOIN strains ON plateinfo.strainid = strains.orfid
//	   INNER JOIN orfs ON strains.orfid = orfs.orfid
//	   WHERE orfs.orfname = 'bni5'
//	   AND library = 'a'";

/* $sql = "SELECT localizeid, xcoord, ycoord, dirpath FROM localization
        INNER JOIN sets ON sets.setid = localization.setid
        INNER JOIN images ON images.setid = sets.setid
        WHERE stainid = 3
        ";
*/

  $sql = "SELECT orfnumber, orfname FROM orfs
          INNER JOIN strains ON strains.orfid = orfs.orfid
          INNER JOIN sets ON sets.strainid = strains.strainid
          WHERE setid = 3";

/*    $sql = "SELECT xcoord, ycoord, dirpath,stainid FROM localization
        INNER JOIN sets ON sets.setid = localization.setid
        INNER JOIN images ON images.setid = sets.setid
        INNER JOIN stain ON stain.stainid = images.stainid";
*/  

$res = dbquery($sql);
$numfields = mysqli_num_fields($res);
// printheader();

print $sql."<br>\n";

print "USERID : ".$_SESSION['userid']."<p>\n";


?>
<table border width=100%>
<?
print "<tr>";
for ($i=0; $i < $numfields; $i++) {
  print "<th>" . mysqli_field_name($res,$i) . "</th>";
}
print "</tr>\n";

while ($row = mysqli_fetch_row($res)) {
  print "<tr>";
  for ($j=0; $j < $numfields; $j++) {
    print "<td align=center>" . $row[$j] . "</td>"; 
  }
  print "</tr>\n";
}

print "<tr><td>&nbsp;</td></tr>";
/*

$res = dbquery($sql);

while ($row = mysqli_fetch_assoc($res)) {
  print "<tr>";
  print "<td align=center>".$row['localizeid']."</td>";
  print "<td align=center>".$row['xcoord']."</td>";
  print "<td align=center>".$row['ycoord']."</td>";
  print "<td align=center>".$row['dirpath']."</td>";
  insertCellImageForTable($row['localizeid']);
  print "</tr>\n";
}
*/
print "</table>";


printfooter();



?>
