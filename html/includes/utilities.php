<?php

// USEFUL UTILITIES FOR GENERIC PHP
// BUILD AN INDEX.....


/* GET A VARIABLE FROM A POST SAFELY, EVEN IF NOT SET */
function getPostVar(&$var, $varName) {
  if (isset($_POST[$varName])) {
    $var = $_POST[$varName];
  } else {
    $var = "";
  }
}

function returnPostVar($varName) {
  if (isset($_POST[$varName])) {
    return $_POST[$varName];
  } 
  return "";
}

function getAllPostVarsString() {
  $retStr = "<table border>\n<tr><th>Name</th><th>Value</th></tr>\n";
  ksort ($_POST);
  foreach ($_POST as $key => $value) {
    $retStr .= "<tr><td align=right>".$key."</td><td>".$value."</td></tr>\n";
  }
  $retStr .= "</table>";
  return $retStr;
}
    

function arrayToCommaList($myArray,$quote) {
  return arrayToList($myArray,",",$quote);
}


function arrayToCommaListOfKeys($myArray,$quote) {
  return arrayToListOfKeys($myArray,",",$quote);
}


function arrayToListOfKeys($myArray,$delim,$quote) {
  $first=true;
  $max = count($myArray);
  $retVal = "";
  foreach ($myArray as $key => $listMember) {
    if ($first == false) {
      $retVal .= $delim;
    }
    $first = false;
    $retVal .= $quote.$key.$quote;
  }
  return $retVal;
}


function arrayToList($myArray,$delim,$quote) {
  $first=true;
  $max = count($myArray);
  $retVal = "";
  foreach ($myArray as $key => $listMember) {
    if ($first == false) {
      $retVal .= $delim;
    }
    $first = false;
    $retVal .= $quote.$listMember.$quote;
  }
  return $retVal;
}

function makeArrayFromResColumn($res, $colName) {
    $retArray = array();
    while($row = mysqli_fetch_assoc($res)) {
	$retArray[] = $row[$colName];
    }
    return $retArray;
}

function makeAssocArrayFromResColumns($res, $keyColName, $valColName) {
    $retArray = array();
    while($row = mysqli_fetch_assoc($res)) {
	$retArray[$row[$keyColName]] = $row[$valColName];
    }
    return $retArray;
}


/*
function array_chunk ($a, $s, $p=false) {
  $r = Array();
  $ak = array_keys($a);
  $i = 0;
  $sc = 0;
  for ($x=0;$x<count($ak);$x++) {
    if ($i == $s){$i = 0;$sc++;}
    $k = ($p) ? $ak[$x] : $i;
    $r[$sc][$k] = $a[$ak[$x]];
    $i++;
  }
  return $r;
}

*/



function ph() {
  printWB("hi");
}



// function toggle($a);
function toggle($a) {
  if($a) {
    return false;
  }
  return true;
}




?>
