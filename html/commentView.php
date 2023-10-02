<?php

require("locInclude.php");
require("$include_dir/include.php");
//require("$include_dir/secure.php");
require("$include_dir/projects_inc.php");
    

?>

<html>
<head>
        <title>comments for orf <?php=$_GET['orfid']?>-- yeastgfp.ucsf.edu</title>
        <link rel="stylesheet" href="imagedb.css">
	<link rel="stylesheet" href="formatQuery.css">
        <meta name="robots" content="noindex,nofollow">
</head>

<body bgcolor="#FFFFFF">

<?php


if (($_GET['orfid']) == "" || preg_match("/\D/",$_GET['orfid'])) {
  //print "<p>not targeted by the correct page....";
  $idErrorMsg = "";
  $idErrorMsg .= "<table width=400 height=280 border=0 cellspacing=0 cellpadding=0><tr><td align=center valign=middle>";
  $idErrorMsg .= "<p><b>No ORF Specified -- not targeted by the correct page</b>";
  $idErrorMsg .= "<p>Please close the window and return to yeastgfp.ucsf.edu";
  $idErrorMsg .= "</td></tr></table>";
} else {
  $orfId = $_GET['orfid']; 
}
    
print "<table width=440 border=0 cellpadding=5 cellspacing=0 style=\"border:1pt solid black; \">\n";
print "<tr bgcolor=\"#669966\">\n";
print "<td class=\"title\">\n";
print "&nbsp;&gt;&gt; comments on this orf";
print "</td>\n";
print "</tr>\n";

// ORF INFORMATION
print "<tr bgcolor=\"#99CC99\">\n";
print "<td>\n";

if (!isset($idErrorMsg)) {
  dumpOrfInfoTableForComment($orfId);
} else {
  print "&nbsp;\n";
}

print "</td>\n";
print "</tr>\n";

print "<tr bgcolor=\"#FFFFFF\">\n";
print "<td align=center>\n";
print "<br>";

if (!isset($idErrorMsg)) {
  displayComments($orfId);
} else {
  print $idErrorMsg;
}

print "</td>\n";
print "</tr>\n";
print "<tr bgcolor=\"#99CC99\">\n";
print "<td>\n";
print "additional remarks can be sent to <a href=\"mailto:jan.ihmels@gmail.com\">jan.ihmels@gmail.com</a>";
print "</td>\n";
print "</tr>\n";
print "</table>";

// CLOSE WINDOW
print "<p align=right><a href=\"javascript:void(window.close())\">close window &nbsp;&nbsp;</a>";

// PASS ORFID AS HIDDEN
print "<input type=hidden name=\"orfid\" value=\"".$_GET['orfid']."\">";

?>

