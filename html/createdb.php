<?php

// createdb.php - Create the gfp database with all tables and the sysadmin user account.

$database	= "gfp";
$dbuser		= "www";
$dbpwd		= "pho2000";

$syslogin	= "sysadmin";
$syspwd		= "phopho";
$sysemail	= "flam@itsa.ucsf.edu";

$maketable["strains"] = "CREATE TABLE strains (
	strainid MEDIUMINT AUTO_INCREMENT NOT NULL PRIMARY KEY,
	strainname VARCHAR(100),
	orfid MEDIUMINT,
	growspeed VARCHAR(100),
	library CHAR(1),
	KEY(orfid))
        TYPE=InnoDB;";

$maketable["sets"] = "CREATE TABLE sets (
	setid MEDIUMINT AUTO_INCREMENT NOT NULL PRIMARY KEY,
	strainid MEDIUMINT,
	conditionid SMALLINT,
	complete BIT DEFAULT 0,
	comments TINYTEXT,
        timestamp TIMESTAMP(14),
        prune_complete ENUM('T','F') DEFAULT 'F',
        prune_skipped ENUM('T','F') DEFAULT 'F',
	score_this ENUM('T','F') DEFAULT 'F',
	no_gfp_visible ENUM('T','F') DEFAULT 'F',
        has_been_scored ENUM('T','F') DEFAULT 'F',
	locked ENUM('T','F') DEFAULT 'F',
	KEY(strainid),
	KEY(conditionid))
        TYPE=InnoDB;";

$maketable["subcell"] = "CREATE TABLE subcell (
	subcellid SMALLINT AUTO_INCREMENT NOT NULL PRIMARY KEY,
	subcellname VARCHAR(100),
	path VARCHAR(255))
        TYPE=InnoDB;";


$maketable["condition"] = "CREATE TABLE condition (
	conditionid SMALLINT AUTO_INCREMENT NOT NULL PRIMARY KEY,
	conditionname VARCHAR(255))
        TYPE=InnoDB;";


$maketable["stain"] = "CREATE TABLE stain (
	stainid TINYINT AUTO_INCREMENT NOT NULL PRIMARY KEY,
	stainname VARCHAR(255),
	ext VARCHAR(10))
        TYPE=InnoDB;";


$maketable["images"] = "CREATE TABLE images (
	imageid MEDIUMINT AUTO_INCREMENT NOT NULL PRIMARY KEY,
	setid MEDIUMINT,
	dirpath VARCHAR(255),
	stainid TINYINT,
	userid MEDIUMINT,
	descript VARCHAR(255),
	KEY(setid),
	KEY(stainid),
	KEY(userid))
        TYPE=InnoDB;";

$maketable["orfs"] = "CREATE TABLE orfs (
	orfid MEDIUMINT AUTO_INCREMENT NOT NULL PRIMARY KEY,
	orfnumber VARCHAR(20),
	orfname VARCHAR(20),
	sgdid VARCHAR(20),
	chromo VARCHAR(10),
	start INT UNSIGNED,
	end INT UNSIGNED,
	size INT UNSIGNED,
	KEY(sgdid(8)),
	KEY(orfnumber(9)))
        TYPE=InnoDB;";

$maketable["plateinfo"] = "CREATE TABLE plateinfo (
	plateid MEDIUMINT AUTO_INCREMENT NOT NULL PRIMARY KEY,
	strainid MEDIUMINT,
	position TINYINT UNSIGNED,
	col TINYINT UNSIGNED,
	row CHAR,	
	platenumber SMALLINT UNSIGNED,
	KEY(strainid))
        TYPE=InnoDB;";

$maketable["cellcycle"] = "CREATE TABLE cellcycle (
	cellcycleid TINYINT AUTO_INCREMENT NOT NULL PRIMARY KEY,
	phase VARCHAR(10))
        TYPE=InnoDB;";
	
$maketable["localization"] = "CREATE TABLE localization (
	localizeid MEDIUMINT AUTO_INCREMENT NOT NULL PRIMARY KEY,
	setid MEDIUMINT,
	userid MEDIUMINT,
	cellcycleid TINYINT,
	cellmorphologyid TINYINT,
	cellbrightnessid TINYINT,
	subcellhomogeneityid TINYINT,
	subcellid SMALLINT,
	xcoord SMALLINT UNSIGNED,
	ycoord SMALLINT UNSIGNED,
	timeofentry TIMESTAMP,
	KEY(setid),
	KEY(cellcycleid),
	KEY(subcellid))
        TYPE=InnoDB;";

