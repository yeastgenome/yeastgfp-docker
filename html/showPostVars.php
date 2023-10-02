<?php
print "<table border>\n
<tr><th>Name</th><th>Value</th></tr>\n";

ksort ($_POST);

foreach ($_POST as $key => $value) {
	print "<tr><td align=right>".$key."</td><td>".$value."</td></tr>\n";
}

print "</table>"
?>