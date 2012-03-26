<!-- BEGIN head -->
<html>
<head>
	<title>webmatrix</title>
	<link type="text/css" rel="stylesheet" href="interface/layout.css">
	<script src="../../interface/script/page_selector.js" type="text/javascript" language="javascript"></script>

	<script type="text/javascript" language="Javascript">
		function selectItem(mID) {
			// first we check the field for a certain id number
			targetfield = document.itemList.tester.value;
			
			// if there was an item select we have to deselect it
			if(targetfield > 0) {
				change(targetfield,'#BBBBBB');
			}
			
			// now select the new item
			change(mID,'#999999');
			
			// and finally we will set the itemID field to the current ITEM
			document.itemList.tester.value=mID;
		}
		
		function change(mID, color) {
    			if (document.layers) {
        			window.document.layers['id'+mID+'b'].bgColor = color;

        		} else if (document.all) {
        			window.document.all['id'+mID+'b'].style.background = color;
        		}
		}

		function wm_addElement(mURL) {
			document.main.op.value = "store_page";
			// now open the window, and then fresh the page
			wm_openWindow(mURL,'addlink','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=280,height=320');
			document.main.submit();
		}
			
		function wm_executeCommand(mURL) {
			// first we get the id to be supplied
			currentID = document.itemList.tester.value;
			
			// then we add this id to the URL
			mURL = mURL + '&cnt_id='+currentID;
			
			// and finally we call the actual routine to open up the window
			wm_openWindow(mURL,'add'+currentID,'toolbar=no,location=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=280,height=320');
		}
		
		function wm_deleteItem(mURL) {
			// first we get the id
			currentID = document.itemList.tester.value;
			mURL = mURL + '&container_id='+currentID;
			
			if(currentID > 0) {
				// the wen ask the user if he really wnats to delete this item
				if(confirm('{language_deleteelementdesc}')) {
					document.itemList.tester.value = -1;
					// this means we can savely execute the command
					document.location.href = mURL;
				} else {
					// we will do nothing
				}
			}
		}
		
	function linklist_deleteLink(mURL) {	
		
		currentItem = document.linkItemList.linkList_Selected.value;
		mURL = mURL + "&item_id="+currentItem;
			
		if(currentItem > 0) {
			// the wen ask the user if he really wnats to delete this item
			wm_openWindow(mURL,'deleteLink','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=460,height=340');			
		}
	}

		
		
		function checkLength(field, countfield, maxlimit) {
			if (field.value.length > maxlimit) // if too long...trim it!
				field.value = field.value.substring(0, maxlimit);
			else
				countfield.value = maxlimit - field.value.length;
			}

		function wm_openWindow(theURL,winName,features) {
  			window.open(theURL,winName,features);
		}
	</script>	
</head>
<body bgcolor="#FFFFFF" text="#000000" link="#000000" background="{site}interface/images/pattern.gif">

<form action="{actionURL}" method="post" enctype="multipart/form-data" name="main">
<table border="0" width="300" cellspacing="0" cellpadding="0">
	<tr>
		<td align="left" valign="top" rowspan="2"><img src="interface/images/blank.gif" alt="" width="15" height="1" border="0"></td>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="5" height="1" border="0"></td>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="500" height="15" border="0"></td>
	</tr>	
	<tr>
		<td bgcolor="#FFFFFF" align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="5" height="1" border="0"></td>
		<td bgcolor="#FFFFFF">

<!-- END head -->
<!-- BEGIN intro -->
<table border="0" width="300" cellspacing="0" cellpadding="0" >
	<tr>
		<td align="left" valign="top" colspan="3"><img src="interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" class="headline" colspan="3"><b>{language_inputhead}</b></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="3"><img src="interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="3">{language_inputbody}</td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><img src="interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><img src="interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td>
	</tr>
</table>
<!-- END intro -->


<!-- BEGIN foot -->
{hiddenfields}
<br><input width="87" height="24" border=0 type=image name="submit" SRC="interface/{saveIMG}"><br><br>
</td></tr>
</table>

</form>
<form method="get" name="itemList" action="">
	<input type="hidden" name="tester">
</form>	
<form method="get" name="linkItemList" action="">
	<input type="hidden" name="linkList_Selected">
</form>	
<form method="get" name="container" action="">
	<input type="hidden" name="container_Selected">
</form>	
</body>
</html>
<!-- END foot -->


<!-- BEGIN text -->
<table border="0" width="300" cellspacing="0" cellpadding="0" >
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="8" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top"><b>{element_tag}</b></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p><input type="text" name="{element_name}" size="50" value="{value}"></p></td>
	</tr>
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="1" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p><i>{element_desc}</i></p></td>
	</tr>	
	<tr><td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="1" border="0"></td></tr>
	<tr><td align="left" valign="top"><img src="interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td></tr>
</table>
<!-- END text -->



<!-- BEGIN linklist -->
<table width="200" border="0" cellspacing="0" cellpadding="0">	
	<tr>
		<td align="left" valign="top" colspan="6"><img src="interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
	<tr>
		<td valign="middle"><b>{element_tag}</b></td>
		<td valign="top" width="22"><img src="interface/images/blank.gif" width="22" height="18"></td>
		<td valign="top" width="22"><a href="javascript:linklist_addLink('{addlinkItemURL}','linklist');"><img src="../../interface/images/icon_add.gif" width="26" height="24" border="0"></a></td>
		<td valign="top" width="22"><a href="javascript:linklist_editLink('{addlinkItemURL}','linklist')"><img src="../../interface/images/icon_edit.gif" width="26" height="24" border="0"></a></td>
		<td valign="top" width="22"><a href="javascript:linklist_deleteLink('{deletelinkItemURL}')"><img src="../../interface/images/icon_trash.gif" width="26" height="24" border="0"></a></td>
	</tr>	
	<tr>
		<td valign="top"><img src="interface/images/blank.gif" width="208" height="1"></td>
		<td valign="top" colspan="5"><img src="interface/images/blank.gif" width="88" height="1"></td>
	</tr>	
<!-- END linklist -->
<!-- BEGIN linklist_row -->														
	<tr bgcolor="#BBBBBB">
		<td valign="middle" width="200" colspan="6" id="id{linkID}b"><a href="javascript:linklist_selectItem('{linkID}');">{decription}</a></td>
	</tr>
	<tr><td valign="top" colspan="6"><img src="interface/images/white.gif" width="301" height="1"></td></tr>					
<!-- END linklist_row -->
<!-- BEGIN linklist_foot -->
	<tr>
		<td align="left" valign="top" colspan="6"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="6"><p><i>{element_desc}</i></p></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="6"><img src="interface/images/blank.gif" alt="" width="1" height="15" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="6"><img src="interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td>
	</tr>												
	</table>				
<!-- END linklist_foot -->
