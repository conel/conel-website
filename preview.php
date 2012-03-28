<?php
	## =======================================================================
	##  preview.php														
	## =======================================================================
	##  
	## this is the main file for previewing a page form within the cms
	## it is protected by the normal access rights settings.
	## =======================================================================

	require("matrix_engine/config.php");
	define("FROM_ENFIELD", FALSE);

	## include the template class
	require(ENGINE.CLASSES_DIR."template.php");  
	## include the db class
	require(ENGINE.CLASSES_DIR."db_mysql.php");
	require(ENGINE.CLASSES_DIR."xmlparser.php");
	require(ENGINE.CLASSES_DIR."files.php");
    ## include the db class
	require(MATRIX_BASEDIR."user_modules.php");

    ## include the db class
	require(ENGINE."userpage.php");
	require(ENGINE."matrix_frontend.php");
	require(ENGINE."functions/utilities.php");
	require(ENGINE."functions/page.php");
	
	require(ENGINE."functions/structure.php");

	require(ENGINE.CLASSES_DIR."container.php");
	require(ENGINE.CLASSES_DIR."session.php");
	require(ENGINE.CLASSES_DIR."authentication.php");
	require(ENGINE.CLASSES_DIR."page.php");

	page_open(array("session" => "session_object", "authenticate" => "Auth")); 
	page_close();
	
	## process the values we expect
	$page_id = isset($_GET['page_id']) ? intval($_GET['page_id']) : (isset($_POST['page_id']) ? intval($_POST['page_id']) : null);
	$offset  = isset($_GET['offset']) ? intval($_GET['offset']) : null;
	
	global $previewMode;
	$previewMode = true;	

	print page_generatePage($page_id,$offset);	

?>
