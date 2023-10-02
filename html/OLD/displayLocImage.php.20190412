<?php

require("locInclude.php");
require("$include_dir/include.php");
// require("$include_dir/secure.php");
require("$include_dir/projects_inc.php");

// REMOVED ASC 080803
// pass($priv['User']);

?>

<html>
<head>
  <title>Full-Field Localization Display -- yeastgfp.yeastgenome.org</title>
  <link rel="stylesheet" href="imagedb.css">
  <script src="interact.js"></script>
</head>


<?php

// ERROR CHECKING FOR NON LOC IDS IN $_GET
if (!empty($_GET['loc'])) {

  $localizeId = $_GET['loc'];

  // match anything that isn't a digit
  if (preg_match("/\D/",$localizeId)) {

    $errorMsg .= "<p><b>ERROR:</b> you have non-numeric characters in the Loc Id.";
    
  } else {

    // only things that have records in bestlocs    
    $rows = getOneToMany('bestlocs','localizeid',$localizeId,'orfid');

    if (empty($rows[0])) {

      $errorMsg .= "<p><b>ERROR:</b> the requested Loc Id cannot be found";       
    }
  }
} else {

  // nothing entered
  $errorMsg .= "<p><b>ERROR:</b> No Loc Id given";
}

// BAIL IF ANY ERRORS HAVE OCCURRED
if (!empty($errorMsg)) {

  $errorMsg .= "<p>Please use the clip links to target this page to a valid Loc ID.";
  $errorMsg .= "<p><a href=\"index.php\">return to yeastgfp.yeastgenome.org </a>";
  print $errorMsg;
  exit;

}

$locObj = new LocalizationDisplay();
$locObj->setLocId($localizeId);
$locObj->populate();

$ext = ".png";
$imageDir = "/images/";
$expDir = "expSeries/";


// CHECK THAT IMAGES EXIST OR DIE GRACEFULLY

/* GET THE OTHER LOCALIZATIONS FOR THIS SET */
/*
$sqlGetSet = "SELECT localizeid FROM localization 
              WHERE setid = ".$locObj->setId."
              AND localizeid != ".$locObj->localizeId;

$resSqlGetSet = dbquery($sqlGetSet);
while ($row = mysql_fetch_assoc($resSqlGetSet)) {
  if(isLocValid($row['localizeid'])) {
    $otherLocObj = new LocalizationDisplay();
    $otherLocObj->setLocId($row['localizeid']);
    $otherLocObj->populate();
    
    $arrayOfOtherLocObj[] = $otherLocObj;
  }
}
*/

// BUILD THE IMAGE LIST

if(isSetColoc($locObj->setId)) {

  // outer loop is GFP
  for($i=0; $i<3; $i++) {

    // GFP ONLY
    $image_list[] = $locObj->setId . "_GFPPlus" . $i . $ext;
    
    // inner loop is RFP
    for($j=0; $j<3; $j++) {
      
      // RFP ONLY
      $image_list[] = $locObj->setId . "_RFPPlus" . $j . $ext;
    
      // MERGE
      $image_list[] = $locObj->setId . "_Plus" . $i . "GFPPlus" . $j . "RFP" . $ext;
      
    }
    
  }
  
} else {

  // NON COLOC SET, GFP AND DAPI ONLY
  
  for($i=0; $i<3; $i++) {

    // DUMB RENAMING MISTAKE FIX -- GFP.png INSTEAD OF GFPPlus0.png
    if ($i == 0) {
      // GFP 
      $image_list[] = $locObj->setId . "_GFP" . $ext;
      // DAPI 
      $image_list[] = $locObj->setId . "_DAPI" . $ext;

    } else {
      // GFP 
      $image_list[] = $locObj->setId . "_GFPPlus" . $i . $ext;
      // DAPI 
      $image_list[] = $locObj->setId . "_DAPIPlus" . $i . $ext;
    }
  }  
}

$image_list = array_unique($image_list);
sort($image_list);

/******* split these into two arrays: one DIC and DAPI, one all other images *******/
/******* make names for browser interpretation: $image_src_one $image_src_two ******/

// ADD THE DIC IMAGE TO image_src_two
$dicPath = getOneToOneArbConditions("images","setid=".$locObj->setId." AND stainid=4","dirpath");
$image_src_two[] = $imageDir.$dicPath;

