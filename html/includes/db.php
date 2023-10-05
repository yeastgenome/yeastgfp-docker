<?php

// FUNCTIONS FOR INTERACTING WITH OUR GFP DATABASE TABLE STRUCTURE
// YES INDIDDY
// MAKE AN INDEX.....

// MySQL connection parameters
$dbserver	= getenv('DBSERVER');
$dbname		= getenv('DBNAME');
$dbuser		= getenv('DBUSER');
$dbpwd		= getenv('DBPWD');

// Open MySQL connection, connect to gfp database
//		Use mysql_connect instead of mysql_pconnect because persistent
//		connections may quickly saturate MySQL's connection limit
function opendb() {
  // global $dbserver, $dbname, $dbuser, $dbpwd, $dbconn;
  global $dbconn;

  print("<p<DEBUG: dbserver is $dbserver</p>");
  print("<p>DEBUG: dbname is $dbname</p>");
  print("<p>DEBUG: dbuser is $dbuser</p>");
  print("<p>DEBUG: dbpwd is $dbpwd</p>");	

  $dbconn = mysqli_connect($dbserver, $dbuser, $dbpwd, $dbname);
  if (!$dbconn) { die("<p><b>ERROR! Cannot connect to MySQL.</b><p>"); }
}


function dbquery($sqlinput) {
  global $dbconn;
  if (!$dbres = mysqli_query($dbconn, $sqlinput)) {
    die("<p>Could not perform the SQL query: <b>'$sqlinput'</b><p>
			MySQL Error <b>".mysqli_errno()."</b> : <b>".mysqli_error()."</b><p>");
    //		return false;
  }	
  else { return $dbres; }
}


function dbnumrows($resourceresult) {
  return mysqli_num_rows($resourceresult);
}


function closedb() {
  global $dbconn;
  mysqli_close($dbconn);
}



/* TURN ON ROW-LEVEL LOCKING MODE TRANSACTIONS FOR INNODB TABLES */
function innoDbStartTransaction() {
  $sqlSetAutocommitOff = "SET AUTOCOMMIT=0";
  dbquery($sqlSetAutocommitOff);
  $sqlBegin = "BEGIN";
  dbquery($sqlBegin);
}


function innoDbEndTransaction() {
  $sqlDoCommit = "COMMIT";
  dbquery($sqlDoCommit);
  $sqlSetAutocommitOn = "SET AUTOCOMMIT=1";
  dbquery($sqlSetAutocommitOn);
}


function innoDbAbortTransaction() {
  $sqlDoCommit = "ROLLBACK";
  dbquery($sqlDoCommit);
  $sqlSetAutocommitOn = "SET AUTOCOMMIT=1";
  dbquery($sqlSetAutocommitOn);
}



/* TAKES ALL THE ROWS IN THE TABLE WITH A GIVEN KEY VALUE AND ASSIGNS A GIVEN COLUMN
   A GIVEN VALUE
*/
function setSynchronizedKeyList($tableName, $keyName, $keyValList, $lockName, $lockVal) {
  
  /* FIRST SELECT THEM ALL AND MARK THEM FOR UPDATE */
  $sqlGetRowsOfInterest = "SELECT * FROM ".$tableName." ";
  foreach ($keyValList as $key => $val) {
    if($key == 0) {
      $sqlGetRowsOfInterest .= "WHERE ";
    } else {
      $sqlGetRowsOfInterest .= " OR ";
    }
    $sqlGetRowsOfInterest .= $keyName." = ".$val;
  }
  $sqlGetRowsOfInterest .= " FOR UPDATE";

  /* NOW WRITE THE NEW VALUE INTO ALL OF THEM */
  $sqlSetRowsOfInterest = "UPDATE ".$tableName." set ".$lockName."='".$lockVal."' ";
  foreach ($keyValList as $key => $val) {
    if($key == 0) {
      $sqlSetRowsOfInterest .= "WHERE ";
    } else {
      $sqlSetRowsOfInterest .= " OR ";
    }
    $sqlSetRowsOfInterest .= $keyName." = ".$val;
  }

  /* DO ALL THE REAL WORK, AND RETURN TRUE IF THERE WERE NO ISSUES */
  //  print $sqlSetRowsOfInterest;
  if(dbquery($sqlGetRowsOfInterest)) {
    assert(dbquery($sqlSetRowsOfInterest));
    return "T";
  }
  return "F";
  
}



function locToOrfnumberMap($in) {
  /* A KINDER, GENTLER WAY */
  $sql = "select orfnumber from localization inner join sets on localization.setid=sets.setid inner join strains on strains.strainid=sets.strainid inner join orfs on strains.orfid=orfs.orfid where localizeid=$in limit 1";
  $res = dbquery($sql);
  $row = mysqli_fetch_assoc($res);
  return $row['orfnumber'];
}


function locToStrainidMap($in) {
  /* A KINDER, GENTLER WAY again.... */
  $sql = "select strains.strainid from localization inner join sets on localization.setid=sets.setid inner join strains on strains.strainid=sets.strainid where localizeid=$in limit 1";
  $res = dbquery($sql);
  $row = mysqli_fetch_assoc($res);
  return $row['strainid'];
}

function getOrderedSubcellList() {
  $sql = "select * from subcell where showingraphs='T'";
  $res = dbquery($sql);
  return makeArrayFromResColumn($res);
}


function filterLocListToMakeValid2($list) {
  $validList = array();
  $setList = array();
  foreach($list as $loc) {
    $setid = getOneToOneMatch("localization", "localizeid", $loc, "setid");
    if(!in_array($setid, $setList)) {
      $setList[$setid] = array();
    }
    $setList[$setid][] = $loc;
  }

  foreach($setList as $setid => $locList) {
    $tempList = filterLocListToMakeValidAllFromSameSet($locList);
    $validList = array_merge($validList, $tempList);
  }

  return $validList;
}

/* IF A CONSENSUS SCORING TRUMPS OTHER USERS' SCORING, USE THE CONSENSUS!  */
/* EXCLUDE LOCS FROM NO_GFP SETS */

function filterLocListToMakeValid($list) {

  $newList = array();
  foreach($list as $loc) {
    $setid = getOneToOneMatch("localization", "localizeid", $loc, "setid");
    //    $tmp = getOneToOneMatch("sets", "setid", $setid, "new_prune_complete");
    //$tmp2 = getOneToOneMatch("sets", "setid", $setid, "new_no_gfp_visible");
    if(isSetPrunedScore($setid)) {
      $newList[] = $loc;
    }
  }

  $list = $newList;

  
  $retList1 = array();
  $retList2 = array();

  $useC = false;
  foreach($list as $val) {
    if(getOneToOneMatch("localization", "localizeid", $val, "userid") == 11) {
      $retList2[] = $val;
      $useC = true;
    } else {
      $retList1[] = $val;
    }
  }
  

  if($useC) {
    return $retList2;

  }
  return $retList1;
}

  
function strainToLocMap($in) {
  $sql = "select * from sets inner join localization on localization.setid=sets.setid where most_current='T' and localization.superceded_by_derived_locid is null and sets.strainid=".$in;
  $res = dbquery($sql);
  return makeArrayFromResColumn($res,"localizeid");
}

function strainToFinalLocMap($in) {
  $sql = "select * from sets inner join localization on localization.setid=sets.setid where most_current='T' and most_final='T' and localization.superceded_by_derived_locid is null and sets.strainid=".$in;
  $res = dbquery($sql);
  return makeArrayFromResColumn($res,"localizeid");
}


function orfidToLocMap($in) {
  $retList = array();
  if ($in != "") {
    $list = getOneToMany("strains", "orfid", $in, "strainid");
    foreach($list as $val) {
      $list2 = strainToLocMap($val);
      $retList = array_merge($retList, $list2);
    }
  }
  return $retList;
}

function orfidToFinalLocMap($in) {
  $retList = array();
  $list = getOneToMany("strains", "orfid", $in, "strainid");
  foreach($list as $val) {
    $list2 = strainToFinalLocMap($val);
    $retList = array_merge($retList, $list2);
  }
  return $retList;
}


function listInToOneInConvert($inList, $func) {
  $retList = array();
  if (count($inList) == 0) {
  } else {	 
 	foreach($inList as $val) {
    	   $list = $func($val);
   	  $retList = array_merge($retList, $list);
  	}

}
  $retList = array_unique($retList);
  return $retList;
}


function orfidListToLocMap($inList) {
  return listInToOneInConvert($inList, "orfidToLocMap");
}

function getOrfidFromLocid($locid) {
  $setid = getOneToOneMatch("localization", "localizeid", $locid, "setid");
  $strainid = getOneToOneMatch("sets", "setid", $setid, "strainid");
  $orfid = getOneToOneMatch("strains", "strainid", $strainid, "orfid");
  return $orfid;
}


function strainidListToLocMap($inList) {
  return listInToOneInConvert($inList, "strainToLocMap");
}


function orfnumberOrOrfnameListToOrfidList($in) {
  $retArray = array();
  foreach($in as $val) {
    $val = convertOrfNameToOrfNumber($val);
    $retArray[] = getOneToOneMatch("orfs", "orfnumber", $val, "orfid");
  }
  return $retArray;
}


function convertOrfnumberOrOrfnameToOrfid($in) {
  $in = convertOrfNameToOrfNumber($in);
  $out = getOneToOneMatch("orfs", "orfnumber", "'".$in."'", "orfid");
  return $out;
}

function convertOrfnumberOrOrfnameListToOrfidList($in) {

  // CALLED BY QUERY.PHP, INPUT IS A SPACE-DELIMITED TEXT FIELD
  // RETURNS ARRAY OF ARRAYS: orfsToSearch (as ids), alias (as input), not found (as input)

  $aliasResults = checkAliases($in);
 
  if (count($aliasResults[0]) == 0) {
	$retArray[] = array();
} else {
  // convert the orfsToSearch to orfids
  foreach ($aliasResults[0] as $val) {
    $tmpArray[] = convertOrfnumberOrOrfnameToOrfid($val);    
  }
  
  $retArray[] = $tmpArray;
}
  // if there are either alias issues or not found orfs,
  // include both arrays in returned array
  if (isset($aliasResults[1]) || isset($aliasResults[2])) {
    $retArray[] = $aliasResults[1];
    $retArray[] = $aliasResults[2];
  }
  
  return $retArray;

}

function convertOrfidToOrfnumberOrOrfname($in) {
  $out = getOneToOneMatch("orfs", "orfid", "'".$in."'", "orfname");
  if($out != null) {
    return $out;
  }

  $out = getOneToOneMatch("orfs", "orfid", "'".$in."'", "orfnumber");
  assert($out != null);
  return $out;
}


function doesOrfHaveLocWithSubcellX($orfid, $subcellid) {
  $sql = "select * from orfs inner join strains on strains.orfid=orfs.orfid inner join sets on sets.strainid=strains.strainid inner join localization on localization.setid=sets.setid where orfs.orfid=$orfid and localization.subcellid=$subcellid LIMIT 1";
  $res = dbquery($sql);
  if(mysqli_num_rows($res) == 0) {
    return false;
  }
  return true;
}


function doesOrfHaveOnlyLocsWithSubcellX($orfid, $subcellid) {
  $sql = "select * from orfs inner join strains on strains.orfid=orfs.orfid inner join sets on sets.strainid=strains.strainid inner join localization on localization.setid=sets.setid where orfs.orfid=$orfid and localization.subcellid=$subcellid LIMIT 1";
  $res = dbquery($sql);
  if(mysqli_num_rows($res) == 0) {
    return false;
  }
  $sql = "select * from orfs inner join strains on strains.orfid=orfs.orfid inner join sets on sets.strainid=strains.strainid inner join localization on localization.setid=sets.setid where orfs.orfid=$orfid and localization.subcellid!=$subcellid LIMIT 1";
  $res = dbquery($sql);
  if(mysqli_num_rows($res) != 0) {
    return false;
  }

  return true;
}


function doesOrfHaveOnlyLocsWithSubcellsXOrY($orfid, $subcellidX, $subcellidY) {
  $sql = "select * from orfs inner join strains on strains.orfid=orfs.orfid inner join sets on sets.strainid=strains.strainid inner join localization on localization.setid=sets.setid where orfs.orfid=$orfid and localization.subcellid=$subcellidX";
  $res = dbquery($sql);
  $numX = mysqli_num_rows($res);

  $sql = "select * from orfs inner join strains on strains.orfid=orfs.orfid inner join sets on sets.strainid=strains.strainid inner join localization on localization.setid=sets.setid where orfs.orfid=$orfid and localization.subcellid=$subcellidY";
  $res = dbquery($sql);
  $numY = mysqli_num_rows($res);


  $sql = "select * from orfs inner join strains on strains.orfid=orfs.orfid inner join sets on sets.strainid=strains.strainid inner join localization on localization.setid=sets.setid where orfs.orfid=$orfid";
  $res = dbquery($sql);
  $numTotal = mysqli_num_rows($res);

  if(($numTotal == $numY + $numX) && $numTotal > 0) {
    return true;
  }
  return false;
}


function doesOrfHaveOnlyConsensusLocsWithSubcellsXOrY($orfid, $subcellidX, $subcellidY) {
  $sql = "select * from orfs inner join strains on strains.orfid=orfs.orfid inner join sets on sets.strainid=strains.strainid inner join localization on localization.setid=sets.setid where orfs.orfid=$orfid and localization.userid=11";
  $res = dbquery($sql);
  $numConsensus = mysqli_num_rows($res);
  if($numConsensus != 0) {
    $extraCondition = " and localization.userid=11";
  } else {
    $extraCondition = "";
  }

  $sql = "select * from orfs inner join strains on strains.orfid=orfs.orfid inner join sets on sets.strainid=strains.strainid inner join localization on localization.setid=sets.setid where orfs.orfid=$orfid and localization.subcellid=$subcellidX";
  $sql .= $extraCondition;
  $res = dbquery($sql);
  $numX = mysqli_num_rows($res);

  $sql = "select * from orfs inner join strains on strains.orfid=orfs.orfid inner join sets on sets.strainid=strains.strainid inner join localization on localization.setid=sets.setid where orfs.orfid=$orfid and localization.subcellid=$subcellidY";
  $sql .= $extraCondition;
  $res = dbquery($sql);
  $numY = mysqli_num_rows($res);


  $sql = "select * from orfs inner join strains on strains.orfid=orfs.orfid inner join sets on sets.strainid=strains.strainid inner join localization on localization.setid=sets.setid where orfs.orfid=$orfid";
  $sql .= $extraCondition;
  $res = dbquery($sql);
  $numTotal = mysqli_num_rows($res);

  if(($numTotal == $numY + $numX) && $numTotal > 0) {
    return true;
  }
  return false;
}



function getAllLocalizations() {
  $sql = "select localizeid from localization";
  $res = dbquery($sql);
  return makeArrayFromResColumn($res,"localizeid");
}


function getAllValidLocalizations() {

  //  $sql = "select localizeid,orfnumber,strains.strainid,sets.setid from localization inner join sets on sets.setid=localization.setid inner join strains on strains.strainid=sets.strainid inner join orfs on orfs.orfid=strains.orfid where most_current='T' and most_final='T' ORDER BY orfname,strains.strainid,sets.setid,localizeid";

  // UPDATED FOR BESTLOCS TABLE
  $sql = "SELECT bestlocs.localizeid, bestlocs.phase, orfs.orfname FROM bestlocs INNER JOIN orfs ON orfs.orfid=bestlocs.orfid WHERE bestlocs.phase='F' ORDER BY orfs.orfname,bestlocs.localizeid";
  $res = dbquery($sql);
  return makeArrayFromResColumn($res,"localizeid");

}


function getSetListFromOrfid($orfid) {
  $sql = "select * from orfs inner join strains on strains.orfid=orfs.orfid inner join sets on sets.strainid=strains.strainid where orfs.orfid=$orfid";
  $res = dbquery($sql);
  return makeArrayFromResColumn($res,"setid");
}
  

function checkAndSetLocked($tableName, $keyName, $conditions, $lockName, $excludedPrimaryKeyList) {

  $sqlGetPotentialsList = "SELECT * FROM ".$tableName." ".
                           $conditions."
                           AND ".$lockName."='F' order by $keyName desc ";
      print  $sqlGetPotentialsList;
  $resGetPotentialsList = dbquery($sqlGetPotentialsList);
  while($potentialKey = mysqli_fetch_assoc($resGetPotentialsList)) {
    //        print "checking for potentialKey: ".$potentialKey[$keyName];
      if(in_array($potentialKey[$keyName], $excludedPrimaryKeyList)) {
	  continue;
      }
      //      ph();
      $sqlGetIndividualRecord = "SELECT * FROM ".$tableName."
                                 WHERE ".$keyName."=".$potentialKey[$keyName]." FOR UPDATE";
      $resGetIndividualRecord = dbquery($sqlGetIndividualRecord);
      $row = mysqli_fetch_assoc($resGetIndividualRecord);
      
      
      /* THIS IS THE VERY LIKELY CASE THAT NO ONE STOLE IT WHILE WE WERE THINKING */
      if($row[$lockName] == 'F') {
	  $sqlSetLocked = "UPDATE ".$tableName." set ".$lockName." = 'T' WHERE ".$keyName."
                           = ".$row[$keyName];
	  dbquery($sqlSetLocked);
	  return $row[$keyName];
      }
      
  }
  return NULL;
}


function getOneToOneMatch($tableName, $leftName, $leftVal, $rightName) {
  //printWB("getOneToOneMatch with leftVal".$leftVal);

  $sql = "SELECT ".$rightName." FROM ".$tableName." WHERE ".$leftName." = ".$leftVal;
  //  printWB($sql);
  $row = mysqli_fetch_assoc(dbquery($sql));
  return $row[$rightName];
}


function getDICDirPathForSet($setid) {
  $list = getOneToMany("images", "setid", $setid, "imageid");
  foreach($list as $val) {
    $stain = getOneToOneMatch("images", "imageid", $val, "stainid");
    if($stain == 4) {
      $a = getOneToOneMatch("images", "imageid", $val, "dirpath");
      //      printWB($a);
      return "/images/".$a;
    }
  }
}

function isSetColoc($setid) {
  $list = getOneToMany("images", "setid", $setid, "imageid");
  foreach($list as $val) {
    $stain = getOneToOneMatch("images", "imageid", $val, "stainid");
    if($stain == 5) {
      return true;
    }
  }
  return false;
}

function isLocColoc($locid) {
  //  printWB("localizeid:".$locid);
  $setid = getOneToOneMatch("localization", "localizeid", $locid, "setid");
  //  printWB("set".$setid);
  //if(isSetColoc($setid)) printWB("coloc");
  return isSetColoc($setid);
}

function isLocMostFinal($locid) {
  $sql = "select * from localization where localizeid=".$locid." and most_final='T'";
  $res = dbquery($sql);
  if(mysqli_num_rows($res) == 0) {
    return false;
  }
  return true;
}

/* THIS FUNCTION CHECKS TO SEE:
1.  IF IT'S MOST_CURRENT
2.  IF SUPERCEDED_BY_DERIVED_LOC IS NULL (WHICH MEANS THAT IT HAS NOT BEEN REPLACED
    BY THE BUD REPLACEMENT CODE (CURRENTLY IN IMPORTS/UPDATESG2BUD))
*/
function isLocValid($locid) {
  $sql = "select * from localization where localizeid=".$locid." and most_current='T'
          and superceded_by_derived_locid is null";
  $res = dbquery($sql);
  if(mysqli_num_rows($res) == 0) {
    return false;
  }
  return true;
}

// utilities
function printWB($a) {
  print $a."<br>\n";
}


function unlockSet($setID) {
	$sql = "UPDATE sets set locked = 'F' where setid = ".$setID;
	dbquery($sql);
}


function getAllOrfs() {
  $sql = "SELECT * FROM orfs";
  $res = dbquery($sql);
  return makeArrayFromResColumn($res,"orfid");
}


function getOneToMany($tableName, $leftName, $leftVal, $rightName) {
  $conditions = $leftName."=".$leftVal;
  return getOneToManyArbConditions($tableName, $conditions, $rightName);
}

function getOneToManyArbConditions($tableName, $conditions, $rightName) {
  $sql = "SELECT * FROM ".$tableName." WHERE ".$conditions;
  $res = dbquery($sql);
  return makeArrayFromResColumn($res, $rightName);
}

function getOneToOneArbConditions($tableName, $conditions, $rightName) {
  $arry = getOneToManyArbConditions($tableName, $conditions, $rightName);
  foreach($arry as $single) {
    return $single;
  }
  return -1;
}



function makeAssociativeArrayFromResColumns($res,$keyColName, $valColName) {
    $retArray = array();
    while($row = mysqli_fetch_assoc($res)) {
	$retArray[$row[$keyColName]] = $row[$valColName];
    }
    ksort($retArray);
    return $retArray;
}


function checkCellImageExists($localizeid) {

  // no longer building clips on the fly....

  
  global $clip_dir;
  global $mog_path;
  $imageSize = 60;
  $offset = $imageSize/2;
  $imageOrder['DAPI'] = 3;
  $imageOrder['GFP'] = 1;
  $imageOrder['DIC'] = 2;

  if (!file_exists($clip_dir.$localizeid.".png")) {
    $sqlGetLocInfo = "SELECT xcoord, ycoord, dirpath, stain.stainid, stain.stainname FROM localization
        INNER JOIN sets ON sets.setid = localization.setid
        INNER JOIN images ON images.setid = sets.setid
        INNER JOIN stain ON stain.stainid = images.stainid
        WHERE localizeid = ".$localizeid;
    $resGetLocInfo = dbquery($sqlGetLocInfo);
    assert(mysqli_num_rows($resGetLocInfo) == 3);
    while ($row = mysqli_fetch_assoc($resGetLocInfo)) {
      $xOffset = $row['xcoord'] - $offset;
      $yOffset = $row['ycoord'] - $offset;
      exec ("cp /home/gfp/images/".$row['dirpath']." ".$clip_dir.$localizeid."_".$row['stainid'].".png");
      exec ($mog_path."mogrify -crop ".$imageSize."x".$imageSize."+".$xOffset."+".$yOffset." ".$clip_dir.$localizeid."_".$row['stainid'].".png");
      $cellImageFilePaths[$imageOrder[$row['stainname']]]  = $clip_dir.$localizeid."_".$row['stainid'].".png";
    }
    ksort($cellImageFilePaths);
    $cellImagesList = arrayToList($cellImageFilePaths," ","");
    exec ($mog_path."montage -geometry ".$imageSize."x".$imageSize." ".$cellImagesList." ".$clip_dir.$localizeid.".png");
    exec ("rm -f ".$cellImagesList);
  }
  
}


// is specific for James and Won-Ki and pho
function getLocChangesWRTUserid() {
  $select = "select * from sets inner join usersxscorecomplete on usersxscorecomplete.setid=sets.setid inner join localization on localization.setid=sets.setid";

  $sameList = array();
  $sameList[] = "setid";

  $changesList = array();
  $changesList[] = "subcellid";

  $WRTValsOfInterest = array();
  $WRTValsOfInterest[] = 3;
  $WRTValsOfInterest[] = 4;
  
  $wrtCol = "userid";
  
  
  return getChangesWRTList($select, $sameList, $changesList, $wrtCol, $WRTValsOfInterest, "setid");
}


function getHomogeneityChangesWRTUserid() {
  $select = "select * from sets inner join usersxscorecomplete on usersxscorecomplete.setid=sets.setid inner join localization on localization.setid=sets.setid";

  $sameList = array();
  $sameList[] = "setid";

  $changesList = array();
  $changesList[] = "subcellhomogeneityid";

  $WRTValsOfInterest = array();
  $WRTValsOfInterest[] = 3;
  $WRTValsOfInterest[] = 4;
  

  //  $wrtList = array();
  $wrtCol = "userid";

  return getChangesWRTList($select, $sameList, $changesList, $wrtCol, $WRTValsOfInterest, "setid");
}



function getBrightnessChangesWRTUserid() {
  $select = "select * from sets inner join usersxscorecomplete on usersxscorecomplete.setid=sets.setid inner join localization on localization.setid=sets.setid";

  $sameList = array();
  $sameList[] = "setid";

  $changesList = array();
  $changesList[] = "cellbrightnessid";

  $WRTValsOfInterest = array();
  $WRTValsOfInterest[] = 3;
  $WRTValsOfInterest[] = 4;
  

  //  $wrtList = array();
  $wrtCol = "userid";

  return getChangesWRTList($select, $sameList, $changesList, $wrtCol, $WRTValsOfInterest, "setid");
}



function getChangesWRTList($sql, $sameColNames, $changesColNames, $WRTColName, $WRTValsOfInterest, $retColName) {
  // array of arrays indexed by the wrt col name
  $present = array();
  
  $res = dbquery($sql);
  while ($row = mysqli_fetch_assoc($res)) {
    // BUILD AN IDENTIFIER STRUCTURE
    //	    $identifierStruct = array();
    $identifierString = "";
    foreach($sameColNames as $keys) {
      $identifierStruct[$keys] = $row[$keys];
      $identifierString .= $keys.":".$row[$keys].",";
    }
    
    foreach($changesColNames as $keys) {
      $identifierStruct[$keys] = $row[$keys];
      $identifierString .= $keys.":".$row[$keys].",";
    }
    
    /*
    foreach ($identifierStruct as $key => $name) {
      print "($key, $name), ";
    }
    */

    $presentKey = $row[$WRTColName];

    // *******HACK*********
    if(!in_array($row[$WRTColName], $WRTValsOfInterest)) {
      continue;
    }

    if(!isset($present[$presentKey])) {
      $present[$presentKey] = array();
    }

    $present[$presentKey][] = $identifierString;
  }
  
  foreach($present as $key=>$val) {
    $present[$key] = array_unique($present[$key]);
  }
  
  
  $first = true;
  foreach($present as $key=>$val) {
    if($first == true) {
      $firstKey = $key;
    }
    $first = false;
  } 

  $diffArrayTotal = array();
  foreach($present as $key=>$val) {
    if($key != $firstKey) {
      //      printWB($key);
      //      printWB($firstKey);
      //printWB(count(array_diff($present[$key], $present[$firstKey])));
      //printWB(count(array_diff($present[$firstKey], $present[$key])));
      
      $diffArray1 = array_diff($present[$key], $present[$firstKey]);
      //print_r($diffArray1);
      $diffArray2 = array_diff($present[$firstKey], $present[$key]);
      //print_r($diffArray2);
      $diffArrayTotal = array_merge($diffArrayTotal, $diffArray1);
      $diffArrayTotal = array_merge($diffArrayTotal, $diffArray2);
      //print_r($diffArrayTotal);

    }
  }

  $retList = array();
  foreach($diffArrayTotal as $member) {
    $matchStr = "/$retColName:(\d+),/";
    //printWB($matchStr);
    preg_match($matchStr, $member, $match);
    //printWB("here");
    // printWB("****".$match[1]);
    $retList[$match[1]] = "here";
  }

  $retList2 = array_keys($retList);

  //print_r($retList2);
  return $retList2;
}


function convertOrfNameToOrfNumber($orfName) {

  /* TAKES GENE NAME OR ORFNUMBER AS INPUT */
  /* RETURNS CORRESPONDING ORFNUMBER -- OR INPUT AND ERROR */
  /* IF NO ORFNAME IS FOUND, OR SEVERAL ARE FOUND */
  
  if (preg_match("/[Yy][a-pA-P][lrLR]\d\d\d[cwCW]/",$orfName)) {
    return $orfName;
  } else {
    $sql = "SELECT * FROM orfs WHERE orfname like '".$orfName."'";
    $res = dbquery($sql);
    
    if (mysqli_num_rows($res) == 1) {
      while ($row = mysqli_fetch_assoc($res)) {
	$orf = $row['orfnumber'];
      }
      return $orf;
    } elseif (mysqli_num_rows($res) == 0) {
      return $orfName." NOT_FOUND";
    } else {
      return $orfName." AMBIGUOUS";
    }
  }
} 


class OrfDisplay {
  var $orfid; //
  var $orfnumber; //
  var $orfname; //
  var $essential; //
  var $size; //
  var $nameRecognized;  //
  var $tagged; //
  var $visualized; //
  var $color = "EEEEEE";

  var $assocArraySubcellToLoc;

  function OrfDisplay($orfNumber) {
    printWB("called orfdisplay on ".$orfNumber);

    $this->orfnumber = $orfNumber;
    ph();
    $orfidString = getOneToOneMatch("orfs", "orfnumber", singleQuote($orfNumber), "orfid");
    if($orfidString != "") {
      printWB("found the orfnumber");
      $nameRecognized = true;
    } else {
      $nameRecognized = false;
      return;
    }

    /* ORFID */
    $this->orfid = 0 + $orfidString;
    //printWB("orfid=".$this->orfid);
    
    /* ORFNAME */
    $this->orfname = getOneToOneMatch("orfs", "orfid",$this->orfid, "orfname");

    /* TAGGED */
    $sql = "SELECT * FROM orfs INNER JOIN strains ON strains.orfid=orfs.orfid where strains.tag_success='T' and strains.tag='GFP' and orfs.orfid=".$orfidString;
    $res = dbquery($sql);
    $numRows = mysqli_num_rows($res);
    printWB($numRows);
    $tagged = false;
    if($numRows > 0) {
      printWB("it was tagged");
      $tagged = true;
    }
    printWB("hfds");

    /* VISUALIZED */
    $sql = "SELECT localization.localizeid FROM orfs INNER JOIN strains ON strains.orfid=orfs.orfid INNER JOIN sets on sets.strainid=strains.strainid INNER JOIN localization on localization.setid=sets.setid where orfs.orfid=" . $orfidString;
    $res = dbquery($sql);
    //    print_r($res);
    printWB($sql);
    $locs = makeArrayFromResColumn($res, "localizeid");
    $locs2 = filterLocListToMakeValid($locs);
    $this->visualized = false;
    if(count($locs2) > 0) {
      $this->visualized = true;
    }
    
    /* ESSENTIAL */
    $this->essential = (getOneToOneMatch("orfs", "orfid",$this->orfid, "essential") == 'T');

    /* SIZE */
    $this->size = 0 + getOneToOneMatch("orfs", "orfid",$this->orfid, "size");
  }
  
  function buildOrfDisplayHTML() {
    $retStr = "";
    $retStr .= "<p class=\"locLabel\">\n";
    $retStr .= "<table width=700 bgcolor=#".$this->color.">\n"; 
    $retStr .= "<td width=240>\n";
    $retStr .= "<b>strain</b>\n";
    $retStr .= "</td>\n";
    $retStr .= "<td width=140>\n";
    $retStr .= "<b></b>\n";
    $retStr .= "</td>\n";
    $retStr .= "<td width=140>\n";
    $retStr .= "placeholder"."\n";
    $retStr .= "</td>\n";
    $retStr .= "<td>\n";
    $retStr .= "<b>id</b>: ".$this->orfid."\n";
    $retStr .= "</td>\n";
    $retStr .= "<td>\n";
    $retStr .= "<b>orname</b>: ".$this->orfname."\n";
    $retStr .= "</td>\n";
    $retStr .= "<td>\n";
    $retStr .= "<b>orfnumber</b>: ".$this->orfnumber."\n";
    $retStr .= "</td>\n";
    $retStr .= "</tr>\n";
    $retStr .= "<tr>\n";
    $retStr .= "<td>\n";
    $retStr .= "<b class=\"searchDisp\">";
    if($this->validOrfnumber) {
      $retStr .= "<a href=\"https://www.yeastgenome.org/locus/". $this->orfNumber ."\" target=\"_new2\">";
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
    
    return $retStr;
  }

}
  


class LocalizationDisplay
{
  var $localizeId;
  var $strainName;
  var $library;
  var $orfNumber; 
  var $orfName;
  var $HTTPToSGD;
  var $cellCyclePhase;
  var $subcellLocalization;
  //  var $userWhoScored;
  var $relativeIntensity;
  var $signalHomogeneity;
  var $clipDir;
  var $clipPath;
  var $cellMorphology;
  var $color1;
  var $color2;
  var $color;
  var $setId;
  var $validOrfnumber;
  var $errorMessage;


  function LocalizationDisplay() {
    
    /* SET THE DEFAULTS */

    $this->localizeId = "none";
    $this->clipDir = "";
    $this->clipPath = "img/noimage.png";
    $this->color1 = "D0D0D0";
    $this->color2 = "EEEEEE";
    $this->color = "D0D0D0";
    $this->cellCyclePhase = "-";
    $this->subcellLocalization = "-";
    //    $this->userWhoScored = "-";
    $this->relativeIntensity = "-";
    $this->signalHomogeneity = "-";
    $this->cellMorphology = "-";
    $this->xPos = "-";
    $this->yPos = "-";
    $this->condition = "-";
    $this->validOrfnumber = true;

  }

  function compare($compareTo) {
    $ourLocalizeId = $this->localizeId;
    $theirLocalizeId = $compareTo->localizeId;

    $ourSetid = $this->setId;
    $theirSetid = $compareTo->setId;
    $ourStrainid = getOneToOneMatch("sets", "setid", $ourSetid, "strainid");
    $ourOrfid = getOneToOneMatch("strains", "strainid", $ourStrainid, "orfid");
    $theirStrainid = getOneToOneMatch("sets", "setid", $theirSetid, "strainid");
    $theirOrfid = getOneToOneMatch("strains", "strainid", $theirStrainid, "orfid");
    
    if($ourOrfid>$theirOrfid) {
      return 1;
    } elseif($ourOrfid<$theirOrfid) {
      return -1;
    } 
    if($ourStrainid>$theirStrainid) {
      return 1;
    } elseif($ourStrainid<$theirStrainid) {
      return -1;
    }
    
    if($ourSetid>$theirSetid) {
      return 1;
    } elseif($ourSetid<$theirSetid) {
      return -1;
    }
    
    if($ourLocalizeid>$theirLocalizeid) {
      return 1;
    } elseif($ourLocalizeid<$theirLocalizeid) {
      return -1;
    }
    return 0;
  }
    


  function populate() {
    
    $sqlsearch = 'SELECT strains.strainname, strains.library, orfs.orfnumber, orfs.orfname, cellcycle.phase, subcell.subcellname, localization.xcoord, localization.ycoord, users.realname, localization.localizeid, cellbrightness.cellbrightness, cellmorphology.cellmorphology, subcellhomogeneity.subcellhomogeneity, condition.conditionname, sets.setid, bestlocs.clipfilename, bestlocs.clipcontainingdir
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
         INNER JOIN `condition` ON sets.conditionid = condition.conditionid
         INNER JOIN bestlocs ON bestlocs.localizeid = localization.localizeid
	 WHERE localization.localizeid = '.$this->localizeId;
    $ressearch = dbquery($sqlsearch);
    
    while ($row = mysqli_fetch_assoc($ressearch)) {
      
      $this->strainName = $row['strainname'];
      $this->library = $row['library'];
      $this->orfNumber = $row['orfnumber'];
      $this->orfName = $row['orfname'];
      $this->subcellLocalization = $row['subcellname'];
      $this->cellCyclePhase = $row['phase'];
      $this->condition = $row['conditionname'];
      //      $this->userWhoScored = $row['realname'];
      $this->relativeIntensity = $row['cellbrightness'];
      $this->signalHomogeneity = $row['subcellhomogeneity'];
      $this->cellMorphology = $row['cellmorphology'];
      $this->xPos = $row['xcoord'];
      $this->yPos = $row['ycoord'];
      $this->setId = $row['setid'];
      
      $this->HTTPToSGD = "https://www.yeastgenome.org/locus/" . $this->orfNumber;

      // OLD STYLE CLIP INFORMATION
      //      checkCellImageExists($this->localizeId);
      //      $this->clipPath = "/images/clips/".$this->localizeId.".png";

      // NEW STYLE CLIP INFORMATION
      // depends on derived locs pointing to correct clip in bestlocs
      $this->clipDir = $row['clipcontainingdir'];
      $this->clipPath = $row['clipfilename'];
      
    }
    

  }

  function setLocId($id) {
    $this->localizeId = $id;

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
    //    $retStr .=  "<p class=\"locLabel\">\n";
    $retStr .= "<table width=760 border=0 bgcolor=#".$this->color.">\n"; 
    $retStr .= "<tr>\n";
    $retStr .= "<td width=240>\n";
    $retStr .= "<b>strain</b>\n";
    $retStr .= "</td>\n";
    $retStr .= "<td width=140>\n";
    $retStr .= "<b>&nbsp;</b>\n";
    $retStr .= "</td>\n";
    $retStr .= "<td width=140>\n";
    //    $retStr .= $this->userWhoScored."\n";
    $retStr .= "&nbsp;";
    $retStr .= "</td>\n";
    $retStr .= "<td width=100>\n";
    if($this->localizeId != "none") {
      $tmp = getOneToOneMatch("localization", "localizeid", $this->localizeId, "setid");
      $retStr .= "<b>set id</b>: ".$tmp."\n";
      // $retStr .= "<b>id</b>:not found\n";
    } else {
      $retStr .= "<b>id</b>: ".$this->localizeId."\n";
    }
    $retStr .= "</td>\n";    print "<td>\n";
    $retStr .= "<td width=70>\n";
    $retStr .= "<b>x</b>: ".$this->xPos."\n";
    $retStr .= "</td>\n";    print "<td>\n";
    $retStr .= "<td width=70>\n";
    $retStr .= "<b>y</b>: ".$this->yPos."\n";
    $retStr .= "</td>\n";
    $retStr .= "</tr>\n";
    $retStr .= "<tr>\n";
    $retStr .= "<td>\n";
    $retStr .= "<b class=\"searchDisp\">";
    if($this->validOrfnumber) {
      $retStr .= "<a href=\"https://www.yeastgenome.org/locus/". $this->orfNumber ."\" target=\"_new2\">";
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
      $retStr .= "<img src=\"".$this->clipDir.$this->clipPath."\" border=0>\n";
      $retStr .= "</a>";
    } else {
      $retStr .= "<img src=\"".$this->clipDir.$this->clipPath."\" border=0>\n";
    }
    
    $retStr .= "</td>\n";
    $retStr .= "</tr>\n";
    $retStr .= "</table>\n";
    $retStr .= "<br>\n";
    
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

// STOLE THIS FROM THE PHP PAGE
function casort($arr, $var, $var2) {
  ph();
  $tarr = array();
  $rarr = array();
  for($i = 0; $i < count($arr); $i++) {
     $element = $arr[$i];
     $tarr[] = strtolower($element->{$var});
     printWB(strtolower($element->{$var}));
  }
 
  reset($tarr);
  asort($tarr);
  $karr = array_keys($tarr);
  for($i = 0; $i < count($tarr); $i++) {
     $rarr[] = $arr[intval($karr[$i])];
  }
 
  return $rarr;
}

function buildLocalizationDisplayHTMLFromLocalizationList($in) {
  // https://www.yeastgenome.org/locus/pho4
  $retStr = "";
  $objList = array();
  foreach($in as $id) {
    $currentLocObj = new LocalizationDisplay();
    $currentLocObj->setLocId($id);
    $currentLocObj->populate();
    $objList[] = $currentLocObj;
  }

  //  $objList = casort($objList,"orfName");
  
  $lastOrf = "";
  $lastColor = "";
  
  
  for($a=0; $a<count($objList); $a++) {
    $theLocObj = &$objList[$a];
    
    /* DO SOME SIMPLE COLOR SWITCHING */
    $color1 = $theLocObj->color1;
    $color2 = $theLocObj->color2;
    
    if ($theLocObj->orfNumber !== $lastOrf) {
      if ($theLocObj->color == $lastColor) {
	$theLocObj->changeColor($color2);
      } else {
	$theLocObj->changeColor($color1);
      }
    } else { 
      $theLocObj->changeColor($lastColor);
    }
    
    /* PRINT THE LOCALIZATION */
    $retStr .= $theLocObj->buildLocalizationHTML("link");
    $lastOrf = $theLocObj->orfNumber;
    $lastColor = $theLocObj->color;
    
  }
  return $retStr;
}


class OrfList {
  var $orfList;
  var $img = "img/o.gif";
  
  function OrfList() {
    $this->orfList = array();
  }

  function addOrfid($orfid) {
    $this->orfList[] = $orfid;
  }

  function temp() {
    printWB("Hi");
  }

  function fixup() {
    $this->orfList = array_unique($this->orfList);
  }

  function buildOrfInfoTable() {

    $subcellWidth = 60;
    $orfinfoWidth = 70;
    $checkWidth =20;
    $numSubcells = 15;
    $bgcolor="#dddddd";
    
    $sql = "select subcellid from subcell";
    $res = dbquery($sql);
    $subcellidList = makeArrayFromResColumn($res, "subcellid");

    $tmp = 2*$orfinfoWidth + $checkWidth;
    $tmp2 = $numSubcells*$subcellWidth;
    $totalTableWidth=$tmp+$tmp2;

    /* BUILD THE HTML FOR THE RULE ROWS  */
    $ruleHTML = "<tr>\n";
    $ruleHTML .= "<td align='center' width=$checkWidth border=0><IMG SRC='img/rule.gif' WIDTH=$checkWidth HEIGHT=1 BORDER=0 VSPACE=0></td>\n";
    $ruleHTML .= "<td align='center' width=$orfinfoWidth border=0><IMG SRC='img/rule.gif' WIDTH=$orfinfoWidth HEIGHT=1 BORDER=0 VSPACE=0></td>\n";
    $ruleHTML .= "<td align='center' width=$orfinfoWidth border=0><IMG SRC='img/rule.gif' WIDTH=$orfinfoWidth HEIGHT=1 BORDER=0 VSPACE=0></td>\n";
    foreach($subcellidList as $subcellid) {
      $ruleHTML .= "<td align='center' width=$orfinfoWidth border=0><IMG SRC='img/rule.gif' WIDTH=$subcellWidth HEIGHT=1 BORDER=0 VSPACE=0></td>\n";
    }
    $ruleHTML .= "</tr>\n";

    
    /* START THE FORM FOR SUBMITTING A BUNCH OF ORFS TO SEARCH.PHP */
    $retStr = "<form name='searchOrfsForm' method=post action='search.php' target='_blank'>";

    $retStr .= "<table  border=0 cellspacing=0 cellpadding=0 width=$totalTableWidth>\n";

    /* CATEGORY ROW */
    $retStr .= "<tr valign='top'>";
    $retStr .= "<td colspan=3 align='center' width=$tmp height='20' border=0 bgcolor='$bgcolor'><h2><i>Orf Information</i></h2></td>\n";
    $retStr .= "<td colspan=15 align='center' width=$tmp2 border=0 bgcolor='$bgcolor'><h2><i>Subcellular Localization</i></h2></td>\n";
    $retStr .= "</tr>";

    /* MAIN TITLE BAR */
    $retStr .= "<tr valign='top'>";
    $retStr .= "<td align='center' width=$checkWidth height='20' border=0>L</td>\n";
    $retStr .= "<td align='center' width=$orfinfoWidth height='20' border=0>Orf Number</td>\n";
    $retStr .= "<td align='center' width=$orfinfoWidth height='20' border=0>Gene Name</td>\n";
    $retStr .= "<td align='center' width=$orfinfoWidth height='20' border=0>TAP Abundance</td>\n";
    $gray = true;
    foreach($subcellidList as $subcellid) {
      $color = "";
      if($gray) {
	$color = " BGCOLOR='#f2f2f2' ";
      }
      $gray = toggle($gray);
      
      $txt = getOneToOneMatch("subcell", "subcellid", $subcellid, "subcellname");
      $retStr .= "<td align='center' width=$subcellWidth height='20' $color border=0>$txt</td>\n";
    }
    $retStr .= "</tr>";
    
    /* RULE */
    $retStr .= $ruleHTML;
  
    
    if(count($this->orfList) == 0) {
      $retStr .= "<tr><td colspan=15 height=40><h5>No Localizations Matched Your Criteria.</h5></td></tr>\n";
    }
    
    
    foreach($this->orfList as $orfid) {
      $retStr .= "<tr>";
      $orfNumber = getOneToOneMatch("orfs", "orfid",$orfid, "orfnumber");
      $orfName = getOneToOneMatch("orfs", "orfid",$orfid, "orfname");

      $tapStrain = getOneToOneArbConditions("strains", "tag='TAP' and orfid=".$orfid,"strainid"); 
      $tapVisualized = getOneToOneMatch("tap", "strainid", $tapStrain, "visualized");
      $tapIntensity = getOneToOneMatch("tap", "strainid", $tapStrain, "abundance");
      $tapError =  getOneToOneMatch("tap", "strainid", $tapStrain, "error");

      if ($tapVisualized == 'F') {
	$tapReadout = "not visualized";
      } else {
	if ($tapIntensity == 0) {
	  $tapReadout = "technical problem";
	} else if ($tapIntensity < 0) {
	  $tapReadout = "low signal";
	} else {
	  $tapReadout = $tapIntensity;
	  if ($tapError != 0) {
	    $tapReadout .= "&nbsp;&plusmn;".$tapError;
	  }
	}
      }
      
      /* CHECKBOX TO SUBMIT TO SEARCH */
      $retStr .= "<td align='center' width=$checkWidth><input type=checkbox name=\"orfid_$orfid\" value=\"".$orfid."\"></td>\n";

      $retStr .= "<td align='center' width=$orfinfoWidth>$orfNumber</td>\n";
      $retStr .= "<td align='center' width=$orfinfoWidth>$orfName</td>\n";
      $retStr .= "<td align='center' width=$orfinfoWidth>$tapReadout</td>\n";
      $hasCertainLoc = array();
      $locList = orfidToFinalLocMap($orfid);
      assert(count($locList) != 0);

      $subcellPresentList = array();
      $heterogeneousList = array();
      $brightList = array();
      $morphList = array();

      foreach($locList as $loc) {
	$subcellid = getOneToOneMatch("localization", "localizeid", $loc, "subcellid");
	
	$hetero = getOneToOneMatch("localization", "localizeid", $loc, "subcellhomogeneityid");
	if($hetero != 1) {
	  $heterogeneousList[$subcellid] = true;
	}
	$bright = getOneToOneMatch("localization", "localizeid", $loc, "cellbrightnessid");
	if($bright != 1) {
	  $brightList[$subcellid] = true;
	}
	$morph = getOneToOneMatch("localization", "localizeid", $loc, "cellmorphologyid");
	if($morph != 1) {
	  $morphList[$subcellid] = true;
	}

	$subcellPresentList[] = $subcellid;
      }
      $subcellPresentList = array_unique($subcellPresentList);
      $gray = true;
      foreach($subcellidList as $i) {
	$color = "";
	if($gray) {
	  $color = " BGCOLOR='#f2f2f2' ";
	}
	$gray = toggle($gray);
	$retStr .= "<td align='center' $color>";
	if(in_array($i, $subcellPresentList)) {
	  $retStr .= "<img src=\"img/checkmark.gif\">";
	} else {
	  $retStr .= "";
	}
	/* HETERO */
	if(isset($heterogeneousList[$i])) {
	  $retStr .= "H";
	}
	/* BRIGHTNESS */
	if(isset($brightList[$i])) {
	  $retStr .= "B";
	}
	/* MORPHOLOGY */
	if(isset($morphList[$i])) {
	  $retStr .= "M";
	}
	$retStr .= "</td>";
      }
      $retStr .= "</tr>";
      $retStr .= $ruleHTML;
    }
    $retStr .= "</table>\n";

    $retStr .= "<table border=0 cellspacing=0 cellpadding=0 bgcolor='$bgcolor'>\n";
    $retStr .= "<tr><td>H = Signal Shows Heterogeneity Within Subcellular Compartment<td><tr>\n";
    $retStr .= "<tr><td>M = Morphologically distinctive<td><tr>\n";
    $retStr .= "<tr><td>B = Signal Brightness Non-uniform Across Cell Population<td><tr>\n";
    $retStr .= "</table>\n";

    
    
    $retStr .= "<input type=hidden name=orfsToLookFor value=\"".urlencode(serialize($this->orfList))."\">";
    /* INSERT BOTH JAVASCRIPT AND BUTTONS FOR CHECKING/UNCHECKING ALL */
    $retStr .= "<script language='JavaScript'>\n";
    $retStr .= "function checkAll(f, cuc) {\n";
    $retStr .= "for(i=0; i<f.length; i++) {\n";
    $retStr .= "f[i].checked = cuc;\n";
    $retStr .= "}\n";
    $retStr .= "}\n";
    $retStr .= "</script>\n";
    $retStr .= "<input type=button name='clk' value='Check All ORFs' onClick='checkAll(this.form, true)'>";
    $retStr .= "<input type=button name='clk' value='Uncheck All ORFs' onClick='checkAll(this.form, false)'>";

    /* SUBMIT */
    $retStr .= "<input type=submit name=\"submit\" value='Display Localizations for these ORFs'>";


    $retStr .= "</form>";
    
    return $retStr;
    
  }

  function writeOrfInfoFile() {
    
    // USE THIS TO GENERATE CLUSTER-STYLE OUTPUT.
    // DOES NOT GIVE USER ANY FEEDBACK YET, JUST WRITES FILE

    // make a unique name
    $outFile = "downloads/";
    $outFile .= time();
    $outFile .= ".txt";
    
    // GET SUBCELL INFO FOR COLUMN HEADINGS
    $sql = "select subcellid from subcell";
    $res = dbquery($sql);
    $subcellidList = makeArrayFromResColumn($res, "subcellid");
    
    if (!$fp = fopen($outFile, 'w')) {
      print "cannot open file";
      exit;
    }

    $writeStr = "UNIQUEID";
    $writeStr .= "\t";
    $writeStr .= "NAME";


    foreach($subcellidList as $subcell) {
      $subcellName = getOneToOneMatch("subcell", "subcellid", $subcell, "subcellname");
      $writeStr .= "\t";
      $writeStr .= $subcellName;
    }
    $writeStr .= "\n";
    
    if (!fwrite($fp,$writeStr)) {
      print "failed to write";
      exit;
    }

    foreach ($this->orfList as $orfid) {
      
      $orfNumber = getOneToOneMatch("orfs", "orfid",$orfid, "orfnumber");
      $orfName = getOneToOneMatch("orfs", "orfid",$orfid, "orfname");
      
      if ($orfName == "") {
	$orfName = $orfNumber;
      }
      
      $writeStr = "";
      $writeStr .= $orfNumber;
      $writeStr .= "\t";      
      $writeStr .= $orfName;
      
      // NOW ENTER ALL OF THE INFO AS NUMBERS....
      
      $hasCertainLoc = array();
      $locList = orfidToFinalLocMap($orfid);
      assert(count($locList) != 0);
      
      $subcellPresentList = array();
      $heterogeneousList = array();
      //      $brightList = array();
      //      $morphList = array();

      foreach ($locList as $loc) {
	$subcellid = getOneToOneMatch("localization", "localizeid", $loc, "subcellid");
	
	$hetero = getOneToOneMatch("localization", "localizeid", $loc, "subcellhomogeneityid");
	if($hetero != 1) {
	  $heterogeneousList[$subcellid] = true;
	}
	//	$bright = getOneToOneMatch("localization", "localizeid", $loc, "cellbrightnessid");
	//	if($bright != 1) {
	//	  $brightList[$subcellid] = true;
	//	}
	//	$morph = getOneToOneMatch("localization", "localizeid", $loc, "cellmorphologyid");
	//	if($morph != 1) {
	//	  $morphList[$subcellid] = true;
	//	}

	$subcellPresentList[] = $subcellid;
      }
      $subcellPresentList = array_unique($subcellPresentList);
      foreach ($subcellidList as $i) {
	// HETERO 
	$writeStr .= "\t";
	if (in_array($i, $subcellPresentList) && !isset($heterogeneousList[$i])) {
	  $writeStr .= "2";
	} elseif (isset($heterogeneousList[$i])) {
	  $writeStr .= "1";
	} else {
	  $writeStr .= "0";
	}
      }
      $writeStr .= "\n";
      
      if (!fwrite($fp,$writeStr)) {
	print "failed to write";
	exit;
      }
      
    }
    
    // THEEND   
    
    fclose($fp);
    
  }
  
}


function isSetPrunedScore($setid) {
  $sql = "select * from sets inner join strains on strains.strainid=sets.strainid where tag_success='T' and new_prune_complete='T' and new_no_gfp_visible='F' and score_this='T' and setid=$setid";
  $res = dbquery($sql);
  $total = mysqli_num_rows($res);
  $sql = "select * from sets inner join strains on strains.strainid=sets.strainid where tag_success='T' and new_prune_complete='F' and score_this='T' and setid=$setid";
  $res = dbquery($sql);
  $total += mysqli_num_rows($res);
  if($total>0) {
    return true;
  }
  return false;
}

function singleQuote($a) {
  $b = "'" . $a . "'";
  printWB($b);
  return $b;
}

function makeAliasStringForOrf($orfnumber) {

  $first = true;
  $retStr = "";
  
  $sql = "SELECT * FROM genealias WHERE orfnumber like '".$orfnumber."'";
  $res = dbquery($sql);
  while ($row = mysqli_fetch_assoc($res)) {
    if ($first == false) { 
      $retStr .= " | ";
    }
    $retStr .= $row['alias'];
    $first = false;
  }
  return $retStr;
}


// initialize variables
//$genesWithAliasIssues = Array();
//$orfsNotFound = Array();
//$orfsToSearch = Array();

function checkAliases($geneList) {

  // READS GENE NAMES OUT OF A SPACE-DELIMITED FIELD
  // RETURNS 3 ARRAYS OF ORFNAMES/NUMBERS/NASTY TEXT
  // 1. SEARCHABLE AS IS
  // 2. AMBIGUOUS, REQUIRING SELECTION BEFORE SEARCHING
  // 3. NOT FOUND ANYWHERE IN THE DB
  
//	print "LIST: $geneList<br>";
  $searchFieldInput = explode(" ",$geneList);
  foreach ($searchFieldInput as $input) {
//	print "IN: $input<br>";

     $genesWithAliasIssues = NULL;
     $orfsNotFound = NULL;
     $orfsToSearch = NULL;


    // catch bad characters in input: any non: alphanumeric, single quote, dash
    if (preg_match("/[^a-zA-Z0-9\-\']/",$input)) {
      $orfsNotFound[] = $input;
      continue;
    }
    
    // catch quotes
    $input = addslashes($input);
    
    if (!preg_match("/[Yy][a-pA-P][lrLR]\d\d\d[cwCW]/",$input)) {
      
      // gene name, so check for ambiguity
      // sql call to genealias
      $sql = "SELECT * FROM genealias WHERE alias like '".$input."'";
      $res = dbquery($sql);
      $aliases = mysqli_num_rows($res);
      
      // ambiguous if more than 1 row returned from genealias
      if ($aliases > 1) {
	$genesWithAliasIssues[] = $input;

      } elseif ($aliases == 1) {
	
	// if one alias only, get orfnumber for that alias
	$row = mysqli_fetch_assoc($res);
	$aliasOrfnumber = $row['orfnumber'];

	//  and check orfs table for conflict
	$sqlOrf = "SELECT * FROM orfs WHERE orfname like '".$input."'";
	$resOrf = dbquery($sqlOrf);
	$names = mysqli_num_rows($resOrf);

	if ($names > 0) {
	  $genesWithAliasIssues[] = $input;
	} else {
	  $orfsToSearch[] = $aliasOrfnumber;
	}
	
      } else {

	// just get the orfnumber if no alias conflicts
	if ($input != "") {

	  $tmp = convertOrfNameToOrfNumber($input);

	  if ($tmp == "") {
	    print "right here!";
	    print $input."<br>";
	  }

	  // if not found
	  if (preg_match("/(\w+)\sNOT_FOUND/",$tmp,$match)) {
	    $orfsNotFound[] = $match[1];
	  } else {      
	    $orfsToSearch[]  = $tmp;
	  }
	}
      }
      
    } else {
      
      // well formed orfnumber like yal001c
      // validate that it exists by checking for an orfid
      $orfid = getOneToOneMatch("orfs","orfnumber","'".$input."'","orfid");
      if ($orfid != "") {
	$orfsToSearch[] = $input;
      } else {
	$orfsNotFound[] = $input;
      }
    }
  }
  $retArray = array($orfsToSearch,$genesWithAliasIssues,$orfsNotFound);
  return $retArray;
  
}

function makeAliasTable($inputGene,$showCheck) {

  print "<table border=1 cellspacing=0 cellpadding=5>";
  print "<tr>";
  if ($showCheck == TRUE) {
    print "<td>&nbsp;</td>";
  }
  print "<th>input</th><th>orf</th><th>gene</th><th>aliases</th></tr>";
  
  
  // get all the orfs it refers to
  // from genealias
  $sqlGenealias = "SELECT * FROM genealias WHERE alias like '".$inputGene."'";
  $resGenealias = dbquery($sqlGenealias);
  $orfsFromAlias = makeArrayFromResColumn($resGenealias,"orfnumber");
  
  // from orfs
  $sqlOrfs = "SELECT * FROM orfs WHERE orfname like '".$inputGene."'";
  $resOrfs = dbquery($sqlOrfs);
  $orfsFromOrfs = makeArrayFromResColumn($resOrfs,"orfnumber");

  // combine the two lists
  $orfsToDisplay = array_merge($orfsFromAlias,$orfsFromOrfs);

  // BUILD A TABLE WITH CHECKBOXES FOR SELECTING
  foreach ($orfsToDisplay as $orfnumber) {

    if ($orfnumber == "") {
      continue;
    }

    $aliasStr = makeAliasStringForOrf($orfnumber);
    $sqlOrfName = "SELECT orfname,orfid FROM orfs WHERE orfnumber like '".$orfnumber."'";
    $resOrfName = dbquery($sqlOrfName);
    $row = mysqli_fetch_assoc($resOrfName);
    $orfname = $row['orfname'];
    $orfid = $row['orfid'];

    $outStr = "";
    $outStr .= "<tr>";
    if ($showCheck == TRUE) {
      $outStr .= "<td><input type=checkbox name='gene_".$orfid."' value='".$orfid."'></td>";
    }
    $outStr .= "<td>".$inputGene."</td>";
    $outStr .= "<td>".$orfnumber."</td>";
    $outStr .= "<td>".$orfname."</td>";
    $outStr .= "<td>".$aliasStr."</td>";
    $outStr .= "</tr>";

    print $outStr;

  }
  print "</table><br>";
}


?>
