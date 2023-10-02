<?php

require("locInclude.php");
require("$include_dir/include.php");
//require("$include_dir/secure.php");
require("$include_dir/projects_inc.php");

?>

<html>


<?php

print "<head>\n";

$frame = FALSE;

// function required for clip switching
dumpOrfDisplayHeaderJs($frame);
dumpStyleForHeader();
dumpExampleWindowJsFunctions();

print "<link rel=\"stylesheet\" href=\"formatQuery.css\">";

print "</head>\n";

// function required for clip switching
dumpBodyForJsIcons();


print "<table border=0 cellspacing=0 cellpadding=10>\n";
print "<tr align=center><td bgcolor=\"#669966\"><a href=\"index.php\"><i><font size=4 color=\"#FFFFFF\">Yeast GFP Fusion Localization Database</font></i></a></td></tr>\n";
print "<tr align=center><td bgcolor=\"#99CC99\">yeastgfp.yeastgenome.org</td></tr>\n";
print "<tr><td height=2 bgcolor=\"#000000\"></td></tr>\n";
print "<tr><td><br>\n";

// print "<p><font color='red'>The yeastgfp database will not be accessible from 9 a.m. 30 Nov 2005 to 11 a.m. 30 Nov 2005 for a power shutdown (times PST).</font><br><br>";

// USE THE _GET SPECIFIED ORF TO FIND A LOCID
$orfNumber = $_GET['orf'];
$orfId = getOneToOneMatch("orfs", "orfnumber","'".$orfNumber."'","orfid");

// CHECK FOR ERRORS, MISTYPED ORFS, ETC. AND THROW A MESSAGE
if ($orfId == NULL) {
  print "<h2>ERROR -- ORF NOT FOUND</h2>";
  print "<table border=0 cellspacing=0 cellpadding=0><tr><td>the orf <b>".$orfNumber."</b> was not found</td></tr>";
  print "<tr><td>please check the systematic name and try again</td></tr>";
  print "<tr><td>or try another search <a href=\"index.php\">&lt;here&gt;</a>.<br><br></td></tr></table>";

} else {
  
  // CONVERT TO ARRAY FOR USING SAME FUNCTION AS QUERY.PHP
  $orfList = array($orfId);

  // DISPLAY
  displayBestLocs($orfList);

  // LINKS TO EXAMPLE, LEGEND, HOME, HELP
  print "<a href=\"javascript: openOrfExample();\"> &lt;example&gt;</a>&nbsp;&nbsp;\n";
  print "<a href=\"javascript: openLegend();\">&lt;legend&gt;</a>&nbsp;&nbsp;\n";
  print "<a href=\"javascript: openAbundance();\">&lt;abundance description&gt;</a>&nbsp;&nbsp;\n";
  print "<a href=\"index.php\">&lt;home&gt;</a>&nbsp;&nbsp;\n";
  print "<a href=\"help.php\" target=\"_blank\">&lt;help&gt;</a>\n";

}

// MAKE A FOOTER THAT GIVES CREDIT
$tableMsg = '';
$tableMsg .= "the localization data presented here is published in Huh, <i>et al.</i>, <i>Nature</i> <b>425</b>, 686-691 (2003).";
$tableMsg .= "&nbsp;&nbsp;";
$tableMsg .= "<a href=\"nature02026_r.pdf\" target=\"_new\">&lt;pdf&gt;</a>";

$tableMsg .= "<br>the quantitation data presented here is published in Ghaemmaghami, <i>et al.</i>, <i>Nature</i> <b>425</b>, 737-741 (2003).";
$tableMsg .= "&nbsp;&nbsp;";
$tableMsg .= "<a href=\"nature02046_r.pdf\" target=\"_new\">&lt;pdf&gt;</a>";
$tableMsg .= "<br>detailed collection construction methods can be found in Howson <i>et al.</i>, <i>Comp Funct Genom</i>  <b>6</b>, 2-16 (2005).";
$tableMsg .= "&nbsp;&nbsp;\n";
$tableMsg .= "<a href=\"HowsonCFG2005.pdf\" target=\"_new\">&lt;pdf&gt;</a>";
$tableMsg .= "&nbsp;&nbsp;\n";

$tableMsg .= "<br><br>please direct comments, concerns, and questions regarding <a href=\"index.php\">yeastgfp.yeastgenome.org</a> <a href=\"mailto:sgd-helpdesk@lists.stanford.edu\">&lt;here&gt;</a>"; 

print "</tr></td>";
print "<tr><td height=2 bgcolor=\"#000000\"></td></tr>";
print "<tr align=center><td bgcolor=\"#99CC99\">".$tableMsg."</td></tr>";
print "</table>";


?>

</body>
</html>
