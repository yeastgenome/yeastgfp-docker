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
  <style type="text/css">
  <!--
  #topic0 {display:block; }
  #topic1 {display:none; }
  #topic2 {display:none; }
  #topic3 {display:none; }
  #topic4 {display:none; }
  #topic5 {display:none; }
  #topic6 {display:none; }
  #topic7 {display:none; }
  #topic8 {display:none; }
  #topic9 {display:none; }
  #topic10 {display:none; }
  #topic11 {display:none; }
  #topic12 {display:none; }
  #topic13 {display:none; }
  #topic14 {display:none; }
  #topic15 {display:none; }
  #topic16 {display:none; }
  #topic17 {display:none; }

  .myTopic { position: absolute; top: 100px; left: 20px; width: 500px; height: 470px; background-color: #EEEEEE; padding: 15px; }
  
  .helpDisplay { position: absolute; top: 20px; left: 20px; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 900; color: #000000 }
  .helpSearching { position: absolute; top: 20px; left: 120px; bgcolor: #ccaacc; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 900; color: #000000 }
  .helpData { position: absolute; top: 20px; left: 240px; bgcolor: #ccaacc; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 900; color: #000000 }
  .helpDetails { position: absolute; top: 20px; left: 350px; bgcolor: #ccaacc; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 900; color: #000000 }
  .helpFeedback { position: absolute; top: 20px; left: 460px; bgcolor: #ccaacc; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 900; color: #000000 }
  
  //-->
  </style>
  <script type="text/javascript">
  <!--
  // function for switching the help topics
  function show(ind) {
    var d = document.getElementById("topic");
    var sd = d.getElementsByTagName("div");
    for (var i=0;i<sd.length;i++) {
      if (/^topic/.test(sd[i].id)) {
        sd[i].style.display = "none";
      }
    }  
    document.getElementById("topic"+ind).style.display = "block";
  }
  //-->
  </script>
</head>

<body>

<div>
  <div class="helpDisplay">
    Display<br>
    <a href="javascript:show(1)">&gt; Clips</a><br>
    <a href="javascript:show(2)">&gt; Icons</a><br>
    <a href="javascript:show(3)">&gt; Width</a><br>
    <a href="javascript:show(4)">&gt; Browsers</a><br>
  </div>
  <div class="helpSearching">
    Searching<br>
    <a href="javascript:show(5)">&gt; Quick Search</a><br>
    <a href="javascript:show(6)">&gt; Query </a><br>
    <a href="javascript:show(7)">&gt; Options</a><br>
  </div>
  <div class="helpData">
    Data<br>
    <a href="javascript:show(8)">&gt; Abundance</a><br>
<!--    <a href="javascript:show(9)">&gt; Download</a><br> -->
<!--     <a href="javascript:show(17)">&gt; Images</a><br> -->
    <a href="javascript:show(15)">&gt; Links</a><br>    
  </div>
  <div class="helpDetails">
    Details<br>
    <a href="javascript:show(10)">&gt; Strains</a><br>
    <a href="javascript:show(16)">&gt; Oligos</a><br>
    <a href="javascript:show(11)">&gt; References</a><br>
    <a href="javascript:show(12)">&gt; Database</a><br>
  </div>
  <div class="helpFeedback">
    Feedback<br>
<!--    <a href="javascript:show(13)">&gt; Comments</a><br> -->
    <a href="javascript:show(14)">&gt; Contact</a><br>
  </div>

  <div id="topic">
    <div id="topic0" class="myTopic">
    <p>&nbsp;Please click on one of the topics above.
    <br><br><br><br><br><br><br>
    </div>

    <div id="topic1" class="myTopic">
<h2>Localization Clip Display</h2>
<p>Individual cells are shown as clips of the GFP image and other images for that set.  There are two general types of image sets: colocalization with an RFP-tagged marker protein or normal.  They can be distinguished by color and by number of clips.
<ul>
<li><b>normal</b>, 3 clips <br>
<br><img src="/images/clipsNew/914_clipPlus1.png">
<br><table width=180 border=1 cellspacing=0 cellpadding=0><tr><td width=60 align=center>GFP</td><td width=60 align=center>DIC</td><td width=60 align=center>DAPI</td></tr></table>
</li><br>
<li><b>colocalization</b>, 4 clips <br>
<br><img src="/images/clipsNew/17704_clipPlus0GFPPlus1RFP.png">
<br><table width=240 border=1 cellspacing=0 cellpadding=0><tr><td width=60 align=center>merged GFP/RFP</td><td width=60 align=center>GFP</td><td width=60 align=center>RFP</td><td width=60 align=center>DIC</td></tr></table>
</li>
</ul>
    </div>

    <div id="topic2" class="myTopic">
<h2>Subcellular Localizations and Icons</h2>
<p>The localization categories used in this database are summarized in <a href="javascript: openLegend();">&lt; this table &gt;</a>, along with the cartoons used in the graphical orf display.
<p>Localizations initially scored as ambiguous were refined into more specific localizations by colocalization with RFP-tagged proteins, as described in <a href="info.php" target="scoring">&gt;&gt; info</a>.
    </div>

    <div id="topic3" class="myTopic">
<h2>Viewing Data</h2>
<p>Several of the features of the database create web pages that are very wide.
<ul>
<li>You will want your browser window to be 800 pixels wide to see all of the menu and the localization and ORF summaries.
<li>Some features, such as the ORF summary table, are too wide to display in a single window view -- you will need to scroll to see it all.
<li>The full-field microscope images are 535 pixels wide, and two are displayed side by side for comparison -- you will need a window 1100 pixels wide to see all of both images.
</ul>
    </div>

    <div id="topic4" class="myTopic">
<h2>Browser Compatibility</h2>

<p>Most display functions will work with most modern browsers -- text browsers are not recommended for viewing this data.  
<ul>Current Known Incompatibilities:
<li>Netscape or Mozilla: clicking on the clip in the graphical ORF display will not display the full-field image.  To view the full-field image in these browsers, you will need to use the static link present in the graphical localization display, not the graphical ORF summary. 
<li>Netscape or Mozilla: you will not be able to switch between full-field images (to view different exposure settings or reference images).  You will only be able to view the GFP and DIC images.
<li>Safari: After viewing a colocalization image in the graphical ORF summary, the normal images (with three cells) will be stretched out horizontally.
</ul>
    </div>

    <div id="topic5" class="myTopic">
<h2>Quick Search</h2>
<p>Enter as many systematic orf names or gene names as you like, separated by a space.
<p>Currently, wildcard searching is not supported.
<p>All matching localizations for each will be returned as a graphical summary for each orf.
<p>Click on the cartoons to see the image clip for the scored cell.
<p>Click on a clip to see the whole microscope image field from which the cell was selected.
<blockquote><b>NOTE:</b> Viewing the full-field images will result in several MB of images being downloaded (exposure series and reference images), so use discretion in clicking on the clips if you are on a slow connection.</blockquote>
<p>You will be asked to resolve ambiguities in the gene names you input before the search proceeds.
    </div>

    <div id="topic6" class="myTopic">
<h2>Advanced Query</h2>
<p>This allows you to "AND" together several search criteria.
<p>Only ORFs with valid localizations will be returned -- other ORFs will be ignored.
<p>Make sure there is an asterix ("*") in each field that you are not constraining.
<p>Enter as many systematic ORF names or gene names as you like, separated by a space or an asterix ("*") if you are not constraining the search by ORF name.
<p>Currently, wildcard searching is not supported.
<p>The gene name alias functions available in quick search are not yet installed in Advanced Query -- if you are unsure of the gene names for the orfs you are interested in, use Quick Search first.
<p>You will be warned of ambiguities in gene names that you input in the search results, but you will not be able to select them to include in your search, as you can in the Quick Search.
    </div>

    <div id="topic7" class="myTopic">
<h2>Options for Data Display</h2>
<ul>
<li><b>graphical ORF summary</b>: summarizes all localizations for ORF, with clickable cartoons to view clips for localizations 
&nbsp;&nbsp;<a href="javascript: openOrfExample();">&lt; example &gt;</a> 
</li>
<!-- <blockquote>NOTE: the graphical summary only displays representative localizations for each category; to see the rest of the localizations, including differences in intensity or cell cycle phase, use the graphical localizations display.</blockquote> -->
<li><b>graphical localizations</b>: individual localizations displayed, along with details about the scored cell
&nbsp;&nbsp;<a href="javascript: openLocExample();">&lt; example &gt;</a> 
</li>
<li><b>ORF summary table</b>: produces table of checkboxes for ORFs found by search</li>
<!-- <li><b>file download</b>: similar to the data presented in the ORF summary table, but as a tab-delimited text file -->
<!-- <ul>
<li>The file will include the yORF systematic name, a gene name (if available), whether or not the ORF was GFP tagged, and whether or not GFP fluoresence was visualized, by default.</li>
<li>Select additional columns to include in the file and a sort method (if desired) using the checkboxes.</li>
<li>Only tagged, visualized, and localized ORFs are included in the downloaded file -- the full data set (all fields, all genes, 740 KB, 6235 lines) can be found <a href="allOrfData.txt">&lt; here &gt;</a>.</li>
</ul>
</li> -->

</ul>
<p>To display results graphically for more than 100 genes, first produce a summary table, select the ORFs of interest from those returned, and search that list using the button at the bottom of the page.
<p>Click on any clip image to see the microscope image field from which the clip was selected.
<blockquote><b>NOTE:</b> Viewing the full-field images will result in several MB of images being downloaded (exposure series and reference images), so use discretion in clicking on the clips if you are on a slow connection.</blockquote>
    </div>


    <div id="topic8" class="myTopic">
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
    </div>

    <div id="topic9" class="myTopic">
    <h2>File Download</h2>
    <p>Returns data similar to the that presented in the ORF summary table, but as a tab-delimited text file.
<ul>
<li>The file will include the yORF systematic name, a gene name (if available), whether or not the ORF was GFP tagged, and whether or not GFP fluoresence was visualized, by default.</li>
<li>Select additional columns to include in the file and a sort method (if desired) using the checkboxes.</li>
<li>Only tagged, visualized, and localized ORFs are included in the downloaded file -- the full data set (all fields, all genes, 740 KB, 6235 lines) can be found <a href="allOrfData.txt">&lt; here &gt;</a>.</li>
<li>If you want to obtain all of the image files, you will need to contact <a href="mailto:sgd-helpdesk@lists.stanford.edu">SGD</a> directly. Soon, this will be available on the SGD ftp site.</li>
</ul>
</div>

    <div id="topic10" class="myTopic">
<h2>Strains</h2>
<p>The TAP-tagged library used to quantitate protein abundance are distributed by <a href="http://www.openbiosystems.com" target="_blank">&lt;&nbsp;Open Biosystems&nbsp;&gt;</a>.
<p>The GFP-tagged library are distributed by <a href="http://clones.invitrogen.com/cloneinfo.php?clone=yeastgfp" target="_blank">&lt;&nbsp;Invitrogen&nbsp;&gt;</a>.
<p>Distributors are providing the TAP and GFP libraries as the entire collection, and as individual strains.
<p>The RFP-tagged strains used for the colocalization studies can be obtained by contacting <a href="mailto:arvidson@mcb.harvard.edu">&lt;&nbsp;Peter Arvidson&nbsp;&gt;</a>.
<p>The mRFP plasmid used for construction of the strains (pFA6a-mRFP1-kanMX6) is distributed by the Dana Farber/Harvard Cancer Center DNA Resource Core Plasmid Repository. Contact Roger Tsien for authorization and a Material Transfer Agreement and then submit your request to Stephanie Mohr of the DF-HCC DNA Resource Core.
<p><a href="http://plasmid.med.harvard.edu/PLASMID" target="_new">&lt; DF-HCC &gt;</a>
<p><a href="http://dnaseq.med.harvard.edu/contactus.html" target="_new">&lt; DF-HCC Contact Info &gt;</a>
<p><a href="http://www-chem.ucsd.edu/research/profile.cfm?cid=C01821" target="_new">&lt; Roger Tsien Contact Info &gt;</a>
<p>The sequence of the RFP plasmid used to generate the fusions is <a href="pFA6a-mRFP-KanMX61.doc">here</a>.
<p>The sequence of the GFP plasmid used to generate the fusions is <a href="http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?db=nucleotide&val=2623981" target="_new">here</a>.

</div>

    <div id="topic11" class="myTopic">
<h2>References</h2>
<p>The work described here has been published in the following articles:
<blockquote>
<li>Huh, WK, Falvo, JV, Gerke, LC, Carroll, AS, Howson, RW, Weissman, JS, and EK O'Shea. Global analysis of protein localization in budding yeast. <i>Nature</i>, <b>425</b>, 686-691 (2003).
<blockquote>
<a href="nature02026_r.pdf" target="_new">&lt;pdf&gt;</a><br>
<a href="nature02026-s1.pdf" target="_new">&lt;supplement1&gt;</a><br>
<a href="nature02026-s2.pdf" target="_new">&lt;supplement2&gt;</a><br>
<a href="nature02026-s3.pdf" target="_new">&lt;supplement3&gt;</a><br>
<a href="nature02026-s4.pdf" target="_new">&lt;supplement4&gt;</a><br>
<a href="nature02026-s5.doc" target="_new">&lt;supplement5&gt;</a>&nbsp;&nbsp;(Microsoft Word document)<br>
</blockquote>
</li>
<li>Ghaemmaghami, S, Huh, WK, Bower, K, Howson, RW, Belle, A, Dephoure, N, O'Shea, EK, and JS Weissman. Global analysis of protein expression in  yeast. <i>Nature</i>, <b>425</b>, 737-741 (2003).
<blockquote>
<a href="nature02046_r.pdf" target="_new">&lt;pdf&gt;</a><br>
<a href="nature02046-s1.doc" target="_new">&lt;supplement1&gt;</a>&nbsp;&nbsp;(Microsoft Word document)<br>
<a href="nature02046-s2.xls" target="_new">&lt;supplement2&gt;</a>&nbsp;&nbsp;(Microsoft Excel document)<br>
</blockquote>
  </li>
<li>Howson, RW, Huh, WK, Ghaemmaghami, S, Falvo, JV, Bower, K, Belle, A, Dephoure N, Wykoff, DD, Weissman, JS, and EK O'Shea. Construction, verification and experimental use of two epitope-tagged collections of budding yeast strains. <i>Comparative and Functional Genomics</i>, <b>6</b>, 2-16 (2005).
<blockquote>
<a href="HowsonCFG2005.pdf" target="_new">&lt;pdf&gt;</a><br>
<a href="http://www.interscience.wiley.com/jpages/1531-6912/suppmat/2005/6/v6.howson.html" target="_new">&lt;supplements&gt;</a> (Wiley Interscience)<br>
</blockquote>
  </li>
</blockquote>
    </div>

    <div id="topic12" class="myTopic">
<h2>Database</h2>
<p>The yeastgfp database and its web interface was designed and built by Luke Gerke, Adam Carroll, and Felix Lam.
<p>The database is maintained by SGD.
    </div>

    <div id="topic13" class="myTopic">
<h2>Comments</h2>
<p>If you would like to make a comment about a specific localization presented here, view the ORF in question using the graphical ORF display.
<p>Click on the "add" link to open a comment submission window.  Fill in the form and click submit.  All fields excluding the PubMed ID field are required.
<p>Your comment will be curated and activated if it will be useful to those using the site. Comments that reference published data (i.e. include a PubMed ID) will be more likely to be activated.
<p>View the comments of others by clicking on the "view" link that is displayed when a comment is available for that ORF. An example: <a href="getOrf.php?orf=YPL031C" target="_new">Pho85</a>.
<p>General comments about the yeastgfp web site should be emailed to <a href="mailto:sgd-helpdesk@lists.stanford.edu">sgd-helpdesk@lists.stanford.edu</a>
    </div>
    
    <div id="topic14" class="myTopic">
<h2>Contact</h2>
<p>Requests for TAP-tagged strains should be directed to <a href="http://www.openbiosystems.com" target="_blank">Open Biosystems</a>.
<p>Requests for GFP-tagged strains should be directed to <a href="http://clones.invitrogen.com/cloneinfo.php?clone=yeastgfp" target="_blank">Invitrogen</a>.
<p>Requests for RFP-tagged strains should be directed to <a href="mailto:falvo@mcb.harvard.edu">James Falvo</a>.
<p>Comments or questions about the data presented here, the database or its interface should be directed to <a href="mailto:sgd-helpdesk@lists.stanford.edu">SGD</a>.
<p>Other requests for information or reagents should be directed to <a href="http://labs.mcb.harvard.edu/o'shea/" target="_blank">Erin O'Shea</a> or <a href="http://www.ucsf.edu/jswlab" target="_blank">Jonathan Weissman</a>.
  </div>

    <div id="topic15" class="myTopic">
<h2>Links</h2>
<p>If you want to have a link on your web site to information about a specific gene on our web site, please use this URL, indicating your favorite ORF:
<p><a href="http://yeastgfp.yeastgenome.org/getOrf.php?orf=YAL001C" target="_new">http://yeastgfp.yeastgenome.org/getOrf.php?orf=YAL001C</a>
<p>Organizations that link to us this way:
<br><a href="http://yeastgfp.yeastgenome.org" target="_new">Saccharomyces Genome Database</a>
<br><a href="http://www.expasy.org" target="_new">ExPASy</a>
<br><a href="http://www.germonline.org" target="_new">GermOnline</a>

  </div>

    <div id="topic16" class="myTopic">
<h2>Oligos Used for Strain Construction</h2>
<p>You can download the sequences for the entire collection <a href="yeastGFPOligoSequence.txt" target="_new">&lt; here &gt;</a> (approx. 1 MB).
<p>You can also retrieve the sequences for a specific gene using the download feature in the Advanced Query.

    </div>


<div id="topic17" class="myTopic">
<h2>Downloading All the Full-Field Images</h2>

<p>Images are available as archives by chromosome:
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
<p>Names of the files in the archives are structured as follows:
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


    </div>

        
    
  </div>

  </div>

</body>
</html>
