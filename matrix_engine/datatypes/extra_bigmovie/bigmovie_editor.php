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
##  linklist_promptDelete        
## =======================================================================        
function bigmovie_selectFileDialog($page_id,$identifier) {
	global $gSession,$Auth,$input_language;

	## prepare the template file
	$input_template = new Template(ENGINE."datatypes/extra_bigmovie/interface");
	$input_template->set_templatefile(array("body" => "selectfile.tpl"));
	
	## data connection
	$db_connectionMain = new DB_Sql();
	## get all text elements
	$select_query = "SELECT movie_id,filename,width,height,autoplay, identifier FROM ".DB_PREFIX."page_movie WHERE page_id='$page_id' AND identifier='$identifier'";
	$result_pointer = $db_connectionMain->query($select_query,true);


	## loop through the results and set the vars in the template
	if($db_connectionMain->next_record()) {
		$width = $db_connectionMain->Record["width"];	
		$height = $db_connectionMain->Record["height"];	
		$autoplay = $db_connectionMain->Record["autoplay"];
		$filename = $db_connectionMain->Record["filename"];
	}
	
	$input_template->set_var('width',$width);
	$input_template->set_var('height',$height);
	$input_template->set_var('filename',$filename);
	

	if ($autoplay == 1) {
		$input_template->set_var('autoplay','checked="checked"');
	}
	
	## language
	$input_template->set_var("saveIMG",$Auth->auth["language"]."_button_save.gif");
	$input_template->set_var('language_deleteelementdesc',LANG_DeleteElementDescription);
	$input_template->set_var('language_inputhead',LANG_movie_EnterData);
	$input_template->set_var('language_inputbody',LANG_movie_EnterDataDescription);
	
	$input_template->set_var('element_name',$identifier);
	
	$actionURL = "editor.php";
	$actionURL = $gSession->url($actionURL);
	$input_template->set_var('actionURL',$actionURL);

	## prepare the process url
	$processURL = SITE."datatypes/extra_bigmovie/editor.php?op=process&page_id=".$page_id."&identifier=".$identifier."&language=".$input_language;
	$processURL = $gSession->url($processURL);
	$input_template->set_var('processURL',$processURL);
	
	## we need to generate a unique identifier 
	$hash = md5(uniqid(rand()));
	$input_template->set_var('HASH',$hash);
		
	## here we initialize the hiddenfields	
	$output =  '<input type="hidden" name="op" value="store">';
	$output .= '<input type="hidden" name="language" value="'.$input_language.'">';	
	$output .= '<input type="hidden" name="pageID" value="'.$page_id.'">';
	$output .= '<input type="hidden" name="identifier" value="'.$identifier.'">';
	$output .= '<input type="hidden" name="Session" value="'.$gSession->id.'">';
	$input_template->set_var("hiddenfields",$output);

	$input_template->pfill_block("body");
}	


## =======================================================================        
##  linklist_promptDelete        
## =======================================================================        
function bigmovie_storeFile($file,$page_id, $identifier) {
	global $Auth,$input_language;
	## multiclient

	var_dump($file);
	
	$client_id = $Auth->auth["client_id"];
	$height		= intval($_POST[$identifier."_HEIGHT"]);
	$width		= intval($_POST[$identifier."_WIDTH"]);
	$autoplay	= intval($_POST[$identifier."_autoplay"]);

	// If uploading a new video file
	if ($file['name'] != '') {

	    $old_filename = $file['name'];
	    $filename = time();
	    $rand_num = rand('100','999');
	    $filename = $filename . $rand_num . substr($file['name'],-4,4);

	    $target_path = MATRIX_UPLOADDIR_DOCS . $filename;
		
	    if(!move_uploaded_file($file['tmp_name'], $target_path)) {
		echo "There was an error uploading the file, please try again!";
		exit;
	    }

	}

	## for security and convenience reasons we have to convert the
	## supplied string

	## prepare the db-object
	$db_connectionStore = new DB_Sql();

	$select_query = "SELECT movie_id,filename FROM ".DB_PREFIX."page_movie WHERE page_id = '$page_id' AND identifier = '$identifier' AND client_id='$client_id'";
	$result_pointer = $db_connectionStore->query($select_query);	

	if($db_connectionStore->num_rows() == 0) { 
		## no entry found
		$insert_query = "INSERT INTO ".DB_PREFIX."page_movie (page_id, identifier,filename,width,height,client_id,autoplay) values ('$page_id', '$identifier','$filename', '$width', '$height','$client_id','$autoplay')";
		$result_pointer = $db_connectionStore->query($insert_query);
	} else {
		$db_connectionStore->next_record();
		$file_id = $db_connectionStore->Record["movie_id"];
		$old_filename = $db_connectionStore->Record["filename"];
		
		$update_query = "UPDATE ".DB_PREFIX."page_movie SET filename='$old_filename', width='$width', height='$height', autoplay='$autoplay' WHERE movie_id = '$file_id' AND client_id='$client_id'";
		$result_pointer = $db_connectionStore->query($update_query);
	}
}


