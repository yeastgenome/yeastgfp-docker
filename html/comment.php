<?php

require("locInclude.php");
require("$include_dir/include.php");
//require("$include_dir/secure.php");
require("$include_dir/projects_inc.php");
    

?>

<html>
<head>
        <title>comment submission for orf <?=$_GET['orfid']?>-- yeastgfp.ucsf.edu</title>
        <link rel="stylesheet" href="imagedb.css">
	<link rel="stylesheet" href="formatQuery.css">
        <meta name="robots" content="noindex,nofollow">
</head>

<body bgcolor="#FFFFFF">

<?

if (($_GET['orfid']) == "" || preg_match("/\D/",$_GET['orfid'])) {
  //print "<p>not targeted by the correct page....";
  $idErrorMsg = "";
  $idErrorMsg .= "<table width=400 height=280 border=0 cellspacing=0 cellpadding=0><tr><td align=center valign=middle>";
  $idErrorMsg .= "<p><b>No ORF Specified -- not targeted by the correct page</b>";
  $idErrorMsg .= "<p>Please close the window and return to yeastgfp.ucsf.edu";
  $idErrorMsg .= "</td></tr></table>";
} else {
  $orfId = $_GET['orfid']; 
}

// CHECK IF WE'RE COMING FROM A SUBMITTED FORM
if ($_GET['btnSubmit']) {  
  
  // CHECK IF FORM IS COMPLETE, ALL FIELDS, USING ERRORMSG ARRAY
  // EMAIL
  if ($_GET['email'] == "") {
    $errorMsg['email'] = "please complete the email field";
  } else {
    $email = $_GET['email'];
    // VERIFY EMAIL ADDRESS COMES FROM A VALID DOMAIN
    list($user,$domain) = split("@",$email);
    if (!checkdnsrr($domain,"MX")) {
      $errorMsg['email'] = "please enter a valid email address";
    }    
  }
  // NAME
  if ($_GET['name'] == "") {
    $errorMsg['name'] = "please complete the name field";
  } else {
    $name = addslashes($_GET['name']);
  }
  // COMMENT
  if ($_GET['comment'] == "") {
    $errorMsg['comment'] = "please enter a comment";
  } else {
    $commentLength = strlen($_GET['comment']);
    // COMMENT SIZE
    if ($commentLength > 400) {
      $errorMsg['comment'] = "please limit your comments to 400 characters";
    } else {
      $comment = addslashes($_GET['comment']);
    }
  }
  // PMID -- NOT CURRENTLY REQUIRED
  if ($_GET['pmid'] == "") {
    //    $errorMsg['comment'] = "please enter a PubMed ID number";
  } else {
    // BUT MUST BE NUMBERS ONLY
    if (!preg_match("/\D/",$_GET['pmid'])) {
      $pmid = $_GET['pmid'];
    } else {
      $errorMsg['pmid'] = "numbers only";
    }
  }
  
  // IF NO ERROR MESSAGES
  if (!is_array($errorMsg)) {       
    
    // ENTER INTO DB
    $sql = "INSERT INTO comment (name,email,orfid,comment,pmid,time) VALUES ('".$name."','".$email."',".$orfId.",'".$comment."',".$pmid.",".$_GET['time'].")";
    //    print $sql;
    dbquery($sql);

    // SET COMPLETED MESSAGE
    $completeMsg = "";
    $completeMsg .= "<table width=400 height=280 border=0 cellspacing=0 cellpadding=0><tr><td align=center valign=middle>";
    $completeMsg .= "<p><b>Thank you for submitting your comments to yeastgfp.ucsf.edu</b>";
    $completeMsg .= "<p>Your comment will be viewable soon.";
    $completeMsg .= "</td></tr></table>";

  } else {
    // HIDDEN VARIABLES TO REBUILD PAGE
    $hiddenVar = "<input type=hidden name=\"\" value=\"\">";
  }
}

print "<form name=\"commentForm\"action=\"".$_SERVER['PHP_SELF']."\" method=get>"; 

print "<table width=440 border=0 cellpadding=5 cellspacing=0 style=\"border:1pt solid black; \">\n";
print "<tr bgcolor=\"#669966\">\n";
print "<td class=\"title\">\n";
print "&nbsp;&gt;&gt; submit a comment on this orf";
print "</td>\n";
print "</tr>\n";

