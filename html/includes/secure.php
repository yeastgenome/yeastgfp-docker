<?php

// secure.php - Check for session existence; redirect to
//		login.php if not.
/**************************************************************/

if (!isset($_SESSION['userid'])) {
  
  $_SESSION['goback'] = $_SERVER['PHP_SELF'];
  $server = $_SERVER['HTTP_HOST']."/";
  if ($_SERVER['SERVER_PORT'] == 80) {
    header("Location: http://".$server.$web_dir."login.php");
  } else {
    header("Location: https://".$server.$web_dir."login.php");
  }
  exit;
}

// Hierarchy of access privileges (project level!!)
$sysadminid	= 1;				/** Set sysadmin userid **/
$priv["Guest"]		= 50;			/** READ ONLY					**/
$priv["User"]		= 100;			/** READ/WRITE					**/
$priv["Superuser"]	= 150;			/** READ/WRITE/MOD				**/
$priv["Owner"]		= 200;			/** READ/WRITE/MOD/ADD/DEL/EDIT	**/


function checksysadmin() {
  global $sysadminid;
  if ($_SESSION['userid'] <> $sysadminid) {
    centermsg("Sorry.  Insufficient access privileges.");	
    exit; }
}


// change the assertion handling to be user dependent

// Active assert and make it quiet
assert_options (ASSERT_ACTIVE, 1);
assert_options (ASSERT_WARNING, 1);
assert_options (ASSERT_QUIET_EVAL, 0);

// Create a handler function
function my_assert_handler ($file, $line, $code) {
  echo "<hr>Assertion Failed:
        File '$file'<br>
        Line '$line'<br>
        Code '$code'<br><hr>";
  exit();
}

// Set up the callback
assert_options (ASSERT_CALLBACK, 'my_assert_handler');





?>
