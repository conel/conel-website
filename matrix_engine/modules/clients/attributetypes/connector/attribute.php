<?php
## =======================================================================        
##  clients_displayInputForm        
## =======================================================================        
##  this is a helper function which calls a couple of other functions
##  in a certain order in order to display the input form
##
## =======================================================================        
function clients_connector_displayInput($ctlData,$id) {
	global $Auth,$gSession;

	$identifier = strtolower($ctlData['IDENTIFIER']);

	## prepare the template
	$inputFile = "input.tpl";
	$template = new Template(ENGINE.'modules/clients/attributetypes/select/interface');
	$template->set_templatefile(array("attribute" => $inputFile));

	## check if we need to setup this attribute type
	clients_connector_setup($identifier);
	
	## then we need to get any data that was previously entered
	$current_value = clients_connector_getData($ctlData,$id);
	
	## we need to get the data from the other dataobject- this will be done by
	## this will be done throught he default filter with the id 0

	## db object
	$db_connection = new DB_Sql();
	
	## we need to get the elements for this datatype- this ill be done trough the standard filter
	$query = "SELECT query FROM ".DB_PREFIX.$ctlData['TARGET']."_filters WHERE id='0'";
	$result = $db_connection->query($query);

	$options[0] = 'select';
	if($db_connection->next_record()) {
		$query = $db_connection->Record["query"];			
		
		## we can execute the filter query now and will recieve a list of clients
		$result = $db_connection->query($query);	

		$current_id = 0;
		while($db_connection->next_record()) {
			$options[$db_connection->Record['id']] = stripslashes($db_connection->Record[$ctlData['LABEL']]);
		}	

	}
	
	$output = '<select name="'.$ctlData['IDENTIFIER'].'" size="1" style="width: 178px;" class="default">';
	foreach($options as $value => $label) {
		## set the option
		if($value == $current_value) {
			$output .= '<option label="'.$label.'" value="'.$value.'" selected>'.$label.'</option>';
		} else {
			$output .= '<option label="'.$label.'" value="'.$value.'">'.$label.'</option>';
		}	
	}
	$output .= '</select>';	

	## finally fill the template and return it
	## generate the editor url

	$template->set_var("title",$ctlData['NAME']);
	$template->set_var("attribute",$output);

	## finally fill the template and return it
	return $template->fill_block('attribute');	
}

## =======================================================================        
##  clients_connector_deleteData        
## =======================================================================        
##  deletes the checkBox entries for a certain client
##
## =======================================================================        
function clients_connector_deleteData($ctlData,$clientID) {	
}


## =======================================================================        
##  clients_connector_getData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_connector_getData($ctlData,$clientID) {	
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
##  clients_connector_storeInput        
## =======================================================================        
##  storing needs to handle multiple inputs
##
## =======================================================================        
function clients_connector_storeInput($ctlData,$clientID) {
	global $Auth,$gSession;
	
	##first check if the database was setup
	clients_connector_setup($ctlData['IDENTIFIER']);
	
	## we need to prepare the input - needs to be done properly
	$data = intval($_POST[$ctlData['IDENTIFIER']]);

	## now we update the appropriate client
	$db_connection = new DB_Sql();  
	
	$query = "UPDATE ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." SET ".$ctlData['IDENTIFIER']."= '$data' WHERE id='$clientID'";
	$result_pointer = $db_connection->query($query);		
}

## =======================================================================        
##  clients_connector_setup        
## =======================================================================        
## creates the two required tables if needed
##
## =======================================================================        
function clients_connector_setup($identifier) {
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
		$query = 'ALTER TABLE '.DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].' ADD '.$identifier.' int(11)';
		$result_pointer = $db_connection->query($query);
	}	
}

