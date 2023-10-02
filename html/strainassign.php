 <?
require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/secure.php");
require("$include_dir/projects_inc.php");

pass($priv['Superuser']);

$delimiter1 = "_";
$delimiter2 = "-";

/** Alternating colors for set groupings **/
$color1 = "#FFFFFF";
$color2 = "#FFCC99";

$upload_dir = $upload_base .$_SESSION['projectid'] ."/";
printWB($upload_dir);

// Build sorted hash with files in upload directory
// File example: Plate03B-11_w1DAPI_ver00-A-2000ms.tif  

assert(file_exists($upload_dir) && is_dir($upload_dir));
$handle = @opendir($upload_dir);
while (false !== ($file = readdir($handle))) {

  
    /** skip files that start with . **/
    if (preg_match("/^\./", $file)) {
	continue;
    }

    /** strip .tif from name **/
    if(!preg_match("/^(.*)\.tif$/i", $file, $matchkey)) {
	continue;
    }	    

    $name = $matchkey[1];

    /** associative arrayed keyed by filename w/o .tif **/
    $dirarray[$name] = $file;
    
}
closedir ($handle);

if ($dirarray) { ksort ($dirarray); }
else {
    centermsg("There are no unassigned images for project <i>".$_SESSION['project']."</i>.");
    exit; 
}

// SQL statements and queries
$sqlstrains = "SELECT strains.strainid, strains.strainname, orfs.orfnumber, orfs.orfname 
               FROM strains, orfs 
               WHERE strains.orfid = orfs.orfid
               AND strains.tag='GFP'";
$sqlconds = "SELECT * FROM condition";
$sqlstains = "SELECT * FROM stain";

// these resource handles are analogous to filehandles

$resstrains	= dbquery($sqlstrains);
$resconds	= dbquery($sqlconds);
$resstains	= dbquery($sqlstains);

// can probably eliminate this, too, because we're not making the assignment of strain alterable at the assign stage
// not going to now because can use having list in memory rather than having to query database repeatedly for same info.
// Copy (huge) strains list into memory for later iteration

while ($row = mysqli_fetch_assoc($resstrains)) {
    $strain[$row["strainid"]] = $row["strainname"] ." (" .$row["orfnumber"] ." / " .$row["orfname"] .")";
}

print "<form method=post action=strainassignproc.php>\n
<center><h2><u>Unassigned Images For Project <i>".$_SESSION['project']."</i></u></h2>";
?>
<table width=100% cellspacing=0>
<tr><th>Include:</th>
	<th>Image:</th>
	<th>Stain:</th>
	<th>Strain:</th>
	<th>Library:</th>
	<th>Strain Background:</th>
	<th>Condition:</th></tr>
<tr><td colspan=6><hr></td></tr>
<?

class SetDescriptionClass {
    var $membSetUniqueIdentifier;

    var $membGFPFileFullPath;
    var $membDICFileFullPath;
    var $membDAPIFileFullPath;
    var $membRFPFileFullPath;

    
    function SetDescriptionClass() {
	//CAN'T FIGURE THIS OUT!
	//$this->membImageMembers = new array();
    }

    /*
    function getStainPath($stainID) {
	$sqlStains = "SELECT * FROM stain
                      WHERE stainID = ".$stainID;
	$resStains = dbquery($sqlStains);
	while ($row = mysqli_fetch_assoc($resStains)) {
	    if($row['stainid'] == $stainID) {
		r
	
	if ($stainID == 1
    */
    
}


// Traverse the sorted hash: do automatic grouping, stain, strain assignment
$pattern = "";

/* REMEMBER WHICH SETS WE'VE STARTED */
$extantSetList = array();
$setDescriptionClassList = array();
$firstRow = 1;

