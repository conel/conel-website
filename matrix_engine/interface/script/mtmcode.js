// Morten's JavaScript Tree Menu
// version 2.3.0, dated 2001-04-30
// http://www.treemenu.com/

// Copyright (c) 2001, Morten Wang & contributors
// All rights reserved.

// This software is released under the BSD License which should accompany
// it in the file "COPYING".  If you do not have this file you can access
// the license through the WWW at http://www.treemenu.com/license.txt

/******************************************************************************
* Define the MenuItem object.                                                 *
******************************************************************************/
function WMMenuItem(text, url, target, icon, menuid, pageid,menuactive,lowsub) {
  this.text = text;
  this.url = url ? url : "";
  this.target =  target ? target : "";
  this.icon = icon ? icon : "";

  this.number = MTMNumber++;
  
  this.menuid = menuid;
  this.pageid = pageid;
  this.menuactive = menuactive;
  this.lowsub = lowsub;

  this.submenu     = null;
  this.expanded    = false;
  this.MTMakeSubmenu = MTMakeSubmenu;
}

function MTMakeSubmenu(menu, isExpanded, collapseIcon, expandIcon) {
  this.submenu = menu;
  this.expanded = isExpanded;
  this.collapseIcon = collapseIcon ? collapseIcon : "norm_page.gif";
  this.expandIcon = expandIcon ? expandIcon : "norm_page.gif";
}

/******************************************************************************
* Define the Menu object.                                                     *
******************************************************************************/

function WMMenu() {
  this.items   = new Array();
  this.MTMAddItem = MTMAddItem;
}

function MTMAddItem(item) {
  this.items[this.items.length] = item;
}

/******************************************************************************
* Define the icon list, addIcon function and MTMIcon item.                    *
******************************************************************************/

function IconList() {
  this.items = new Array();
  this.addIcon = addIcon;
}

function addIcon(item) {
  this.items[this.items.length] = item;
}

function MTMIcon(iconfile, match, type) {
  this.file = iconfile;
  this.match = match;
  this.type = type;
}

/******************************************************************************
* The MTMBrowser object.  A custom "user agent" that'll define the browser    *
* seen from the menu's point of view.                                         *
******************************************************************************/

function MTMBrowser() {
  this.preHREF = "";
  this.MTMable = false;
  this.cssEnabled = true;
  this.browserType = "other";

  if(navigator.appName == "Netscape" && navigator.userAgent.indexOf("WebTV") == -1) {
    if(parseInt(navigator.appVersion) == 3 && (navigator.userAgent.indexOf("Opera") == -1)) {
      this.MTMable = true;
      this.browserType = "NN3";
      this.cssEnabled = false;

    } else if(parseInt(navigator.appVersion) >= 4) {
      this.MTMable = true;
      this.browserType = parseInt(navigator.appVersion) == 4 ? "NN4" : "NN5";
    }
  } else if(navigator.appName == "Microsoft Internet Explorer" && parseInt(navigator.appVersion) >= 4) {
    this.MTMable = true;
    this.browserType = "IE4";
  } else if(navigator.appName == "Opera" && parseInt(navigator.appVersion) >= 5) {
    this.MTMable = true;
    this.browserType = "O5";
  }

  if(this.browserType != "NN4") {
    this.preHREF = location.href.substring(0, location.href.lastIndexOf("/") +1)
  }
}

/******************************************************************************
* Global variables.  Not to be altered unless you know what you're doing.     *
* User-configurable options are at the end of this document.                  *
******************************************************************************/

var MTMLoaded = false;
var MTMLevel;
var WMMenusDisplayed = 0;
var MTMIndices = new Array();

var wmBrowserObj = new MTMBrowser();

var MTMClickedItem = false;
var MTMExpansion = false;

var MTMNumber = 1;
var MTMTrackedItem = false;
var MTMTrack = false;
var MTMFrameNames;

var MTMFirstRun = true;
var MTMUpdating = false;
var MTMWinSize, MTMyval, MTMxval;
var MTMOutputString = "";


