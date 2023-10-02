<script language="JavaScript">
<?php
while (list($key, $val) = @each($HTTP_POST_VARS)) {

  if(preg_match("/imageField_x/", $key)) {
    print "parent.scoringImagesFrame.document.pane".$_POST['hiddenWhichPane'].
      "_jscript_displays_form.x_val.value=".$val.";\n";

    print "parent.scoringImagesFrame.document.cell_data.x_val.value=".$val.";\n";

  }
  if(preg_match("/imageField_y/", $key)) {
    print "parent.scoringImagesFrame.document.pane".$_POST['hiddenWhichPane'].
      "_jscript_displays_form.y_val.value=".$val.";\n";

    print "parent.scoringImagesFrame.document.cell_data.y_val.value=".$val.";\n";
//    print "alert((parent.scoringImagesFrame.document.getElementById('pane1RealPictureLayer')).getClass());\n";

  }
}
?>
</script>