/** w1DAPI-ver00.A-2000ms **/
foreach($dirarray as $key => $filefull) {
    /** redundant renaming, but will save recoding **/
    $file = $key;

    /** Grab PLATEWELLINFO, STAIN from key (name w/o .tif) **/
    list ($plateWellInfo,$stain,$version) = explode ($delimiter1, $key);

    // backwards compat.
    $setname = $plateWellInfo;
    

    /** grab plate info (includes library letter) and position from plateWellInfo **/
    list ($plate, $position) = explode ($delimiter2, $plateWellInfo);
    
    //    print $file."&nbsp;\t".$version."&nbsp;\t".$stain."&nbsp;<br>\n";

    $setUniqueIdentifier = $plateWellInfo."-".$version;
    //    printWB($setUniqueIdentifier);

    //			      $extantSetList[] = $setUniqueIdentifier
    

    
    /* Check for new set */
    if (!array_key_exists($setUniqueIdentifier, $setDescriptionClassList)) {
	$temp = new SetDescriptionClass;
	$temp->membSetUniqueIdentifier = $setUniqueIdentifier;	
	$setDescriptionClassList[$setUniqueIdentifier] = $temp;
    }
    if(preg_match("/GFP/", $filefull)) {
	$setDescriptionClassList[$setUniqueIdentifier]->membGFPFileFullPath = $filefull;
    }
    if(preg_match("/DIC/", $filefull)) {
	$setDescriptionClassList[$setUniqueIdentifier]->membDICFileFullPath = $filefull;
    }
    if(preg_match("/DAPI/", $filefull)) {
	$setDescriptionClassList[$setUniqueIdentifier]->membDAPIFileFullPath = $filefull;
    }
    if(preg_match("/RFP/", $filefull)) {
	$setDescriptionClassList[$setUniqueIdentifier]->membRFPFileFullPath = $filefull;
    }
}

$dirArrayResorted = array();

