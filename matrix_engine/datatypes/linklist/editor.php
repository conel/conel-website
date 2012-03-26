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
require("../../functions/language.php");
require("../../functions/page.php");
require('../../functions/access.php');
require("../../functions/utilities.php");

## the xmlparser
require("../../".CLASSES_DIR."xmlparser.php");

require("../../".CLASSES_DIR."class_mailer.php");

## matrix_functions => general functions
##require("../../functions/utilities.php");

page_open(array("session" => "session_object", "authenticate" => "Auth")); 
page_close();

## we need to load the language specific strings
include("interface/lang/".$Auth->auth["language"].".php");
include("../../interface/lang/".$Auth->auth["language"].".php");

include("linklist.php");
include_once("utilities/page_selector.php");
include_once("../../functions/structure.php");
include("linklist_editor.php");

## register the language
language_registerLanguage();

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
		$linkItemID	= isset($_GET['linkItemID']) ? intval($_GET['linkItemID']) : null;

		linklist_displayInputForm($linkItemID,$linklistID,$page_id,$identifier);
		break;

	case "store":
		## store the selected item
		linklist_storeItem();
		## and then close the window
		linklist_close_reload("");
		break;	
				
	case "delete":
		## is called via the main form
		$pageID 	= $_GET['page_id'];
		$identifier	= $_GET['identifier'];
		$itemID 	= $_GET['item_id'];

		## we need to generate the right urls
		$yesURL = "editor.php?op=doDelete&page_id=".$pageID."&identifier=".$identifier."&item_id=".$itemID."&language=".$input_language;
		$yesURL = $gSession->url($yesURL);
		
		$noURL = "editor.php?op=closeEditor";
		$noURL = $gSession->url($noURL);		
		
		linklist_promptDelete($yesURL,$noURL);		
		break;

	case "doDelete":
		$page_id 	= $_GET['page_id'];
		$identifier	= $_GET['identifier'];
		$itemID 	= $_GET['item_id'];
		
		linklist_deletItem($page_id, $identifier, $itemID);
		linklist_close_reload("");
		break;
					
	case "sort":
		## this is the beginning
		linklist_sort_displayInputForm();
		break;

	case "doSort":
		## do the actual sorting
		linklist_sort_setItemOrder($_POST['linklistID'],$_POST['linkID'],$_POST['order'], $_POST['move']);
		## we are done display the input form
		linklist_sort_displayInputForm();
		break;

	case "closeEditor":
		linklist_close_reload("");
		break;	      

    default:
      	linklist_close_reload("");
      	break;
    }
    
## =======================================================================        
##  close_reload        
## =======================================================================        
##  closes the current window and updates the parent window   
##
##  TODO:
## =======================================================================        
function linklist_close_reload($targetURL) {
	global $gSession;
	## prepare the template file
	$select_template = new Template(ENGINE."datatypes/linklist/interface");
	$select_template->set_templatefile(array("body" => "closesubmit.tpl"));
	
	$targetURL = $gSession->url($targetURL);
	
	$select_template->set_var('targetURL',$targetURL);
	$select_template->pfill_block("body");
}?>
