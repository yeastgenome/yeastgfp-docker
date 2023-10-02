<?php
require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/secure.php");
require("$include_dir/projects_inc.php");

pass($priv['Superuser']);	


/* IF THIS PAGE WAS TARGETED BY SELF WITH A REQUST, DO THAT */

if(isset($_POST['buttonSubmitPruneSelections'])) {
  $msg = processPruneSelections();
}
if(isset($_POST['buttonSubmitSkipRequest'])) {
  $msg = processSkipRequest();
}
print $msg;

?>
<link href="prune.css" rel="stylesheet" type="text/css">
<?

if(isset($_POST['buttonSkippedOrfsSubmit'])) {
  $conditions = "WHERE sets.new_prune_complete = 'F' AND sets.new_prune_skipped = 'T' AND sets.prune_skipped = 'F'";
} else {
/* IF WE'RE IN THE MODE WHERE WE TAKE THE FIRST ONE */
$conditions = "WHERE sets.new_prune_complete = 'F'
                 AND sets.new_prune_skipped = 'F'
                 AND sets.prune_skipped = 'F'";
}
$sqlGetPotentialsList = "SELECT * FROM sets ".
                         $conditions."
                         AND locked='F'";
$resGetPotentialsList = dbquery($sqlGetPotentialsList);


if(mysqli_num_rows($resGetPotentialsList) == 0) {
  $msg = "No unpruned images exist in that class (eg on that plate, unpruned, etc).<br>";
  $msg .= "<A HREF=\"pruneSetup.php\">Click here to return to prune setup.</A>";
  centermsg($msg);
  exit;
}


/* ONLY IN VERY RARE SITUATIONS WHEN WE'RE STEPPING ON EACH OTHER SHOULD WE GO THROUGH THIS
   MORE THAN ONCE */
while($potentialKey = mysqli_fetch_assoc($resGetPotentialsList)) {

  $strainForSet = getOneToOneMatch("sets", "setid", $potentialKey['setid'], "strainid");
  //  printWB($strainForSet);
  $orfOfInterest = getOneToOneMatch("strains", "strainid", $strainForSet, "orfid");

  assert(isset($orfOfInterest));
  
  
  /* GET THE STRAINS THAT MATCH THE ORF THAT WE FOUND */
  $sqlGetAllOrfMatches = "SELECT * FROM strains
                        WHERE tag='GFP' AND
                        orfid = ".$orfOfInterest;
  $resStrainOfInterest = dbquery($sqlGetAllOrfMatches);

  /* CURRENTLY THERE SHOULD ONLY BE TWO OF THESE...  THIS WILL CHANGE */
  assert(mysqli_num_rows($resStrainOfInterest) == 2);
  
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
    break;
  }
  printWB("looks like something went wrong");
  innoDbAbortTransaction();
}


/* DEFINE A CLASS TO HOLD THE INFO FOR BUILDING THE DISPLAY PER SET */
class PaneInfo
{
    var $setId;
    var $strainId;
    var $gfpImagePath;
    var $dicThumbPath;
    var $dapiThumbPath;
    var $tableXLoc;
    var $tableYLoc;
    var $formName;
    var $strainName;
    var $strainId;
    var $orfId;
    var $library;
    
    function PaneInfo() {
      $this->gfpImagePath = "../img/missing.jpg";
      $this->dicThumbPath = "../img/missing.thumb.jpg";
      $this->dapiThumbPath = "../img/missing.thumb.jpg";
    }



