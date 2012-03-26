<?php
	require("../../config.php");
	if(REWRITE_GLOBALS == "ON") {
		include("../../functions/register_globals.php");
	}	
	## include the template class
	require("../../".CLASSES_DIR."template.php");
  
	## include the db class
	require("../../".CLASSES_DIR."db_mysql.php");


	# call all session related objects
	require("../../".CLASSES_DIR."container.php");
	require("../../".CLASSES_DIR."session.php");
	require("../../".CLASSES_DIR."authentication.php");
	require("../../".CLASSES_DIR."page.php");
	require("../../".CLASSES_DIR."class_mailer.php");
	
	require("../../functions/access.php");
	require("../../functions/ui_dialogs.php");
	
  	page_open(array("session" => "session_object"));
  	page_close();
	include("../../interface/lang/".$Auth->auth["language"].".php");

	## multiclient
	$client_id = $Auth->auth["client_id"];
?>