## =======================================================================        
##  clients_connector_setupSearch        
## =======================================================================        
##  this is called by the overviewpage- we will return the options for
##  the search. For this normal text type- these are: the standard search
##  fields+ and our entry in the pulldown menu
##
## =======================================================================        
function clients_connector_setupSearch($ctlData,$data,$element_count) {
	## first we prepare the field selector entry
	if($ctlData['SEARCHABLE'] != 'no') {
		if($ctlData['IDENTIFIER'] == $data[$element_count]['identifier']) {
			$value_box_visibility = '';
			$field_selector = '<option label="'.$ctlData['NAME'].'" value="'.$ctlData['IDENTIFIER'].'" selected>'.$ctlData['NAME'].'</option>';
		} else {
			$value_box_visibility = 'style="display:none;"';
			$field_selector = '<option label="'.$ctlData['NAME'].'" value="'.$ctlData['IDENTIFIER'].'">'.$ctlData['NAME'].'</option>';		
		}
	
		## then we must tell the main programm the fucntionality for selecting our desired input form set
		$input_selector = " else if (document.s.search#.value == '".$ctlData['IDENTIFIER']."') { showElement_row#('row#_".$ctlData['IDENTIFIER']."'); }";
		
		## finally in the case, that we want to specify our own input element we can do this here
		$values = explode(',',$ctlData['LABELS']);
		$keys = explode(',',$ctlData['VALUES']);
		$count = count($values);
		
		## now preare the HTMl-Code for it
		$output = '';
		$output .= '<option label="select" value="-1">select</option>';
		for($i = 0; $i < $count; $i++) {
			if($keys[$i] == $data[$element_count]['value']) {
				$output .= '<option label="'.$values[$i].'" value="'.$keys[$i].'" selected>'.$values[$i].'</option>';
			} else {
				$output .= '<option label="'.$values[$i].'" value="'.$keys[$i].'">'.$values[$i].'</option>';
			}
		}	
		
		$input_element = '<div id="row#_'.$ctlData['IDENTIFIER'].'" '.$value_box_visibility.'><table border="0" cellspacing="0" cellpadding="0"><tr><td valign="middle">
							<select name="operator#_'.$ctlData['IDENTIFIER'].'"><option label="contains" value="contains">contains</option>
							</select>
							</td><td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="10" height="24" border="0">
							</td><td align="left" valign="middle"><select name="search_value#_'.$ctlData['IDENTIFIER'].'">'.$output.'</select></td></tr></table></div>';
	
		## finally we return the setting
		return array('fieldSelector'=>$field_selector,'inputSelector'=>$input_selector,'inputElement'=>$input_element,'inputName'=>"row#_".$ctlData['IDENTIFIER']);
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
function clients_connector_storeSearchData($i,$ctlData) {
	## init the return value
	$data = array();
	
	## we target everything through the search element counter
	if(!empty($_POST['search_value'.$i.'_'.$ctlData['IDENTIFIER']]) && $_POST['search_value'.$i.'_'.$ctlData['IDENTIFIER']] >= 0) {
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
##  clients_connector_getSearchQuery       
## =======================================================================        
##  this function returns the query- required in order to retrieve 
##  the specified search values. it consists of 3 elements:
##  A) Tables required- seperated by commas
##  B) Joining of the required tables with the client
##  C) the actual search pattern
##
## =======================================================================        
function clients_connector_getSearchQuery($ctlData,$searchRow) {
	if($ctlData['SEARCHABLE'] != 'no') {
		$identifier = strtolower($ctlData['IDENTIFIER']);

		## the A part	
		$query_A = '';
		
		## the B part
		$query_B = '';
		
		## the C part
		$search_value = $searchRow['value'];
		$query_C = ' AND ';
		$query_C .= DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'.'.$identifier.' like'."'%".$search_value."%'";
		
		return array('partA'=>$query_A,'partB'=>$query_B,'partC'=>$query_C);
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
function clients_connector_getSearchFields($ctlData,$searchRow) {
	$identifier = strtolower($ctlData['IDENTIFIER']);

	if($ctlData['OVERVIEW'] == 'true') {
		## if we need to display this field- check if we are already installed
		clients_connector_setup($identifier);

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
##  clients_connector_getExportData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_connector_getExportData($ctlData,$clientID) {	
	return clients_connector_getData($ctlData,$clientID);
}
?>