## =======================================================================        
##  movie_prepareFile        
## ======================================================================= 
##
##  we are basically getting a hash- using this hash we need to 
##  find all info about the uploaded file- in order to allow
##  smooth integration with the existing upload/store method- we
##  will pre-populate the _FILES vars
## ======================================================================= 
function bigmovie_prepareFile($hash,$identifier) {
	## first we sanitze the hash
	$hash = ereg_replace("[^a-zA-Z0-9]","",$hash);
	
	## then get the info file from the tmp directory
	$file = '/tmp/'.$hash.'_qstring';
	
	## check if it exists
	if(!file_exists($file)) {
		return false;
	}
	
	## now fetch the query string and split it up
	$query_string = file_get_contents($file);
	$query = array();
	parse_str($query_string,$query);
	
	## we only support one file- try to make sure we don't have any directory 
	## params in the filename
  	$filename = $query['file']['name'][0];
  	$b_pos = strrpos($filename, '\\');$f_pos = strrpos($filename, '/');
  	if($b_pos == false and $f_pos == false) {
  		$_FILES[$identifier]['name'] = $filename;
  	} else {
  		$_FILES[$identifier]['name'] = substr($filename, max($b_pos,$f_pos)+1);
  	}	
  	
	$_FILES[$identifier]['tmp_name'] = $query['file']['tmp_name'][0];
	$_FILES[$identifier]['size'] = $query['file']['size'][0];
}

## =======================================================================        
##  linklist_promptDelete        
## =======================================================================        
function file_promptDelete($yesURL,$noURL) {
	global $gSession,$Auth;
	$db_connectionLayout = new DB_Sql();  

	## prepare the template file
	$select_template = new Template(ENGINE."datatypes/extra_bigmovie/interface");
	$select_template->set_templatefile(array("body" => "deletelink.tpl"));
	
	$select_template->set_var("yesIMG","lang/".$Auth->auth["language"]."_button_ja.gif");
	$select_template->set_var("noIMG","lang/".$Auth->auth["language"]."_button_nein.gif");
	$select_template->set_var('language_deletepage','Delete File');
	$select_template->set_var('language_doyouwant','Do you really want to delete this mp3 file');
	
	## grab the information for this page
  		
	$select_template->set_var('yesURL',$yesURL);
	$select_template->set_var('noURL',$noURL);
	
	$select_template->pfill_block("body");
}	


## =======================================================================        
##  file_doDelete        
## =======================================================================        
function file_delete() {
	global $Auth,$input_language;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	$db = new DB_Sql();
	$f = new file_object();
	
	## we need a page id and an identifier
	$page_id = intval($_GET['page_id']);
	$identifier = $_GET['identifier'];
	
	## first get the image id for this image
	$query = "SELECT movie_id,filename FROM ".DB_PREFIX."page_movie WHERE page_id='$page_id' AND identifier='$identifier'";
	$result_pointer = $db->query($query,true);
	
	if($db->next_record()) {
		$file_id = $db->Record['movie_id'];
		$filename = $db->Record['filename'];
		## okay it's used only once- let's delete the base entry
		$query = "DELETE FROM ".DB_PREFIX."page_movie WHERE movie_id=".$file_id;
		$result_pointer = $db->query($query,true);

		# okay this means we can delete the file itself too
		$f->delete_file(MATRIX_UPLOADDIR.$filename);		
	}
}
	
?>
