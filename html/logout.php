<?php

require("locInclude.php");
require("$include_dir/include.php");

if (!empty($_SESSION['currentset'])) {

/*********** check to see if currentset has localizations scored ******************/

   $sqlcheck = "SELECT * FROM localization
   			  	 WHERE setid = ".$_SESSION['currentset'];
   $rescheck = dbquery($sqlcheck);
   $num_check = mysqli_num_rows($rescheck);
   
/*************** if not, set complete to 0 so it can be scored ********************/   
   
   if ($num_check == 0) {
   	  $sqlrefresh = "UPDATE sets
   				   	SET complete = 0
				   	WHERE setid = ".$_SESSION['currentset'];
	  dbquery($sqlrefresh);
	  exec("rm -rf ".$tmp_dir.$_SESSION['userid']);	
   }
   
/*************************** if so, set complete to 1 *****************************/   
   
   else {
   		$sqlcomplete = "UPDATE sets
   				   	   SET complete = 1
				   	   WHERE setid = ".$_SESSION['currentset'];
   		dbquery($sqlcomplete);
		exec("rm -rf ".$tmp_dir.$_SESSION['userid']);	
   }   

}

session_start();
session_destroy();
header("Location: login.php");
exit;
?>