$maketable["oligos"] = "CREATE TABLE oligos (
	oligoid MEDIUMINT AUTO_INCREMENT NOT NULL PRIMARY KEY,
	orfid MEDIUMINT,
	F2 VARCHAR(100),
	R1 VARCHAR(100),
	KEY(orfid))
        TYPE=InnoDB;";
	
$maketable["checkprimers"] = "CREATE TABLE checkprimers (
	checkprimerid MEDIUMINT AUTO_INCREMENT NOT NULL PRIMARY KEY,
	orfid MEDIUMINT,
	seq VARCHAR(100),
	Tm FLOAT,
	productsize FLOAT,
	KEY(orfid))
        TYPE=InnoDB;";

$maketable["projects"] = "CREATE TABLE projects (
	projectid MEDIUMINT AUTO_INCREMENT NOT NULL PRIMARY KEY,
	userid MEDIUMINT,
	name VARCHAR(100),
	descript VARCHAR(255),
	active BIT DEFAULT 1,
	KEY(userid))
        TYPE=InnoDB;";

$maketable["users"] = "CREATE TABLE users (
	userid MEDIUMINT AUTO_INCREMENT NOT NULL PRIMARY KEY,
	login VARCHAR(50),
	passwd VARCHAR(50),
	realname VARCHAR(150),
	active BIT DEFAULT 1,
	email VARCHAR(150))
        TYPE=InnoDB;";

$maketable["projectsximages"] = "CREATE TABLE projectsximages (
	projectid MEDIUMINT,
	imageid MEDIUMINT,
	KEY(projectid),
	KEY(imageid))
        TYPE=InnoDB;";

$maketable["usersxprojects"] = "CREATE TABLE usersxprojects (
	userid MEDIUMINT,
	projectid MEDIUMINT,
	rights TINYINT UNSIGNED,
	KEY(userid),
	KEY(projectid))
        TYPE=InnoDB;";

//echo $maketable["sets"];
//exit;

// Connect to mysql using above user, password
$dbh = mysqli_connect("localhost",$dbuser,$dbpwd)
	or exit("Could not connect to database.  Make sure user \"$dbuser\" is a valid MySQL user and retry.");

// Attempt to connect to correct database; else create it, connect	
if (!mysqli_select_db($database)) {
	mysqli_create_db($database);
	mysqli_select_db($database);
		print "CREATED DATABASE <b>$database</b><p>";
}

$i = 0;
// Create tables if nonexistent
foreach ($maketable as $key => $value) {
	if (!mysqli_query("SELECT * from $key")) {
		if (mysqli_errno() == 1146) {
			mysqli_query($value);
			$i++;
			print "$i. CREATED TABLE <b>$key</b><br>";
		}
	}
	else { print "TABLE <b>$key</b> already exists!<br>"; }
}

// Create sysadmin account
$sqladmin = "SELECT login FROM users WHERE login='$syslogin'";
$res = mysqli_query($sqladmin);
if (mysqli_num_rows($res) == 0) {
	$sqladd = "INSERT INTO users (login, passwd, realname, email) VALUES ('$syslogin', '$syspwd', 'System Administrator', '$sysemail')";
	if (mysqli_query($sqladd)) { print "<p>Login: <b>$syslogin</b> successfully added:<br>
			Password: <b>$syspwd</b><br>
			Email: <b>$sysemail</b><p>"; }
	}
else { print "<p><b>System Administrator</b> account exists."; }

print "<p>Remember to <b>chown</b> of www directory to <b>apache</b> or <b>www</b> (whichever user httpd uses).
<p>Remember to create the <b>uploads</b> and <b>images</b> directories with <b>full owner privileges </b> but <b>no public access</b> privileges <b>(700)</b>.";
print "<p>Remember to add the aliases for the <b>images</b> and <b>uploads</b> directories
	<p>by adding Alias /images/ \"/home/gfp/images/\"
Alias /uploads/ \"/home/gfp/uploads/\" to the httpd.conf file!";

/* ADDING MYSELF ALWAYS FOR CONVENIENCE */
$sql = "INSERT INTO users (login, passwd, realname, email) VALUES 
        ('lcgerke', 'illadelph', 'Luke Gerke', 'lcgerke@oddpost.com')";
if (mysqli_query($sql)) { print "<p>ADDED THE LCGERKE USER<br>"; }

mysqli_close($dbh);
?>
