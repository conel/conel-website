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
## file_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function file_displayInput($xmldata, $data) {
	global $gSession,$g_pageID,$input_language;
	## init the vars
	$return = "";

	## we should open our own template
	$template = new Template(ENGINE."datatypes/file/interface/");
	$template->set_templatefile(array("file" => "interface.tpl"));
	
	## set the vars
	$template->set_var('element_name',$xmldata['NAME']);
	$template->set_var('element_desc',$xmldata['DESC']);
	
	## we got your record to process the data
	## intput the delete link
	$deletelinkURL = SITE."datatypes/file/editor.php?op=delete&page_id=".$g_pageID."&language=".$input_language."&identifier=".$xmldata['NAME'];		
	$deletelinkURL = $gSession->url($deletelinkURL);
	$template->set_var('deletelinkItemURL',$deletelinkURL);
	
	## now it's time to check the previous data entered
	$filename = $data['filename'];
	$text = convert_html($data['text']);
	
	## set the vars accordingly
	$template->set_var('text',$text);
	$template->set_var('filename',$filename);	
	
	if(!$xmldata['TAG']) {
		$template->set_var('element_tag',LANG_ElementFile);
	} else {
		$template->set_var('element_tag',$xmldata['TAG']);
	}
							
	return $template->fill_block("file");
}


## =======================================================================        
##  file_storeData        
## =======================================================================        
## save the content in the db
## ======================================================================= 
function file_storeData($page_id, $identifier) {
	global $Auth,$input_language;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	$userfile	= $_FILES[$identifier]['tmp_name'];
	$file_name	= $_FILES[$identifier]['name'];
	$file_size	= $_FILES[$identifier]['size'];
	$file_type	= $_FILES[$identifier]['type'];
	//$text		= $_POST[$identifier."name"];
	$text		= mysql_real_escape_string($_POST["HEADLINE"]);

	## for security and convenience reasons we have to convert the
	## supplied string

	## prepare the db-object
	$db_connectionStore = new DB_Sql();

	## okay we first create an upload object
	$f = new file_object();     
	##$userfile = stripslashes($userfile);
	if ($userfile != "none" && $userfile!='') {              
		## then we upload the file
		$filename = $f->upload($userfile, $file_name,$file_size,$file_type, MATRIX_UPLOADDIR_DOCS);
		if($filename != -1) {
			## first we need to find out if the entry already exists
			$select_query = "SELECT file_id,filename FROM ".PAGE_FILE." WHERE page_id = '$page_id' AND identifier = '$identifier' AND client_id='$client_id' AND language='$input_language'";
			$result_pointer = $db_connectionStore->query($select_query);	
	
			if($db_connectionStore->num_rows() == 0) { 
				## no entry found
				$insert_query = "INSERT INTO ".PAGE_FILE." (page_id, identifier, filename, text, mime_type, client_id,language) values ('$page_id', '$identifier', '$filename', '$text', '$file_type', '$client_id','$input_language')";
				$result_pointer = $db_connectionStore->query($insert_query);
			} else {
				$db_connectionStore->next_record();
				$file_id = $db_connectionStore->Record["file_id"];
				$old_filename = $db_connectionStore->Record["filename"];
				
				## delete the old file first
				$f->delete_file(MATRIX_UPLOADDIR_DOCS.$old_filename);
				
				$update_query = "UPDATE ".PAGE_FILE." SET filename = '$filename', text='$text' WHERE file_id = '$file_id' AND client_id='$client_id' AND language='$input_language'";
				$result_pointer = $db_connectionStore->query($update_query);
			}
		}
	} else {
		## in this case we will update the text element
			## first we need to find out if the entry already exists
			$select_query = "SELECT file_id FROM ".PAGE_FILE." WHERE page_id = '$page_id' AND identifier = '$identifier' AND client_id='$client_id' AND language='$input_language'";
			$result_pointer = $db_connectionStore->query($select_query);	
	
			if($db_connectionStore->num_rows() == 0) { 
				## no entry found
				$insert_query = "INSERT INTO ".PAGE_FILE." (page_id, identifier, text, client_id,language) values ('$page_id', '$identifier', '$text','$client_id','$input_language')";
				$result_pointer = $db_connectionStore->query($insert_query);
			} else {
				$db_connectionStore->next_record();
				$file_id = $db_connectionStore->Record["file_id"];
				$update_query = "UPDATE ".PAGE_FILE." SET text='$text' WHERE file_id = '$file_id' AND client_id='$client_id' AND language='$input_language'";
				$result_pointer = $db_connectionStore->query($update_query);
			}
		}
}

## =======================================================================        
##  file_getData        
## =======================================================================        
##  get the Data
## ======================================================================= 
function file_getData($vPageID,&$page_record) {
	global $Auth,$input_language;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## data connection
	$db_connectionMain = new DB_Sql();
	## get all text elements
	$select_query = "SELECT file_id,filename,text, identifier FROM ".PAGE_FILE." WHERE page_id='$vPageID' AND client_id='$client_id' AND language='$input_language'";
	$result_pointer = $db_connectionMain->query($select_query);

	## loop through the results and set the vars in the template
	while($db_connectionMain->next_record()) {
		$filename = $db_connectionMain->Record["filename"];
		$text = $db_connectionMain->Record["text"];
		$varname = $db_connectionMain->Record["identifier"];
		$file_id = $db_connectionMain->Record["file_id"];
		
		// 16/6/2009 - nkowald: Create file icon template var
		$filetype = substr(strrchr($filename, '.'), 1);
		$icon_img = '';
		// switch on pdf and doc filetypes for now - can add others as needed
		switch($filetype) {
			case 'pdf':
			$icon_img = '<img src="/layout/img/pdf.gif" alt="PDF file" /> ';
			break;
			
			case 'doc':
			$icon_img = '<img src="/layout/img/word.gif" alt="Word Doc file" /> ';
			break;
			
			case 'jpg':
			$icon_img = '<img src="/layout/img/jpg.gif" alt="JPEG Image file" /> ';
			break;
			
			case 'gif':
			$icon_img = '<img src="/layout/img/word.gif" alt="Gif Image file" /> ';
			break;
		}
		
		$page_record[$varname]["type"] = "FILE";
		$page_record[$varname]["filename"] = $filename; 
		$page_record[$varname]["icon_img"] = $icon_img;
		$page_record[$varname]["text"] = $text; 
		$page_record[$varname]["identifier"] = $varname; 
		$page_record[$varname]["file_id"] = $file_id; 
	}
}

