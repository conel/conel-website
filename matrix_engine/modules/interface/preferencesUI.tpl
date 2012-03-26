<!-- BEGIN head -->
<html>
<head>
	<title>webmatrix</title>
	<link type="text/css" rel="stylesheet" href="../../interface/layout.css">
	{HEADER}
	<style>
	.tab {
	font-family: Trebuchet,Trebuchet MS,Verdana,Arial,Geneva,Helvetica,sans-serif;
	font-size: 12px;
	color: black;
	font-weight: bold;
	text-decoration: none;
	}
</style>
	
	<script src="../../interface/script/page_selector.js" type="text/javascript" language="javascript"></script>
</head>
<body bgcolor="#FFFFFF" text="#000000" link="#000000" background="../../interface/images/pattern.gif">

<form action="{actionURL}" method="post" enctype="multipart/form-data" name="main">
<table border="0" width="300" cellspacing="0" cellpadding="0">
	<tr>
		<td align="left" valign="top" rowspan="3"><img src="../../interface/images/blank.gif" alt="" width="80" height="1" border="0"></td>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="5" height="1" border="0"></td>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="500" height="15" border="0"></td>
	</tr>
	<tr>
		<td align="right" valign="top" colspan="2">{tabs}</td>
	</tr>		
	<tr>
		<td bgcolor="#FFFFFF" align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="5" height="1" border="0"></td>
		<td bgcolor="#FFFFFF">

<!-- END head -->
<!-- BEGIN intro -->
<table border="0" width="300" cellspacing="0" cellpadding="0" >
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../../interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" class="headline" colspan="3"><b>{language_inputhead}</b></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../../interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="3">{language_inputbody}</td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../../interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../../interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td>
	</tr>
</table>
<!-- END intro -->
<!-- BEGIN triggerfunction -->
<table border="0" width="300" cellspacing="0" cellpadding="0" >
	<tr>
		<td align="left" valign="top" colspan="6"><img src="../../interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top" nowrap colspan="5"><b>{element_tag}</b></td>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="80" height="1" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="6"><img src="../../interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="5"><p><i class="ghost">{element_desc}</i></p></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="6"><img src="../../interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>	
	<tr>
		<td align="right" valign="top" colspan="5"><a href="{triggerURL}"><img src="{triggerIMG}" alt="" border="0"></a></td>
	</tr>		
	<tr>
		<td align="left" valign="top" colspan="6"><img src="../../interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="6"><img src="../../interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td>
	</tr>
</table>
<!-- END triggerfunction -->
<!-- BEGIN radio -->
<table border="0" width="300" cellspacing="0" cellpadding="0" >
	<tr>
		<td align="left" valign="top" colspan="6"><img src="../../interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top" nowrap><b>{element_tag}</b></td>
		<td align="left" valign="middle"><p><input type="radio" name="{element_name}" value="{value1}" {status1}></p></td>
		<td align="left" valign="middle"><p>{valueLabel1}</p></td>
		<td align="left" valign="middle"><p><input type="radio" name="{element_name}" value="{value2}" {status2}></p></td>
		<td align="left" valign="middle"><p>{valueLabel2}</p></td>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="120" height="1" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="6"><img src="../../interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="6"><p><i class="ghost">{element_desc}</i></p></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="6"><img src="../../interface/images/blank.gif" alt="" width="1" height="15" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="6"><img src="../../interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td>
	</tr>
</table>
<!-- END radio -->
<!-- BEGIN date -->
<table border="0" width="200" cellspacing="0" cellpadding="0" >
	<tr>
		<td align="left" valign="top" colspan="6"><img src="../../interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="6" nowrap><b>{element_tag}</b></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="6"><img src="../../interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p class="ghost">{DAY}</p></td>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
		<td align="left" valign="top"><p class="ghost">{MONTH}</p></td>		
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
		<td align="left" valign="top"><p class="ghost">{YEAR}</p></td>		
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p><input type="text" name="{element_name}.day" size="5" value="{day}"></p></td>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="5" height="5" border="0"></td>
		<td align="left" valign="top"><p><input type="text" name="{element_name}.month" size="5" value="{month}"></p></td>		
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="5" height="5" border="0"></td>
		<td align="left" valign="top"><p><input type="text" name="{element_name}.year" size="5" value="{year}"></p></td>		
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="140" height="5" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="6"><img src="../../interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="6"><p><i class="ghost">{element_desc}</i></p></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="6"><img src="../../interface/images/blank.gif" alt="" width="1" height="25" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="6"></td>
	</tr>
