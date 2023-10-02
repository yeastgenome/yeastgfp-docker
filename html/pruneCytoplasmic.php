

<?
require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/secure.php");
require("$include_dir/projects_inc.php");


$orfList = getAllOrfs();
foreach($orfList as $orf) {
  $cytID1 = getOneToOneMatch("subcell", "subcellname", "'cytoplasm'", "subcellid");
  $cytID2 = getOneToOneMatch("subcell", "subcellname", "'ambiguous'", "subcellid");
  if(doesOrfHaveOnlyConsensusLocsWithSubcellsXOrY($orf, $cytID1, $cytID2)) {
    $setList = getSetListFromOrfid($orf);
    foreach($setList as $set) {
      $sql2 = "update sets set prune_skipped='F' where setid=$set";
      printWB($sql2);
      dbquery($sql2);
    }
      
    printWB(getOneToOneMatch("orfs", "orfid", $orf, "orfname"));
  }


}
?>