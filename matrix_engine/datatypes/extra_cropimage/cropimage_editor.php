<?PHP
## ======================================================================= 
##   webmatrix                                                     
## ======================================================================= 
##   Copyright (c) 2001, 2002, 2003 Stefan Elsner, Workmatrix        
## ======================================================================= 
##   <info@workmatrix.de> 
##
## ======================================================================= 
include_once(ENGINE."functions/api_images.php");

## =======================================================================        
##  cropimage_displayInputForm      
## =======================================================================        
##  displays the input form for a certain entry  
##
## ======================================================================= 
function cropimage_displayInputForm($vPageID,$vIdentifier,$error='') {	
	## we access the global pageID
	global $g_pageID;
	global $Auth,$gSession,$input_language;

	## init the vars
	$return = "";

	// get the master template for the popup page	
	$inputFile = "master_input.tpl";
	$input_template = new Template('interface/');
	$input_template->set_templatefile(array("head" => $inputFile,"intro" => $inputFile,"foot" => $inputFile));
	
	## language
	$input_template->set_var("saveIMG",$Auth->auth["language"]."_button_save.gif");
	$input_template->set_var('language_deleteelementdesc',LANG_DeleteElementDescription);
	$input_template->set_var('language_inputhead','Upload image');
	$input_template->set_var('language_inputbody','Select an image file from your hard drive. Uploading large images might take a while.');
	


	$actionURL = "editor.php";
	$actionURL = $gSession->url($actionURL);
	$input_template->set_var('actionURL',$actionURL);
		
	## here we initialize the hiddenfields	
	$output =  '<input type="hidden" name="op" value="upload">';
	$output .= '<input type="hidden" name="language" value="'.$input_language.'">';	
	$output .= '<input type="hidden" name="page_id" value="'.$vPageID.'">';
	$output .= '<input type="hidden" name="identifier" value="'.$vIdentifier.'">';
	$output .= '<input type="hidden" name="Session" value="'.$gSession->id.'">';
	$input_template->set_var("hiddenfields",$output);


	## set the datatype specific header elements
	$input_template->set_var('HEADER',$output['header']);

	## the next step is to ouput the head
	$input_template->pfill_block("head");
	$input_template->pfill_block("intro");	

	## we should open our own template
	$template = new Template(ENGINE."datatypes/extra_cropimage/interface/");
	$template->set_templatefile(array("image" => "upload_image.tpl","altimage" => "upload_image.tpl"));

	## set the vars
	$template->set_var('element_name',$vIdentifier);
	$template->set_var('element_desc',$xmldata['DESC']);
	$template->set_var('element_error',$error);

	if($xmldata['LANG'] == 'GLOBAL') {
		$template->set_var('language',-1);	
	} else {
		$template->set_var('language',$input_language);
	}
	
	## we got your record to process the data
	$filename = $data['filename'];
	
	## intput the delete link
	$deletelinkURL = SITE."datatypes/image/editor.php?op=delete&page_id=".$g_pageID."&language=".$input_language."&identifier=".$xmldata['NAME'];		
	$deletelinkURL = $gSession->url($deletelinkURL);
	$template->set_var('deletelinkItemURL',$deletelinkURL);
						
	## now it's time to check the previous data entered
	if($filename) {
		$img_size = @GetImageSize(MATRIX_UPLOADDIR.$filename);
					
		$longest_side = max($img_size[0],$img_size[1]);
		if($longest_side > 90) {
			$factor = 90 / $longest_side;
			$img_size[0] = ($img_size[0]*$factor);
			$img_size[1] = ($img_size[1]*$factor);
		}
		$image = '<img src="'.SITE_ROOT.UPLOAD_DIR.$filename .'" alt="'.$data['alt'].'" width="'.$img_size[0].'" height="'.$img_size[1].'">';								
		$template->set_var('file',$image);
	} else {
		$image = '<img src="interface/images/blank.gif" alt=" " width="1" height="1">';								
		$template->set_var('file',$image);
	}

	if(!$xmldata['TAG']) {
		$template->set_var('element_tag',LANG_ElementImage);
	} else {
		$template->set_var('element_tag',$xmldata['TAG']);
	}
	
	## prepare the alt tag element if it is set
	$alt = '';
	if($xmldata['SHOWALT']) {
		if(isset($data['alt'])) {
			$template->set_var('alt_value',$data['alt']);
		} else {
			$template->set_var('alt_value',$xmldata['ALT']);
		}
		$alt = $template->fill_block('altimage');
	}
	$template->set_var('ALT',$alt);
	
	$template->pfill_block("image");
	
	
	$input_template->pfill_block("foot");	
} 



