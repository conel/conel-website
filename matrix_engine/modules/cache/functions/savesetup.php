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
function saveSetup() {
	global $Auth, $gSession;
	global $_POST;

	## okay we should prepare the data to be stored
	## in the config file
	
	## get the configmanager
	$configmanager = new Configmanager();
	$configmanager->setConfigFile('settings');
	$configmanager->setConfigName('cache_config');
	
	## now we can add the vars to the configuration
	$configmanager->addConfigVar('MODULE_CACHE_ACTIVE',$_POST['MODULE_CACHE_ACTIVE'],'boolean');
	$configmanager->addConfigVar('CACHE_SEND304',$_POST['CACHE_SEND304'],'boolean');
	
	## set the time
	$cachetime = $_POST['CACHE_TIME_year'] + $_POST['CACHE_TIME_month']*60 + $_POST['CACHE_TIME_day']*60*60;
	
	$configmanager->addConfigVar('CACHE_TIME',$cachetime,'integer');

	## finally flush the config file
	$configmanager->writeConfigFile();
}
?>