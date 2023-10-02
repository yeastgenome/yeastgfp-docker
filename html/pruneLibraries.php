<?
require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/secure.php");
require("$include_dir/projects_inc.php");
require("$include_dir/javascript.php");

pass($priv['Superuser']);

/* SO IT'S GOING TO GO LIKE THIS...

BEFORE WE START THE PROCESS, DELETE FROM BESTLOCS

IF WE'RE ARRIVING HERE FROM HERE, PROCESS THE SELECTIONS
IF IT'S FINAL, ALSO ASSIGN IT TO EITHER COLOC OR INITIAL.  

FOREACH ORF
FOREACH SUBCELL
FORACH PHASE (FINAL, COLOC, INIT)

IF THERE'S ALREADY AN ENTRY IN THE BESTLOCS TABLE BY THIS DESCRIPTION, CONTINUE;

IF 0 LOCALIZATIONS MATCH THIS DESCRIPTION, CONTINUE;

NOW, IF WE'RE IN FINAL AND THERE ARE ANY COLOCS, ONLY PRESENT THOSE FOR THE FINAL OPTIONS.  MAKE A LIST OF EITHER ALL COLOCS OR ALL NON, AND LOOP THROUGH EACH. MAKE A TABLE THAT HAS HORIZONTALLY ALL THE LOCALIZATIONS ON VERTICALLY ALL THE EXPOSURE OPTIONS
*/

$orfList = getAllOrfs();
$subcellList = getOrderedSubcellList();
$phaseList = array();
$phaseList[] = "F";
$phaseList[] = "C";
$phaseList[] = "I";
foreach($orfList as $orfid) {
  foreach($subcellList as $subcellid) {
    foreach($phase as $phase) {
      printWB("working on" + $orfid);
    }
  }
}




$resGetExcludedSetList = dbquery($sqlGetExcludedSetList);
$excludedList = makeArrayFromResColumn($resGetExcludedSetList, "setid") ;



dumpJavascriptOpen();
dumpMM_preloadImages();
dumpMM_findObj();
dumpMM_swapImage();
dumpJavascriptClose();

// NEEDED BOTH FOR PROCESSING LAST ONE AND BUILDING THIS ONE
$levelsGFP = array ("65535","41400","21120");
$levelsRFP = array ("65535","41400","21120");


/* IF THIS PAGE WAS TARGETED BY SELF WITH A REQUST, DO THAT */

if(isset($_POST['buttonSubmitPruneSelections'])) {
  $msg = processPruneSelections($levelsGFP, $levelsRFP);
}

if(isset($_POST['buttonSubmitSkipRequest'])) {
  $msg = processSkipRequest();
}
if(isset($msg)) { print $msg; }

?>
<link href="prune.css" rel="stylesheet" type="text/css">
<?

/******** GET THE SET WE'RE INTERESTED IN ****************/
/* IF WE'RE IN THE MODE WHERE WE'RE DOING THE SKIPPED ORFS */
if(isset($_POST['buttonSkippedOrfsSubmit'])) {
  $conditions = "WHERE sets.prune_complete = 'F' AND sets.prune_skipped = 'T'";
} elseif(isset($_POST['buttonUnprunedOrfsSubmit'])) { /* IF WE'RE IN THE MODE WHERE WE TAKE THE FIRST ONE */
  $conditions = "WHERE sets.prune_complete = 'F'
                 AND sets.prune_skipped = 'F'";
} else {
  assert(0);
}

$sqlGetExcludedSetList = "select * from sets where prune_complete='T'";
$resGetExcludedSetList = dbquery($sqlGetExcludedSetList);
$excludedList = makeArrayFromResColumn($resGetExcludedSetList, "setid") ;

/* START THE TRANSACTION AND GET THE FIRST UNLOCKED SET THAT MEETS SPEC */
innoDbStartTransaction();
$currentSet = checkAndSetLocked("sets", "setid", $conditions, "locked", $excludedList);
innoDbEndTransaction();

//$currentSet = 25341;


if($currentSet == null) {
  $msg = "No unpruned images exist in that class (eg on that plate, unpruned, etc).<br>";
  $msg .= "<A HREF=\"pruneSetup.php\">Click here to return to prune setup.</A>";
  centermsg($msg);
  exit;
}

//printWB($currentSet);
$strainForSet = getOneToOneMatch("sets","setid",$currentSet,"strainid");

//printWB($strainForSet);

$backgroundForSet = getOneToOneMatch("strains","strainid",$strainForSet,"backgroundid");

