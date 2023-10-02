<?php
require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/secure.php");

if (!$_GET['imageid']) {
	centermsg("No image selected.");
	exit; }

$sql = "SELECT dirpath FROM images WHERE imageid = " .$_GET['imageid'];
$rs = dbquery($sql);
if (mysqli_num_rows($rs) <> 1) {
	centermsg("Error.  Cannot retrieve image.");
	exit; }
$row = mysqli_fetch_assoc($rs);

if ($_GET['type'] == "jpeg") {
	preg_match("/^.*(\..+)$/i", $row['dirpath'], $matchfile);
	$origext = $matchfile[1];	/** returns .TIF **/
	$file = str_replace($origext, $viewext, $row['dirpath']); }
else { $file = $row['dirpath']; }
$file = $image_dir . $file;

//preg_match("/^.*\/(.+)$/i", $file, $matchfile);
//$filename = $matchfile[1];
$filename = basename ($file);

//header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/octet-stream");
header("Content-disposition: attachment; filename=". $filename);
header("Content-length: ".filesize($file));

$fd = fopen($file,'r');
fpassthru($fd);
?>
