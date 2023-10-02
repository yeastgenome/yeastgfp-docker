<?php
require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/secure.php");

$msg = "";
$uid = $_SESSION['userid'];

// HACK IN THE GFP PROJECTID
$_SESSION['projectid'] = 1;
print "<script> parent.frames.scoring.location=\"splash.php\" </script>";




if (isset($_POST['btnChoose']) AND isset($_POST['projid'])) {
//	if (!$_POST['projid']) {
//		centermsg("Project not selected.  Go back and select.");	
//		exit; }
	$_SESSION['projectid'] = $_POST['projid'];
	$sqlproj = "SELECT name FROM projects WHERE projectid=".$_POST['projid'];
	$resproj = dbquery($sqlproj);
	$row = mysqli_fetch_assoc($resproj);
	$_SESSION['project'] = $row["name"];
	/** Force refresh of score banner, menu **/
	print "<script> parent.frames.menu.location=\"menu.php\" </script>";
	if ($_SESSION['goback'] == "" OR strpos($_SESSION['goback'],"index")) {
		centermsg("Select a menu option from the left.");
		exit; }
	else {
		closedb();
		$goto = $_SESSION['goback'];
		$_SESSION['goback'] = "";
		print "<script> parent.frames.scoring.location=\"$goto\" </script>";
//		header("Location: " .$goto);
		exit; }
//	else {
//		centermsg("Select a menu option from the left.");
//		exit; }
}

// Create new project, create upload subdir for project
if (isset($_POST['btnNew'])) {
	if (!$_POST['name'] OR !$_POST['descript'] OR !$_POST['userid']) {
		centermsg("Information is missing.  Go back and reenter.");	
		exit; }
	else { $sqladd = "INSERT INTO projects (name, descript, userid) 
		VALUES ('".$_POST['name']."', '".$_POST['descript']."', ".$_POST['userid'].")";
	}
	dbquery($sqladd);
		/** Get newly created projectid for creating new uploads directory **/
	$sqlid = "SELECT projectid FROM projects 
	WHERE name = '" .$_POST['name'] ."' AND userid = " .$_POST['userid'] ." 
	ORDER BY projectid DESC";
	$row = mysqli_fetch_assoc(dbquery($sqlid));
	$projid = $row['projectid'];
	if (mkdir($upload_base .$projid, 0744)) {
		$msg = "Project <b><font color=red>".$_POST['name']."</b></font> created!
		</br>Uploads folder created."; }
	else { $msg = "Project <b><font color=red>".$_POST['name']."</b></font> created!
		</br>ERROR!  Uploads folder was NOT created!"; }
		/** Add owner to usersxprojects table **/
	$sqladd = "INSERT INTO usersxprojects (userid, projectid, rights)
		VALUES (".$_POST['userid'] .", $projid, ".$priv["Owner"].")";
	dbquery($sqladd);
}

// ENTER SQL STATEMENT HERE:
if ($_SESSION['userid'] == $sysadminid) {
	$sql= "SELECT projects.projectid, projects.name 
	FROM projects 
	ORDER BY projects.name"; }
else { $sql = "SELECT projects.projectid, projects.name, usersxprojects.rights 
	FROM users, usersxprojects, projects 
	WHERE (users.userid = $uid 
		AND users.userid = usersxprojects.userid 
		AND projects.projectid = usersxprojects.projectid) 
	ORDER BY projects.name"; }

$res = dbquery($sql);

//printheader();
print "<link rel=\"stylesheet\" href=\"imagedb.css\">";
print "<form method=post action='".$_SERVER['PHP_SELF']."'>\n";
if ($msg) { print "<center>$msg</center>"; }

if ($_SESSION['userid'] == $sysadminid) {
	$sqlusers = "SELECT userid, realname FROM users ORDER BY realname";
	$resusers = dbquery($sqlusers);
?>
<center><h2>Create new project:</h2>
<table width=100%>
<tr><td width=33% align=right><b>Project Name:</b></td>
	<td><input type=text NAME='name' SIZE=50 MAXLENGTH=100></td></tr>
<tr><td align=right><b>Description:</b></td>
	<td><input type=text NAME='descript' SIZE=50 MAXLENGTH=255></td></tr>
<tr><td align=right><b>Owner:</b></td>
	<td><SELECT name=userid>
<?
	while ($row = mysqli_fetch_assoc($resusers)) {
		print "<OPTION value=".$row["userid"].">".$row["realname"]."\n";
	}
?>
</SELECT></td></tr>
</table><p>
<input type=submit value=Submit name=btnNew>
<p><p>
<hr width=85%>
<p><p>
<?
}
print "<center><h2>Select existing: <SELECT name=projid><OPTION>\n";
while ($row = mysqli_fetch_assoc($res)) {
	print "<OPTION value=".$row["projectid"].">".$row["name"]."\n";
}
?>

</SELECT></h2><p>
<input type=submit value=Choose name=btnChoose>

<?
printfooter();
?>
