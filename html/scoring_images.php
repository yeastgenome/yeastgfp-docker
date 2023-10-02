<php?
require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/secure.php");
require("$include_dir/projects_inc.php");

pass($priv['Superuser']);

$rebuildImages = "T";

$setInfoMsg = "";

/* THE FOLLOWING IS BASED ON PRUNELIBRARIES */
/* IF THIS PAGE WAS TARGETED BY SELF WITH A REQUST, DO THAT */

if(isset($_POST['buttonScoreCell_x'])) {
  processScoringRequests();
  $currentSet = $_POST['setid'];

} else if(isset($_POST['buttonRemoveScoring_x'])) {
  processDeleteLocalizationRequests();
  $currentSet = $_POST['setid'];

} else if(isset($_POST['buttonGoBack_x'])) {
  /* WE MUST HAVE GOTTEN HERE FROM THE INCOMPLETE DATA PAGE (REALLY THIS) */
  $currentSet = $_POST['setid'];

} else {
  /* WE COULD BE GETTING HERE FROM THE NEXT SET BUTTON OR THE MENU */
  /* ONLY DO THIS FOR THE NEXT SET CASE */
  if(isset($_POST['buttonNextSet_x'])) {
    $setInfoMsg .= "Just committed scoring info re set: ".$_POST['setid']."<br>\n";
    processNextSet();
    unlockSet($_POST['setid']);
  }
  
  $sqlGetExcludedSetList = "SELECT * FROM usersxscorecomplete WHERE userid = ".
    $_SESSION['userid'];
  $resGetExcludedSetList = dbquery($sqlGetExcludedSetList);
  $excludedList = makeArrayFromResColumn($resGetExcludedSetList, "setid") ;
  
  //  print_r(array_values($excludedList));
  $conditions = "WHERE sets.final_score_this = 'T'";
  
  /* START THE TRANSACTION AND GET THE FIRST UNLOCKED SET THAT MEETS SPEC */
  innoDbStartTransaction();
  $currentSet = checkAndSetLocked("sets", "setid", $conditions, "locked", $excludedList);
  innoDbEndTransaction();
  
  $rebuildImages = "T";
  
}
//$currentSet = 25181;
$setInfoMsg .= "Currently looking at set: ".$currentSet."<br>\n";
printWB($setInfoMsg);

// BAIL IF NO SETS REMAIN TO BE SCORED....
if ($currentSet == "") {
  centerMsg("I do believe you are done scoring");
  exit;
}


$sqlDesignator = "SELECT designator,referencemarker FROM background INNER JOIN strains ON strains.backgroundid=background.backgroundid INNER JOIN sets ON sets.strainid=strains.strainid WHERE sets.setid=".$currentSet;
$resDesignator = dbquery($sqlDesignator);

assert(mysqli_num_rows($resDesignator) == 1);
$row = mysqli_fetch_assoc($resDesignator);

$setInfoMsg .= $row['designator']." : ".$row['referencemarker']."<br>\n";

assert($currentSet != "");
//print $setInfoMsg;



/****************** get the stainid and stains from stains table *********************/
/********************* make an array of stainname by stainid *************************/

$sqlStains = "SELECT * FROM stain";
$resStains = dbquery($sqlStains);
while ($row = mysqli_fetch_assoc($resStains)) {
    $stains[$row["stainid"]] = $row["stainname"];
}

/***************** get the images and stain info for current set **********************/

$sqlGetSetImages = "SELECT * FROM images
                    INNER JOIN sets ON sets.setid = images.setid
                    INNER JOIN stain ON stain.stainid = images.stainid
                    WHERE sets.setid = ".$currentSet;
$resSetImages = dbquery($sqlGetSetImages);



/******* make an associative array that maps stainid to a full image filepath ********/
while ($row = mysqli_fetch_assoc($resSetImages)) {
    $stainToImagePathMap[$row["stainid"]] = $row["dirpath"];
    printWB($row["dirpath"]);
}
$num_stains = count($stainToImagePathMap);

print_r($stainToImagePathMap);

