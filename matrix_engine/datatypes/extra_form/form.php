<?PHP
## ======================================================================= 
##   webmatrix                                                     
## ======================================================================= 
##   Copyright (c) 2001, 2002, 2003 Stefan Elsner, Workmatrix        
## ======================================================================= 
##   <info@workmatrix.de> 
##
## ======================================================================= 
require_once(ENGINE.'modules/clients/classes/ctlparser.php');
require_once(ENGINE.'classes/class_formparser.php');
require_once(ENGINE.'classes/class_validate.php');
## =======================================================================        
## form_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function form_displayInput($xmldata, $data) {
}


## =======================================================================        
##  form_storeData        
## =======================================================================        
## save the content in the db
## ======================================================================= 
function form_storeData($page_id, $identifier) {
	return false;
}
## =======================================================================        
##  form_getData        
## =======================================================================        
##  get the Data
## ======================================================================= 
function form_getData($vPageID,&$page_record) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	$db_connectionMain = new DB_Sql();  

	## now for the linklistitems
	$query = "SELECT link_list_identifier,link_list_id FROM ".LINKLIST." WHERE page_id='$vPageID' AND client_id='$client_id' ORDER BY link_list_identifier";

	$result_pointer = $db_connectionMain->query($query);

	## loop through the resuls and set the vars accordingly
	$db_connection = new DB_Sql();
	$old_identifier="";
	while($db_connectionMain->next_record()) {
		## we need to get the items associated with each linkList on this page
		$identifier = $db_connectionMain->Record["link_list_identifier"];
		$link_list_id = $db_connectionMain->Record["link_list_id"];	
		
		## check if it is a new entry
		if($identifier != $old_identifier) {
			##$counter = 1;
			$old_identifier = $identifier; 
		} 
		## add this info to the container
		$page_record[$identifier]["type"] = "LINKLIST";
		$page_record[$identifier]["id"] = $link_list_id;	
		
		$itemCounter = 0;
		## let's get the page name for each linklistitem
		$select_query = "SELECT page_id,structure_text, link_list_item_id,link_list_item_order FROM ".LINKLISTITEM." INNER JOIN ".STRUCTURE." ON ".STRUCTURE.".page_id=".LINKLISTITEM.".link_list_item_target WHERE link_list_id='$link_list_id' AND ".LINKLISTITEM.".client_id='$client_id' ORDER BY link_list_item_order";
		$result_pointer = $db_connection->query($select_query);	
		while($db_connection->next_record()) {	
			$text = $db_connection->Record["structure_text"];
			$link = $db_connection->Record["link_list_item_id"];
			$page_id = $db_connection->Record["page_id"];
			$order = $db_connection->Record["link_list_item_order"];
			
			$page_record[$identifier][$itemCounter]["text"] = $text; 
			$page_record[$identifier][$itemCounter]["link"] = $link; 
			$page_record[$identifier][$itemCounter]["page_id"] = $page_id;
			$page_record[$identifier][$itemCounter]["order"] = $order;
			$itemCounter++;			
		}		
	$page_record[$identifier]["length"] = $itemCounter;
	}
}

## =======================================================================        
##  form_deleteData        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function form_deleteData($vPageID) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## data connection
	$db_connection = new DB_Sql();
	
	## we need to find out which linklistitems are related to the current pageID
	## first we should get the linklist
	$query = "SELECT link_list_id FROM ".LINKLIST." WHERE page_id = '$vPageID' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($query);
	
	## get the id
	$db_connection->next_record();
	$vLinkListID = $db_connection->Record["link_list_id"];

	## let's delete the items
	$query = "DELETE FROM ".LINKLISTITEM." WHERE link_list_id='$vLinkListID' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($query);

	## now delete the linklist
	$query = "DELETE FROM ".LINKLIST." WHERE page_id='$vPageID' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($query);
}

## =======================================================================        
##  form_output        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function form_output($item,$structure,$template,$menu_id,$page_id) {
	// init the error codes
	$error_codes = array(0 => 'VALID',-11 =>'STRING_TO_SHORT',-12 =>'STRING_TO_LONG', -13 => 'STRING_WRONG_FORMAT', -14 => 'STRING_NODATA_ENTERED');

	// load the form/ctl file
	$formfile = MATRIX_BASEDIR.'settings/datatypes/extra_form/'.$structure['NAME'].'.xml';
	if(!file_exists($formfile)) {
		return;
	}
	
	// prepare the form file
	$ctl_form = new formparser($formfile);
	$ctl_form->parse();
		
	// chekc if the form is submitted otherwise prepare the form
	if(!empty($_POST[$structure['TRIGGER']])) {		
		$errors = _form_validateInput($structure,$ctl_form);

		// output any errors in the validation of the form
		if(is_array($errors)) {
			return $errors;
		}

		// form passed validation- no trigger the actions
		$actions = $ctl_form->getPostActions();
		$results = _form_processCommand($actions,$page_id);
	} else {
		// the form is not submitted  we need to call the prepare actions
		$actions = $ctl_form->getPrepareActions();
		$results = _form_processCommand($actions,$page_id);
	}
	
	if(isset($results['output'])) {
		return $results['output'];
	}
}

