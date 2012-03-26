<?php
## =======================================================================        
##  clients_displayInputForm        
## =======================================================================        
##  this is a helper function which calls a couple of other functions
##  in a certain order in order to display the input form
##
## =======================================================================        
function clients_copytext_displayInput($ctlData,$id) {
	global $Auth,$gSession;
	
	## check if we need to setup this attribute type
	clients_copytext_setup($ctlData['IDENTIFIER']);
	
	## then we need to get any data that was previously entered (not yet)
	$value = clients_copytext_getData($ctlData,$id);
	$value = stripslashes(eregi_replace('<br[[:space:]]*/?[[:space:]]*>', '', $value));
	## and finally we output the input form
	## prepare the template
	$inputFile = "input.tpl";
	$template = new Template(ENGINE.'modules/clients/attributetypes/copytext/interface');
	$template->set_templatefile(array("attribute" => $inputFile));
		
	## finally fill the template and return it
	
	$template->set_var("title",$ctlData['NAME']);
	$template->set_var("attribute",$ctlData['IDENTIFIER']);
	$template->set_var("value",$value);	

	return $template->fill_block('attribute');	
}

## =======================================================================        
##  clients_copytext_deleteData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_copytext_deleteData($ctlData,$clientID) {	
	## the text element is stored within the main client table-
	## so we don't need to delete anything- it will be gone already
}


## =======================================================================        
##  clients_copytext_getData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_copytext_getData($ctlData,$clientID) {	
	## db object
	$db_connection = new DB_Sql();
	
	$query = "SELECT ".$ctlData['IDENTIFIER']." FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." WHERE id='$clientID'";
	$result_pointer = $db_connection->query($query);	
	
	$value ='';
	if($db_connection->next_record()) {
		$value = $db_connection->Record[$ctlData['IDENTIFIER']];
	}	
	return $value;
}

## =======================================================================        
##  clients_copytext_storeInput        
## =======================================================================        
##  processes the data entered by the user- and stores the data
##  in the correct table
##
## =======================================================================        
function clients_copytext_storeInput($ctlData,$clientID) {
	global $Auth,$gSession;
	
	##first check if the database was setup
	clients_copytext_setup($ctlData['IDENTIFIER']);
	
	## we need to prepare the input - needs to be done properly
	$data = htmlentities($_POST[$ctlData['IDENTIFIER']]);
	$data = mysql_real_escape_string(nl2br($data));
			
	## now we update the appropriate client
	$db_connection = new DB_Sql();  
	
	$query = "UPDATE ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." SET ".$ctlData['IDENTIFIER']."= '$data' WHERE id='$clientID'";
	$result_pointer = $db_connection->query($query);	
}

## =======================================================================        
##  clients_copytext_setup        
## =======================================================================        
##  is called to see if we need to setup this attribute type- and if yes
##  it sets itself up
##
## =======================================================================        
function clients_copytext_setup($identifier) {
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
		$query = 'ALTER TABLE '.DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].' ADD '.$identifier.' MEDIUMTEXT';
		$result_pointer = $db_connection->query($query);
	}	
	
}

## =======================================================================        
##  clients_copytext_setupSearch        
## =======================================================================        
##  this is called by the overviewpage- we will return the options for
##  the search. For this normal text type- these are: the standard search
##  fields+ and our entry in the pulldown menu
##
## =======================================================================        
function clients_copytext_setupSearch($ctlData,$data,$element_count) {
	if($ctlData['SEARCHABLE'] != 'no') {
		## first we prepare the field selector entry
		if($ctlData['IDENTIFIER'] == $data[$element_count]['identifier']) {
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
}

## =======================================================================        
##  clients_text_getSearchQuery       
## =======================================================================        
##  this function returns the query- required in order to retrieve 
##  the specified search values. it consists of 3 elements:
##  A) Tables required- seperated by commas
##  B) Joining of the required tables with the client
##  C) the actual search pattern
##
## =======================================================================        
function clients_copytext_storeSearchData($i) {
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
##  clients_copytext_getSearchQuery       
## =======================================================================        
##  this function returns the query- required in order to retrieve 
##  the specified search values. it consists of 3 elements:
##  A) Tables required- seperated by commas
##  B) Joining of the required tables with the client
##  C) the actual search pattern
##
## =======================================================================        
function clients_copytext_getSearchQuery($ctlData,$data) {
	if($ctlData['SEARCHABLE'] == 'no') {
		return;
	}

	$identifier = strtolower($ctlData['IDENTIFIER']);

	## the A part	
	$query_A = '';
	
	## the B part
	$query_B = '';

	## the C part
	$search_value = $data['value'];

	switch($data['operator']) {
		case 1:
			$query_C = ' AND '.DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'.'.$identifier.' LIKE '."'%".$search_value."%'";
			break;
		case 2:
			$query_C = ' AND '.DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'.'.$identifier.' ='."'".$search_value."'";
			break;
		case 3:
			$operator .= '!=\''.$current_word.'\'';
			$query_C = ' AND '.DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'.'.$identifier.' !='."'".$search_value."'";
			break;
	}
	return array('partA'=>$query_A,'partB'=>$query_B,'partC'=>$query_C);
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
function clients_copytext_getSearchFields($ctlData) {
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
##  clients_copytext_getExportData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_copytext_getExportData($ctlData,$clientID) {	
	return clients_copytext_getData($ctlData,$clientID);
}


## =======================================================================        
##  clients_text_importData        
## =======================================================================        
##  imports the data for all elements of this attribute type in a data set
##
## =======================================================================        
function clients_copytext_importData($id,$ctlData,$data) {
	global $Auth,$gSession;

	## we are responsible to import all elements of our type
	$values = array();

	foreach($ctlData as $current_element) {
		if($current_element['TYPE'] == 'COPYTEXT' && isset($data[$current_element['IDENTIFIER']])) {
			$current_value = htmlentities($data[$current_element['IDENTIFIER']]);
			$current_value = mysql_real_escape_string(nl2br($current_value));
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