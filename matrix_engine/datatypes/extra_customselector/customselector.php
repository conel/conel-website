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
## customselector_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function customselector_displayInput($xmldata, $data) {
	global $Auth,$input_language;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	## init the vars
	$return = "";
	## we should open our own template
	$template = new Template(ENGINE."datatypes/extra_customselector/interface/");
	$template->set_templatefile(array("customselector" => "interface.tpl"));
	
	## set the vars
	$template->set_var('element_name',$xmldata['NAME']);
	$template->set_var('element_desc',$xmldata['DESC']);
	
	## we got your record to process the data
	
	## now it's time to check the previous data entered
	$value = $data['text'];
	
	$db_connectionStore = new DB_Sql();
	$select_query = "SELECT text FROM ".PAGE_CONTENT." WHERE identifier = '".$xmldata['NAME']."' AND client_id = '$client_id' AND language='$input_language' GROUP BY (text)";
	$result_pointer = $db_connectionStore->query($select_query);	
	
	$options = array();
	if($db_connectionStore->num_rows() != 0) { 
		while($db_connectionStore->next_record()) {
			$options[] = $db_connectionStore->Record["text"];
		}
	}
		
	$output = '<select name="'.$xmldata['NAME'].'" size="1">';
	for($i=0;$i<count($options); $i++) {
		## set the option
		if($value == $options[$i]) {
			$output .= '<option label="'.$options[$i].'" value="'.$options[$i].'" selected>'.$options[$i].'</option>';
		} else {
			$output .= '<option label="'.$options[$i].'" value="'.$options[$i].'">'.$options[$i].'</option>';
		}	
	}
	$output .= '</select>';	
	
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
##  customselector_storeData        
## =======================================================================        
## save the content in the db
## ======================================================================= 
function customselector_storeData($page_id, $identifier) {
	global $Auth,$input_language;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	$return_value = false;
	
	## if we've got a new entry we will enter it into the db
	$text = isset($_POST[$identifier.'_newentry']) ? (!empty($_POST[$identifier.'_newentry']) ? $_POST[$identifier.'_newentry']:$_POST[$identifier]) : $_POST[$identifier];	
	## for security and convenience reasons we have to convert the supplied string
	$text = $text;
	$text = addslashes($text);
	## prepare the db-object
	$db_connectionStore = new DB_Sql();

	## first we need to find out if the entry already exists
	$select_query = "SELECT content_id,text,UNIX_TIMESTAMP(modified) AS modified FROM ".PAGE_CONTENT." WHERE page_id = '$page_id' AND identifier = '$identifier' AND client_id = '$client_id' AND language='$input_language' ORDER BY modified DESC";
	$result_pointer = $db_connectionStore->query($select_query);	

	if($db_connectionStore->num_rows() == 0) { 
		## no entry found- create a new one
		$insert_query = "INSERT INTO ".PAGE_CONTENT." (page_id, identifier ,text, modified,client_id,language) values ('$page_id', '$identifier', '$text',FROM_UNIXTIME('$timestamp'),'$client_id','$input_language')";
		$result_pointer = $db_connectionStore->query($insert_query);
		$return_value = true;
	} else {
		## there is an entry, now check if the text was updated.
		$db_connectionStore->next_record();
		$content_id = $db_connectionStore->Record["content_id"];

		## this means we are updateing a text-element within the same session.
		$query = "UPDATE ".PAGE_CONTENT." SET text='$text' WHERE content_id = '$content_id'";
		$result_pointer = $db_connectionStore->query($query);

	}
	
	return $return_value;
}

## =======================================================================        
##  customselector_getData        
## =======================================================================        
##  get the Data
## ======================================================================= 
function customselector_getData($vPageID,&$page_record) {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth['client_id'];

	## data connection
	$db_connectionMain = new DB_Sql();
	
	## get all text elements
	$select_query = "SELECT text, identifier FROM ".PAGE_CONTENT." WHERE page_id='$vPageID' AND client_id='$client_id' ORDER BY identifier";
	$result_pointer = $db_connectionMain->query($select_query);
	## loop through the results and set the vars in the template
	$varname = '';
	while($db_connectionMain->next_record()) {
		$text = $db_connectionMain->Record['text'];
		if($varname != $db_connectionMain->Record['identifier']) {
			$varname = $db_connectionMain->Record['identifier'];
			$page_record[$varname]['type'] = "TEXT";
			$page_record[$varname]['identifier'] = $varname;
			$page_record[$varname]['text'] = $text; 
			$page_record[$varname]['page_id'] = $vPageID; 
		}
	}
}

## =======================================================================        
##  customselector_deleteData        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function customselector_deleteData($vPageID) {
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
function customselector_output($item,$structure,$menu_id) {
	$value = stripslashes($item['text']);
	return $value;
}


## =======================================================================        
##  customselector_copyData        
## =======================================================================        
##  pass it the sourcepage_id and the targetpage_id. It'll copy all
##  text entries to the new page.
## ======================================================================= 
function customselector_copyData($source_id, $target_id) {
	text_copyData($source_id, $target_id);	
}

?>
