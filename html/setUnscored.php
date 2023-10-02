<?
require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/secure.php");
require("$include_dir/projects_inc.php");


if(isset($_POST['buttonSubmit'])) {
  if(!is_numeric($_POST['setid'])) {
    print "Please go back and enter a valid setid";
  } else {
//    $sql = "UPDATE sets SET has_been_scored = 'F' WHERE setid = ".$_POST['setid'];
    $sql = "DELETE FROM usersxscorecomplete where setid=".$_POST['setid']."
            AND userid=".$_SESSION['userid'];
//    print $sql;
    dbquery($sql);
    print "set ".$_POST['setid']." will now be scored again.";
  }	
}





//require("showPostVars.php");

?>


<form name="form1" method="post" action="<?=$_SERVER['PHP_SELF']?>">
<table>
    <tr>
         <td>
              setid:
         </td>
         <td>
             <input type="text" name="setid">
         </td>
         <td>
             <input type="submit" name="buttonSubmit" value="Submit">
         </td>
    </tr>
</table>
</form>

