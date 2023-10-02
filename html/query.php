<?php
require("locInclude.php");
require("$include_dir/include.php");
//require("$include_dir/secure.php");
require("$include_dir/projects_inc.php");
?>

<html>

<?php

print "<head>";

$frame = FALSE;

dumpStyleForHeader();
dumpStyleForPopover();
dumpFunctionForPreload();
dumpExampleWindowJsFunctions();

print "<link rel=\"stylesheet\" href=\"formatQuery.css\">";
print "<meta name=\"robots\" content=\"index,nofollow\">";

print "</head>";

//pass($priv['User']);

// JAVASCRIPT FUNCTIONALITY REQUIRED FOR GRAPHIC ORF DISPLAY
dumpBodyForJsIcons();


function locToLocMap($in) {
  return $in;
}

class Criterion {

  var $mapIDColName;
  var $mapTableName;
  var $textDescription;
  var $operatorText;
  var $RHSColName;
  var $RHSHTML;
  var $fundamentalTableName;
  var $fundamentalColName;
  var $conversionToLocCallback;
  var $conversionToFundTableCallback;
  var $mapToFundIDColName;
  
  function Criterion($a, $b, $c, $d, $e, $f, $g, $h, $i, $j) {
    $this->mapIDColName = $a;
    $this->tableName = $b;
    $this->textDescription = $c;
    $this->operatorText = $d;
    $this->RHSColName = $e;
    $this->RHSHTML = $f;
    $this->conversionToLocCallback = $g;
    $this->fundamentalTableName = $h;
    $this->fundamentalColName = $i;
    $this->conversionToFundTableCallback = $j;
  }

  function printMe() {
    //    print("<table> <tr>\n");
    print("<tr>\n");
    print("<td> $this->textDescription </td>\n");
    print("<td> $this->operatorText </td>\n");
    print("<td> ".$this->RHSHTML." </td>\n");
    print("</tr>\n");
    //    print("</tr> </table>\n");
  }

  function printStart() {
    print("<table>\n");
  }

  function printEnd() {
    print("</table>\n");
  }

  function getSelectName() {
    return $this->mapIDColName.$this->tableName;
  }

  function getResults($val) {
    $fundCallback = $this->conversionToFundTableCallback;
    $func = $this->conversionToLocCallback;
    
    /* FOR SOME WE NEED SPECIALIZED MAPPING */
    // ONLY FOR orfid  -- so tweak it to handle an array of arrays.

    if($fundCallback != "") {

      // orf fundcallback returns 3 arrays: search, alias, notfound
      $list = $fundCallback($val);

      // convert the first to locids
      $retList[] = $func($list[0]);

      // just return the others
      if (sizeof($list) > 1) {
            $retList[] = $list[1];// || "";
      	    $retList[] = $list[2]; // || "";
      }
    } else {

      /* FOR OTHERS WE USE THE STANDARD MECHANISM */
      $sql = "SELECT $this->fundamentalColName, $this->mapIDColName from $this->fundamentalTableName where $this->mapIDColName='$val'";
      $res = dbquery($sql);
      $list = makeArrayFromResColumn($res, $this->fundamentalColName);
      $retList = $func($list);
    }
    
    return $retList;
  }


  
}

$criterionList = array();

/* SUBCELL */
$mapIDColName = "subcellid";
$mapTableName = "subcell";
$textDescription = "Subcellular Localization";
$operatorText = "=";
$RHSColName = "subcellname";
$fundamentalTableName = "localization";
$fundamentalColName = "localizeid";
$conversionCallback = "locToLocMap";
$conversionToFundTableCallback = "";

$selectName = $mapIDColName.$mapTableName;
$sql = "SELECT * FROM $mapTableName";
$res = dbquery($sql);
$assoc = makeAssocArrayFromResColumns($res, $mapIDColName, $RHSColName, false, false);
$HTML = makeSelectFromAssocArrayWithStar($assoc, $selectName, returnPostVar($selectName));
$c = new Criterion($mapIDColName, $mapTableName, $textDescription, $operatorText, $RHSColName, $HTML, $conversionCallback, $fundamentalTableName, $fundamentalColName, $conversionToFundTableCallback);
$criterionList[] = $c;

