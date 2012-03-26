<?PHP
## ======================================================================= 
##   webmatrix                                                     
## ======================================================================= 
##   Copyright (c) 2001, 2002, 2003, 2004 Stefan Elsner, Workmatrix        
## ======================================================================= 
##   <info@workmatrix.de> 
## ======================================================================= 

## =======================================================================        
## text_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function text_displayInput($xmldata, $data) {	
	## init the vars
	$return = "";
	
	## open our own template
	$template = new Template(ENGINE."datatypes/text/interface/");
	$template->set_templatefile(array("text" => "interface.tpl"));
	
	## set the vars
	$template->set_var('element_name',$xmldata['NAME']);
	$template->set_var('element_desc',$xmldata['DESC']);

	## process the data
	$value = convert_html($data['text']);
	
	if($value != "") {
		## set the vars accordingly
		$template->set_var('value',htmlentities(utf8_decode($value)));	
	} else {
		$template->set_var('value',$xmldata['DEFAULT']);	
	}
	if(!$xmldata['TAG']) {
		$template->set_var('element_tag',LANG_ElementText);
	} else {
		$template->set_var('element_tag',$xmldata['TAG']);
	}
							
	return $template->fill_block("text");
}


## =======================================================================        
##  text_storeData        
## =======================================================================        
## save the content in the db
## ======================================================================= 
function text_storeData($page_id, $identifier) {
	global $Auth,$input_language;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	if(!isset($_POST[$identifier])) {

		return '';
	}
	$text = $_POST[$identifier];

	## for security and convenience reasons we have to convert the supplied string
	$text = addslashes($text);
	## prepare the db-object
	$db_connectionStore = new DB_Sql();

	## first we need to find out if the entry already exists
	$select_query = "SELECT content_id FROM ".PAGE_CONTENT." WHERE page_id = '$page_id' AND identifier = '$identifier' AND client_id = '$client_id' AND language='$input_language'";
	$result_pointer = $db_connectionStore->query($select_query);	
	
	if($db_connectionStore->num_rows() == 0) { 
		## no entry found
		$insert_query = "INSERT INTO ".PAGE_CONTENT." (page_id, identifier, text,client_id,language) values ('$page_id', '$identifier', '$text','$client_id','$input_language')";
		$result_pointer = $db_connectionStore->query($insert_query);
	} else {
		$db_connectionStore->next_record();
		$content_id = $db_connectionStore->Record["content_id"];
		$update_query = "UPDATE ".PAGE_CONTENT." SET text = '$text' WHERE content_id = '$content_id' AND client_id = '$client_id'";
		$result_pointer = $db_connectionStore->query($update_query);
	}
}

## =======================================================================        
##  text_getData        
## =======================================================================        
##  get the Data
## ======================================================================= 
function text_getData($vPageID,&$page_record) {
	global $Auth,$input_language;
	
	## multiclient
	$client_id = $Auth->auth['client_id'];

	## data connection
	$db_connectionMain = new DB_Sql();
	## get all text elements
	$select_query = "SELECT text, identifier FROM ".PAGE_CONTENT." WHERE page_id='$vPageID' AND client_id='$client_id' AND language='$input_language'";
	$result_pointer = $db_connectionMain->query($select_query);

	## loop through the results and set the vars in the template
	while($db_connectionMain->next_record()) {
		$text = $db_connectionMain->Record['text'];
		$varname = $db_connectionMain->Record['identifier'];
		$page_record[$varname]['type'] = "TEXT";
		$page_record[$varname]['text'] = stripslashes($text); 
		$page_record[$varname]['identifier'] = $varname; 
	}
}
## =======================================================================        
##  text_deleteData        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function text_deleteData($vPageID) {
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
function output_text($item,$structure,$menu_id) {
	## we support 'texteffects'- they are currently not multitlanguage
	## we need to honor the 'setlocale' state!
	if(isset($structure['EFFECT'])) {
		switch($structure['EFFECT']) {
			case 'UPPER':
				$item['text'] = strtoupper($item['text']);
				break;
			case 'LOWER':
				$item['text'] = strtolower($item['text']);
				break;
		}
	}

	return $item['text'];
}

## =======================================================================        
##  text_displayPreview        
## =======================================================================        
##  is called by datatypes that need to 
##  display a part of a dataype (e.g. box)
## ======================================================================= 
function text_displayPreview($xmldata, $data) {
	$value = convert_html($data['text']);
	return $value;

}

## =======================================================================        
##  text_copyData        
## =======================================================================        
##  pass it the sourcepage_id and the targetpage_id. It'll copy all
##  text entries to the new page.
## ======================================================================= 
function text_copyData($source_id, $target_id) {
	global $Auth;
	## multiclient
	$client_id = $Auth->auth['client_id'];	

	## data connection
	$db_source = new DB_Sql();
	$db_target = new DB_Sql();
	
	## get all text elements
	$select_query = "SELECT identifier,text,language FROM ".PAGE_CONTENT." WHERE page_id='$source_id' AND client_id='$client_id'";
	$result_pointer = $db_source->query($select_query);

	## loop through the results and copy them over
	while($db_source->next_record()) {
		$identifier = $db_source->Record['identifier'];
		$text = mysql_escape_string($db_source->Record['text']);
		$language = $db_source->Record['language'];
		
		## since it is possible that we get called muliple times for each datatype that stores the data into our tables,
		## we need to check if the entry already exists
		$query = "SELECT content_id FROM ".PAGE_CONTENT." WHERE page_id = '$target_id' AND identifier = '$identifier' AND client_id = '$client_id' AND language='$input_language'";
		$result_pointer = $db_target->query($query);			
	
		if($db_target->num_rows() == 0) { 
			$query = "INSERT INTO ".PAGE_CONTENT." (page_id, identifier, text,client_id,language,modified) values ('$target_id', '$identifier', '$text','$client_id','$language',now())";
			$result_pointer = $db_target->query($query,true);
		}
	}	
}

?>
