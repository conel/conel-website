<!-- BEGIN head -->
<html>
<head>
<link rel="stylesheet" type="text/css" href="interface/layout.css">
<script src="interface/script/layers.js" type="text/javascript" language="javascript"></script>

<script type="text/javascript" language="javascript">
function WMDoCommand(action) {
	if (action == 1) {
		parent.frames['text'].location.href = "{targetURL}&op=edit&id="+currentPageId;
	} else if (action == 2) {
		parent.frames['text'].location.href = "{targetURL}&op=delete&id="+currentPageId;
	}  else if (action == 3) {
		parent.frames['text'].location.href = "{targetURL}&op=rename&id="+currentPageId;
	} 	
}
</script>
</head>
<body bgcolor="#DFE1E5" text="#000000" link="#000000" alink="#000000" vlink="#000000">
<!-- END head -->
<!-- BEGIN section -->
<table cellspacing="0" cellpadding="0" border="0">
<tr valign="top">
	<td><a href="{targetURL}&op=create" target="text">
		<img src="interface/{newpageIMG}" width="269" height="22" border="0"></a>
	</td>
</tr>
<tr>
	<td colspan="2"><img src="interface/images/menu/blank.gif" width="1" height="2"></td>
</tr>
</table>
<table cellspacing="0" cellpadding="0" border="0" bgcolor="#DFE1E5">
	<tr>
		<td align="left"><img src="interface/images/menu/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="interface/images/menu/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="interface/images/menu/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="interface/images/menu/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="interface/images/menu/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="interface/images/menu/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="interface/images/menu/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="interface/images/menu/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="interface/images/menu/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="interface/images/menu/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="interface/images/menu/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="interface/images/menu/blank.gif" width="12" height="1"></td>
	</tr>
<tr><td bgcolor="#DFE1E5" valign="top" colspan="6"><img src="interface/images/menu/blank.gif" width="10" height="2"></td></tr>	
<!-- END section -->
<!-- BEGIN spacer -->
<td bgcolor="#DFE1E5" align="left" width="12"><img src="interface/images/menu/blank.gif"></td>
<!-- END spacer -->
<!-- BEGIN expand -->
<td bgcolor="#DFE1E5" align="left" width="12"><a href="matrix_menu.php{params}"><img src="{img}" border="0" width="20" height="17"></a></td>
<!-- END expand -->
<!-- BEGIN element -->
	<td bgcolor="#DFE1E5" align="left" width="12"><img src="interface/images/menu/blank.gif"></td>
	<td bgcolor="#DFE1E5" align="left" width="12"><a href="javascript:wmShowPullDown(0,{id},{level},{menuid},{pageid},1);" mouseOver="parent.status='test';return true;" ><img src="{itemimg}" width="20" height="17" border="0"></a></td>
	<td bgcolor="#DFE1E5" align="left" colspan="{span}" nowrap><a href="{url}" target="{target}" class="{active}">{text}</a></td></tr>
	<tr><td bgcolor="#DFE1E5" valign="top" colspan="6" background="interface/images/menu/menu_seperator.gif"><img src="interface/images/menu/menu_seperator.gif" width="10" height="2"></td></tr>
<!-- END element -->
<!-- BEGIN lastelement -->
	<td bgcolor="#DFE1E5" align="left" width="12"><a href="javascript:wmShowPullDown(0,{id},{level},{menuid},{pageid},1);" mouseOver="parent.status='test';return true;" ><img src="{itemimg}" width="20" height="17" border="0"></a></td>
	<td bgcolor="#DFE1E5" align="left" colspan="{span}" nowrap><a href="{url}" target="{target}" class="{active}">{text}</a></td></tr>
	<tr><td bgcolor="#DFE1E5" valign="top" colspan="6" background="interface/images/menu/menu_seperator.gif"><img src="interface/images/menu/menu_seperator.gif" width="10" height="2"></td></tr>
<!-- END lastelement -->

<!-- BEGIN sectionfoot -->
<td bgcolor="#DFE1E5" align="left" width="12"><img src="interface/images/menu/blank.gif" width="1" height="20"></td>

</table>

<!-- END sectionfoot -->
<!-- BEGIN foot -->
{menu}
</body>
</html>
<!-- END foot -->