## =======================================================================        
##  file_deleteData        
## =======================================================================        
##  deletes all file entries of a page
## ======================================================================= 
function file_deleteData($vPageID) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## data connection
	$db_connectionMain = new DB_Sql();
	$f = new file_object();  
	
	## first we need to get the files in order to delete them from the filesystem- then remove
	## them from the db
	$pageData = array();
	file_getData($vPageID,$pageData);

	foreach($pageData as $current_file) {
		## remove the file
		$f->delete_file(MATRIX_UPLOADDIR_DOCS.$current_file['filename']);
	}

	## finally delete all files
	$query = "DELETE FROM ".PAGE_FILE." WHERE page_id='$vPageID' and client_id='$client_id'";
	$result_pointer = $db_connectionMain->query($query);
}

## =======================================================================        
##  output_text        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function output_file($item,$structure,$menu_id) {
	$text 		= $item['text'];
	$filename 	= $item['filename'];
	$identifier	= $item['identifier'];
	$file_id	= $item['file_id'];
	$icon_img   = $item['icon_img'];
	
	## determine the size
	$file_size = @filesize(MATRIX_BASEDIR.UPLOAD_DIR_DOCS.$filename);
	$file_size = _files_formatFileSize($file_size);
	
	$value = array(
		$identifier.'.id' => $file_id, 
		$identifier.'.text' => $text, 
		$identifier.'.file' => UPLOAD_DIR_DOCS.$filename,
		$identifier.'.filename' => $filename,
		$identifier.'.icon_img' => $icon_img,
		$identifier.'.size' => $file_size,
		$identifier.'.absolute' => '<a href="'.ABSOLUTE_UPLOAD_DIR.$filename .'">'.$text.'</a>',
		$identifier.'.absolutefile' => UPLOAD_DIR_DOCS.$filename,
		$identifier => '<a href="'.UPLOAD_DIR_DOCS.$filename .'">'.$text.'</a>'
		
		);
	return $value;
}

## =======================================================================        
##  file_displayPreview        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function file_displayPreview($xmldata, $data) {
	$value = convert_html($data['text']);
	return $value;

}


## =======================================================================        
##  file_copyData        
## =======================================================================        
##  pass it the sourcepage_id and the targetpage_id. It'll copy all
##  text entries to the new page.
## ======================================================================= 
function file_copyData($source_id, $target_id) {
	global $Auth;
	## multiclient
	$client_id = $Auth->auth['client_id'];	

	## data connection
	$db_source = new DB_Sql();
	$db_target = new DB_Sql();
	
	## get all text elements
	$select_query = "SELECT identifier,filename,text,mime_type,language FROM ".PAGE_FILE." WHERE page_id='$source_id' AND client_id='$client_id'";
	$result_pointer = $db_source->query($select_query);

	## loop through the results and copy them over
	while($db_source->next_record()) {
		$identifier = $db_source->Record['identifier'];
		$filename = $db_source->Record['filename'];
		$text = $db_source->Record['text'];
		$mime_type = $db_source->Record['mime_type'];
		$language = $db_source->Record['language'];
		
		## since it is possible that we get called muliple times for each datatype that stores the data into our tables,
		## we need to check if the entry already exists
		$query = "SELECT file_id FROM ".PAGE_FILE." WHERE page_id = '$target_id' AND identifier = '$identifier' AND client_id = '$client_id' AND language='$input_language'";
		$result_pointer = $db_target->query($query);			
	
		if($db_target->num_rows() == 0) { 
			## we haven't copied the element yet- so we first need to create a copy of the file first
			if(!empty($filename)) {
				$f = new file_object(); 
				$filename = $f->copy_file(MATRIX_UPLOADDIR_DOCS.$filename,$filename,MATRIX_UPLOADDIR_DOCS);
			}
			$query = "INSERT INTO ".PAGE_FILE." (page_id, identifier, filename,text,mime_type,client_id,language) values ('$target_id', '$identifier','$filename', '$text','$mime_type','$client_id','$language')";
			$result_pointer = $db_target->query($query);
		}
	}	
}

## =======================================================================        
##  _files_formatFileSize        
## =======================================================================        
##  returns the filsize in bytes, kb, or mb
## ======================================================================= 
function _files_formatFileSize($size) {
	$suffix = '';
	
	## check if we got a number
	if(!is_numeric($size) || $size < 0) {
		return 0;
	}
	
	## now determine the 'level'
	for($level = 0; $size >= 1024; $level++) {
		$size /= 1024;
	}
	
	## now add the suffix
	switch($level) {
		case 0: $suffix = 'Bytes'; break;
		case 1: $suffix = 'KB'; break;
		case 2: $suffix = 'MB'; break;
		case 3: $suffix = 'GB'; break;
		default: $suffix = '';
	}
	
	return round($size,2) . ' '.$suffix;
}

?>
