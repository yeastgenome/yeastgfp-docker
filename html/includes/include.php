<?php
// include.php - THE single include file for every php script.
//
/*************************************************************
		Set global parameters.
		Check for session existence.
*************************************************************/
// PHP parameters

/* CHECK FOR OPTIONAL INCLUDES/LOC.PHP REDIR OF $WEB_DIR */
include("$include_dir/loc.php");

/* GET DATA REPRESENTATION CONSTANTS */
require("$include_dir/representation.php");

/*********  Universal script initialization ********/
//require("$include_dir/secure.php");

require("$include_dir/html.php");
require("$include_dir/utilities.php");
require("$include_dir/db.php");
require("$include_dir/fileInfo.php");
//print_r($checksysadmin());

session_start();
// session_set_cookie_params($session_timeout);
opendb();
?>
