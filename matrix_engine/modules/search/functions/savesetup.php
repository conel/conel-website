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
	$configmanager->setConfigPath(ENGINE."modules/seo/");
	$configmanager->setConfigFile('settings');
	$configmanager->setConfigName('module_seo');
	
	## now we can add the vars to the configuration
	$configmanager->addConfigVar('TITLE',$_POST['title'],'string');
	$configmanager->addConfigVar('DESCRIPTION',$_POST['description'],'string');
	$configmanager->addConfigVar('KEYWORDS',$_POST['keywords'],'string');
	
	## finally flush the config file
	$configmanager->writeConfigFile();
}
?>