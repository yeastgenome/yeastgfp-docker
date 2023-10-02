<?php
require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/secure.php");

// Called from listimages.php both GET and POST

$selectcount = "SELECT sets.setid, count(*) ";
$selectfields = "SELECT images.dirpath, images.descript, stain.stainname, condition.conditionname, 
	sets.comments, sets.complete, strains.strainname, orfs.orfname, orfs.orfnumber, users.realname, 
	sets.setid ";
$sqlmid = "FROM images, stain, sets, condition, strains, orfs, users
	WHERE images.setid = sets.setid 
		AND images.ownerid = users.userid
		AND sets.strainid = strains.strainid
		AND strains.orfid = orfs.orfid
		AND images.stainid = stain.stainid
		AND sets.conditionid = condition.conditionid ";


// Check for POST checkboxes
if (!empty($_POST)) {
		/** Filter $_POST for numeric values, which we assume to be imageids **/
	$ids = array_filter ($_POST, "is_numeric");
	$cond = "AND (images.imageid=" . join (" OR images.imageid=", $ids);
	$sql = $selectfields . $sqlmid . $cond .") ORDER BY images.setid";
}


// Check for GET setid
if (empty($_GET) AND empty($_POST)) {
	centermsg("No options provided.");
	exit; }
elseif ($_GET['setid']) {
	$cond = "AND (sets.setid=" . $_GET['setid'];
	$sql = $selectfields . $sqlmid . $cond .") ORDER BY images.stainid"; }


// Do count query and build array with # images/set
$sqlcount = $selectcount . $sqlmid . $cond .") GROUP BY images.setid";
$rscount = dbquery($sqlcount);
while ($row = mysqli_fetch_assoc($rscount)) {
	$span[$row["setid"]] = $row["count(*)"];
}
//	print $sqlcount ."<p>".$sql."<p>";
//foreach ($span as $key => $value) { print "$key : $value<br>"; }
//exit;

// Do query and SET-level formatting
$rs = dbquery($sql);
//printheader();

// Strains
print "<table border width=100%><tr><td align=right><font size=1><b>Strain</b></td>";
mysqli_data_seek ($rs, 0);
$setid = 0;
while ($row = mysqli_fetch_assoc($rs)) {
	if ($setid <> $row["setid"]) {
		print "<td align=center colspan=".$span[$row["setid"]].">" .$row["strainname"] ." (" .$row["orfnumber"] ." / " .$row["orfname"] .")</td>"; }
	$setid = $row["setid"]; }
	
// Conditions
print "</tr><tr><td align=right><font size=1><b>Condition</b></td>";
mysqli_data_seek ($rs, 0);
$setid = 0;
while ($row = mysqli_fetch_assoc($rs)) {
	if ($setid <> $row["setid"]) {
		print "<td align=center colspan=".$span[$row["setid"]].">" .$row["conditionname"]."</td>"; }
	$setid = $row["setid"]; }

// Set comments
print "</tr><tr><td align=right><font size=1><b>Set Comments</b></td>";
mysqli_data_seek ($rs, 0);
$setid = 0;
while ($row = mysqli_fetch_assoc($rs)) {
	if ($setid <> $row["setid"]) {
		print "<td align=center colspan=".$span[$row["setid"]].">" .$row["comments"]."</td>"; }
	$setid = $row["setid"]; }
		
// Scored?
print "</tr><tr><td align=right><font size=1><b>Scored?</b></td>";
mysqli_data_seek ($rs, 0);
$setid = 0;
while ($row = mysqli_fetch_assoc($rs)) {
	if ($setid <> $row["setid"]) {
		if ($row["complete"] == 1) { $complete = "Yes"; }
		else { $complete = "No"; }
		print "<td align=center colspan=".$span[$row["setid"]].">" .$complete."</td>"; }
	$setid = $row["setid"]; }


// IMAGE-level formatting
print "</tr><tr><td></td>";
$filebase = basename ($image_dir)."/";
mysqli_data_seek ($rs, 0);
while ($row = mysqli_fetch_assoc($rs)) {
	$ext = substr(basename($row["dirpath"]), strrpos(basename($row["dirpath"]),"."));
	$file = $filebase . str_replace($ext, $viewext, $row["dirpath"]);
//	preg_match("/^.*\/(.*\/.*\/)(.*)(\..+)$/i", $row['dirpath'], $matchfile);
//	$file = $matchfile[1] .$matchfile[2]. $viewext;
	print "<td align=center><img src='$file'></td>"; }

/** Stain **/
print "</tr>\n<tr><td align=right><font size=1><b>Stain</b></td>";
mysqli_data_seek ($rs, 0);
while ($row = mysqli_fetch_assoc($rs)) {
	print "<td align=center>". $row["stainname"]."</td>"; }

/** Filename **/
print "</tr>\n<tr><td align=right><font size=1><b>File</b></td>";
mysqli_data_seek ($rs, 0);
while ($row = mysqli_fetch_assoc($rs)) {
	print "<td align=center>". basename($row["dirpath"])."</td>"; }

/** Notes **/
print "</tr>\n<tr><td align=right><font size=1><b>Notes</b></td>";
mysqli_data_seek ($rs, 0);
while ($row = mysqli_fetch_assoc($rs)) {
	print "<td align=center>". $row["descript"]."</td>"; }

/** Uploaded by **/
print "</tr>\n<tr><td align=right><font size=1><b>Uploaded By</b></td>";
mysqli_data_seek ($rs, 0);
while ($row = mysqli_fetch_assoc($rs)) {
	print "<td align=center>". $row["realname"]."</td>"; }
print "</tr></table>";


printfooter();
?>
