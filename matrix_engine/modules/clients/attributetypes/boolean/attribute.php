<?php
## =======================================================================        
##  clients_displayInputForm        
## =======================================================================        
##  this is a helper function which calls a couple of other functions
##  in a certain order in order to display the input form
##
## =======================================================================        
function clients_boolean_displayInput($ctlData,$id) {
	global $Auth,$gSession;
	
	## check if we need to setup this attribute type
	clients_boolean_setup($ctlData['IDENTIFIER']);
	
	## then we need to get any data that was previously entered (not yet)
	$value = clients_boolean_getData($ctlData,$id);
			
	## and finally we output the input form
	## prepare the template
	$inputFile = "input.tpl";
	$template = new Template(ENGINE.'modules/clients/attributetypes/boolean/interface');
	$template->set_templatefile(array("attribute" => $inputFile));
		
	## finally fill the template and return it
	if($value == 1) {
		$template->set_var("status",'checked');
	} else {
		$template->set_var("status",'');
	}
		
	$template->set_var("title",$ctlData['NAME']);
	$template->set_var("attribute",$ctlData['IDENTIFIER']);
	$template->set_var("value",$value);	

	return $template->fill_block('attribute');	
}

## =======================================================================        
##  clients_boolean_deleteData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_boolean_deleteData($ctlData,$clientID) {	
	## the text element is stored within the main client table-
	## so we don't need to delete anything- it will be gone already
}


## =======================================================================        
##  clients_boolean_getData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_boolean_getData($ctlData,$clientID) {	
	## db object
	$db_connection = new DB_Sql();
	
	$query = "SELECT ".$ctlData['IDENTIFIER']." FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." WHERE id='$clientID'";
	$result_pointer = $db_connection->query($query);	
	
	$value ='';
	if($db_connection->next_record()) {
		$value = intval($db_connection->Record[$ctlData['IDENTIFIER']]);
	}	
	return $value;
}

## =======================================================================        
##  clients_boolean_storeInput        
## =======================================================================        
##  processes the data entered by the user- and stores the data
##  in the correct table
##
## =======================================================================        
function clients_boolean_storeInput($ctlData,$clientID) {
	global $Auth,$gSession;

	##first check if the database was setup
	clients_boolean_setup($ctlData['IDENTIFIER']);
	
	## we need to prepare the input - needs to be done properly
	$data = $_POST[$ctlData['IDENTIFIER']];
	$data = intval($data);

	## now we update the appropriate client
	$db_connection = new DB_Sql();  
	
	$query = "UPDATE ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." SET ".$ctlData['IDENTIFIER']."= '$data' WHERE id='$clientID'";
	$result_pointer = $db_connection->query($query);	
}

## =======================================================================        
##  clients_boolean_validateInput        
## =======================================================================        
##  validates the input- since we can very savely convert the
##  input- we can validate very easily
##
## =======================================================================        
function clients_boolean_validateInput($ctlData,$clientID) {
	global $Auth,$gSession;

	##first check if the database was setup
	clients_boolean_setup($ctlData['IDENTIFIER']);

	## we need to prepare the input 
	$data = intval($_POST[$ctlData['IDENTIFIER']]);
	
	## we should return the error code and the input from the user
	return array('error'=>0,'data'=>$data);
}


## =======================================================================        
##  clients_boolean_setup        
## =======================================================================        
##  is called to see if we need to setup this attribute type- and if yes
##  it sets itself up
##
## =======================================================================        
function clients_boolean_setup($identifier) {
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
		$query = 'ALTER TABLE '.DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].' ADD '.$identifier.' BOOL NOT NULL DEFAULT 0';
		$result_pointer = $db_connection->query($query);
	}	
	
}