/* ORFNUMBER */
$mapIDColName = "orfid";
$mapTableName = "orfs";
$textDescription = "Systematic or Common Name";
$operatorText = "=";
$RHSColName = "orfnumber";
$fundamentalTableName = "orfs";
$fundamentalColName = "orfid";
$conversionCallback = "orfidListToLocMap";
$conversionToFundTableCallback = "convertOrfnumberOrOrfnameListToOrfidList";

$selectName = $mapIDColName.$mapTableName;
$HTML = makeTextBoxWithStar($selectName, returnPostVar($selectName));

$c = new Criterion($mapIDColName, $mapTableName, $textDescription, $operatorText, $RHSColName, $HTML, $conversionCallback, $fundamentalTableName, $fundamentalColName, $conversionToFundTableCallback);
$criterionList[] = $c;

/* CELL CYCLE */
$mapIDColName = "cellcycleid";
$mapTableName = "cellcycle";
$textDescription = "Cell Cycle Phase";
$operatorText = "=";
$RHSColName = "phase";
$fundamentalTableName = "localization";
$fundamentalColName = "localizeid";
$conversionCallback = "locToLocMap";
$conversionToFundTableCallback = "";

$selectName = $mapIDColName.$mapTableName;
$sql = "SELECT * FROM $mapTableName";
$res = dbquery($sql);
$assoc = makeAssocArrayFromResColumns($res, $mapIDColName, $RHSColName, false, false);
$HTML = makeSelectFromAssocArrayWithStar($assoc, $selectName, returnPostVar($selectName));
$c = new Criterion($mapIDColName, $mapTableName, $textDescription, $operatorText, $RHSColName, $HTML, $conversionCallback, $fundamentalTableName, $fundamentalColName, $conversionToFundTableCallback);
$criterionList[] = $c;


/* USERID */
/*
$mapIDColName = "userid";

$mapTableName = "users";
$textDescription = "Scorer";
$operatorText = "=";
$RHSColName = "realname";
$fundamentalTableName = "localization";
$fundamentalColName = "localizeid";
$conversionCallback = "locToLocMap";
$conversionToFundTableCallback = "";

$selectName = $mapIDColName.$mapTableName;
$sql = "SELECT * FROM $mapTableName";
$res = dbquery($sql);
$assoc = makeAssocArrayFromResColumns($res, $mapIDColName, $RHSColName, false, false);
//printWB($selectName.$_POST[$selectName]);
$HTML = makeSelectFromAssocArrayWithStar($assoc, $selectName, returnPostVar($selectName));
$c = new Criterion($mapIDColName, $mapTableName, $textDescription, $operatorText, $RHSColName, $HTML, $conversionCallback, $fundamentalTableName, $fundamentalColName, $conversionToFundTableCallback);
$criterionList[] = $c;
*/

/* CELL MORPHOLOGY */
$mapIDColName = "cellmorphologyid";
$mapTableName = "cellmorphology";
$textDescription = "Cell Morphology";
$operatorText = "=";
$RHSColName = "cellmorphology";
$fundamentalTableName = "localization";
$fundamentalColName = "localizeid";
$conversionCallback = "locToLocMap";
$conversionToFundTableCallback = "";

$selectName = $mapIDColName.$mapTableName;
$sql = "SELECT * FROM $mapTableName";
$res = dbquery($sql);
$assoc = makeAssocArrayFromResColumns($res, $mapIDColName, $RHSColName, false, false);
//printWB($selectName.$_POST[$selectName]);
$HTML = makeSelectFromAssocArrayWithStar($assoc, $selectName, returnPostVar($selectName));
$c = new Criterion($mapIDColName, $mapTableName, $textDescription, $operatorText, $RHSColName, $HTML, $conversionCallback, $fundamentalTableName, $fundamentalColName, $conversionToFundTableCallback);
$criterionList[] = $c;

/* SUBCELLULAR HOMOGENEITY */
$mapIDColName = "subcellhomogeneityid";
$mapTableName = "subcellhomogeneity";
$textDescription = "Subcellular Homogeneity";
$operatorText = "=";
$RHSColName = "subcellhomogeneity";
$fundamentalTableName = "localization";
$fundamentalColName = "localizeid";
$conversionCallback = "locToLocMap";
$conversionToFundTableCallback = "";

