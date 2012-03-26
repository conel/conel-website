<?php
## =======================================================================        
##  config.php        
## =======================================================================        
##  used to define all vars that need to be set when we install this onto
##  a new server- later we will search an replace the vbars to protect 
##  the code
##
##  TODO:   
##     - check if it works    
## =======================================================================
	require("../framework.php");
	
	require("functions/overview.php");
	require("functions/savesetup.php");
	
	## get the page functions
	require("pages/overview.php");
	
	## we need to load the language specific details
	include("lang/".$Auth->auth["language"].".php");
	
	
	## okay first parse the url
	$params = utility_dispatcherParseURL($_GET['url']);	
	
	## we will only look at the first parameters
	$page = isset($params[0]) ? $params[0] : '';
	
	## okay now we determine which page we need to call
	switch($page) {
		case 'overview':
			## okay we will pass the control to the pages main function
			search_overview($params);
			break;
		case 'thesaurus':
			## okay we will pass the control to the pages main function
			echo '1';
			break;	
		case 'featuredresults':
			## okay we will pass the control to the pages main function
			echo '2';
			break;			
		case 'component':
			## the correct function name: module_pageActionComponent
			## construct the fucntion name
			sleep(1);
			eval("\$element = search_overview".ucfirst($params[1]).ucfirst($params[2])."();");	
			echo $element;
			break;		
		default:
			## we haven't received anything- so we will call our default page
			search_overview($params);
			break;
	}
?>
