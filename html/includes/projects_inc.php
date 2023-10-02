<?php

// projects_inc.php - Functions, parameters for handling projects
//
/*************************************************************


*************************************************************/

// Other parameters
$maxfiles = 99999999;		/** Limit # of images in a subdirectory **/
$mog_path = "/usr/X11R6/bin/";	/** For both OS X and Linux **/

function oktopass($pid) {	/** Obsoleted - old strainassign? **/
    global $sysadminid;
    if ($_SESSION['projectid'] <> $pid) {
	centermsg("Wrong project.");
	exit; }
    $sql = "SELECT * FROM projects, users 
	    WHERE projects.userid = users.userid 
            AND projects.userid =" .$_SESSION['userid'] ."
            AND projects.projectid = $pid";
         if (mysqli_num_rows(dbquery($sql)) == 0 AND ($_SESSION['userid'] <> $sysadminid)) { 
		centermsg("Sorry.  Insufficient access privileges.");
		exit; }
}

function pass($level) {
	global $sysadminid;

	if (!isset($_SESSION['projectid'])) {
	  $_SESSION['projectid'] = 1;
	  //		centermsg("You must first choose a project.  Click 'choose project' from the above menu");
	  //exit(0);
	}

	if ($_SESSION['userid'] <> $sysadminid) {
		$sql = "SELECT * FROM projects
			   INNER JOIN usersxprojects ON projects.projectid = usersxprojects.projectid
			   WHERE projects.projectid = ".$_SESSION['projectid']." 
			   AND usersxprojects.userid = ".$_SESSION['userid']."			   
			   AND rights >= ".$level;
		if (mysqli_num_rows(dbquery($sql)) == 0) { 
			centermsg("Sorry.  Insufficient access privileges.");
			exit; }
	}
}






?>