/************** handle filenames and dirpaths for each stain in stains ***************/
/** maintains flexibility for many projects (eg CFP) AND used for naming tmp files ***/
/* LCG -- BUILD A MAP BETWEEN STAINID AND FULL IMAGE PATH TO VIEWABLE FORM */

foreach ($stainToImagePathMap as $stainid => $file) {
    
    /** match and capture filename without .TIF **/
    preg_match("/^(.*)\..+$/i", $file, $matchone);
    
    /* We use .jpg right now */
    $stainToViewableImagePathMap[$stainid] = $matchone[1].$viewext;
}

/************** set $tmp_user_dir to the appropriate subdirectory ******************/
/*********** make subdirectory if user is scoring for the first time ***************/

assert(is_dir($tmp_dir));


$tmp_user_dir = $tmp_dir.$_SESSION['userid']."/";

if (file_exists($tmp_user_dir)) {
    assert(is_dir($tmp_user_dir));
} else {
    mkdir($tmp_user_dir, 0754);
}

/********* copy the images for the selected set into the tmp/user directory ***********/

while (list ($stainID, $path) = each ($stainToViewableImagePathMap)) {
    $orig_files[$stainID] = $image_dir.$path;
    $tmp_images[$stainID] = $stains[$stainID].$currentSet.$viewext;
    $tmp_files[$stainID]= $tmp_user_dir.$tmp_images[$stainID];
    if($rebuildImages == "T") {
      copy ($orig_files[$stainID],$tmp_files[$stainID]);
    }
    printWB($orig_files[$stainID]);
    printWB($tmp_files[$stainID]);
    assert(file_exists($tmp_files[$stainID]));
}

/** set values for high and low levels for making images **/
// LET'S MAKE THIS AN APPLET!
$levelh = array ("51400","41120","30840");
//$levelhRFP = array ("51400","41120", "31000", "21000");
//$levelh = array ("51400","41120");
$levelh = array ("41400","21120");

$GFPImagesArry = "";
$RFPImagesArry = "";
$commonBase = "";


foreach ($tmp_images as $strainID => $image) {
    /** skip DIC image because "normalize" works well on these pics */
    if (preg_match("/^DIC.*/",$image)) {
        $file = $tmp_user_dir.$image;
        $image_list[] = $file;
        continue;
    } else {
	$file = $tmp_user_dir.$image;
	$image_list[] = $file;

	// START GETTING SET UP TO MAKE THE COLOCALIZATION IMAGES
	preg_match("/^(.*)\..+$/", $image, $base);
	$rootName = $base[1].$viewext;
	if(preg_match("/GFP.*/",$file)) {
	  $GFPImagesArry[] = $rootName;
	}
	if(preg_match("/RFP.*/",$file)) {
	  $RFPImagesArry[] = $rootName;
	}

	foreach ($levelh as $j => $value) {
	    /* get file (dirpath) from $tmp_files **/
	    $file = $tmp_files[$strainID];
	    
	    /* strip .jpeg off file name */
	    preg_match("/^(.*)\..+$/", $image, $base);
	    
	    /* make a unique name for copy */
	    $new_file = $tmp_user_dir.$base[1]."+".($j+1).$viewext;

	    /* STORE AWAY THE BASE...  THIS IS REPETITIVE, BUT FINE. */
	    $rootName = $base[1]."+".($j+1).$viewext;
	    
	    /* make duplicate file */
	    if($rebuildImages == "T") {
	      copy ($file,$new_file);
	    }
	    assert(file_exists($new_file));
	    
	    /* mogrify it using the values in $levelh */
	    if($rebuildImages == "T") {
	      exec ($mog_path."mogrify -level 0,1,".$value." ".$new_file);
	    }
	      
	    /* append current file to the image_list */
	    $image_list[] = $new_file;

	    // START GETTING SET UP TO MAKE THE COLOCALIZATION IMAGES
	    if(preg_match("/GFP.*/",$rootName)) {
	      $GFPImagesArry[] = $rootName;
	    }
	    if(preg_match("/RFP.*/",$rootName)) {
	      $RFPImagesArry[] = $rootName;
	    }

	}
    }
}

