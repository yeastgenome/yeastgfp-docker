<?php
require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/secure.php");


// Check for sysadmin privileges
if ($_SESSION['userid'] <> $sysadminid) {
	centermsg("Sorry.  Insufficient access privileges.");	
	exit;
}

if ($_POST['btnAdd']) {
	header("Location: usersproc.php");
	exit;
}

elseif ($_POST['btnDel'] AND $_POST['userid']) {
	$_SESSION['deleteuser'] = $_POST['userid'];
	header("Location: usersproc.php");
	exit;
}

elseif ($_POST['btnAct'] AND $_POST['userid']) {
	$_SESSION['activateuser'] = $_POST['userid'];
	header("Location: usersproc.php");
	exit;
}

elseif ($_POST['btnEdit'] AND $_POST['userid']) {
	$_SESSION['edituser'] = $_POST['userid'];
	header("Location: usersproc.php");
	exit;
}

elseif ($_POST['btnProj'] AND $_POST['userid']) {
	$_SESSION['projuser'] = $_POST['userid'];
	header("Location: usersprojects.php");
	exit;
}

// ENTER SQL HERE:
$sql = "SELECT userid, realname, login, active, email 
	FROM users ORDER BY realname";
$res = dbquery($sql);

//printheader();
print "<form method=post action='".$_SERVER['PHP_SELF']."'>\n
	<center>
	<input type=submit name=btnAdd value='Add User'><hr width=75%><p><p>
	<table border width=100%><tr>
	<th></th>
	<th>Login</th>
	<th>Real Name</th>
	<th>E-mail</th>
	<th>Active</th>\n";

while ($row = mysqli_fetch_assoc($res)) {
	print "<tr><td align=center><input type=radio name=userid value=".$row["userid"]."></td>
		<td align=center>".$row["login"]."</td>
		<td>&nbsp;".$row["realname"]."</td>
		<td>&nbsp;<a href=\"mailto:".$row["email"]."\">".$row["email"]."</a></td>";
	if ($row["active"] == 1) { print "<td align=center>&#149;</td></tr>\n"; }
		else { print "<td></td></tr>\n"; }
}

print "</table><p><center>
	<input type=submit name=btnEdit value='Edit'>
	<input type=submit name=btnAct value='Activate/Inactivate'>
	<input type=submit name=btnDel value='Delete'>
	<input type=submit name=btnProj value='Projects'>";

printfooter();
?>
