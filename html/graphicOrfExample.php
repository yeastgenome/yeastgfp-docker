<?php

require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/projects_inc.php");

?>

<html>
<head>
<title>Example of Graphic ORF Display</title>
<link rel="stylesheet" href="imagedb.css">
</head>
<body>

<?php
// FUNCTIONS REQUIRED FOR SWITCHING CLIP

$frame = FALSE;
dumpOrfDisplayHeaderJs($frame);
dumpBodyForJsIcons();
// FUNCTION REQUIRED FOR POP-UP FOR ABUNDANCE INFO
dumpExampleWindowJsFunctions();

// SPECIFY A GOOD EXAMPLE ORF, WITH COLOCS....
$orfNumber = "YDR425W";
$orfId = getOneToOneMatch("orfs", "orfnumber","'".$orfNumber."'","orfid");

// CONVERT TO ARRAY FOR USING SAME FUNCTION AS QUERY.PHP
$orfList = array($orfId);
displayBestLocs($orfList);

print "<p>Click on the cartoons at right to show the cell scored for that localization.";
print " Mouse over the cartoon to see what localization it represents -- this will be displayed in the 'loc' text box when you click on a cartoon.";
print " Cell clips are presented at bottom left -- click on the clip to see the full field image.";
print " Clips for colocalization are (L to R) merged GFP-RFP, GFP, RFP, and DIC.";
print " Other clips are GFP, DIC, and DAPI.";
print " Standard Gene name listed in green, if known; systematic ORF name always listed.";
print " The systematic ORF name is a link to the yeastgenome.org page for the ORF.";
print " 'Molecules/cell' links to an";
print " <a href=\"javascript: openAbundance();\">explanation of abundance measurements</a>";
print " -- values as determined by Ghaemmaghami, <i>et al.</i> (2003).";
print " The 'order' link will take you to Open Biosystems, the supplier of the TAP library strains.";
print " The 'add' link under 'comments' will allow you to make a comment on the localization scoring for the strain -- any user of the database can read these comments by clicking 'view' (this strain has an example comment).";

?>

</body>
</html>