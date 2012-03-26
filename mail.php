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

	require(ENGINE.CLASSES_DIR."class_mailer.php");
	require(ENGINE.CLASSES_DIR."class_smtp.php");
	require(ENGINE."functions/mail.php");
	
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

	## process the pagename
	$path = utility_dispatcherParseURL($_POST["matrix_pageName"]);	
	$page_id = structure_getPageUsingPath($path);
			
	## finally we will generate the page
	print page_generatePage($page_id,$offset);
	
	## it basically works like the search utility
	$body = generateBody($_POST["matrix_mailTemplate"]);
	
	## get the list of recipients
	$recipients = getRecipients($_POST["matrix_recipient"]);
	
	## if supplied we'll get the sender
	$from = $_POST["matrix_from"];	
	
	
	if(isset($from)) {
		## actually send the mail
		sendMail(array("address"=>$from,"name"=>$from),$_POST["matrix_subject"],$recipients,$body,$attachment);
	} else {
		sendMail(array("address"=>MAIL_FROMMAIL,"name"=>MAIL_FROM),$_POST["matrix_subject"],$recipients,$body,$attachment);
	}
?>