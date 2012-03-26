<?php
	## =======================================================================
	##  content.php														
	## =======================================================================
	##  Version: 		1.0													
	##  Last change: 	09.07.2001												
	##  by: 			S. Elsner												
	## =======================================================================	
	## first require the configuration
	require("../../matrix_engine/config.php");
	
	## get the required classes
	require(ENGINE.CLASSES_DIR."template.php");  
	require(ENGINE.CLASSES_DIR."db_mysql.php");
	require(ENGINE.CLASSES_DIR."files.php");
	require(ENGINE.CLASSES_DIR."xmlparser.php");
	
	## get the required functions
	require(ENGINE."matrix_frontend.php");
	require(ENGINE."functions/utilities.php");
	require(ENGINE."functions/page.php");	
	require(ENGINE."functions/structure.php");
	
	## now get the user modules
	require(MATRIX_BASEDIR."user_modules.php");
		
	## workaround
	class Auth {
		var $auth = array();
		function Auth() {
			$this->auth["client_id"] = SITE_OWNER;
		}
	}
	
	global $Auth;
	$Auth = new Auth;	
	
	## data connection
	$db_source = new DB_Sql();
	$db_target = new DB_Sql();
	
	## get all text elements
	$select_query = "SELECT * FROM ".PAGE_CONTENT." WHERE 1";
	$result_pointer = $db_source->query($select_query);

	## loop through the results and copy them over
	while($db_source->next_record()) {
		$text = utf8_encode($db_source->Record['text']);
		$content_id = $db_source->Record['content_id'];

		$query = "UPDATE ".PAGE_CONTENT." SET text = '$text' WHERE content_id='$content_id'";
		$result_pointer = $db_target->query($query,true);
	}

?>
