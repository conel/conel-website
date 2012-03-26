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

require("functions/displaysetup.php");
require("functions/savesetup.php");
include_once("config.php");
## we need to load the language specific details
include("lang/".$Auth->auth["language"].".php");

	## this is the main switchbox
	switch($cmd) {
		case "setup":
			## we need to display the setup screen
			displaySetup();
			break;
		
		case "save":
			## we need to store the settings and
			## display a confirmation page
			saveSetup();
			output_confirm(LANG_MODULE_CACHE_TitleSettings,LANG_MODULE_CACHE_SavedSuccessfully,"matrix_menu.php");			
			break;
    	case "empty":
    		## this deletes all files in the cache
    		$f = new file_object();  
    		$f->clean_directory(MODULE_CACHE_DIR);
 			output_confirm(LANG_MODULE_CACHE_TitleClean,LANG_MODULE_CACHE_CleanedSuccessfully,"matrix_menu.php");   		
    		break;
    	default:
			## we need to display the setup screen
			displaySetup();
      	break;
    }
?>
