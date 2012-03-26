<!-- BEGIN body -->
<HTML>
	<HEAD>
		<TITLE>webmatrix</TITLE>
		<META HTTP-EQUIV="EXPIRES" CONTENT="0">
		<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
		<base href="{BaseURL}"> 
	<link href="interface/layout.css" rel="styleSheet" type="text/css">
	<script src="interface/script/md5.js" language="javascript"></script>
    <script language="javascript">
		<!--
  			function doChallengeResponse() {
    			str = document.login.username.value + ":" +
          			  document.login.password.value + ":" +
          			  document.login.challenge.value;

				document.login.response.value = MD5(str);
   				document.login.password.value = "";
   				document.login.submit();
  			}
  		
  			if(top.frames.length > 1)
				top.location = "{base}index.php";			
		// -->
	</script>		</head>
	<body bgcolor="#ffffff" background="interface/images/pattern.gif" onLoad="document.login.username.focus();document.login.username.select();">
		<center>
			<FORM NAME= "login" method=POST action="{actionURL}">
				<input type="hidden" name="response"  value="">
				<input type="hidden" name="challenge" value="{challenge}">
				<img src="interface/images/blank.gif" width="5" height="80">
				<table border="0" cellpadding="0" cellspacing="0" width="375" bgcolor="#FFFFFF">
					<tr>
						<td colspan="2" bgcolor="#787878"><img src="interface/images/black.gif" width="374" height="1"></td>
						<td rowspan="2" valign="top" background="interface/images/border_r.gif"><img src="interface/images/border_tr.gif" width="5" height="4"></td>
					</tr>
					<tr>
						<td bgcolor="#787878"><img src="interface/images/black.gif" width="1" height="212"></td>
						<td valign="top" class="small">
							<table border="0" cellpadding="0" cellspacing="0" width="374" bgcolor="#FFFFFF">
								<tr>
									<td colspan="3"><img src="interface/images/info.gif" width="374" height="70"></td>
								</tr>
								<tr>
									<td colspan="3"><img src="interface/images/blank.gif" width="2" height="5" border="0"></td>
								</tr>
								<tr>
									<td><img src="interface/images/blank.gif" width="12" height="2" border="0"></td><td colspan="2" class="login">Build: {version}</td>
								</tr>
								<tr>
									<td colspan="3"><img src="interface/images/blank.gif" width="2" height="2" border="0"></td>
								</tr>
								<tr>
									<td><img src="interface/images/blank.gif" width="12" height="2" border="0"></td><td class="login">Lizenz: {lizenz}</td><td></td>
								</tr>
								<tr>
									<td colspan="3"><img src="interface/images/blank.gif" width="2" height="5" border="0"></td>
								</tr>
								<tr>
									<td colspan="3"><img src="interface/images/blank.gif" width="2" height="15" border="0"></td>
								</tr>
								<tr>
									<td></td>
									<td colspan="2"><table border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td class="small">{language_username}:</td><td></td><td></td>
										</tr>
										<tr>
											<td><input type="text" name="username" size="16" class="default" style="width:220"></td><td></td><td class="default"></td>
										</tr>
										<tr>
											<td colspan="2"><img src="interface/images/blank.gif" width="2" height="5" border="0"></td><td></td>
										</tr>
										<tr>
											<td class="small">{language_password}:</td><td></td><td></td>
										</tr>
										<tr>
											<td><input type="password" name="password" size="16" class="default" style="width:220"></td>
										</tr>
										<tr>
											<td colspan="2"><img src="interface/images/blank.gif" width="2" height="10" border="0"></td><td></td>
										</tr>
										<tr>
											<td class="small">{language_langselect}:</td><td></td><td></td>
										</tr>										
										<tr>
											<td><select name="language" size="1">{options}</select></td>
										</tr>										
										<tr>
											<td colspan="2"><img src="interface/images/blank.gif" width="2" height="15" border="0"></td><td></td>
										</tr>	
										<tr>
											<td></td><td colspan="2"><input type="image" border="0" width="66" height="24" src="interface/images/button_login.gif" onClick="doChallengeResponse();"></td>
										</tr>
										<tr>
											<td colspan="2"><img src="interface/images/blank.gif" width="2" height="15" border="0"></td><td></td>
										</tr>
										<tr>
											<td colspan="2" class="login">{language_copyright}</td><td></td>
										</tr>
										<tr>
											<td colspan="2"><img src="interface/images/blank.gif" width="2" height="5" border="0"></td><td></td>
										</tr>																																																	
									</table></td>
								</tr>
							</table>
						</td>

					</tr>
					
					<tr>
						<td colspan="2" background="interface/images/border_b.gif"><img src="interface/images/border_bl.gif" width="4" height="5"></td>
						<td><img src="interface/images/border_br.gif" width="5" height="5"></td>
					</tr>

				</table>
			</form>
		</center>
	</body>
</html>
<!-- END body -->
