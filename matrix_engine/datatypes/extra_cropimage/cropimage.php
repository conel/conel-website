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
## cropimage_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function cropimage_displayInput($xmldata, $data,$error='') {
	## we access the global pageID
	global $g_pageID,$Auth;
	global $gSession,$input_language;
	
	## init the vars
	$return = "";
		
	## we need to load the language specific strings
	include(ENGINE."datatypes/extra_cropimage/interface/lang/".$Auth->auth["language"].".php");

	
	## we should open our own template
	$template = new Template(ENGINE."datatypes/extra_cropimage/interface/");
	$template->set_templatefile(array("image" => "interface.tpl","altimage" => "interface.tpl"));
	
	## set the vars
	$template->set_var('element_name',$xmldata['NAME']);
	$template->set_var('element_desc',$xmldata['DESC']);
	$template->set_var('element_error',$error);

	if($xmldata['LANG'] == 'GLOBAL') {
		$template->set_var('language',-1);	
	} else {
		$template->set_var('language',$input_language);
	}
			
	## prepare the url
	$addlinkURL = SITE."datatypes/extra_cropimage/editor.php?op=add&page_id=".$g_pageID."&language=".$input_language."&identifier=".$xmldata['NAME'];
	$addlinkURL = $gSession->url($addlinkURL);	
	
	$deletelinkURL = SITE."datatypes/extra_cropimage/editor.php?op=delete&page_id=".$g_pageID."&language=".$input_language."&identifier=".$xmldata['NAME'];		
	$deletelinkURL = $gSession->url($deletelinkURL);
		
	## set the vars
	$template->set_var('addlinkItemURL',$addlinkURL);	
	$template->set_var('deletelinkItemURL',$deletelinkURL);

	// check if we have a image 
	$filename = $data['filename'];

	if($filename) {
		$fileInfo= explode(".",$filename);
		$filename = $fileInfo[0].'_'.intval($xmldata['WIDTH']).'_'.intval($xmldata['HEIGHT']).'.'.$fileInfo[1];
	
		// we want to load the croppped version if we have any
	
		$img_size = @GetImageSize(MATRIX_UPLOADDIR.$filename);
					
		$longest_side = max($img_size[0],$img_size[1]);
		if($longest_side > 90) {
			$factor = 90 / $longest_side;
			$img_size[0] = ($img_size[0]*$factor);
			$img_size[1] = ($img_size[1]*$factor);
		}
		$image = '<img src="'.SITE_ROOT.UPLOAD_DIR.$filename .'" alt="'.$data['alt'].'" width="'.$img_size[0].'" height="'.$img_size[1].'">';								
		$template->set_var('file',$image);
		$template->set_var('action_image','icon_edit');		
	} else {
		$image = '<img src="interface/images/blank.gif" alt=" " width="1" height="1">';								
		$template->set_var('file',$image);
		$template->set_var('action_image','icon_add');
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
	
	return $template->fill_block("image");
}

## =======================================================================        
##  cropimage_storeData        
## =======================================================================        
## save the data in the db
## ======================================================================= 
function cropimage_storeData($page_id, $identifier) {
	global $Auth,$input_language;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	$alt = isset($_POST[$identifier.'_alt']) ? mysql_real_escape_string($_POST[$identifier.'_alt']) : '';
	
	$db_connectionStore = new DB_Sql();
	
	if($alt != '') {
		## okay the alt tag changed
		## check if we have a image already assigned
		$query = "SELECT image_id FROM ".PAGE_IMAGE." WHERE page_id = '$page_id' AND identifier = '$identifier' AND client_id= '$client_id' AND language='$language'";
		$result_pointer = $db_connectionStore->query($query);
		if($db_connectionStore->num_rows() > 0) {
			$db_connectionStore->next_record();
			$image_id = $db_connectionStore->Record['image_id'];

			## then we will update the associated image object
			$update_query = "UPDATE ".PAGE_IMAGE."_data SET alt = '$alt' WHERE image_id = '$image_id'";
			$result_pointer = $db_connectionStore->query($update_query);	

		} else {
			## we need to create a image object- and then a image entry
			$insert_query = "INSERT INTO ".PAGE_IMAGE."_data (filename, alt,client_id,language) values ('$filename','$alt','$client_id','$language')";
			$result_pointer = $db_connectionStore->query($insert_query);
			$image_id = $db_connectionStore->db_insertid($result_pointer);	
			
			$insert_query = "INSERT INTO ".PAGE_IMAGE." (image_id, page_id, identifier, client_id,language) values ('$image_id','$page_id', '$identifier','$client_id','$language')";
			$result_pointer = $db_connectionStore->query($insert_query);			
		}
	}	
}

