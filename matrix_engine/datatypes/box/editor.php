<?php
## ======================================================================= 
##   webmatrix                                                     
## ======================================================================= 
##   Copyright (c) 2001, 2002, 2003 Stefan Elsner, Workmatrix        
## ======================================================================= 
##   <info@workmatrix.de> 
##
## ======================================================================= 

## the editor will need to be reworked completely... we can take 
## the portlets as a starting point... first we should check if
## a template was provided- the templates will be supplied via the xml
## file... if there are more then on... you need to seperate them using ','
## if there is only one template, we will display the input page for that template



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
require(ENGINE.CLASSES_DIR."files.php");
## the xmlparser
require("../../".CLASSES_DIR."xmlparser.php");

require("../../".CLASSES_DIR."class_mailer.php");
require("../../functions/template.php");
require("../../functions/utilities.php");
require("../../functions/page.php");

require('../../functions/access.php');
require("../../userpage.php");

include_once("../../datatypes/image/image.php");

include_once("../../datatypes/text/text.php");
include_once("../../datatypes/copytext/copytext.php");
include_once("../../datatypes/date/date.php");
include_once("../../datatypes/linklist/linklist.php");
include_once("../../datatypes/link/link.php");
include_once("../../datatypes/file/file.php");
require("../../functions/structure.php");	
include("functions/store.php");

include('../../functions/language.php');

page_open(array("session" => "session_object", "authenticate" => "Auth")); 
page_close();

## we need to load the language specific strings
include("interface/lang/".$Auth->auth["language"].".php");
include("../../interface/lang/".$Auth->auth["language"].".php");

include("box_editor.php");
include("box.php");

## register the language
language_registerLanguage();

$op = isset($_POST['op']) ? $_POST['op'] : $_GET['op'];

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
		
		$display_input = false;
				
		## check if we are editing a boxItem
		if($linkItemID > 0 && $linklistID > 0) {
			## okay we have a selected one- 
			$boxInfo = box_getBoxInfo($linklistID,$linkItemID);
			## we need to get the template id
			$box_page_id = $boxInfo['target'];
			$template_info = template_getTemplate($box_page_id);
			
			## finally we can display the input form
			box_displayInputForm($box_page_id,$template_info['template_id']);
			exit;
		}		
		$display_input = box_displayTemplates($page_id,$identifier,$_GET['basename']);

		break;
		
	case "editpage":
		## display the input form for this page
		$templateID = $_POST['templateID'];
		## then we create the box object if it doesn't already exists
		$box_id = box_storeBox($_POST['page_id'],$_POST['identifier']);	
		## okay we have a template, so we store the page		
		$page_id = box_storePage(LANG_NoName,$templateID);

		## we have the box_id and the page id, now we connect them
		box_structure_storePage($box_id,$page_id);
		
		## finally we display the input form
		box_displayInputForm($page_id,$templateID);
		break;
		
	case "store":
		## store the selected item
		page_storePage();
		box_close_reload("");
		break;	
	case "editor":
		## store the selected item
		page_storePage();
		$templateID = $_POST['templateID'];
		$page_id =$_POST['pageID'];
		box_displayInputForm($page_id,$templateID);
		break;			
	case "delete":
		
		## is called via the main form
		$pageID 	= $_GET['page_id'];
		$linklistID	= $_GET['linklistID'];
		$itemID 	= $_GET['item_id'];

		## we need to generate the right urls
		$yesURL = "editor.php?op=doDelete&page_id=".$pageID."&linklistID=".$linklistID."&item_id=".$itemID;
		$yesURL = $gSession->url($yesURL);
		
		$noURL = "editor.php?op=closeEditor";
		$noURL = $gSession->url($noURL);		
		
		box_promptDelete($yesURL,$noURL);		
		break;

	case "doDelete":
		$pageID 	= $_GET['page_id'];
		$linklistID	= $_GET['linklistID'];
		$itemID 	= $_GET['item_id'];
		
		## here we actually delete the item
		$target_page = box_deletItem($linklistID, $itemID);		
		page_deletePage($target_page);
		box_close_reload("");
		break;
					
	case "sort":
		## this is the beginning
		box_sort_displayInputForm();
		break;
	case "doSort":
		## do the actual sorting
		box_sort_setItemOrder($_POST['linklistID'],$_POST['linkID'],$_POST['order'], $_POST['move']);
		## we are done display the input form
		box_sort_displayInputForm();
		break;

	case "closeEditor":
		box_close_reload("");
		break;	      

    default:
      	box_close_reload("");
      	break;
    }
    
 ## =======================================================================        
##  close_reload        
## =======================================================================        
##  closes the current window and updates the parent window   
##
##  TODO:
## =======================================================================        
function box_close_reload($targetURL) {
	global $gSession;
	## prepare the template file
	$select_template = new Template(ENGINE."datatypes/box/interface/");
	$select_template->set_templatefile(array("body" => "closesubmit.tpl"));
	
	$targetURL = $gSession->url($targetURL);
	
	$select_template->set_var('targetURL',$targetURL);
	$select_template->pfill_block("body");
}
?>
