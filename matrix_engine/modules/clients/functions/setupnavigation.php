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
function clients_setupNavigation() {
	global $gSession,$Auth;

	include(ENGINE."modules/clients/interface/lang/".$Auth->auth["language"].".php");
	
	## clear the query cache
	$db_connection = new DB_Sql();
	$query = 'DELETE FROM '.DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_searchcache WHERE user_id='".$Auth->auth['user_id']."'";
	$rp = $db_connection->query($query);   		

	## prepare the URL to the cache Module
	$backendURL = 'modules/clients/module.php?clear_session=1';          
	$backendURL = $gSession->url($backendURL);	

	$return_value = array('URL' => $backendURL,'LABEL' => LANG_MODULE_CLIENTS_Title);
	return $return_value;
}

?>