/* DO THE MAJIK FOR COLOCALIZATION-SPECIFIC SCORING */
if(count($RFPImagesArry) > 0 && count($GFPImagesArry) > 0) {
  foreach  ($RFPImagesArry as $RFPImage) {
    $cmd = "mogrify -colorize 0/100/100 ".$tmp_user_dir.$RFPImage;
    exec($cmd);
  }
  foreach ($GFPImagesArry as $GFPImage) {
    $cmd = "mogrify -colorize 100/0/100 ".$tmp_user_dir.$GFPImage;
    exec($cmd);
  }
  
  foreach  ($RFPImagesArry as $RFPImage) {
    foreach ($GFPImagesArry as $GFPImage) {
      $cmd = "composite -compose CopyGreen ".$tmp_user_dir.$GFPImage." ".$tmp_user_dir.$RFPImage." ".$tmp_user_dir.$GFPImage.$RFPImage;
      //      printWB($cmd);
      exec($cmd);
      $image_list[] = $tmp_user_dir.$GFPImage.$RFPImage;
    }
  }
}






/******* split these into two arrays: one DIC and DAPI, one all other images *******/
/******* make names for browser interpretation: $image_src_one $image_src_two ******/

foreach ($image_list as $img) {
  //  printWB($img);
    $base = basename($img);
    if (preg_match("/^DIC.*/",$base) || preg_match("/^DAPI.*/",$base)) { 
	$image_src_two[] = $aliased_image_dir.$_SESSION['userid']."/".$base;
    }
//    else {
	$image_src_one[] = $aliased_image_dir.$_SESSION['userid']."/".$base;
//    }
}


rsort ($image_src_one);
rsort ($image_src_two);

/****** make names for browser select display: $image_disp_one $image_disp_two ******/

foreach ($image_src_one as $img) {
    $base = basename($img);
    $match = "";
    preg_match("/(\D+)\d+(\+\d)?(\.png)(\D+)?(\d+)?(\+\d)?(\..+)?$/",$base,$match);
    $image_disp_one[] = $match[1]." ".$match[2].$match[4]." ".$match[6];
    //    printWB($img);
}


foreach ($image_src_two as $img) {
    $base = basename($img);
    preg_match("/(\D+)\d+([\+?\-]\d|)\..+$/",$base,$match);
    $image_disp_two[] = $match[1]." ".$match[2];

}

/************************** get the reference images ********************************/

$sqlref = "SELECT * FROM subcell";
$resref = dbquery($sqlref);
while ($row = mysqli_fetch_assoc($resref)) {
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
              WHERE sets.setid = ".$currentSet;
$resstrain = dbquery($sqlstrain);
$strain = mysqli_fetch_row($resstrain);
foreach ($strain as $ids) {
    $setInfo .= " ".$ids." --";
}
$setInfo .= "&gt;<br>\n";

?>


<head> 
    <link rel="stylesheet" href="imagedb.css">
    <script src="interact.js"></script>
</head>

<body bgcolor="#FFFFFF" onLoad="load_images('scoring'); return true;">

<script language="JavaScript">

// PRELOAD ALL THE IMAGES BEFORE WE START ANYTHING....
    var imagePathArrayPane1 = new Array(<?php echo $imagePathListPane1 ?>);
    var imagePathArrayPane2 = new Array(<?php echo $imagePathListPane2 ?>);
    
    preloadImages(imagePathArrayPane1);
    preloadImages(imagePathArrayPane2);

/*
function newChangeScoringImage() { // APPARENTLY THE ARGS DON'T NEED TO BE DECLARED
  var i,j = 0;
  var x;
  var a = newChangeScoringImage.arguments;
  document.myArry = new Array;
  for(i=09; i<(a.length-2); i+=3) {
  }
}
*/
function MM_reloadPage(init) {  //reloads the window if Nav4 resized 
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) { 
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }} 
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload(); 
} 
MM_reloadPage(true); 
function MM_preloadImages() { //v3.0 
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array(); 
  var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++) 
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}} 
} 
function MM_swapImgRestore() { //v3.0 
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc; 
} 
function MM_findObj(n, d) { //v4.01 
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) { 
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);} 
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n]; 
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document); 
  if(!x && d.getElementById) x=d.getElementById(n); return x; 
} 
function MM_swapImage() { //v3.0 
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3) 
    if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];} 
} 


