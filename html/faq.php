<?php
require("locInclude.php");
require("$include_dir/include.php");
?>

<html>
<head>
        <meta name="robots" content="noindex,nofollow">
<?

dumpStyleForHeader();
dumpExampleWindowJsFunctions();

?>

</head>

<body>
<table border=0 cellspacing=0 cellpadding=0 width=500><tr><td>

<h1>>&gt;&gt;&nbsp;Frequently Asked Questions</h1>

<b class="question">Can I get any more specific information and methods for strain collection construction?</b>
<blockquote>
A detailed methods paper has been published recently: Howson, RW, <i>et al.</i> Construction, verification and experimental use of two epitope-tagged collections of budding yeast strains. <i>Comparative and Functional Genomics</i>, <b>6</b>, 2-16 (2005).
<p>You can download it here: <a href="HowsonCFG2005.pdf" target="_new">&lt;pdf&gt;</a>
</blockquote>

<b class="question">How can I retrieve the oligo sequences used for strain construction?</b>
<blockquote>
You can download the sequences for the entire collection <a href="yeastGFPOligoSequence.txt" target="_new">&lt; here &gt;</a> (approx. 1 MB).<br>
You can also retrieve the sequences for a specific gene using the download feature in the Advanced Query.

</blockquote>

<b class="question">How do I make a link to the information about my favorite gene?</b>
<blockquote>
If you want to have a link on your web site to information about a specific gene on our web site, please use this URL, indicating your favorite ORF:
<pre>
<a href="http://yeastgfp.yeastgenome.org/getOrf.php?orf=YAL001C" target="_new">yeastgfp.yeastgenome.org/getOrf.php?orf=YAL001C</a>
</pre>
</blockquote>

<!-- <b class="question">How can I get all the images from the study?</b>
<blockquote>
Finally, we have all of these available as archives by chromosome.
<ul>
<li>&nbsp; <a href="archive/yeastGFPImagesChrI.tar.gz">yeastGFPImagesChrI.tar.gz</a> &nbsp;(31M)</li>
<li>&nbsp; <a href="archive/yeastGFPImagesChrII.tar.gz">yeastGFPImagesChrII.tar.gz</a> &nbsp;(171M)</li>
<li>&nbsp; <a href="archive/yeastGFPImagesChrIII.tar.gz">yeastGFPImagesChrIII.tar.gz</a> &nbsp;(57M)</li>
<li>&nbsp; <a href="archive/yeastGFPImagesChrIV.tar.gz">yeastGFPImagesChrIV.tar.gz</a> &nbsp;(350M)</li>
<li>&nbsp; <a href="archive/yeastGFPImagesChrV.tar.gz">yeastGFPImagesChrV.tar.gz</a> &nbsp;(113M)</li>
<li>&nbsp; <a href="archive/yeastGFPImagesChrVI.tar.gz">yeastGFPImagesChrVI.tar.gz</a> &nbsp;(47M)</li>
<li>&nbsp; <a href="archive/yeastGFPImagesChrVII.tar.gz">yeastGFPImagesChrVII.tar.gz</a> &nbsp;(230M)</li>
<li>&nbsp; <a href="archive/yeastGFPImagesChrVIII.tar.gz">yeastGFPImagesChrVIII.tar.gz</a> &nbsp;(112M)</li>
<li>&nbsp; <a href="archive/yeastGFPImagesChrIX.tar.gz">yeastGFPImagesChrIX.tar.gz</a> &nbsp;(90M)</li>
<li>&nbsp; <a href="archive/yeastGFPImagesChrX.tar.gz">yeastGFPImagesChrX.tar.gz</a> &nbsp;(150M)</li>
<li>&nbsp; <a href="archive/yeastGFPImagesChrXI.tar.gz">yeastGFPImagesChrXI.tar.gz</a> &nbsp;(143M)</li>
<li>&nbsp; <a href="archive/yeastGFPImagesChrXII.tar.gz">yeastGFPImagesChrXII.tar.gz</a> &nbsp;(213M)</li>
<li>&nbsp; <a href="archive/yeastGFPImagesChrXIII.tar.gz">yeastGFPImagesChrXIII.tar.gz</a> &nbsp;(211M)</li>
<li>&nbsp; <a href="archive/yeastGFPImagesChrXIV.tar.gz">yeastGFPImagesChrXIV.tar.gz</a> &nbsp;(178M)</li> 
<li>&nbsp; <a href="archive/yeastGFPImagesChrXV.tar.gz">yeastGFPImagesChrXV.tar.gz</a> &nbsp;(223M)</li>
<li>&nbsp; <a href="archive/yeastGFPImagesChrXVI.tar.gz">yeastGFPImagesChrXVI.tar.gz</a> &nbsp;(193M)</li>
</ul>
-->

<!--  CAN'T CURRENTLY DOWNLOAD SUCH A BIG FILE W/O CHANGING SOME APACHE SETTINGS
<p>
And as the whole collection:
<ul>
<li>2.5G  &nbsp; <a href="archive/yeastGFPImagesAll.tar.gz">yeastGFPImagesAll.tar.gz</a></li>
<li>2.5G  &nbsp; <a href="archive/yeastGFPImagesAllx.tar.gz">yeastGFPImagesAllx.tar.gz</a></li>
</ul> -->
<!-- Names are structured as follows:
<p>ORF_ver##-X-EXPOSURE_FILTER[_colocDESGINATOR].png
<ul>
<li>YAR07W_ver00-A-2000ms_DAPI.png</li>
<li>YAR07W_ver00-A-2000ms_DIC.png</li>
<li>YAR07W_ver00-A-2000ms_GFP.png</li>
<br>
<li>YAR019C_ver00-A-2000ms_DIC_colocSPB.png
<li>YAR019C_ver00-A-2000ms_GFP_colocSPB.png</li>
<li>YAR019C_ver00-A-2000ms_RFP_colocSPB.png</li>
</ul>

