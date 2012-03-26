<!-- BEGIN head -->
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../interface/layout.css">
<script src="interface/script/page_selector.js" type="text/javascript" language="javascript"></script>
</head>
<body bgcolor="#FFFFFF" text="#000000" link="#000000" background="../../interface/images/pattern.gif">
<form action="{actionURL}" method="post" enctype="multipart/form-data" name="main">
<table border="0" width="300" cellspacing="0" cellpadding="0">
	<tr>
		<td align="left" valign="top" rowspan="2"><img src="../../interface/images/blank.gif" alt="" width="15" height="1" border="0"></td>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="5" height="1" border="0"></td>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="400" height="15" border="0"></td>
	</tr>	
	<tr>
		<td bgcolor="#FFFFFF" align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="5" height="1" border="0"></td>
		<td bgcolor="#FFFFFF">
			<table border="0" width="300" cellspacing="0" cellpadding="0" >
				<tr>
					<td align="left" valign="top" colspan="3"><img src="../../interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
				</tr>
				<tr>
					<td align="left" valign="top" class="headline" colspan="3"><b>{Title}</b></td>
				</tr>	
				<tr>
					<td align="left" valign="top" colspan="3"><img src="../../interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
				</tr>	
				<tr>
					<td align="left" valign="top" colspan="3">{Desc}</td>
				</tr>
				<tr>
					<td align="left" valign="top" colspan="3"><img src="../../interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
				</tr>
				<tr>
					<td align="left" valign="top" colspan="3"><img src="../../interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td>
				</tr>
				<tr>
					<td align="left" valign="top" colspan="3"><img src="../../interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
				</tr>				
			</table>
<table cellspacing="0" cellpadding="0" border="0" width="209">
	<tr>
		<td align="left"><img src="../../interface/images/menu/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="../../interface/images/menu/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="../../interface/images/menu/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="../../interface/images/menu/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="../../interface/images/menu/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="../../interface/images/menu/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="../../interface/images/menu/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="../../interface/images/menu/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="../../interface/images/menu/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="../../interface/images/menu/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="../../interface/images/menu/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="../../interface/images/menu/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="../../interface/images/menu/blank.gif" width="210" height="1"></td>
	</tr>
<!-- END head -->
<!-- BEGIN spacer -->
<td valign="top" align="left" width="12"><img src="../../interface/images/menu/blank.gif"></td>
<!-- END spacer -->
<!-- BEGIN expand -->
<td valign="top" align="left" width="12"><a href="{params}"><img src="{img}" border="0" width="20" height="17"></a></td>
<!-- END expand -->
<!-- BEGIN element -->
	<td valign="top" align="left" width="12"><a href="javascript:submitSelectedPage('{pageid}')"><img src="{itemimg}" width="20" height="17" border="0"></a></td>
	<td valign="top" align="left" colspan="{span}" nowrap><a href="javascript:submitSelectedPage('{pageid}')" target="{target}" class="{active}">{text}</a></td></tr>
	<tr><td valign="top" colspan="13" background="../../interface/images/menu/menu_seperator.gif"><img src="../../interface/images/menu/menu_seperator.gif" width="10" height="2"></td></tr>
<!-- END element -->
<!-- BEGIN lastelement -->
	<td valign="top" align="left" width="12"><a href="javascript:submitSelectedPage('{pageid}')"><img src="{itemimg}" width="20" height="17" border="0"></a></td>
	<td valign="top" align="left" colspan="{span}" nowrap><a href="javascript:submitSelectedPage('{pageid}')" target="{target}" class="{active}">{text}</a></td></tr>
	<tr><td valign="top" colspan="13" background="../../interface/images/menu/menu_seperator.gif"><img src="../../interface/images/menu/menu_seperator.gif" width="10" height="2"></td></tr>
<!-- END lastelement -->

<!-- BEGIN foot -->
</table>
<input type="hidden" name="Session" value="{Session}">
<input type="hidden" name="page_id" value="{page_id}">
<input type="hidden" name="op" value="{op}">
<input type="hidden" name="identifier" value="{identifier}">
<input type="hidden" name="item_id" value="{item_id}">
<input type="hidden" name="selected_page" value="0">
</form>
</body>
</html>
<!-- END foot -->

