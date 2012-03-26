<!-- BEGIN header -->
<html>
<head>
	<title>{language_selectlayouttitle}</title>
	<link type="text/css" rel="stylesheet" href="../../../../interface/layout.css">
	<script language="JavaScript">
		function closeWindow(){
			// first we need to set the var to reload
			opener.document.main.cmd.value = "save";
			opener.document.main.submit();
			top.window.close(); 
		}
	</script>
	<script type="text/javascript" language="Javascript">
	
		function wm_deleteClient() {
			document.main.op.value = 'delete';
			// now open the window, and then fresh the page
			document.main.submit();
		}
	</script>		
</head>
<body bgcolor="#FFFFFF" text="#000000" link="#000000" background="../../../../interface/images/pattern.gif">	
<table border="0" width="300" cellspacing="0" cellpadding="0">
	<tr>
		<td align="left" valign="top" rowspan="2"><img src="../../../../interface/images/blank.gif" alt="" width="15" height="1" border="0"></td>
		<td align="left" valign="top"><img src="../../../../interface/images/blank.gif" alt="" width="5" height="1" border="0"></td>
		<td align="left" valign="top"><img src="../../../../interface/images/blank.gif" alt="" width="500" height="15" border="0"></td>
	</tr>	
	<tr>
		<td bgcolor="#FFFFFF" align="left" valign="top"><img src="../../../../interface/images/blank.gif" alt="" width="5" height="1" border="0"></td>
		<td bgcolor="#FFFFFF">
				
		
			<table border="0" width="380" cellspacing="0" cellpadding="0">
				<tr><td align="left" valign="top" colspan="4"><img src="../../../../interface/images/blank.gif" alt="" width="1" height="15" border="0"></td></tr>
				<tr>
					<td align="left" valign="top"><img src="../../../../interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
					<td align="left" valign="top" class="headline" colspan="3"><b>{language_header}</b></td>
				</tr>	
				<tr><td align="left" valign="top" colspan="4"><img src="../../../../interface/images/blank.gif" alt="" width="1" height="5" border="0"></td></tr>	
				<tr>
					<td align="left" valign="top"><img src="../../../../interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
					<td align="left" valign="top" colspan="4">{language_description}</td>
				</tr>
				<tr><td align="left" valign="top" colspan="4"><img src="../../../../interface/images/blank.gif" alt="" width="1" height="5" border="0"></td></tr>
				<tr>
					<td align="left" valign="top"><img src="../../../../interface/images/blank.gif" alt="" width="1" height="1" border="0"></td>
					<td align="left" valign="top" colspan="3"><img src="../../../../interface/images/seperator.gif" alt="" width="340" height="2" border="0"></td>
				</tr>
				<tr><td align="left" valign="top" colspan="4"><img src="../../../../interface/images/blank.gif" alt="" width="1" height="5" border="0"></td></tr>	
			</table>
			<form action="{actionURL}" method="post" method="post" enctype="multipart/form-data">
				
			<table border="0" width="380" cellspacing="0" cellpadding="0" >
				<tr>
					<td align="left" valign="top" rowspan="10"><img src="../../../../interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
					<td align="left" valign="top" colspan="2"><img src="../../../../interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
				</tr>
		
				<tr>
					<td valign="top"><img src="../../../../interface/images/blank.gif" width="274" height="1"></td>
					<td valign="top"><img src="../../../../interface/images/blank.gif" width="22" height="1"></td>
				</tr>	
				<tr>
					<td align="left" valign="top" colspan="2">{file}</td>
				</tr>	
				<tr>
					<td align="left" valign="top" colspan="2"><img src="../../../../interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
				</tr>	
				<tr>
					<td align="left" valign="top" colspan="2" width="274"><p><input type="file" name="image" value="{filename}"></p></td>
				</tr>	
				<tr>
					<td align="left" valign="top" colspan="2"><img src="../../../../interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
				</tr>	
				<tr>
					<td align="left" valign="top" colspan="2"><textarea type="text" name="caption" id="caption" rows="3" cols="50">{caption}</textarea></td>
				</tr>				
			</table>			
			
			<table border="0" width="300" cellspacing="0" cellpadding="0">
				<!-- END header -->
				<!-- BEGIN body -->	
				<tr>
					<td align="left" valign="top"><img src="../../../../interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
					<td align="left" valign="top"><img src="../../../../interface/images/blank.gif" alt="" width="87" height="1" border="0"></td>
					<td align="left" valign="top"><img src="../../../../interface/images/blank.gif" alt="" width="100" height="1" border="0"></td>
					<td align="left" valign="top"><img src="../../../../interface/images/blank.gif" alt="" width="84" height="1" border="0"></td>
				</tr>
				<tr>
					<td align="left" valign="top" colspan="4"><img src="../../../../interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
				</tr>
				<tr>
					<td align="left" valign="top"><img src="../../../../interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
					<td align="left" valign="top" colspan="3"><img src="../../../../interface/images/seperator.gif" alt="" width="340" height="2" border="0"></td>
				</tr>
				<tr>
					<td align="left" valign="top" colspan="4"><img src="../../../../interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
				</tr>
				<!-- END body -->
				<!-- BEGIN footer -->	
				<tr>
					<td align="left" valign="top"><img src="../../../../interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
					<td align="left" valign="top"><a href="#" onClick="closeWindow();"><img src="../../../../interface/{backIMG}" border="0"></a></td>
					<td align="left" valign="top"><img src="../../../../interface/images/blank.gif" alt="" width="1" height="1" border="0"></td>
					<td align="right" valign="top">{hiddenfields}
					<input border=0 type=image name="submit" SRC="../../../../interface/{saveIMG}"></td>		
				</tr>
				<tr>
					<td align="left" valign="top" colspan="4"><img src="../../../../interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
				</tr>					
			</table>
</td></tr>
</table>
</form>

</body>
</html>
<!-- END footer -->
