<?PHP
## ======================================================================= 
##   webmatrix                                                     
## ======================================================================= 
##   Copyright (c) 2001, 2002, 2003 Stefan Elsner, Workmatrix        
## ======================================================================= 
##   <info@workmatrix.de> 
##
## ======================================================================= 

## get the clients functionality
include_once(ENGINE.'modules/clients/functions/elements.php');

## =======================================================================        
## clients_selector_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function clients_selector_displayInput($xmldata, $data) {
	## init the vars
	$return = "";
	##var_dump($xmldata);
	## we should open our own template
	$template = new Template(ENGINE."datatypes/extra_clients_selector/interface/");
	$template->set_templatefile(array("textselector" => "interface.tpl"));
	
	## set the vars
	$template->set_var('element_name',$xmldata['NAME']);
	$template->set_var('element_desc',$xmldata['DESC']);
	
	## we got your record to process the data

	## now it's time to check the previous data entered
	$value = convert_html($data['id']);
	## we need to get the options
	$options = clients_getClients('',0,10,1);
	$options = $options['data'];	

	$output = '<select name="'.$xmldata['NAME'].'" size="1">';
	$output .= '<option label="Please select" value="-1">Please select</option>';
	foreach($options as $current_option) {
		if($value == $current_option['id']) {
			$output .= '<option label="'.$current_option['firstname'].' '.$current_option['lastname'].'" value="'.$current_option['id'].'" selected>'.$current_option['firstname'].' '.$current_option['lastname'].'</option>';
		} else {
			$output .= '<option label="'.$current_option['firstname'].' '.$current_option['lastname'].'" value="'.$current_option['id'].'">'.$current_option['firstname'].' '.$current_option['lastname'].'</option>';
		}
	}

	$output .= '</select>';	
	
	## set the vars accordingly
	$template->set_var('value',$output);	
	
	if(!$xmldata['TAG']) {
		$template->set_var('element_tag',LANG_ElementText);
	} else {
		$template->set_var('element_tag',$xmldata['TAG']);
	}	
	
	return $template->fill_block("textselector");
}


## =======================================================================        
##  clients_selector_storeData        
## =======================================================================        
## save the content in the db
## ======================================================================= 
function clients_selector_storeData($page_id, $identifier) {
	global $Auth,$input_language;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	if(!isset($_POST[$identifier])) {
		return '';
	}
	
	## get the data
	$id = $_POST[$identifier];

	## prepare the db-object
	$db_connectionStore = new DB_Sql();

	## first we need to find out if the entry already exists
	$select_query = "SELECT id FROM ".DB_PREFIX."page_client_selector WHERE page_id = '$page_id' AND identifier = '$identifier' AND client_id = '$client_id' AND language='$input_language'";
	$result_pointer = $db_connectionStore->query($select_query);	

	if($db_connectionStore->num_rows() == 0) { 
		## no entry found
		$insert_query = "INSERT INTO ".DB_PREFIX."page_client_selector (page_id, identifier, id,client_id,language) values ('$page_id', '$identifier', '$id','$client_id','$input_language')";
		$result_pointer = $db_connectionStore->query($insert_query);

	} else {
		$db_connectionStore->next_record();
		$content_id = $db_connectionStore->Record["id"];
		$update_query = "UPDATE ".DB_PREFIX."page_client_selector SET id = '$id' WHERE id = '$content_id' AND client_id = '$client_id'";
		$result_pointer = $db_connectionStore->query($update_query);

	}
}

## =======================================================================        
##  clients_selector_getData        
## =======================================================================        
##  get the Data
## ======================================================================= 
function clients_selector_getData($vPageID,&$page_record) {
	global $Auth,$input_language;
	
	## multiclient
	$client_id = $Auth->auth['client_id'];

	## data connection
	$db_connectionMain = new DB_Sql();
	## get all text elements
	$select_query = "SELECT id, identifier FROM ".DB_PREFIX."page_client_selector WHERE page_id='$vPageID' AND client_id='$client_id' AND language='$input_language'";
	$result_pointer = $db_connectionMain->query($select_query);

	## loop through the results and set the vars in the template
	while($db_connectionMain->next_record()) {
		$varname = $db_connectionMain->Record['identifier'];
		$page_record[$varname]['type'] = "CLIENT_SELECTOR";
		$page_record[$varname]['id'] = $db_connectionMain->Record['id'];
		$page_record[$varname]['identifier'] = $varname; 
	}
}

## =======================================================================        
##  clients_selector_deleteData        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function clients_selector_deleteData($vPageID) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## data connection
	$db_connectionMain = new DB_Sql();
	## get all text elements
	$query = "DELETE FROM ".DB_PREFIX."page_client_selector WHERE page_id='$vPageID' AND client_id='$client_id'";
	$result_pointer = $db_connectionMain->query($query);
}

## =======================================================================        
##  clients_selector_output        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function clients_selector_output($item,$structure,$menu_id) {
	return $item['id'];
}

## =======================================================================        
##  clients_selector_displayPreview        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function clients_selector_displayPreview($xmldata, $data) {	
	return $item['id'];
}

## =======================================================================        
##  clients_selector_copyData        
## =======================================================================        
##  pass it the sourcepage_id and the targetpage_id. It'll copy all
##  text entries to the new page.
## ======================================================================= 
function clients_selector_copyData($source_id, $target_id) {
	global $Auth;
	## multiclient
	$client_id = $Auth->auth['client_id'];	

	## data connection
	$db_source = new DB_Sql();
	$db_target = new DB_Sql();
	
	## get all text elements
	$select_query = "SELECT identifier,id,language FROM ".DB_PREFIX."page_client_selector WHERE page_id='$source_id' AND client_id='$client_id'";
	$result_pointer = $db_source->query($select_query);

	## loop through the results and copy them over
	while($db_source->next_record()) {
		$identifier = $db_source->Record['identifier'];
		$id = $db_source->Record['id'];
		$language = $db_source->Record['language'];
		
		## since it is possible that we get called muliple times for each datatype that stores the data into our tables,
		## we need to check if the entry already exists
		$query = "SELECT id FROM ".DB_PREFIX."page_client_selector WHERE page_id = '$target_id' AND identifier = '$identifier' AND client_id = '$client_id' AND language='$input_language'";
		$result_pointer = $db_target->query($query);			
	
		if($db_target->num_rows() == 0) { 
			$query = "INSERT INTO ".PAGE_CONTENT." (page_id, identifier, id,client_id,language,modified) values ('$target_id', '$identifier', '$id','$client_id','$language',now())";
			$result_pointer = $db_target->query($query);
		}
	}	
	
}

?>
