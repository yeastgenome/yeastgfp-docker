<?
require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/secure.php");
require("$include_dir/projects_inc.php");

pass($priv['Owner']);

if (!$_SESSION['projectid']) {
	$_SESSION['goback'] = $_SERVER['PHP_SELF'];
	closedb();
	header("Location: projectchoose.php");
	exit;
}

if ($_POST['btnAdd'] AND !empty($_POST['addusers'])) {
	foreach ($_POST['addusers'] as $uid) {
		$sql = "INSERT INTO usersxprojects (projectid, userid, rights)
		VALUES (".$_SESSION['projectid'] .", $uid, ".$_POST["rights"].")";
		dbquery($sql);
	}
}

if ($_POST['btnRemove'] AND !empty($_POST['remusers'])) {
	foreach ($_POST['remusers'] as $uid) {
		if ($uid <> $_SESSION["userid"]) {
			$sql = "DELETE FROM usersxprojects
			WHERE projectid=" .$_SESSION['projectid']." AND userid=$uid";
			dbquery($sql);
		}
	}
}

/** Create temporary table to hold project users **/
$maketable = "CREATE TEMPORARY TABLE uxp (userid MEDIUMINT,	projectid MEDIUMINT, rights TINYINT UNSIGNED, KEY(userid), KEY(projectid));";
$populate = "INSERT INTO uxp SELECT * FROM usersxprojects
	WHERE usersxprojects.projectid = " .$_SESSION['projectid'];
dbquery($maketable);
dbquery($populate);

$sqlbase = "SELECT users.userid, users.realname, uxp.rights ";
$sqlinproject = "FROM users, uxp
	WHERE users.userid = uxp.userid ORDER BY uxp.rights DESC, users.realname";
$sqloutproject = "FROM uxp RIGHT JOIN users ON uxp.userid = users.userid
	WHERE uxp.userid IS NULL ORDER BY users.realname";
	
$rsproj = dbquery($sqlbase . $sqlinproject);
$rsarch = dbquery($sqlbase . $sqloutproject);

//printheader();
print "<form method=post action='".$_SERVER['PHP_SELF']."'>
	<table width=100%>
	<tr><th>Users</th>
	<th></th>
	<th>Users In Project</th></tr>
	<tr>";

listfiles ("addusers[]", $rsarch, 30);

print "</td><td valign=center align=center>
	<input type=submit name=btnAdd value='Add &#187;'> <b>AS</b> <SELECT name='rights'>\n";
foreach ($priv as $key => $value) {
	if ($key <> "Owner") { print "<OPTION value=$value>$key\n"; }
	}
print "</SELECT><p>
	<input type=submit name=btnRemove value='&#171; Remove'><hr><p>
	<input type=reset value='Clear All'></td>";

listfiles ("remusers[]", $rsproj, 30);

print "</td></tr></table>";
printfooter();

function listfiles ($name, $rs, $size) {
	global $priv;
	print "<td width=33% align=center>";
	if (!mysqli_num_rows($rs)) {
		print "<h3>None</h3>"; }
	else {
		print "<SELECT name='$name' multiple size=$size>\n";
		while ($row = mysqli_fetch_assoc($rs)) {
			if ($row['rights']) { $rights = "(".array_search($row['rights'], $priv).")"; }
				else { $rights = ""; }
			print "<OPTION value=".$row['userid'].">".$row['realname']." $rights\n"; }
		print "</SELECT>"; }
}
?>