/******************************************************************************
* Code that picks up frame names of frames in the parent frameset.            *
******************************************************************************/

function MTMgetFrames() {
  if(wmBrowserObj.MTMable) {
    MTMFrameNames = new Array();
    for(i = 0; i < parent.frames.length; i++) {
      MTMFrameNames[i] = parent.frames[i].name;
    }
  }
}

/******************************************************************************
* Functions to draw the menu.                                                 *
******************************************************************************/

function MTMSubAction(SubItem) {

  SubItem.expanded = (SubItem.expanded) ? false : true;
  if(SubItem.expanded) {
    MTMExpansion = true;
  }

  MTMClickedItem = SubItem.number;

  if(MTMTrackedItem && MTMTrackedItem != SubItem.number) {
    MTMTrackedItem = false;
  }

	setTimeout("MTMDisplayMenu()", 10);
  if(SubItem.url == "" || !SubItem.expanded) {
    setTimeout("MTMDisplayMenu()", 10);
    return false;
  } else {
    return true;
  }
}

function MTMStartMenu() {
  MTMLoaded = true;
  if(MTMFirstRun) {
    MTMDisplayMenu();
  } 
}

function MTMDisplayMenu() {
  if(wmBrowserObj.MTMable && !MTMUpdating) {
    MTMUpdating = true;

    if(MTMFirstRun) {
      MTMgetFrames();
    }

    if(MTMTrack) { MTMTrackedItem = MTMTrackExpand(menu); }

	WMMenusDisplayed = 0;
    MTMLevel = 0;
    MTMDoc = parent.frames[WMMenuFrame].document
    MTMDoc.open("text/html", "replace");
    MTMOutputString = '<html><head>\n';
    MTMOutputString += '<link rel="stylesheet" type="text/css" href="' + wmBrowserObj.preHREF + MTMSSHREF + '">\n';
	MTMOutputString += '<script type="text/javascript" src="../templates/script/layers.js">\n<\/script>';
    MTMOutputString += '<\/head>\n<body ';
    if(MTMBackground != "") {
      MTMOutputString += 'background="' + wmBrowserObj.preHREF + WMMenuImageDirectory + MTMBackground + '" ';
    }
    MTMOutputString += 'bgcolor="' + MTMBGColor + '" text="' + MTMTextColor + '" link="' + MTMLinkColor + '" vlink="' + MTMLinkColor + '" alink="' + MTMLinkColor + '">\n';
    MTMOutputString += '\n<table border="0" cellpadding="0" cellspacing="0" width="' + MTMTableWidth + '">\n';
    MTMOutputString += '<tr valign="top"><td nowrap><a href="../admin.php?op=display_template_list" target="text"><img src="../html/menu-images/add_root_icon.gif" align="left" border="0" vspace="0" hspace="0" width="23" height="17">Neue Hauptseite</a></td></tr>\n';
	MTMOutputString += '<tr><td colspan="2"><img src="menu-images/menu_seperator.gif" align="left" border="0" vspace="0" hspace="0" width="200" height="2"></td></tr>';

    MTMDoc.writeln(MTMOutputString);

    MTMListItems(menu);

    MTMDoc.writeln('<\/table>\n');
    
    MTMDoc.writeln(WMMakeMenu());
   
    MTMDoc.writeln('<\/body>\n<\/html>');
    MTMDoc.close();

    if(wmBrowserObj.browserType == "NN5") {
      parent.frames[WMMenuFrame].scrollTo(0, 0);
    }

    if((MTMClickedItem || MTMTrackedItem) && wmBrowserObj.browserType != "NN3" && !MTMFirstRun) {
      MTMItemName = "sub" + (MTMClickedItem ? MTMClickedItem : MTMTrackedItem);
      if(document.layers && parent.frames[WMMenuFrame].scrollbars) {
        MTMyval = parent.frames[WMMenuFrame].document.anchors[MTMItemName].y;
        MTMWinSize = parent.frames[WMMenuFrame].innerHeight;
      } else if(wmBrowserObj.browserType != "O5") {
        if(wmBrowserObj.browserType == "NN5") {
          parent.frames[WMMenuFrame].document.all = parent.frames[WMMenuFrame].document.getElementsByTagName("*");
        }
        MTMyval = MTMGetYPos(parent.frames[WMMenuFrame].document.all[MTMItemName]);
        MTMWinSize = wmBrowserObj.browserType == "NN5" ? parent.frames[WMMenuFrame].innerHeight : parent.frames[WMMenuFrame].document.body.offsetHeight;
      }
      if(MTMyval > (MTMWinSize - 60)) {
        parent.frames[WMMenuFrame].scrollBy(0, parseInt(MTMyval - (MTMWinSize * 1/3)));
      }
    }

    MTMFirstRun = false;
    MTMClickedItem = false;
    MTMExpansion = false;
    MTMTrack = false;
  }
MTMUpdating = false;
}

