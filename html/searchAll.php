<!-- THE BROWSER DOESN'T SEEM TO LIKE IT WHEN PICTURES BY THE SAME NAME
ARE ACTUALLY CHANGING IN THEIR CONTENT.   THIS NO-CACHE BIT SEEMS TO FIX
THINGS UP, BUT WE SHOULD REALLY HAVE A PHP ROUTINE THAT DUMPS APPROPRIATE
HEADERS AND TAKES NO-CACHE AS AN ARG -->
<html>
<head>
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<link rel="stylesheet" href="imagedb.css">
</head>
<?php

require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/secure.php");
require("$include_dir/projects_inc.php");

pass($priv['User']);


foreach($_POST as $key=>$val) {
  if(preg_match("/orfid_(\d+)/", $key, $matches)) {
    $orfnumber = getOneToOneMatch("orfs", "orfid",$matches[1], "orfnumber");
    $orfsToSearch[] = $orfnumber;
  }
}

if(isset($_POST['orf_number'])) {
  $searchFieldInput = explode(" ",$_POST['orf_number']);
  foreach ($searchFieldInput as $input) {
    $orfsToSearch[] = convertOrfNameToOrfNumber($input);
    //printWB($input);
  }
}

print "<h1>Search results...</h1>";
$arrayOfResultsObj = array();
$arrayOfOrfDisplays = array();

foreach ($orfsToSearch as $orf) {
  //  printWB($orf);

  
//  $orfDisplay = new OrfDisplay($orf);
//  $arrayOfOrfDisplays[] = new OrfDisplay($orf);

  
  if (preg_match("/NOT_FOUND/",$orf)) {
    $getOrf = explode(" ",$orf);
    $noOrfObj = new LocalizationDisplay();
    $noOrfObj->orfName = $getOrf[0];
    $noOrfObj->orfNumber = "ORFname :".$noOrfObj->orfName." not recognized";
    $noOrfObj->strainName = "-";
    $noOrfObj->library = "-";
    $noOrfObj->validOrfnumber = false;
    $arrayOfResultsObj[] = $noOrfObj;
    
    
  } elseif (preg_match("/AMBIGUOUS/",$orf)) {
    $getOrf = explode(" ",$orf);
    $noOrfObj = new LocalizationDisplay();
    $noOrfObj->orfName = $getOrf[0];
    $noOrfObj->orfNumber = "AMBIGUOUS";
    $noOrfObj->strainName = "-";
    $noOrfObj->library = "-";
    $arrayOfResultsObj[] = $noOrfObj;

  } else {
   
    /* PROCESS ORFS THAT ARE PRESENT IN DB */
    /* FIRST GET THE LOCALIZATIONS ASSOCIATED WITH THE ORF */

    $sqlsearch = "SELECT localization.localizeid
	 FROM localization
	 INNER JOIN sets ON sets.setid = localization.setid
	 INNER JOIN strains ON sets.strainid = strains.strainid
	 INNER JOIN orfs ON orfs.orfid = strains.orfid
         WHERE most_current='T'
         AND orfnumber like '".$orf."'";
    $ressearch = dbquery($sqlsearch);
    
    /* HANDLE ORFS THAT ARE MISSING IN STRAIN COLLECITON */

    $localizeidList = makeArrayFromResColumn($ressearch, "localizeid");
    //    printWB(count($localizeidList));
    //    $localizeidList = filterLocListToMakeValid($localizeidList);

    if (count($localizeidList) == 0) {
      $noLocObj = new LocalizationDisplay();
      $noLocObj->orfNumber = $orf;      
      $noLocObj->getOrfInfo($orf);
      if($noLocObj->validOrfnumber) {
	// FIND OUT WHY THERE ARE NO LOCS
	$res = dbquery("select * from orfs inner join strains on strains.orfid=orfs.orfid where strains.tag_success='T' and orfnumber='".$noLocObj->orfNumber."'");
	if(count(makeArrayFromResColumn($res, "strainid")) == 0) {
	  $noLocObj->errorMessage="no strains were successfully tagged";
	} else {
	  $noLocObj->errorMessage="strains were tagged, but GFP signal was not seen";
	}
      }
      $arrayOfResultsObj[] = $noLocObj;
      continue;
    } 

    foreach($localizeidList as $id) {
      $currentLocObj = new LocalizationDisplay();
      $currentLocObj->setLocId($id);
      $currentLocObj->populate();
      
      $arrayOfResultsObj[] = $currentLocObj;
      
    }
    
  }
 
  
}

