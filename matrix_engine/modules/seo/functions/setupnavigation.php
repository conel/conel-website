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
function seo_setupNavigation() {
	global $gSession,$Auth;
	
	## prepare the URL to the cache Module
	$backendURL = 'modules/seo/module.php';          
	$backendURL = $gSession->url($backendURL);	

	$return_value = array('URL' => $backendURL,'LABEL' => 'Search Engine Optimisation');
	return $return_value;
}

?>