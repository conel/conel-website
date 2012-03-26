<!-- BEGIN linklistadd -->
<table width="200" border="0" cellspacing="0" cellpadding="0">	
	<tr>
		<td align="left" valign="top" colspan="6"><img src="interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
	<tr>
		<td valign="middle"<b>{element_tag}</b></td>
		<td valign="top" colspan="3"><img src="interface/images/blank.gif" width="22" height="18"></td>
		<td valign="top" width="22"><a href="javascript:linklist_addLink('{addlinkItemURL}','extra_alias');"><img src="interface/images/icon_add.gif" width="26" height="24" border="0"></a></td>
		<td valign="top" width="22"><a href="javascript:linklist_deleteLink('{deletelinkItemURL}')"><img src="interface/images/icon_trash.gif" width="26" height="24" border="0"></a></td>
	</tr>	
	<tr>
		<td valign="top"><img src="interface/images/blank.gif" width="208" height="1"></td>
		<td valign="top" colspan="5"><img src="interface/images/blank.gif" width="88" height="1"></td>
	</tr>	
<!-- END linklistadd -->
<!-- BEGIN linklistedit -->
<table width="200" border="0" cellspacing="0" cellpadding="0">	
	<tr>
		<td align="left" valign="top" colspan="6"><img src="interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
	<tr>
		<td valign="middle"><b>{element_tag}</b></td>
		<td valign="top" colspan="3"><img src="interface/images/blank.gif" width="22" height="18"></td>
		<td valign="top" width="22"><a href="javascript:linklist_editLink('{addlinkItemURL}','extra_alias')"><img src="interface/images/icon_edit.gif" width="26" height="24" border="0"></a></td>
		<td valign="top" width="22"><a href="javascript:linklist_deleteLink('{deletelinkItemURL}')"><img src="interface/images/icon_trash.gif" width="26" height="24" border="0"></a></td>
	</tr>	
	<tr>
		<td valign="top"><img src="interface/images/blank.gif" width="208" height="1"></td>
		<td valign="top" colspan="5"><img src="interface/images/blank.gif" width="88" height="1"></td>
	</tr>	
<!-- END linklistedit -->
<!-- BEGIN linklist_row -->														
	<tr bgcolor="#BBBBBB">
		<td valign="middle" width="200" colspan="6" id="id{linkID}b"><a href="javascript:linklist_selectItem('{linkID}');">{decription}</a></td>
	</tr>
	<tr><td valign="top" colspan="6"><img src="interface/images/white.gif" width="301" height="1"></td></tr>					
<!-- END linklist_row -->
<!-- BEGIN linklist_foot -->
	<tr>
		<td align="left" valign="top" colspan="6"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="6"><p><i>{element_desc}</i></p></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="6"><img src="interface/images/blank.gif" alt="" width="1" height="15" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="6"><img src="interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td>
	</tr>												
	</table>				
<!-- END linklist_foot -->