$selectName = $mapIDColName.$mapTableName;
$sql = "SELECT * FROM $mapTableName";
$res = dbquery($sql);
$assoc = makeAssocArrayFromResColumns($res, $mapIDColName, $RHSColName, false, false);
//printWB($selectName.$_POST[$selectName]);
$HTML = makeSelectFromAssocArrayWithStar($assoc, $selectName, returnPostVar($selectName));
$c = new Criterion($mapIDColName, $mapTableName, $textDescription, $operatorText, $RHSColName, $HTML, $conversionCallback, $fundamentalTableName, $fundamentalColName, $conversionToFundTableCallback);
$criterionList[] = $c;


/* CELL BRIGHTNESS */
$mapIDColName = "cellbrightnessid";
$mapTableName = "cellbrightness";
$textDescription = "Relative Cell Brightness";
$operatorText = "=";
$RHSColName = "cellbrightness";
$fundamentalTableName = "localization";
$fundamentalColName = "localizeid";
$conversionCallback = "locToLocMap";
$conversionToFundTableCallback = "";

$selectName = $mapIDColName.$mapTableName;
$sql = "SELECT * FROM $mapTableName";
$res = dbquery($sql);
$assoc = makeAssocArrayFromResColumns($res, $mapIDColName, $RHSColName, false, false);
$HTML = makeSelectFromAssocArrayWithStar($assoc, $selectName, returnPostVar($selectName));
$c = new Criterion($mapIDColName, $mapTableName, $textDescription, $operatorText, $RHSColName, $HTML, $conversionCallback, $fundamentalTableName, $fundamentalColName, $conversionToFundTableCallback);
$criterionList[] = $c;

/* LIBRARY */
/*
$mapIDColName = "library";
$mapTableName = "strains";
$textDescription = "Library";
$operatorText = "=";
$RHSColName = "library";
$fundamentalTableName = "strains";
$fundamentalColName = "strainid";
$conversionCallback = "strainidListToLocMap";
$conversionToFundTableCallback = "";

$selectName = $mapIDColName.$mapTableName;
$sql = "SELECT * FROM $mapTableName";
$res = dbquery($sql);
$assoc = makeAssocArrayFromResColumns($res, $mapIDColName, $RHSColName, false, false);
$HTML = makeSelectFromAssocArrayWithStar($assoc, $selectName, returnPostVar($selectName));
$c = new Criterion($mapIDColName, $mapTableName, $textDescription, $operatorText, $RHSColName, $HTML, $conversionCallback, $fundamentalTableName, $fundamentalColName, $conversionToFundTableCallback);
$criterionList[] = $c;
*/
/*****DEBUG ******/
$self = $_SERVER['PHP_SELF'];
//$self = "showPostVars.php";

/* LETS PUT THIS ALL IN A LAYER SO WE CAN MOVE IT ABOUT */
$distDToFrame         = 10;	
$distRToFrame         = 100;
$frameHeight          = 350;
$frameWidth           = 430;

print("<p class='layerQuery'>");
print("Search Criteria");
print("</p>");


/* CENTER EVERYTHING */
//print("<div align='center'>");

/* START THE FORM */
print("<form name=\"querySubmitForm\" method=post action='$self'>");

/* PRINT ALL THE SELECT OPTIONS */
$c->printStart();
for($a=0; $a<count($criterionList); $a++) {
    $c = &$criterionList[$a];
    $c->printMe();
}
$c->printEnd();

print("<div align='left'>\n");
print("<p class='layerQuery'>\n");
print("Display Options\n");
print("</p>\n");
//print("<div align='center'>\n");

$locPics = "";
$orfTable = "";
$orfText = "";
$orfPics = "";

// MAINTAIN CHECKED CHECKBOXES ON SUBMIT
if(isset($_POST['locPics'])) {
  $locPics = " CHECKED ";
}

if(isset($_POST['orfPics'])) {
  $orfPics = " CHECKED ";
}

// ALSO CHECK orfPics AS DEFAULT QUERY DISPLAY ON FIRST LOAD OF PAGE
if(!isset($_POST['submit_x'])) {
  $orfPics = " CHECKED ";
}

if(isset($_POST['orfTable'])) {
  $orfTable = " CHECKED ";
}

