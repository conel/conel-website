<?php
## =======================================================================        
##  clients_displayInputForm        
## =======================================================================        
##  this is a helper function which calls a couple of other functions
##  in a certain order in order to display the input form
##
## =======================================================================        
function clients_checkbox_displayInput($ctlData,$id) {
	global $Auth,$gSession;
	
	## check if we need to setup this attribute type
	clients_checkbox_setup($ctlData['IDENTIFIER']);
	
	## then we need to get any data that was previously entered (not yet)
	$values = clients_checkbox_getData($ctlData,$id);

	## prepare the xml input
	$labels = explode(',',$ctlData['LABELS']);
	$options = explode(',',$ctlData['OPTIONS']);

	## and finally we output the input form
	## prepare the template
	$inputFile = "input.tpl";
	$template = new Template(ENGINE.'modules/clients/attributetypes/checkbox/interface');
	$template->set_templatefile(array("attribute" => $inputFile));
	
	## preare the checkboxes
	$output = '';
	$count = count($labels);
	for($i=0; $i< $count; $i++) {
		if(in_array($options[$i],$values)) {
			$template->set_var("status",'checked');
		} else {
			$template->set_var("status",'');
		}
				
		$template->set_var("title",$labels[$i]);
		$template->set_var("attribute",$ctlData['IDENTIFIER']);
		$template->set_var("value",$options[$i]);
		$output .= $template->fill_block('attribute');	
	}

	## finally fill the template and return it
	return $output;	
}

## =======================================================================        
##  clients_checkbox_deleteData        
## =======================================================================        
##  deletes the checkBox entries for a certain client
##
## =======================================================================        
function clients_checkbox_deleteData($ctlData,$clientID) {	
	## prepare the db object
	$db_connection = new DB_Sql(); 
	
	$identifier = strtolower($ctlData['IDENTIFIER']);
	
	## first we will delete any previously set entries
	$query = "DELETE FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."2".$identifier." WHERE client_id='$clientID'";
	$result_pointer = $db_connection->query($query);	
	
}


## =======================================================================        
##  clients_checkbox_getData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_checkbox_getData($ctlData,$clientID) {	
	## db object
	$db_connection = new DB_Sql();
	
	$identifier = strtolower($ctlData['IDENTIFIER']);
	
	## in order to get the appropriate data- we need to join the connector, with the 
	## clients and data tables
	$query = "SELECT * FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." AS A, ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'2'.$identifier." AS B WHERE A.id=B.client_id AND A.id='".$clientID."'";
	$result_pointer = $db_connection->query($query);	
	
	$value = array();
	while($db_connection->next_record()) {
		$value[] = $db_connection->Record['item_id'];
	}
		
	return $value;
}

## =======================================================================        
##  clients_checkbox_storeInput        
## =======================================================================        
##  storing needs to handle multiple inputs
##
## =======================================================================        
function clients_checkbox_storeInput($ctlData,$clientID) {
	global $Auth,$gSession;

	## now we update the appropriate client
	$db_connection = new DB_Sql(); 
	
	$identifier = strtolower($ctlData['IDENTIFIER']);
	
	##first check if the database was setup
	clients_checkbox_setup($ctlData['IDENTIFIER']);
	
	## we need to prepare the input - needs to be done properly
	$data = $_POST[$ctlData['IDENTIFIER']];

	## first we will delete any previously set entries
	$query = "DELETE FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."2".$identifier." WHERE client_id='$clientID'";
	$result_pointer = $db_connection->query($query);	
	
	## now insert the values
	if(isset($data) && is_array($data)) {
		foreach($data as $current_entry) {
			## now create the content	
			$query = "INSERT INTO ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."2".$identifier." (client_id,item_id) values ('$clientID','$current_entry')";
			$result_pointer = $db_connection->query($query);	
		}
	}	
}

