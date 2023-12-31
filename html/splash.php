<?php
require("locInclude.php");
require("$include_dir/include.php");
//require("$include_dir/secure.php");

print "<html>";
print "<head>";
print "<link rel=\"stylesheet\" href=\"imagedb.css\">";
print "<meta name=\"robots\" content=\"noindex,nofollow\">";
print "</head>";
print "<body>";

// centermsg("Please select a search option from the menu above.");
$alt = "a photomosaic image of a yeast cell expressing a GFP-fusion to the ER resident protein Pho86 comprised of cell images from the entire database\n\ncreated: A. Carroll\nsoftware: Mazaika\nconcept: J. Falvo, N. Dephoure";
$tableMsg = "";
$tableMsg .= "<div>";
// $tableMsg .= "<p><font color='red'>The yeastgfp database will not be accessible from 9 a.m. 30 Nov 2005 to 11 a.m. 30 Nov 2005 for a planned power shutdown (times PST).</font><br><br>";
$tableMsg .= "<table width=750 cellspacing=0 cellpadding=10 bgcolor=\"#EEEEEE\" style=\"border: 1px black solid\">\n";
$tableMsg .= "<tr>\n";
$tableMsg .= "<td><img src=\"img/splash.jpg\" alt=\"".$alt."\"></td>\n";
$tableMsg .= "<td>\n";
$tableMsg .= "<h2>Welcome to the Yeast GFP Fusion Localization Database</h2>\n";
$tableMsg .= "<h2>The YeastGFP database of global analysis of protein localization studies in the budding yeast, <i>S. cerevisiae</i>, was originally designed and built by the laboratories of Erin O'Shea and Jonathan Weissman at the University of California, San Francisco.&nbsp;&nbsp;It is now hosted by SGD.</h2>";
$tableMsg .= "<p>&nbsp;";
$tableMsg .= "<p>&nbsp;&gt;&nbsp;quick case-insensitive searches of the database may be performed on yeast orf names (yal001c) or gene names (TFC3)\n";
$tableMsg .= "<p>&nbsp;&gt;&nbsp;separate multiple orfs/genes with a space (e.g. yal001c zwf1 bud2 etc.)\n";
$tableMsg .= "<p>&nbsp;&gt;&nbsp;more advanced searching and downloading can be done in Advanced Query\n";
$tableMsg .= "<p>&nbsp;&gt;&nbsp;GFP-tagged strains can be obtained from <a href=\"http://clones.invitrogen.com/cloneinfo.php?clone=yeastgfp\" class=\"splash\" target=\"_blank\">Invitrogen</a>.";
$tableMsg .= "<p>&nbsp;&gt;&nbsp;TAP-tagged strains can be obtained from <a href=\"http://www.openbiosystems.com\" target=\"_blank\" class=\"splash\">Open Biosystems</a>.";
$tableMsg .= "<p>&nbsp;&gt;&nbsp;more details available in \n";
$tableMsg .= "&nbsp;&nbsp; ";
$tableMsg .= "<a href=\"info.php\" target=\"scoring\" class=\"splash\"><i><b>&gt;&gt; info</b></i></a>\n";
$tableMsg .= "&nbsp;&nbsp; ";
$tableMsg .= "<a href=\"faq.php\" target=\"scoring\" class=\"splash\"><i><b>&gt;&gt; faq</b></i></a>\n";
$tableMsg .= "&nbsp;&nbsp; ";
$tableMsg .= "<a href=\"help.php\" target=\"scoring\" class=\"splash\"><i><b>&gt;&gt; help</b></i></a>\n";
$tableMsg .= "<br>";
$tableMsg .= "<br>";
$tableMsg .= "<br>";
//$tableMsg .= "<p><font color='#CC6633'><b>April 15th, 2004 &nbsp;&nbsp;-- &nbsp; NEWS &nbsp; -- &nbsp;&nbsp;GFP Strain distribution begins.</b></font>\n";
//$tableMsg .= "<p>The GFP tagged strains are now distributed by <a href=\"http://clones.invitrogen.com/cloneinfo.php?clone=yeastgfp\" class=\"splash\" target=\"_blank\">Invitrogen</a>. Strains are available individually and as a complete collection. Currently, you must search for the strains you are interested in at the <a href=\"http://clones.invitrogen.com/bacpacsearch.php\" class=\"splash\" target=\"_blank\">Invitrogen site</a>. \n";
//$tableMsg .= "<p>Coming Soon: direct links from yeastgfp search results to Invitrogen product pages.";
//$tableMsg .= "<br><br>";
$tableMsg .= "<br>\n";
$tableMsg .= "</td>\n";
$tableMsg .= "</tr>\n";
$tableMsg .= "<tr>\n";
$tableMsg .= "<td colspan=2 bgcolor=\"#FFFFFF\" style=\"border-top: 1px black solid\">\n";
$tableMsg .= "<table><tr><td>\n";
$tableMsg .= "This web site supports Huh, <i>et al.</i>, <i>Nature</i>  <b>425</b>, 686-691 (2003).";
$tableMsg .= "&nbsp;&nbsp;\n";
$tableMsg .= "</td><td>\n";
$tableMsg .= "<a href=\"nature02026_r.pdf\" target=\"_new\">&lt;pdf&gt;</a>";
$tableMsg .= "&nbsp;&nbsp;\n";
//$tableMsg .= "<a href=\"http://www.ncbi.nlm.nih.gov/entrez/query.fcgi?cmd=Retrieve&db=PubMed&list_uids=PMID&dopt=Abstract\" target=\"_blank\">";
//$tableMsg .= "&lt;PubMed&gt;";
//$tableMsg .= "</a>";
$tableMsg .= "</td></tr><tr><td>The quantitation data presented here is published in Ghaemmaghami, <i>et al.</i>, <i>Nature</i>  <b>425</b>, 737-741 (2003).";
$tableMsg .= "</td><td>\n";
$tableMsg .= "<a href=\"nature02046_r.pdf\" target=\"_new\">&lt;pdf&gt;</a>";
$tableMsg .= "&nbsp;&nbsp;\n";
//$tableMsg .= "<a href=\"http://www.ncbi.nlm.nih.gov/entrez/query.fcgi?cmd=Retrieve&db=PubMed&list_uids=PMID&dopt=Abstract\" target=\"_blank\">";
//$tableMsg .= "&lt;PubMed&gt;";
//$tableMsg .= "</a>";
$tableMsg .= "</td></tr><tr><td>Detailed collection construction methods can be found in Howson <i>et al.</i>, <i>Comp Funct Genom</i>  <b>6</b>, 2-16 (2005).";
$tableMsg .= "&nbsp;&nbsp;\n";
$tableMsg .= "</td><td>\n";
$tableMsg .= "<a href=\"HowsonCFG2005.pdf\" target=\"_new\">&lt;pdf&gt;</a>";
$tableMsg .= "</td></tr></table>";
$tableMsg .= "<br><br>";
// $tableMsg .= "This research is the work of the laboratories of ";
//$tableMsg .= "<a href=\"http://www.ucsf.edu/ekolab\" target=\"_blank\" class=\"splash\">";
//$tableMsg .= "Erin O'Shea";
//$tableMsg .= "</a> ";
//$tableMsg .= "and ";
//$tableMsg .= "<a href=\"http://www.ucsf.edu/jswlab\" target=\"_blank\" class=\"splash\">";
//$tableMsg .= "Jonathan Weissman";
//$tableMsg .= "</a> ";
//$tableMsg .= "at the ";
//$tableMsg .= "<a href=\"http://www.ucsf.edu/\" target=\"_blank\" class=\"splash\">";
//$tableMsg .= "University of California San Francisco.";
//$tableMsg .= "</a>\n ";

$tableMsg .= "<br>Please direct comments, concerns, and questions to <a href=\"mailto:sgd-helpdesk@lists.stanford.edu\">&lt;SGD&gt;</a>";
$tableMsg .= "<p>&copy; Copyright 2001 - 2006 University of California Regents. All rights reserved.\n";
$tableMsg .= "</td>\n";
$tableMsg .= "</tr>\n";
$tableMsg .= "</table>\n";
$tableMsg .= "</div>\n";
print $tableMsg;

print "</body>\n";
print "</html>\n";

?>
