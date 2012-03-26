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
## metatags_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function metatags_displayInput($xmldata, $data) {
	global $Auth,$input_language;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	## init the vars
	$return = "";
	## we should open our own template
	$template = new Template(ENGINE."datatypes/extra_metatags/interface/");
	$template->set_templatefile(array("customselector" => "interface.tpl"));

	## we need to load the language specific strings
	include(ENGINE."datatypes/extra_metatags/interface/lang/".$Auth->auth["language"].".php");
	
	## set the text elements
	$template->set_var('LANG_DATATYPE_META_Title',LANG_DATATYPE_META_Title);
	$template->set_var('LANG_DATATYPE_META_PageTitle',LANG_DATATYPE_META_PageTitle);
	$template->set_var('LANG_DATATYPE_META_PageTitleDesc',LANG_DATATYPE_META_PageTitleDesc);

	$template->set_var('LANG_DATATYPE_META_PageDesc',LANG_DATATYPE_META_PageDesc);
	$template->set_var('LANG_DATATYPE_META_PageDescDesc',LANG_DATATYPE_META_PageDescDesc);
	
	$template->set_var('LANG_DATATYPE_META_Keywords',LANG_DATATYPE_META_Keywords);
	$template->set_var('LANG_DATATYPE_META_KeywordsDesc',LANG_DATATYPE_META_KeywordsDesc);	

	## set the vars
	$template->set_var('element_name',$xmldata['NAME']);
	$template->set_var('element_desc',$xmldata['DESC']);

	## here is a list of vars we will process
	$identifiers = array('META_title'=>'DEFAULT:TITLE','META_description'=>'DEFAULT:DESCRIPTION','META_keywords'=>'DEFAULT:KEYWORDS');
	
	## okay output the values that where previously entered
	foreach($identifiers as $identifier=>$default_data) {
		$text = isset($data[$identifier]) ? $data[$identifier]['text'] : '';
		$template->set_var($identifier,$text);
	}
	
	## set the vars accordingly
	$template->set_var('value',$output);	
	$template->set_var('element_name',$xmldata['NAME']);
	
	if(!$xmldata['TAG']) {
		$template->set_var('element_tag',LANG_ElementText);
	} else {
		$template->set_var('element_tag',$xmldata['TAG']);
	}	
	
	return $template->fill_block("customselector");
}


## =======================================================================        
##  metatags_storeData        
## =======================================================================        
## save the content in the db
## ======================================================================= 
function metatags_storeData($page_id, $identifier) {
	global $Auth,$input_language;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## prepare the return value
	$return_value = false;
	
	## prepare the db-object
	$db = new DB_Sql();	
	
	## here is a list of vars we will process (used for the database calls)
	$identifiers = array('META_title','META_description','META_keywords');
	
	## if the value for a field equals the defaul field- we won't update it
	## get the default data
	$default_values = _metatags_getDefaultData($page_id);
	$current_var = '';
	foreach($identifiers as $current_identifier) {
		if($default_values['METATAGS'][$current_identifier]['text'] != $_POST[$current_identifier]) {
			$current_var = addslashes($_POST[$current_identifier]);
	
			## first check if there already is a entry for this var
			$query = "SELECT content_id FROM ".PAGE_CONTENT." WHERE page_id = '$page_id' AND identifier = '$current_identifier' AND client_id = '$client_id' AND language='$input_language'";
			$result_pointer = $db->query($query);	
	
			if($db->num_rows() == 0) { 
				## no entry found- create a new one
				$query = "INSERT INTO ".PAGE_CONTENT." (page_id, identifier ,text, modified,client_id,language) values ('$page_id', '$current_identifier', '$current_var',now(),'$client_id','$input_language')";
				$result_pointer = $db->query($query);
				$return_value = true;
			} else {
				## there is an entry, now check if the text was updated.
				$db->next_record();
				$content_id = $db->Record["content_id"];
		
				## this means we are updateing a text-element within the same session.
				$query = "UPDATE ".PAGE_CONTENT." SET text='$current_var', modified=now() WHERE content_id = '$content_id'";
				$rp = $db->query($query);
			}
		}
	}
	return $return_value;
}

