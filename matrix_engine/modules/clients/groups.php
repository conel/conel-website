<?php
## =======================================================================        
##  setup.php        
## =======================================================================        
##  is a switchbox for all functions realted to the setup. The user needs
##  to be able to create new user groups (he assigns them a xml-controlfile)
##  wehn the user clicks on setup in the navigation he will get a list of
##  available groups. he can add, edit, delete and sort the groups
##  TODO:   
##     - check if it works    
## =======================================================================
require("../framework.php");
require("settings.php");
require("functions/setup.php");
require("functions/filters.php");
require("functions/groups.php");
require("functions/elements.php");
require("classes/ctlparser.php");

##require("../../functions/class_html_pager.php");

## include the attributetypes
require_once('attributetypes/selectbox/attribute.php');
require_once('attributetypes/selectmenu/attribute.php');
require_once('attributetypes/text/attribute.php');

## we need to load the language specific details
include("interface/lang/".$Auth->auth["language"].".php");

	## process the input vars
	$cmd = isset($_GET['cmd']) ? $_GET['cmd'] : (isset($_POST['cmd']) ? $_POST['cmd'] : '');
	
	## this is the main switchbox
	switch($cmd) {
		case "create":
			## the used decided to create a new group- so we will
			## display the group input form and 
			clients_groupDisplayOverview($id,$clientRecord);
			break;			
		case "store":
			## when the user wants to store the group info- we get called
			## we will go back to the setup page for now.
			clients_setupStoreGroup();
			## finnally redisplay the overview page
			displaySetup();
			break;		
		case "edit":
			## display the input form for this page
			## since we use this also for editing an entry we need to portlet id
			$filter = isset($_GET['filter']) ? intval($_GET['filter']): -1;

			## get the filter info	
			$filterRecord = clients_getFilter($filter);
			
			## now we display the message
			$actionURL = "groups.php?cmd=save&filter=".$filterRecord['id'];
			$actionURL = $gSession->url($actionURL);
			
			clients_displayInputName('want to save the filter?',$actionURL,$filterRecord['name']);
			break;						

		case "save":
			## okay the user typed inj a new name- so we update it here.
			$filter = intval($_GET['filter']);
			$new_name = addslashes($_POST['menu_text']);
			
			## now update the database 
			$db = new DB_Sql();
			$query = "UPDATE ".DB_PREFIX."clients_filters SET name='$new_name' WHERE id=".$filter;
			$result = $db->query($query);
			
			## finally we re-display the overview page
			displaySetup();
		
			break;
		case "delete":
			## we need to delete this group- but first we need to ask
			## the user if he wants to delete the group and all its users
			
			$items_to_delete = $_POST['rows_to_delete'];
			module_clients_output_confirm(LANG_MODULE_CLIENTS_DeleteFilterTitle,LANG_MODULE_CLIENTS_DeleteFilterDesc,1,$items_to_delete,'dodelete');
			break;
		case "dodelete":
			## now we can actually delete the clients
			$items_to_delete = $_POST['rows_to_delete'];

			clients_deleteFilters($items_to_delete);
			
			## redisplay the listing
			displaySetup();
			break;	
			
		case "toggleState":
			## we need to delete this group- but first we need to ask
			## the user if he wants to delete the group and all its users
			
			$items_to_delete = $_POST['rows_to_delete'];
			module_clients_output_confirm(LANG_MODULE_CLIENTS_DeleteFilterToggleTitle,LANG_MODULE_CLIENTS_DeleteFilterToggleDesc,1,$items_to_delete,'dotoggleState');
			break;
		case "dotoggleState":
			## now we can actually delete the clients
			$items_to_delete = $_POST['rows_to_delete'];

			foreach($items_to_delete as $current_filter) {
				clients_toggleFilterVisibility($current_filter);
			}
			
			## redisplay the listing
			displaySetup();
			break;
			
    	default:
			## we need to display the setup screen
			displaySetup();
      	break;
    }

## =======================================================================        
##  module_clients_output_confirm        
## =======================================================================        
##  display a confirmation page that the current
##  action was completed succesfully
##
##  TODO:
##  
## ======================================================================= 
function module_clients_output_confirm($title,$message,$target,$items,$cmd) {
	global $gSession,$Auth;
	## prepare the template file
	$select_template = new Template('interface');
	$select_template->set_templatefile(array("body" => "deletelink.tpl"));
	
	$select_template->set_var("yesIMG","lang/".$Auth->auth["language"]."_button_ja.gif");
	$select_template->set_var("noIMG","lang/".$Auth->auth["language"]."_button_nein.gif");
	$select_template->set_var('language_deletepage',$title);
	$select_template->set_var('language_doyouwant',$message);
	
	## grab the information for this page
	$noURL = "module.php";
	$noURL = $gSession->url($noURL);
	$select_template->set_var('noURL',$noURL);
	
	## now prepare the hiddden fields for deletion
	$output = '';
	foreach($items as $current_item) {
		$output .=  '<input type="hidden" name="rows_to_delete[]" value="'.$current_item.'">';
	}	
	
	## finally the command and the session
	$output .=  '<input type="hidden" name="cmd" value="'.$cmd.'">';
	$output .=  '<input type="hidden" name="Session" value="'.$gSession->id.'">';
	
	$select_template->set_var('items',$output);
	
	$select_template->pfill_block("body");
}

?>
