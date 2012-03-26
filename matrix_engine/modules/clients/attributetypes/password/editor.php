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
require("../../../../config.php");

## include the template class
require("../../../../".CLASSES_DIR."template.php");
  
## include the db class
require("../../../../".CLASSES_DIR."db_mysql.php");

require(ENGINE.CLASSES_DIR."files.php");

require("../../../../".CLASSES_DIR."container.php");
require("../../../../".CLASSES_DIR."session.php");
require("../../../../".CLASSES_DIR."authentication.php");
require("../../../../".CLASSES_DIR."page.php");

## the xmlparser
require("../../../../".CLASSES_DIR."xmlparser.php");

require("../../../../".CLASSES_DIR."class_mailer.php");

## matrix_functions => general functions
require("../../../../functions/template.php");
require("../../../../functions/utilities.php");
require("../../../../functions/page.php");
require("../../../../functions/access.php");

page_open(array("session" => "session_object", "authenticate" => "Auth")); 
page_close();
include("../../interface/lang/".$Auth->auth["language"].".php");
include("../../../../interface/lang/".$Auth->auth["language"].".php");
include("interface/lang/".$Auth->auth["language"].".php");

include('../../functions/elements.php');


$op 			= isset($_POST['op']) ? $_POST['op'] : $_GET['op'];
$attribute 		= isset($_GET['attribute']) ? $_GET['attribute'] : $_POST['attribute'];
$element_id 	= isset($_GET['element_id']) ? intval($_GET['element_id']) : intval($_POST['element_id']);
$source 		= isset($_GET['source']) ? $_GET['source'] : $_POST['source'];

## okay we need to include the correct settingsfile for source dataobject that called us
include("../../../".$source."/settings.php");

include_once("../../../clients/attributetypes/password/attribute.php");

####################################################################################
#    The SWITCH:                                                                   #
#       This switch statement takes the arguement $op passed from an admin page    #
#       and decides which functions to call based on that variable.                #
####################################################################################  
switch($op) {            
	case "edit":
		clients_showAttribute($attribute,$element_id);
		break;			
	case "save":
		## store the selected item
		clients_storeAttribute();
		## finally close the window
		$actionURL = "../../../".$source."module.php?cmd=edit&client=".$element_id;
		$actionURL = $gSession->url($actionURL);
			
		clients_close($actionURL);
		break;	
	case "closeEditor":
		$actionURL = "../../../".$source."module.php?cmd=edit&client=".$element_id;
		$actionURL = $gSession->url($actionURL);
			
		clients_close($actionURL);
		break;	      
    default:
		$actionURL = "../../../".$source."module.php?cmd=edit&client=".$element_id;
		$actionURL = $gSession->url($actionURL);
			
		clients_close($actionURL);
      	break;
    }

## =======================================================================        
##  clients_showAttribute      
## =======================================================================        
##  displays the list of portlets- should later forward directly to
##  the input form  if there is only one template
##   
## =======================================================================
function clients_close() {
	global $Auth,$gSession;

	## get the template for now
	$select_template = new Template('interface/');
	$select_template->set_templatefile(array("body" => "closesubmit.tpl"));
	
	## and finally flush the footer
	$select_template->pfill_block("body");
}


## =======================================================================        
##  clients_showAttribute      
## =======================================================================        
##  displays the list of portlets- should later forward directly to
##  the input form  if there is only one template
##   
## =======================================================================
function clients_showAttribute($identifier,$element_id) {
	global $Auth,$gSession;

	## get the template for now
	$select_template = new Template('interface/');
	$select_template->set_templatefile(array("body" => "add_attribute.tpl"));

	## get the current data
	$current_password = '';
	
	$db_connectionStore = new DB_Sql();
	$query = "SELECT ".$identifier." FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." WHERE id='".$element_id."'";	
	$result_pointer = $db_connectionStore->query($query);
	if($db_connectionStore->next_record(MYSQL_ASSOC)) {
		$current_password = $db_connectionStore->Record[$identifier];
		
	}
	
	## this is for storing and deleting a new value
	$actionURL = "editor.php";
	$actionURL = $gSession->url($actionURL);
	$select_template->set_var('actionURL',$actionURL);
	
	## prepare the language stuff
	$select_template->set_var("backIMG","lang/".$Auth->auth["language"]."_button_cancel.gif");
	$select_template->set_var("saveIMG","lang/".$Auth->auth["language"]."_button_save.gif");
	$select_template->set_var('language_header',LANG_MODULE_CLIENTS_AttributeTitle);
	$select_template->set_var('language_description',LANG_MODULE_CLIENTS_AttributeDesc);
	$select_template->set_var('NewEntry',LANG_MODULE_CLIENTS_AttributeNewEntry);
	$select_template->set_var('CurrentEntry',LANG_MODULE_CLIENTS_AttributeCurrentEntry);
	
	$select_template->set_var('currentValue',$current_password);
	$select_template->set_var('newValue',$current_password);
	## output the lang
	$select_template->set_var('LANG',$Auth->auth["language"]);
			
	## here we initialize the hiddenfields	
	$output =  '<input type="hidden" name="op" value="save">';
	$output .= '<input type="hidden" name="attribute" value="'.$identifier.'">';
	$output .= '<input type="hidden" name="element_id" value="'.$element_id.'">';
	$output .= '<input type="hidden" name="Session" value="'.$gSession->id.'">';
	$output .= 	'<input type="hidden" name="source" value="'.$GLOBALS['_MODULE_DATAOBJECTS_NAME'].'">';
	$select_template->set_var("hiddenfields",$output);
	
	## and finally flush the footer
	$select_template->pfill_block("body");

	## finally we return the positive value
	return $return_value;
}

## =======================================================================        
##  clients_storeAttribute      
## =======================================================================        
##  stores a new Attribute Value
##
## =======================================================================        
function clients_storeAttribute() {	
	## prepare the input 
	$identifier = $_POST['attribute'];
	$value = $_POST['newValue'];
	$element_id = intval($_POST['element_id']);

	## prepare the db-object
	$db_connectionStore = new DB_Sql();
	if($element_id > 0) {
		$query = "UPDATE ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." SET ".$identifier."= '".$value."' WHERE id='".$element_id."'";
		$result_pointer = $db_connectionStore->query($query);
	}
}
?>
