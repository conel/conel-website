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
require("../../".CLASSES_DIR."files.php");


require("../../".CLASSES_DIR."container.php");
require("../../".CLASSES_DIR."session.php");
require("../../".CLASSES_DIR."authentication.php");
require("../../".CLASSES_DIR."page.php");

## the xmlparser
require("../../".CLASSES_DIR."xmlparser.php");

require("../../".CLASSES_DIR."class_mailer.php");

include(ENGINE.'functions/language.php');

## matrix_functions => general functions
##require("../../functions/utilities.php");

page_open(array("session" => "session_object", "authenticate" => "Auth")); 
page_close();

## we need to load the language specific strings
include("interface/lang/".$Auth->auth["language"].".php");
include("../../interface/lang/".$Auth->auth["language"].".php");

include("image_editor.php");

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
	case "delete":
		## is called via the main form
		$pageID 	= $_GET['page_id'];
		$identifier	= $_GET['identifier'];

		## we need to generate the right urls
		$yesURL = "editor.php?op=doDelete&page_id=".$pageID."&language=".$input_language."&identifier=".$identifier;
		$yesURL = $gSession->url($yesURL);
		
		$noURL = "editor.php?op=closeEditor";
		$noURL = $gSession->url($noURL);		
		
		image_promptDelete($yesURL,$noURL);		
		break;

	case "doDelete":
		$pageID 	= $_GET['page_id'];
		$identifier	= $_GET['identifier'];
		$itemID 	= $_GET['item_id'];

		image_delete($pageID, $identifier);
		close_reload("");
		break;
    default:
      	close_reload("");
      	break;
    }
    
## =======================================================================        
##  close_reload        
## =======================================================================        
##  closes the current window and updates the parent window   
##
##  TODO:
## =======================================================================        
function close_reload($targetURL) {
	global $gSession;
	## prepare the template file
	$select_template = new Template(ENGINE."datatypes/image/interface");
	$select_template->set_templatefile(array("body" => "closesubmit.tpl"));
	
	$targetURL = $gSession->url($targetURL);
	
	$select_template->set_var('targetURL',$targetURL);
	$select_template->pfill_block("body");
}?>