## =======================================================================        
##  cropimage_getData     
## =======================================================================        
##  get Data
## ======================================================================= 
function cropimage_getData($vPageID,&$page_record) {
	global $Auth,$input_language;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## data connection
	$db_connectionMain = new DB_Sql();
	## get all text elements
	
	if(!isset($vPageID)) {
		return;
	}
	$select_query = "SELECT ".PAGE_IMAGE.".image_id, filename, identifier, width, height, alt FROM ".PAGE_IMAGE." INNER  JOIN ".PAGE_IMAGE."_data ON ".PAGE_IMAGE.".image_id = ".PAGE_IMAGE."_data.image_id WHERE page_id='$vPageID' AND ".PAGE_IMAGE.".client_id='$client_id' AND (".PAGE_IMAGE.".language='$input_language' OR ".PAGE_IMAGE.".language='-1')";
	$result_pointer = $db_connectionMain->query($select_query);

	## loop through the results and set the vars in the template
	while($db_connectionMain->next_record()) {

		$filename = $db_connectionMain->Record["filename"];
		$varname = $db_connectionMain->Record["identifier"]; 
		$width = $db_connectionMain->Record["width"];
		$height = $db_connectionMain->Record["height"];
		$alt = $db_connectionMain->Record["alt"];
		$image_id = $db_connectionMain->Record["image_id"];
			
		$page_record[$varname]["type"] = "IMAGE";
		$page_record[$varname]["filename"] = $filename; 
		$page_record[$varname]["width"] = $width; 
		$page_record[$varname]["height"] = $height;
		$page_record[$varname]["alt"] = $alt; 
		$page_record[$varname]["identifier"] = $varname; 
		$page_record[$varname]["image_id"] = $image_id; 
	}
}

## =======================================================================        
##  cropimage_copyData        
## =======================================================================        
##  pass it the sourcepage_id and the targetpage_id. It'll copy all
##  text entries to the new page.
## ======================================================================= 
function cropimage_deleteData($vPageID) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## data connection
	$db_connectionMain = new DB_Sql();
	$f = new file_object();
	
	## first we check all entries that have the image ids- problem we casn have multiple images on one
	## page- so we do it in two steps... first we get all entries	
	$pageData = array();
	image_getData($vPageID,$pageData);

	foreach($pageData as $current_file) {
		## now check if the image is used on another page

		$query = "SELECT page_id FROM ".PAGE_IMAGE." WHERE image_id=".$current_file['image_id'];
		$result_pointer = $db_connectionMain->query($query);

		if($db_connectionMain->num_rows() <=1) {
			## okay it's used only once- let's delete the base entry
			$query = "DELETE FROM ".PAGE_IMAGE."_data WHERE image_id=".$current_file['image_id'];
			$result_pointer = $db_connectionMain->query($query);
			
			## okay this means we can delete the file itself too
			$f->delete_file(MATRIX_UPLOADDIR.$current_file['filename']);
		}
	}
	
	## now delete all link elements for this page
	$query = "DELETE FROM ".PAGE_IMAGE." WHERE page_id='$vPageID' AND client_id='$client_id'";
	$result_pointer = $db_connectionMain->query($query);
}


