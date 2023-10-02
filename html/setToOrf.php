<?

/* a simple page for James to get Orf info from a setid */

require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/secure.php");

?>


<html>
<head>
        <link rel="stylesheet" href="imagedb.css">
</head>


<?

$orfInfoStr = "";

if (isset($_POST['btnGo'])) {
  $setInput = explode(" ",$_POST['setId']);
  
  foreach ($setInput as $setid) {
    if ($setid == NULL) {
      print "<p>please enter a value";
      continue;
    }
    $sql = "SELECT orfnumber, orfname FROM orfs
          INNER JOIN strains ON strains.orfid = orfs.orfid
          INNER JOIN sets ON sets.strainid = strains.strainid
          WHERE setid = ".$setid;
    $res = dbquery($sql);

    if (mysqli_num_rows($res) == 0) {
      print "<p>no such set: ".$setid;
    } else {
      while ($row = mysqli_fetch_assoc($res)) {
	$orfInfoStr .= "<p>the setid ".$setid." corresponds to ";
	$orfInfoStr .= $row['orfnumber'];    
	$orfInfoStr .= " -- ";
	$orfInfoStr .= $row['orfname'];
      }
    } 
  }
  print $orfInfoStr;
}

print "<p>Enter a setId";
print "<form method=post target=\"scoring\" action='".$_SERVER['PHP_SELF']."'>";
print "<input name=\"setId\" size=6>";
print "<input name=\"btnGo\" type=submit value=\"go\">";
print "</form>";

?>
