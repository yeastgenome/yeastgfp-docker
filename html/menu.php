<?php
require("locInclude.php");
require("$include_dir/include.php");
// require("$include_dir/secure.php");
?>
<html>
<head>
	<link rel="stylesheet" href="imagedb.css">
	<script src="interact.js"></script>
        <meta name="robots" content="index,follow">
</head>

<body bgcolor="#336633">

<table bgcolor="#669966" width=800 cellspacing=0 cellpadding=0 border=0>
<form name="search" method="post" target="scoring" action="search.php" valign=middle>
<tr>
 <td rowspan=4 align=center valign=middle bgcolor="#336633">&nbsp;&nbsp;</td>
 <td width=360 rowspan=4 class="title" align=left valign=middle bgcolor="#336633"><a href="splash.php" target="scoring"><img style="max-width:320px" src="gfp_logo.png" /></a></td>
 <td rowspan=4 align=center valign=middle bgcolor="#336633">&nbsp;</td>
 <td rowspan=4 align=center valign=middle bgcolor="#669966">&nbsp;&nbsp;</td>
 <td width=360 rowspan=2 class="menu">&gt;&gt; Advanced Query &nbsp;<a href="query.php" target="scoring" class="menuAll">&lt; Go &gt;</a>&nbsp;&nbsp;</td>
 <td rowspan=4 align=center valign=middle bgcolor="#336633">&nbsp;&nbsp;</td>

 <?php
 
 // print "<td class=\"login\" width=100 height=25  bgcolor=\"#336633\"><b>&nbsp;login: guest&nbsp;&nbsp;<b class=\"name\">"./*$_SESSION['realname'].*/"</b></td>";

?>
    <td height=25 width=100 bgcolor="#336633" valign=bottom><a href="info.php" target="scoring"><b class="menuLink">&nbsp;&gt;&gt;&nbsp; info</b></a></td>
 
 <td bgcolor="#336633">&nbsp;</td>
</tr>
<tr align=left valign=middle bgcolor="#336633">
 <td rowspan=2 width=100><a href="faq.php" target="scoring"><b class="menuLink">&nbsp;&gt;&gt;&nbsp; faq</b></a></td>
<td height=15></td>
</tr>
<tr valign=top>
    <td width=360 rowspan=2 class="menu">&gt;&gt; Quick Search &nbsp;&nbsp;&nbsp;
	<input type=text MAXLENGTH="200000" name="orf_number" size=12 value="yal001c">
        <input type=submit value="go">
        <input type=button name='clk' value='clear' onClick='window.document.search.orf_number.value=""'>
     </td>
    <td height=15 bgcolor="#336633"></td>
  </tr>
<tr valign=top bgcolor="#336633">
    <td height=25 width=100><a href="help.php" target="scoring"><b class="menuLink">&nbsp;&gt;&gt;&nbsp; help</b></td>
    <td>&nbsp;</td>
</tr>
</table>
</form>

</BODY></HTML>


<?php

    // THE CODE BELOW IS NOT CURRENTLY IN USE
    // ALLOWS PROJECT AND OWNERSHIP DETECTION
    
    /*
 print "<td ><h3><b>login:&nbsp;&nbsp;</b></td>
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
else { print "<td><h3>&nbsp;</td>
	<td><h3>&nbsp;</td><td><h3>&nbsp;</td>"; }
print "<td><a href=\"help.php\" target=\"scoring\"><h4><b><i>&gt;&gt;&nbsp;help</b></i></a></td>";	
print "</tr>";
    */




//<td><a href="projectchoose.php" target="scoring">projects</a></td>


/** Check for ownership of project **/

if (/*isset($_SESSION['projectid'])*/ false) {
	$sql = "SELECT * FROM projects 
                WHERE userid = ".$_SESSION['userid']." 
                AND projectid = " .$_SESSION['projectid'];
	if (mysqli_num_rows(dbquery($sql)) OR $_SESSION['userid'] == $sysadminid) { 
	print "<tr><td><img src=\"img/m_owner.gif\" border=0 alt=\"\"></td><td>&nbsp;</td>
	<td><a href=\"strainassign.php\" target=\"scoring\">strainassign</a></td>
	<td><a href=\"moveprojectimages.php\" target=\"scoring\">move images</a></td>
	<td><a href=\"moveprojectusers.php\" target=\"scoring\">assign users</a></td>
	<td>&nbsp;</td>"; }
}

if (/*$_SESSION['userid'] == $sysadminid*/false) {
	print "<td><img src=\"img/m_sysadmin.gif\" border=0 alt=\"\"></td>
	<td><a href=\"users.php\" target=\"scoring\">users</a></td>
	<td><a href=\"add_subcell.php\" target=\"scoring\">add localization</a></td>
	</tr></table>";
}
else {
  //  print "</table>"; }
}

// printfooter();


?>

