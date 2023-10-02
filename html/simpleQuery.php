<?php
require("locInclude.php");
require("$include_dir/include.php");
//require("$include_dir/secure.php");
require("$include_dir/projects_inc.php");


/* MAKE AN ARRAY OF POSSIBLE OPTIONS FOR OPERATORS AND CONSTRAINTS */
/* DON'T FORGET TO USE RAD commaToList FUUUUUUUNCTIONS!!!! */

?>

<html>
<head>
<link href="formatQuery.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="imagedb.css" type="text/css">
</head>
<script language="JavaScript">

<?php

/* DO THIS FOR RIZEAL */
$localizationFieldArray = array("phase", "realname");
$localizationFieldList = arrayToCommaList($localizationFieldArray,"\"");

$sqlGetSubcells = "SELECT * FROM subcell";
$resGetSubcells = dbquery($sqlGetSubcells);

$optionsArray = makeAssociativeArrayFromResColumns($resGetSubcells, "subcellid", "subcellname");
print makeJavaScriptAssociativeArray("simpleTextEquals", $optionsArray);


?>
var changesWithRespectTo = new Array(<?php=$localizationFieldList?>);
changeConstrainingValue(changesWithRespectTo);

function changeConstrainingValue(theArrayName) {
  var theArray = eval(theArrayName);
  var temp = window.document.theQueryForm.constrainingValueSelect;
  setOptionText(temp, theArray);
}

function setOptionText(theSelect, theArray) {
  theSelect.length = theArray.length;
  var selectCount = 0;
  for (var key in theArray) {
    theSelect.options[selectCount].text = theArray[key];
    theSelect.options[selectCount].value = key;
    selectCount++;
  }
}



</script>

</head>

<body>

<?php
/* BASEQUERY IS THE BASIC TEMPLATE FOR ANY QUERY THAT COMES IN THE
   FORM SELECT * FROM LOCALIZATION INNER JOIN....
*/
$baseQuery = "SELECT * FROM localization ";
$baseQuery .= "INNER JOIN sets ON sets.setid = localization.setid ";
$baseQuery .= "INNER JOIN strains ON strains.strainid = sets.strainid ";
$baseQuery .= "INNER JOIN orfs ON orfs.orfid = strains.orfid ";
$baseQuery .= "INNER JOIN users ON localization.userid = users.userid ";
$baseQuery .= "INNER JOIN cellbrightness ON localization.cellbrightnessid = cellbrightness.cellbrightnessid ";
$baseQuery .= "INNER JOIN cellcycle ON localization.cellcycleid = cellcycle.cellcycleid ";
$baseQuery .= "INNER JOIN cellmorphology ON localization.cellmorphologyid = cellmorphology.cellmorphologyid ";
$baseQuery .= "INNER JOIN condition ON sets.conditionid = condition.conditionid ";
$baseQuery .= "LEFT JOIN dotblot ON dotblot.strainid = strains.strainid ";
$baseQuery .= "INNER JOIN subcell ON localization.subcellid = subcell.subcellid ";
$baseQuery .= "INNER JOIN subcellhomogeneity ON subcellhomogeneity.subcellhomogeneityid = localization.subcellhomogeneityid ";


/* FIRST BUILD THE MULTIPLE SELECT THAT WILL BE USED TO SELECT
   EXACTLY WHICH COLUMNS THE USER WISHES TO SEE
*/
$selectString = insertQueryDisplayOptionSelect($baseQuery);