</table>
<img src="../../interface/images/seperator.gif" alt="" width="366" height="2" border="0">
<!-- END date -->
<!-- BEGIN text -->
<table border="0" width="300" cellspacing="0" cellpadding="0" >
	<tr>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top"><b>{element_tag}</b></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p><input type="text" name="{element_name}" size="50" value="{value}"></p></td>
	</tr>
	<tr>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p><i>{element_desc}</i></p></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="1" height="15" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top"><img src="../../interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td>
	</tr>
</table>
<!-- END text -->
<!-- BEGIN copytextcounter -->
<table border="0" width="300" cellspacing="0" cellpadding="0" >
	<tr>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top"><b>{element_tag}</b></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top"><p><input readonly type="text" name="{element_name}counter" size="3" maxlength="3" value="{maxchar}"></p></td>
	</tr>		
	<tr>
		<td align="left" valign="top"><p><textarea name="{element_name}" wrap="virtual" cols="{columns}" rows="{lines}" onKeyDown="checkLength(this.form.{element_name},this.form.{element_name}counter,{maxchar});" onKeyUp="checkLength(this.form.{element_name},this.form.{element_name}counter,{maxchar});">{value}</textarea></p></td>
	</tr>
	<tr>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p><i>{element_desc}</i></p></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="1" height="15" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top"><img src="../../interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td>
	</tr>
</table>
<!-- END copytextcounter -->
<!-- BEGIN copytext -->
<table border="0" width="300" cellspacing="0" cellpadding="0" >
	<tr>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top"><b>{element_tag}</b></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p><textarea name="{element_name}" cols="{columns}" rows="{lines}" wrap="virtual">{value}</textarea></p></td>
	</tr>
	<tr>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p><i>{element_desc}</i></p></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="1" height="15" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top"><img src="../../interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td>
	</tr>
</table>
<!-- END copytext -->

<!-- BEGIN link -->
<table border="0" width="300" cellspacing="0" cellpadding="0" >
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../../interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><b>{element_tag}</b></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../../interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="1">Link Beschriftung:</td>
		<td align="left" valign="top" colspan="1"><input type="text" name="{element_name}name" size="50" value="{text}"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../../interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="1"><p>Link:</p></td>
		<td align="left" valign="top" colspan="1"><input type="text" name="{element_name}link" size="50" value="{link}"></td>	
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../../interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>		
	<tr>
		<td align="left" valign="top" colspan="3"><p><i>{element_desc}</i></p></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../../interface/images/blank.gif" alt="" width="1" height="15" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../../interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td>
	</tr>
</table>
<!-- END link -->
<!-- BEGIN image -->
<table border="0" width="300" cellspacing="0" cellpadding="0" >
	<tr>
		<td align="left" valign="top" colspan="2"><img src="../../interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="middle" colspan="2"><b>{element_tag}</b></td>
	</tr>		
	<tr>
		<td align="left" valign="top" colspan="2"><img src="../../interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="2">{file}</td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="2"><img src="../../interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="2"><p><input type="file" name="{element_name}" maxlength="60" size="40" value="{filename}"></p></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="2"><img src="../../interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="2"><p><i>{element_desc}</i></p></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="2"><img src="../../interface/images/blank.gif" alt="" width="1" height="15" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="2"><img src="../../interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td>
	</tr>