</blockquote>
-->

<b class="question">How were the abundance measurements in 'molecules/cell' determined?</b>
<blockquote>
Absolute protein abundances were determined by quantitative Western blot analysis of TAP-tagged strains.  Replicate analysis for a subset of tagged strains found a linear correlation coefficient of R = 0.94, with the pairs of proteins having a median variation of a factor of 2.0.  This error analysis does not account for potential alterations in the endogenous levels of the proteins caused by the the fused tag, which may be particularly disruptive for small proteins. Abundances that include error values were done in triplicate with serial dilutions of purified TAP-tagged standards included in each gel, which substantially reduces the measurement error.  In addition, for these strains, the tagged genes were confirmed to rescue the loss of function phenotype of the corresponding deletion strain. See Ghaemmaghami, <i>et al.</i>, <i>Nature</i>  <b>425</b>, 737-741 (2003). <a href="nature02046_r.pdf" target="_new">&lt;pdf&gt;</a><br>
</blockquote>

<b class="question">How can I get a copy of the library of strains used in this study?</b>
<blockquote>
The TAP-tagged library is distributed by <a href="http://www.openbiosystems.com" target="_blank">&lt;&nbsp;Open Biosystems&nbsp;&gt;</a>.<br>
The GFP-tagged library is distributed by <a href="http://clones.invitrogen.com/cloneinfo.php?clone=yeastgfp" target="_blank">&lt;&nbsp;Invitrogen&nbsp;&gt;</a>.<br>
The RFP-tagged strains used for the colocalization studies can be obtained by contacting <a href="mailto:arvidson@mcb.harvard.edu">Peter Arvidson</a>.
</blockquote>

<!-- <b class="question">What do I do if I want to comment on a specific localization presented here?</b>
<blockquote>A comment section is in place that allows you to associate comments with ORFs, so that anyone viewing the scorings in question can also view your comments. Access this feature using the graphical ORF summary.  <a href="javascript: openOrfExample();"> &lt;example&gt;</a>
<br><br>
General comments about the site should be emailed to <a href="mailto:jan.ihmels@gmail.com">jan.ihmels@gmail.com</a>. -->
</blockquote>

<b class="question">How should I cite data I retrieve from the yeastgfp database?</b>
<blockquote>If you reference localization data, you should cite Huh, <i>et al.</i>, <i>Nature</i>  <b>425</b>, 686-691 (2003) -- <a href="http://www.ncbi.nlm.nih.gov/entrez/query.fcgi?cmd=Retrieve&db=PubMed&list_uids=14562095&dopt=Abstract" target="_blank">PubMed</a><br>
If you reference protein abundance data, you should cite Ghaemmaghami, <i>et al.</i>, <i>Nature</i>  <b>425</b>, 737-741 (2003) -- <a href="http://www.ncbi.nlm.nih.gov/entrez/query.fcgi?cmd=Retrieve&db=PubMed&list_uids=14562106&dopt=Abstract" target="_blank">PubMed</a><br>
</blockquote>

<b class="question">How can I get the data in a tabular format for doing computation?</b>
<blockquote>You can either use Advanced Query and use all the default constraints to get information about localized ORFs (will take a few minutes to process), or download this <a href="allOrfData.txt">&lt; file &gt;</a> containing information about all ORFs (including those not tagged and not visualized by this study). (file size: 740K, 6235 lines)
</blockquote>

<!--
<b class="question">Why won't clicking on the clips bring up the full-field image?</b>
<blockquote>You are probably using Mozilla or Netscape Navigator -- the javascript for calling up that page isn't compatible with those browsers.  We're still trying to fix this -- suggestions are welcome.
<p>One workaround is to do your search in the Advanced Query section, and check the box to display localization clips (second one down). The clips in this display will link to the full size image.
</blockquote>
-->

<b class="question">How can I perform a wild-card search?</b>
<blockquote>Wild-card searching is not supported at this time.</blockquote>

<b class="question">Why can't I find a named gene with the name I'm typing in?</b>
<!-- <blockquote>Our gene names and aliases will be updated automatically each week when the SGD releases it's latest update. --> 
<blockquote>The input name you are using may be too old to be official or may be too new to be in the most recent release.&nbsp;&nbsp;Try entering the systematic ORF name to retrieve the data of interest.
</blockquote>

<b class="question">What version of the SGD was used to guide construction of the Libraries used here?</b>
<blockquote>The library was constructed using the information in the SGD as of the spring of 2001.
<br><br>You can download the file (includes ORF name, gene name, chromosomal coordinates, etc.) used for strain construction <a href="orfsForOligos.txt">&lt; here &gt;</a>.
<br><br>The names of most ORFs have been updated to match the most current name in the SGD, in the cases where there is a one-to-one match. ORFs that have been merged with other ORFs or deprecated have been left as is to reflect the intended genomic location of the tag.
</blockquote>

</td></tr></table>

</body>
</html>
