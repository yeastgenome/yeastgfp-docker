<?
require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/secure.php");

function oktodelete($uid) {		/** Check tables for any ownership by user **/
	$tables = array("projects", "localization", "images");
	foreach($tables as $table) {
		$sql = "SELECT * FROM $table WHERE userid=$uid";
		$res = dbquery($sql);
		if (dbnumrows($res)) {
			return false;
			break; }
	}
	return true;
}

// Check for sysadmin privileges
checksysadmin();

// Check for user delete
if ($_SESSION['deleteuser'] > 0) {
	if ($_SESSION['deleteuser'] == $sysadminid) {
		$_SESSION['deleteuser'] = "";
		centermsg("Deleting the sysadmin is not allowed.");	
		exit; }
	if (oktodelete($_SESSION['deleteuser'])) {	/** Delete user from users, usersxprojects **/
		$sqldel = "DELETE FROM users WHERE userid=".$_SESSION['deleteuser'];
		$sqldel2 = "DELETE FROM usersxprojects WHERE userid=".$_SESSION['deleteuser'];
		$_SESSION['deleteuser'] = "";
		dbquery($sqldel);
		dbquery($sqldel2);
		closedb();
		header("Location: users.php");
		exit; }
	else {								/** If user owns something, inactivate **/
		$sqlchange = "UPDATE users SET active=0 WHERE userid=".$_SESSION['deleteuser'];
		$_SESSION['deleteuser'] = "";
		dbquery($sqlchange);
		centermsg("User owns an Image, Localization entry, or Project.
			<p>Has been inactivated instead.");
		exit;}
}

// Check for user activate/inactivate
if ($_SESSION['activateuser'] > 0) {
	$sqlact = "SELECT active FROM users WHERE userid=".$_SESSION['activateuser'];
	$row = mysqli_fetch_assoc(dbquery($sqlact));
	if ($row["active"]) { $changeto = 0; }
		else { $changeto = 1; }
	$sqlchange = "UPDATE users SET active=$changeto WHERE userid=".$_SESSION['activateuser'];
	$_SESSION['activateuser'] = "";
	dbquery($sqlchange);
	closedb();
	header("Location: users.php");
	exit;
}

// Check for user edit
if ($_SESSION['edituser'] > 0) {
	$sqledit = "SELECT * FROM users WHERE userid=".$_SESSION['edituser'];
	// Do not clear $_SESSION['edituser'] until end of script
}

// Check for user (new or edit) info
if ($_POST['btnAddUser']) {
	foreach ($_POST as $key => $value) {
		if ($value == "") {
			centermsg("Information is missing.  Go back and reenter.");	
			exit; }
	}
	if ($_POST['passwd'] <> $_POST['passwd2']) {
			centermsg("Passwords do not match.  Go back and reenter.");	
			exit; }
	if ($_POST['edituser'])	{ $sqladd = "UPDATE users SET login='".$_POST['login']."', 
		passwd='".$_POST['passwd']."', realname='".$_POST['realname']."', 
		email='".$_POST['email']."' WHERE userid=".$_POST['edituser'].";"; }
	else { $sqladd = "INSERT INTO users (login, passwd, realname, email) 
		VALUES ('".$_POST['login']."', '".$_POST['passwd']."', 
		'".$_POST['realname']."', '".$_POST['email']."')"; }
	dbquery($sqladd);
	closedb();
	header("Location: users.php");
	exit; 
}

//printheader();
if ($sqledit) {
//	$res = dbquery($sqledit);
	$row = mysqli_fetch_assoc(dbquery($sqledit));
}
print "<form method=post action='".$_SERVER['PHP_SELF']."'>\n";
//print "<form method=post action=postvars.php>\n";

print "<table width=100%>
	<tr><td colspan=2 align=center><font color=red>All fields required</td></tr>
	<tr><td width=33% align=right><b>Full Name:</b></td>
	<td><input type=text NAME='realname' ";
if ($sqledit) { print "VALUE=\"".$row["realname"]."\" "; }
print "SIZE=30 MAXLENGTH=60> (Jane Smith)</td></tr>
	<tr><td width=33% align=right><b>E-mail:</b></td>
	<td><input type=text NAME='email' ";
if ($sqledit) { print "VALUE=\"".$row["email"]."\" "; }
print "SIZE=30 MAXLENGTH=60> (jsmith@itsa.ucsf.edu)</td></tr>
	<tr><td width=33% align=right><b>Database Login:</b></td>
	<td><input type=text NAME='login' ";
if ($sqledit) { print "VALUE=\"".$row["login"]."\" "; }
print "SIZE=30 MAXLENGTH=60> (jsmith)</TD></tr>
	<tr><td width=33% align=right><b>Password:</b></td>
	<td><input type=password NAME='passwd' SIZE=30 MAXLENGTH=60></td></tr>
	<tr><td width=33% align=right><b>Confirm Password:</b></td>
	<td><input type=password NAME='passwd2' SIZE=30 MAXLENGTH=60></td></tr>
	</table><p>\n";
if ($sqledit) {
	print "<input type=hidden name=edituser value='".$_SESSION['edituser']."'>";
	$_SESSION['edituser'] = "";
}
print "<center><input type=submit name=btnAddUser value=Submit>";
printfooter();
?>
