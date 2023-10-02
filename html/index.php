<?php
require("locInclude.php");
require("$include_dir/include.php");
/* require("$include_dir/secure.php");*/
?>

<html>
<head>

<?php

$frame = TRUE;

// function required for clip switching
dumpOrfDisplayHeaderJs($frame);
dumpStyleForHeader();

?>

<title>Yeast GFP Fusion Localization Database</title>
<meta name="description" content="web interface to our database of localizations for Saccharomyces cerevisiae gene fusions to green fluorescent protein (GFP)">
<meta name="robots" content="index,follow">
<link rel="stylesheet" href="imagedb.css">
</head>

<frameset rows=80,* border=0>

<!-- <frame src="score_banner.php" name="score_banner" marginwidth=0 marginheight=0 noresize scrolling=no> -->
<frame src="menu.php" name="menu" marginwidth=0 marginheight=0 noresize scrolling=no>

<?php
/*
print "<frame ";

if (isset($_SESSION['goback']) AND strpos($_SESSION['goback'],"index")==0) {
	$redrt = $_SESSION['goback'];
	$_SESSION['goback'] = "";
	print "src=\"$redrt\" ";
} elseif (!isset($_SESSION['projectid'])) {
  $_SESSION['projectid'] = 1;
  print "src=\"splash.php\" ";
} else {
  print "src=\"projectchoose.php\"";

}

*/
?>

<frame src="splash.php" name="scoring" marginwidth=30 marginheight=15 noresize scrolling=auto>


</frameset>

</html>

