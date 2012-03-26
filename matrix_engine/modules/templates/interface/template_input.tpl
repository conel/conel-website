<!-- BEGIN head -->
<html>
<head>
	<title>webmatrix</title>
	<link type="text/css" rel="stylesheet" href="interface/layout.css">	
</head>
<body bgcolor="#FFFFFF" text="#000000" link="#000000" background="../../interface/images/pattern.gif">

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
		<td align="left" valign="top" class="headline" colspan="3"><b>{language_templatehead}</b></td></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="3"><img src="interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="3"><p>{language_templatebody}</p></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><img src="interface/images/blank.gif" alt="" width="1" height="4" border="0"></td>
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
		<td align="left" valign="top"><b>{language_templateinputname}</b></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p><input type="text" name="template_title" size="50" value="{template_title}"></p></td>
	</tr>
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p><i>{language_templateinputnamedesc}</i></p></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="4" border="0"></td>
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
		<td align="left" valign="top"><b>{language_templatedesc}</b></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p><textarea name="template_description" cols="40" rows="5" wrap="virtual">{template_description}</textarea></p></td>
	</tr>
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p><i>{language_templatedescdesc}</i></p></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="4" border="0"></td>
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
		<td align="left" valign="top"><b>{language_templatefilename}</b></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p><input type="text" name="template_basename" size="50" value="{template_basename}"></p></td>
	</tr>
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p><i>{language_templatefilenamedesc}</i></p></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="4" border="0"></td>
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
		<td align="left" valign="top"><b>{language_templatelevels}</b></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p>{levelsform}</p></td>
	</tr>
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p><i>{language_templatelevelsdesc}</i></p></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="4" border="0"></td>
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
		<td align="left" valign="top"><b>{language_templateparent}</b></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top">
			<table border="0" cellspacing="0" cellpadding="0"><tr>
				<td width="10"><input type="checkbox" name="hidden" value="1" {hidden_status}></td>
				<td><p>not selectable</p></td>
				</tr>
				<tr>
				<td colspan="2"><i> </i></td>
				</tr>				
			</table>
		</td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="8" border="0"></td>
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
		<td align="left" valign="top"><b>{language_templateparent}</b></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p><input type="text" name="template_parent" size="50" value="{template_parent}"></p></td>
	</tr>
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p><i>{language_templateparentdesc}</i></p></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="4" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top"><img src="interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td>
	</tr>
</table>
<table border="0" width="300" cellspacing="0" cellpadding="0" >
	<tr>
		<td align="left" valign="top" colspan="2"><img src="interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="middle" colspan="2"><b>{language_templatethumb}</b></td>
	</tr>		
	<tr>
		<td align="left" valign="top" colspan="2"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="2">{file}</td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="2"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>			
	<tr>
		<td align="left" valign="top" colspan="2"><p><input type="file" name="template_icon" maxlength="60" size="40" value="{template_icon}"></p></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="2"><img src="interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="2"><p><i>{language_templatethumbdesc}</i></p></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="2"><img src="interface/images/blank.gif" alt="" width="1" height="4" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="2"><img src="interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td>
	</tr>
</table>
<!-- END body -->

<!-- BEGIN foot -->
<input type="hidden" name="Session" value="{Session}">
<input type="hidden" name="template_id" value="{template_id}">
<input type="hidden" name="op" value="{op}">
<input type="hidden" name="subcmd" value="">
<input type="hidden" name="low_sub" value="{low_sub}">
<input type="hidden" name="page_id" value="{page_id}">

<br><input width="77" height="24" border=0 type=image name="submit" SRC="interface/{saveIMG}"><br><br>
</td></tr>
</table>

</form>
<form method="get" name="itemList" action="">
	<input type="hidden" name="tester">
</form>	

</body>
</html>
<!-- END foot -->
