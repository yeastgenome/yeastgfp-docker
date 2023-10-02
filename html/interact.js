var window_status = new Array();

var images_loaded = null;

var on_m_0 = new Image();
var off_m_0 = new Image();

var on_m_1 = new Image();
var on_m_2 = new Image();
var on_m_3 = new Image();
var on_m_4 = new Image();
var on_m_5 = new Image();
var on_m_6 = new Image();
var on_m_7 = new Image();
var on_m_8 = new Image();
var on_m_9 = new Image();
var on_m_10 = new Image();

var off_m_1 =  new Image();
var off_m_2 =  new Image();
var off_m_3 =  new Image();
var off_m_4 =  new Image();
var off_m_5 =  new Image();
var off_m_6 =  new Image();
var off_m_7 =  new Image();
var off_m_8 =  new Image();
var off_m_9 =  new Image();
var off_m_10 =  new Image();

var on_s_1 = new Image();
var on_s_2 = new Image();
var off_s_1 = new Image();
var off_s_2 = new Image();
var on_s_3 = new Image();
var on_s_4 = new Image();
var off_s_3 = new Image();
var off_s_4 = new Image();


window_status[0] = "UCSF homepage";
window_status[1] = "proteomics imaging db  >  p r o j e c t s";
window_status[2] = "proteomics imaging db  >  u p l o a d";
window_status[3] = "proteomics imaging db  >  s c o r i n g";
window_status[4] = "proteomics imaging db  >  l i s t";
window_status[5] = "proteomics imaging db  >  q u e r y";
window_status[6] = "proteomics imaging db  >  ";
window_status[7] = "proteomics imaging db  >  ";
window_status[8] = "proteomics imaging db  >  ";
window_status[9] = "proteomics imaging db  >  ";
window_status[10] = "proteomics imaging db  >  ";

function load_images(which) {
    if (which == "main") {
	on_m_0.src = "img/m_0_on.gif";
	on_m_1.src = "img/m_1_on.gif";
	on_m_2.src = "img/m_2_on.gif";
	on_m_3.src = "img/m_3_on.gif";
	on_m_4.src = "img/m_4_on.gif";
	on_m_5.src = "img/m_5_on.gif";
	on_m_6.src = "img/m_6_on.gif";
	on_m_7.src = "img/m_7_on.gif";
	on_m_8.src = "img/m_8_on.gif";
	on_m_9.src = "img/m_9_on.gif";
	on_m_10.src = "img/m_10_on.gif";
	off_m_0.src = "img/m_0_off.gif";
	off_m_1.src = "img/m_1_off.gif";
	off_m_2.src = "img/m_2_off.gif";
	off_m_3.src = "img/m_3_off.gif";
	off_m_4.src = "img/m_4_off.gif";
	off_m_5.src = "img/m_5_off.gif";
	off_m_6.src = "img/m_6_off.gif";
	off_m_7.src = "img/m_7_off.gif";
	off_m_8.src = "img/m_8_off.gif";
	off_m_9.src = "img/m_9_off.gif";
	off_m_10.src = "img/m_10_off.gif";
    }
    if (which == "scoring") {
	//on_s_1.src = "img/s_1_on.gif";
	//off_s_1.src = "img/s_1_off.gif";
	//on_s_3.src = "img/s_3_on.gif";
	//off_s_3.src = "img/s_3_off.gif";
	//on_s_4.src = "img/s_4_on.gif";
	//off_s_4.src = "img/s_4_off.gif";
    }
}

function mapcoord(image) {
    if (image == 1) {
	p_map_x = event.x - p_xoffset;
	p_map_y = event.y - yoffset;
	window.document.phase_val.x_val.value = p_map_x;
	window.document.phase_val.y_val.value = p_map_y;
    }
    if (image == 2) {
	g_map_x = event.x - g_xoffset;
	g_map_y = event.y - yoffset;
	window.document.gfp_val.x_val.value=g_map_x;	
	window.document.gfp_val.y_val.value=g_map_y;
    }
}

function coordset(image) {
    if (image == 1) {
	cell_data.x_val.value = p_map_x;
	cell_data.y_val.value = p_map_y;
    }
    if (image == 2) {
	cell_data.x_val.value = g_map_x;
	cell_data.y_val.value = g_map_y;
    }
}

function menu(direction,which) {
    if (direction == "on") {
	window.status = window_status[which];
    }
    else {
	window.status = "";
    }
    img_source = eval(direction + "_m_" + which + ".src");
    target_img = "menu_" + which;
    document.images[target_img].src = img_source;
}

function scoring(direction,which) {
    if (direction == "on") {
	window.status = window_status[which];
    }
    else {
	window.status = "";
    }
    img_source = eval(direction + "_s_" + which + ".src");
    target_img = "score_" + which;
    document.images[target_img].src = img_source;
}

function preloadImages(the_images_array) {
    for(var loop = 0; loop < the_images_array.length; loop++) {
 	  var an_image = new Image();
	  an_image.src = the_images_array[loop];
    }
}

function do_update(loc) {
	parent.scoring.location = "scoreFrame.php?set=" + loc;
}







