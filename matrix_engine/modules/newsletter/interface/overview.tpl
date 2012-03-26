<!-- BEGIN body -->
<html>
<head>
	<title>Please choose a layout</title>
	<link type="text/css" rel="stylesheet" href="../../interface/layout.css">
</head>
<body bgcolor="#FFFFFF" text="#000000" link="#000000" background="../../interface/images/pattern.gif" onload="parent.frames['code'].location.href = '{menuURL}';">


<table border="0" width="300" cellspacing="0" cellpadding="0">
	<tr>
		<td align="left" valign="top" rowspan="2"><img src="interface/images/blank.gif" alt="" width="15" height="1" border="0"></td>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="5" height="1" border="0"></td>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="600" height="15" border="0"></td>
	</tr>	
	<tr>
		<td bgcolor="#FFFFFF" align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="5" height="1" border="0"></td>
		<td bgcolor="#FFFFFF">		
		
		<table border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td align="left" valign="top" colspan="4"><img src="interface/images/blank.gif" alt="" width="1" height="15" border="0"></td>
			</tr>
			<tr>
				<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
				<td align="left" valign="top" class="headline_newsletter" colspan="3"><b>Campaign:</b> {TITLE}</td>
			</tr>		
			<tr>
				<td align="left" valign="top" colspan="4"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
			</tr>							
		</table>		

		<table border="0" width="300" cellspacing="0" cellpadding="0" >
			<tr>
				<td align="left" valign="top" rowspan="10"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
				<td align="left" valign="top" colspan="3"><img src="../../interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
			</tr>
			<tr>
				<td align="left" valign="top" colspan="2">
					<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						{TABS}
					</tr>
					</table>
				</td>
				<td align="right" valign="top" colspan="2">&nbsp;</td>
			</tr>	
			<tr>
				<td align="left" valign="top" colspan="3"><img src="../../interface/images/blank.gif" alt="" width="1" height="1" border="0"></td>
			</tr>						
			<tr>
				<td align="left" valign="top" colspan="3"><img src="../../interface/images/seperator.gif" alt="" width="466" height="2" border="0"></td>
			</tr>
			<tr>
				<td align="left" valign="top" colspan="3"><img src="../../interface/images/blank.gif" alt="" width="1" height="4" border="0"></td>
			</tr>			
		</table>

		<form method="post" action="{actionURL}" name="subject">
		<table border="0" width="300" cellspacing="0" cellpadding="0">		
			<tr>
				<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
				<td align="left" valign="top" colspan="4">
					<!-- BEGIN text -->
					<table border="0" width="400" cellspacing="0" cellpadding="0" >
						<tr><td align="left" valign="top" class="subheadline">Subject</td></tr>	
						<tr><td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="2" border="0"></td></tr>	
						<tr><td align="left" valign="top"><p><input type="text" name="email" size="50" value="{SUBJECT}"></p></td></tr>
						<tr><td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="1" border="0"></td></tr>	
						<tr><td align="left" valign="top"><p>Please specify the subject for this newsletter</p></td></tr>	
						<tr><td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="1" border="0"></td></tr>
					</table>
					<!-- END text -->							
				</td>
			</tr>
			<tr>
				<td align="left" valign="top" colspan="4"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
			</tr>
			<tr>
				<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
				<td align="left" valign="top" colspan="3"><img src="interface/images/seperator.gif" alt="" width="466" height="2" border="0"></td>
			</tr>
			<tr>
				<td align="left" valign="top" colspan="4"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
			</tr>		
			<tr>
				<td align="left" valign="top" colspan="3"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
				<td align="right" valign="top" rowspan="2"><input name="op" type="hidden" value="storesubject">
				<a href="#" onClick="document.subject.submit();"><img height="24" border=0 SRC="interface/lang/{LANG}_button_save.gif"></a></td>		
			</tr>				
		</table>
		</form>
		<form method="post" action="{actionURL}" name="reply">
		<table border="0" width="300" cellspacing="0" cellpadding="0">		
			<tr>
				<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
				<td align="left" valign="top" colspan="4">
					<!-- BEGIN text -->
					<table border="0" width="400" cellspacing="0" cellpadding="0" >
						<tr><td align="left" valign="top" class="subheadline">reply-to adddress</td></tr>	
						<tr><td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="2" border="0"></td></tr>	
						<tr><td align="left" valign="top"><p><input type="text" name="reply" size="50" value="{REPLYTO}"></p></td></tr>
						<tr><td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="1" border="0"></td></tr>	
						<tr><td align="left" valign="top"><p> </p></td></tr>	
						<tr><td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="1" border="0"></td></tr>
					</table>
					<!-- END text -->							
				</td>
			</tr>
			<tr>
				<td align="left" valign="top" colspan="4"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
			</tr>
			<tr>
				<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
				<td align="left" valign="top" colspan="3"><img src="interface/images/seperator.gif" alt="" width="466" height="2" border="0"></td>
			</tr>
			<tr>
				<td align="left" valign="top" colspan="4"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
			</tr>		
			<tr>
				<td align="left" valign="top" colspan="3"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
				<td align="right" valign="top" rowspan="2"><input name="op" type="hidden" value="storereply">
				<a href="#" onClick="document.reply.submit();"><img height="24" border=0 SRC="interface/lang/{LANG}_button_save.gif"></a></td>		
			</tr>				
		</table>
		</form>
		<form action="admin.php?Session=b8d6cc4fcc83904b2fddae1ea029d6af" method="post">
		
		{page}
		{recipients}
		</form>
		

		
		<form method="post" action="{actionURL}" name="test">
		<table border="0" width="300" cellspacing="0" cellpadding="0">		
			<tr>
				<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
				<td align="left" valign="top" colspan="4">
					<!-- BEGIN text -->
					<table border="0" width="400" cellspacing="0" cellpadding="0" >
						<tr><td align="left" valign="top" class="subheadline">{LANG_MODULE_Newsletter_TestMail}</td></tr>	
						<tr><td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="2" border="0"></td></tr>	
						<tr><td align="left" valign="top"><p><input type="text" name="email" size="50" value=""></p></td></tr>
						<tr><td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="1" border="0"></td></tr>	
						<tr><td align="left" valign="top"><p>{LANG_MODULE_Newsletter_TestMailDesc}</p></td></tr>	
						<tr><td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="1" border="0"></td></tr>
					</table>
					<!-- END text -->							
				</td>
			</tr>
			<tr>
				<td align="left" valign="top" colspan="4"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
			</tr>
			<tr>
				<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
				<td align="left" valign="top" colspan="3"><img src="interface/images/seperator.gif" alt="" width="466" height="2" border="0"></td>
			</tr>
			<tr>
				<td align="left" valign="top" colspan="4"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
			</tr>		
			<tr>
				<td align="left" valign="top" colspan="3"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
				<td align="right" valign="top" rowspan="2">{hiddenfields_test}
				<a href="#" onClick="document.test.submit();"><img height="24" border=0 SRC="interface/lang/{LANG}_button_testmail.gif"></a></td>		
			</tr>				
		</table>			

		<table border="0" width="300" cellspacing="0" cellpadding="0">		
			<tr>
				<td align="left" valign="top" colspan="4"><img src="interface/images/blank.gif" alt="" width="1" height="15" border="0"></td>
			</tr>
			<tr>
				<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
				<td align="left" valign="top" colspan="3" class="subheadline">{newsletterStatusHead}</td>
			</tr>	
			<tr>
				<td align="left" valign="top" colspan="4"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
			</tr>	
			<tr>
				<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
				<td align="left" valign="top" colspan="4">{newsletterStatus}</td>
			</tr>
			<tr>
				<td align="left" valign="top" colspan="4"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
			</tr>			
			<tr>
				<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
				<td align="left" valign="top" colspan="3"><img src="interface/images/seperator.gif" alt="" width="466" height="2" border="0"></td>
			</tr>
			<tr>
				<td align="left" valign="top" colspan="4"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
			</tr>		
			<tr>
				<td align="left" valign="top" colspan="3"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
				<td align="right" valign="top" rowspan="1">
				<a href="{sendURL}"><img height="24" border=0 SRC="interface/lang/{LANG}_button_send.gif"></a></td>		
			</tr>				
			<tr>
				<td align="left" valign="top" colspan="4"><img src="interface/images/blank.gif" alt="" width="1" height="15" border="0"></td>
			</tr>
		</table>		
									
</td></tr>
</table>
</form>

</body>
</html>
<!-- END body -->