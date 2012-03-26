<!-- BEGIN head -->
<html>
<head>
	<title>webmatrix</title>
	<link type="text/css" rel="stylesheet" href="interface/layout.css">
	<script type="text/javascript" language="Javascript">
		function selectItem(mID) {
			// first we check the field for a certain id number
			targetfield = document.itemList.tester.value;
			
			// if there was an item select we have to deselect it
			if(targetfield > 0) {
				change(targetfield,'#f1f1eb');
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
				if(confirm('Wollen Sie dieses Element wirklich l&ouml;schen')) {
					document.itemList.tester.value = -1;
					// this means we can savely execute the command
					document.location.href = mURL;
				} else {
					// we will do nothing
				}
			}
		}
		
		
		function wm_openWindow(theURL,winName,features) {
  			window.open(theURL,winName,features);
		}
	</script>	
</head>
<body bgcolor="#FFFFFF" text="#000000" link="#000000" background="{site}interface/images/pattern.gif">

<form action="{targetURL}" method="post" enctype="multipart/form-data" name="main">
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
		<td align="left" valign="top" class="headline" colspan="3">{language_userhead}</td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="3"><img src="interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="3">{language_userbody}</td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><img src="interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><img src="interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td>
	</tr>
</table>
<!-- END intro -->
<!-- BEGIN body -->
<table border="0" width="300" cellspacing="0" cellpadding="0" >
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top">{language_userinputname}</td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p><input type="text" name="user_name" size="50" value="{user_name}"></p></td>
	</tr>
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top">{language_userinputnamedesc}</td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="15" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top"><img src="interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td>
	</tr>
</table>
<table border="0" width="300" cellspacing="0" cellpadding="0" >
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="178" height="10" border="0"></td>
		<td align="left" valign="top" rowspan="4"><img src="interface/images/blank.gif" alt="" width="10" height="1" border="0"></td>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="178" height="1" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top">{language_userinputlastname}</td>
		<td align="left" valign="top">{language_userinputfirstname}</td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="1" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p><input type="text" name="user_lastname" size="22" value="{user_lastname}"></p></td>
		<td align="left" valign="top"><p><input type="text" name="user_firstname" size="23" value="{user_firstname}"></p></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><img src="interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="3">{language_userinputrealnamedesc}</td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="3"><img src="interface/images/blank.gif" alt="" width="1" height="15" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><img src="interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td>
	</tr>
</table>
<table border="0" width="300" cellspacing="0" cellpadding="0" >
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top">{language_userinputmail}</td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p><input type="text" name="user_email" size="50" value="{user_email}"></p></td>
	</tr>
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top">{language_userinputmaildesc}</td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="15" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top"><img src="interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td>
	</tr>
</table>
<table border="0" width="300" cellspacing="0" cellpadding="0" >
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top"><b>{languag_password}</b></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p><input type="password" name="user_passwd" size="50" value=""></p></td>
	</tr>
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p><i>{language_userinputpasswortdesc}</i></p></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="15" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top"><img src="interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td>
	</tr>
</table>
<!-- END body -->

<!-- BEGIN foot -->
<input type="hidden" name="Session" value="{Session}">
<input type="hidden" name="user_id" value="{user_id}">
<input type="hidden" name="group_id" value="{group_id}">
<input type="hidden" name="op" value="{op}">
<input type="hidden" name="subcmd" value="">
<input type="hidden" name="low_sub" value="{low_sub}">
<input type="hidden" name="page_id" value="{page_id}">

<br><input width="87" height="24" border=0 type=image name="submit" SRC="interface/{SAVEIMG}"><br><br>
</td></tr>
</table>

</form>
<form method="get" name="itemList" action="">
	<input type="hidden" name="tester">
</form>	

</body>
</html>
<!-- END foot -->