## =======================================================================        
##  clients_boolean_setupSearch        
## =======================================================================        
##  this is called by the overviewpage- we will return the options for
##  the search. For this normal text type- these are: the standard search
##  fields+ and our entry in the pulldown menu
##
## =======================================================================        
function clients_boolean_setupSearch($ctlData,$data,$element_count) {
	if($ctlData['SEARCHABLE'] != 'no') {
		## first we prepare the field selector entry
		if($ctlData['IDENTIFIER'] == $data[$element_count]['identifier']) {
			$value_box_visibility = '';
			$field_selector = '<option label="'.$ctlData['NAME'].'" value="'.$ctlData['IDENTIFIER'].'" selected>'.$ctlData['NAME'].'</option>';
		} else {
			$value_box_visibility = 'style="display:none;"';
			$field_selector = '<option label="'.$ctlData['NAME'].'" value="'.$ctlData['IDENTIFIER'].'">'.$ctlData['NAME'].'</option>';		
		}
	
		## then we must tell the main programm the fucntionality for selecting our desired input form set
		$input_selector = " else if (document.s.search#.value == '".$ctlData['IDENTIFIER']."') { showElement_row#('row#_".$ctlData['IDENTIFIER']."'); }";
			
		## we need to get all possible values
		$labels = array('false','true');
		$options = array(0,1);
		
		## now preare the HTMl-Code for it
		$output = '';
		$output .= '<option label="select" value="-1">select</option>';
		for($i=0; $i< count($labels); $i++) {
			if($options[$i] == $data[$element_count]['value']) {
				$output .= '<option label="'.$labels[$i].'" value="'.$options[$i].'" selected>'.$labels[$i].'</option>';
			} else {
				$output .= '<option label="'.$labels[$i].'" value="'.$options[$i].'">'.$labels[$i].'</option>';
			}
		}
		
		$input_element = '<div id="row#_'.$ctlData['IDENTIFIER'].'" '.$value_box_visibility.'><table border="0" cellspacing="0" cellpadding="0"><tr><td valign="middle">
							<select name="operator#_'.$ctlData['IDENTIFIER'].'"><option label="equals" value="equals">equals</option>
							</select>
							</td><td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="10" height="24" border="0">
							</td><td align="left" valign="middle"><select name="search_value#_'.$ctlData['IDENTIFIER'].'">'.$output.'</select></td></tr></table></div>';
	
		## finally we return the setting
		
		return array('fieldSelector'=>$field_selector,'inputSelector'=>$input_selector,'inputElement'=>$input_element,'inputName'=>"row#_".$ctlData['IDENTIFIER']);
	}
}

## =======================================================================        
##  clients_boolean_getSearchQuery       
## =======================================================================        
##  this function returns the query- required in order to retrieve 
##  the specified search values. it consists of 3 elements:
##  A) Tables required- seperated by commas
##  B) Joining of the required tables with the client
##  C) the actual search pattern
##
## =======================================================================        
function clients_boolean_getSearchQuery($ctlData,$data) {
	
	if($ctlData['SEARCHABLE'] != 'no') {
		$identifier = strtolower($ctlData['IDENTIFIER']);

		## the A part	
		$query_A = '';
		
		## the B part
		$query_B = '';

		## the C part
		$search_value = $data['value'];
		$query_C = ' AND '.DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'.'.$identifier.' ='."'".$search_value."'";

		return array('partA'=>$query_A,'partB'=>$query_B,'partC'=>$query_C);
	}
}


## =======================================================================        
##  clients_boolean_storeSearchData       
## =======================================================================        
##  this function returns the query- required in order to retrieve 
##  the specified search values. it consists of 3 elements:
##  A) Tables required- seperated by commas
##  B) Joining of the required tables with the client
##  C) the actual search pattern
##
## =======================================================================        
function clients_boolean_storeSearchData($i,$ctlData) {
	## init the return value
	$data = array();

	## we target everything through the search element counter
	if($_POST['search_value'.$i.'_'.$ctlData['IDENTIFIER']] != -1) {
		$data['datatype'] = 'custom';
		$data['search_element'] = "row#_".$ctlData['IDENTIFIER'];
		$data['count'] = $i;
		$data['identifier'] = $_POST['search'.$i];
		$data['operator'] = $_POST['operator'.$i.'_'.$ctlData['IDENTIFIER']];
		$data['value'] = $_POST['search_value'.$i.'_'.$ctlData['IDENTIFIER']];
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
function clients_boolean_getSearchFields($ctlData,$searchRow) {
	$identifier = strtolower($ctlData['IDENTIFIER']);

	if($ctlData['OVERVIEW'] == 'true') {
		## if we need to display this field- check if we are already installed
		clients_boolean_setup($identifier);

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
##  clients_boolean_getExportData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_boolean_getExportData($ctlData,$clientID) {	
	return clients_boolean_getData($ctlData,$clientID);
}
?>