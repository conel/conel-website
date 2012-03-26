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
$current_tab 	= isset($_GET['categoryID']) ? $_GET['categoryID'] : $_POST['categoryID'];
$source 		= isset($_GET['source']) ? $_GET['source'] : $_POST['source'];

## okay we need to include the correct settingsfile for source dataobject that called us
include("../../../".$source."/settings.php");


####################################################################################
#    The SWITCH:                                                                   #
#       This switch statement takes the arguement $op passed from an admin page    #
#       and decides which functions to call based on that variable.                #
####################################################################################  
switch($op) {            
	case "create":
		clients_showAttributeList($_GET['attribute']);
		break;
	case "add":
		if(isset($_GET['attribute_id']) && !empty($_GET['attribute_id'])) {
			$attribute_id = $_GET['attribute_id'];
		} else {
			$attribute_id = null;
		}
		
		clients_addAttribute($_GET['attribute'],$attribute_id);
		break;
			
	case "save":
		## store the selected item
		clients_storeAttribute();
		## finnally redisplay the overview page
		clients_showAttributeList($_POST['attribute']);
		break;	
				
	case "delete":
		## is called via the main form
		$items_to_delete = $_POST['rows_to_delete'];
		$identifier =  $_POST['identifier'];
		clients_selectboxPromptDeleteValues(1,1,1,$items_to_delete,$identifier);
		break;

	case "doDelete":
		$items_to_delete = $_POST['rows_to_delete'];
		$identifier =  $_POST['identifier'];

		## first we drop the element
		clients_deleteAttribute($items_to_delete,$identifier);
		
		## redisplay the listing			
		clients_showAttributeList($identifier);
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
##  clients_selectboxPromptDeleteValues        
## =======================================================================        
##  display a confirmation page that the current
##  action was completed succesfully
##
##  TODO:
##  
## ======================================================================= 
function clients_selectboxPromptDeleteValues($title,$message,$target,$items,$identifier) {
	global $gSession,$Auth;
	## prepare the template file
	$select_template = new Template('interface');
	$select_template->set_templatefile(array("body" => "deletelink.tpl"));
	
	$select_template->set_var("yesIMG","lang/".$Auth->auth["language"]."_button_ja.gif");
	$select_template->set_var("noIMG","lang/".$Auth->auth["language"]."_button_nein.gif");
	$select_template->set_var('language_deletepage',LANG_MODULE_CLIENTS_AttributeDeleteTitle);
	$select_template->set_var('language_doyouwant',LANG_MODULE_CLIENTS_AttributeDeleteDesc);
	
	## grab the information for this page
	$noURL = "editor.php";
	$noURL = $gSession->url($noURL);
	$select_template->set_var('noURL',$noURL);
	$select_template->set_var('actionURL',$noURL);
	
	## now prepare the hiddden fields for deletion
	$output = '';
	foreach($items as $current_item) {
		$output .=  '<input type="hidden" name="rows_to_delete[]" value="'.$current_item.'">';
	}	
	
	## finally the command and the session
	$output .=  '<input type="hidden" name="op" value="doDelete">';
	$output .=  '<input type="hidden" name="identifier" value="'.$identifier.'">';
	$output .=  '<input type="hidden" name="Session" value="'.$gSession->id.'">';
	$output .= 	'<input type="hidden" name="source" value="'.$GLOBALS['_MODULE_DATAOBJECTS_NAME'].'">';
	
	$select_template->set_var('items',$output);
	
	$select_template->pfill_block("body");
}

## =======================================================================        
##  clients_addAttribute      
## =======================================================================        
##  displays the list of portlets- should later forward directly to
##  the input form  if there is only one template
##   
## =======================================================================
function clients_addAttribute($table_identifier,$attributeid=null) {
	global $Auth,$gSession;

	## get the template for now
	$select_template = new Template('interface/');
	$select_template->set_templatefile(array("header" => "add_attribute.tpl","body" => "add_attribute.tpl","footer" => "add_attribute.tpl"));

	## first check if we got a attribute-id
	if(isset($attributeid)) {
		## okay we need to edit the supplied attribute
		$db_connection = new DB_Sql();
		$query = "SELECT text FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_".$table_identifier." WHERE id='".$attributeid."'";
		$result_pointer = $db_connection->query($query);
		$db_connection->next_record();
		
		$attribute_value = $db_connection->Record["text"];
		$select_template->set_var('value',$attribute_value);
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
	
	## output the lang
	$select_template->set_var('LANG',$Auth->auth["language"]);
			
	## here we initialize the hiddenfields	
	$output =  '<input type="hidden" name="op" value="save">';
	$output .= '<input type="hidden" name="attribute" value="'.$table_identifier.'">';
	$output .= '<input type="hidden" name="attribute_id" value="'.$attributeid.'">';
	$output .= '<input type="hidden" name="Session" value="'.$gSession->id.'">';
	$output .= 	'<input type="hidden" name="source" value="'.$GLOBALS['_MODULE_DATAOBJECTS_NAME'].'">';
	$select_template->set_var("hiddenfields",$output);
	
	## and finally flush the footer
	$select_template->pfill_block("header");
	$select_template->pfill_block("body");
	$select_template->pfill_block("footer");

	
	## finally we return the positive value
	return $return_value;
}


## =======================================================================        
##  portlets_displayTemplates      
## =======================================================================        
##  displays the list of portlets- should later forward directly to
##  the input form  if there is only one template
##   
## =======================================================================
function clients_showAttributeList($table_identifier) {
	global $Auth, $gSession;

	## return value
	$returnvalue = -1;

	## multiclient
	$client_id = $Auth->auth["client_id"];

	$editURL = "editor.php?op=add&attribute=".$table_identifier."&source=".$GLOBALS['_MODULE_DATAOBJECTS_NAME'];
	$editURL = $gSession->url($editURL);
	
	
	## get all elements from the db
	$db_connection = new DB_Sql();
	$query = "SELECT * FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_".$table_identifier;
	$result_pointer = $db_connection->query($query);

	$return_value = array();
	$elements = '';
	while($db_connection->next_record()) {
		$return_value[$db_connection->Record["id"]] = $db_connection->Record["text"];
		
		
		$elements .= '<tr><td valign="top" colspan="7"><img src="../clients/interface/images/blank.gif" width="301" height="1"></td></tr><tr>
				<td align="left" valign="top" rowspan="2"><img src="../../../../interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
				<td width="16" align="center" valign="top"><input type="checkbox" name="rows_to_delete[]" value="'.$db_connection->Record["id"].'"></td>
				<td><img src="../clients/interface/images/blank.gif" width="2" height="1"></td>
				<td valign="top"><a href="'.$editURL.'&attribute_id='.$db_connection->Record["id"].'">&nbsp; '.$db_connection->Record["id"].'</a></td>
				<td><img src="../clients/interface/images/blank.gif" width="2" height="1"></td>
				<td valign="top"><a href="'.$editURL.'&attribute_id='.$db_connection->Record["id"].'">&nbsp; '.$db_connection->Record["text"].'</a></td>
			</tr>
			<tr><td valign="top" colspan="7" bgcolor="#DDDDDD"><img src="../../../../interface/images/blank.gif" width="301" height="1"></td></tr>	';
		
	}

	## get the template for now
	$select_template = new Template('interface/');
	$select_template->set_templatefile(array("header" => "attributelist.tpl","body" => "attributelist.tpl","footer" => "attributelist.tpl"));

	## this is for storing and deleting a new value
	$actionURL = "editor.php";
	$actionURL = $gSession->url($actionURL);
	$select_template->set_var('actionURL',$actionURL);
	
	## this is for storing and deleting a new value
	$addURL = "editor.php?op=add&attribute=".$table_identifier."&source=".$GLOBALS['_MODULE_DATAOBJECTS_NAME'];
	$addURL = $gSession->url($addURL);
	$select_template->set_var('addURL',$addURL);	
	
	## prepare the language stuff
	$select_template->set_var('language_header',LANG_MODULE_CLIENTS_AttributeTitle);
	$select_template->set_var('language_description',LANG_MODULE_CLIENTS_AttributeDesc);

	$select_template->set_var('IDSection',LANG_MODULE_CLIENTS_AttributeIDSection);
	$select_template->set_var('ValueSection',LANG_MODULE_CLIENTS_AttributeValueSection);
		
	## output the lang
	$select_template->set_var('LANG',$Auth->auth["language"]);
	
	## output the current values
	$select_template->set_var('elements',$elements);

	## here we initialize the hiddenfields_delte	
	$output =  '<input type="hidden" name="op" value="delete">';
	$output .=  '<input type="hidden" name="identifier" value="'.$table_identifier.'">';
	$output .= '<input type="hidden" name="Session" value="'.$gSession->id.'">';
	$output .= 	'<input type="hidden" name="source" value="'.$GLOBALS['_MODULE_DATAOBJECTS_NAME'].'">';
	$select_template->set_var("hiddenfields_delete",$output);
	
	## and finally flush the footer
	$select_template->pfill_block("header");
	$select_template->pfill_block("body");
	$select_template->pfill_block("footer");

	
	## finally we return the positive value
	return $return_value;
}

## =======================================================================        
##  clients_deleteAttribute      
## =======================================================================        
##  deletes the attribute and all links to taht attribute
##
## =======================================================================        
function clients_deleteAttribute($items,$identifier) {	
	## prepare the db-object
	$db_connectionStore = new DB_Sql();

	foreach($items as $current_element) {	
		## insert the new value
		$query = "DELETE FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_".$identifier." WHERE id='".$current_element."'";
		$result_pointer = $db_connectionStore->query($query);	
		
		## remove everything from the connector
		$query = "DELETE FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."2".$identifier." WHERE item_id='".$current_element."'";
		$result_pointer = $db_connectionStore->query($query);		
	}
}

## =======================================================================        
##  clients_storeAttribute      
## =======================================================================        
##  stores a new Attribute Value
##
## =======================================================================        
function clients_storeAttribute() {	
	## prepare the input 
	$table_identifier = $_POST['attribute'];
	$value = $_POST['newItem'];

	## prepare the db-object
	$db_connectionStore = new DB_Sql();
		
	if(isset($_POST['attribute_id']) && !empty($_POST['attribute_id'])) {
		$query = "UPDATE ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_".$table_identifier." SET text= '".$value."' WHERE id='".$_POST['attribute_id']."'";
	} else {	
		## insert the new value
		$query = "INSERT INTO ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_".$table_identifier." (text) VALUES ('$value')";
	}
	$result_pointer = $db_connectionStore->query($query);	
}
?>