// ORF INFORMATION
print "<tr bgcolor=\"#99CC99\">\n";
print "<td>\n";

if (!isset($idErrorMsg)) {
  dumpOrfInfoTableForComment($orfId);
} else {
  print "&nbsp;\n";
}

print "</td>\n";
print "</tr>\n";

print "<tr bgcolor=\"#FFFFFF\">\n";
print "<td>\n";

 
if (isset($completeMsg)) {
  print $completeMsg;
} else if (isset($idErrorMsg)) {
  print $idErrorMsg;
} else {
  
  print "<table border=0 cellspacing=0 cellpadding=2>";
  print "<tr>\n";
  print "<td align=center>\n";
  print "<b>name </b>";
  print "</td>\n";
  print "<td align=left>\n";
  print "<input type=text name=\"name\" size=40 value=".$_GET['name'].">";
  
  // NAME ERROR MESSAGE
  if (isset($errorMsg['name'])) {
    print "<br>";
    print "<b class=\"error\">";
    print $errorMsg['name'];
    print "</b>";
  }
  
  print "</td>\n";
  print "</tr>\n";
  print "<tr>\n";
  print "<td align=center>\n";
  print "<b>email </b>";
  print "</td>\n";
  print "<td align=left>\n";
  print "<input type=text name=\"email\" size=40  value=".$_GET['email'].">";
  
  // EMAIL ERROR MESSAGE
  if (isset($errorMsg['email'])) {
    print "<br>";
    print "<b class=\"error\">";
    print $errorMsg['email'];
    print "</b>";
  }
  
  print "</td>\n";
  print "</tr>\n";
  print "<tr>\n";
  print "<td align=center valign=middle>\n";
  print "<b>comments</b>";
  print "<br>(limit 400 <br>characters)";
  print "</td>\n";
  print "<td align=left valign=middle>\n";
  print "<textarea name=\"comment\" cols=40 rows=10>".$_GET['comment']."</textarea>";
  
  // COMMENT ERROR MESSAGE
  if (isset($errorMsg['comment'])) {
    print "<br>";
    print "<b class=\"error\">";
    print $errorMsg['comment'];
    print "</b>";
  }
  
  print "</td>\n";
  print "</tr>\n";
  print "<tr>\n";
  print "<td align=center>\n";
  print "<b>PMID</b>";
  print "<br>(if published)";
  print "</td>\n";
  print "<td align=left>\n";

  print "<input type=text name=\"pmid\" size=20 value=".$_GET['pmid'].">";
  
  // PMID ERROR MESSAGE
  if (isset($errorMsg['pmid'])) {
    print "&nbsp;&nbsp;";
    print "<b class=\"error\">";
    print $errorMsg['pmid'];
    print "</b>";
  }
  
  print "<a href=\"http://www.nlm.nih.gov/bsd/pubmed_tutorial/glossary.html#p\" target=\"_blank\">&nbsp;&nbsp;&lt; what's this? &gt;</a>"; 
  print "</td>\n";
  print "</tr>\n";
  print "<tr>\n";
  print "<td colspan=2 align=center valign=middle>\n";
  print "<input type=reset value=\"clear form\">";
  print "&nbsp;&nbsp;";
  print "<input type=submit name=\"btnSubmit\" value=\"submit comment\">";
  print "</td>\n";
  print "</tr>\n";
  print "</table>";

}

print "</td>\n";
print "</tr>\n";
print "<tr bgcolor=\"#99CC99\">\n";
print "<td>\n";
print "additional remarks can be sent to <a href=\"mailto:jan.ihmels@gmail.com\">jan.ihmels@gmail.com</a>";
print "</td>\n";
print "</tr>\n";
print "</table>";

// CLOSE WINDOW
print "<p align=right><a href=\"javascript:void(window.close())\">close window &nbsp;&nbsp;</a>";

// PASS ORFID AS HIDDEN
print "<input type=hidden name=\"time\" value=\"".time()."\">";
print "<input type=hidden name=\"orfid\" value=\"".$_GET['orfid']."\">";
print "</form>";

?>

