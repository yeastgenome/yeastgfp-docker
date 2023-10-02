<?
require("locInclude.php");
require("$include_dir/include.php");
require("$include_dir/secure.php");
require("$include_dir/projects_inc.php");

pass($priv['Superuser']);

?>
<link href="prune.css" rel="stylesheet" type="text/css">
<p class="titleText">   

<form name="pruneSkippedOrfs" method="post" action="newPruneLibraries.php">
<input type="submit" name="buttonSkippedOrfsSubmit" value="Prune Skipped Orfs!!!">
</form>

<form name="pruneUnprunedOrfs" method="post" action="newPruneLibraries.php">
<input type="submit" name="buttonUnprunedOrfsSubmit" value="Prune Next Available Orf!!!">
</form>

