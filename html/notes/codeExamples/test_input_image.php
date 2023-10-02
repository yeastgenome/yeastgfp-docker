<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>Untitled</title>
</head>

<body>

<script language="JavaScript"><!--
function validate(myobject) {
    if (myobject.mytext.value == "yes")
        return true;
    else {
        alert('Data not completed successfully');
        return false;
    }
}

function check(myobject) {
    if (validate(myobject))
        myobject.submit();
}
//--></script>

<form name="myform" onSubmit="return validate(document.myform)" method=post action="postvars.php">
<input type="text" name="mytext" value="">
<a href="javascript:check(document.myform)"><img src="image_1.jpg" border="0" width="16" height="16"></a>
</form>


</body>
</html>
