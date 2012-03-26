<?PHP
require("../config.php");


if(REWRITE_GLOBALS == "ON") {
	include(ENGINE."functions/register_globals.php");
}
require(ENGINE."functions/structure.php");

global $allowed_group;

$allowed_group = 0;

	## check if we have the correct access rights
	$access_rights = _getUserAccessRights($Auth->auth['user_id']);
	
	if($access_rights['users']['edit_owngroup']['access'] != 1) {
		if ($access_rights['users']['edit_all']['access'] != 1) {
			## display the error message
			ui_output_error("<b>".LANG_UserAdministration."</b><br><br> ".LANG_NoAccessRights);
			exit;
		}
	} else {
		## we are only allowed to edit our own group
		$allowed_group =  $access_rights['users']['edit_owngroup']['object'];
	}	

include("interface/lang/".$Auth->auth["language"].".php");
$op = isset($_GET['op']) ? $_GET['op'] : (isset($_POST['op']) ? $_POST['op']: '');

####################################################################################
#    The SWITCH:                                                                   #
#       This switch statement takes the arguement $op passed from an admin page    #
#       and decides which functions to call based on that variable.                #
####################################################################################  
switch($op) {            
	case "new_workspace":
		## we create a new empty workspace
		$workspace_id = save_workspace();
		show_workspace_input($workspace_id);
		break;
	case "edit_workspace":
		## is called in order to create a new user
		show_workspace_input($_GET['workspace_id']);
		break;		
	case "editor":
		## check if we got the editor command
		$cmd = isset($_POST['mode']) ? $_POST['mode']: '';
		if($cmd == 'save_workspace') {
			$workspace_id = save_workspace();
			## we are in edit mode, so we need to redisplay the input form	
			show_workspace_input($workspace_id);
		}
		break;	
				
	case "save_saveworkspace":
		## this is the starting point for a new page- 
		## we will display a list of templates available to the user
		save_workspace();
		output_confirm(LANG_WorkspaceSavedTitle, LANG_WorkspaceSavedTitleDesc, "menu.php");	
		break;	
	case "delete_workspace":
		## this is the starting point for a new page- 
		## we will display a list of templates available to the user
		
		delete_workspace($_GET['workspace_id']);
		output_confirm(LANG_WorkspaceDeletedTitle, LANG_WorkspaceDeletedTitleDesc, "menu.php");
		break;								
	default:
		## okay if we were called with no paramters 
		## we need to check if the user has the approriate
		## rights to edit the templates, if not we should be
		## displaying this but just in case
		ui_output_error("<b>".LANG_UserAdministration."</b><br>".LANG_UserDescription);
		break;
}

