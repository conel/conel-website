<!-- BEGIN header -->
<html>
<head>
	<title>{language_selectlayouttitle}</title>
	<link type="text/css" rel="stylesheet" href="../../interface/layout.css">
	<script type="text/javascript" language="Javascript">
		
		function wm_newElement(mURL) {
			document.main.cmd.value = "save";
			// now open the window, and then fresh the page
			wm_openWindow(mURL,'addlink','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=480,height=280');
			document.main.submit();
		}
		function wm_openWindow(theURL,winName,features) {
  			window.open(theURL,winName,features);
		}			
		function wm_calculateAvrg() {
		  if (document.forms.main.totalpurchases.value != 0 &amp;&amp; document.forms.main.totalpurchases.value != "") {
            var temp = Math.round((document.forms.main.totalspend.value / document.forms.main.totalpurchases.value) * 100);
            document.forms.main.averagespend.value = temp/100;
          } else {
            document.forms.main.averagespend.value = 0;
          }
		}
	</script>	

</head>
<body bgcolor="#FFFFFF" text="#000000" link="#000000" background="interface/images/pattern.gif">	
<form action="{actionURL}" method="post" name="main">
<table border="0" width="300" cellspacing="0" cellpadding="0">
	<tr>
		<td align="left" valign="top" rowspan="2"><img src="../clients/interface/images/blank.gif" alt="" width="15" height="1" border="0"></td>
		<td align="left" valign="top"><img src="../clients/interface/images/blank.gif" alt="" width="5" height="1" border="0"></td>
		<td align="left" valign="top"><img src="../clients/interface/images/blank.gif" alt="" width="300" height="15" border="0"></td>
	</tr>	
	<tr>
		<td bgcolor="#FFFFFF" align="left" valign="top"><img src="../clients/interface/images/blank.gif" alt="" width="5" height="1" border="0"></td>
		<td bgcolor="#FFFFFF">
			<table border="0" width="480" cellspacing="0" cellpadding="0">
				<tr><td align="left" valign="top" colspan="4"><img src="../clients/interface/images/blank.gif" alt="" width="1" height="15" border="0"></td></tr>
				<tr>
					<td align="left" valign="top"><img src="../clients/interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
					<td align="left" valign="top" class="headline" colspan="3"><b>{language_header}</b></td>
				</tr>	
				<tr><td align="left" valign="top" colspan="4"><img src="../clients/interface/images/blank.gif" alt="" width="1" height="5" border="0"></td></tr>	
				<tr>
					<td align="left" valign="top"><img src="../clients/interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
					<td align="left" valign="top" colspan="4">{language_description}</td>
				</tr>
				<tr><td align="left" valign="top" colspan="4"><img src="../clients/interface/images/blank.gif" alt="" width="1" height="5" border="0"></td></tr>
				<tr>
					<td align="left" valign="top"><img src="../clients/interface/images/blank.gif" alt="" width="1" height="1" border="0"></td>
					<td align="left" valign="top" colspan="3"><img src="../clients/interface/images/seperator.gif" alt="" width="440" height="2" border="0"></td>
				</tr>
				<tr><td align="left" valign="top" colspan="4"><img src="../clients/interface/images/blank.gif" alt="" width="1" height="5" border="0"></td></tr>	
			</table>		
			<table width="480" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td align="left" valign="top" rowspan="20"><img src="../clients/interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
					<td background="interface/images/blank.gif"><img src="../clients/interface/images/blank.gif" width="2" height="8" border="0"></td>
				</tr>
				<tr>
					<td>
						<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td valign="top"><strong>&nbsp;</strong></td>
								<td>
								<table width="300" cellpadding="0" cellspacing="0" border="0">
									<tr valign="top">
										<td>
											<table cellpadding="0" cellspacing="0" border="0">
												<tr>
													<td align="left" colspan="1">Groupname</td>
												</tr>
												<tr>
													<td colspan="1"><img src="../clients/interface/images/blank.gif" width="2" height="1" border="0"></td>
												</tr>
												<tr>
													<td><input type="text" name="groupname" size="28" value="{groupname}"></td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td><img src="../clients/interface/images/blank.gif" width="1" height="10" border="0"></td>
						</tr>
					</table>
						<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td valign="top"><strong>&nbsp;</strong></td>
								<td>
								<table width="300" cellpadding="0" cellspacing="0" border="0">
									<tr valign="top">
										<td>
											<table cellpadding="0" cellspacing="0" border="0">
												<tr>
													<td align="left" colspan="1">XML Control-File</td>
												</tr>
												<tr>
													<td colspan="1"><img src="../clients/interface/images/blank.gif" width="2" height="1" border="0"></td>
												</tr>
												<tr>
													<td><input type="text" name="controlfile" size="28" value="{controlfile}"></td>
												</tr>
											</table>
										</td>
										<td><img src="../clients/interface/images/blank.gif" width="10" height="1" border="0"></td>
									</tr>
																																													
								</table>
							</td>
						</tr>
						<tr>
							<td><img src="../clients/interface/images/blank.gif" width="1" height="10" border="0"></td>
						</tr>
					</table>																							
				</td>
	</tr>
	<tr>
		<td><img src="../clients/interface/images/blank.gif" alt="" width="400" height="14" border="0"></td>
	</tr>