    function printPane() {
      print "<p class=\"libLabel\">Lib ".$this->library." set:".$this->setId." (".$this->gfpImagePath.")";
      print "<table>";
      print "<tr>";
      print "<td>";
      print "<img src=\"images/".$this->gfpImagePath."\">";
      print "<td valign=\"top\">";


      print "<table>";
      print "<tr> <td>";
      print "<img src=\"images/".$this->dicThumbPath."\">";
      print "</td> </tr>";
      print "<tr> <td>";
      print "<img src=\"images/".$this->dapiThumbPath."\">";
      print "</td> </tr>";
      print "<tr> <td>";
      print "<input type=\"hidden\" name=\"pruneChoice".$this->setId."\" value=\"marker\">";

      print "<select name=\"pruneChoice".$this->setId."\" size = \"3\">";
      print "<option value=\"score\">Score</option>";
      print "<option value=\"noscore\">Don't Score</option>";
      print "<option value=\"nogfp\">GFP Not Visible</option>";
      print "</select>";
      print "</td> </tr>";

      print "</table>";




      
      print "</td>";
      
      print "</tr>";
      print "</table>";

    }
}

/* KEEP TRACK OF WHICH ORF WE'RE DEALING WITH... */
$orfNum = "";
$strainName = "";

/* BUILD THE 2-D ARRAY OF PANEINFOS... */

/* REQUERY AS ABOVE, THIS TIME WITH INTENT TO CHANGE */
$resStrainOfInterest = dbquery($sqlGetAllOrfMatches);

/* CURRENTLY THERE SHOULD ONLY BE TWO OF THESE...  THIS WILL CHANGE */
assert(mysqli_num_rows($resStrainOfInterest) == 2);

while($strainOfInterest = mysqli_fetch_assoc($resStrainOfInterest)) {
    $sqlGetAllSetsInStrain = "SELECT * FROM sets
                              INNER JOIN strains ON strains.strainid = sets.strainid
                              WHERE sets.strainid = ".$strainOfInterest['strainid'];
    $resGetAllSetsInStrain = dbquery($sqlGetAllSetsInStrain);
    $maxIndexForLibrary = 0;
    while($setOfInterest = mysqli_fetch_assoc($resGetAllSetsInStrain)) {
	$library = $setOfInterest['library'];
	
	$arry[$library][$maxIndexForLibrary] = new PaneInfo;
	$arry[$library][$maxIndexForLibrary]->setId = $setOfInterest['setid'];
	$arry[$library][$maxIndexForLibrary]->strainId = $setOfInterest['strainid'];
	$arry[$library][$maxIndexForLibrary]->library = $setOfInterest['library'];

	assert ($library == "A" || $library == "B");
	if ($library == "A") {	
	    $arry[$library][$maxIndexForLibrary]->tableXLoc = 0;
	}
	else {
	    $arry[$library][$maxIndexForLibrary]->tableXLoc = 1;
	}

//	print "arrylibrary: ".$arry[$library][$maxIndexForLibrary]->tableXLoc."<br>\n";

	$arry[$library][$maxIndexForLibrary]->tableYLoc = $maxIndexForLibrary;


	/* UHHH...  MAYBE NOT SO EFFICIENT, BUT EASY */
	$sqlGetFilePathsStains = "SELECT * FROM images
                                  INNER JOIN stain on stain.stainid = images.stainid
                                  INNER JOIN sets on images.setid = sets.setid
                                  INNER JOIN strains on sets.strainid = strains.strainid
                                  INNER JOIN orfs on strains.orfid = orfs.orfid
                                  WHERE images.setid = ".$setOfInterest['setid'];
	$resGetFilePathsStains = dbquery($sqlGetFilePathsStains);
	/* MAKE SURE ALL THREE PICS ARE THERE */
	//	print $setOfInterest['setid'];
	//	print mysqli_num_rows($resGetFilePathsStains);
	//	assert(mysqli_num_rows($resGetFilePathsStains) == 3);

	/* THIS SHOULD BE REPLACED WITH THE GLOBAL OF THE SAME NATURE */
	$thumbExt = ".thumb.png";
	
	/* ASSIGN THE PATH MEMBER VARIABLES ACCORDING TO STAIN */
	while($imageOfInterest = mysqli_fetch_assoc($resGetFilePathsStains)) {

	    assert(($imageOfInterest['stainname'] == "DAPI") ||
		   ($imageOfInterest['stainname'] == "DIC") ||
		   ($imageOfInterest['stainname'] == "GFP"));
	    
	    /* WE'RE GOING TO SET THESE THREE TIMES, BUT WHATEVER */
	    //	    print "------------->". $imageOfInterest['strainname'];
	    $arry[$library][$maxIndexForLibrary]->strainName = $imageOfInterest['strainname'];
	    $arry[$library][$maxIndexForLibrary]->strainId = $imageOfInterest['strainid'];
	    $arry[$library][$maxIndexForLibrary]->orfId = $imageOfInterest['orfid'];
	    $orfNum = $imageOfInterest['orfnumber'];
	    $strainName = $imageOfInterest['strainname'];


	    /* SET THE APROPOS FILEPATH */
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
	
	
	}
    	$maxIndexForLibrary = $maxIndexForLibrary + 1;
    }
}


