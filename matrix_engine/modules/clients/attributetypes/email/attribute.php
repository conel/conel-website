<?php
## =======================================================================        
##  clients_displayInputForm        
## =======================================================================        
##  this is a helper function which calls a couple of other functions
##  in a certain order in order to display the input form
##
## =======================================================================        
function clients_email_displayInput($ctlData,$id) {
	global $Auth,$gSession;
	
	## check if we need to setup this attribute type
	clients_text_setup($ctlData['IDENTIFIER']);
	
	## then we need to get any data that was previously entered (not yet)
	$value = clients_text_getData($ctlData,$id);
	$value = htmlentities($value);
	## and finally we output the input form
	## prepare the template
	$inputFile = "input.tpl";
	$template = new Template(ENGINE.'modules/clients/attributetypes/text/interface');
	$template->set_templatefile(array("attribute" => $inputFile,"attribute_noedit" => $inputFile));
		
	## finally fill the template and return it
	
	$template->set_var("title",$ctlData['NAME']);
	$template->set_var("attribute",$ctlData['IDENTIFIER']);
	$template->set_var("value",$value);	

	if($ctlData['EDIT'] == "false") {
		return $template->fill_block('attribute_noedit');	
	} else {
		return $template->fill_block('attribute');
	}
}

## =======================================================================        
##  clients_email_deleteData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_email_deleteData($ctlData,$clientID) {	
	## the email element is stored within the main client table-
	## so we don't need to delete anything- it will be gone already
}


## =======================================================================        
##  clients_email_getData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_email_getData($ctlData,$clientID) {	
	## db object
	$db_connection = new DB_Sql();
	
	$query = "SELECT ".$ctlData['IDENTIFIER']." FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." WHERE id='$clientID'";
	$result_pointer = $db_connection->query($query);	
	
	$value ='';
	if($db_connection->next_record()) {
		$value = stripslashes($db_connection->Record[$ctlData['IDENTIFIER']]);
	}	
	return $value;
}

## =======================================================================        
##  clients_text_getData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_email_getClient($ctlData,$value) {	
	## init the vars
	$client_id = false;
	
	## db object
	$db_connection = new DB_Sql();
	
	## sanitze the data first
	$value = mysql_real_escape_string($value);
	
	$query = "SELECT id FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." WHERE ".$ctlData['IDENTIFIER']." ='$value'";
	$result_pointer = $db_connection->query($query);	
	
	$value ='';
	if($db_connection->next_record()) {
		$client_id = $db_connection->Record['id'];
	}	
	return $client_id;
}

## =======================================================================        
##  clients_email_storeInput        
## =======================================================================        
##  processes the data entered by the user- and stores the data
##  in the correct table
##
## =======================================================================        
function clients_email_storeInput($ctlData,$clientID) {
	global $Auth,$gSession;
	
	##first check if the database was setup
	clients_email_setup($ctlData['IDENTIFIER']);
	
	## we need to prepare the input - needs to be done properly
	$data = $_POST[$ctlData['IDENTIFIER']];
	
	## we need to validate the input- and make sure it is sanitized
	$data = mysql_real_escape_string($data);
	
	## now we update the appropriate client
	$db_connection = new DB_Sql();  
	
	$query = "UPDATE ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." SET ".$ctlData['IDENTIFIER']."= '$data' WHERE id='$clientID'";
	$result_pointer = $db_connection->query($query);	
}

## =======================================================================        
##  clients_email_validateInput        
## =======================================================================        
##  the email validation routine automatically validates
##
## =======================================================================        
function clients_email_validateInput($ctlData,$clientID) {
	global $Auth,$gSession;
	
	## we need to prepare the input - needs to be done properly
	$data = $_POST[$ctlData['IDENTIFIER']];
	
	## now prepare the validation class
	
	$validation_status = Validate::email($data);

	## we should return the error code and the input from the user
	return array('error'=>$validation_status,'data'=>$data);
}

## =======================================================================        
##  clients_email_setup        
## =======================================================================        
##  is called to see if we need to setup this attribute type- and if yes
##  it sets itself up
##
## =======================================================================        
function clients_email_setup($identifier) {
	## let's check if we can find the field in the main client table
	## db class
	$db_connection = new DB_Sql();  

	$query = "DESCRIBE ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."";
	$result_pointer = $db_connection->query($query);
	
	$exists = false;
	while($db_connection->next_record()) {
		if($db_connection->Record["Field"] == $identifier || $db_connection->Record["Field"] == strtolower($identifier)) {
			$exists = true;
		}
	}
	
	if(!$exists) {
		## okay then we need to create the appropriate table
		$query = 'ALTER TABLE '.DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].' ADD '.$identifier.' VARCHAR(80)';
		$result_pointer = $db_connection->query($query);
	}	
}

