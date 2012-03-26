<!-- BEGIN head -->
<html>
<head>
<link rel="stylesheet" type="text/css" href="interface/layout.css">
<script src="../../interface/script/layers.js" type="text/javascript" language="javascript"></script>

<script type="text/javascript" language="javascript">
function WMDoCommand(action) {
	if (action == 1) {
		parent.frames['text'].location.href = "{targetURL}&op=edit_template&template_id="+currentPageId;
	} else if (action == 2) {
		parent.frames['text'].location.href = "{targetURL}&op=delete_template&template_id="+currentPageId;
	}  else if (action == 3) {
		parent.frames['text'].location.href = "{targetURL}&op=newGroup";
	}	
}
</script>
</head>
<body bgcolor="#DFE1E5" text="#000000" link="#000000" alink="#000000" vlink="#000000">
<table cellspacing="0" cellpadding="0" border="0">
<tr valign="top">
	<td>
		<img src="interface/{newpageIMG}" width="269" height="22" border="0" usemap="#topnav">
		<MAP NAME="topnav">
		<AREA SHAPE="rect" ALT="" COORDS="19,0,156,22" HREF="{targetURL}&op=edit_template" target="text">
		<AREA SHAPE="rect" ALT="" COORDS="0,0,17,22" HREF="javascript:wmShowPullDown('_top',1,-1,0,1,0,0);">
		</MAP>
	</td>
</tr>
</table>
<table cellspacing="0" cellpadding="0" border="0" bgcolor="#DFE1E5">
	<tr>
		<td align="left"><img src="../../interface/images/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="../../interface/images/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="../../interface/images/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="../../interface/images/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="../../interface/images/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="../../interface/images/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="../../interface/images/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="../../interface/images/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="../../interface/images/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="../../interface/images/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="../../interface/images/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="../../interface/images/blank.gif" width="12" height="1"></td>
	</tr>
<tr><td valign="top" colspan="12" background="../../interface/images/blank.gif"><img src="../../interface/images/blank.gif" width="10" height="2"></td></tr>	
<!-- END head -->
<!-- BEGIN expand -->
	<tr>
	<td bgcolor="#DFE1E5" align="left" width="12"><a href="{GROUP_URL}"><img src="{img}" border="0" width="20" height="17"></a></td>
	<td bgcolor="#DFE1E5" align="left" width="12"><a href="javascript:wmShowPullDown(0,{id},{level},{menuid},{pageid},1);" mouseOver="parent.status='test';return true;" ><img src="../../interface/images/menu/norm_folder.gif" width="20" height="17" border="0"></a></td>
	<td bgcolor="#DFE1E5" align="left" colspan="6" nowrap><a href="{url}" target="{target}" class="{active}">{text}</a></td></tr>
	<tr><td bgcolor="#DFE1E5" valign="top" colspan="16" background="../../interface/images/menu/menu_seperator.gif"><img src="../../interface/images/menu/menu_seperator.gif" width="10" height="2"></td></tr>
<!-- END expand -->
<!-- BEGIN element -->
	<tr>
	<td bgcolor="#DFE1E5" align="left" width="12"><img src="../../interface/images/blank.gif"></td>
	<td bgcolor="#DFE1E5" align="left" width="12"><img src="../../interface/images/blank.gif"></td>
	<td bgcolor="#DFE1E5" align="left" width="12"><a href="javascript:wmShowPullDown(0,{id},{level},{menuid},{pageid},1);" mouseOver="parent.status='test';return true;" ><img src="{itemimg}" width="20" height="17" border="0"></a></td>
	<td bgcolor="#DFE1E5" align="left" colspan="6" nowrap><a href="{url}" target="{target}" class="{active}">{text}</a></td></tr>
	<tr><td bgcolor="#DFE1E5" valign="top" colspan="16" background="../../interface/images/menu/menu_seperator.gif"><img src="../../interface/images/menu/menu_seperator.gif" width="10" height="2"></td></tr>
<!-- END element -->
<!-- BEGIN lastelement -->
	<tr>
	<td bgcolor="#DFE1E5" align="left" width="12"><img src="../../interface/images/blank.gif"></td>
	<td bgcolor="#DFE1E5" align="left" width="12"><img src="../../interface/images/blank.gif"></td>
	<td bgcolor="#DFE1E5" align="left" width="12"><a href="javascript:wmShowPullDown(0,{id},{level},{menuid},{pageid},1);" mouseOver="parent.status='test';return true;" ><img src="{itemimg}" width="20" height="17" border="0"></a></td>
	<td bgcolor="#DFE1E5" align="left" colspan="6" nowrap><a href="{url}" target="{target}" class="{active}">{text}</a></td></tr>
	<tr><td bgcolor="#DFE1E5" valign="top" colspan="16" background="../../interface/images/menu/menu_seperator.gif"><img src="../../interface/images/menu/menu_seperator.gif" width="10" height="2"></td></tr>
<!-- END lastelement -->

<!-- BEGIN foot -->
</table>

{menu}
</body>
</html>
<!-- END foot -->

