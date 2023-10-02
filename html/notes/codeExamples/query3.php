<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html><head><title>glish.com : CSS layout techniques : 3 columns, the holy grail</title>

<style type="text/css">
	@import "all.css"; 
	
	body {
		margin:10px 10px 0px 10px;
		padding:0px;
		}
	
	#leftcontent {
		position: absolute;
		left:10px;
		top:50px;
		width:200px;
		background:#fff;
		border:1px solid #000;
		}

	#centercontent {
		background:#fff;
   		margin-left: 199px;
   		margin-right:199px;
		border:1px solid #000;
		voice-family: "\"}\"";
		voice-family: inherit;
   		margin-left: 201px;
   		margin-right:201px;
		}
	html>body #centercontent {
   		margin-left: 201px;
   		margin-right:201px;
		}

	#rightcontent {
		position: absolute;
		right:10px;
		top:50px;
		width:200px;
		height: 280px;
		background:#fff;
		border:1px solid #000;
		}
	
	#banner {
		background:#fff;
		height:40px;
		border-top:1px solid #000;
		border-right:1px solid #000;
		border-left:1px solid #000;
		voice-family: "\"}\"";
		voice-family: inherit;
		height:39px;
		}
	html>body #banner {
		height:39px;
		}
		
	p,h1,pre {
	    font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size:10px;
		margin:0px 10px 10px 10px;
		}
	a {
	   	font-style: normal; 
		line-height: normal; 
		text-decoration: none; 
		color: #336699
	  	}	
	h1 {
		font-size:12px;
		padding-top:10px;
		}
		
	#banner h1 {
		font-size:14px;
		padding:10px 10px 0px 10px;
		margin:0px;
		}
	
	#rightcontent p {
		font-size:10px
		}
	
</style>
</head><body>
<div id="banner"><h1>&gt;&gt; query tools for the GFP database</h1></div>
<div id="leftcontent">
	<h1>&gt;&gt; fields to display</h1>
<pre><select type=multiple size=10>
	 		 <option>so's your old man
			 <option>and yer mama, too
			 <option>and her mama.
	 </select>
</pre>
	<p class="greek">
	 choose what data you would like to see displayed in your results table<br>
	 use ctrl-click to select more than one field
	 </p>
</div>

<div id="centercontent">
	<h1>&gt;&gt; filters</h1>
<p><input type=checkbox name="check_one">&nbsp;<select name="box_one">
   <? 	 $sql = "SELECT subcellname,subcellid
	 	  FROM subcell  
		  ORDER BY subcellid";
	 $res = dbquery($sql);
	 while ($row = mysql_fetch_assoc($res)) {
	 	   $localizations[$row["subcellid"]] = $row["subcellname"];
	 	   print "<OPTION value=".$row["subcellid"].">".$row["subcellname"]."\n";
	 }
   ?>
   </select>&nbsp;localization</p>
<p><input type=checkbox name="check_two">&nbsp;<select name="box_two">
   <? 	 $sql = "SELECT subcellname,subcellid
	 	  FROM subcell  
		  ORDER BY subcellid";
	 $res = dbquery($sql);
	 while ($row = mysql_fetch_assoc($res)) {
	 	   $localizations[$row["subcellid"]] = $row["subcellname"];
	 	   print "<OPTION value=".$row["subcellid"].">".$row["subcellname"]."\n";
	 }
   ?>
   </select>&nbsp;localization</p>
   <!-- OTHER OPTIONS
<p><input type=checkbox name="box_one">&nbsp;<select name="box_three">
	<option value=1>loc1<option value=1>loc1<option value=1>loc1<option value=1>loc1<option value=1>loc1	   
   </select>&nbsp;localization</p>
<p><input type=checkbox name="box_one">&nbsp;<select name="box_one">
	<option value=1>loc1<option value=1>loc1<option value=1>loc1<option value=1>loc1<option value=1>loc1	   
   </select>&nbsp;cell cycle</p>
<p><input type=checkbox name="box_one">&nbsp;<select name="box_one">
	<option value=1>loc1<option value=1>loc1<option value=1>loc1<option value=1>loc1<option value=1>loc1	   
   </select>&nbsp;user</p>
<p><input type=checkbox name="box_one">&nbsp;<select name="box_one">
	<option value=1>loc1<option value=1>loc1<option value=1>loc1<option value=1>loc1<option value=1>loc1	   
   </select>&nbsp;gene name</p>
<p><input type=checkbox name="box_one">&nbsp;<select name="box_one">
	<option value=1>loc1<option value=1>loc1<option value=1>loc1<option value=1>loc1<option value=1>loc1	   
   </select>&nbsp;orf name</p>
-->
<p>use the checkboxes to activate</p>
</div>



<div id="rightcontent">
	<h1>&gt;&gt; options</h1>
	<p><input type=checkbox>single cell with BOTH selected localizations</p>
	<p><input type=checkbox>strain with cell cycle regulation of localization</p>
	<p><input type=checkbox>representative sets only</p>
	<p>scoring: <input type=radio value=1>manual<input type=radio value=0>auto
	<br>
	<p><input type=submit value="go" name="btnQuery"> submit query...
</div>

</body>
</html>
