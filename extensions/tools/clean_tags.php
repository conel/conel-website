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

	## we need to loop trhough all tags and check if there are client entries available
	$db = new DB_Sql();
	$query = "SELECT client_id FROM ".DB_PREFIX."images2keywords WHERE 1=1";
	$result_pointer = $db_connection->query($query);	

	if($db_connection->num_rows() > 0) {


?>
