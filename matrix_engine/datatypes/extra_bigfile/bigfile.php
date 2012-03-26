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
function bigfile_displayInput($xmldata, $data) {
	global $gSession,$g_pageID,$input_language;
	## init the vars
	$return = "";

	## we should open our own template
	$template = new Template(ENGINE."datatypes/extra_bigfile/interface/");
	$template->set_templatefile(array("file" => "interface.tpl"));
	
	## set the vars
	$template->set_var('element_name',$xmldata['NAME']);
	$template->set_var('element_desc',$xmldata['DESC']);
	
	## prepare the toolbar links
	$addlinkURL = SITE."datatypes/extra_bigfile/editor.php?op=add&page_id=".$g_pageID."&identifier=".$xmldata['NAME']."&language=".$input_language;
	$addlinkURL = $gSession->url($addlinkURL);	
		
	$deletelinkURL = SITE."datatypes/extra_bigfile/editor.php?op=delete&page_id=".$g_pageID."&language=".$input_language."&identifier=".$xmldata['NAME'];		
	$deletelinkURL = $gSession->url($deletelinkURL);
	
	## prepare the process url
	$processURL = SITE."datatypes/extra_bigfile/editor.php?op=process&page_id=".$g_pageID."&identifier=".$xmldata['NAME']."&language=".$input_language;
	$processURL = $gSession->url($processURL);
	
	$template->set_var('deletelinkItemURL',$deletelinkURL);
	$template->set_var('addlinkItemURL',$addlinkURL);
	$template->set_var('processURL',$processURL);
	
	## now it's time to check the previous data entered
	$filename = $data['filename'];
	$text = convert_html($data['text']);


	## set the vars accordingly
	$template->set_var('text',$text);
	$template->set_var('filename','/docs/'.$filename);	
	
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
function bigfile_storeData($page_id, $identifier) {
}

## =======================================================================        
##  file_getData        
## =======================================================================        
##  get the Data
## ======================================================================= 
function bigfile_getData($vPageID,&$page_record) {
	global $Auth,$input_language;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## data connection
	$db_connectionMain = new DB_Sql();
	## get all text elements
	$select_query = "SELECT file_id,filename, identifier FROM ".PAGE_FILE." WHERE page_id='$vPageID' AND client_id='$client_id' AND language='$input_language'";
	$result_pointer = $db_connectionMain->query($select_query,false);

	## loop through the results and set the vars in the template
	while($db_connectionMain->next_record()) {
		$filename = $db_connectionMain->Record["filename"];
		$varname = $db_connectionMain->Record["identifier"];
		$file_id = $db_connectionMain->Record["file_id"];
		
		$page_record[$varname]["type"] = "MOVIE";
		$page_record[$varname]["filename"] = $filename;  
		$page_record[$varname]["identifier"] = $varname; 
		$page_record[$varname]["file_id"] = $file_id; 
	}
}

## =======================================================================        
##  file_deleteData        
## =======================================================================        
##  deletes all file entries of a page
## ======================================================================= 
function bigfile_deleteData($vPageID) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## data connection
	$db_connectionMain = new DB_Sql();
	$f = new file_object();  
	
	## first we need to get the files in order to delete them from the filesystem- then remove
	## them from the db
	$pageData = array();
	bigfile_getData($vPageID,$pageData);

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
function bigfile_output($item,$structure,$menu_id) {
	$filename 	= $item['filename'];
	$identifier	= $item['identifier'];
	$file_id	= $item['file_id'];
	
	## determine the size
	$file_size = @filesize(MATRIX_BASEDIR.UPLOAD_DIR_DOCS.$filename);
	$file_size = _files_formatFileSize($file_size);
	
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
function bigfile_displayPreview($xmldata, $data) {
	$value = convert_html($data['filename']);
	return $value;

}


## =======================================================================        
##  file_copyData        
## =======================================================================        
##  pass it the sourcepage_id and the targetpage_id. It'll copy all
##  text entries to the new page.
## ======================================================================= 
function bigfile_copyData($source_id, $target_id) {
	file_copyData($source_id, $target_id);	
}

?>