## =======================================================================        
##  cropimage_displayInputForm      
## =======================================================================        
##  displays the input form for a certain entry  
##
## ======================================================================= 
function cropimage_displayCropper($page_id,$identifier,$width,$height,$thumb=0) {	
	## we access the global pageID
	global $g_pageID;
	global $Auth,$gSession,$input_language;

	//fetch the image
	$data = array();
	image_getData($page_id,$data);
	$filename = $data[$identifier]['filename'];
	
	## init the vars
	$return = "";


	// get the master template for the popup page	
	$inputFile = "crop_image.tpl";
	$input_template = new Template(ENGINE."datatypes/extra_cropimage/interface/");
	$input_template->set_templatefile(array("body" => $inputFile));
	
	// check if we are displaying a main image or a thumb
	$input_template->set_var("saveIMG",$Auth->auth["language"]."_button_save.gif");

	$output_width = ($width>0) ?  $width.' pixels wide': 'any width'; 
	$output_height = ($height>0) ?  $height.' pixels wide': 'any height'; 
		
	if($thumb == 0) {
		$input_template->set_var('language_deleteelementdesc',LANG_DeleteElementDescription);
		$input_template->set_var('language_inputhead','Main image');
		$input_template->set_var('language_inputbody','Choose which part of the image should be displayed as the main image.<br>Display image format: '.$output_width.', '.$output_height);
	} else {
		$input_template->set_var('language_inputhead','Thumbnail '.$thumb);
		$input_template->set_var('language_inputbody','Choose which part of the image should be displayed for the '.$thumb.'. thumbnail<br>Display image format: '.$output_width.', '.$output_height);
	}
	
	
	$input_template->set_var('WIDTH',$width);
	$input_template->set_var('HEIGHT',$height);
	$input_template->set_var('IMAGE',SITE_ROOT.UPLOAD_DIR.$filename);

	$actionURL = "editor.php";
	$actionURL = $gSession->url($actionURL);
	$input_template->set_var('actionURL',$actionURL);
		
	## here we initialize the hiddenfields	
	$output =  '<input type="hidden" name="op" value="crop">';
	$output .= '<input type="hidden" name="language" value="'.$input_language.'">';	
	$output .= '<input type="hidden" name="page_id" value="'.$page_id.'">';
	$output .= '<input type="hidden" name="thumbnail_id" value="'.$thumb.'">';
	$output .= '<input type="hidden" name="identifier" value="'.$identifier.'">';
	$output .= '<input type="hidden" name="Session" value="'.$gSession->id.'">';
	$input_template->set_var("hiddenfields",$output);

	$input_template->pfill_block("body");	
} 
	
