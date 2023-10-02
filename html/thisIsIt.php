<?

require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/secure.php");

?>

<html>
<head>

<style>

div {
 height: 28px; 
 width: 100%;
 background-color: #6699BB;
 border: 1px solid black;

}

#status {
 position: absolute;
 left: 0px;
 background-color: #447799;
 top: 0px;
}

#allUser {
 position: absolute;
 left: 0px;
 top: 28px;
}

#superUser {
 position: absolute;
 left: 0px;
 top: 56px;
}

#sysAdmin {
 position: absolute;
 left: 0px;
 top: 84px;
}

.menuSection, .menuInfo, .menuValue, .menuLink, .menuTitle {
  font-family: Verdana, sans-serif;
  font-size: 10pt;
  font-weight: 900;
  padding-top: 6px;
  padding-bottom: 6px;
  padding-right: 6px;
  width: 100%; 
}

.menuTitle {
  background-color: #336699;
}

.menuInfo {
  font-size: 9pt;
  color: #000000;
  padding-left: 6px;

} 

.menuValue {
  font-size: 9pt;
 color: #FFFFFF;
  padding-left: 6px;
} 

.menuSection {
 color: #000000;
  background-color: #447799;
  padding-left: 20px;
  font-style: italic;
}

.menuLink {
  font-size: 8pt;
 color: #FFFFFF;
 text-decoration: none;
 font-weight: 700;
  padding-left: 6px;
  padding-top: 7px;
  padding-bottom: 7px;
}

a:hover {
  color: #FFFFFF;
  background-color: #77AACC;
} 

a:active {
  /* background-color: #DDDDDD; */
}

.links {
 text-align: center;
}

</style>

</head>

<body>
<div id="status">
<table cellspacing=0 cellpadding=0 border=0><tr>
<td width=200><a href="about.php"><img src="img/title_off.jpg" border=0></a></td>
<td><b class="menuInfo">Login: </b></td>
<td><b class="menuValue">Adam Carroll</b></td>
<td><b class="menuInfo">Project: </b></td>
<td><b class="menuValue">Adam Carroll</b></td>
<td><b class="menuInfo">Privileges: </b></td>
<td><b class="menuValue">Adam Carroll</b></td>
</tr></table>
</div>

<div id="allUser">
<table cellspacing=0 cellpadding=0 border=0><tr>
<td width=200><b class="menuSection">user functions &gt;&gt;</b></td>
<td><a class="menuLink" href="projectchoose.php">&nbsp;change projects&nbsp;</a></td>
<td><a class="menuLink" href="query.php">&nbsp;query&nbsp;</a></td>
<td><a class="menuLink" href="setToOrf.php">&nbsp;setID->ORF&nbsp;</a></td>
<td><a class="menuLink" href="makeInfoTable.php">&nbsp;download&nbsp;</a></td>
</tr></table>
</div>


<div id="superUser">
<table cellspacing=0 cellpadding=0 border=0><tr>
<td width=200><b class="menuSection">superuser &gt;&gt;</b></td>
<td><a class="menuLink" href="upload.php">&nbsp;upload&nbsp;</a></td>
<td><a class="menuLink" href="scoreFrame.php">&nbsp;scoring</a></td>
<td><a class="menuLink" href="pruneSetup.php">&nbsp;pruning&nbsp;</a></td>
<td><a class="menuLink" href="setUnscored.php">&nbsp;revisit&nbsp;</a></td>
<td><a class="menuLink" href="fixup.php">&nbsp;unskip/unlock&nbsp;</a></td>
</tr></table>
</div>

<div id="sysAdmin">
<table cellspacing=0 cellpadding=0 border=0><tr>
<td width=200><b class="menuSection">sysAdmin &gt;&gt;</b></td>
<td><a class="menuLink" href="users.php">&nbsp;add users&nbsp;</a></td>
<td><a class="menuLink" href="add_subcell.php">&nbsp;localizations&nbsp;</a></td>
</tr></table>
</div>

</body>
<html>