if(isset($_POST['orfText'])) {
  $orfText = " CHECKED ";
}

if(isset($_POST['download'])) {
  $download = " CHECKED ";
}

if(isset($_POST['downloadTable'])) {
  $downloadTable = " CHECKED ";
}

if(isset($_POST['downloadSummary'])) {
  $downloadSummary = " CHECKED ";
}
if(isset($_POST['downloadSortByOrfnumber'])) {
  $downloadSortByOrfnumber = " CHECKED ";
}
if(isset($_POST['downloadSortByOrfname'])) {
  $downloadSortByOrfname = " CHECKED ";
}
if(isset($_POST['downloadOrfid'])) {
  $downloadOrfid = " CHECKED ";
}
if(isset($_POST['downloadTapTagged'])) {
  $downloadTapTagged = " CHECKED ";
}
if(isset($_POST['downloadTapAbundance'])) {
  $downloadTapAbundance = " CHECKED ";
}
if(isset($_POST['downloadTapError'])) {
  $downloadTapError = " CHECKED ";
}
if(isset($_POST['downloadOligoSeq'])) {
  $downloadOligoSeq = " CHECKED ";
}
if(isset($_POST['downloadCheckOligoSeq'])) {
  $downloadCheckOligoSeq = " CHECKED ";
}


$S1 = "";
$S10 = "";
$S25 = "";
$S50 = "";
$S100 = "";


if(isset($_POST['limitLocs']) && $_POST['limitLocs'] == 1) {
  $S1 = " SELECTED ";
}
if(isset($_POST['limitLocs']) && $_POST['limitLocs'] == 10) {
  $S10 = " SELECTED ";
}
if(isset($_POST['limitLocs']) && $_POST['limitLocs'] == 25) {
  $S25 = " SELECTED ";
}
if(isset($_POST['limitLocs']) && $_POST['limitLocs'] == 50) {
  $S50 = " SELECTED ";
}
if(isset($_POST['limitLocs']) && $_POST['limitLocs'] == 100) {
  $S100 = " SELECTED ";
}

$O1 = "";
$O10 = "";
$O25 = "";
$O50 = "";
$O100 = "";




if(isset($_POST['limitORFs']) && $_POST['limitORFs'] == 1) {
  $O1 = " SELECTED ";
}
if(isset($_POST['limitORFs']) && $_POST['limitORFs'] == 10) {
  $O10 = " SELECTED ";
}
if(isset($_POST['limitORFs']) && $_POST['limitORFs'] == 25) {
  $O25 = " SELECTED ";
}
if(isset($_POST['limitORFs']) && $_POST['limitORFs'] == 50) {
  $O50 = " SELECTED ";
}
if(isset($_POST['limitORFs']) && $_POST['limitORFs'] == 100) {
  $O100 = " SELECTED ";
}


