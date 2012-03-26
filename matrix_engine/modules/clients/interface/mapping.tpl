<!-- BEGIN body -->
<html>
<head>
	<title>webmatrix</title>
	<link type="text/css" rel="stylesheet" href="interface/layout.css">
	<script src="../clients/interface/script/page_selector.js" type="text/javascript" language="javascript"></script>
	<script src="../clients/interface/script/container.js" type="text/javascript" language="javascript"></script>

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
		function wm_selectTab(language) {
			document.main.op.value = "editor";
			document.main.language.value = language;
			// now open the window, and then fresh the page
			document.main.submit();
		}		
		function change(mID, color) {
    			if (document.layers) {
        			window.document.layers['id'+mID+'b'].bgColor = color;

        		} else if (document.all) {
        			window.document.all['id'+mID+'b'].style.background = color;
        		}
		}

		function wm_selectLanguageTab(language) {
			document.main.op.value = "editor";
			document.main.language.value = language;
			// now open the window, and then fresh the page
			document.main.submit();
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
				if(confirm('{language_deletelementdesc}')) {
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
		
		function preview_version(mURL) {
			version = document.linkItemList.linkList_Selected.value;
			document.location.href = mURL+'&date='+version;
		}		
	
	</script>	
</head>
<body bgcolor="#FFFFFF" text="#000000" link="#000000" background="interface/images/pattern.gif">

<form action="{actionURL}" method="post" enctype="multipart/form-data" name="main">
<table border="0" width="300" cellspacing="0" cellpadding="0">
	<tr>
		<td align="left" valign="top" rowspan="2"><img src="../clients/interface/images/blank.gif" alt="" width="15" height="1" border="0"></td>
		<td align="left" valign="top"><img src="../clients/interface/images/blank.gif" alt="" width="5" height="1" border="0"></td>
		<td align="left" valign="top"><img src="../clients/interface/images/blank.gif" alt="" width="500" height="15" border="0"></td>
	</tr>	
	<tr>
		<td bgcolor="#FFFFFF" align="left" valign="top"><img src="../clients/interface/images/blank.gif" alt="" width="5" height="1" border="0"></td>
		<td bgcolor="#FFFFFF">

<table border="0" width="300" cellspacing="0" cellpadding="0" >
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../clients/interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" class="headline" colspan="3"><b>{language_inputhead}</b></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../clients/interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="3">{language_inputbody}</td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../clients/interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>				
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../clients/interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../clients/interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>		
</table>
<table border="0" width="366" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top" bgcolor="#EEEEEE"><img src="../clients/interface/images/blank.gif" width="167" height="1"></td>
		<td valign="top" colspan="3" bgcolor="#EEEEEE"><img src="../clients/interface/images/blank.gif" width="30" height="1"></td>
		<td valign="top" bgcolor="#EEEEEE"><img src="../clients/interface/images/blank.gif" width="167" height="1"></td>
		<td valign="top" bgcolor="#EEEEEE"><img src="../clients/interface/images/blank.gif" width="2" height="1"></td>
	</tr>
{CONTENT}													
</table>
<table border="0" width="300" cellspacing="0" cellpadding="0" >
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../clients/interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top"><img src="../clients/interface/images/blank.gif" alt="" width="87" height="1" border="0"></td>
		<td align="left" valign="top"><img src="../clients/interface/images/blank.gif" alt="" width="192" height="10" border="0"></td>
		<td align="left" valign="top"><img src="../clients/interface/images/blank.gif" alt="" width="87" height="1" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><input width="87" height="24" border=0 type=image name="cancel" src="../clients/interface/{cancelIMG}"></td>
		<td align="left" valign="top"><img src="../clients/interface/images/blank.gif" alt="" width="192" height="10" border="0"></td>
		<td align="left" valign="top"><input width="87" height="24" border=0 type=image name="submit" src="../clients/interface/{saveIMG}"></td>
	</tr>			
</table>

{hiddenfields}
<br><br><br>
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
<!-- END body -->

<!-- BEGIN assigned -->
	<tr>
		<td valign="top" colspan="3"><img src="../clients/interface/images/blank.gif" width="1" height="2"></td>
		<td valign="top" colspan="3" bgcolor="#EEEEEE"><img src="../clients/interface/images/blank.gif" width="1" height="4"></td>
	</tr>					
	<tr>
		<td valign="top"><b>{SOURCE}</b></td>
		<td valign="middle" width="2"><img src="../clients/interface/images/blank.gif" width="2" height="1"></td>
		<td valign="middle" width="2"><img src="../clients/interface/images/import_arrow.gif" width="26" height="24"></td>
		<td valign="middle" width="2" bgcolor="#EEEEEE"><img src="../clients/interface/images/blank.gif" width="2" height="1"></td>
		<td valign="top" bgcolor="#EEEEEE"><b>{TARGET_NAME}</b><br><i>{TARGET_IDENTIFIER}</i></td>				
		<td valign="middle" width="2" bgcolor="#EEEEEE"><img src="../clients/interface/images/blank.gif" width="2" height="1"></td>
	</tr>
	<tr>
		<td valign="top" colspan="3"><img src="../clients/interface/images/blank.gif" width="1" height="2"></td>
		<td valign="top" colspan="3" bgcolor="#EEEEEE"><img src="../clients/interface/images/blank.gif" width="1" height="2"></td>
	</tr>			
	<tr><td valign="top" colspan="6" bgcolor="#DDDDDD"><img src="../clients/interface/images/blank.gif" width="1" height="1"></td></tr>															
														
<!-- END assigned -->	


<!-- BEGIN notarget -->
	<tr><td valign="top" colspan="6"><img src="../clients/interface/images/blank.gif" width="1" height="4"></td></tr>					
	<tr>
		<td valign="top" colspan="3"><img src="../clients/interface/images/blank.gif" width="1" height="2"></td>
		<td valign="top" colspan="3"><img src="../clients/interface/images/blank.gif" width="1" height="2"></td>
	</tr>					
	<tr>
		<td valign="top"><b>{SOURCE}</b></td>
		<td valign="middle" width="2"><img src="../clients/interface/images/blank.gif" width="2" height="1"></td>
		<td valign="middle" width="2"><img src="../clients/interface/images/import_cross.gif" width="26" height="24"></td>
		<td valign="middle" width="2"><img src="../clients/interface/images/blank.gif" width="2" height="1"></td>
		<td valign="top">&nbsp;</td>				
		<td valign="middle" width="2"><img src="../clients/interface/images/blank.gif" width="2" height="1"></td>
	</tr>
	<tr>
		<td valign="top" colspan="3"><img src="../clients/interface/images/blank.gif" width="1" height="2"></td>
		<td valign="top" colspan="3"><img src="../clients/interface/images/blank.gif" width="1" height="2"></td>
	</tr>			
	<tr><td valign="top" colspan="6" bgcolor="#DDDDDD"><img src="../clients/interface/images/blank.gif" width="1" height="1"></td></tr>															
<!-- END notarget -->	


<!-- BEGIN nosource -->
	<tr>
		<td valign="top" colspan="3"><img src="../clients/interface/images/blank.gif" width="1" height="2"></td>
		<td valign="top" colspan="3" bgcolor="#EEEEEE"><img src="../clients/interface/images/blank.gif" width="1" height="4"></td>
	</tr>					
	<tr>
		<td valign="top">&nbsp;</td>
		<td valign="middle" width="2"><img src="../clients/interface/images/blank.gif" width="2" height="1"></td>
		<td valign="middle" width="2"><img src="../clients/interface/images/import_cross.gif" width="26" height="24"></td>
		<td valign="middle" width="2" bgcolor="#EEEEEE"><img src="../clients/interface/images/blank.gif" width="2" height="1"></td>
		<td valign="top" bgcolor="#EEEEEE"><b>{TARGET_NAME}</b><br><i>{TARGET_IDENTIFIER}</i></td>				
		<td valign="middle" width="2" bgcolor="#EEEEEE"><img src="../clients/interface/images/blank.gif" width="2" height="1"></td>
	</tr>
	<tr>
		<td valign="top" colspan="3"><img src="../clients/interface/images/blank.gif" width="1" height="2"></td>
		<td valign="top" colspan="3" bgcolor="#EEEEEE"><img src="../clients/interface/images/blank.gif" width="1" height="2"></td>
	</tr>			
	<tr><td valign="top" colspan="6" bgcolor="#DDDDDD"><img src="../clients/interface/images/blank.gif" width="1" height="1"></td></tr>
<!-- END nosource -->	