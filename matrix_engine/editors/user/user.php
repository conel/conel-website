<?PHP
require("../config.php");
require(ENGINE."functions/structure.php");

## include the group functions
require('functions/group.php');
require('functions/user.php');
require('functions/_getworkspaces.php');

global $allowed_group;

$allowed_group = 0;

	## check if we have the correct access rights
	$access_rights = _getUserAccessRights($Auth->auth['user_id']);
	if($access_rights['users']['edit_own']['access'] != 1) {
		if ($access_rights['users']['edit_all']['access'] != 1) {
			## display the error message
			ui_output_error("<b>".LANG_UserAdministration."</b><br><br> ".LANG_NoAccessRights);
			exit;
		}
	} else {
		## we are only allowed to edit our own group
		$allowed_group =  $access_rights['group_id'];
	}	

include("interface/lang/".$Auth->auth["language"].".php");
$op = isset($_GET['op']) ? $_GET['op'] : (isset($_POST['op']) ? $_POST['op']: '');

####################################################################################
#    The SWITCH:                                                                   #
#       This switch statement takes the arguement $op passed from an admin page    #
#       and decides which functions to call based on that variable.                #
####################################################################################  
switch($op) {            
	case "new_group":
		## is called in order to create a new user
		show_group_input($_GET['group_id']);
		break;
	case "edit_group":
		## is called in order to create a new user
		show_group_input($_GET['group_id']);
		break;			
	case "delete_group":
		## this is the starting point for a new page- 
		## we will display a list of templates available to the user
		delete_group($_GET['group_id']);
		output_confirm(LANG_UserDeleted, LANG_UserDeletedSuccess, "menu.php");
		break;		
	case "update_group":
		## this is the starting point for a new page- 
		## we will display a list of templates available to the user
		update_group($_POST['group_id']);
		output_confirm(LANG_GroupSaved, LANG_GroupSavedSuccess, "menu.php");
		break;		
	case "new_user":
		## is called in order to create a new user
		show_user_input($_GET['user_id'],$_GET['group_id']);
		break;
	case "edit_user":
		## is called in order to display a user inpout form
		show_user_input($_GET['user_id']);
		break;
	case "save_user":
		## this is the starting point for a new page- 
		## we will display a list of templates available to the user
		save_user();
		output_confirm(LANG_UserSaved, LANG_UserSavedSuccess, "menu.php");	
		break;	
	case "update_user":
		## this is the starting point for a new page- 
		## we will display a list of templates available to the user
		update_user($_POST['user_id']);
		output_confirm(LANG_UserChanged, LANG_UserChangedSuccess, "menu.php");
		break;	
	case "delete_user":
		## this is the starting point for a new page- 
		## we will display a list of templates available to the user
		delete_user($_GET['user_id']);
		output_confirm(LANG_UserDeleted, LANG_UserDeletedSuccess, "menu.php");
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
	$select_template->set_var('site',SITE);
	
	$targetURL = $target;
	$targetURL = $gSession->url($targetURL);
	$select_template->set_var('targetURL',$targetURL);
	
	$select_template->pfill_block("body");
}


?>
