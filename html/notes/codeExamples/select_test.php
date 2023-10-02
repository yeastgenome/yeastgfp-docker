<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>Untitled</title>
	<link rel="stylesheet" href="imagedb.css">
	<script src="interact.txt">
	</script>

</head>

<body>

<script language=JavaScript>

		function ref_image() {
				 the_image.src="img/ref/bud_neck.jpg";
		}
		
</script>

<select OnChange="ref_image();">
<option>one
<option>two
<option>three
</select>

<img src="img/ref/blank.gif" name="the_image">


</body>
</html>