foreach($setDescriptionClassList as $unique => $setDescriptionObject) {

  // step through for each image in Class
  // do some detection for missing filepaths -- determine which kind of experiment
  
  if (!isset($setDescriptionObject->membDAPIFileFullPath)) {
    // define this as a coloc expt. 
    $exptType = "colocalization";
  } else if (!isset($setDescriptionObject->membRFPFileFullPath)) {
    // define this as a standard expt.
    $exptType = "standard";    
  } else {	    
    $exptType = "";
    print "Missing file for:".$unique."<br>";
    continue;
  }
	

  for($i=0; $i<3; $i++) {
    if ($exptType == "standard") {
	if($i == 1) {
	    $filefull = $setDescriptionObject->membGFPFileFullPath;
	}
	else if($i == 2) {
	    $filefull = $setDescriptionObject->membDICFileFullPath;
	}
	else {
	    $filefull = $setDescriptionObject->membDAPIFileFullPath;
	} 
    } else if($exptType == "colocalization") {
	if($i == 1) {
	    $filefull = $setDescriptionObject->membGFPFileFullPath;
	}
	else if($i == 2) {
	    $filefull = $setDescriptionObject->membDICFileFullPath;
	}
	else {
	    $filefull = $setDescriptionObject->membRFPFileFullPath;
	} 
    } else {
      print "Missing file:".$unique."<br>";
      continue;
    }

    //    printWB($filefull);

    if($filefull == "") {
      print "Missing file:".$unique."<br>";
      continue;
    }  
    assert(preg_match("/(.*)(.tif)/", $filefull, $matches));
    //    printWB("");
      
    $key = $matches[1];
    //    printWB("");
    //	print $key."<br>";
    
    /* THIS IS TERRIBLE!  WE NEEEED TO FIX THIS.  HERE WE DEPEND ON THE ORDER  UGH.*/
    $dirArrayResorted[$key] = $filefull;
    //print_r($dirArrayResorted);
    ////////////////////////////////
    
    /** redundant renaming, but will save recoding **/
    $file = $key;
    
    /** Grab SETNAME, STAIN from key (name w/o .tif) **/
    list ($setname,$stain,$version) = explode ($delimiter1, $key);
    
    /** grab plate info (includes library letter) and position from setname **/
    list ($plate, $position) = explode ($delimiter2, $setname);
    
    // print $file."&nbsp;\t".$version."&nbsp;\t".$stain."&nbsp;<br>\n";
    
    /* Check for new set */
    if ($setDescriptionObject->membSetUniqueIdentifier <> $pattern) {
      if ($pattern) { print "<tr><td colspan=6 bgcolor=$color>&nbsp;</td></tr>\n"; }
      if ($color == $color2) { $color = $color1; }
      else { $color = $color2; }
      
      /** Display include checkbox **/
      print "<tr><td align=center bgcolor=$color><input type=checkbox name='".$setDescriptionObject->membSetUniqueIdentifier."' CHECKED></td>";
      $newset = 1;
    }
    else {
      print "<tr><td bgcolor=$color></td>";
      $newset = 0;
    }
    
    /** Display image name **/
    print "<td align=center bgcolor=$color><font color=blue><b>$filefull</b></td>";
    
    /** Display stains **/
    print "<td align=center bgcolor=$color>&nbsp;\n";
    
    /** currently, this matches every occurence in the stain table -- should alter to throw an error if there is more than one **/
    /** start at the beginning **/
    mysqli_data_seek ($resstains, 0);
    while ($row = mysqli_fetch_assoc($resstains)) {
      
      /** match the stain "ext" from the database in the $stain **/
      if (preg_match ("/".$row["ext"]."/i", $stain)) {
	
	/** print the matching stain name **/
	print $row["stainname"]."\n";
	print "<input type=hidden name='".$file."-STAIN' value='".$row["stainid"]."'>";
      }
    }
    
    /** include decriptive information **/
    print "<input type=hidden name='".$file."-DESC' value='".$version."'>";
    print "</td>\n";
    
    if ($newset) {
      
      /** Match, grabbing plate type, #, and library letter **/
      assert(preg_match("/(\w+\D)(\d+)(\w)/", $plate, $platematch));
      
      /** Always get the first number if there's more than one **/
      $plateType = $platematch[1];
      $platenumber = $platematch[2];
      $library = $platematch[3];

      /*
      print "plate: ".$plate;
      print "platematch[1]: ".$platematch[1];
      print "platematch[2]: ".$platematch[2];
      print "platematch[3]: ".$platematch[3];
      */
      
      /** get strainid from strains using plateinfo (plate, position) and strians (library) **/
            $platenumber = 0 + $platenumber; // ensures an integer value
      
      //      print $plateType;
      $sqlplateinfo = "SELECT strains.strainid FROM strains
 INNER JOIN plateinfo ON strains.strainid=plateinfo.strainid
   WHERE experimentercomment like '".$plateType.$platenumber.$library."' 
 AND position = $position
 AND tag='GFP'";
      //  AND library = '".$library."'

      //      		printWB( $sqlplateinfo);
      //exit;
      $resplate = mysqli_fetch_assoc(dbquery($sqlplateinfo));
      
      /** Display matched strain **/
      print "<td align=center bgcolor=$color>";
      

      //failing to match strain....


      if($strain[$resplate["strainid"]] == "") {
	//	print $plate." is plate <br>".$position;
	print "failed to match strain for ".$plateType.$platenumber.$library."-".$position;
      }
      print $strain[$resplate["strainid"]];
      print "<input type=hidden name='".$setDescriptionObject->membSetUniqueIdentifier."-STRAIN' value='".$resplate["strainid"]."'>";
      print "</td>";
      
      print "<td align=center bgcolor=$color>$library</td>";
      
      /** Display conditions **/
      print "<td align=center bgcolor=$color>
<SELECT name='".$setDescriptionObject->membSetUniqueIdentifier."-CONDITION'>\n";
      mysqli_data_seek ($resconds, 0);
      while ($row = mysqli_fetch_assoc($resconds)) {
	print "<OPTION value=". $row["conditionid"]. ">".$row["conditionname"]."\n"; }
      print "</SELECT></td>\n";
    }
    else { print "<td colspan=3 bgcolor=$color></td>"; }
    print "</tr>";
    $pattern = $setDescriptionObject->membSetUniqueIdentifier;
    
  }
  
  
}

?>
<tr><td colspan=6><hr></td></tr>
</table>
<font color=red size=+2><center><b>BIG FAT WARNING HERE!<br></font><font color=red>
Note: <u>Unchecked</u> files may become ungrouped from their proper sets!<br>
...Are you sure <b>set groupings, stain, strain, and conditions</b> are all correct??...
<br>This will take a while.  Don't hit cancel!
<p><center><input type=submit name=btnAssign value=Assign>
<?

// Send sorted hash $dirarray, $delimiter1, $upload_dir as hidden variables
print "<input type=hidden name=dirarray value=\"".urlencode(serialize($dirArrayResorted))."\">";
print "<input type=hidden name=delimiter1 value=\"".urlencode(serialize($delimiter1))."\">";
print "<input type=hidden name=delimiter2 value=\"".urlencode(serialize($delimiter2))."\">";
print "<input type=hidden name=upload_dir value=\"".urlencode(serialize($upload_dir))."\">";

printfooter();
?>
