<?php
require("locInclude.php");
require("$include_dir/include.php");
//printheader();

print "<table border>\n
	<tr><th>Name</th><th>Value</th></tr>\n";

foreach($HTTP_SESSION_VARS as $key=>$value) {
	print "<tr><td align=right>".$key."</td><td>".$value."</td></tr>\n";
}

print "</table>";
printfooter();
?>