foreach ($image_list as $img) {

  if (preg_match("/DAPI/",$img)) { 
    $image_src_two[] = $imageDir.$expDir.$img;
  }
  else {
    $image_src_one[] = $imageDir.$expDir.$img;
  }
}

// BUILD THE IMAGE DISPLAY ARRAYS

foreach ($image_src_one as $img) {
  $base = basename($img);
  $match = "";
  preg_match("/^\d+_(\w+)\.png/",$base,$match);
  $image_disp_one[] = $match[1];
}

foreach ($image_src_two as $img) {
    $base = basename($img);
    if (preg_match("/DIC/",$img)) { 
      $image_disp_two[] = "DIC";      
    } else {
      preg_match("/^\d+_(\w+)\.png/",$base,$match);
      $image_disp_two[] = $match[1];
    }
}

/************************** get the reference images ********************************/

$sqlref = "SELECT * FROM subcell WHERE showasref='T'";
$resref = dbquery($sqlref);
while ($row = mysql_fetch_assoc($resref)) {
  $ref_src[$row['subcellname']] = $row['path'];
}

/****************** add the reference images to the second array ********************/

foreach ($ref_src as $subcellname => $path) {
  $image_src_two[] = $path;
  $image_disp_two[] = $subcellname;
}

/********************************* display the images *******************************/

/* BUILD UP THE LISTS THAT WILL BE USED TO MAKE THE JAVASCRIPT ASSOCIATION BETWEEN
   THE INDEX TO THE SELECT AND THE NEW IMAGE SOURCE.
*/
$imagePathListPane1 = arrayToCommaList($image_src_one,"'");
$imagePathListPane2 = arrayToCommaList($image_src_two,"'");


$setInfo="";
$sqlstrain = "SELECT strains.strainname,strains.library,orfs.orfnumber,orfs.orfname
              FROM sets
              INNER JOIN strains ON sets.strainid = strains.strainid
              INNER JOIN orfs ON strains.orfid = orfs.orfid
              WHERE sets.setid = ".$locObj->setId;
$resstrain = dbquery($sqlstrain);
$strain = mysql_fetch_row($resstrain);
foreach ($strain as $ids) {
  $setInfo .= " ".$ids." --";
}
$setInfo .= "&gt;<br>\n";

?>


<body bgcolor="#FFFFFF" onLoad="load_images('scoring'); return true;">

<script language="JavaScript">

// PRELOAD ALL THE IMAGES BEFORE WE START ANYTHING....
    var imagePathArrayPane1 = new Array(<?php echo $imagePathListPane1 ?>);
    var imagePathArrayPane2 = new Array(<?php echo $imagePathListPane2 ?>);
    
    preloadImages(imagePathArrayPane1);
    preloadImages(imagePathArrayPane2);



function changeScoringImage(image) {
    
    if (image == 1) {
	var the_scoring_one_image_value = scoringFormPane1.the_image_select.selectedIndex;
	document.pane1RealPicture.src = imagePathArrayPane1[the_scoring_one_image_value];
    }
    if (image == 2) {
	var the_scoring_two_image_value = scoringFormPane2.the_image_select.selectedIndex;
	document.pane2RealPicture.src = imagePathArrayPane2[the_scoring_two_image_value];
    }
}



</script>

<?php

/* BUILD UP SELECT FOR BOTH PANES' PICTURE FLIPPERS */
$imageSelectOptionPane1String = "";
$imageSelectOptionPane2String = "";
foreach ($image_disp_one as $key => $name) {
  $imageSelectOptionPane1String .= "<OPTION value=".$key.">".$name."\n";
}

foreach ($image_disp_two as $key => $name) {
  $imageSelectOptionPane2String .= "<OPTION value=".$key.">".$name."\n";
}


$distDToFrame         = 10;
$distRToFrame         = 10;
$heightPic            = 512;
$widthPic             = 535;
$picSpacer            = 10;
$controlSpacer        = 10;
$heightControls       = 60;
$boxOffset            = 30;
$locBoxHeight         = 105;
$boxHeight            = 66;
$boxWidth             = 66;


$xFrame1 = $distRToFrame;
$yFrame1 = $distDToFrame + $locBoxHeight;
$hFrame1 = $heightPic;
$wFrame1 = $widthPic;

$xFrame2 = $xFrame1 + $widthPic + $picSpacer;
$yFrame2 = $distDToFrame + $locBoxHeight;
$hFrame2 = $heightPic;
$wFrame2 = $widthPic;

