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


require("../../".CLASSES_DIR."container.php");
require("../../".CLASSES_DIR."session.php");
require("../../".CLASSES_DIR."authentication.php");
require("../../".CLASSES_DIR."page.php");
require("../../functions/language.php");
require("../../functions/page.php");
require("../../functions/template.php");
require('../../functions/access.php');
require("../../functions/utilities.php");

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
## include the db class
require("../../".CLASSES_DIR."files.php");

include_once("../image/image.php");
include("cropimage_editor.php");

## register the language
language_registerLanguage();

	// what every we do we will always require an action, page_id and identifier
	$op = isset($_POST['op']) ? $_POST['op'] : $_GET['op'];
	$page_id = isset($_GET['page_id']) ? $_GET['page_id'] : $_POST['page_id'];
	$identifier = isset($_GET['identifier']) ? $_GET['identifier'] : $_POST['identifier'];
	
	// now handle the main actions
	switch($op) {            
		case "add":
			// display the upload form
			cropimage_displayInputForm($page_id,$identifier);
			break;
		case "upload":
			// we need to upload the image just as a normal image uploader would do
			image_storeData($page_id,$identifier);
		
			// in order to crop the image we need to know the size
			$image_data = cropimage_loadXML($page_id,$identifier);
			
			// check the size of the image
			if(!cropimage_isSizeValid($page_id,$identifier,$image_data)) {
				// we need to delete the image first
				image_deleteData($page_id);
			
				cropimage_displayInputForm($page_id,$identifier,'The image you have uploaded is too small.<br> Please upload a larger image.');
				exit;
			}

			// now we need to open the first crop step
			cropimage_displayCropper($page_id,$identifier,intval($image_data['WIDTH']),intval($image_data['HEIGHT']));
			break;	
		case "crop":

			// in order to crop the image we need to know the size
			$image_data = cropimage_loadXML($page_id,$identifier);
			
			// we need to decide which element to process- default to the main image
			$thumbnail_id = isset($_GET['thumbnail_id']) ? $_GET['thumbnail_id'] : $_POST['thumbnail_id'];

			if(intval($thumbnail_id) == 0) {
				// default to the main image
				$width = intval($image_data['WIDTH']);
				$height = intval($image_data['HEIGHT']);
			
				// actually crop an image	
				cropimage_DoCrop($page_id, $identifier,$width,$height);
				
				
				// check if we need to do more scaling
				if(isset($image_data['THUMB_WIDTH'])) {
					$widths = explode(',',$image_data['THUMB_WIDTH']);
					$heights = explode(',',$image_data['THUMB_HEIGHT']); 

					// check if we have an entry
					if(isset($widths[$thumbnail_id])) {
						// display the second cropper
						cropimage_displayCropper($page_id,$identifier,intval($widths[$thumbnail_id]),intval($heights[$thumbnail_id]),$thumbnail_id+1);
						exit;
					}
				}				
			} else {
				// we have received the request to scale a thumbnail
				if(isset($image_data['THUMB_WIDTH'])) {
					$widths = explode(',',$image_data['THUMB_WIDTH']);
					$heights = explode(',',$image_data['THUMB_HEIGHT']); 
					
					// check if we have an entry
					if(isset($widths[$thumbnail_id-1])) {
						// actually crop an image	
						cropimage_DoCrop($page_id, $identifier,$widths[$thumbnail_id-1],$heights[$thumbnail_id-1]);
						
						// check if we have another thumbnail to scale
						if(isset($widths[$thumbnail_id])) {
							// display the second cropper
							cropimage_displayCropper($page_id,$identifier,intval($widths[$thumbnail_id]),intval($heights[$thumbnail_id]),$thumbnail_id+1);
							exit;
						}
					}
				}
			}
			
			// okay if we are still here twe assume that cropping took place- we need to close the window
			cropimage_close_reload("");
			break;


	case "delete":
		## is called via the main form
		$pageID 	= $_GET['page_id'];
		$identifier	= $_GET['identifier'];

		## we need to generate the right urls
		$yesURL = "editor.php?op=doDelete&page_id=".$pageID."&language=".$input_language."&identifier=".$identifier;
		$yesURL = $gSession->url($yesURL);
		
		$noURL = "editor.php?op=closeEditor";
		$noURL = $gSession->url($noURL);		
		
		cropimage_promptDelete($yesURL,$noURL);		
		break;

	case "doDelete":
		$pageID 	= $_GET['page_id'];
		$identifier	= $_GET['identifier'];
		$itemID 	= $_GET['item_id'];

		cropimage_delete($pageID, $identifier);
		close_reload("");
		break;

	case "closeEditor":
		cropimage_close_reload("");
		break;	      

    default:
      	cropimage_close_reload("");
      	break;
    }
   
   
function cropimage_loadXML($page_id,$identifier) {
	// load the template for this page
	$templateInfo = template_getTemplate($page_id);

	$xmlFile = $templateInfo['basename'].'.xml';
	$wt = new xmlparser(HTML_DIR.$xmlFile);
	$wt->parse();		
	$elements 	= $wt->getElements();	
	
	// find the element
	foreach($elements as $current_element) {
		if($current_element['NAME'] == $identifier) {
			return $current_element;
		}
	}
}   
   
## =======================================================================        
##  close_reload        
## =======================================================================        
##  closes the current window and updates the parent window   
##
##  TODO:
## =======================================================================        
function cropimage_close_reload($targetURL) {
	global $gSession;
	## prepare the template file
	$select_template = new Template(ENGINE."datatypes/extra_linkback/interface");
	$select_template->set_templatefile(array("body" => "closesubmit.tpl"));
	
	$targetURL = $gSession->url($targetURL);
	
	$select_template->set_var('targetURL',$targetURL);
	$select_template->pfill_block("body");
}?>
