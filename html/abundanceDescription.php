<?php

require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/projects_inc.php");

print "<p>";
?>

<html>
<head>
<title>yeastgfp -- Explanation of Abundance</title>
<link rel="stylesheet" href="imagedb.css">
</head>
<body>


    <h2>Protein Abundance Data</h2>
<ul>The data are reported as follows:<br><br>
<li><b>number</b> (e.g. 3000 molecules/cell)
     <br>Absolute protein abundances were determined by quantitative Western blot analysis of TAP-tagged strains.  Replicate analysis for a subset of tagged strains found a linear correlation coefficient of R = 0.94, with the pairs of proteins having a median variation of a factor of 2.0.  This error analysis does not account for potential alterations in the endogenous levels of the proteins caused by the the fused tag, which may be particularly disruptive for small proteins.
<li><b>number plus error</b> (e.g. 3000 &plusmn; 124)
     <br>Abundances that include error values were done in triplicate with serial dilutions of purified TAP-tagged standards included in each gel, which substantially reduces the measurement error.  In addition, for these strains, the tagged genes were confirmed to rescue the loss of function phenotype of the corresponding deletion strain.
<br>
<li><b>&quot;not visualized&quot;</b>
<br>Either the tagging was unsuccessful or no signal was detected.
<br>
<li><b>&quot;low signal&quot;</b>
<br>The tagging was successful, but the signal was not sufficiently high above background to permit accurate quantitation (&sim;50 molecules/cell).
<br>
<li><b>&quot;technical problem&quot;</b>
<br>The protein was detectable but could not be quantitated because it did not migrate as a single band or comigrated with the internal standards in the gel.
</ul>

<p>
<b>reference</b>
<br>Ghaemmaghami, S, Huh, WK, Bower, K, Howson, RW, Belle, A, Dephoure, N, O'Shea, EK, and JS Weissman. Global analysis of protein expression in  yeast. <i>Nature</i>, <b>425</b>, 737-741 (2003).
<br><br>&nbsp;&nbsp;<a href="nature02046_r.pdf" target="_new">&lt;pdf&gt;</a>

</body>
</html>