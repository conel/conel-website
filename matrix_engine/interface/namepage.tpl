<!-- BEGIN header -->
<html>
<head>
	<title>Seiten Name</title>
	<link type="text/css" rel="stylesheet" href="interface/layout.css">
</head>
<body bgcolor="#FFFFFF" text="#000000" link="#000000" background="interface/images/pattern.gif">
	
<form action="{actionURL}" method="post">
<table border="0" width="300" cellspacing="0" cellpadding="0">
	<tr>
		<td align="left" valign="top" rowspan="2"><img src="interface/images/blank.gif" alt="" width="15" height="1" border="0"></td>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="5" height="1" border="0"></td>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="500" height="15" border="0"></td>
	</tr>	
	<tr>
		<td bgcolor="#FFFFFF" align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="5" height="1" border="0"></td>
		<td bgcolor="#FFFFFF">

<table border="0" width="300" cellspacing="0" cellpadding="0">
	<tr>
		<td align="left" valign="top" colspan="3"><img src="interface/images/blank.gif" alt="" width="1" height="15" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" class="headline" colspan="3"><b>{language_pagename}</b></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="3"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="3"><p>{language_pagenamedesc}</p>
</td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><img src="interface/images/dialog_seperator.gif" alt="" width="320" height="2" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>	
	<!-- END header -->
	<!-- BEGIN body -->	
	<tr>
		<td align="left" valign="top">{language_pagename}:</td>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="10" height="1" border="0"></td>
		<td align="left" valign="top"><input type="text" size="40" name="menu_text" value="{menu_text}"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><img src="interface/images/dialog_seperator.gif" alt="" width="320" height="2" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>
	<!-- END body -->
	<!-- BEGIN footer -->	
	<tr>
		<td align="left" valign="top" colspan="2"><img src="interface/images/blank.gif" alt="" width="80" height="1" border="0"></td>
		<td align="right" valign="top" rowspan="2">
		{hiddenfields}
		<input width="87" height="24" border=0 type=image name="submit" src="interface/{saveIMG}"></td>		
	</tr>				
</table>
</td></tr>
</table>
</form>


</body>
</html>
<!-- END footer -->
