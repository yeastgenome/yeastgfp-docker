<?php

require("locInclude.php");
require("$include_dir/include.php");

?>

<html>
<head>
	<link rel="stylesheet" href="imagedb.css">
        <meta name="robots" content="index,nofollow">

<?
dumpExampleWindowJsFunctions();
?>

</head>

<body>

<table border=0 cellspacing=0 cellpadding=0 width=500><tr><td>

<h1>&gt;&gt;&nbsp;Strain Information</h1>

<ul>
<li> <b>Strain Background</b>: EY0986
<br><a href="http://www.atcc.org/SearchCatalogs/Fungi_Yeasts.cfm" target="_blank">ATCC</a> 201388: <i>MAT</i><b>a</b><i> his3&Delta;1 leu2&Delta;0 met15&Delta;0 ura3&Delta;0</i> (S288C)
<br><br>
</li>
<li> <b>Strain Background for RFP-tagging for Colocalization</b>: EY0987
<br><a href="http://www.atcc.org/SearchCatalogs/Fungi_Yeasts.cfm" target="_blank">ATCC</a> 201389: <i>MAT</i><b>&alpha;</b><i> his3&Delta;1 leu2&Delta;0 lys2&Delta;0 ura3&Delta;0</i> (S288C)
<br><br>
</li>

<li><b>Library distribution information</b>
<p>Strains are available individually and as collections.
<p>
<b>TAP</b>: <a href="http://www.openbiosystems.com" target="_blank">Open Biosystems</a>
<p>
<b>GFP</b>: <a href="http://clones.invitrogen.com/cloneinfo.php?clone=yeastgfp" target="_blank">Invitrogen</a>

<br><br>
</li>

<li> <b>Markers for Colocalization </b>
<br><br>
<table border=1 cellspacing=0 cellpadding=2>

<th>localization
</th>
<th>ORF or stain
</th>
<tr><td>actin</td><td align=center>Sac6</td></tr>
<tr><td>early Golgi/Cop1</td><td align=center>Cop1</td></tr>
<tr><td>endosome</td><td align=center>Snf7</td></tr>
<tr><td>ER to Golgi vesicle</td><td align=center>Sec13</td></tr>
<tr><td>Golgi apparatus</td><td align=center>Anp1</td></tr>
<tr><td>late Golgi/clathrin</td><td align=center>Chc1</td></tr>
<tr><td>lipid particle</td><td align=center>Erg6</td></tr
<tr><td>mitochondrion</td><td align=center>MitoTracker</td></tr>
<tr><td>nucleus</td><td align=center>DAPI</td></tr>
<tr><td>nucleolus</td><td align=center>Sik1</td></tr>
<tr><td>nuclear periphery</td><td align=center>Nic96</td></tr>
<tr><td>peroxisome</td><td align=center>Pex3</td></tr>
<tr><td>spindle pole</td><td align=center>Spc42</td></tr>

</table>
<br>Other localizations were determined by comparison of the GFP images to the DIC and DAPI images.
<br><br>
</li>

<li> <b>RFP-tagged Strains for Colocalization </b>
<br><br>
To obtain the strains used for the colocalization studies, please contact Peter Arvidson at <a href="mailto:arvidson@mcb.harvard.edu">arvidson@mcb.harvard.edu</a> test
<br><br>
The mRFP plasmid used for construction of the strains (pFA6a-mRFP1-kanMX6) is distributed by the Dana Farber/Harvard Cancer Center DNA Resource Core Plasmid Repository. Contact Roger Tsien for authorization and a Material Transfer Agreement and then submit your request to Stephanie Mohr of the DF-HCC DNA Resource Core.
<p><a href="http://plasmid.med.harvard.edu/PLASMID/" target="_new">&lt; DF-HCC &gt;</a>
<p><a href="http://dnaseq.med.harvard.edu/contactus.html" target="_new">&lt; DF-HCC Contact Info &gt;</a>
<p><a href="http://www-chem.ucsd.edu/research/profile.cfm?cid=C01821" target="_new">&lt; Roger Tsien Contact Info &gt;</a>
<p>The sequence of the RFP plasmid used to generate the fusions is <a href="pFA6a-mRFP-KanMX61.doc">here</a>.
<p>The sequence of the GFP plasmid used to generate the fusions is <a href="http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?db=nucleotide&val=2623981" target="_new">here</a>.

</li>
<br><br>

</ul>

</td></tr></table>

</body>
</html>
