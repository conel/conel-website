<!-- BEGIN head -->
<html>
<head>
<link rel="stylesheet" type="text/css" href="interface/layout.css">
<script src="interface/script/jquery.js" type="text/javascript" language="javascript"></script>
<script src="interface/script/layers.js" type="text/javascript" language="javascript"></script>

<script type="text/javascript" language="javascript">
function WMDoCommand(action) {
	if (action == 1) {
		parent.frames['text'].location.href = "{adminURL}&op=edit_menu_item&menu_id="+currentMenuId;
	} else if (action == 2) {
		parent.frames['text'].location.href = "{adminURL}&op=display_template_list&parent="+(currentMenuId);
	} else if (action == 3) {
		parent.frames['text'].location.href = "{adminURL}&op=menu_prompt_delete&menu_id="+currentMenuId+"&page_id="+currentPageId;
	} else if (action == 4) {
		parent.frames['text'].location.href = "{adminURL}&op=preview&page_id="+currentPageId+"&cache=0";
	} else if (action == 5) {
		parent.frames['text'].location.href = "{adminURL}&op=menu_active&menu_id="+currentMenuId;
	} else if (action == 6) {
		parent.frames['text'].location.href = "{adminURL}&op=order_menu&menu_id="+currentMenuId;
	} else if (action == 7) {
		parent.frames['text'].location.href = "{adminURL}&op=make_homepage&menu_id="+currentMenuId;
	} else if (action == 8) {
		parent.frames['text'].location.href = "{adminURL}&op=editpage&mode=edit&page_id="+currentPageId+"&type="+currentType;
	} else if (action == 9) {
		parent.frames['text'].location.href = "{adminURL}&op=change_state&menu_id="+currentMenuId;
	} else if (action == 14) {
		parent.frames['text'].location.href = "{adminURL}&op=showTemplate&page_id="+currentPageId;
	} else if (action == 15) {
		parent.frames['text'].location.href = "{adminURL}&op=move&page_id="+currentPageId;
	} else if (action == 21) {
		parent.frames['text'].location.href = "{adminURL}&op=create_folder&page_id="+currentPageId+"&parent="+(currentMenuId);
	} else if (action == 22) {
		parent.frames['text'].location.href = "{adminURL}&op=copyPage&page_id="+currentPageId;
	} else if (action == 23) {
		parent.frames['text'].location.href = "{adminURL}&op=copyStructure&page_id="+currentPageId;
	} else if (action == 24) {
		parent.frames['text'].location.href = "{adminURL}&op=editTimer&page_id="+currentPageId;
	} else if (action == 25) {
		parent.frames['text'].location.href = "{adminURL}&op=redirect_page&page_id="+currentPageId;
	}			
}

function WMDoSpecialCommand(action,parameter) {
	if (action == 40) {
		parent.frames['text'].location.href = "{adminURL}&op=preview&page_id="+currentPageId+"&cache=0&language="+parameter;
	}
}
</script>
</head>
<body bgcolor="#DFE1E5" text="#000000" link="#000000" alink="#000000" vlink="#000000">
<table cellspacing="0" cellpadding="0" border="0">
<tr valign="top">
	<td>
		{TOPNAV}
		<MAP NAME="topnav">
		<AREA SHAPE="rect" ALT="" COORDS="19,0,156,22" HREF="{adminURL}&op=display_template_list" target="text">
		<AREA SHAPE="rect" ALT="" COORDS="0,0,17,22" HREF="javascript:wmShowPullDown('_top',1,-1,0,1,0,0);">
		</MAP>
	</td>
</tr>
</table>
<table cellspacing="0" cellpadding="0" border="0" width="209">
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
		<td align="left"><img src="interface/images/menu/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="interface/images/menu/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="interface/images/menu/blank.gif" width="12" height="1"></td>
		<td align="left"><img src="interface/images/menu/blank.gif" width="12" height="1"></td>		
	</tr>
<tr><td valign="top" colspan="16" background="interface/images/menu/blank.gif"><img src="interface/images/menu/blank.gif" width="10" height="2"></td></tr>	
<!-- END head -->
<!-- BEGIN spacer -->
<td valign="top" align="left" width="12"><img src="interface/images/menu/blank.gif"></td>
<!-- END spacer -->
<!-- BEGIN expand -->
<td valign="top" align="left" width="12"><a href="{params}"><img src="{img}" border="0" width="20" height="17"></a></td>
<!-- END expand -->
<!-- BEGIN element -->
	<td valign="top" align="left" width="12"><a href="javascript:wmShowPullDown(0,{id},{level},{menuid},{pageid},'{type}',{language});" mouseOver="parent.status='test';return true;" ><img src="{itemimg}" width="20" height="17" border="0"></a></td>
	<td valign="top" align="left" colspan="{span}" nowrap><a href="{url}" target="{target}" class="{active}">{text}</a></td></tr>
	<tr><td valign="top" colspan="16" background="interface/images/menu/menu_seperator.gif"><img src="interface/images/menu/menu_seperator.gif" width="10" height="2"></td></tr>
<!-- END element -->
<!-- BEGIN lastelement -->
	<td valign="top" align="left" width="12"><a href="javascript:wmShowPullDown(1,{id},{level},{menuid},{pageid},'{type}',{language});" mouseOver="parent.status='test';return true;" ><img src="{itemimg}" width="20" height="17" border="0"></a></td>
	<td valign="top" align="left" colspan="{span}" nowrap><a href="{url}" target="{target}" class="{active}">{text}</a></td></tr>
	<tr><td valign="top" colspan="16" background="interface/images/menu/menu_seperator.gif"><img src="interface/images/menu/menu_seperator.gif" width="10" height="2"></td></tr>
<!-- END lastelement -->
<!-- BEGIN ghostelement -->
	<td valign="top" align="left" width="12"><img src="{itemimg}" width="20" height="17" border="0"></td>
	<td valign="top" align="left" colspan="{span}" nowrap><a href="{url}" target="{target}" class="{active}">{text}</a></td></tr>
	<tr><td valign="top" colspan="16" background="interface/images/menu/menu_seperator.gif"><img src="interface/images/menu/menu_seperator.gif" width="10" height="2"></td></tr>
<!-- END ghostelement -->
<!-- BEGIN foot -->
</table>

{menu}
</body>
</html>
<!-- END foot -->

