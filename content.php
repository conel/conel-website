<?php
	## =======================================================================
	##  content.php														
	## =======================================================================
	##  Version: 		1.0													
	##  Last change: 	09.07.2001												
	##  by: 			S. Elsner												
	## =======================================================================	
	## first require the configuration
	require("matrix_engine/config.php");
	
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
	
	## process the values we expect
	$page_id = isset($_GET['page_id']) ? intval($_GET['page_id']) : (isset($_POST['page_id']) ? intval($_POST['page_id']) : null);
	$offset  = isset($_GET['offset']) ? intval($_GET['offset']) : null;
	$name    = isset($_GET['name']) ? addslashes($_GET['name']) : null;
			
	if(isset($name) && !isset($page_id))  {
		## in this case we target the page using its menu name
		$page_id = structure_getPageID($name);
	}

	if(MODULE_STATS == "ON") {
		include_once(ENGINE."modules/reports/log.php");
		reports_logRequest(array('page_id' => $page_id));
		
	}
	
	if(DEVELOPMENT == true) {
		## make sure that the pages are not cached while we are in development mode
		global $previewMode;
		$previewMode = true;	
	}

	## finally we will generate the page
	print page_generatePage($page_id,$offset);	
?>
