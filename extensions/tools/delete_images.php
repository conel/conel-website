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
	
	## first we try to find the orphand entries in image_data
	
	

	## we need to fetch all images in the images folder
	$dir = MATRIX_UPLOADDIR;
	
	$files = array();
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if(filetype($dir.$file) != 'dir') {
					$files[] = $file;
				}
			}
			closedir($dh);
		}
	}	

	## data connection
	$db = new DB_Sql();
	
	## loop through all files
	$files_to_delete = array();
	foreach($files as $current_file) {
		$query = "SELECT * FROM ".PAGE_IMAGE."_data WHERE filename='$current_file'";
		$rp = $db->query($query);
		
		if($db->num_rows() == 0) {
			$files_to_delete[] = $current_file;
		}
	}
	
	var_dump($files_to_delete);



?>
