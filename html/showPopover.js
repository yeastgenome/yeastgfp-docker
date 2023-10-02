
<!--
// We are not detecting true browser, just which technique is used to address the layers
var isNN      = (document.layers)? true:false;
var isIE      = (document.all)? true:false;
var isNN6     = ((!document.all) && (document.getElementById))

var offsetx=60, offsety=15, popupWidth=100;
 
var skin;
if (isNN) skin = document.popup;
else if (isIE) skin = popup.style;
else if (isNN6) skin = document.getElementById("popup").style;

if (isNN) document.captureEvents(Event.MOUSEMOVE);
document.onmousemove = getMouse;

function getMouse(e) 
{
  var x = ((isNN)||(isNN6)) ? e.pageX : event.x+document.body.scrollLeft; 
  var y = ((isNN)||(isNN6)) ? e.pageY : event.y+document.body.scrollTop;
  skin.left = x+offsetx-(popupWidth/2); //this controls the position
  skin.top  = y+offsety; //this controls the position
}

function popupClose()
{
  skin.visibility = "hidden";
  self.status = "";
}

function popupOpen()
{
  var args=arguments;
  var message=args[0];
  var statusText=(args.length > 1 ? args[1] : "");
  var thePopup  = "<TABLE class=popborder BORDER=0 CELLSPACING=0 CELLPADDING=1 width="+popupWidth+"><TR><TD>"
      thePopup += "<TABLE class=popfground BORDER=0 CELLSPACING=0 CELLPADDING=2 width=100%><TR><TD class=poptext>"
      thePopup += message+"</TD></TR></TABLE></TD></TR></TABLE>"
    
  if (isNN) 
  { 
     skin.document.open();
     skin.document.write(thePopup); 
     skin.document.close();
  }
  else if (isIE) 
  {
     document.all("popup").innerHTML = thePopup;
  }
  else if (isNN6)
  {
     document.getElementById("popup").innerHTML = thePopup;
  }
  skin.visibility = "visible"; 
  self.status = statusText;
  return true;
}
//-->