function changeScoringImage(image) {
    
    if (image == 1) {
	var the_scoring_one_image_value = scoringFormPane1.the_image_select.selectedIndex;
	MM_swapImage('pane1RealPicture','',imagePathArrayPane1[the_scoring_one_image_value]);
	//document.pane1RealPicture.src = imagePathArrayPane1[the_scoring_one_image_value];
    }
    if (image == 2) {
	var the_scoring_two_image_value = scoringFormPane2.the_image_select.selectedIndex;
	document.pane2RealPicture.src = imagePathArrayPane2[the_scoring_two_image_value];
    }
}

-->

</script>

<php?
/***********************FUNCTION************/
function processScoringRequests() {
  assert(!empty($_POST['buttonScoreCell_x']));
  
  /** check for values not set **/
  foreach ($_POST as $key => $value) {
    //    print $key.", ".$value."\n";
    if (empty($value) || $value == "marker") { 
      //      print "got a notset";
      $notset[] = $key;
    }
  }
    
  /* bail if any of the values are not set **/
  /* this doesn't check localization, since it doesn't send variable at all 
     if Array is empty **/
  
  if (!empty($notset)) {
    
    /* BUILD UP A MESSAGE TELLING THE USER HE IS A DOLT AND FORGOT TO 
       ENTER SOME INFORMATION.
    */
    $outString = "";
    
    $outString .= "<head><link rel=\"stylesheet\" href=\"imagedb.css\">\n";
    $outString .= "<script src=\"interact.js\"></script>\n";
    $outString .= "</head>\n";
    $outString .= "<table border=0 cellspacing=0 cellpadding=10><tr>\n";
    $outString .= "<tr><td colspan=3><b><font color=\"#CC6633\">&gt;&gt;";
    $outString .= "Please go back and select values for: </font></b></td></tr>";
    foreach ($notset as $value) {
      $outString .= "<td valign=middle align=center><b>$value</b></td>\n";
    }
    $outString .= "</tr>\n";
    $outString .= "<tr><td colspan=3 align=\"right\">";
    $outString .= "<form name=\"missingDataForm\" method=post action='";
    $outString .= $_SERVER['PHP_SELF']."'>";
    $outString .= "<input type=image src=\"img/goback.gif\" name=\"buttonGoBack\">";
    $outString .= "<input type=hidden name=\"setid\" value=\"".$_POST['setid']."\">";
    $outString .= "</form>";
    $outString .= "</td></tr>\n";
    $outString .= "</table>\n";
    print $outString;
    exit;
  }
  
  /** if all values are there, submit each localization to the database **/
  
  $setid = $_POST['setid'];
  $userid = $_SESSION['userid'];
  $cellcycleid = $_POST['cell_cycle'];
  $xcoord = $_POST['x_val'];
  $ycoord = $_POST['y_val'];
  $cellMorph = $_POST['cellmorphology'];
  $cellBright = $_POST['cellbrightness'];
  $subcellHomo = $_POST['subcellhomogeneity'];




  foreach ($_POST['localization'] as $value) {
    $sqlsubmit = "INSERT INTO localization                                      
                            (setid, userid, cellcycleid, subcellid, xcoord, ycoord, cellmorphologyid, cellbrightnessid, subcellhomogeneityid)      
                            VALUES ($setid, $userid, $cellcycleid,                       
                            $value, $xcoord, $ycoord, $cellMorph, $cellBright, $subcellHomo)";
    dbquery($sqlsubmit);
  } 
}

