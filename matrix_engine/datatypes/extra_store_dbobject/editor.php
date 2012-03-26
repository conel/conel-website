<?PHP
## ======================================================================= 
##   webmatrix                                                     
## ======================================================================= 
##   Copyright (c) 2001, 2002, 2003 Stefan Elsner, Workmatrix        
## ======================================================================= 
##   <info@workmatrix.de> 
##
## ======================================================================= 

#################################
#     Required Include Files    #
#################################
require("../../config.php");

if(REWRITE_GLOBALS == "ON") {
	include("../../functions/register_globals.php");
}

## include the template class
require("../../".CLASSES_DIR."template.php");
  
## include the db class
require("../../".CLASSES_DIR."db_mysql.php");


require("../../".CLASSES_DIR."container.php");
require("../../".CLASSES_DIR."session.php");
require("../../".CLASSES_DIR."authentication.php");
require("../../".CLASSES_DIR."page.php");

## the xmlparser
require("../../".CLASSES_DIR."xmlparser.php");

require("../../".CLASSES_DIR."class_mailer.php");

## matrix_functions => general functions
require("../../functions/utilities.php");

page_open(array("session" => "session_object", "authenticate" => "Auth")); 
page_close();
include("../../interface/lang/".$Auth->auth["language"].".php");
include("interface/lang/".$Auth->auth["language"].".php");

include("subscribe.php");
include_once("utilities/page_selector.php");
include_once("../../functions/structure.php");
include("subscribe_editor.php");

if(isset($_POST['op'])) {
	$op = $_POST['op'];
} else {
	$op = $_GET['op'];
}

####################################################################################
#    The SWITCH:                                                                   #
#       This switch statement takes the arguement $op passed from an admin page    #
#       and decides which functions to call based on that variable.                #
####################################################################################  
switch($op) {            
	case "add":
		## we need the following vars- we'll get them via GET
		## because this is called from the main form
		$linklistID = $_GET['linklistID'];
		$page_id	= $_GET['page_id'];
		$identifier	= $_GET['identifier'];
		$linkItemID	= $_GET['linkItemID'];

		subscribe_displayInputForm($linkItemID,$linklistID,$page_id,$identifier);
		break;

	case "store":
		## store the selected item
		if($HTTP_POST_VARS['page_id'] != $HTTP_POST_VARS['selected_page']) {
			subscribe_storeItem();
		}
		## and then close the window
		subscribe_close_reload("");
		break;	
				
	case "delete":
		## is called via the main form
		$pageID 	= $_GET['page_id'];
		$identifier	= $_GET['identifier'];
		$itemID 	= $_GET['item_id'];

		## we need to generate the right urls
		$yesURL = "editor.php?op=doDelete&page_id=".$pageID."&identifier=".$identifier."&item_id=".$itemID;
		$yesURL = $gSession->url($yesURL);
		
		$noURL = "editor.php?op=closeEditor";
		$noURL = $gSession->url($noURL);		
		
		subscribe_promptDelete($yesURL,$noURL);		
		break;

	case "doDelete":
		$pageID 	= $_GET['page_id'];
		$identifier	= $_GET['identifier'];
		$itemID 	= $_GET['item_id'];
		
		subscribe_deletItem($pageID, $identifier, $itemID);
		subscribe_close_reload("");
		break;
					
	case "closeEditor":
		subscribe_close_reload("");
		break;	      

    default:
      	subscribe_close_reload("");
      	break;
    }
    
## =======================================================================        
##  close_reload        
## =======================================================================        
##  closes the current window and updates the parent window   
##
##  TODO:
## =======================================================================        
function subscribe_close_reload($targetURL) {
	global $gSession;
	## prepare the template file
	$select_template = new Template(ENGINE."datatypes/extra_subscribe/interface/");
	$select_template->set_templatefile(array("body" => "closesubmit.tpl"));
	
	$targetURL = $gSession->url($targetURL);
	
	$select_template->set_var('targetURL',$targetURL);
	$select_template->pfill_block("body");
}?>
