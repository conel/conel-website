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
function movie_displayInput($xmldata, $data) {
	global $gSession,$g_pageID,$input_language;
	## init the vars
	$return = "";

	## we should open our own template
	$template = new Template(ENGINE."datatypes/extra_movie/interface/");
	$template->set_templatefile(array("file" => "interface.tpl"));
	
	## set the vars
	$template->set_var('element_name',$xmldata['NAME']);
	$template->set_var('element_desc',$xmldata['DESC']);
	
	## prepare the toolbar links
	$addlinkURL = SITE."datatypes/extra_movie/editor.php?op=add&page_id=".$g_pageID."&identifier=".$xmldata['NAME']."&language=".$input_language;
	$addlinkURL = $gSession->url($addlinkURL);	
		
	$deletelinkURL = SITE."datatypes/extra_movie/editor.php?op=delete&page_id=".$g_pageID."&language=".$input_language."&identifier=".$xmldata['NAME'];		
	$deletelinkURL = $gSession->url($deletelinkURL);
	
	$template->set_var('deletelinkItemURL',$deletelinkURL);
	$template->set_var('addlinkItemURL',$addlinkURL);
	
	
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
function movie_storeData($page_id, $identifier) {
}

## =======================================================================        
##  file_getData        
## =======================================================================        
##  get the Data
## ======================================================================= 
function movie_getData($vPageID,&$page_record) {
	global $Auth,$input_language;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## data connection
	$db_connectionMain = new DB_Sql();
	## get all text elements
	$select_query = "SELECT movie_id,filename,width,height, identifier FROM ".DB_PREFIX."page_movie WHERE page_id='$vPageID' AND client_id='$client_id' AND language='$input_language'";
	$result_pointer = $db_connectionMain->query($select_query,false);

	## loop through the results and set the vars in the template
	while($db_connectionMain->next_record()) {
		$filename = $db_connectionMain->Record["filename"];
		$width = $db_connectionMain->Record["width"];
		$height = $db_connectionMain->Record["height"];
		$varname = $db_connectionMain->Record["identifier"];
		$movie_id = $db_connectionMain->Record["movie_id"];
		
		$page_record[$varname]["type"] = "MOVIE";
		$page_record[$varname]["filename"] = $filename; 
		$page_record[$varname]["width"] = $width; 
		$page_record[$varname]["height"] = $height; 
		$page_record[$varname]["identifier"] = $varname; 
		$page_record[$varname]["movie_id"] = $movie_id; 
	}
}

## =======================================================================        
##  file_deleteData        
## =======================================================================        
##  deletes all file entries of a page
## ======================================================================= 
function movie_deleteData($vPageID) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## data connection
	$db_connectionMain = new DB_Sql();
	$f = new file_object();  
	
	## first we need to get the files in order to delete them from the filesystem- then remove
	## them from the db
	$pageData = array();
	movie_getData($vPageID,$pageData);

	foreach($pageData as $current_file) {
		## remove the file
		$f->delete_file(MATRIX_UPLOADDIR_DOCS.$current_file['filename']);
	}

	## finally delete all files
	$query = "DELETE FROM ".DB_PREFIX."page_movie WHERE page_id='$vPageID' and client_id='$client_id'";
	$result_pointer = $db_connectionMain->query($query,false);
}

## =======================================================================        
##  output_text        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function movie_output($item,$structure,$menu_id) {
	$filename 	= $item['filename'];
	$width 		= $item['width'];
	$height		= $item['height'];
	$movie_id	= $item['movie_id'];
	$identifier	= $item['identifier'];
	
	## prepare the output
	$element = "<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0\" width=\"".$width."\" height=\"".$height."\" id=\"video_player\" align=\"middle\">
<param name=\"allowScriptAccess\" value=\"sameDomain\" />
<param name=\"movie\" value=\"".SITE_ROOT."matrix_engine/datatypes/extra_movie/resources/video_player.swf?flv=/".UPLOAD_DIR_DOCS.$filename."\" />
<param name=\"quality\" value=\"high\" /><param name=\"bgcolor\" value=\"#ffffff\" /><embed src=\"".SITE_ROOT."matrix_engine/datatypes/extra_movie/resources/video_player.swf?flv=/".UPLOAD_DIR_DOCS.$filename."\" quality=\"high\" bgcolor=\"#ffffff\" width=\"".$width."\" height=\"".$height."\" name=\"video_player\" align=\"middle\" allowScriptAccess=\"sameDomain\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" />
</object>";	
	
	$value = array(
		$identifier.'.id' => $movie_id, 
		$identifier.'.width' => $width, 
		$identifier.'.file' =>UPLOAD_DIR_DOCS.$filename,
		$identifier.'.filename' =>$filename,
		$identifier.'.height' =>$height,
		$identifier.'.absolutefile' =>UPLOAD_DIR_DOCS.$filename,
		$identifier =>$element
		
		);

	return $value;
}

## =======================================================================        
##  file_displayPreview        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function movie_displayPreview($xmldata, $data) {
	$value = convert_html($data['filename']);
	return $value;

}


## =======================================================================        
##  file_copyData        
## =======================================================================        
##  pass it the sourcepage_id and the targetpage_id. It'll copy all
##  text entries to the new page.
## ======================================================================= 
function movie_copyData($source_id, $target_id) {
	global $Auth;
	## multiclient
	$client_id = $Auth->auth['client_id'];	

	## data connection
	$db_source = new DB_Sql();
	$db_target = new DB_Sql();
	
	## get all text elements
	$select_query = "SELECT identifier,filename,length,language FROM ".DB_PREFIX."page_movie WHERE page_id='$source_id' AND client_id='$client_id'";
	$result_pointer = $db_source->query($select_query);

	## loop through the results and copy them over
	while($db_source->next_record()) {
		$identifier = $db_source->Record['identifier'];
		$filename = $db_source->Record['filename'];
		$length = $db_source->Record['length'];
		$language = $db_source->Record['language'];
		
		## since it is possible that we get called muliple times for each datatype that stores the data into our tables,
		## we need to check if the entry already exists
		$query = "SELECT file_id FROM ".DB_PREFIX."page_movie WHERE page_id = '$target_id' AND identifier = '$identifier' AND client_id = '$client_id' AND language='$input_language'";
		$result_pointer = $db_target->query($query);			
	
		if($db_target->num_rows() == 0) { 
			## we haven't copied the element yet- so we first need to create a copy of the file first
			if(!empty($filename)) {
				$f = new file_object(); 
				$filename = $f->copy_file(MATRIX_UPLOADDIR_DOCS.$filename,$filename,MATRIX_UPLOADDIR_DOCS);
			}
			$query = "INSERT INTO ".DB_PREFIX."page_movie (page_id, identifier, filename,length,client_id,language) values ('$target_id', '$identifier','$filename', '$length','$client_id','$language')";
			$result_pointer = $db_target->query($query);
		}
	}	
}

?>