## =======================================================================        
##  metatags_getData        
## =======================================================================        
##  get the Data
## ======================================================================= 
function metatags_getData($vPageID,&$page_record) {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth['client_id'];

	## data connection
	$db_connectionMain = new DB_Sql();
	
	## get all text elements
	$select_query = "SELECT text, identifier FROM ".PAGE_CONTENT." WHERE page_id='$vPageID' AND client_id='$client_id' AND (identifier='META_title' OR identifier='META_description' OR identifier='META_keywords')";
	$result_pointer = $db_connectionMain->query($select_query);
	## loop through the results and set the vars in the template
	$varname = '';
	while($db_connectionMain->next_record()) {
		$text = $db_connectionMain->Record['text'];
		$varname = $db_connectionMain->Record['identifier'];
		$page_record['METATAGS'][$varname]['type'] = "METATAGS";
		$page_record['METATAGS'][$varname]['text'] = stripslashes($text); 
		$page_record['METATAGS'][$varname]['page_id'] = $vPageID; 
	}
	
	
	## check if we have an empty field - if yes we will fill it with the default values
	
	## first get the default values
	$default_values = _metatags_getDefaultData($vPageID);
	if(!empty($default_values)) {		
		if(empty($page_record['METATAGS']['META_title']['text'])) {
			$page_record['METATAGS']['META_title']['type'] = "METATAGS";
			$page_record['METATAGS']['META_title']['text'] = $default_values['METATAGS']['META_title']['text'];
			$page_record['METATAGS']['META_title']['page_id'] = $default_values['METATAGS']['META_title']['page_id']; 
		}

		if(empty($page_record['METATAGS']['META_description']['text'])) {
			$page_record['METATAGS']['META_description']['type'] = "METATAGS";
			$page_record['METATAGS']['META_description']['text'] = $default_values['METATAGS']['META_description']['text']; 
			$page_record['METATAGS']['META_description']['page_id'] = $default_values['METATAGS']['META_description']['page_id']; 
		}
		
		if(empty($page_record['METATAGS']['META_keywords']['text'])) {		
			$page_record['METATAGS']['META_keywords']['type'] = "METATAGS";
			$page_record['METATAGS']['META_keywords']['text'] = $default_values['METATAGS']['META_keywords']['text']; 
			$page_record['METATAGS']['META_keywords']['page_id'] = $default_values['METATAGS']['META_keywords']['page_id']; 
		}
	}
		
}

## =======================================================================        
##  metatags_getData        
## =======================================================================        
##  get the Data
## ======================================================================= 
function _metatags_getDefaultData($vPageID) {	
	## check if we have a seo settings file
	$data = array();
	if(file_exists(MATRIX_BASEDIR."settings/modules/seo/settings.php")) {
		include(MATRIX_BASEDIR."settings/modules/seo/settings.php");
		
		$data['METATAGS']['META_title']['type'] = "METATAGS";
		$data['METATAGS']['META_title']['text'] = stripslashes($module_seo["TITLE"]); 
		$data['METATAGS']['META_title']['page_id'] = $vPageID; 

		$data['METATAGS']['META_description']['type'] = "METATAGS";
		$data['METATAGS']['META_description']['text'] = stripslashes($module_seo["DESCRIPTION"]); 
		$data['METATAGS']['META_description']['page_id'] = $vPageID; 
		
		$data['METATAGS']['META_keywords']['type'] = "METATAGS";
		$data['METATAGS']['META_keywords']['text'] = stripslashes($module_seo["KEYWORDS"]); 
		$data['METATAGS']['META_keywords']['page_id'] = $vPageID; 
	}
	
	return $data;
		
}

## =======================================================================        
##  metatags_deleteData        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function metatags_deleteData($vPageID) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## data connection
	$db_connectionMain = new DB_Sql();
	## get all text elements
	$query = "DELETE FROM ".PAGE_CONTENT." WHERE page_id='$vPageID' AND client_id='$client_id'";
	$result_pointer = $db_connectionMain->query($query);
}

## =======================================================================        
##  output_text        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function metatags_output($data,$structure,$menu_id) {
	## prepare the return values
	$output = array();
	
	## loop through all entries
	foreach($data as $identifier =>$current_data) {
		$output['matrix:'.$identifier] = stripslashes($current_data['text']);
	}
	return $output;
}


## =======================================================================        
##  metatags_copyData        
## =======================================================================        
##  pass it the sourcepage_id and the targetpage_id. It'll copy all
##  text entries to the new page.
## ======================================================================= 
function metatags_copyData($source_id, $target_id) {
	## we need to check if this works for this datatype
	text_copyData($source_id, $target_id);	
}

?>
