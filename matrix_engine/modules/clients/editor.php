<?PHP
## ======================================================================= 
##   webmatrix                                                     
## ======================================================================= 
##   Copyright (c) 2001, 2002, 2003 Stefan Elsner, Workmatrix        
## ======================================================================= 
##   <info@workmatrix.de> 
##
## ======================================================================= 

## ======================================================================= 
## editor                                                  
## ======================================================================= 
## the editor is the main switchbox- for adding, deleting sorting etc.
## of the portlets. 
##
## ======================================================================= 
#################################
#     Required Include Files    #
#################################
require("../framework.php");

page_open(array("session" => "session_object", "authenticate" => "Auth")); 
page_close();
include("interface/lang/".$Auth->auth["language"].".php");
include("../../interface/lang/".$Auth->auth["language"].".php");

$op 			= isset($_POST['op']) ? $_POST['op'] : $_GET['op'];
$current_tab 	= isset($_GET['categoryID']) ? $_GET['categoryID'] : $_POST['categoryID'];

####################################################################################
#    The SWITCH:                                                                   #
#       This switch statement takes the arguement $op passed from an admin page    #
#       and decides which functions to call based on that variable.                #
####################################################################################  
switch($op) {            
	case "create":
		clients_addAttribute($_GET['attribute']);
		break;
	
	case "save":
		## store the selected item
		clients_storeAttribute();
		## finnally redisplay the overview page
		$actionURL = "module.php?cmd=edit".$current_tab;
		$actionURL = $gSession->url($actionURL);
		clients_closeSubmit();			
		break;	
				
	case "delete":
		## is called via the main form
		$pageID 	= $_GET['portletID'];

		## we need to generate the right urls
		$yesURL = "editor.php?op=doDelete&page_id=".$pageID."&categoryID=".$current_tab;
		$yesURL = $gSession->url($yesURL);
		
		$noURL = "editor.php?op=closeEditor&categoryID=".$current_tab;
		$noURL = $gSession->url($noURL);		
		
		portlets_promptDelete($yesURL,$noURL);		
		break;

	case "doDelete":
		$pageID 	= $_GET['page_id'];

		## first we drop the page
		page_deletePage($pageID);
				
		$actionURL = "module.php?tab=".$current_tab;
		$actionURL = $gSession->url($actionURL);
			
		close_reload($actionURL);
		break;				

	case "closeEditor":
		$actionURL = "module.php?tab=".$current_tab;
		$actionURL = $gSession->url($actionURL);
			
		close_reload($actionURL);
		break;	      

    default:
		$actionURL = "module.php?tab=".$current_tab;
		$actionURL = $gSession->url($actionURL);
			
		close_reload($actionURL);
      	break;
    }

## =======================================================================        
##  portlets_displayTemplates      
## =======================================================================        
##  displays the list of portlets- should later forward directly to
##  the input form  if there is only one template
##   
## =======================================================================
function clients_addAttribute($table_identifier) {
	global $Auth, $gSession;

	## return value
	$returnvalue = -1;

	## multiclient
	$client_id = $Auth->auth["client_id"];	

	## get the template for now
	$select_template = new Template('interface/');
	$select_template->set_templatefile(array("header" => "add_attribute.tpl","body" => "add_attribute.tpl","footer" => "add_attribute.tpl"));

	$actionURL = "editor.php";
	$actionURL = $gSession->url($actionURL);
	$select_template->set_var('actionURL',$actionURL);
	
	## prepare the language stuff
	$select_template->set_var("backIMG","lang/".$Auth->auth["language"]."_button_cancel.gif");
	$select_template->set_var("saveIMG","lang/".$Auth->auth["language"]."_button_save.gif");
	$select_template->set_var('language_header',LANG_MODULE_CLIENTS_AttributeTitle);
	$select_template->set_var('language_description',LANG_MODULE_CLIENTS_AttributeDesc);
	

	$select_template->pfill_block("header");
	$select_template->pfill_block("body");
		
	## here we initialize the hiddenfields	
	$output =  '<input type="hidden" name="op" value="save">';
	$output .= '<input type="hidden" name="attribute" value="'.$table_identifier.'">';
	$output .= '<input type="hidden" name="Session" value="'.$gSession->id.'">';
	$select_template->set_var("hiddenfields",$output);
	## and finally flush the footer
	$select_template->pfill_block("footer");
	
	## finally we return the positive value
	return $return_value;
}

## =======================================================================        
##  clients_storeSelectBox      
## =======================================================================        
##  store the selectbox entries (can be multiple)
##
## =======================================================================        
function clients_storeAttribute() {	
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	$table_identifier = $_POST['attribute'];
	$value = $_POST['newItem'];
		
	## prepare the db-object
	$db_connectionStore = new DB_Sql();
	
	## insert the new value
	$query = "insert into ".DB_PREFIX."clients_".$table_identifier." (text) values ('$value')";
	$result_pointer = $db_connectionStore->query($query);	
}

## =======================================================================        
##  portlets_promptDelete        
## =======================================================================        
function portlets_promptDelete($yesURL,$noURL) {
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
##  clients_closeSubmit        
## =======================================================================        
##  display a confirmation page that the current
##  action was completed succesfully
##
##  TODO:
##  
## ======================================================================= 
function clients_closeSubmit() {
	global $gSession,$Auth;
	## prepare the template file
	$select_template = new Template('interface');
	$select_template->set_templatefile(array("body" => "closesubmit.tpl"));
		
	$select_template->pfill_block("body");
}    
?>