## =======================================================================        
##  cropimage_deleteData        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function cropimage_copyData($source_id, $target_id) {
	global $Auth;
	## multiclient
	$client_id = $Auth->auth['client_id'];	

	## data connection
	$db_source = new DB_Sql();
	$db_target = new DB_Sql();
	
	## the image data is stored in two tables- since we are copying the image, we only need to create a new link entry
	## the base entry is the same- we will also need to fix the delete function
	
	## get all image elements
	$select_query = "SELECT A.image_id,identifier,A.language,filename FROM ".PAGE_IMAGE." AS A INNER JOIN ".PAGE_IMAGE."_data AS B ON A.image_id=B.image_id WHERE page_id='$source_id' AND A.client_id='$client_id'";
	$result_pointer = $db_source->query($select_query);

	## loop through the results and copy them over
	while($db_source->next_record()) {
		$identifier = $db_source->Record['identifier'];
		$image_id = $db_source->Record['image_id'];
		$language = $db_source->Record['language'];
		$filename = $db_source->Record['filename'];

		## since it is possible that we get called muliple times for each datatype that stores the data into our tables,
		## we need to check if the entry already exists
		$query = "SELECT image_id FROM ".PAGE_IMAGE." WHERE page_id = '$target_id' AND identifier = '$identifier' AND client_id = '$client_id' AND language='$input_language'";
		$result_pointer = $db_target->query($query,true);			

		if($db_target->num_rows() == 0) { 
			## create a new link element
			$query = "INSERT INTO ".PAGE_IMAGE." (image_id,page_id, identifier,client_id,language) values ('$image_id','$target_id','$identifier','$client_id','$language')";
			$result_pointer = $db_target->query($query,true);
		}
	}	
}


## =======================================================================        
##  output_linklist       
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function cropimage_output($item,$structure,$menu_id) {	
	if(!$item) {
		return "";
	}
	
	// first we merge the widths and heights together
	$widths[] = intval($structure['WIDTH']);
	$heights[] = intval($structure['HEIGHT']);
	
	$thumb_widths = explode(',',$structure['THUMB_WIDTH']);
	$thumb_heights = explode(',',$structure['THUMB_HEIGHT']);

	$widths = array_merge($widths,$thumb_widths);
	$heights = array_merge($heights,$thumb_heights);
	
	$identifier = $item['identifier'];
	
	$filename = $item['filename'];
	$fileInfo= explode(".",$filename);
	$alt = $item['alt'];

	$image_count = count($widths);
	$output = array();
	for($i=0; $i<$image_count; $i++) {
		if($i == 0) {
			$prefix = '';
		} else {
			$prefix = '.'.$i;
		}
		
		if($widths[$i] == 0 || $heights[$i] == 0) {
			$img_size = @GetImageSize(MATRIX_UPLOADDIR.$fileInfo[0].'_'.$widths[$i].'_'.$heights[$i].'.'.$fileInfo[1]);	

			$current_width = $img_size[0];
			$current_height = $img_size[1];

		} else {	
			$current_width = $widths[$i];
			$current_height = $heights[$i];		
		}


		if($filename) {
			$image_absolute = '<img src="'.ABSOLUTE_UPLOAD_DIR.$fileInfo[0].'_'.$widths[$i].'_'.$heights[$i].'.'.$fileInfo[1] .'" alt="'.$alt.'" width="'.$current_width.'" height="'.$current_height.'" border="0" />';	
			$image = '<img src="/'.UPLOAD_DIR.$fileInfo[0].'_'.$widths[$i].'_'.$heights[$i].'.'.$fileInfo[1] .'" alt="'.$alt.'" width="'.$current_width.'" height="'.$current_height.'" border="0" />';								
		} else {
			$image_absolute = '';
			$image = '';
		}
		
		$output[$identifier.$prefix.'.width'] = $current_width;
		$output[$identifier.$prefix.'.height'] = $current_height;
		$output[$identifier.$prefix.'.file'] = '/'.UPLOAD_DIR.$fileInfo[0].'_'.$widths[$i].'_'.$heights[$i].'.'.$fileInfo[1];
		$output[$identifier.$prefix.'.filename'] = $fileInfo[0].'_'.$widths[$i].'_'.$heights[$i].'.'.$fileInfo[1];
		$output[$identifier.$prefix.'.absolute'] = $image_absolute;
		$output[$identifier.$prefix.'.alt'] = $alt;
		$output[$identifier.$prefix.'.absolutefile'] = UPLOAD_DIR_DOCS.$fileInfo[0].'_'.$widths[$i].'_'.$heights[$i].'.'.$fileInfo[1];
		$output[$identifier.$prefix] = $image;
	}


	return $output;	
}



?>
