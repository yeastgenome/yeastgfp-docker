<?php
require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/secure.php");
require("$include_dir/projects_inc.php");

pass($priv['Superuser']);

?>

<html>
<head> 
    <link rel="stylesheet" href="imagedb.css">
    <script src="interact.js"></script>
</head>
<body>
<?

print "<p>so's your old man";

$myObj = new LocalizationDisplayByORF;
$myObj->setLocId(12124);
$myObj->populate();
$myObj->printLocalization("link");

class LocalizationExamplePic {
  var $subcellname;
  var $pathToClip;
}





class LocalizationDisplayByORF
{
  var $orfNumber; 
  var $orfName;
  var $HTTPToSGD;
  var $orfid;
  var $GFPLocalizationExampleList;
  var $colocLocalizationExampleList;
  var $finalLocalizationExampleList;
  var $orfSize;
  var $essential;
    
  function LocalizationDisplayByORF() {
  }

  function populate($orfid) {

    $this->orfid = $orfid;

    $sql = "SELECT * from strains inner join sets on sets.strainid=strains.strainid inner join localizations on localizations.setid=sets.setid where strains.orfid=".$orfid." where strains.backgroundid=1";
    $res = dbquery($sql);
    $subcellidList = makeArrayFromResColumn($res, "subcellid");
    
    
    
    $sqlsearch = "SELECT strains.strainname, strains.library, orfs.orfnumber, orfs.orfname, cellcycle.phase, subcell.subcellname, localization.xcoord, localization.ycoord, users.realname, localization.localizeid, cellbrightness.cellbrightness, cellmorphology.cellmorphology, subcellhomogeneity.subcellhomogeneity, condition.conditionname, sets.setid, orfs.orfid
	 FROM strains 
	 INNER JOIN orfs ON orfs.orfid = strains.orfid
	 INNER JOIN sets ON sets.strainid = strains.strainid
	 INNER JOIN localization ON sets.setid = localization.setid
	 INNER JOIN subcell ON localization.subcellid = subcell.subcellid
	 INNER JOIN cellcycle ON localization.cellcycleid = cellcycle.cellcycleid
	 INNER JOIN users ON localization.userid = users.userid
         INNER JOIN cellbrightness ON localization.cellbrightnessid = cellbrightness.cellbrightnessid
         INNER JOIN cellmorphology ON localization.cellmorphologyid = cellmorphology.cellmorphologyid
         INNER JOIN subcellhomogeneity ON localization.subcellhomogeneityid = subcellhomogeneity.subcellhomogeneityid
         INNER JOIN condition ON sets.conditionid = condition.conditionid
	 WHERE localizeid = ".$this->localizeId;
    $ressearch = dbquery($sqlsearch);
    
    while ($row = mysqli_fetch_assoc($ressearch)) {
      
      $this->strainName = $row['strainname'];
      $this->library = $row['library'];
      $this->orfNumber = $row['orfnumber'];
      $this->orfName = $row['orfname'];
      $this->subcellLocalization = $row['subcellname'];
      $this->cellCyclePhase = $row['phase'];
      $this->condition = $row['conditionname'];
      $this->userWhoScored = $row['realname'];
      $this->relativeIntensity = $row['cellbrightness'];
      $this->signalHomogeneity = $row['subcellhomogeneity'];
      $this->cellMorphology = $row['cellmorphology'];
      $this->xPos = $row['xcoord'];
      $this->yPos = $row['ycoord'];
      $this->setId = $row['setid'];
      $this->orfId = $row['orfid'];

      $this->HTTPToSGD = "https://yeastgfp.yeastgenome.org/locus/" . $this->orfNumber;
      checkCellImageExists($this->localizeId);
      $this->clipPath = "/images/clips/".$this->localizeId.".png";
   
    

    /* ADD THE REST OF THE LOCS FOR THE ORF TO THE STRUCTURE */

      $this->otherLocIds = orfIdToLocMap($this->orfId);
      print_r($this->otherLocIds);
      
      foreach ($this->otherLocIds as $locId) {
	// check to see if it's the current loc 
	if ($locId == $this->localizeId) {
	  // add this loc to display option
	  $this->displayOptionText[$this->localizeId] = $this->subcellLocalization." -- ".$this->localizeId;
	} else {
	  checkCellImageExists($locId);
	  // get displayable info for the loc
	  $subcellId = getOneToOneMatch("localization","localizeid",$locId,"subcellid");
	  $subcellName = getOneToOneMatch("subcell","subcellid",$subcellId,"subcellname");
	  // build another array for display info? option string info
	  $this->displayOptionText[] = $subcellName." -- ".$locId;
	  
	}
	
      }

    }
  }
  
  
  function getOrfInfo($orf) {
    
    //    $sqlGetOrfInfo = "SELECT * from orfs left join strains on strains.orfid=orfs.orfid where orfnumber='".$orf."'";
    $sqlGetOrfInfo = "SELECT strains.strainname, strains.library, orfs.orfnumber, orfs.orfname
                      FROM strains
                      INNER JOIN orfs ON orfs.orfid = strains.orfid
                      WHERE orfnumber = '".$orf."'";
    $row = mysqli_fetch_assoc(dbquery($sqlGetOrfInfo));
    if ($row['strainname'] == NULL) {
      $row['strainname'] = "not in library";
    }
    if ($row['orfname'] == NULL) {
      $row['orfname'] = "not in DB";
    }
    
    $this->orfName = $row['orfname'];
    $this->strainName = $row['strainname'];
    $this->library = $row['library'];
    
  }