assert($backgroundForSet != 1);
$orfOfInterest = getOneToOneMatch("strains", "strainid", $strainForSet, "orfid");
assert(isset($orfOfInterest));



/* GET THE STRAINS THAT MATCH THE ORF THAT WE FOUND */
$sqlGetAllOrfMatches = "SELECT * FROM strains
                        WHERE tag='GFP' AND strains.backgroundid=".$backgroundForSet." and
                        orfid = ".$orfOfInterest;
$resStrainOfInterest = dbquery($sqlGetAllOrfMatches);

$setList = "";
while($strainOfInterest = mysqli_fetch_assoc($resStrainOfInterest)) {
  $sqlGetAllSetsInStrain = "SELECT * FROM sets
                              INNER JOIN strains ON strains.strainid = sets.strainid
                              WHERE sets.strainid = ".$strainOfInterest['strainid'];
  $resGetAllSetsInStrain = dbquery($sqlGetAllSetsInStrain);
  while($setOfInterest = mysqli_fetch_assoc($resGetAllSetsInStrain)) {
    $setList[] = $setOfInterest['setid'];
  }
}

innoDbStartTransaction();  
if(setSynchronizedKeyList("sets", "setid", $setList, "locked", "T") == "T") {
  innoDbEndTransaction();
} else {
  printWB("looks like something went wrong");
  innoDbAbortTransaction();
}

//print_r($setList);


/* DEFINE A CLASS TO HOLD THE INFO FOR BUILDING THE DISPLAY PER SET */
class PaneInfo
{
    var $setId;
    var $strainId;
    var $gfpImagePath;
    var $rfpImagePath;
    var $dicImagePath;
    var $dicThumbPath;
    var $dapiThumbPath;
    var $tableXLoc;
    var $tableYLoc;
    var $formName;
    var $strainName;
    var $strainId;
    var $orfId;
    var $library;
    /* AN ARRAY FOR THE ADJUSTED IMAGES FOR THE SET */
    var $adjustedImages;
    
    function PaneInfo() {
      $this->gfpImagePath = "../img/missing.jpg";
      $this->dicThumbPath = "../img/missing.thumb.jpg";
      $this->dapiThumbPath = "../img/missing.thumb.jpg";
      $this->rfpImagePath = "../img/missing.jpg";
    }



    function printPane() {
      if(!isSetColoc($this->setId)) {
	printWB($this->setId);
	assert(0);
      }
      $a = getDICDirPathForSet($this->setId);
      $this->dicImagePath = $a;
      //      printWB($this->adjustedImages[0]);
      $startLeftImagePath = $this->adjustedImages[0];
      
      print "<p class=\"libLabel\">Lib ".$this->library." set:".$this->setId.")";

      print "<table>";
      /* going to change this section the most */
      print "<tr> <td>";
      print "<img name='image".$this->setId."' src=\"".$startLeftImagePath."\">";
      print "</td><td>";
      print "<img src=\"".$this->dicImagePath."\">";
      print "</td></tr>";
      print "<tr> <td>";
      print "</td><td>";
      print "<input type=\"hidden\" name=\"pruneChoice".$this->setId."\" value=\"marker\">";

      print "</td> </tr>";
      print "</table>";
      print "<select name=\"pruneChoice".$this->setId."\" size = \"3\">";
      print "<option value=\"score\">Score</option>";
      print "<option value=\"noscore\">Don't Score</option>";
      print "<option value=\"nogfp\">GFP Not Visible</option>";
      print "</select>";

      $im = "";
      $first = true;
      foreach ($this->adjustedImages as $key => $name) {
	$im .= "<OPTION value=".$key;
	if($first) {
	  //	  $im .= " SELECTED ";
	  $first = false;
	}
	$im .= ">".$name."\n";
      }

      dumpJavascriptOpen();
      $imgArry = arrayToCommaList($this->adjustedImages,"'");
      printf("var variant".$this->setId."ImgPaths = new Array(".$imgArry.");");
      dumpJavascriptClose();

      print "<input type=\"hidden\" name=\"variant".$this->setId."\" value=\"marker\">";
      print "<select name=\"variant".$this->setId."\" value=\"marker\" size = \"10\" OnChange=\"MM_swapImage('image".$this->setId."','', variant".$this->setId."ImgPaths[this.selectedIndex],1)\" >";
      print $im;
      
      print "</select>";




      //      print "hefff";

    }
}

/* KEEP TRACK OF WHICH ORF WE'RE DEALING WITH... */
$orfNum = convertOrfidToOrfnumberOrOrfname($orfOfInterest);
$strainName = getOneToOneMatch("background", "backgroundid", $backgroundForSet,"referencemarker");