function processNextSet() {
  assert(isset($_POST['buttonNextSet_x']));
  //  $sqlMarkSetScored = "UPDATE sets SET has_been_scored = 'T'
  //                       WHERE sets.setid = ".$_POST['setid'];
  $sqlMarkSetScored = "INSERT INTO usersxscorecomplete (setid, userid) VALUES (".$_POST['setid'].",".$_SESSION['userid'].")";
    print $sqlMarkSetScored;
    
  dbquery($sqlMarkSetScored);
}

function processDeleteLocalizationRequests() {
  foreach ($_POST['remove_cells'] as $value) {
    $sqlremove = "DELETE FROM localization WHERE localizeid = ".$value;
    $success = dbquery($sqlremove);
    $removed += $success;
  }
}



/*********************************************/
/* BUILD UP THE TEXT TO INSERT INTO THE HTML */
/*********************************************/

  

/* BUILD UP THE SELECT FOR CELL CYCLE PHASE */
$cellCycleSelectOptionString = "";
$sqlCellCycleQuery = "SELECT * FROM cellcycle ORDER BY cellcycleid";
$res = dbquery($sqlCellCycleQuery);
while ($row = mysqli_fetch_assoc($res)) {
    $selected = "";
    if($row['default_selected'] == 'T') {
        $selected = " SELECTED ";
    }
    $cellCycleSelectOptionString .= 
	"<OPTION value=".$row["cellcycleid"].">".$row["phase"]."\n";
}


/* BUILD UP THE SELECT FOR LOCATION (EG NUCLEUS, CYTOPLASM) */
$locationSelectOptionString = "";
$sqlLocationQuery = "SELECT subcellname,subcellid FROM subcell ORDER BY subcellid";
$res = dbquery($sqlLocationQuery);
while ($row = mysqli_fetch_assoc($res)) {
    $localizations[$row["subcellid"]] = $row["subcellname"];
    $locationSelectOptionString .=
	"<OPTION value=".$row["subcellid"].">".$row["subcellname"]."\n";
}

/* BUILD UP THE SELECT FOR LOCALIZ*/
$sqlScoredLocalizations = "SELECT *                                                    
                           FROM localization                                              
                           INNER JOIN cellcycle ON                                        
                           cellcycle.cellcycleid = localization.cellcycleid               
                           INNER JOIN subcell ON                                          
                           subcell.subcellid = localization.subcellid                     
                           INNER JOIN cellbrightness ON
                           cellbrightness.cellbrightnessid = localization.cellbrightnessid
                           INNER JOIN cellmorphology ON
                           cellmorphology.cellmorphologyid = localization.cellmorphologyid
                           INNER JOIN subcellhomogeneity ON
                           subcellhomogeneity.subcellhomogeneityid =
                               localization.subcellhomogeneityid
                           INNER JOIN users ON users.userid = localization.userid        
                           WHERE localization.setid = ".$currentSet."
                           AND localization.userid = ".$_SESSION['userid']." 
                           ORDER BY localizeid";
$resscored = dbquery($sqlScoredLocalizations);
while ($row = mysqli_fetch_assoc($resscored)) {
    $localizationSelectOptionString .= "<OPTION value=\"".$row['localizeid']."\">";
    $localizationSelectOptionString .= $row['xcoord']."&nbsp;".$row['ycoord']."&nbsp;";
    $localizationSelectOptionString .= $row['phase']."&nbsp;".$row['subcellname']."&nbsp;";
    $localizationSelectOptionString .= $row['cellmorphology']."&nbsp;";
    $localizationSelectOptionString .= $row['cellbrightness']."&nbsp;";
    $localizationSelectOptionString .= $row['subcellhomogeneity']."&nbsp;";
    $localizationSelectOptionString .= "&nbsp;".$row['login']."<br>\n";
}


/* BUILD UP THE SELECT FOR SUBCELLULAR HOMOGENEITY */
$subcellHomogeneitySelectOptionString = "";
$sqlSubcellHomogeneityQuery = "SELECT * FROM subcellhomogeneity";
$res = dbquery($sqlSubcellHomogeneityQuery);
while ($row = mysqli_fetch_assoc($res)) {
    $selected = "";
    if($row['default_selected'] == 'T') {
	$selected = " SELECTED ";
    }
    $subcellHomogeneitySelectOptionString .=
	"<OPTION".$selected." value=".$row['subcellhomogeneityid'].">".$row["subcellhomogeneity"]."\n";
}