$xBox1 = $xFrame1 + $locObj->xPos - $boxOffset;
$yBox1 = $yFrame1 + $locObj->yPos - $boxOffset;
$xBox2 = $xFrame2 + $locObj->xPos - $boxOffset;
$yBox2 = $yFrame2 + $locObj->yPos - $boxOffset;

$yOthers = $yFrame1 + $hFrame1 + $heightControls;

$exposureTop = $locBoxHeight - $distDToFrame;

?>


<!-- BUILD THE LAYER CONTAINING THE REAL LEFT PIC AND CONTROLS -->
<?php
print "<div id=\"pane1RealPictureLayer\" style=\"position:absolute;
 width:".$wFrame1."px; height:".$hFrame1."px;
 z-index:1; left:".$xFrame1."px; top: ".$yFrame1."px;\">
    <img src=\"$image_src_one[0]\" name=\"pane1RealPicture\"
     width=\"$widthPic\" height=\"$heightPic\" border=\"3\"> 

    <br>

    <span class=\"tdLookalike\">
        <table cellspacing=2 cellpadding=0 border=0 align=\"left\">
            <tr>
                <td valign=\"top\">
                    select image ::
                </td>
                <td valign=\"top\">
                    <form name=\"scoringFormPane1\">
                        <select name=\"the_image_select\" OnChange=\"changeScoringImage(1);\">
                            $imageSelectOptionPane1String
                        </select>
                    </form>
                </td>
            </tr>
        </table>
    </span>
</div>"
?>

<!-- BUILD THE LAYER CONTAINING THE REAL RIGHT PIC AND CONTROLS -->
<?php 
print "<div id=\"pane2RealPictureLayer\" style=\"position:absolute;
 width:$wFrame2"."px; height:$hFrame2"."px;
 z-index:1; left: ".$xFrame2."px; top: ".$yFrame2."px;\">
    <img src=\"$image_src_two[0]\" name=\"pane2RealPicture\"
     width=\"$widthPic\" height=\"$heightPic\" border=\"3\"> 

    <br>

    <span class=\"tdLookalike\">
        <table cellspacing=2 cellpadding=0 border=0 align=\"left\">
            <tr>
                <td valign=\"top\">
                    select image ::
                </td>
                <td valign=\"top\">
                    <form name=\"scoringFormPane2\">
                        <select name=\"the_image_select\" OnChange=\"changeScoringImage(2);\">
                            $imageSelectOptionPane2String
                        </select>
                    </form>
                </td>
            </tr>
        </table>
    </span>
</div>"
?>

<!--
   BUILD THE LAYERS WITH THE TRANSPARENT PICTURES WITH BOXES 
   TO INDICATE THE POSITION OF THE LOCALIZATION
 -->
<?php

print "<div id=\"pane1TransparentPictureLayer\" style=\"position:absolute; 
width:".$boxWidth."px; height:".$boxHeight."px;
z-index:2; left:".$xBox1."px; top:".$yBox1."px;\">
        <img src=\"img/box1.gif\" border=0>
</div>

<div id=\"pane2TransparentPictureLayer\" style=\"position:absolute;
width:".$boxWidth."px; height:".$boxHeight."px;
z-index:2; left:".$xBox2."px; top:".$yBox2."px;\">
        <img src=\"img/box1.gif\" border=0>
</div>"
?>

<?php

/* DISPLAY THE SELECTED LOCALIZATION */

print "<div style=\"position: absolute; left: ".$distRToFrame."; top: ".$distDToFrame."\">";
$locObj->printLocalization("noLink");
print "</div>";

print "<div style=\"position: absolute; left: ".$distRToFrame."; top: ".$exposureTop."\">";
$descript = getOneToOneMatch("images","setid",$locObj->setId,"descript");
preg_match("/\-(\d+)ms/",$descript,$match);
$exposure = $match[1];
print "<table><tr><td>exposure for this GFP image: ".$exposure." msec</td></tr></table>";
print "</div>";

/* DISPLAY THE OTHER LOCALIZATIONS FOR THIS SET */
/*
print "<div style=\"position: absolute; left: ".$distRToFrame."; top: ".$yOthers."\">";
print "<h1>Other Localizations for this Set of Images...</h1>";
foreach ($arrayOfOtherLocObj as $obj) {  
  $obj->printLocalization("link");
}
print "</div>";
*/
//printfooter();

?>
</body>
</html>
