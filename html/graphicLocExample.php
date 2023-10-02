<?php

require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/projects_inc.php");

?>

<html>
<head>
<title>Example of Graphic Localization Display</title>
<link rel="stylesheet" href="imagedb.css">
</head>
<body>

<?php
// SPECIFY TWO GOOD EXAMPLE LOCS, WITH AND WITHOUT COLOC....
$locList = array(20110,14789);
print(buildLocalizationDisplayHTMLFromLocalizationList($locList));

print "<p>One box is presented for each localization scored in the database. Different strains are indicated by the background color of the box. Standard Gene name listed in green at left, if known -- systematic ORF name always listed; both are linked to the corresponding yeastgenome.org page. The second column contains the subcellular localization, the cell cycle phase for the scored cell, and the growth media. The third column contains information about the cell: Int -- fluoresence intensity, Hom -- fluoresence homogeneity, and Mor -- cell morphology. Clips for colocalization are (L to R) merged GFP-RFP, GFP, RFP, and DIC. Other clips are GFP, DIC, and DAPI.  Click on the clip to see the full field image. 'Setid' is an internal reference -- 'x' and 'y' describe the location of the cell in the full field image in pixels.";


?>

</body>
</html>