</table>
<!-- END image -->
<!-- BEGIN file -->
<table border="0" width="300" cellspacing="0" cellpadding="0" >
	<tr>
		<td align="left" valign="top" colspan="2"><img src="../../interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="middle"><b>{element_tag}</b></td>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="26" height="24" border="0"></td>
	</tr>		
	<tr>
		<td align="left" valign="top" colspan="2"><img src="../../interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>		
	<tr>
		<td align="left" valign="top" colspan="2"><input type="text" name="{element_name}name" maxlength="60" size="50" value="{text}"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../../interface/images/blank.gif" alt="" width="1" height="4" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="2"><span class="ghost">{filename}</span></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../../interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>				
	<tr>
		<td align="left" valign="top" colspan="2"><p><input type="file" name="{element_name}" maxlength="60" size="40" value="{filename}"></p></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="2"><img src="../../interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="2"><p><i>{element_desc}</i></p></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="2"><img src="../../interface/images/blank.gif" alt="" width="1" height="15" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="2"><img src="../../interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td>
	</tr>
</table>
<!-- END file -->
<!-- BEGIN container -->
<table width="200" border="0" cellspacing="0" cellpadding="0">	
	<tr>
		<td align="left" valign="top" colspan="6"><img src="../../interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
	<tr>
		<td valign="middle"><b>{element_tag}</b></td>
		<td valign="top" width="22"><img src="../../interface/images/blank.gif" width="22" height="18"></td>
		<td valign="top" width="22"><a href="javascript:wm_addElement('{addlinkURL}');"><img src="../../interface/images/icon_add.gif" width="26" height="24" border="0"></a></td>
		<td valign="top" width="22"><a href="javascript:wm_executeCommand('{editURL}')"><img src="../../interface/images/icon_edit.gif" width="26" height="24" border="0"></a></td>
		<td valign="top" width="22"><a href="javascript:wm_deleteItem('{deleteURL}')"><img src="../../interface/images/icon_trash.gif" width="26" height="24" border="0"></a></td>
	</tr>	
	<tr>
		<td valign="top"><img src="../../interface/images/blank.gif" width="208" height="1"></td>
		<td valign="top" colspan="4"><img src="../../interface/images/blank.gif" width="88" height="1"></td>
	</tr>	
<!-- END container -->
<!-- BEGIN container_row -->														
	<tr bgcolor="#BBBBBB">
		<td valign="middle" width="87" colspan="5" id="id{linkID}b"><a href="javascript:selectItem('{linkID}');">{decription}</a></td>
	</tr>
	<tr><td valign="top" colspan="6"><img src="../../interface/images/white.gif" width="301" height="1"></td></tr>					
<!-- END container_row -->
<!-- BEGIN container_foot -->
	<tr>
		<td align="left" valign="top" colspan="5"><img src="../../interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="5"><p><i>{element_desc}</i></p></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="5"><img src="../../interface/images/blank.gif" alt="" width="1" height="15" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="5"><img src="../../interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td>
	</tr>												
	</table>				
<!-- END container_foot -->

<!-- BEGIN textselector -->
<table border="0" width="300" cellspacing="0" cellpadding="0" >
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top"><b>{element_tag}</b></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p>{value}</p></td>
	</tr>
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="1" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p><i>{element_desc}</i></p></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top"><img src="interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td>
	</tr>
</table>
<!-- END textselector -->

<!-- BEGIN divider -->
<table border="0" width="300" cellspacing="0" cellpadding="0" >
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../../interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><span class="ghost">{element_tag}</span></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../../interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../../interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../../interface/images/blank.gif" alt="" width="1" height="15" border="0"></td>
	</tr>	
</table>
<!-- END divider -->
<!-- BEGIN normfoot -->
<br></td></tr>
</table>
</form>
</body>
</html>
<!-- END normfoot -->
<!-- BEGIN foot -->
{hiddenfields}
<br><input width="87" height="24" border=0 type=image name="submit" SRC="../../interface/{saveIMG}"><br><br>
</td></tr>
</table>

</form>
<form method="get" name="itemList" action="">
	<input type="hidden" name="tester">
</form>	
<form method="get" name="linkItemList" action="">
	<input type="hidden" name="linkList_Selected">
</form>	
</body>
</html>
<!-- END foot -->
