<!-- BEGIN body -->
<html>
<head>
	<title>webmatrix</title>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
	<link type="text/css" rel="stylesheet" href="../../interface/layout.css">
	<script language="javascript" type="text/javascript" src="interface/script/upload.js"></script>
<script type="text/javascript">
    var uploads_in_progress = 0;

    function beginAsyncUpload(ul,sid) {		
      ul.submit();
    	uploads_in_progress = uploads_in_progress + 1;
    	var pc = document.getElementById("progresscontainer");
    	var fs = document.getElementById("fileselector");
    	var pb = document.getElementById("{element_name}_progress");
    	var submit_button = document.getElementById("submit");
    	pc.style.display='block';
    	
    	submit_button.style.display='none';

    	new ProgressTracker(sid,{
    		progressBar: pb,
    		onComplete: function() {
    			var inp_id = pb.id.replace("_progress","");
    			uploads_in_progress = uploads_in_progress - 1;
    			var inp = document.getElementById(inp_id);
    			if(inp) {
    				inp.value = sid;
    				document.forms.main.submit();
    			}
    			pc.style.display='none';
    			submit_button.style.display='block';
    		},
    		onFailure: function(msg) {
    			pc.style.display='none';
    			alert(msg);
    			uploads_in_progress = uploads_in_progress - 1;
    		}
    	});
    }
    
    function submitUpload(frm) {
      if(uploads_in_progress > 0) {
        alert("File upload in progress. Please wait until upload finishes and try again.");
      } else {
        frm.submit();
      }
    }
	</script>	
</head>
<body bgcolor="#FFFFFF" text="#000000" link="#000000" background="../../interface/images/pattern.gif">
<form action="{actionURL}" method="post" name="main">   
<input type="hidden" name="async" value="true" />
<input id="{element_name}" type="hidden" name="{element_name}" value="">
{hiddenfields}
</form>
	
<form action="/cgi-bin/upload.cgi?sid={HASH}" method="post" enctype="multipart/form-data" name="upload" onsubmit="beginAsyncUpload(this,'{HASH}');" target="iframe_{element_name}">
<input type="hidden" name="sid" value="{HASH}" />
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
<div id="fileselector" style="display: block;">
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
	<tr>
		<td align="left" valign="top" colspan="2"><p><input type="file" name="{element_name}" maxlength="60" size="40" value="{filename}"></p></td>
	</tr>		
	<tr>
		<td align="left" valign="top" colspan="2"><img src="../../interface/images/blank.gif" alt="" width="1" height="15" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="2"><img src="../../interface/images/seperator.gif" alt="" width="306" height="2" border="0"></td>
	</tr>
</table>
</div>
<div id="progresscontainer" style="display: none;">
<table border="0" width="300" cellspacing="0" cellpadding="0">
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../../interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3" id="{element_name}_progress">0%</td>
	</tr>	
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../../interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../../interface/images/progress_flat.gif" alt="" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../../interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="3"><img src="../../interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>
</table>
</div>

<br><input width="87" height="24" border=0 type=image name="submit" id="submit" SRC="../../interface/lang/{saveIMG}"><br><br>
</td></tr>
</table>
</form>	
<iframe name="iframe_{element_name}" style="border: 0;width: 0px;height: 0px;"></iframe>
</body>
</html>

<!-- END body -->