## =======================================================================        
##  cropimage_storeItem      
## =======================================================================        
##  stores a link
##  there is now checking if the link has actually changed-  
##  since we only get called when a user does something- so we save
##	a new version everytime
## ======================================================================= 
function cropimage_DoCrop($page_id,$identifier,$target_width,$target_height) {
	global $Auth,$input_language;
	$client_id = $Auth->auth["client_id"];	


	// now prepare the target sizes
	$top = intval($_POST['top']);
	$left = intval($_POST['left']);
	$width = ceil($_POST['width']);
	$height = ceil($_POST['height']);
	$scale = $_POST['scalefactor'];
	
	// fetch the image
	$data = array();
	image_getData($page_id,$data);
	$image = $data[$identifier]['filename'];
	
	if(intval($target_width) == 0 && intval($target_height) == 0) {
		$t_width = ceil($width);
		$t_height = ceil($height);
	} else if(intval($target_width) == 0) {
		$t_height = ($target_height > 0) ? $target_height : ceil($height);
		$t_width = ($target_width > 0) ? $target_width : ceil($t_height/$height * $width);
	} else {
		$t_width = ($target_width > 0) ? $target_width : ceil($width);
		$t_height = ($target_height > 0) ? $target_height : ceil($t_width/$width * $height);
	}
			
	$fileInfo= explode(".",$image);

	$mainImage = API_images_loadImage(MATRIX_UPLOADDIR.$image);
	$mainImage = API_images_Crop($mainImage, $top,$left,$width,$height,$t_width,$t_height);	

	/* nkowald - 23/07/09 - It was saving images as jpegs by default, added check for filetype */
	// We need to work out if image is a gif, jpg or other
	$ext = substr($image, strrpos($image, '.') + 1);
	
	if ($ext == 'gif') {
		Imagegif($mainImage,MATRIX_UPLOADDIR.$fileInfo[0].'_'.$target_width.'_'.$target_height.'.'.$fileInfo[1]);
	} else {
		// if jpg or other
		Imagejpeg($mainImage,MATRIX_UPLOADDIR.$fileInfo[0].'_'.$target_width.'_'.$target_height.'.'.$fileInfo[1],85);
	}
	// nkowald -23/07/09
	
	ImageDestroy($mainImage);

}


## =======================================================================        
##  linklist_promptDelete        
## =======================================================================        
function cropimage_promptDelete($yesURL,$noURL) {
	global $gSession,$Auth;
	$db_connectionLayout = new DB_Sql();  

	## prepare the template file
	$select_template = new Template(ENGINE."datatypes/extra_cropimage/interface");
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
##  linklist_promptDelete        
## =======================================================================        
function cropimage_isSizeValid($page_id,$identifier,$image_data) {
	// laod the actual image
	$data = array();
	image_getData($page_id,$data);
	$filename = $data[$identifier]['filename'];

	// laod the images size
	$img_size = @GetImageSize(MATRIX_UPLOADDIR.$filename);
	//var_dump($img_size);
	//exit;
	
	// now prepare the sizes from the xml
	$widths[] = intval($image_data['WIDTH']);
	$heights[] = intval($image_data['HEIGHT']);
	
	$thumb_widths = explode(',',$image_data['THUMB_WIDTH']);
	$thumb_heights = explode(',',$image_data['THUMB_HEIGHT']);

	$widths = array_merge($widths,$thumb_widths);
	$heights = array_merge($heights,$thumb_heights);


	// now check the size of the image
	foreach($widths as $current_width) {
		if($img_size[0] < $current_width) {
			return false;
		}
	}

	foreach($heights as $current_height) {
		if($img_size[1] < $current_height) {
			return false;
		}
	}	

	return true;
}	


## =======================================================================        
##  image_doDelete        
## =======================================================================        
function cropimage_delete() {
	global $Auth,$input_language;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	$db = new DB_Sql();
	$f = new file_object();
	
	## we need a page id and an identifier
	$page_id = intval($_GET['page_id']);
	$identifier = $_GET['identifier'];
	
	## first get the image id for this image
	$query = "SELECT A.image_id,filename FROM ".PAGE_IMAGE." AS A INNER JOIN ".PAGE_IMAGE."_data AS B ON A.image_id=B.image_id WHERE A.page_id=".$page_id." AND A.identifier='".$identifier."'";
	$result_pointer = $db->query($query,true);
	
	if($db->next_record()) {
		$image_id = $db->Record['image_id'];
		$filename = $db->Record['filename'];

		## now check if this is the only usage of this image
		$query = "SELECT page_id FROM ".PAGE_IMAGE." WHERE image_id=".$image_id;
		$result_pointer = $db->query($query,true);

		if($db->num_rows() <=1) {
			## okay it's used only once- let's delete the base entry
			$query = "DELETE FROM ".PAGE_IMAGE."_data WHERE image_id=".$image_id;
			$result_pointer = $db->query($query,true);
			
			## okay this means we can delete the file itself too
			
			$f->delete_file(MATRIX_UPLOADDIR.$filename);
		}		
	}
	
	$query = "DELETE FROM ".PAGE_IMAGE." WHERE page_id='$page_id' AND identifier='$identifier' AND client_id='$client_id' AND language='$input_language'";
	$rp = $db->query($query);
}




?>
