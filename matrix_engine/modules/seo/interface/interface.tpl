<!-- BEGIN body -->
<html>
<head>
	<title>webmatrix</title>
	<link type="text/css" rel="stylesheet" href="../../interface/layout.css">
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

<form action="{actionURL}" method="post" name="main">
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

<table border="0" width="300" cellspacing="0" cellpadding="0" >
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../../interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" class="headline" colspan="3"><b>{LANG_DATATYPE_META_Title}</b></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../../interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="3">{LANG_DATATYPE_META_TitleDesc}</td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../../interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../../interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td>
	</tr>
</table>



<table border="0" width="300" cellspacing="0" cellpadding="0" >
	<tr>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top"><b>{LANG_DATATYPE_META_PageTitle}</b></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p><input type="text" name="title" size="50" value="{title_value}"></p></td>
	</tr>
	<tr>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p><i>{LANG_DATATYPE_META_PageTitleDesc}</i></p></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>
</table>

<table border="0" width="300" cellspacing="0" cellpadding="0" >
	<tr>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top"><b>{LANG_DATATYPE_META_PageDesc}</b></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p><textarea name="description" cols="50" rows="6" wrap="virtual">{description_value}</textarea></p></td>
	</tr>
	<tr>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p><i>{LANG_DATATYPE_META_PageDescDesc}</i></p></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
</table>

<table border="0" width="300" cellspacing="0" cellpadding="0" >
	<tr>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top"><b>{LANG_DATATYPE_META_Keywords}</b></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p><textarea name="keywords" cols="50" rows="8" wrap="virtual">{keywords_value}</textarea></p></td>
	</tr>
	<tr>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><p><i>{LANG_DATATYPE_META_KeywordsDesc}</i></p></td>
	</tr>	
	<tr>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="1" height="15" border="0"></td>
	</tr>
</table>

<table border="0" width="326" cellspacing="0" cellpadding="0" >
	<tr>
		<td align="left" valign="top" colspan="6"><img src="../../interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>		
	<tr>
		<td align="right" valign="top" colspan="5"><input width="87" height="24" border=0 type=image name="submit" SRC="{saveIMG}"></td>
	</tr>		
	<tr>
		<td align="left" valign="top" colspan="6"><img src="../../interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
</table>
{hiddenfields}
</form>
<!-- 
<table border="0" width="325" cellspacing="0" cellpadding="0" >
	<tr>
		<td align="left" valign="top" colspan="6"><img src="../../interface/images/seperator.gif" alt="" width="336" height="1" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="6"><img src="../../interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>	
	<tr>
		<td align="left" valign="top" nowrap colspan="5"><b>{element_tag}</b></td>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="1" height="1" border="0"></td>
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
		<td align="left" valign="top" colspan="6"><img src="../../interface/images/blank.gif" alt="" width="1" height="15" border="0"></td>
	</tr>
</table>

 -->

</td></tr>
</table>


</body>
</html>
<!-- END body -->