## =======================================================================        
## _form_validateInput        
## =======================================================================        
## validate the data passed by a from
## ======================================================================= 
function _form_processCommand($command,$page_id,$previous_results=array()) {
	foreach($command as $current_command) {
		## check if we can find the action
		$type = strtolower($current_command['type']);
		@include_once(ENGINE.'datatypes/extra_form/actions/'.$type.'.php');
		
		## chekc if we need to try the custom action folder
		if(!function_exists("extra_formAction_".$type)) {
			@include_once(MATRIX_BASEDIR.'settings/datatypes/extra_form/actions/'.$type.'.php');
		}
		
		## check if we have the function
		if(function_exists("extra_formAction_".$type)) {
			eval("\$previous_results = extra_formAction_".$type."(\$menu_id,\$page_id,\$current_command,\$previous_results);");	
		}
		
		## check the status of the function
		if($previous_results['status'] == true) {
			## check if we have any subcommands
			if(isset($current_command['succeeded'])) {
				## then we should execute the commands
				$previous_results = _form_processCommand($current_command['succeeded'],$page_id,$previous_results);
			}
		} else if($previous_results['status'] == false) {
			## check if we have any subcommands
			if(isset($current_command['failed'])) {
				## then we should execute the commands
				$previous_results =_form_processCommand($current_command['failed'],$page_id,$previous_results);
			}				
		}
	}
	
	return $previous_results;
}

## =======================================================================        
## _form_validateInput        
## =======================================================================        
## validate the data passed by a from
## ======================================================================= 
function _form_validateInput($structure,$ctl_form) {
	## prepare the error codes
	$error_codes = array(0 => 'VALID',-11 =>'STRING_TO_SHORT',-12 =>'STRING_TO_LONG', -13 => 'STRING_WRONG_FORMAT', -14 => 'STRING_NODATA_ENTERED');
		
	## fetch the data from the formfile
	$types = $ctl_form->getTypes();
	$fields = $ctl_form->getFields();
	$general_error = $ctl_form->getError();
	
	## for faster processing we preload the required attribute types
	foreach($types as $type=>$value) {
		$type = strtolower($type);
		@include_once(ENGINE.'modules/clients/attributetypes/'.$type.'/attribute.php');
	}
	
	## okay we are all set- now we loop through the elements and call their validation routines
	$output = array();
	$error_found = false;
	foreach($fields as $current_element) {
		$type = strtolower($current_element['type']);
		$identifier = $current_element['identifier'];
		$name = $current_element['name'];
		
		## make sure all elements are upper case
		$current_element = array_change_key_case($current_element,CASE_UPPER);

		## check if the function exists
		if(function_exists("clients_".$type."_validateInput")) {
			## no we call the function
			eval("\$element = clients_".$type."_validateInput(\$current_element,\$_POST);");
			## we need to stor the error code- if any error was returned- we
			## will return the appropriate error stirng as well as the data that
			## was previously entered.
			if($element['error'] == STRING_NODATA_ENTERED) {
				if($current_element['REQUIRED'] == true) {
					## we found an error- set the flag
					$error_found = true;
					
					$error_code = $error_codes[$element['error']];
					$error_string = utf8_encode($current_element['ERRORS'][$error_code]);
					
					$output[$structure['FORM'].':'.$identifier.'.error'] = $error_string;	
					$output[$structure['FORM'].':'.$identifier.'.highlight'] = 'error';
					$output[$structure['FORM'].':ERROR'] = $general_error;		
				}
			} else if($element['error'] != VALIDATE_VALID) {
				## we found an error- set the flag
				$error_found = true;
				
				$error_code = $error_codes[$element['error']];
				$error_string = utf8_encode($current_element['ERRORS'][$error_code]);
				
				$output[$structure['FORM'].':'.$identifier.'.error'] = $error_string;	
				$output[$structure['FORM'].':'.$identifier.'.highlight'] = 'error';
				$output[$structure['FORM'].':ERROR'] = $general_error;
			}
				
			## store the data in case nay other field contains an error
			$output[$structure['FORM'].':'.$identifier.'.value'] = $element['data'];
			## for later usage we store all input vars					
		}
	}
	
	
	
	if(!$error_found) {
		return true;
	} else {
		return $output;
	}
}

## =======================================================================        
##  form_copyData        
## =======================================================================        
##  pass it the sourcepage_id and the targetpage_id. It'll copy all
##  text entries to the new page.
## ======================================================================= 
function form_copyData($source_id, $target_id) {
	linklist_copyData($source_id, $target_id);	
}

?>
