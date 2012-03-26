<?php
## =======================================================================        
##  clients_displayInputForm        
## =======================================================================        
##  this is a helper function which calls a couple of other functions
##  in a certain order in order to display the input form
##
## =======================================================================        
function clients_id_displayInput($ctlData,$id) {
}

## =======================================================================        
##  clients_id_deleteData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_id_deleteData($ctlData,$clientID) {	
}


## =======================================================================        
##  clients_id_getData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_id_getData($ctlData,$clientID) {		
	return $clientID;
}


## =======================================================================        
##  clients_id_storeInput        
## =======================================================================        
##  processes the data entered by the user- and stores the data
##  in the correct table
##
## =======================================================================        
function clients_id_storeInput($ctlData,$clientID) {
}

## =======================================================================        
##  clients_id_storeInput        
## =======================================================================        
##  processes the data entered by the user- and stores the data
##  in the correct table
##
## =======================================================================        
function clients_id_validateInput($ctlData,$input_data = null) {
}


## =======================================================================        
##  clients_id_setup        
## =======================================================================        
##  is called to see if we need to setup this attribute type- and if yes
##  it sets itself up
##
## =======================================================================        
function clients_id_setup($identifier) {	
	
}

## =======================================================================        
##  clients_id_setupSearch        
## =======================================================================        
##  this is called by the overviewpage- we will return the options for
##  the search. For this normal text type- these are: the standard search
##  fields+ and our entry in the pulldown menu
##
## =======================================================================        
function clients_id_setupSearch($ctlData,$data,$element_count) {	
	## first we prepare the field selector entry

	
	if(isset($data[$element_count]['identifier']) && 'id' == $data[$element_count]['identifier']) {
		$field_selector = '<option label="ID" value="id" selected>ID</option>';
	} else {
		$field_selector = '<option label="ID" value="id">ID</option>';		
	}
	
	## then we must tell the main programm the fucntionality for selecting our desired input form set
	$input_selector = " else if (document.s.search#.value == 'id') { showElement_row#('row#_standard'); }";
	
	## finally in the case, that we want to specify our own input element we can do this here
	$input_element = '';

	## finally we return the setting
	return array('fieldSelector'=>$field_selector,'inputSelector'=>$input_selector,'inputElement'=>$input_element,'inputName'=>"row#_standard");
}

## =======================================================================        
##  clients_id_getSearchQuery       
## =======================================================================        
##  this function returns the query- required in order to retrieve 
##  the specified search values. it consists of 3 elements:
##  A) Tables required- seperated by commas
##  B) Joining of the required tables with the client
##  C) the actual search pattern
##
## =======================================================================        
function clients_id_getSearchQuery($ctlData,$data) {
	## the A part	
	$query_A = '';
	
	## the B part
	$query_B = '';

	## the C part
	$search_value = $data['value'];


	switch($data['operator']) {
		case 1:
			$query_C = ' AND '.DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'.id LIKE '."'%".$search_value."%'";
			break;
		case 2:
			$query_C = ' AND '.DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'.id ='."'".$search_value."'";
			break;
		case 3:
			$operator .= '!=\''.$current_word.'\'';
			$query_C = ' AND '.DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'.id !='."'".$search_value."'";
			break;
	}
	


	return array('partA'=>$query_A,'partB'=>$query_B,'partC'=>$query_C);
}


## =======================================================================        
##  clients_id_storeSearchData       
## =======================================================================        
##  this function returns the query- required in order to retrieve 
##  the specified search values. it consists of 3 elements:
##  A) Tables required- seperated by commas
##  B) Joining of the required tables with the client
##  C) the actual search pattern
##
## =======================================================================        
function clients_id_storeSearchData($i) {
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
function clients_id_getSearchFields($ctlData) {
	$identifier = strtolower($ctlData['IDENTIFIER']);

	if($ctlData['OVERVIEW'] == 'true') {
		## if we need to display this field- check if we are already installed
		clients_id_setup($identifier);

		$query_tableselection = '';
		$query_columnselection = ','.DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'.id AS id';
		$query_sort = DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'.id';
	} else {
		$query_tableselection = '';
		$query_columnselection = '';
	}
	
	return array('identifier'=>$identifier,'table'=>$query_tableselection,'column'=>$query_columnselection,'sort'=>$query_sort);
	
}	
	

## =======================================================================        
##  clients_id_getExportData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_id_getExportData($ctlData,$clientID) {	
	return clients_id_getData($ctlData,$clientID);
}

?>