## =======================================================================        
##  clients_checkbox_setup        
## =======================================================================        
## creates the two required tables if needed
##
## =======================================================================        
function clients_checkbox_setup($identifier) {
	## let's check if we can find the field in the main client table
	## db class
	$db_connection = new DB_Sql();  

	## make the fields lowercase
	$identifier = strtolower($identifier);

	## lets'findout if the tables already exist
	$query = "SHOW TABLES LIKE '".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_%'";
	$result_pointer = $db_connection->query($query);
	
	$exists = false;
	while($db_connection->next_record()) {
		if($db_connection->Record[0] == (DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'2'.$identifier) || $db_connection->Record[0] == strtolower(DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'2'.$identifier)) {
			$exists = true;
		}
	}

	if(!$exists) {
		## now the connector
		$query = 'CREATE TABLE '.DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'2'.$identifier.' (`client_id` INT(10) NOT NULL ,`item_id` INT(10) NOT NULL)';
		$result_pointer = $db_connection->query($query);
	}		
}

## =======================================================================        
##  clients_checkbox_setupSearch        
## =======================================================================        
##  this is called by the overviewpage- we will return the options for
##  the search. For this normal text type- these are: the standard search
##  fields+ and our entry in the pulldown menu
##
## =======================================================================        
function clients_checkbox_setupSearch($ctlData,$data,$element_count) {
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
	$labels = explode(',',$ctlData['LABELS']);
	$options = explode(',',$ctlData['OPTIONS']);
	
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
						<select name="operator#_'.$ctlData['IDENTIFIER'].'"><option label="contains" value="contains">contains</option>
						</select>
						</td><td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="10" height="24" border="0">
						</td><td align="left" valign="middle"><select name="search_value#_'.$ctlData['IDENTIFIER'].'">'.$output.'</select></td></tr></table></div>';

	## finally we return the setting
	return array('fieldSelector'=>$field_selector,'inputSelector'=>$input_selector,'inputElement'=>$input_element,'inputName'=>"row#_".$ctlData['IDENTIFIER']);
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
function clients_checkbox_storeSearchData($i,$ctlData) {
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
##  clients_checkbox_getSearchQuery       
## =======================================================================        
##  this function returns the query- required in order to retrieve 
##  the specified search values. it consists of 3 elements:
##  A) Tables required- seperated by commas
##  B) Joining of the required tables with the client
##  C) the actual search pattern
##
## =======================================================================        
function clients_checkbox_getSearchQuery($ctlData,$searchRow) {
	$identifier = strtolower($ctlData['IDENTIFIER']);
	$tablename = "checkbox_".$identifier."_".$searchRow['count'];

	## the A part	
	$query_A = ', ';
	$query_A .= DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."2".$identifier." AS ".$tablename;
	
	## the B part
	$query_B = ' AND ';
	$query_B .= $tablename.'.client_id='.DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'.id';
	
	## the C part
	$query_C = ' AND ';
	$query_C .= $tablename.'.item_id='.$searchRow['value'];
	
	return array('partA'=>$query_A,'partB'=>$query_B,'partC'=>$query_C);
}


## =======================================================================        
##  clients_checkbox_getExportData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_checkbox_getExportData($ctlData,$clientID) {	
	## db object
	$db_connection = new DB_Sql();
	
	$identifier = strtolower($ctlData['IDENTIFIER']);
	
	## in order to get the appropriate data- we need to join the connector, with the 
	## clients and data tables
	$query = "SELECT * FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." AS A, ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'2'.$identifier." AS B WHERE A.id=B.client_id AND A.id='".$clientID."'";
	$result_pointer = $db_connection->query($query);	
	
	$labels = explode(',',$ctlData['LABELS']);	
	
	$value = '';
	while($db_connection->next_record()) {
		if($value != '') {
			$value .= ';'.$labels[$db_connection->Record['item_id']-1];
		} else {
			$value .= $labels[$db_connection->Record['item_id']-1];
		}	
	}
	return $value;
}
?>