## =======================================================================        
##  show_workspace_input        
## =======================================================================        
##  shows the appropriate Input Form for a group
##
##  TODO:  
## =======================================================================
function show_workspace_input($workspace_id) {
	global $gSession,$Auth;


	$db_connection = new DB_Sql();
	
	## first we need to gather the required data
	if($workspace_id > 0) {
		## okay get the data from the db
		$query = 'SELECT * FROM '.DB_PREFIX.'workspaces WHERE workspace_id='.$workspace_id;
		$result_pointer = $db_connection->query($query);
		$db_connection->next_record();
		
		## get the name to output this.
		$name = $db_connection->Record["name"];
		
		## now we need to get all pages that are linked to this workspace
		$query = 'SELECT workspace_item_id,structure_text FROM '.DB_PREFIX.'workspace_item INNER JOIN '.STRUCTURE.' ON '.DB_PREFIX.'workspace_item.workspace_item='.STRUCTURE.'.page_id WHERE workspace_id='.$workspace_id;
		$result_pointer = $db_connection->query($query);
		
		$data = array();
		$items = 0;
		while($db_connection->next_record()) {
			$data[$items]["text"] = $db_connection->Record["structure_text"];
			$data[$items]["link"] = $db_connection->Record["workspace_item_id"];
			$items++;
		} 

	} else {
		## since we are creating a dummy entry before hand, we should not need
		## this case.
		return;
	}
		
	## okay let's use this file, as a template
	## prepare the template file- we changed to structure of the template file
	$inputFile = "workspace_input.tpl";
	$input_template = new Template("interface/");
	$input_template->set_templatefile(array("head" => $inputFile,"intro" => $inputFile,"text" => $inputFile,"foot" => $inputFile));

	## language
	$input_template->set_var("saveIMG","lang/".$Auth->auth["language"]."_button_save.gif");
	$input_template->set_var('language_inputhead',LANG_WorkspacePages);
	$input_template->set_var('language_inputbody',LANG_WorkspaceDesc);
	
	$actionURL = "workspace_entry.php";
	$actionURL = $gSession->url($actionURL);
	$input_template->set_var('actionURL',$actionURL);
		
	## the next step is to ouput the head
	$input_template->pfill_block("head");
	$input_template->pfill_block("intro");
	

	## here we initialize the hiddenfields	
	$output =  '<input type="hidden" name="op" value="save_saveworkspace">';
	$output .= '<input type="hidden" name="mode" value="save_workspace">';
	$output .= '<input type="hidden" name="workspaceID" value="'.$workspace_id.'">';
	$output .= '<input type="hidden" name="Session" value="'.$gSession->id.'">';
	$input_template->set_var("hiddenfields",$output);
	
	$input_template->set_var('element_tag',LANG_WorkspaceName);
	$input_template->set_var('element_name','workspace_name');
	$input_template->set_var('value',$name);
	$input_template->set_var('element_desc',LANG_WorkspaceNameDesc);
	$input_template->pfill_block("text");
	
	
	## we should open our own template
	$template = new Template("interface/");
	$template->set_templatefile(array("linklist" => "workspace_input.tpl","linklist_row" => "workspace_input.tpl","linklist_foot" => "workspace_input.tpl"));
	
	## set the vars
	$template->set_var('element_tag',LANG_WorkspacePages);
	$template->set_var('element_desc',$xmldata['DESC']);
		
	## prepare the vars
	$linklistID = $data['id'];
	$basename 	= $xmldata['TEMPLATE'];

	## prepare the url
	$addlinkURL = "workspace_editor.php?op=add&workspace_id=".$workspace_id;
	$addlinkURL = $gSession->url($addlinkURL);	
	
	$deletelinkURL = "workspace_editor.php?op=delete&workspace_id=".$workspace_id;		
	$deletelinkURL = $gSession->url($deletelinkURL);
		
	## set the vars
	$template->set_var('addlinkItemURL',$addlinkURL);	
	$template->set_var('deletelinkItemURL',$deletelinkURL);
	$template->set_var('sortURL',$sortURL);
	
	$return = $template->fill_block("linklist");
	
	## loop through all records
	for($i=0; $i< $items; $i++) {;
		## display the page title an the id number
						
		## so we can savely dsiplay the entry
		$decription = $data[$i]["text"];							
		$template->set_var('decription',$decription);
						
		$template->set_var('linkID',$data[$i]['link']);		
		$return .= $template->fill_block("linklist_row");
	}	
	$return .= $template->fill_block("linklist_foot");

	print $return;
	
	
	
	
	
	$input_template->pfill_block("foot");

	return;
}