  function buildLocalizationHTML($linkPic) {
    $retStr = "";
    $retStr .=  "<p class=\"locLabel\">\n";
    $retStr .= "<table width=700 bgcolor=#".$this->color.">\n"; 
    $retStr .= "<tr>\n";
    $retStr .= "<td width=240>\n";
    $retStr .= "<b>strain</b>\n";
    $retStr .= "</td>\n";
    $retStr .= "<td width=140>\n";
    $retStr .= "<b></b>\n";
    $retStr .= "</td>\n";
    $retStr .= "<td width=140>\n";
    $retStr .= $this->userWhoScored."\n";
    $retStr .= "</td>\n";
    $retStr .= "<td>\n";
    if($this->localizeId != "none") {
      $tmp = getOneToOneMatch("localization", "localizeid", $this->localizeId, "setid");
      $retStr .= "<b>id</b>: ".$tmp."\n";
    } else {
      $retStr .= "<b>id</b>: ".$this->localizeId."\n";
    }
    $retStr .= "</td>\n";    print "<td>\n";
    $retStr .= "<td>\n";
    $retStr .= "<b>x</b>: ".$this->xPos."\n";
    $retStr .= "</td>\n";    print "<td>\n";
    $retStr .= "<td>\n";
    $retStr .= "<b>y</b>: ".$this->yPos."\n";
    $retStr .= "</td>\n";
    $retStr .= "</tr>\n";
    $retStr .= "<tr>\n";
    $retStr .= "<td>\n";
    $retStr .= "<b class=\"searchDisp\">";
    if($this->validOrfnumber) {
      $retStr .= "<a href=\"https://yeastgfp.yeastgenome.org/locus/". $this->orfNumber ."\" target=\"_new2\">";
    }
    $retStr .= $this->orfName."</b><br>\n";
    $retStr .= "<b class=\"searchDisp\">".$this->orfNumber;
    if($this->validOrfnumber) {
      $retStr .= "</a>";
    }
    $retStr .= "</b><br>\n";
    $retStr .= $this->errorMessage;
    $retStr .= "</td>\n";
    $retStr .= "<td>\n";
    $retStr .= "<b class=\"searchDisp\">".$this->subcellLocalization."</b><br>\n";
    $retStr .= $this->cellCyclePhase."<br>\n";
    $retStr .= $this->condition; 
    $retStr .= "</td>\n";
    $retStr .= "<td>\n";
    $retStr .= "Int: ".$this->relativeIntensity."<br>\n";
    $retStr .= "Hom: ".$this->signalHomogeneity."<br>\n";
    $retStr .= "Mor: ".$this->cellMorphology."\n";
    $retStr .= "</td>\n";
    $retStr .= "<td colspan=3>\n";
    if ($this->localizeId !== "none" && $linkPic == "link") {
      $retStr .= "<a href=\"displayLocImage.php?loc=".$this->localizeId."\" target=\"_new\">";
      $retStr .= "<img src=\"".$this->clipPath."\" border=0>\n";
      $retStr .= "</a>";
    } else {
      $retStr .= "<img src=\"".$this->clipPath."\" border=0>\n";
    }
    
    $retStr .= "</td>\n";
    $retStr .= "</tr>\n";
    $retStr .= "</table>\n";
    $retStr .= "<table width=700 bgcolor=#".$this->color.">\n"; 
    $retStr .= "<tr>\n";
    $retStr .= "<td>\n";
    $retStr .= "<b>other locs for strain &gt;&gt;</b>";
    $retStr .= "</td>\n";
    $retStr .= "<td>\n";
    $retStr .= "nuclear periphery";
    $retStr .= "</td>\n";
    $retStr .= "<td>\n";
    $retStr .= "bud neck";
    $retStr .= "</td>\n";
    $retStr .= "<td>\n";
    $retStr .= "your mom";
    $retStr .= "</td>\n";
    $retStr .= "<td>\n";
    $retStr .= "test";
    $retStr .= "</td>\n";
    $retStr .= "<td>\n";
    $retStr .= "nuclear periphery";
    $retStr .= "</td>\n";
    $retStr .= "<td>\n";
    $retStr .= "koool keith";
    $retStr .= "</td>\n";
    $retStr .= "<td>\n";
    $retStr .= "test";
    $retStr .= "</td>\n";
    $retStr .= "</tr>\n";
    $retStr .= "<b>other locs for strain &gt;&gt;</b>";
    $retStr .= "</td>\n";
    $retStr .= "<td>\n";
    $retStr .= "nuclear periphery";
    $retStr .= "</td>\n";

    $retStr .= "<td>\n";
    $retStr .= "bud neck";
    $retStr .= "</td>\n";

    $retStr .= "<td>\n";
    $retStr .= "your mom";
    $retStr .= "</td>\n";
    $retStr .= "<td>\n";
    $retStr .= "test";
    $retStr .= "</td>\n";
    $retStr .= "<td>\n";
    $retStr .= "nuclear periphery";
    $retStr .= "</td>\n";
    $retStr .= "<td>\n";
    $retStr .= "koool keith";
    $retStr .= "</td>\n";
    $retStr .= "<td>\n";
    $retStr .= "test";
    $retStr .= "</td>\n";
    $retStr .= "</tr>\n";
    $retStr .= "<b>other locs for strain &gt;&gt;</b>";
    $retStr .= "</td>\n";
    $retStr .= "<td>\n";
    $retStr .= "nuclear periphery";
    $retStr .= "</td>\n";

    $retStr .= "<td>\n";
    $retStr .= "bud neck";
    $retStr .= "</td>\n";

    $retStr .= "<td>\n";
    $retStr .= "your mom";
    $retStr .= "</td>\n";
    $retStr .= "<td>\n";
    $retStr .= "test";
    $retStr .= "</td>\n";
    $retStr .= "<td>\n";
    $retStr .= "nuclear periphery";
    $retStr .= "</td>\n";
    $retStr .= "<td>\n";
    $retStr .= "koool keith";
    $retStr .= "</td>\n";
    $retStr .= "<td>\n";
    $retStr .= "test";
    $retStr .= "</td>\n";


    $retStr .= "</tr>\n";
    $retStr .= "</table>\n";

    return $retStr;
  }

  function printLocalization($linkPic) {
    
    // change this so that you can have link to new, link to same, and no link

    print($this->buildLocalizationHTML($linkPic));
  }

  function changeColor($value) {
    $this->color = $value;
  }
  
    }  

?>
</body>
</html>