/* IF WE HAVE COME FROM OURSELVES, AND BTNSUBMIT IS SET, BUILD THE
   RESULT SET
*/
if(isset($_POST['btnSubmit'])) {
  $sqlQuery = $baseQuery;
  $sqlQuery .= "WHERE ";

  assert(isset($_POST['leftOperandSelect']));
  if(isset($_POST['leftOperandSelect'])) {
    $sqlQuery .= $_POST['leftOperandSelect']." ";
  }

  assert(isset($_POST['operator']));
  if($_POST['operator'] == "simpleTextEquals") {
    if($_POST['theNotOperatorSelect'] == "") {
      $sqlQuery .="= ";
    } else if($_POST['theNotOperatorSelect'] == "not") {
      $sqlQuery .="!= ";
    } else {
      assert(0);
    }
  }

  assert(isset($_POST['constrainingValueSelect']));
  $sqlQuery .= $_POST['constrainingValueSelect'];

  //  printWB($sqlQuery);
  
  $resQuery = dbquery($sqlQuery);

  /* BUILD THE PRETTIFYING OBJECT LIST */
  $locDataWrapperArray = buildLocalizationDataWrapperArrayFromLocArray($resQuery);

  for($a=0; $a<count($locDataWrapperArray); $a++) {
    $locDataWrapperObj = &$locDataWrapperArray[$a];
    $locDataWrapperObj->printFormattedPane();
  }
  
  $uniqueOrfArray = array();
  while ($row = mysqli_fetch_assoc($resQuery)) {
    if(!in_array($row['orfid'],$uniqueOrfArray)) {
      $uniqueOrfArray[] = $row['orfid'];
    }
  }

  printWB("total orfs:".count($uniqueOrfArray));

  if ($_POST['existsOperatorSelect'] == "!exists") {
    $completeOrfArray = getAllOrfs();
    $displayArray = array_diff($completeOrfArray,$uniqueOrfArray);
  } else {
    $displayArray = $uniqueOrfArray;
  }
  sort($displayArray);
  //  printWB(arrayToCommaList($displayArray,"\""));

  $columnArray = $_POST['columnsToDisplay'];
  //  displaySqlTableColumnArray($sqlQuery, $columnArray, "orfs", "orfid");
  displaySqlTableColumnArrayTags($sqlQuery, $columnArray, "orfs", "orfid","<p class=\"locTableBodyLabel\">", "");
  
  //  printWB(count($displayArray));
  if(count($displayArray) == 0) {
    printWB("There are no localizations that match your query.");
  } else {
    $str = "SELECT localization.localizeid, strains.strainname, strains.library, orfs.orfnumber, orfs.orfid, orfs.orfname, cellcycle.phase, subcell.subcellname, localization.xcoord, localization.ycoord, users.realname
	 FROM strains 
         INNER JOIN orfs ON orfs.orfid = strains.orfid
	 INNER JOIN sets ON sets.strainid = strains.strainid
	 INNER JOIN localization ON sets.setid = localization.setid
	 INNER JOIN subcell ON localization.subcellid = subcell.subcellid
	 INNER JOIN cellcycle ON localization.cellcycleid = cellcycle.cellcycleid
	 INNER JOIN users ON localization.userid = users.userid ";
    $str .= "WHERE "; 
    $first = true;
    foreach($displayArray as $val) {
      if(!$first) {
	$str .= " OR";
      }
      $first = false;
      $str .= " orfs.orfid = ".$val;
    }
    
    //    displayColumnNamesInMultipleSelect($str);
    //    print $str."***";
    assert(isset($_POST['columnsToDisplay']));
    $columnArray = $_POST['columnsToDisplay'];
    //    print_r($columnArray);
    displaySqlTableColumnArrayTags($str, $columnArray, "strains", "strainname", "<bold>", "</bold>");
  }
  
  
}
?>

<form name="theQueryForm" method="post" action="<?php=$_SERVER['PHP_SELF']?>">
<table>
  <tr>
    <td>
      * ORFs
    </td>
    <td>
      <select name="existsOperatorSelect">
        <option value="exists">exists
        <option value="!exists">!exists
      </select>
    </td>
    <td>
      cell where
    </td>
    <td>
      <select name="leftOperandSelect">
        <option value="localization.subcellid">localization
    </td>
    <td>
      <select name="theNotOperatorSelect">
        <option value="">&nbsp; 
        <option value="not">not
      </select>
    </td>
    <td>
      <select name="operator" onChange="changeConstrainingValue(window.document.theQueryForm.operator.options[selectedIndex].text); return true;">
        <option value="simpleTextEquals">simpleTextEquals
        <option value="changesWithRespectTo">changesWithRespectTo
        <option value="changesWithRespectTo">changesWithRespectTo
    </td>
    <td>
      <select name="constrainingValueSelect">
    </td>
    <td>
      <input type="submit" name="btnSubmit" value="go">
    </td>

  </tr>

	
</table>
<!-- insert the select string-->
<?php=$selectString?>
</form>
<script>
	changeConstrainingValue(simpleTextEquals);
</script>

</body>
</html>