/* BUILD THE 2-D ARRAY OF PANEINFOS... */

/* REQUERY AS ABOVE, THIS TIME WITH INTENT TO CHANGE */
$resStrainOfInterest = dbquery($sqlGetAllOrfMatches);

$pastLibraries = array();
// print_r($pastLibraries); 
// print "<br>BOOOOYEAHHHH<br>";
while($strainOfInterest = mysqli_fetch_assoc($resStrainOfInterest)) {
  //  printWB($strainOfInterest['strainid']);
  $sqlGetAllSetsInStrain = "SELECT * FROM sets
                            INNER JOIN strains ON strains.strainid = sets.strainid
                            WHERE sets.strainid = ".$strainOfInterest['strainid'];
  $resGetAllSetsInStrain = dbquery($sqlGetAllSetsInStrain);
  $maxIndexForLibrary = 0;
  while($setOfInterest = mysqli_fetch_assoc($resGetAllSetsInStrain)) {
    $libraryLetter = $setOfInterest['library'];
    $check = array_search($libraryLetter,$pastLibraries);
    if (!is_int($check)) {
      $pastLibraries[] = $libraryLetter;
      $library = array_search($libraryLetter,$pastLibraries);
    } else {
      $library = $check;
    }
	//	printWB($library);
    //	print_r($pastLibraries);
	
	$arry[$library][$maxIndexForLibrary] = new PaneInfo;
	$arry[$library][$maxIndexForLibrary]->setId = $setOfInterest['setid'];
	$arry[$library][$maxIndexForLibrary]->strainId = $setOfInterest['strainid'];
	$arry[$library][$maxIndexForLibrary]->library = $setOfInterest['library'];

	/* put image name constructor here? */

	/* Change this so that it doesn't depend on explicit naming of library letter */

	//	foreach ($pastLibraries as $libraryIndex => $libraryLetter) {
	// }  
	$arry[$library][$maxIndexForLibrary]->tableXLoc = $library;
	
	/*  
	if(1) { // the original run
	  assert ($library == "A" || $library == "B");
	  if ($library == "A") {	
	    $arry[$library][$maxIndexForLibrary]->tableXLoc = 0;
	  }
	  else {
	    $arry[$library][$maxIndexForLibrary]->tableXLoc = 1;
	  }
	} else { // the redos
	  assert ($library == "C" || $library == "D" || $library == "E" || $library == "F");
	  if ($library == "C") {	
	    $arry[$library][$maxIndexForLibrary]->tableXLoc = 0;
	  }
	  elseif($library == "D") {
	    $arry[$library][$maxIndexForLibrary]->tableXLoc = 1;
	  }
	  elseif($library == "E") {
	    $arry[$library][$maxIndexForLibrary]->tableXLoc = 2;
	  }
	  else {
	    $arry[$library][$maxIndexForLibrary]->tableXLoc = 3;
	  }
	}
	*/
//	print "arrylibrary: ".$arry[$library][$maxIndexForLibrary]->tableXLoc."<br>\n";

	$arry[$library][$maxIndexForLibrary]->tableYLoc = $maxIndexForLibrary;


	/* build the list of merged images to get */
	
	$currentSet = $setOfInterest['setid'];
	$mergedList = NULL;
	$buffer = NULL;

	/* keep the number of digits in setid the same */


	$currentSet = sprintf("%05d", $currentSet);
	/*
	if ($currentSet >= 10000) { $buffer =""; }
	else if ($currentSet >= 1000) { $buffer ="0"; }
	else if ($currentSet >= 100) { $buffer ="00"; }
	else if ($currentSet >= 10) { $buffer ="000"; }
	else { $buffer = NULL; }
	*/		
	
	$mergedList = getVariantsNameList($currentSet, $levelsGFP, $levelsRFP);
    //    	print_r($mergedList);
    //	print "<br>";
	/* now pass this list into the object for display.... */
	$arry[$library][$maxIndexForLibrary]->adjustedImages = $mergedList;

	//print $images;
	
	/* UHHH...  MAYBE NOT SO EFFICIENT, BUT EASY */
	/*
	$sqlGetFilePathsStains = "SELECT * FROM images
                                  INNER JOIN stain on stain.stainid = images.stainid
                                  INNER JOIN sets on images.setid = sets.setid
                                  INNER JOIN strains on sets.strainid = strains.strainid
                                  INNER JOIN orfs on strains.orfid = orfs.orfid
                                  WHERE images.setid = ".$setOfInterest['setid'];
	$resGetFilePathsStains = dbquery($sqlGetFilePathsStains);
	*/
	/* MAKE SURE ALL THREE PICS ARE THERE */
	//	print $setOfInterest['setid'];
	//	print mysqli_num_rows($resGetFilePathsStains);
	//	assert(mysqli_num_rows($resGetFilePathsStains) == 3);
	
	/* THIS SHOULD BE REPLACED WITH THE GLOBAL OF THE SAME NATURE */
	// $thumbExt = ".thumb.png";
	
	/* ASSIGN THE PATH MEMBER VARIABLES ACCORDING TO STAIN */
	// while($imageOfInterest = mysqli_fetch_assoc($resGetFilePathsStains)) {



	  
	  //   assert(($imageOfInterest['stainname'] == "DAPI") ||
	  //   ($imageOfInterest['stainname'] == "DIC") ||
	  //   ($imageOfInterest['stainname'] == "GFP"));
	    
	    /* WE'RE GOING TO SET THESE THREE TIMES, BUT WHATEVER */
	    //	    print "------------->". $imageOfInterest['strainname'];
	//$arry[$library][$maxIndexForLibrary]->strainName = $imageOfInterest['strainname'];
	//$arry[$library][$maxIndexForLibrary]->strainId = $imageOfInterest['strainid'];
	//$arry[$library][$maxIndexForLibrary]->orfId = $imageOfInterest['orfid'];
	//$orfNum = $imageOfInterest['orfnumber'];
	//$strainName = $imageOfInterest['strainname'];
	  

	/* SET THE APROPOS FILEPATH */
	/*
	    list ($base, $ext) = explode (".", $imageOfInterest['dirpath']);
	    if($imageOfInterest['stainname'] == "DAPI") {
		$arry[$library][$maxIndexForLibrary]->dapiThumbPath =
		    $base.$thumbExt;
	    }
	    else if ($imageOfInterest['stainname'] == "DIC") {
		$arry[$library][$maxIndexForLibrary]->dicThumbPath =
		    $base.$thumbExt;
	    }
	    else {
		$arry[$library][$maxIndexForLibrary]->gfpImagePath =
		    $imageOfInterest['dirpath'];	
	    }

	  	
	*/
  $maxIndexForLibrary = $maxIndexForLibrary + 1;
  }
}