function MTMListItems(menu) {
  var i;
  for (i = 0; i < menu.items.length; i++) {
    MTMIndices[MTMLevel] = i;
    WMMenusDisplayed++;
    MTMDisplayItem(menu.items[i],i);
    
    if(menu.items[i].submenu && menu.items[i].expanded) {
      MTMLevel++;
      MTMListItems(menu.items[i].submenu);
      MTMLevel--;
    } 
  }
}

function MTMDisplayItem(item,id) {
  var i, img;

  var MTMfrm = "parent.frames['code']";
  var MTMref = '.menu.items[' + MTMIndices[0] + ']';
   
  if(MTMLevel > 0) {
    for(i = 1; i <= MTMLevel; i++) {
      MTMref += ".submenu.items[" + MTMIndices[i] + "]";
    }
  }

  if(item.submenu) {
    var MTMClickCmd = "return " + MTMfrm + ".MTMSubAction(" + MTMfrm + MTMref + ");";
    var MTMouseOverCmd = "parent.status='" + (item.expanded ? "Collapse " : "Expand ") + (item.text.indexOf("'") != -1 ? MTMEscapeQuotes(item.text) : item.text) + "';return true;";
    var MTMouseOutCmd = "parent.status=parent.defaultStatus;return true;";
  }

  MTMOutputString = '<tr valign="top"><td nowrap>';
  if(MTMLevel > 0) {
    for (i = 0; i < MTMLevel; i++) {
      MTMOutputString += MTMakeImage("blank.gif");
    }
  }
  
	if(item.submenu) {
		if(item.url == "") {
			MTMOutputString += MTMakeLink(item, true, true, true, MTMClickCmd, MTMouseOverCmd, MTMouseOutCmd);
		} else {
			if(!item.expanded) {
				MTMOutputString += MTMakeLink(item, false, true, true, MTMClickCmd, MTMouseOverCmd, MTMouseOutCmd);
			} else {
				MTMOutputString += MTMakeLink(item, true, true, false, MTMClickCmd, MTMouseOverCmd, MTMouseOutCmd);
			}
		}

		if(item.expanded) {
			img = "menu_corner_minus.gif";
		} else {
			img = "menu_corner_plus.gif";
		}
		
		MTMOutputString += MTMakeImage(img);
  		MTMOutputString += "</a>";
	} else {
		img = "blank.gif";
		MTMOutputString += MTMakeImage(img);		
	}
  
    img = (item.icon != "") ? item.icon : MTMFetchIcon(item.url);

  MTMOutputString += WMMakePullDownLink(item,"parent.status='test';return true;");
  MTMOutputString += MTMakeImage(img);
  MTMOutputString += "</a>";
  
  MTMOutputString += MTMakeLink(item, false, true, true);
  

  if(item.submenu && item.url != "" && item.expanded) {
    MTMOutputString += '</a>' + MTMakeLink(item, false, false, true);
  }
  
  MTMOutputString += '&nbsp;' + item.text + '</a>' ;
  MTMDoc.writeln(MTMOutputString + '</td></tr><tr><td colspan="2"><img src="menu-images/menu_seperator.gif" align="left" border="0" vspace="0" hspace="0" width="200" height="2"></td></tr>');
}