</table>
<table border="0" width="300" cellspacing="0" cellpadding="0">
	<!-- END header -->
	<!-- BEGIN body -->	
	<tr>
		<td align="left" valign="top"><img src="../clients/interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
		<td align="left" valign="top"><img src="../clients/interface/images/blank.gif" alt="" width="87" height="1" border="0"></td>
		<td align="left" valign="top"><img src="../clients/interface/images/blank.gif" alt="" width="200" height="1" border="0"></td>
		<td align="left" valign="top"><img src="../clients/interface/images/blank.gif" alt="" width="84" height="1" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="4"><img src="../clients/interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top"><img src="../clients/interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
		<td align="left" valign="top" colspan="3"><img src="../clients/interface/images/seperator.gif" alt="" width="440" height="2" border="0"></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="4"><img src="../clients/interface/images/blank.gif" alt="" width="1" height="5" border="0"></td>
	</tr>
	<!-- END body -->
	<!-- BEGIN footer -->	
	<tr>
		<td align="left" valign="top"><img src="../clients/interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
		<td align="left" valign="top"><a href="{backURL}"><img src="{backIMG}" alt="" width="84" height="24" border="0"></a></td>
		<td align="left" valign="top"><img src="../clients/interface/images/blank.gif" alt="" width="1" height="1" border="0"></td>
		<td align="right" valign="top">{hiddenfields}
		<input border=0 type=image name="submit" SRC="{saveIMG}"></td>		
	</tr>
	<tr>
		<td align="left" valign="top" colspan="4"><img src="../clients/interface/images/blank.gif" alt="" width="1" height="10" border="0"></td>
	</tr>					
</table>
</td></tr>
</table>
</form>

</body>
</html>
<!-- END footer -->


<!-- BEGIN grouphead -->
<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top"><strong>{group_name}</strong></td>
			<td>
<!-- END grouphead -->	
<!-- BEGIN rowhead -->
			<table width="300" cellpadding="0" cellspacing="0" border="0">
				<tr valign="top">				
<!-- END rowhead -->
<!-- BEGIN column -->	
					{element}
<!-- END column -->	
<!-- BEGIN rowfoot -->
				</tr>
			</table>	
		</td>
	</tr>
<!-- END rowfoot -->	
<!-- BEGIN groupfoot -->		
	<tr>
		<td><img src="../clients/interface/images/blank.gif" width="80" height="10" border="0"></td>
		<td></td>
	</tr>
</table>
<!-- END groupfoot -->					