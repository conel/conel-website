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


## =======================================================================        
## image_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function image_displayInput($xmldata, $data) {
	## we access the global pageID
	global $g_pageID;
	global $gSession,$input_language;
	
	## init the vars
	$return = "";
	
	## we should open our own template
	$template = new Template(ENGINE."datatypes/image/interface/");
	$template->set_templatefile(array("image" => "interface.tpl","altimage" => "interface.tpl"));
	
	## set the vars
	$template->set_var('element_name',$xmldata['NAME']);
	$template->set_var('element_desc',$xmldata['DESC']);

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
	
	return $template->fill_block("image");
}


## =======================================================================        
##  image_storeData        
## =======================================================================        
## save the content in the db
## ======================================================================= 
function image_storeData($page_id, $identifier) {
	global $Auth,$input_language;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## prepare the data
	$userfile	= $_FILES[$identifier]['tmp_name'];
	$file_name	= $_FILES[$identifier]['name'];
	$file_size	= $_FILES[$identifier]['size'];
	$file_type	= $_FILES[$identifier]['type'];
	$language = intval($_POST[$identifier.'_language']);

	$alt = isset($_POST[$identifier.'_alt']) ? mysql_real_escape_string($_POST[$identifier.'_alt']) : '';

	## prepare the db-object
	$db_connectionStore = new DB_Sql();
	              
	## okay we first create an upload object
	$f = new file_object();     
	if (($userfile != "none") && ($userfile !="")) {             
		##then we upload the file
		$filename = $f->upload($userfile, $file_name,$file_size,$file_type, MATRIX_UPLOADDIR);
		if($filename != -1) {
			## we have the filename- so before we insert the image into the db,
			## we will retrieve its size
			$img_size = GetImageSize(MATRIX_UPLOADDIR.$filename);

			## first we need to regsiter the image in your image_data table
			$insert_query = "INSERT INTO ".PAGE_IMAGE."_data (filename, mime_type, width, height, alt,client_id,language) values ('$filename','$file_type','$img_size[0]','$img_size[1]','$alt','$client_id','$language')";
			$result_pointer = $db_connectionStore->query($insert_query);
			$image_id = $db_connectionStore->db_insertid($result_pointer);			

			## first we need to find out if the entry already exists
			$select_query = "SELECT image_id FROM ".PAGE_IMAGE." WHERE page_id = '$page_id' AND identifier = '$identifier' AND client_id= '$client_id' AND language='$language'";
			$result_pointer = $db_connectionStore->query($select_query);
			
			if($db_connectionStore->num_rows() == 0) { 
				## no entry found
				$insert_query = "INSERT INTO ".PAGE_IMAGE." (image_id, page_id, identifier, client_id,language) values ('$image_id','$page_id', '$identifier','$client_id','$language')";
				$result_pointer = $db_connectionStore->query($insert_query);
			} else {
				$update_query = "UPDATE ".PAGE_IMAGE." SET image_id = '$image_id' WHERE page_id = '$page_id' AND identifier = '$identifier' AND client_id ='$client_id' AND language='$language'";
				$result_pointer = $db_connectionStore->query($update_query);
			}
		}
	} 
	
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
##  image_getData        
## =======================================================================        
##  get the Data
## ======================================================================= 
function image_getData($vPageID,&$page_record) {
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
##  image_deleteData        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function image_deleteData($vPageID) {
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
##  output_image        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function output_image($item,$structure,$menu_id) {
	if(!$item) {
		return "";
	}
	## init filename
	$filename = "";
	
	$filename = $item['filename'];
	$alt = $item['alt'];
	$scale = 1;
	$width = $item['width'];
	$height = $item['height'];	

	if(!$filename) {
		return '';						
	} 	
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

	if($filename) {
		$image_absolute = '<img src="'.ABSOLUTE_UPLOAD_DIR.$filename .'" alt="'.$alt.'" width="'.$width.'" height="'.$height.'" border="0" />';	
		$image = '<img src="/'.UPLOAD_DIR.$filename .'" alt="'.$alt.'" width="'.$width.'" height="'.$height.'" border="0" />';								
	} else {
		$image_absolute = '';
		$image = '';
	}

	## prepare the identifier
	$identifier = $item['identifier'];
	$value = array(
		$identifier.'.width' => $width,
		$identifier.'.height' => $height,
		$identifier.'.file' => '/'.UPLOAD_DIR.$filename,
		$identifier.'.filename' =>$filename,
		$identifier.'.absolute' =>$image_absolute,
		$identifier.'.alt' => $alt,
		$identifier.'.absolutefile' =>UPLOAD_DIR_DOCS.$filename,		
		$identifier =>$image
	
	);

	return $value;
}

## =======================================================================        
##  image_copyData        
## =======================================================================        
##  pass it the sourcepage_id and the targetpage_id. It'll copy all
##  text entries to the new page.
## ======================================================================= 
function image_copyData($source_id, $target_id) {
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
##  output_text        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function image_displayPreview($xmldata, $data) {
	$value = output_image($data,$xmldata,0);
	$identifier = $xmldata['NAME'];
	
	$width = $value[$identifier.'.width'];
	$height = $value[$identifier.'.height'];
	$file = $value[$identifier.'.filename'];
		
	if($file) {
		$scale = 45/$width;
		## scale the stuff
		$width = (int) ($width*$scale);
		$height = (int) ($height*$scale);	
		$output = '<img src="'.ABSOLUTE_UPLOAD_DIR.$file .'" alt=" " width="'.$width.'" height="'.$height.'" border="0" />';
		return $output;
	}
	return " ";

}
?>