/* BUILD UP THE SELECT FOR RELATIVE BRIGHTNESS */
$cellBrightnessSelectOptionString = "";
$sqlCellBrightnessQuery = "SELECT * FROM cellbrightness";
$res = dbquery($sqlCellBrightnessQuery);
while ($row = mysqli_fetch_assoc($res)) {
    $selected = "";
    if($row['default_selected'] == 'T') {
	$selected = " SELECTED ";
    }
    $cellBrightnessSelectOptionString .=
       "<Option".$selected." value=".$row['cellbrightnessid'].">".$row["cellbrightness"]."\n";
}

/* BUILD UP THE SELECT FOR MORPHOLOGY */
$cellMorphologySelectOptionString = "";
$sqlCellMorphologyQuery = "SELECT * FROM cellmorphology";
$res = dbquery($sqlCellMorphologyQuery);
while ($row = mysqli_fetch_assoc($res)) {
    $selected = "";
    if($row['default_selected'] == 'T') {
	$selected = " SELECTED ";
    }
    $cellMorphologySelectOptionString .=
       "<Option".$selected." value=".$row['cellmorphologyid'].">".$row["cellmorphology"]."\n";
}


/* BUILD UP SELECT FOR BOTH PANES' PICTURE FLIPPERS */
$imageSelectOptionPane1String = "";
$imageSelectOptionPane2String = "";
foreach ($image_disp_one as $key => $name) {
    $imageSelectOptionPane1String .= "<OPTION value=".$key.">".$name."\n";
}

foreach ($image_disp_two as $key => $name) {
    $imageSelectOptionPane2String .= "<OPTION value=".$key.">".$name."\n";
}







?>  



<php?
    $distDToFrame         = 10;
    $distRToFrame         = 10;
    $heightPic            = 512;
    $widthPic             = 535;
    $picSpacer            = 10;
    $conrolSpacer         = 10;
    $heightControls       = 20;
    $heightSharedControls = 600;
    $widthControls        = $widthPic;
    $widthDeleteControls  = 400;
    $heightDeleteControls = 600;



    $xFrame1 = $distRToFrame;
    $yFrame1 = $distDToFrame;
    $hFrame1 = $heightPic + $conrolSpacer + $heightControls;
    assert($widthPic == $widthControls);
    $wFrame1 = $widthPic;
	

    $xFrame2 = $xFrame1 + $widthPic + $picSpacer;
    $yFrame2 = $distDToFrame;
    $hFrame2 = $heightPic + $conrolSpacer + $heightControls;
    $wFrame2 = $widthPic;

    $xFrameSharedControls = $distRToFrame;
    $yFrameSharedControls = $distDToFrame + $hFrame1 + $conrolSpacer + $heightControls +
                            $conrolSpacer;
    $wFrameSharedControls = $wFrame1 + $picSpacer + $wFrame2;
    $hFrameSharedControls = $heightSharedControls;

    $xFrameDeleteControls = $distRToFrame + $widthPic + $picSpacer + $widthPic +
                            $picSpacer;
    $yFrameDeleteControls = $distDToFrame;
    $wFrameDeleteControls = $widthDeleteControls;
    $hFrameDeleteControls = $heightDeleteControls;

?>


<!-- BUILD THE LAYER CONTAINING THE REAL LEFT PIC AND CONTROLS -->
<?php 
print "<div id=\"pane1RealPictureLayer\" style=\"position:absolute;
 width:$wFrame1 px; height:$hFrame1 px;
 z-index:1; left: $xFrame1 px; top:$yFrame1 px;\">
    <img src=\"$image_src_one[0]\" name=\"pane1RealPicture\"
     width=\"$widthPic\" height=\"$heightPic\" border=\"3\"> 

    <br>

    <span class=\"tdLookalike\">
        <table cellspacing=2 cellpadding=0 border=0 align=\"left\">
            <tr>
                <td valign=\"top\">
                     x
                </td>
                <td valign=\"top\">
