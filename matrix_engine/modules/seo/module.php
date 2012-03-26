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

## we need to load the language specific details
include("lang/".$Auth->auth["language"].".php");
$cmd = $_POST['cmd'];

	## this is the main switchbox
	switch($cmd) {
		case "save":
			## we need to store the settings and
			## display a confirmation page
			saveSetup();
			$targetURL = 'module.php';          
			$targetURL = $gSession->url($targetURL);	

			output_confirm_refresh(LANG_MODULE_SEO_TitleSettings,LANG_MODULE_SEO_SavedSuccessfully,$targetURL);

			break;
    	case 'overview':
    	default:
			## we need to display the setup screen
			seo_displayOverview();
      	break;
    }
?>
