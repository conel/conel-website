<!-- BEGIN image -->
	<script type="text/javascript" language="Javascript">
	<!--
	function image_delete(mURL) {	
		wm_openWindow(mURL,'deleteLink','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=460,height=340');			
	}
	 -->
	</script>	
<table border="0" width="200" cellspacing="0" cellpadding="0" >
	<tr><td align="left" valign="top" colspan="2"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td></tr>
	<tr>
		<td align="left" valign="middle"><b>{element_tag}</b></td>
		<td valign="top" width="22"><a href="javascript:linklist_editLink('{addlinkItemURL}','linklist')"><img src="interface/images/{action_image}.gif" width="26" height="24" border="0"></a></td>
		<td valign="top" width="22"><a href="javascript:image_delete('{deletelinkItemURL}')"><img src="interface/images/icon_trash.gif" width="26" height="24" border="0"></a></td>	
	</tr>		
	<tr>
		<td valign="top"><img src="interface/images/blank.gif" width="274" height="1"></td>
		<td valign="top"><img src="interface/images/blank.gif" width="22" height="1"></td>
		<td valign="top"><img src="interface/images/blank.gif" width="22" height="1"></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="3">{file}</td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="3"><img src="interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	{ALT}	
	<tr>
		<td align="left" valign="top" colspan="3"><p><i>{element_desc}</i></p></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="3"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><img src="interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td>
	</tr>
</table>
<!-- END image -->

<!-- BEGIN altimage -->	
	<tr><td align="left" valign="top"><p><input type="text" name="{element_name}_alt" size="50" value="{alt_value}" id="{element_name}_alt"></p></td></tr>
	<tr><td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="1" border="0"></td></tr>	
<!-- END altimage -->