## =======================================================================        
##  update_group        
## =======================================================================         
##  updates an existing group    
## =======================================================================
function update_group($group_id) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	$db_connection = new DB_Sql();
	
	$name = addslashes($_POST['user_name']);
	
	## first we will update the name
	$query = "UPDATE ".GROUPS." SET name='".$name."' WHERE group_id='$group_id'";
	$result_pointer = $db_connection->query($query);
	
	## then we need to setup the pages settings
	$add = $edit = $delete = isset($_POST['pages_add']) ? 1 : 0;

	$workspace = isset($_POST['user_workspaces']) ? $_POST['workspace'] : NULL;
	
	## okay we are ready to setup the pages rights
	$query = "UPDATE ".ACCESS." SET `add`=".$add.",`edit`='".$edit."',`delete`='".$delete."', sub_object='".$workspace."' WHERE group_id='$group_id' AND object='pages'";
	$result_pointer = $db_connection->query($query);

	## okay next: templates
	$add = $edit = $delete = isset($_POST['templates_add']) ? 1 : 0;
	
	$query = "UPDATE ".ACCESS." SET `add`=".$add.",`edit`='".$edit."',`delete`='".$delete."' WHERE group_id='$group_id' AND object='template'";
	$result_pointer = $db_connection->query($query);
	
	## usermanagement
	$add = $edit = $delete = isset($_POST['allgroup_edit']) ? 1 : 0;

	$query = "UPDATE ".ACCESS." SET `add`=".$add.",`edit`='".$edit."',`delete`='".$delete."' WHERE group_id='$group_id' AND object='users_allgroup'";
	$result_pointer = $db_connection->query($query);

	$add = $edit = $delete = isset($_POST['owngroup_edit']) ? 1 : 0;

	$query = "UPDATE ".ACCESS." SET `add`=".$add.",`edit`='".$edit."',`delete`='".$delete."' WHERE group_id='$group_id' AND object='users_owngroup'";
	$result_pointer = $db_connection->query($query);	

	return true;
}

## =======================================================================        
##  save_workspace        
## =======================================================================        
##  saves a workspace: if you supply a workspace_id (via _POST)
##  it'll update the workspace- otherwise it'll create a dummy entry
##     
## =======================================================================
function save_workspace() {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	$db_connection = new DB_Sql();
	
	
	## did we get a name?
	if(isset($_POST['workspace_name'])) {
		$name = addslashes($_POST['workspace_name']);
	} else {
		## let's generate a new name
		$query = 'SELECT max(workspace_id) AS last_element FROM '.DB_PREFIX.'workspaces WHERE 1';
		$result_pointer = $db_connection->query($query);
		$db_connection->next_record();
		
		$name = LANG_NoName.($db_connection->Record["last_element"]+1);
	}
	
	## okay now we need to check if we are updating an entry
	## or if we create a new one
	$lock_query = "LOCK TABLE ".DB_PREFIX."workspaces WRITE";
	$result_pointer = $db_connection->query($lock_query);
		
	if(isset($_POST['workspaceID'])) {
		## we are updating
		$query = "UPDATE ".DB_PREFIX."workspaces SET name='$name' WHERE workspace_id='".intval($_POST['workspaceID'])."'";
		$result_pointer = $db_connection->query($query);
		$workspace_id   = intval($_POST['workspaceID']);
	} else {
		## let's create a new entry
		$query = "INSERT INTO ".DB_PREFIX."workspaces (name,client_id) VALUES ('$name','$client_id')";
		$result_pointer = $db_connection->query($query);
		$workspace_id    = $db_connection->db_insertid($result_pointer);
	}
	
	$lock_query = "unlock table";
	$result_pointer = $db_connection->query($lock_query);
	
	return $workspace_id;
}


## =======================================================================        
##  delete_group        
## =======================================================================        
##  deletes the group and all its users
##
## =======================================================================
function delete_workspace($workspace_id) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## prepare the db-object
	$db_connection = new DB_Sql(); 

	## okay we delete the workspace-
	## should there be a warning when deleting a workspace that is used?
	$select_query = "DELETE FROM ".DB_PREFIX."workspaces WHERE workspace_id='$workspace_id' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($select_query);	

	$select_query = "DELETE FROM ".DB_PREFIX."workspace_item WHERE workspace_id='$workspace_id' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($select_query);			

}



## =======================================================================        
##  output_confirm        
## =======================================================================        
##  display a confirmation page that the current
##  action was completed succesfully
##
##  TODO:
##  
## ======================================================================= 
function output_confirm($title,$message,$target) {
	global $gSession;
	## prepare the template file
	$select_template = new Template(INTERFACE_DIR);
	$select_template->set_templatefile(array("body" => "dialog_confirm.tpl"));
	$select_template->set_var('title',$title);
	$select_template->set_var('message',$message);
	
	$targetURL = $target;
	$targetURL = $gSession->url($targetURL);
	$select_template->set_var('targetURL',$targetURL);
	
	$select_template->pfill_block("body");
}


?>