/* BEGIN THE BIG FORM */

?>
<?//<form name="pruneDecisions" method="post" action="showPostVars.php">
?>
<form name="pruneDecisions" method="post" action="pruneLibraries.php">
<?
print "<p class=\"titleText\">orf: ".$orfNum."  ||  strain:".$strainName."</p>";

$y = 0;
$eitherPopulated = 1;
print "<table border=\"3\">";
while($eitherPopulated == 1) {
    $eitherPopulated = 0;
    
    print "<tr>\n";
    
    /* I'll burn for this... */
    for($j=0; $j<count($pastLibraries);$j++) {
      $x = $j;

      /*
      $x = 0;
      if($j==0) {
	$x = "A";
      } elseif($j == 1) {
	$x = "B";
      } elseif($j == 2) {
	$x = "C";
      } elseif($j == 3) {
	$x = "D";
      } elseif($j == 4) {
	$x = "E";
      } elseif($j == 5) {
	$x = "F";
      }
      */
      //      print "x: ".$x." y: ".$y."<br>\n";
            print "<td>";
      if($arry[$x][$y] != "") {
	$eitherPopulated = 1;
	$arry[$x][$y]->printPane();
      }
      print "</td>";
      
      //print "<img src=\"img/score_button.gif\">";
      
    }
    $y++;
    print "</tr>";
}
print "</table>";

// print_r($arry);

?>
<input name="hiddenOrfName" type="hidden" value="<?=$orfNum?>">
<input name="buttonSubmitPruneSelections" type="submit" value="SUBMIT PRUNE SELECTIONS">
<? 
if(isset($_POST['buttonSkippedOrfsSubmit'])) {
  print "<input name=\"buttonSkippedOrfsSubmit\" type=\"hidden\" 
         value=\"".$_POST['buttonSkippedOrfsSubmit']."\">";
} elseif (isset($_POST['buttonSpecificPlateSubmit'])) {
  print "<input name=\"buttonSpecificPlateSubmit\" type=\"hidden\" 
         value=\"".$_POST['buttonSpecificPlateSubmit']."\">";

  print "<input name=\"specificPlateSelect\" type=\"hidden\" 
         value=\"".$_POST['specificPlateSelect']."\">";

} elseif(isset($_POST['buttonUnprunedOrfsSubmit'])) {
  print "<input name=\"buttonUnprunedOrfsSubmit\" type=\"hidden\" 
         value=\"".$_POST['buttonUnprunedOrfsSubmit']."\">";
} else {
  assert(0);
}