<form name=\"pane1_jscript_displays_form\"> 
                     <input name=\"x_val\" type=\"text\" value=\"0\" size=3>
                     <input name=\"y_val\" type=\"text\" value=\"0\" size=3>
</form>
                </td>
                <td valign=\"top\">
                    y
                <td></td><td></td><td></td><td></td><td></td>
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
<div id="pane2RealPictureLayer" style="position:absolute;
 width:<?=$wFrame2?>px; height:<?=$hFrame2?>px;
 z-index:1; left: <?=$xFrame2?>px; top: <?=$yFrame2?>px;">
    <img src="<?=$image_src_two[0]?>" name="pane2RealPicture"
     width="<?=$widthPic?>" height="<?=$heightPic?>" border="3"> 

    <br>

    <span class="tdLookalike">
        <table cellspacing=2 cellpadding=0 border=0 align="left">
            <tr>

                <td valign="top">
                     x
                </td>
<form name="pane2_jscript_displays_form"> 
                <td valign="top">
                     <input name="x_val" type="text" value="0" size=3>
                </td>
                <td valign="top">
                     <input name="y_val" type="text" value="0" size=3>
                </td>
</form>
                <td valign="top">
                    y
                <td></td><td></td><td></td><td></td><td></td>
                <td valign="top">
                    select image ::
                </td>
                <td valign="top">
                    <form name="scoringFormPane2">
                        <select name="the_image_select" OnChange="changeScoringImage(2);">
                            <?=$imageSelectOptionPane2String?>
                        </select>
                    </form>
                </td>
            </tr>
        </table>
    </span>
</div>


<!--
   BUILD THE LAYERS WITH THE TRANSPARENT PICTURES.  THEY SIT ATOP THE REAL PICTURES
   AND SERVE AS ISMAPS...  WE COULDN'T FIGURE OUT HOW TO DO THAT ANY OTHER WAY WHILE
   STILL KEEPING THE PICTURES DYNAMIC VIA JAVASCRIPT
 -->
<div id="pane1TransparentPictureLayer" style="position:absolute; 
width:<?=$wFrame1?>px; height:<?=$hFrame1?>px;
z-index:2; left: <?=$xFrame1?>px; top: <?=$yFrame1?>px;">
    <form name="imageClickForm" method="post" action="hiddenFrame.php" target="hiddenFrame">
        <input name="imageField" type="image" src="img/transparent.gif" ISMAP>
        <input type="hidden" name="hiddenWhichPane" value="1">
    </form>
</div>

<div id="pane2TransparentPictureLayer" style="position:absolute;
width:<?=$wFrame2?>px; height:<?=$hFrame2?>px;
z-index:2; left: <?=$xFrame2?>px; top: <?=$yFrame2?>px;">
    <form name="imageClickForm" method="post" action="hiddenFrame.php" target="hiddenFrame">
        <input name="imageField" type="image" src="img/transparent.gif" ISMAP>
        <input type="hidden" name="hiddenWhichPane" value="2">
    </form>
</div>



<!--
   BUILD THE LAYER WITH THE CONTROLS IN COMMON
-->
<div id="commonControlLayer" style="position:absolute; 
width:<?=$wFrameSharedControls?>px; height:<?=$hFrameSharedControls?>px;
z-index:2; left: <?=$xFrameSharedControls?>px; top: <?=$yFrameSharedControls?>px;">

    <table cellspacing=0 cellpadding=0 border=0>
        <tr valign="top">
            <td>
