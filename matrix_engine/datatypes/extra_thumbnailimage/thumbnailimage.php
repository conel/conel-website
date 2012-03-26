<?PHP
## ======================================================================= 
##   webmatrix                                                     
## ======================================================================= 
##   Copyright (c) 2001, 2002, 2003 Stefan Elsner, Workmatrix        
## ======================================================================= 
##   <info@workmatrix.de> 
##
## =======================================================================        
## Version 1.2    (last changed: 26.4.2004)
##		- it's now possible to delete an image once it has been added
## =======================================================================        

include_once(ENGINE."functions/api_images.php");

## =======================================================================        
## image_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function thumbnailimage_displayInput($xmldata, $data) {
	return image_displayInput($xmldata, $data);
}


## =======================================================================        
##  image_storeData        
## =======================================================================        
## save the content in the db
## ======================================================================= 
function thumbnailimage_storeData($page_id, $identifier) {
	image_storeData($page_id, $identifier);
}
## =======================================================================        
##  image_getData        
## =======================================================================        
##  get the Data
## ======================================================================= 
function thumbnailimage_getData($vPageID,&$page_record) {
	global $Auth,$input_language;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## data connection
	$db_connectionMain = new DB_Sql();
	## get all text elements
	
	if(!isset($vPageID)) {
		return;
	}
	
	$select_query = "SELECT filename, identifier, width, height, alt FROM ".PAGE_IMAGE." INNER  JOIN ".PAGE_IMAGE."_data ON ".PAGE_IMAGE.".image_id = ".PAGE_IMAGE."_data.image_id WHERE page_id='$vPageID' AND ".PAGE_IMAGE.".client_id='$client_id' AND ".PAGE_IMAGE.".language='$input_language'";
	$result_pointer = $db_connectionMain->query($select_query);

	## loop through the results and set the vars in the template
	while($db_connectionMain->next_record()) {

		$filename = $db_connectionMain->Record["filename"];
		$varname = $db_connectionMain->Record["identifier"]; 
		$width = $db_connectionMain->Record["width"];
		$height = $db_connectionMain->Record["height"];
		$alt = $db_connectionMain->Record["alt"];
			
		$page_record[$varname]["type"] = "THUMBNAILIMAGE";
		$page_record[$varname]["filename"] = $filename; 
		$page_record[$varname]["width"] = $width; 
		$page_record[$varname]["height"] = $height;
		$page_record[$varname]["alt"] = $alt; 
		$page_record[$varname]["identifier"] = $varname; 
	}

}


## =======================================================================        
##  image_deleteData        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function thumbnailimage_deleteData($vPageID) {
	image_deleteData($vPageID);
}

## =======================================================================        
##  output_image        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function thumbnailimage_output($item,$structure,$menu_id) {
	if(!$item) {
		return "";
	}

	$value = array();
	
	## init filename
	$filename = "";
		
	## prepare the identifier
	$identifier = $item['identifier'];
		
	## the inital settings for this file
	$filename 	= $item['filename'];
	$scale 		= 1;
	$width 		= $item['width'];
	$height 	= $item['height'];	
	$alt 	= $item['alt'];	
	if(!$filename) {
		return '';						
	} 
	
	## basically we first have to resize the main image
	## to match the main size
	if(!$structure['WIDTH']) {
		if(!$structure['HEIGHT']) {
			$width = $item['width'];
			$height = $item['height'];
		} else {
			## width is not defined- but the height
			$scale = $structure['HEIGHT']/($item['height']+1);
		}
	} else {
		## okay the width is defined what about the height
		if(!$structure['HEIGHT']) {
			## this means we need to scale it to the width
			if($item['width'] == 0) {
				$item_width = 1;
			} else {
				$item_width = $item['width'];
			}
			$scale = $structure['WIDTH']/($item_width);
		} else {
			## both are defined- so we will assign them both
		$width = $structure['WIDTH'];
		$height = $structure['HEIGHT'];
		}
	}		
		
	
		
	## scale the stuff
	$width = (int) ($width*$scale);
	$height = (int) ($height*$scale);

	## here we do the file handling stuff
	$fileInfo= explode(".",$filename);

	if(!file_exists(MATRIX_UPLOADDIR.$fileInfo[0].'_'.$width.'_'.$height.'.'.$fileInfo[1])) {
		## we need to generate the image
		
		## load the base image
		$mainImage = API_images_loadImage(MATRIX_UPLOADDIR.$filename);
		## resize the image according to the setup
		
		$mainImage = API_images_resizeMaxPart($mainImage,$width,$height);	
		## save the image
		Imagejpeg($mainImage,MATRIX_UPLOADDIR.$fileInfo[0].'_'.$width.'_'.$height.'.'.$fileInfo[1]);
		ImageDestroy($mainImage);
	}

	## setup the return value
	$image = '<img src="/'.UPLOAD_DIR.$fileInfo[0].'_'.$width.'_'.$height.'.'.$fileInfo[1].'" alt="'.$alt.'" width="'.$width.'" height="'.$height.'" border="0" />';	
	
	$value[$identifier.'.width'] 	= $width;
	$value[$identifier.'.height'] 	= $height;
	$value[$identifier.'.file'] 	= '/'.UPLOAD_DIR.$fileInfo[0].'_'.$width.'_'.$height.'.'.$fileInfo[1];
	$value[$identifier.'.filename'] = $fileInfo[0].'_'.$width.'_'.$height.'.'.$fileInfo[1];
	$value[$identifier.'.sourcefile'] = '/'.UPLOAD_DIR.$filename;
	$value[$identifier] 			= $image;	
	$value[$identifier.'.alt'] 		= $alt;
	$value[$identifier.'.raw'] 		= '/'.UPLOAD_DIR.$filename;

	return $value;
}


## =======================================================================        
##  image_deleteData        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function thumbnailimage_copyData($source_id, $target_id) {
	image_copyData($source_id, $target_id);
}

?>