$lastOrf = "";
$lastColor = "";


for($a=0; $a<count($arrayOfResultsObj); $a++) {
  
  $theLocObj = &$arrayOfResultsObj[$a];
}



for($a=0; $a<count($arrayOfResultsObj); $a++) {
  
  $theLocObj = &$arrayOfResultsObj[$a];
  
  /* DO SOME SIMPLE COLOR SWITCHING */

  $color1 = $theLocObj->color1;
  $color2 = $theLocObj->color2;
  
  if ($theLocObj->orfNumber !== $lastOrf) {

    if ($theLocObj->color == $lastColor) {
      $theLocObj->changeColor($color2);
    } 
    else {
      $theLocObj->changeColor($color1);
    }
  } else { 
    $theLocObj->changeColor($lastColor);
  }
  
  /* PRINT THE LOCALIZATION */
 
  $theLocObj->printLocalization("link");

  $lastOrf = $theLocObj->orfNumber;
  $lastColor = $theLocObj->color;

}

$outFile = "/tmp/orfs.txt";
if (!$fp = fopen($outFile, 'w')) {
  print "cannot open file ".$outFile;
  exit;
}

$orfString = "";

foreach ($orfsToSearch as $orf) {
  //  printWB($orf);
  $orfString .= $orf . " ";
}
if (!fwrite($fp,$orfString)) {
  print "failed to write";
  exit;
}
fclose($fp);

system("rm -f /tmp/err /tmp/out /tmp/temp.ps /tmp/temp.pdf /home/gfp/images/tmp/temp.pdf /tmp/runR.rout");

$cmd = "/usr/java/j2re1.4.1_01/bin/java -classpath /home/lcgerke/javaWork:/home/lcgerke/javaWork/makeRBarplot:/home/lcgerke/ parseResults -opt 0x47 -rScript /tmp/runR.R -geneFile /tmp/orfs.txt -outFile /tmp/out  -tempDir /tmp -psFile temp.ps";
//print $cmd;
$a = system($cmd . " &> /tmp/err");
if($a != "") {
  print("error in the java step.  Check /tmp/err");
  exit;
}
/*
$cmd = "cp /home/lcgerke/javaWork/makeRBarplot/runR.R  /tmp/runR.R";
$a = system($cmd . " &> /tmp/err");
if($a != "") {
  print("error in cp1 step.  Check /tmp/err");
  exit;
}
*/
$cmd = "/usr/bin/R --no-save < /tmp/runR.R > /tmp/runR.rout";
$a = exec($cmd);
if($a != "") {
    print("error in the R step." . $a);
  exit;
}
//system($cmd);
$cmd = "ps2pdf /tmp/temp.ps /home/gfp/images/tmp/temp.pdf";
$a = exec($cmd);
if($a != "") {
    print("error in the pdf step." . $a);
  exit;
}

$cmd = "convert -rotate 90 /tmp/temp.ps /home/gfp/images/tmp/temp.png";
$a = exec($cmd);
if($a != "") {
    print("error in the png step." . $a);
  exit;
}

//system($cmd . " &> /tmp/err");
print("<a href=/images/tmp/temp.pdf target=_new2> get the simple-view pdf </a>");
print("<img src=\"/images/tmp/temp.png\">");





?>