/* BEGIN THE BIG FORM */

?>
<!-- <form name="pruneDecisions" method="post" action="showPostVars.php"> -->
<form name="pruneDecisions" method="post" action="newPruneLibraries.php"> 
<?
print "<p class=\"titleText\">orf: ".$orfNum."  ||  strain:".$strainName."</p>";

$y = 0;
$eitherPopulated = 1;
print "<table border=\"3\">";
while($eitherPopulated == 1) {
    $eitherPopulated = 0;
    
    print "<tr>\n";
    
    /* I'll burn for this... */
    for($j=0; $j<2;$j++) {
	$x = 0;
	if($j==0) {
	    $x = "A";
	}
	else {
	    $x = "B";
	}

	//	print "x: ".$x." y: ".$y."<br>\n";
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
?>
<input name="hiddenOrfName" type="hidden" value="<?=$orfNum?>">
<input name="buttonSubmitPruneSelections" type="submit" value="SUBMIT PRUNE SELECTIONS">
<? 
if(isset($_POST['buttonSkippedOrfsSubmit'])) {
  print "<input name=\"buttonSkippedOrfsSubmit\" type=\"hidden\" 
         value=\"".$_POST['buttonSkippedOrfsSubmit']."\">";
} else {
print "<input name=\"buttonUnprunedOrfsSubmit\" type=\"hidden\" 
         value=\"".$_POST['buttonUnprunedOrfsSubmit']."\">";
}

if(!isset($_POST['buttonSkippedOrfsSubmit'])) {
  print "<input name=\"buttonSubmitSkipRequest\" type=\"submit\" 
         value=\"SKIP THIS ORF FOR NOW\">";
}
?>
</form>


<? /* SUBROUTINES */

function processPruneSelections() {

  $allSelectsSelected = "true";
  foreach ($_POST as $key => $value) {
    if(preg_match("/pruneChoice(\d+)/", $key, $setId)) {
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
              SET new_score_this='T', new_prune_complete='T', new_no_gfp_visible='F', locked='F'
              WHERE setid=".$setOfInterest;
      assert(mysqli_query($sql));
      }

      if($value == "noscore") {
      $sql = "UPDATE sets
              SET new_score_this='F', new_prune_complete='T', new_no_gfp_visible='F', locked='F'
              WHERE setid=".$setOfInterest;
      assert(mysqli_query($sql));
      }

      if($value == "nogfp") {
      $sql = "UPDATE sets
              SET new_score_this='F', new_prune_complete='T', new_no_gfp_visible='T', locked='F'
              WHERE setid=".$setOfInterest;
      assert(mysqli_query($sql));
      }
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
            SET new_score_this='F', new_prune_complete='F', new_no_gfp_visible='F', new_prune_skipped='T',
            locked='F'
            WHERE setid=".$setOfInterest;
    assert(mysqli_query($sql));
  }
  assert(isset($_POST['hiddenOrfName']));
  return "SET ORF #".$_POST['hiddenOrfName']." AS SKIPPED.";

}

?>






