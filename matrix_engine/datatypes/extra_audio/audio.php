<?PHP
## ======================================================================= 
##   webmatrix                                                     
## ======================================================================= 
##   Copyright (c) 2001, 2002, 2003 Stefan Elsner, Workmatrix        
## ======================================================================= 
##   <info@workmatrix.de> 
##
## ======================================================================= 

require('classes/class_mp3.php');
## =======================================================================        
## file_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function audio_displayInput($xmldata, $data) {
	global $gSession,$g_pageID,$input_language;
	## init the vars
	$return = "";

	## we should open our own template
	$template = new Template(ENGINE."datatypes/extra_audio/interface/");
	$template->set_templatefile(array("file" => "interface.tpl"));
	
	## set the vars
	$template->set_var('element_name',$xmldata['NAME']);
	$template->set_var('element_desc',$xmldata['DESC']);
	
	## prepare the toolbar links
	$addlinkURL = SITE."datatypes/extra_audio/editor.php?op=add&page_id=".$g_pageID."&identifier=".$xmldata['NAME']."&language=".$input_language;
	$addlinkURL = $gSession->url($addlinkURL);	
		
	$deletelinkURL = SITE."datatypes/extra_audio/editor.php?op=delete&page_id=".$g_pageID."&language=".$input_language."&identifier=".$xmldata['NAME'];		
	$deletelinkURL = $gSession->url($deletelinkURL);
	
	$template->set_var('deletelinkItemURL',$deletelinkURL);
	$template->set_var('addlinkItemURL',$addlinkURL);
	
	

	## now it's time to check the previous data entered
	$filename = $data['filename'];
	$text = convert_html($data['text']);

	$mp3 = new MP3(MATRIX_UPLOADDIR_DOCS.$filename);
	if($mp3->error != -1) {
		$mp3->get_id3();
		$mp3->get_info(); 
	}
	
	$template->set_var('TRACK','/docs/'.$filename);
	$template->set_var('DURATION',$mp3->info["length_millisecs"]);
	
	## set the vars accordingly
	$template->set_var('text',$text);
	$template->set_var('filename',$filename);	
	
	if(!$xmldata['TAG']) {
		$template->set_var('element_tag',LANG_ElementFile);
	} else {
		$template->set_var('element_tag',$xmldata['TAG']);
	}
	
	## finally set the file
	$template->set_var('PATH',SITE."datatypes/extra_audio/interface/");
	$template->set_var('SESSION',$gSession->id);	
			
	return $template->fill_block("file");
}


## =======================================================================        
##  file_storeData        
## =======================================================================        
## save the content in the db
## ======================================================================= 
function audio_storeData($page_id, $identifier) {
}

## =======================================================================        
##  file_getData        
## =======================================================================        
##  get the Data
## ======================================================================= 
function audio_getData($vPageID,&$page_record) {
	global $Auth,$input_language;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## data connection
	$db_connectionMain = new DB_Sql();
	## get all text elements
	$select_query = "SELECT audio_id,filename,length, identifier FROM ".DB_PREFIX."page_audio WHERE page_id='$vPageID' AND client_id='$client_id' AND language='$input_language'";
	$result_pointer = $db_connectionMain->query($select_query);

	## loop through the results and set the vars in the template
	while($db_connectionMain->next_record()) {
		$filename = $db_connectionMain->Record["filename"];
		$text = $db_connectionMain->Record["text"];
		$varname = $db_connectionMain->Record["identifier"];
		$audio_id = $db_connectionMain->Record["audio_id"];
		
		$page_record[$varname]["type"] = "FILE";
		$page_record[$varname]["filename"] = $filename; 
		$page_record[$varname]["text"] = $text; 
		$page_record[$varname]["identifier"] = $varname; 
		$page_record[$varname]["audio_id"] = $audio_id; 
	}
}

## =======================================================================        
##  file_deleteData        
## =======================================================================        
##  deletes all file entries of a page
## ======================================================================= 
function audio_deleteData($vPageID) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## data connection
	$db_connectionMain = new DB_Sql();
	$f = new file_object();  
	
	## first we need to get the files in order to delete them from the filesystem- then remove
	## them from the db
	$pageData = array();
	audio_getData($vPageID,$pageData);

	foreach($pageData as $current_file) {
		## remove the file
		$f->delete_file(MATRIX_UPLOADDIR_DOCS.$current_file['filename']);
	}

	## finally delete all files
	$query = "DELETE FROM ".DB_PREFIX."page_audio WHERE page_id='$vPageID' and client_id='$client_id'";
	$result_pointer = $db_connectionMain->query($query);
}

## =======================================================================        
##  output_text        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function audio_output($item,$structure,$menu_id) {
	$text 		= $item['text'];
	$filename 	= $item['filename'];
	$identifier	= $item['identifier'];
	$audio_id	= $item['audio_id'];
	
	
	$value = array(
		$identifier.'.id' => $file_id, 
		$identifier.'.text' => $text, 
		$identifier.'.file' =>UPLOAD_DIR_DOCS.$filename,
		$identifier.'.filename' =>$filename,
		$identifier.'.size' =>$file_size,
		$identifier.'.absolute' =>'<a href="'.ABSOLUTE_UPLOAD_DIR.$filename .'">'.$text.'</a>',
		$identifier.'.absolutefile' =>UPLOAD_DIR_DOCS.$filename,
		$identifier =>'<a href="'.UPLOAD_DIR_DOCS.$filename .'">'.$text.'</a>'
		
		);
	return $value;
}

## =======================================================================        
##  file_displayPreview        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function audio_displayPreview($xmldata, $data) {
	$value = convert_html($data['filename']);
	return $value;

}


## =======================================================================        
##  file_copyData        
## =======================================================================        
##  pass it the sourcepage_id and the targetpage_id. It'll copy all
##  text entries to the new page.
## ======================================================================= 
function audio_copyData($source_id, $target_id) {
	global $Auth;
	## multiclient
	$client_id = $Auth->auth['client_id'];	

	## data connection
	$db_source = new DB_Sql();
	$db_target = new DB_Sql();
	
	## get all text elements
	$select_query = "SELECT identifier,filename,length,language FROM ".DB_PREFIX."page_audio WHERE page_id='$source_id' AND client_id='$client_id'";
	$result_pointer = $db_source->query($select_query);

	## loop through the results and copy them over
	while($db_source->next_record()) {
		$identifier = $db_source->Record['identifier'];
		$filename = $db_source->Record['filename'];
		$length = $db_source->Record['length'];
		$language = $db_source->Record['language'];
		
		## since it is possible that we get called muliple times for each datatype that stores the data into our tables,
		## we need to check if the entry already exists
		$query = "SELECT file_id FROM ".DB_PREFIX."page_audio WHERE page_id = '$target_id' AND identifier = '$identifier' AND client_id = '$client_id' AND language='$input_language'";
		$result_pointer = $db_target->query($query);			
	
		if($db_target->num_rows() == 0) { 
			## we haven't copied the element yet- so we first need to create a copy of the file first
			if(!empty($filename)) {
				$f = new file_object(); 
				$filename = $f->copy_file(MATRIX_UPLOADDIR_DOCS.$filename,$filename,MATRIX_UPLOADDIR_DOCS);
			}
			$query = "INSERT INTO ".DB_PREFIX."page_audio (page_id, identifier, filename,length,client_id,language) values ('$target_id', '$identifier','$filename', '$length','$client_id','$language')";
			$result_pointer = $db_target->query($query);
		}
	}	
}

?>