if(!isset($_POST['buttonSkippedOrfsSubmit'])) {
  print "<input name=\"buttonSubmitSkipRequest\" type=\"submit\" 
         value=\"SKIP THIS ORF FOR NOW\">";
}
?>
</form>


<? /* SUBROUTINES */

function processPruneSelections($levelsGFP, $levelsRFP) {

  
  $allSelectsSelected = "true";
  foreach ($_POST as $key => $value) {
    if(preg_match("/pruneChoice(\d+)/", $key, $setId)) {
      assert(isset($value));
      if($value == "marker") {
	$allSelectsSelected = "false";
      }
    }
    if(preg_match("/variant(\d+)/", $key, $setId)) {
      assert(isset($value));
      if($value == "marker") {
	$allSelectsSelected = "false";
      }
    }
  }

  if ($allSelectsSelected == "false") {
    /* WE MUST UNLOCK THE SETS SO THAT IT CAN BE SCORED AGAIN */
    foreach ($_POST as $key => $value) {
      if(preg_match("/pruneChoice(\d+)/", $key, $match)) {
	
	assert(isset($value));
	$setOfInterest = $match[1];
	$sql = "UPDATE sets
              SET locked='F' WHERE setid=".$setOfInterest;
	assert(mysqli_query($sql));
      }
    }
    return "PLEASE SELECT A DESIGNATION FOR EACH SET.<br>\n";
  }

  foreach ($_POST as $key => $value) {
    if(preg_match("/pruneChoice(\d+)/", $key, $match)) {
      assert(isset($value));
      assert(($value == "score") ||
	     ($value == "noscore") ||
	     ($value == "nogfp"));
      $setOfInterest = $match[1];
      if($value == "score") {
      $sql = "UPDATE sets
              SET score_this='T', prune_complete='T', no_gfp_visible='F', locked='F'
              WHERE setid=".$setOfInterest;
      assert(mysqli_query($sql));
      }

      if($value == "noscore") {
      $sql = "UPDATE sets
              SET score_this='F', prune_complete='T', no_gfp_visible='F', locked='F'
              WHERE setid=".$setOfInterest;
      assert(mysqli_query($sql));
      }

      if($value == "nogfp") {
      $sql = "UPDATE sets
              SET score_this='F', prune_complete='T', no_gfp_visible='T', locked='F'
              WHERE setid=".$setOfInterest;
      assert(mysqli_query($sql));
      }
    } else if(preg_match("/variant(\d+)/", $key, $match)) {
      assert(isset($value));
      assert($value < 9);
      $setOfInterest = $match[1];
      $backMatchArray = getVariantsNameList($setOfInterest, $levelsGFP, $levelsRFP);
      
      //      printWB($backMatchArray[$value]);
      assert(preg_match("/(setid.*)/", $backMatchArray[$value], $match2));
      $sql = "UPDATE sets SET bestcolocdirpath='".$match2[1]."' WHERE setid=".$setOfInterest;
      //      printWB($sql);
      assert(mysqli_query($sql));
    }
  }  
  assert(isset($_POST['hiddenOrfName']));
  return "UPDATED PRUNE INFO FOR ORF #".$_POST['hiddenOrfName'];

}

function processSkipRequest() {
  foreach ($_POST as $key => $value) {

    if(preg_match("/pruneChoice(\d+)/", $key, $match)) {
      $setOfInterest = $match[1];
    }

    $sql = "UPDATE sets
            SET score_this='F', prune_complete='F', no_gfp_visible='F', prune_skipped='T',
            locked='F'
            WHERE setid=".$setOfInterest;
    assert(mysqli_query($sql));
  }
  assert(isset($_POST['hiddenOrfName']));
  return "SET ORF #".$_POST['hiddenOrfName']." AS SKIPPED.";

}

function getVariantsNameList($setid, $levelsGFP, $levelsRFP) {
  $mergedList = array();
  foreach ($levelsGFP as $levelGFP) {
    foreach ($levelsRFP as $levelRFP) {
      $mergedImageName = "setid".$setid."_gfp".$levelGFP."_rfp".$levelRFP.".png";
      $mergedList[] = "/images/colocComposites/".$mergedImageName;
    }
  }
  return $mergedList;
}
?>