## =======================================================================        
##  clients_email_setupSearch        
## =======================================================================        
##  this is called by the overviewpage- we will return the options for
##  the search. For this normal text type- these are: the standard search
##  fields+ and our entry in the pulldown menu
##
## =======================================================================        
function clients_email_setupSearch($ctlData,$data,$element_count) {
	if(isset($ctlData['SEARCHABLE'])) {
		if($ctlData['SEARCHABLE'] == 'no') {
			return;
		}
	}
	
	## first we prepare the field selector entry

	if(isset($data[$element_count]['identifier']) && $ctlData['IDENTIFIER'] == $data[$element_count]['identifier']) {
		$field_selector = '<option label="'.$ctlData['NAME'].'" value="'.$ctlData['IDENTIFIER'].'" selected>'.$ctlData['NAME'].'</option>';
	} else {
		$field_selector = '<option label="'.$ctlData['NAME'].'" value="'.$ctlData['IDENTIFIER'].'">'.$ctlData['NAME'].'</option>';		
	}
	
	## then we must tell the main programm the fucntionality for selecting our desired input form set
	$input_selector = " else if (document.s.search#.value == '".$ctlData['IDENTIFIER']."') { showElement_row#('row#_standard'); }";
	
	## finally in the case, that we want to specify our own input element we can do this here
	$input_element = '';

	## finally we return the setting
	return array('fieldSelector'=>$field_selector,'inputSelector'=>$input_selector,'inputElement'=>$input_element,'inputName'=>"row#_standard");

}

## =======================================================================        
##  clients_email_getSearchQuery       
## =======================================================================        
##  this function returns the query- required in order to retrieve 
##  the specified search values. it consists of 3 elements:
##  A) Tables required- seperated by commas
##  B) Joining of the required tables with the client
##  C) the actual search pattern
##
## =======================================================================        
function clients_email_getSearchQuery($ctlData,$data) {
	if($ctlData['SEARCHABLE'] != 'no' && !empty($data['value'])) {
		$identifier = strtolower($ctlData['IDENTIFIER']);

		## the A part	
		$query_A = '';
		
		## the B part
		$query_B = '';

		## the C part
		$search_value = $data['value'];
		
		if(empty($search_value)) {
			## than the user is searching for a empty value
			$query_C = " AND ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].".".$identifier." = ''";
		} else {
			$query_C = ' AND '.DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'.'.$identifier.' like'."'%".$search_value."%'";
		}
		
		return array('partA'=>$query_A,'partB'=>$query_B,'partC'=>$query_C);
	}
}


## =======================================================================        
##  clients_email_getSearchQuery       
## =======================================================================        
##  this function returns the query- required in order to retrieve 
##  the specified search values. it consists of 3 elements:
##  A) Tables required- seperated by commas
##  B) Joining of the required tables with the client
##  C) the actual search pattern
##
## =======================================================================        
function clients_email_storeSearchData($i) {
	## init the return value
	$data = array();
	
	## we target everything through the search element counter
	if(!empty($_POST['search_value'.$i.'_standard'])) {
		$data['identifier'] = $_POST['search'.$i];
		$data['operator'] = $_POST['operator'.$i.'_standard'];
		$data['value'] = $_POST['search_value'.$i.'_standard'];
		## return the data
		return $data;
	} else {
		return null;
	}
}

## =======================================================================        
##  dbobject_songs_getSearchFields       
## =======================================================================        
##  this function returns the query- required in order to retrieve 
##  the specified search values. it consists of 3 elements:
##  A) Tables required- seperated by commas
##  B) Joining of the required tables with the client
##  C) the actual search pattern
##
## =======================================================================        
function clients_email_getSearchFields($ctlData,$searchRow) {
	$identifier = strtolower($ctlData['IDENTIFIER']);

	if($ctlData['OVERVIEW'] == 'true') {
		## if we need to display this field- check if we are already installed
		clients_text_setup($identifier);

		$query_tableselection = '';
		$query_columnselection = ','.DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'.'.$identifier.' AS '.$identifier;
		$query_sort = DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'.'.$identifier;
	} else {
		$query_tableselection = '';
		$query_columnselection = '';
	}
	
	return array('identifier'=>$identifier,'table'=>$query_tableselection,'column'=>$query_columnselection,'sort'=>$query_sort);

}	
	

## =======================================================================        
##  clients_email_getExportData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_email_getExportData($ctlData,$clientID) {	
	return clients_email_getData($ctlData,$clientID);
}



## =======================================================================        
##  clients_text_importData        
## =======================================================================        
##  imports the data for all elements of this attribute type in a data set
##
## =======================================================================        
function clients_email_importData($id,$ctlData,$data) {
	global $Auth,$gSession;

	## we are responsible to import all elements of our type
	$values = array();
	foreach($ctlData as $current_element) {
		if($current_element['TYPE'] == 'EMAIL' && isset($data[$current_element['IDENTIFIER']])) {
			$current_value = mysql_real_escape_string($data[$current_element['IDENTIFIER']]);
			$values[] = $current_element['IDENTIFIER']."='".$current_value."'";
		}
	}
	
	## prepare the data for entry
	$values = join(',',$values);
				
	## now we update the appropriate client
	$db_connection = new DB_Sql();  
	$query = "UPDATE ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." SET ".$values." WHERE id='$id'";
	$result_pointer = $db_connection->query($query);
}
?>