<!-- the scoring form for the current cell -->
<!-- include a space for comments -->
                <form name="cell_data" method=post action='<?=$_SERVER['PHP_SELF']?>'>
                    <table cellspacing=0 cellpadding=0 border=0 align="left">
                        <input type="hidden" name="localization" value="marker">
                        <input type="hidden" name="cell_cycle" value="marker">
                        <input type="hidden" name="cellmorphology" value="marker">
                        <input type="hidden" name="cellbrightness" value="marker">
                        <input type="hidden" name="subcellhomogeneity" value="marker">
                        <tr>
                            <td width=100 align="center"><b>x</b></td>
                            <td width=100 align="center"><b>y</b></td>
                            <td width=90 align="center"><b>cell cycle stage</b></td>
                            <td width=90 align="center" ><b>localization(s)</b></td>
                            <td width=90 align="center"><b>cell morphology</b></td>
                            <td width=90 align="center"><b>cell's relative brightness</b></td>
                            <td width=90 align="center"><b>subcellular gfp distribution</b></td>
                            <td width=90 align="center"><b><font color="#CC6633">&gt;&gt; done w/set</font></b></td>
                        </tr>
                        <tr valign="top">
                            <td align="center"><input name="x_val" type="text" value="0" size=9>
                            </td>
                            <td align="center"><input name="y_val" type="text" value="0" size=9>
                            </td>
                            <td width=100 align="center">
                                <select name="cell_cycle" size="5">
<?=$cellCycleSelectOptionString?>
                                </select>
                            </td>
                            <td width=100 align="center">
                                <select multiple name="localization[]" size=15>
<?=$locationSelectOptionString ?>
                                </select>
                            </td>
                            <td align="center">
                                <select multiple name="cellmorphology" size=5>
<?=$cellMorphologySelectOptionString ?>
                                </select>
                            </td>
                            <td align="center">
                                <select multiple name="cellbrightness" size=5>
<?=$cellBrightnessSelectOptionString ?>
                                </select>
                            </td>
                            <td align="center">
                                <select multiple name="subcellhomogeneity" size=5>
<?=$subcellHomogeneitySelectOptionString ?>
                                </select>
                            </td>
                            <td align="right" valign="middle">&nbsp;&nbsp; 		
                                <input type="image" src="img/score_button.gif"
                                 name="buttonScoreCell"
                                 alt="add current cell to list of scored cells">&nbsp;&nbsp;
                                 <!-- hidden variables not included in form -->
                                 <input type=hidden name='setid' value='<?=$currentSet?>'>
                                 <input type=hidden name='scoredby' value='<?=$_SESSION['userid']?>'>

                            </td>

                        </tr>
                    </table>
                </form>
            </td>
        </tr>
    </table>
</div>




<div id="deleteLocalizationControlsLayer" style="position:absolute;
width:<?=$wFrameDeleteControls?>px; height:<?=$hFrameDeleteControls?>px;
z-index:2; left: <?=$xFrameDeleteControls?>px; top: <?=$yFrameDeleteControls?>px;">
    <table border=0>
        <tr>
            <td width = <?=$wFrameDeleteControls?> align="left" valign="top">
                <form name="finished" method=post 
                 action='<?=$_SERVER['PHP_SELF']?>'>
                    <input type=image src="img/next_button.gif"
                     name="buttonNextSet"
                     alt="finish with this set and begin another">
                    <input type=hidden name="setid" value="<?=$currentSet?>">
                </form> 
            </td>
        </tr>
        <tr>
            <td><b><font color="#CC6633">&gt;&gt; scored cells</font></b><td>
        </tr>

<!-- THE FORM FOR THE REMOVAL OF LOCALIZATIO  -->
        <tr>
            <td align="center">
                <form name="scored" method=post action='<?=$_SERVER['PHP_SELF']?>'> 
                    <SELECT multiple size=5 name="remove_cells[]">

<? /* INSERT THE LOCALIZATION OPTIONS */
print $localizationSelectOptionString;
?>
                    </select>
                    <input type=hidden name="setid" value="<?=$currentSet?>">
                    <input type=image src="img/remove_button.gif"
                     name='buttonRemoveScoring' alt='remove cells'>
                </form>
            </td>
            <td>
<? if (!empty($removed)) { print "<br>removed ".$removed." records"; } ?>
            </td>
        </tr>
        <tr>
            <td> <?=$setInfoMsg?> </td>
        </tr>
    </table>
</div>

<?
printfooter();
?>