print "<table border=0 cellspacing=0 cellpadding=2>";
print "<tr>";
print "<td>";
print "<p>";
print("<input type='checkbox' name='orfPics' value='classical' $orfPics >Show <b>graphical scoring summary</b> for up to ");
print("<select name='limitORFs'><option value=1 $O1>1<option value=10 $O10>10<option value=25 $O25>25<option value=50 $O50>50<option value=100 $O100>100</select>");
print(" ORFs\n");
print "</td>";
print "<td>";
print("&nbsp;&nbsp;<a href=\"javascript: openOrfExample();\"> &lt;example&gt; </a><br>\n");
print "</td>";
print "</tr>";
print "<tr>";
print "<td>";
print("<input type='checkbox' name='locPics' value='classical' $locPics >Show up to ");
print("<select name='limitLocs'><option value=1 $S1>1<option value=10 $S10>10<option value=25 $S25>25<option value=50 $S50>50<option value=100 $S100>100</select>");
print(" individual <b>localization clips</b>\n");
print "</td>";
print "<td>";
print("&nbsp;&nbsp;<a href=\"javascript: openLocExample();\"> &lt;example&gt; </a><br>\n");
print "</td>";
print "</tr>";
print "<tr>";
print "<td>";
print("<input type='checkbox' name='orfTable' value='classical' $orfTable >Show <b>ORF Summary Table</b><br>\n");
print "</td>";
print "<td>";
print "&nbsp;&nbsp;(use this to display more than 100 results)";
print "</td>";
print "</tr>";
// print "<tr>";
// print "<td>";
// print("<input type='checkbox' name='download' value='classical' $download ><b>Download</b> the selected dataset as a tab-delimited file<br>// print "</td>";
// print "<td>";
// print "&nbsp;&nbsp;(will take a minute or two to do every localization)";
// print "</td>";
// print "</tr>";
// new row for download options
// print "<tr>";
// print "<td colspan=2>";
// print "<table border=0 cellspacing=0 cellpadding=2>";
// print "<tr>";
// print "<td>";
// print "&nbsp;&nbsp;";
// print "</td>";
// print "<td>";
// print "&nbsp;&nbsp;";
// print "</td>";
// print "<td>";
// print "<b>include: </b>";
// print "</td>";
// print "<td>";
// print "<input type='checkbox' name='downloadSummary' value='classical' $downloadSummary >localization summary\n";
// print "</td>";
// print "<td>";
// print "<input type='checkbox' name='downloadTable' value='classical' $downloadTable >localization table\n";
// print "</td>";
// print "<td>";
// print "<input type='checkbox' name='downloadOrfid' value='classical' $downloadOrfid >internal orfid\n";
// print "</td>";
// print "</tr>";
// print "<tr>";
// print "<td>";
// print "&nbsp;&nbsp;";
// print "</td>";
// print "<td>";
// print "&nbsp;&nbsp;";
// print "</td>";
// print "<td>";
//print "&nbsp;&nbsp;";
//print "</td>";
//print "<td>";
//print "<input type='checkbox' name='downloadTapTagged' value='classical' $downloadTapTagged >TAP tag success\n";
//print "</td>";
//print "<td>";
//print "<input type='checkbox' name='downloadTapAbundance' value='classical' $downloadTapAbundance >abundance (molecules/cell)\n";
//print "</td>";
//print "<td>";
//print "<input type='checkbox' name='downloadTapError' value='classical' $downloadTapError >abundance error\n";
//print "</td>";
//print "</tr>";
//print "<tr>";
//print "<td>";
//print "&nbsp;&nbsp;";
// print "</td>";
// print "<td>";
// print "&nbsp;&nbsp;";
// print "</td>";
// print "<td>";
// print "&nbsp;&nbsp;";
// print "</td>";
// print "<td>";
// print "<input type='checkbox' name='downloadOligoSeq' value='classical' $downloadOligoSeq >PCR oligo sequences\n";
// print "</td>";
// print "<td>";
// print "<input type='checkbox' name='downloadCheckOligoSeq' value='classical' $downloadCheckOligoSeq >check oligo sequence\n";
// print "</td>";
// print "<td>";
// print "&nbsp;&nbsp;";
// print "</td>";
// print "</tr>";print "<tr>";
// print "<td>";
// print "&nbsp;&nbsp;";
// print "</td>";
// print "<td>";
// print "&nbsp;&nbsp;";
// print "</td>";
// print "<td>";
// print "<b>sort by: </b>";
// print "</td>";
// print "<td>";
// print "<input type='checkbox' name='downloadSortByOrfnumber' value='classical' $downloadSortByOrfnumber > orf number\n";
// print "</td>";
// print "<td>";
// print "<input type='checkbox' name='downloadSortByOrfname' value='classical' $downloadSortByOrfname > gene name\n";
// print "</td>";
// print "</tr>";
// print "</table>";
// print "</td>";
// print "</tr>";
print "</table>";
print "<br>";


print("<input type=image src=\"arrowSubmit.gif\" name='submit'>\n");

/* END THE FORM */
print("</form>\n");