function MTMEscapeQuotes(myString) {
  var newString = "";
  var cur_pos = myString.indexOf("'");
  var prev_pos = 0;
  while (cur_pos != -1) {
    if(cur_pos == 0) {
      newString += "\\";
    } else if(myString.charAt(cur_pos-1) != "\\") {
      newString += myString.substring(prev_pos, cur_pos) + "\\";
    } else if(myString.charAt(cur_pos-1) == "\\") {
      newString += myString.substring(prev_pos, cur_pos);
    }
    prev_pos = cur_pos++;
    cur_pos = myString.indexOf("'", cur_pos);
  }
  return(newString + myString.substring(prev_pos, myString.length));
}

function MTMTrackExpand(thisMenu) {
  var i, targetPath, targetLocation;
  var foundNumber = false;
  for(i = 0; i < thisMenu.items.length; i++) {
    if(thisMenu.items[i].url != "" && MTMTrackTarget(thisMenu.items[i].target)) {
      targetLocation = parent.frames[thisMenu.items[i].target].location;
      targetPath = targetLocation.pathname + targetLocation.search;
      if(wmBrowserObj.browserType == "IE4" && targetLocation.protocol == "file:") {
        var regExp = /\\/g;
        targetPath = targetPath.replace(regExp, "\/");
      }
      if(targetPath.lastIndexOf(thisMenu.items[i].url) != -1 && (targetPath.lastIndexOf(thisMenu.items[i].url) + thisMenu.items[i].url.length) == targetPath.length) {
        return(thisMenu.items[i].number);
      }
    }
    if(thisMenu.items[i].submenu) {
      foundNumber = MTMTrackExpand(thisMenu.items[i].submenu);
      if(foundNumber) {
        if(!thisMenu.items[i].expanded) {
          thisMenu.items[i].expanded = true;
          if(!MTMClickedItem) { MTMClickedItem = thisMenu.items[i].number; }
          MTMExpansion = true;
        }
        return(foundNumber);
      }
    }
  }
return(foundNumber);
}

function MTMCloseSubs(thisMenu) {
  var i, j;
  var foundMatch = false;
  for(i = 0; i < thisMenu.items.length; i++) {
    if(thisMenu.items[i].submenu && thisMenu.items[i].expanded) {
      if(thisMenu.items[i].number == MTMClickedItem) {
        foundMatch = true;
        for(j = 0; j < thisMenu.items[i].submenu.items.length; j++) {
          if(thisMenu.items[i].submenu.items[j].expanded) {
            thisMenu.items[i].submenu.items[j].expanded = false;
          }
        }
      } else {
        if(foundMatch) {
          thisMenu.items[i].expanded = false; 
        } else {
          foundMatch = MTMCloseSubs(thisMenu.items[i].submenu);
          if(!foundMatch) {
            thisMenu.items[i].expanded = false;
          }
        }
      }
    }
  }
return(foundMatch);
}

function MTMFetchIcon(testString) {
  var i;
  for(i = 0; i < MTMIconList.items.length; i++) {
    if((MTMIconList.items[i].type == 'any') && (testString.indexOf(MTMIconList.items[i].match) != -1)) {
      return(MTMIconList.items[i].file);
    } else if((MTMIconList.items[i].type == 'pre') && (testString.indexOf(MTMIconList.items[i].match) == 0)) {
      return(MTMIconList.items[i].file);
    } else if((MTMIconList.items[i].type == 'post') && (testString.indexOf(MTMIconList.items[i].match) != -1)) {
      if((testString.lastIndexOf(MTMIconList.items[i].match) + MTMIconList.items[i].match.length) == testString.length) {
        return(MTMIconList.items[i].file);
      }
    }
  }
return("norm_page.gif");
}

