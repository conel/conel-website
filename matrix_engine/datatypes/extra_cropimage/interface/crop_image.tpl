<!-- BEGIN body -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>webmatrix</title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<link type="text/css" rel="stylesheet" href="../../interface/layout.css">

<link rel="stylesheet" href="interface/css/crop.css" type="text/css">

<script src="interface/js/jquery-1.2.3.min.js" type="text/javascript"></script>
<script type="text/javascript" src="interface/js/jquery.cropperui.js"></script>
<script type="text/javascript" src="interface/js/crop2img.js"></script>

</head>
<body bgcolor="#FFFFFF" text="#000000" link="#000000" background="../../interface/images/pattern.gif">

<form action="{actionURL}" method="post" enctype="multipart/form-data" name="main">
<table border="0" width="300" cellspacing="0" cellpadding="0">
	<tr>
		<td align="left" valign="top" rowspan="2"><img src="../../interface/images/blank.gif" alt="" width="15" height="1" border="0"></td>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="5" height="1" border="0"></td>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="500" height="15" border="0"></td>
	</tr>	
	<tr>
		<td bgcolor="#FFFFFF" align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="5" height="1" border="0"></td>
		<td bgcolor="#FFFFFF">

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

<table border="0" width="300" cellspacing="0" cellpadding="0" >
	<tr><td align="left" valign="top" colspan="2"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td></tr>
	<tr>
		<td align="left" valign="middle"><b>{element_tag}</b></td>
		<td valign="top" width="22"></td>	
	</tr>		
	<tr>
		<td valign="top"><img src="interface/images/blank.gif" width="274" height="1"></td>
		<td valign="top"><img src="interface/images/blank.gif" width="22" height="1"></td>
	</tr>	
	<tr>
		<td align="left" valign="top">
<div id="canvas">
<div id="image">
<img src="{IMAGE}" class="original" alt="{WIDTH},{HEIGHT}">
</div>
</div>
	<br>	
		</td>
		<td valign="top"><img id="resizeicon" src="/matrix_engine/interface/images/icon_resize.gif" title="Maximize selection" /></td>
	</tr>		
	<tr>
		<td align="left" valign="top" colspan="2"><img src="interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="2"><img src="interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td>
	</tr>
</table>
{hiddenfields}
		<input name="top" id="top" type="hidden" value="0" /><br />
		<input name="left" id="left" type="hidden" value="0" /><br />
		<input name="width" id="width" type="hidden" value="0" /><br />
		<input name="height" id="height" type="hidden" value="0" /><br />
		<input name="scalefactor" id="scalefactor" type="hidden" value="0" />
		
<br><input width="87" height="24" border=0 type=image name="submit" SRC="../../interface/lang/{saveIMG}"><br><br>

</td></tr>
</table>

</form>
</body>
</html>
<!-- END body -->
