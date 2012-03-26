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
$element_id 	= isset($_GET['element_id']) ? intval($_GET['element_id']) : 0;
$object 		= isset($_GET['object']) ? intval($_GET['object']) : intval($_POST['object']);

## okay we need to include the correct settingsfile for source dataobject that called us
include("../../../".$source."/settings.php");


####################################################################################
#    The SWITCH:                                                                   #
#       This switch statement takes the arguement $op passed from an admin page    #
#       and decides which functions to call based on that variable.                #
####################################################################################  
switch($op) {            
	case "edit":
		## we handle add and edit in the same call
		$identifier = $_GET['attribute'];
			
		$data = array();
		
		if($element_id > 0) {
			## we need to get the data for this entry


			$db_connection = new DB_Sql(); 
			
			## try to fetch the required information
			$query = "SELECT * FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_".$identifier." WHERE id='$element_id' ORDER BY order_number DESC";
			$result_pointer = $db_connection->query($query);	
			$data = array();
			if($db_connection->next_record(MYSQL_ASSOC)) {
				$data = $db_connection->Record;
			}
		}
	
		element_displayInputForm($object,$identifier,$data);
		break;
			
	case "save":
		## store the selected item
		element_storeElement();
		## finnally close the pop-up window
		$actionURL = "../../../".$source ."/module.php?cmd=edit&client=".$object;
		$actionURL = $gSession->url($actionURL);

		close_reload($actionURL);
		break;	
				
	case "delete":
		## is called via the main form
		$items_to_delete = $_POST['rows_to_delete'];
		$identifier =  $_POST['identifier'];
		##clients_selectboxPromptDeleteValues(1,1,1,$items_to_delete,$identifier);
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
			
		##close_reload($actionURL);
		break;	      

    default:
		$actionURL = "module.php?tab=".$current_tab;
		$actionURL = $gSession->url($actionURL);
			
		##close_reload($actionURL);
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
##  element_displayInputForm      
## =======================================================================        
##  displays the list of portlets- should later forward directly to
##  the input form  if there is only one template
##   
## =======================================================================
function element_displayInputForm($object_id,$identifier,$data) {
	global $Auth, $gSession;

	## return value
	$returnvalue = -1;

	## get the template for now
	$select_template = new Template('interface/');
	$select_template->set_templatefile(array("header" => "edit_element.tpl","body" => "edit_element.tpl","footer" => "edit_element.tpl"));


	## first we prpeare the data- generate a thumbnail first
	if(isset($data['id'])) {
		## first we prepare the path
		$image_file = ABSOLUTE_UPLOAD_DIR.$GLOBALS['_MODULE_DATAOBJECTS_NAME'] .'/'.$identifier.'/'.$data['filename'];
		
		if($data['width'] > 0) {
			## now calculate the size
			$scale = 96/$data['width'];
			$width = (int) ($data['width']*$scale);
			$height = (int) ($data['height']*$scale);
			
			$image = '<img src="'.$image_file.'" width="'.$width.'" height="'.$height.'" border="0">';
			
			## output the image element
			$select_template->set_var('file',$image);
			$select_template->set_var('filename',$data['filename']);
		}
		## now output the caption
		
		$select_template->set_var('caption',$data['caption']);
	}


	$editURL = "editor.php?op=add&attribute=".$table_identifier."&source=".$GLOBALS['_MODULE_DATAOBJECTS_NAME'];
	$editURL = $gSession->url($editURL);


	## this is for storing and deleting a new value
	$actionURL = "editor.php";
	$actionURL = $gSession->url($actionURL);
	$select_template->set_var('actionURL',$actionURL);
	
	## this is for storing and deleting a new value
	$addURL = "editor.php?op=add&attribute=".$table_identifier."&source=".$GLOBALS['_MODULE_DATAOBJECTS_NAME'];
	$addURL = $gSession->url($addURL);
	$select_template->set_var('addURL',$addURL);	
	
	## prepare the language stuff
	$select_template->set_var("backIMG","lang/".$Auth->auth["language"]."_button_cancel.gif");
	$select_template->set_var("saveIMG","lang/".$Auth->auth["language"]."_button_save.gif");
	
	$select_template->set_var('language_header',LANG_MODULE_CLIENTS_AttributeTitle);
	$select_template->set_var('language_description',LANG_MODULE_CLIENTS_AttributeDesc);

	$select_template->set_var('IDSection',LANG_MODULE_CLIENTS_AttributeIDSection);
	$select_template->set_var('ValueSection',LANG_MODULE_CLIENTS_AttributeValueSection);
		
	## output the lang
	$select_template->set_var('LANG',$Auth->auth["language"]);
	
	
	## output the current values
	$select_template->set_var('elements',$elements);

	## here we initialize the hiddenfields_delte	
	$output =  '<input type="hidden" name="op" value="save">';
	$output .=  '<input type="hidden" name="attribute" value="'.$identifier.'">';
	$output .=  '<input type="hidden" name="element_id" value="'.$data['id'].'">';
	$output .=  '<input type="hidden" name="object" value="'.$object_id.'">';
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
function element_storeElement() {	
	## okay we need to store/update the element
	
	## prepare the data
	$identifier = $_POST['attribute'];
	$filename = '';

	$caption = mysql_real_escape_string($_POST['caption']);
	$element_id = intval($_POST['element_id']);
	$object_id = intval($_POST['object']);
	
	## now preare the image
	## we need to prepare the input - needs to be done properly
	$userfile	= $_FILES['image']['tmp_name'];
	$file_name	= $_FILES['image']['name'];
	$file_size	= $_FILES['image']['size'];
	$file_type	= $_FILES['image']['type'];
	
	## okay we first create an upload object
	$f = new file_object();  
	if (($userfile != "none") && ($userfile !="")) { 
		##then we upload the file
		$filename = $f->upload($userfile, $file_name,$file_size,$file_type, MATRIX_UPLOADDIR.'dataobjects/'.$identifier.'/');
	
		if($filename != -1) {
			$img_size = GetImageSize(MATRIX_UPLOADDIR.'dataobjects/'.$identifier.'/'.$filename);
		}
	}

	$db_connection = new DB_Sql();

	## first check if the entry already exists
	$select_query = "SELECT * FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_".$identifier." WHERE id='$element_id'";
	$result_pointer = $db_connection->query($select_query);
	$db_connection->next_record();
	
	$id = $db_connection->Record['id'];

	if($db_connection->num_rows() > 0) { 
		if($filename == '') {
			$update_query = "UPDATE ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_".$identifier." SET caption = '".$caption."' WHERE id = '$element_id'";
		} else {
			$update_query = "UPDATE ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_".$identifier." SET caption = '".$caption."', filename='".$filename."', width= '$img_size[0]',height='$img_size[1]' WHERE id = '$element_id'";
		}	
		$result_pointer = $db_connection->query($update_query);
	} else {
		## first we need to regsiter the image in your image_data table
		$query = "INSERT INTO ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_".$identifier." (element_id,caption,filename, width, height) values ('$object_id','".$caption."','".$filename."','$img_size[0]','$img_size[1]')";
		$result_pointer = $db_connection->query($query);
	}
	
}
?>