function MTMGetYPos(myObj) {
  return(myObj.offsetTop + ((myObj.offsetParent) ? MTMGetYPos(myObj.offsetParent) : 0));
}

function MTMCheckURL(myURL) {
  var tempString = "";
  if((myURL.indexOf("http://") == 0) || (myURL.indexOf("https://") == 0) || (myURL.indexOf("mailto:") == 0) || (myURL.indexOf("ftp://") == 0) || (myURL.indexOf("telnet:") == 0) || (myURL.indexOf("news:") == 0) || (myURL.indexOf("gopher:") == 0) || (myURL.indexOf("nntp:") == 0) || (myURL.indexOf("javascript:") == 0)) {
    tempString += myURL;
  } else {
    tempString += wmBrowserObj.preHREF + myURL;
  }
return(tempString);
}

function MTMakeLink(thisItem, voidURL, addName, addTitle, clickEvent, mouseOverEvent, mouseOutEvent) {
  var tempString = '<a href="' + (voidURL ? 'javascript:;' : MTMCheckURL(thisItem.url)) + '" ';
  if(addName) {
    tempString += 'name="sub' + thisItem.number + '" ';
  }
  if(clickEvent) {
    tempString += 'onclick="' + clickEvent + '" ';
  }
  if(mouseOverEvent && mouseOverEvent != "") {
    tempString += 'onmouseover="' + mouseOverEvent + '" ';
  }
  if(mouseOutEvent && mouseOutEvent != "") {
    tempString += 'onmouseout="' + mouseOutEvent + '" ';
  }
  if(thisItem.submenu && MTMClickedItem && thisItem.number == MTMClickedItem) {
    tempString += 'class="' + (thisItem.expanded ? "subexpanded" : "subclosed") + '" ';
  } else if(MTMTrackedItem && thisItem.number == MTMTrackedItem) {
    tempString += 'class="tracked"';
  }
  if(thisItem.target != "") {
    tempString += 'target="' + thisItem.target + '" ';
  }
  return(tempString + '>');
}


function WMMakePullDownLink(thisItem, clickEvent) {
  var tempString = '<a href="' + "javascript:wmShowPullDown("+ WMMenusDisplayed+ ","+ MTMLevel+","+thisItem.menuid+","+thisItem.pageid+","+thisItem.menuactive+","+thisItem.lowsub+");" + '" ';
  if(clickEvent) {
    tempString += 'mouseOver="' + clickEvent + '" ';
  }
  return(tempString + '>');
}

