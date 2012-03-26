<!-- BEGIN head -->
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../interface/layout.css">
	<script type="text/javascript" language="Javascript">
		function selectItem(mID,move,order) {
			// we need to set the target
			document.items.linkID.value = mID;
			document.items.move.value = move;
			document.items.order.value = order;
			
			// then we should submit the form
			document.items.submit();
		}
		
		function prepareClose(closeOP) {
			// we need to set the target
			document.items.op.value = 'close';
			
			// then we should submit the form
			document.items.submit();
		}		
	</script>
</head>
<body bgcolor="#FFFFFF" text="#000000" link="#000000" background="../../interface/images/pattern.gif">
<form action="{actionURL}" method="post" enctype="multipart/form-data" name="items">
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
<!-- END head -->

<!-- BEGIN linklist -->
<table width="200" border="0" cellspacing="0" cellpadding="0">	
	<tr>
		<td align="left" valign="top" colspan="6"><img src="../../interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
<!-- END linklist -->
<!-- BEGIN linklist_row -->														
	<tr bgcolor="#BBBBBB">
		<td valign="middle" width="360" colspan="4">{decription}</td>
		<td valign="middle" align="center"><a href="javascript:selectItem('{linkID}',1,'{order}');"><img src="../../interface/images/linklist_move_up.gif" width="12" height="7" border="0"></a></td>
		<td valign="middle" align="center"><a href="javascript:selectItem('{linkID}',0,'{order}');"><img src="../../interface/images/linklist_move_down.gif" width="12" height="7" border="0"></a></td>
	</tr>
	<tr><td valign="top" colspan="6"><img src="../../interface/images/white.gif" width="301" height="1"></td></tr>					
<!-- END linklist_row -->
<!-- BEGIN foot -->
</table>
<input type="hidden" name="linkID" value="0">
<input type="hidden" name="move" value="0">
<input type="hidden" name="order" value="0">

<input type="hidden" name="Session" value="{Session}">
<input type="hidden" name="language" value="{language}">
<input type="hidden" name="page_id" value="{page_id}">
<input type="hidden" name="op" value="{op}">
<input type="hidden" name="linklistID" value="{linklistID}">
<input type="hidden" name="identifier" value="{identifier}">
<input type="hidden" name="subcmd" value="{subcmd}">
<input type="hidden" name="low_sub" value="{low_sub}">
<input type="hidden" name="selected_page" value="0">
<br><a href="javascript:prepareClose('{closeOP}');"><img src="{saveIMG}" width="87" height="24" border="0"></a><br><br>
</td></tr>
</table>
</form>
</body>
</html>
<!-- END foot -->
