<html>
<head>
<style type="text/css">
body {
font-family: Arial, Helvetica, sans-serif; 
font-size: 10pt; 
padding: 0px;
margin:0px;
}
b{
font-family: Arial, Helvetica, sans-serif;
font-size:12pt;
padding:0px;
}
</style>
</head>
<body>
<?php
if (empty($_REQUEST['message']) || empty($_REQUEST['usrname']))
//if "email" is not filled out, display the form
  {
	echo "<form method='post' action='unblock.php'><input name='URL' type='hidden' value='"; 
	echo $_REQUEST['src']; 
	echo "'><input name='cat' type='hidden' value='"; 
	echo $_REQUEST['cat']; 
	echo "'><b>Username:</b> <input name='usrname' type='text' style='width: 207px'";

	if (!empty($_REQUEST['usrname']))
	{
		echo "value='" . $_REQUEST['usrname'] . "'";
	}
	echo "><br /><br /><b>Comments:</b><br /><textarea name='message' rows='7' cols='50'>";

	if (!empty($_REQUEST['message']))
	{
		echo $_REQUEST['message'];
	}
	echo "</textarea><br /><input type='submit' /></form>";
  }
else
//if "email" is filled out, send email
  {
	  //send email
	  $message = "User: " . $_REQUEST['usrname'] . "\nURL: " . $_REQUEST['URL'] . "\n" . $_REQUEST['cat'] ."\nComments: " . $_REQUEST['message'];
	  mail( "unblock@staff.conel.ac.uk", "Subject: Unblock web site",$message, "From: webmaster@staff.conel.ac.uk" );
	  echo "<p><b>Your request will be reviewed and a notification sent out to you shortly.</b></p>";
  }
?>
<a id="bottom"></a>
</body>
</html>