function WMMakeMenu() {
	var tempString = "";
	
	tempString += '<div id="pulldown" style="position:absolute; top:197; left:35; z-index: 10; visibility=hidden">'; 
	tempString += '<table border="0" width="120" cellspacing="0" cellpadding="0" bgcolor="#DADADA">';
	tempString += '<tr><td><img src="../html/menu-images/sub_l_corner.gif" width="5" height="2"></td>';
	tempString += '<td><img src="../html/menu-images/sub_top.gif" width="120" height="2"></td>';
	tempString += '<td><img src="../html/menu-images/sub_r_corner.gif" width="2" height="2"></td></tr>	';	
	tempString += '<tr><td><img src="../html/menu-images/sub_l_border.gif" width="5" height="16"></td>';
	tempString += '<td><a href="javascript:WMDoCommand(2);" target="content">Neue Seite...</a></td>';
	tempString += '<td><img src="../html/menu-images/sub_r_border.gif" width="2" height="16"></td></tr>';
	tempString += '<tr><td><img src="../html/menu-images/sub_l_border.gif" width="5" height="16"></td>';
	tempString += '<td><a href="javascript:WMDoCommand(4);" target="content">&Ouml;ffnen</a></td>';
	tempString += '<td><img src="../html/menu-images/sub_r_border.gif" width="2" height="16"></td></tr>	';
	tempString += '<tr><td><img src="../html/menu-images/sub_sl_border.gif" width="5" height="2"></td>';
	tempString += '<td><img src="../html/menu-images/sub_s_line.gif" width="120" height="2"></td>';
	tempString += '<td><img src="../html/menu-images/sub-sr_border.gif" width="2" height="2"></td></tr>';
	tempString += '<tr>';
	tempString += '<td><img src="../html/menu-images/sub_l_border.gif" width="5" height="16"></td>';
	tempString += '<td><a href="javascript:WMDoCommand(1);" target="content">Umbenennen</a></td>';
	tempString += '<td><img src="../html/menu-images/sub_r_border.gif" width="2" height="16"></td></td>';
	tempString += '</tr>	';
	tempString += '<tr>';
	tempString += '<td><img src="../html/menu-images/sub_l_border.gif" width="5" height="16"></td>';
	tempString += '<td><a href="javascript:WMDoCommand(5);" target="content">Sichtbar/Unsichtbar</a></td>';
	tempString += '<td><img src="../html/menu-images/sub_r_border.gif" width="2" height="16"></td></td>';
	tempString += '</tr>	';	
	tempString += '<tr>';
	tempString += '<td><img src="../html/menu-images/sub_l_border.gif" width="5" height="16"></td>';
	tempString += '<td><a href="javascript:WMDoCommand(6);" target="content">Sortieren</a></td>';
	tempString += '<td><img src="../html/menu-images/sub_r_border.gif" width="2" height="16"></td></td>';
	tempString += '</tr>	';	
	tempString += '<tr><td><img src="../html/menu-images/sub_sl_border.gif" width="5" height="2"></td>';
	tempString += '<td><img src="../html/menu-images/sub_s_line.gif" width="120" height="2"></td>';
	tempString += '<td><img src="../html/menu-images/sub-sr_border.gif" width="2" height="2"></td></tr>';
	tempString += '<tr><td><img src="../html/menu-images/sub_l_border.gif" width="5" height="16"></td>';
	tempString += '<td><a href="javascript:WMDoCommand(7);" target="content">Homepage</a></td>';
	tempString += '<td><img src="../html/menu-images/sub_r_border.gif" width="2" height="16"></td></td>';
	tempString += '</tr>	';
	tempString += '<tr><td><img src="../html/menu-images/sub_sl_border.gif" width="5" height="2"></td>';
	tempString += '<td><img src="../html/menu-images/sub_s_line.gif" width="120" height="2"></td>';
	tempString += '<td><img src="../html/menu-images/sub-sr_border.gif" width="2" height="2"></td></tr>';
	tempString += '<tr><td><img src="../html/menu-images/sub_l_border.gif" width="5" height="16"></td>';
	tempString += '<td><a href="javascript:WMDoCommand(3);" target="content">L&ouml;schen</a></td>';
	tempString += '<td><img src="../html/menu-images/sub_r_border.gif" width="2" height="16"></td></td>';
	tempString += '</tr>	';
	tempString += '<tr><td><img src="../html/menu-images/sub_bl_corner.gif" width="5" height="2"></td>';
	tempString += '<td><img src="../html/menu-images/sub_bottom.gif" width="120" height="2"></td>';
	tempString += '<td><img src="../html/menu-images/sub_br_corner.gif" width="2" height="2"></td></tr>	';		
	tempString += '</table>';
	tempString += '</div>';
	
	return(tempString);
}


function MTMakeImage(thisImage) {
  return('<img src="menu-images/'  + thisImage + '" align="left" border="0" vspace="0" hspace="0">');
}

function MTMakeBackImage(thisImage) {
  var tempString = 'transparent url("' + ((wmBrowserObj.preHREF == "") ? "" : wmBrowserObj.preHREF);
  tempString += WMMenuImageDirectory + thisImage + '")'
  return(tempString);
}

function MTMTrackTarget(thisTarget) {
  if(thisTarget.charAt(0) == "_") {
    return false;
  } else {
    for(i = 0; i < MTMFrameNames.length; i++) {
      if(thisTarget == MTMFrameNames[i]) {
        return true;
      }
    }
  }
  return false;
}


