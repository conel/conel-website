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
	include("../../"."functions/register_globals.php");
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
##require("../../functions/utilities.php");

page_open(array("session" => "session_object", "authenticate" => "Auth")); 
page_close();

## we need to load the language specific strings
include("interface/lang/".$Auth->auth["language"].".php");
include("../../interface/lang/".$Auth->auth["language"].".php");

include_once("utilities/page_selector.php");
require(ENGINE."functions/structure.php");
require("../../functions/utilities.php");

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
		$workspace_id = $_GET['workspace_id'];
		pageselector_displayinput($workspace_id);
		break;

	case "store":
		## store the selected item
		workspace_storeItem();
		## and then close the window
		workspace_close_reload("");
		break;	
				
	case "delete":
		## is called via the main form
		$itemID 	= $_GET['item_id'];

		## we need to generate the right urls
		$yesURL = "workspace_editor.php?op=doDelete&workspace_item_id=".$itemID;
		$yesURL = $gSession->url($yesURL);
		
		$noURL = "workspace_editor.php?op=closeEditor";
		$noURL = $gSession->url($noURL);		
		
		workspace_promptDelete($yesURL,$noURL);		
		break;

	case "doDelete":
		$itemID 	= $_GET['workspace_item_id'];
		
		workspace_deletItem($itemID);
		workspace_close_reload("");
		break;
					

	case "closeEditor":
		workspace_close_reload("");
		break;	      

    default:
      	workspace_close_reload("");
      	break;
    }
    


## =======================================================================        
##  pageselector_displayinput      
## =======================================================================        
##  displays the input form for a certain entry  
##
## ======================================================================= 
function pageselector_displayinput($workspace_id) {	
	## first get the page structure
	$menuItems = structure_getStructure();
	$varsToBeSet["workspace_id"] = $workspace_id;
	$varsToBeSet["Title"] = LANG_WorkspaceSelectAPage;
	$varsToBeSet["Desc"] = LANG_WorkspaceSelectAPageDesc;

	$targetURL="workspace_editor.php?op=add&workspace_id=".$workspace_id;
	page_selector_drawMenu($menuItems,$varsToBeSet,$targetURL);
	
	## this is it- we are done
} 

## =======================================================================        
##  linklist_storeItem      
## =======================================================================        
##  store the specified icon
##  we need to check if there is a link listID already passed 
##
## ======================================================================= 
function workspace_storeItem() {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];	
	
	$item_id = $_POST['selected_page'];
	$workspace_id = $_POST['workspace_id'];

	$db_connection = new DB_Sql();  
	
	## store the item in the workspaces_container
	$lock_query = "LOCK TABLE ".DB_PREFIX."workspace_item write";
	$result_pointer = $db_connection->query($lock_query);	

	$query = "INSERT ".DB_PREFIX."workspace_item (workspace_id,workspace_item, client_id) values ('$workspace_id','$item_id','$client_id')";
	$result_pointer = $db_connection->query($query);	


	$lock_query = "UNLOCK table";
	$result_pointer = $db_connection->query($lock_query);
}

## =======================================================================        
##  workspace_promptDelete        
## =======================================================================        
function workspace_promptDelete($yesURL,$noURL) {
	global $gSession,$Auth;
	$db_connectionLayout = new DB_Sql();  

	## prepare the template file
	$select_template = new Template(ENGINE."datatypes/linklist/interface");
	$select_template->set_templatefile(array("body" => "deletelink.tpl"));
	
	$select_template->set_var("yesIMG","lang/".$Auth->auth["language"]."_button_ja.gif");
	$select_template->set_var("noIMG","lang/".$Auth->auth["language"]."_button_nein.gif");
	$select_template->set_var('language_deletepage',LANG_LINKLIST_DeleteTitle);
	$select_template->set_var('language_doyouwant',LANG_LINKLIST_DeleteDesc);
	
	## grab the information for this page
  		
	$select_template->set_var('yesURL',$yesURL);
	$select_template->set_var('noURL',$noURL);
	
	$select_template->pfill_block("body");
}	


## =======================================================================        
##  workspace_deletItem      
## =======================================================================        
##  store the specified icon
##  we need to check if there is a link listID already passed 
##
## ======================================================================= 
function workspace_deletItem($workspace_item_id) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];	

	$db_connection = new DB_Sql();  

	## let's delete the item
	$query = "DELETE FROM ".DB_PREFIX."workspace_item WHERE workspace_item_id='$workspace_item_id' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($query);
}

## =======================================================================        
##  close_reload        
## =======================================================================        
##  closes the current window and updates the parent window   
##
##  TODO:
## =======================================================================        
function workspace_close_reload($targetURL) {
	global $gSession;
	## prepare the template file
	$select_template = new Template("interface/");
	$select_template->set_templatefile(array("body" => "closesubmit.tpl"));
	
	$targetURL = $gSession->url($targetURL);
	
	$select_template->set_var('targetURL',$targetURL);
	$select_template->pfill_block("body");
}

?>
