<?php
/* THIS FILE BUILDS OR UPDATES A TABLE THAT DESCRIBES THE REST OF THE
   GFP DATABASE.  THAT TABLE WILL BE USED TO HELP GUIDE DISPLAY OF
   INFORMATION FROM ALL THE TABLES
*/


require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/secure.php");
require("$include_dir/projects_inc.php");

$tableColumnInfoTableName = "tablecolumninfo";

$result = mysqli_list_tables("gfp");
if (!$result) {
  print "DB Error, could not list tables\n";
  print 'MySQL Error: ' . mysqli_error();
  exit;
}

while ($row = mysqli_fetch_row($result)) {
  $tableNameList[] = $row[0];
}

mysqli_free_result($result);

/* BUILD THE TABLE IF IT DOESN'T EXIST */
print_r($tableNameList);
printWB($tableColumnInfoTableName);
  
if(!in_array($tableColumnInfoTableName, $tableNameList)) {
  $sql = "CREATE TABLE $tableColumnInfoTableName (
            tablecolumninfoid mediumint(9) NOT NULL auto_increment,
            tablename VARCHAR(100) DEFAULT 'ERROR',
            columnname VARCHAR(100) DEFAULT 'ERROR',
            queryvisible ENUM('T','F') DEFAULT 'T',
            descriptivename VARCHAR(100) DEFAULT 'ERROR',
            PRIMARY KEY (tablecolumninfoid)
          ) TYPE=InnoDB";
  dbquery($sql);
}

/* NOW GO THROUGH AND LOOK FOR ENTRIES THAT DON'T EXIST */
foreach($tableNameList as $tableName) {
  $sql = "SELECT * FROM ".$tableName;
  $res = dbquery($sql);
  $numFields = mysqli_num_fields($res);
  for($i=0; $i<$numFields; $i++) {
    $columnName = mysqli_field_name($res,$i);
    $sqlGetTableColumnInfoTable =
      "SELECT * FROM ".$tableColumnInfoTableName.
      " WHERE
       tablename = '$tableName' AND columnname = '$columnName'";
    printWB($sqlGetTableColumnInfoTable);

    $resGetTableColumnInfoTable = dbquery($sqlGetTableColumnInfoTable);
    if(mysqli_num_rows($resGetTableColumnInfoTable) == 0) {
      $sqlInsert = "INSERT INTO tablecolumninfo (tablename,
                    columnname) VALUES ('$tableName', '$columnName')";
      dbquery($sqlInsert);
      printWB($sqlInsert);
    }
    

    
  }

}






/*
foreach($result as $tableName) {
  printWB($tableName);
}
*/






?>


