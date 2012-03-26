<?php
	## =======================================================================
	##  content.php														
	## =======================================================================
	##  Version: 		1.0													
	##  Last change: 	09.07.2001												
	##  by: 			S. Elsner												
	## =======================================================================
	
	error_reporting(0);
	
	## first require the configuration
	require("matrix_engine/config.php");
	
	## get the required classes
	require(ENGINE.CLASSES_DIR."template.php");  
	require(ENGINE.CLASSES_DIR."db_mysql.php");
	require(ENGINE.CLASSES_DIR."files.php");
	require(ENGINE.CLASSES_DIR."xmlparser.php");
	
	## get the required functions
	require(ENGINE."userpage.php");
	require(ENGINE."matrix_frontend.php");
	require(ENGINE."functions/utilities.php");
	require(ENGINE."functions/page.php");	
	require(ENGINE."functions/structure.php");
	require(ENGINE."functions/events.php");
	
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
	
	## before we do anything - we check the eventmanager settings
	## depeinding if it is handle by cron or by the index.php file
	## we will need to execute the code here
	if(EVENTMANAGER_TRIGGER == 'index') {
		## okay we need to handle the events
		event_triggerEventManager();
	}
	
	
	## we need to fetch a page id based to the structure of the website
	## we will loop through all path names enterd (max number !)
	## if at any time we can't find a page with that name, we will return the previous page
	## at the and if the page id is -1 we will fetch the homepage
	$path = utility_dispatcherParseURL($_GET['url']);	
	$page_id = structure_getPageUsingPath($path);
	
	## here we handle the special cases (newsletter, and affilitates)
	if(isset($path[0])) {
		switch($path[0]) {
			case 'campaign link':
				## used to track the open rates of a newsletter cmpaign
				## campaign_link/newsletterid/clientid/3
				##
				## here we track the links of the newsletter
				$db = new DB_Sql();
			
				$newsletterID = intval($path[1]);
				$clientID = intval($path[2]);
				$page_id = intval($path[3]);
				$timestamp = time();
				$hour = date('G',$timestamp);
				
				$query = "INSERT INTO ".DB_PREFIX."reports_newsletter_links (client_id,target_id,timestamp,hour,newsletter_id) VALUES ('".$clientID."','".$page_id."','".$timestamp."','".$hour."','".$newsletterID."')";
				$rp = $db->query($query);
				break;
			case 'campaign open':
				## used to track the open rates of a newsletter cmpaign
				## campaign_open/newsletterID/clientID/random.gif
				##
				## here we track the opens of the newsletter
				$db = new DB_Sql();
			
				$newsletterID = intval($path[1]);
				$clientID = intval($path[2]);
				$timestamp = time();
				$hour = date('G',$timestamp);
				
				$query = "INSERT INTO ".DB_PREFIX."reports_newsletter_open (client_id,timestamp,hour,newsletter_id) VALUES ('".$clientID."','".$timestamp."','".$hour."','".$newsletterID."')";
				$rp = $db->query($query);

				header('Content-type: image/gif');
				header('Expires: Sat, 10 Apr 2000 02:19:00 GMT');
				header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
				header('Cache-Control: no-store, no-cache, must-revalidate');
				header('Cache-Control: post-check=0, pre-check=0', false);
				header('Pragma: no-cache');
				
				printf(
				  '%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%',
				  71,73,70,56,57,97,1,0,1,0,128,255,0,192,192,192,0,0,0,33,249,4,1,0,0,0,0,44,0,0,0,0,1,0,1,0,0,2,2,68,1,0,59
				);
				exit;
				
				break;				
			case 'affiliate':
				echo 'partner';
				break;
			case 'page':
				$page_id = intval($path[1]);
				break;					
		}
	}

	## this is very important- if we can't find  page- we need to return
	## 404 to avoid that missing images etc. all reroute to the homepage
	if($page_id == -1 && !empty($path)) {
		header("HTTP/1.0 404 Not Found");
		header("location:/");
		exit;		
	}

	if(MODULE_STATS == "ON") {
		include_once(ENGINE."modules/reports/log.php");
		$reportData = reports_logRequest(array('page_id' => $page_id));
		##reports_storeOrder($reportData,'room booking','1','USD','100.234');
	}	
	
	if(DEVELOPMENT == true) {
		## make sure that the pages are not cahced while we are in development mode
		global $previewMode;
		$previewMode = true;	
	}
	
	print page_generatePage($page_id,$offset);
?>