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
	$configmanager->setConfigPath(MATRIX_BASEDIR."settings/modules/settings/");
	$configmanager->setConfigFile("prefs");
	$configmanager->setConfigName("settings");
	
	## now we can add the vars to the configuration
	$configmanager->addConfigVar('SMTPHOST',$_POST['SMTPHOST']);
	$configmanager->addConfigVar('SMTPUSER',$_POST['SMTPUSER']);
	$configmanager->addConfigVar('SMTPPASS',$_POST['SMTPPASS']);

	$configmanager->addConfigVar('SENDERSEMAIL',$_POST['SENDERSEMAIL']);
	$configmanager->addConfigVar('SENDERSNAME',$_POST['SENDERSNAME']);
	
	## finally flush the config file
	$configmanager->writeConfigFile();
}
?>