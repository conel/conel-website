<!-- BEGIN body -->
<html>
<head>
<title>Close Window</title>
<script language="JavaScript">
	function closeWindow(){
		// first we need to set the var to reload
		opener.document.main.cmd.value = "save";
	    opener.document.main.submit();
	    top.window.close(); 
	}
</script>
</head>
	<body bgcolor="#E6E6E6" text="#000000" link="#000000" alink="#000000" vlink="#000000" onload="javascript:closeWindow();">
	</body>
</html>	
<!-- END body -->