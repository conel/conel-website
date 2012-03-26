<?PHP

## =======================================================================        
## configModules       
## =======================================================================        
## initializes all the modules that are installed
##   
## ======================================================================= 
function configModules($vInstalledModules) {
	## loop through the modules and get the config files
	for($i=0;$i<count($vInstalledModules);$i++) {
		$moduleSettingsFile = ENGINE."modules/".$vInstalledModules[$i]."/module_init.php";
		if ($file = @file($moduleSettingsFile)) {
			include(ENGINE."modules/".$vInstalledModules[$i]."/module_init.php");
		}
	}

}


## =======================================================================        
## setupModulesNavigation       
## =======================================================================        
## this will call the function to create the admin menu
##   
## ======================================================================= 
function setupModulesNavigation($vInstalledModules) {
	global $Auth;
	
	## check the accesrights first
	$access_rights = _getUserAccessRights($Auth->auth['user_id']);
	
	## setup the vars
	$output = array();
	## loop through the modules
	for($i=0;$i<count($vInstalledModules);$i++) {
		## for each module we call the Navigation Function
		if(isset($access_rights[$vInstalledModules[$i]])) {
			## first we check if it exists
			if (function_exists($vInstalledModules[$i]."_setupNavigation")) {
				## we call the function and should recieve an array containing the label and the url to be placed
				eval("\$output[] = ".$vInstalledModules[$i]."_setupNavigation();");
			}	
		}
	}
	return $output;
}

?>
