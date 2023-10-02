function printheader() {
        global $nameofdb, $logo;
?>
<HTML>
<HEAD>
<TITLE><?=$nameofdb?></TITLE>
<link rel="stylesheet" href="imagedb.css">
</HEAD>
<BODY>
<table border=0 cellspacing=0 cellpadding=0 width=100%>
<tr bgcolor="#666666">
        <td width=15% align=right valign=middle><?=$logo?></td>
        <td align=right valign=middle>
                <font size=+2 color="#ffffff"><i><?=$nameofdb?></i></font></td>
</tr>
<?
print "<tr bgcolor='#FFFF66'><td></td><td><b>Login: </b>".$_SESSION['realname']."</td></tr>";
if ($_SESSION['project']) {
        print "<tr bgcolor='#FFFF66'><td></td><td><b>Project: </b>".$_SESSION['project']."</td></tr>";
}
?>
</table>

<table width=100%>
<tr>
<td width=15% bgcolor="#99FF66" valign=top align=right>
<!----menu---->
<?
$dir = `ls *.php`;
$files = explode("\n", $dir);
foreach($files as $file) {
        print "<p><a href=$file>$file</a>\n";
}
?>
</td>
<td valign=middle>
<!----body start---->
<?
}
