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
function cache_setupNavigation() {
	global $gSession,$Auth;

	## prepare the URL to the cache Module
	$backendURL = 'modules/cache/module.php';          
	$backendURL = $gSession->url($backendURL);	

	$return_value = array('URL' => $backendURL,'LABEL' => 'Cache');
	return $return_value;
}

?>