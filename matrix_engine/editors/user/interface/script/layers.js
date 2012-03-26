var isIE4;
var isNav4;
var isNav6;


function processClicks() {
  WMHideLayer('pulldown0');
  WMHideLayer('pulldown1');
  //currentMenuId = 0;
  //currentPageId = 0;
}

if (window.Event) // Navigator 4.0x
  document.captureEvents(Event.MOUSEUP);
document.onmouseup = processClicks;



// Determine which browser is used
function setbrowser() {
	if (navigator.appVersion.charAt(0) == "4") {
		if (navigator.appName.indexOf("Explorer") >= 0) {
			isIE4 = true;
		}
		else {
			isNav4 = true;
		}
	}
	else if (navigator.appVersion.charAt(0) > "4") {
		isNav6 = true;
	}
}

setbrowser();

//------- global vars needed to redirect the jump --------//
var currentMenuId = 0;
var currentPageId = 0;
var currentMenuActive = 0;
var currentLowSub = 0;

//------- these are the general layer functions --------//

function wmShowPullDown(id,jsMenuId, jsMenuLevel, phpMenuID,phpPageID,phpActive,phplowsub) {
	// okay this function does display the menu on the calculated
	// position- and sets the selectedObject var to the current
	// phpPageId
	
	//jsMenuID = verticalPosition
	//jsMenuLevel = horizontalPosition
	WMMoveLayer('pulldown'+id,29+20*jsMenuLevel,16+19* (jsMenuId-1));
	WMShowLayer('pulldown'+id);	
	
	currentMenuId = phpMenuID;
	currentPageId = phpPageID;
	currentMenuActive =phpActive;
	currentLowSub = phplowsub;
}


//------- these are the general layer functions --------//

function WMMoveLayer(layerName,x,y) {
	var n_layer = null;
	
	if (isNav4) {
		n_layer = document.layers[layerName];
	}
	else if (isIE4) {
		n_layer = document.all(layerName).style;
	}
	else if (isNav6) {
		n_layer = document.getElementById(layerName).style;
	}

	if(n_layer != null) {
		n_layer.left = x;
		n_layer.top  = y;
	}
}


function WMToggleLayer(layerName) {
	var n_layer = null;
	
	if (isNav4) {
		n_layer = document.layers[layerName];
	}
	else if (isIE4) {
		n_layer = document.all(layerName).style;
	}
	else if (isNav6) {
		n_layer = document.getElementById(layerName).style;
	}
	
	if(n_layer != null) {
		if(n_layer.visibility=="visible" || n_layer.visibility=="show") {
			WMHideLayer(layerName);
		} else {
			WMShowLayer(layerName);
		}
	}
}


function WMShowLayer(layerName) {
	var n_layer = null;
	
	if (isNav4) {
		n_layer = document.layers[layerName];
	}
	else if (isIE4) {
		n_layer = document.all(layerName).style;
	}
	else if (isNav6) {
		n_layer = document.getElementById(layerName).style;
	}
	
	if(n_layer != null) {
		n_layer.visibility="visible";
	}
}

function WMHideLayer(layerName){
	var n_layer = null;
	if (isNav4) {
		n_layer = document.layers[layerName];
	}
	else if (isIE4) {
		n_layer = document.all(layerName).style;
	}
	else if (isNav6) {
		n_layer = document.getElementById(layerName).style;
	}
	
	if(n_layer != null) {
		n_layer.visibility="hidden";
    }
}
