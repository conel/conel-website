<!-- BEGIN body -->
<html>
<head>
	<title>webmatrix</title>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
	<link type="text/css" rel="stylesheet" href="../../interface/layout.css">
	
</head>
<body bgcolor="#FFFFFF" text="#000000" link="#000000" background="../../interface/images/pattern.gif">	
<table border="0" width="300" cellspacing="0" cellpadding="0">
	<tr>
		<td align="left" valign="top" rowspan="2"><img src="../../interface/images/blank.gif" alt="" width="15" height="1" border="0"></td>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="5" height="1" border="0"></td>
		<td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="366" height="15" border="0"></td>
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
<!-- {actionURL} -->
<form action="{processURL}" method="post" name="main" enctype="multipart/form-data">   
<input type="hidden" name="async" value="true" />
<input id="{element_name}" type="hidden" name="{element_name}" value="">
{hiddenfields}
<table border="0" width="300" cellspacing="0" cellpadding="0" >
	<tr><td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="8" border="0"></td></tr>
	<tr><td align="left" valign="top"><b>Height</b></td></tr>	
	<tr><td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="2" border="0"></td></tr>	
	<tr><td align="left" valign="top"><p><input type="text" name="{element_name}_HEIGHT" size="50" value="{height}" id="{element_name}_HEIGHT"></p></td></tr>
	<tr><td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="1" border="0"></td></tr>	
	<tr><td align="left" valign="top"><p><i> </i></p></td></tr>	
	<tr><td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="1" border="0"></td></tr>
	<tr><td align="left" valign="top"><img src="interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td></tr>
</table>
<table border="0" width="300" cellspacing="0" cellpadding="0" >
	<tr><td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="8" border="0"></td></tr>
	<tr><td align="left" valign="top"><b>Width</b></td></tr>	
	<tr><td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="2" border="0"></td></tr>	
	<tr><td align="left" valign="top"><p><input type="text" name="{element_name}_WIDTH" size="50" value="{width}" id="{element_name}_WIDTH"></p></td></tr>
	<tr><td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="1" border="0"></td></tr>	
	<tr><td align="left" valign="top"><p><i> </i></p></td></tr>	
	<tr><td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="1" border="0"></td></tr>
	<tr><td align="left" valign="top"><img src="interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td></tr>
</table>
<table border="0" width="300" cellspacing="0" cellpadding="0" >
	<tr><td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="8" border="0"></td></tr>
	<tr><td align="left" valign="top"><b>Autoplay</b></td></tr>	
	<tr><td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="2" border="0"></td></tr>	
	<tr><td align="left" valign="top"><p><input name="{element_name}_autoplay" id="autoplay" type="checkbox" value="1" {autoplay}></p></td></tr>
	<tr><td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="1" border="0"></td></tr>	
	<tr><td align="left" valign="top"><p><i> </i></p></td></tr>	
	<tr><td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="1" border="0"></td></tr>
	<tr><td align="left" valign="top"><img src="interface/images/seperator.gif" alt="" width="366" height="2" border="0"></td></tr>
</table>
<table border="0" width="300" cellspacing="0" cellpadding="0" >
	<tr>
		<td align="left" valign="top" colspan="2"><img src="../../interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="2"><span class="ghost">{filename}</span></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../../interface/images/blank.gif" alt="" width="1" height="2" border="0"></td>
	</tr>				
	<tr><td align="left" valign="top"><b>File</b></td></tr>	
	<tr><td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="2" border="0"></td></tr>	
	<tr>
		<td align="left" valign="top" colspan="2">
		<p>
		    <!-- max file size = 100MB -->
		    <input type="hidden" name="MAX_FILE_SIZE" value="104857600" />
		    Choose an .flv file to upload: <input name="uploadedfile" type="file" />
		</p>
		</td>
	</tr>		
	<tr>
		<td align="left" valign="top" colspan="2"><img src="../../interface/images/blank.gif" alt="" width="1" height="15" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="2"><img src="../../interface/images/seperator.gif" alt="" width="306" height="2" border="0"></td>
	</tr>
</table>

<br><input width="87" height="24" border=0 type=image name="submit" id="submit" SRC="../../interface/lang/{saveIMG}"><br><br>
</form>
</td></tr>
</table>
</body>
</html>

<!-- END body -->
