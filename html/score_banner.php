<?php
require("locInclude.php");
require("$include_dir/include.php");
?>
<html>
<head>
	  <link rel="stylesheet" href="imagedb.css">
          <meta name="robots" content="noindex,nofollow">
</head>

<body>

<table bgcolor="#006600" width=100% cellspacing=0 cellpadding=0>
	<tr><td height=30>&gt;&gt; YEAST GFP FUSION LOCALIZATION DATABASE
<?

print "<td align=right><h3><b>Login:&nbsp;&nbsp;</b></td>
	<td align=left><h4><b>".$_SESSION['realname']."</b></td>";
if (isset($_SESSION['project'])) {
	print "<td align=right><h3><b>Project:&nbsp;&nbsp;</b></td>
	<td align=left><h4><b>".$_SESSION['project']."</b></td>";
	$sql = "SELECT rights FROM usersxprojects
		WHERE userid =".$_SESSION['userid'] ." AND projectid =".$_SESSION['projectid'];
	$row = mysqli_fetch_assoc(dbquery($sql));
	print "<td align=right><h3><b>Privileges:&nbsp;&nbsp;</b></td>
	<td align=left><h4><b>" . array_search($row['rights'], $priv)."</b></td>";
}
else { print "<td><h3>&nbsp;</td><td><h3>&nbsp;</td>
	<td><h3>&nbsp;</td><td><h3>&nbsp;</td>"; }
print "<td><a href=\"help.php\" target=\"scoring\"><h4><b><i>&gt;&gt;&nbsp;help</b></i></a></td>";	
print "</tr></table>";

printfooter();

?>
