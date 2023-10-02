<?php

print "<html>";


require("locInclude.php");
require("$include_dir/include.php");
// require("$include_dir/secure.php");
// require("$include_dir/projects_inc.php");


// needs to be called by displaying page
// produces <head> </head> and <body> tags
dumpOrfDisplayHeaderJs();
dumpBodyForJsIcons();

// information passed in to function
$orf = "666";
$arrayInit = array (2 => '17076', 3 => '8570', 4 => '8575', 5 => '8576', 6 => '8577');
$arrayColoc = array (7 => '8571', 8 => '8572', 9 => '8580', 10 => '857', 11 => '8578', 12 => '8590');
$arrayFinal = array (3 => '8573', 5 => '8574');


// iterate to see how it works for multiple orfs....

$j = 0;

while ($j < 5) {

  displayOrfLocs($orf,$arrayInit,$arrayColoc,$arrayFinal);
  $j++;
  $orf += $j;
  
}
 
print "</body>";
print "</html>";

?>