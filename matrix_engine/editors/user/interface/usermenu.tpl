<!-- BEGIN head -->
<html>
<head>
<link rel="stylesheet" type="text/css" href="interface/layout.css">
<script src="interface/script/layers.js" type="text/javascript" language="javascript"></script>

<script type="text/javascript" language="javascript">
function WMDoCommand(action) {
	if (action == 1) {
		parent.parent.frames['text'].location.href = "{targetURL}&op=edit_group&group_id="+currentPageId;
	} else if (action == 2) {
		parent.parent.frames['text'].location.href = "{targetURL}&op=delete_group&group_id="+currentPageId;
	} else 	if (action == 3) {
		parent.parent.frames['text'].location.href = "{targetURL}&op=edit_user&user_id="+currentPageId;
	} else if (action == 4) {
		parent.parent.frames['text'].location.href = "{targetURL}&op=delete_user&user_id="+currentPageId;
	} else if (action == 0) {
		parent.parent.frames['text'].location.href = "{targetURL}&op=new_user&group_id="+currentPageId;
	} 
}
</script>
</head>
<body bgcolor="#EDEDED" text="#000000" link="#000000" alink="#000000" vlink="#000000">
<table cellspacing="0" cellpadding="0" border="0">
<tr valign="top">
	<td>
	{TOPNAV}
	</td>
</tr>
</table>
<table cellspacing="0" cellpadding="0" border="0" bgcolor="#EDEDED">
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
	</tr>
<tr><td bgcolor="#EDEDED" valign="top" colspan="10" background="interface/images/menu/menu_seperator.gif"><img src="interface/images/menu/menu_seperator.gif" width="10" height="2"></td></tr>	
<!-- END head -->
<!-- BEGIN spacer -->
<td bgcolor="#EDEDED" align="left" width="12"><img src="interface/images/menu/blank.gif"></td>
<!-- END spacer -->
<!-- BEGIN expand -->
<td bgcolor="#EDEDED" align="left" width="12"><a href="{menuURL}"><img src="{EXPAND}" border="0" width="20" height="17"></a></td>
<!-- END expand -->
<!-- BEGIN element -->
	<td bgcolor="#EDEDED" align="left" width="12"><a href="javascript:wmShowPullDown(0,{id},0,{id},{groupid},1);" mouseOver="parent.status='test';return true;" ><img src="interface/images/menu/norm_group.gif" width="20" height="17" border="0"></a></td>
	<td bgcolor="#EDEDED" align="left" colspan="8" nowrap><a href="{url}" target="{target}" class="{active}">{text}</a></td></tr>
	<tr><td bgcolor="#EDEDED" valign="top" colspan="10" background="interface/images/menu/menu_seperator.gif"><img src="interface/images/menu/menu_seperator.gif" width="10" height="2"></td></tr>
<!-- END element -->
<!-- BEGIN lastelement -->
	<td bgcolor="#EDEDED" align="left" width="12"><a href="javascript:wmShowPullDown(1,{id},1,{id},{memberid},1);" mouseOver="parent.status='test';return true;" ><img src="interface/images/menu/norm_user.gif" width="20" height="17" border="0"></a></td>
	<td bgcolor="#EDEDED" align="left" colspan="7" nowrap><a href="#" class="{active}">{text}</a></td></tr>
	<tr><td bgcolor="#EDEDED" valign="top" colspan="10" background="interface/images/menu/menu_seperator.gif"><img src="interface/images/menu/menu_seperator.gif" width="10" height="2"></td></tr>
<!-- END lastelement -->

<!-- BEGIN foot -->
</table>

{menu}
</body>
</html>
<!-- END foot -->