/* DO THE JOB WE CAME HERE TO DO */
if(isset($_POST['submit_x'])) {
  print("<div align='left'>");
  
  print("<p class='layerQuery'>");
  print("Search Results");
  print("</p>");
  
  $locList = getAllValidLocalizations();
  for($a=0; $a<count($criterionList); $a++) {
    $c = &$criterionList[$a];
    //        print $c->getSelectName();
    if($_POST[$c->getSelectName()] != "*") {
      
      $newList = $c->getResults($_POST[$c->getSelectName()]);

      // check for alias issues and/or not found issues for in searched gene names
      if (is_array($newList[0])) {
      	 if (sizeof($newList) > 1) {
	 	$orfsWithAliasIssues = $newList[1];
		$orfsNotFound = $newList[2];
	}
	$newList = $newList[0];
      }    

      $locList = array_intersect($locList, $newList);
      
    }
  }

  // HANDLE ORFS WITH ALIAS ISSUES -- CURRENTLY JUST A WARNING
  if (isset($orfsWithAliasIssues)) {
    $radio = FALSE;
    print "<h2>Please note -- the following search terms are ambiguous...</h2>";
    print "<ul>";
    foreach ($orfsWithAliasIssues as $orf) {
      print "<lh>\n";
      makeAliasTable($orf,$radio);
    }
    print "</ul>";
  }

  // WARN OF ORFS NOT FOUND
  if (isset($orfsNotFound)) {
    print "<h2>Please note -- the following search terms could not be found...</h2>\n";
    print "<ul>";
    foreach ($orfsNotFound as $orf) {
      print "<li> $orf\n";
    }
    print "</ul>";
  }
  
  if(count($locList) == 0) {
    printWB("<p class='locTableHeaderLabel'><h3>No Localizations in the Database Matched Your Criteria</h3></p>");
    
    exit();
  }

  $preMessage = "";
  $postMessage = "";

  // USED FOR?  CAN'T FIND orfidorfs ANYWHERE IN php FILES....
  //  if($_POST['orfidorfs'] != "*") {
  //    printWB($_POST['orfidorfs']);
  //    $orfidList = convertOrfnumberOrOrfnameListToOrfidList($_POST['orfidorfs']);
  //    print_r($orfidList);
  
  //  }
  

  // DISPLAY THE GRAPHIC LOCS
  
  if($locPics != "") {
    if(isset($_POST['limitLocs'])) {
      $limit = $_POST['limitLocs'];;
    } else {
      $limit = 10;
    }
    
    $smallerList = array();
    $remainingList = array();
    $myCount = 0;
    foreach($locList as $loc) {
      if($myCount < $limit) {
	$smallerList[] = $loc;
      } else {
	$remainingList[] = $loc;
      }
      $myCount++;
    }
    
    print "<table border=0><tr><td><h1>&gt;&gt; Displaying up to $limit localizations graphically... </h1></td><td>&nbsp;</td>";
    print "</tr></table>";
    
    print(buildLocalizationDisplayHTMLFromLocalizationList($smallerList));
    
  }
  
  // DISPLAY THE GRAPHIC ORF SUMMARY

  if($orfPics && $orfPics != "") {
    if(isset($_POST['limitORFs'])) {
      $limit = $_POST['limitORFs'];;
    } else {
      $limit = 10;
    }

    print "<br><table border=0><tr><td><h1>&gt;&gt; Displaying up to $limit ORFs graphically... </h1></td><td>&nbsp;</td>";
    print "<td valign=top><a href=\"javascript: openLegend();\">&nbsp;&nbsp;&lt;legend&gt;&nbsp;</a></td>";
    print "<td valign=top><a href=\"javascript: openAbundance();\">&nbsp;&nbsp;&lt;abundance description&gt;&nbsp;</a></td>";
    print "</tr></table>";

    $orfList = array();
    foreach($locList as $loc) {
      $thisOrf = getOrfidFromLocid($loc);
      if(!in_array($thisOrf, $orfList)) {
	//	printWB($thisOrf);
	$orfList[] = $thisOrf;
      }
      if(count($orfList) >= $limit) {
	break;
      }
    }

    displayBestLocs($orfList);
    


  }
  
  
  // NEW JAVA DRIVEN TEXT FILE DOWNLOAD
  $download = "";
  // NOTE DOWNLOAD PHP CODE HAS BEEN DISABLED.
  if ($download != "") {

    // make an input file of orfnumbers from the to search list
    $inFilePath = "downloads/input";
    $inFilePath .= time();
    $inFilePath .= ".txt";

    $inFile = basename($inFilePath);
    
    if (!$fpIn = fopen($inFilePath, 'w+')) {
      print "cannot open file ".$inFilePath;
      exit;
    }

    // build orfStr from search results....
    $orfStr = "";
    $delim = " ";
    $quote = "";

    foreach ($locList as $loc) {
      $orfId = getOrfidFromLocid($loc);
      $orfNumber = convertOrfidToOrfnumberOrOrfname($orfId);
      $orfArray[] = $orfNumber;
    }
    $orfArray = array_unique($orfArray);
    $orfStr = arrayToList($orfArray,$delim,$quote);

    // write the orfStr to the input file
    if (!fwrite($fpIn,$orfStr)) {
      print "failed to write";
      exit;
    }
        
    // make a output name
    $outFile = "downloads/download";
    $outFile .= time();
    $outFile .= ".txt";

    // default values for options without checkboxes
    
    $opt = "";
    $opt += "0x001"; // only visualized orfs
    $opt += "0x008"; // orfnumber
    $opt += "0x010"; // gene name   
    $opt += "0x080"; // order by visualized

    // get runtime options for -opt from checkboxes
    
    // sort by orfnumber
    if($_POST['downloadSortByOrfnumber'] != "") {
      $opt = $opt + "0x002";
    }
    // sort by orf name (gene name)
    if($_POST['downloadSortByOrfname'] != "") {
      $opt = $opt + "0x800";
    }
    // show orfid (internal reference)
    if($_POST['downloadOrfid'] != "") {
      $opt = $opt + "0x004";
    }
    // localization table    
    if($_POST['downloadTable'] != "") {
      $opt = $opt + "0x020";
    }
    // localization summary
    if($_POST['downloadSummary'] != "") {
      $opt = $opt + "0x040";
    }
    // tap tagged?
    if($_POST['downloadTapTagged'] != "") {
      $opt = $opt + "0x100";
    }
    // tap abundance
    if($_POST['downloadTapAbundance'] != "") {
      $opt = $opt + "0x200";
    }
    // tap error
    if($_POST['downloadTapError'] != "") {
      $opt = $opt + "0x400";
    }
    // oligo sequence
    if($_POST['downloadOligoSeq'] != "") {
      $opt = $opt + "0x1000";
    }
    // check oligo sequence
    if($_POST['downloadCheckOligoSeq'] != "") {
      $opt = $opt + "0x2000";
    }

    // run the program
    print ("<h1>&gt;&gt; Writing query results to file $outFile</h1>");
    print ("<p>working . . . ");
    
    // $cmd = "/usr/java/j2re1.4.1_01/bin/java -classpath /home/adam/html/ downloadData -opt ".$opt." -geneFile ".$inFilePath." -outFile ".$outFile." &> err";
  
    $cmd = "/usr/bin/java -classpath /share/abbey/www-data_gfp/html/ downloadData -opt ".$opt." -geneFile ".$inFilePath." -outFile ".$outFile." &> err";

    exec($cmd, $output, $error);
//	if ($error) print "OS ERROR: $error<br>"; 
//	var_dump($output)."<br>";
       print "<p>".$cmd;
    
    fclose($fpIn);

    print "&nbsp;&nbsp;download file <a href=\"".$outFile."\">here</a>";
    
  }
   
  
  /* THE PALM.COM ORFTABLE */
  if($orfTable != "") {
    
    $uniqueOrfs = array();
    foreach($locList as $locs) {
      $uniqueOrfs[] = locToOrfnumberMap($locs);
    }
    
    $uniqueOrfs = array_unique($uniqueOrfs);  
    
    sort($uniqueOrfs);
    
    $myOrfidList = new OrfList();
    
    foreach($uniqueOrfs as $orf) {
      $orfid = getOneToOneMatch("orfs", "orfnumber", "'".$orf."'", "orfid");
      $myOrfidList->addOrfid($orfid);
    }
    
    $postMessage .= "<h1>&gt;&gt; The following orfs have localizations that matched your criteria.</h1>";
    $postMessage .= "<p>Use the checkboxes at left and display button below to see graphical summaries for each selected ORF.";    
    $postMessage .= "<br><br><a href=\"javascript: openAbundance();\">&nbsp;&nbsp;&lt;abundance description&gt;&nbsp;</a>";

    print($postMessage);
    
    print($myOrfidList->buildOrfInfoTable());
    
    printWB(count($uniqueOrfs)." orfs were returned");
    
  }
  
  
}

printfooter();

?>