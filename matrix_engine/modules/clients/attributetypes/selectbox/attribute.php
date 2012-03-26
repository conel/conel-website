<?php
## =======================================================================        
##  clients_displayInputForm        
## =======================================================================        
##  this is a helper function which calls a couple of other functions
##  in a certain order in order to display the input form
##
## =======================================================================        
function clients_selectbox_displayInput($ctlData,$id) {
	global $Auth,$gSession;
	
	$identifier = strtolower($ctlData['IDENTIFIER']);

	## prepare the template
	$inputFile = "input.tpl";
	$template = new Template(ENGINE.'modules/clients/attributetypes/selectbox/interface');
	$template->set_templatefile(array("attribute" => $inputFile));

	
	## check if we need to setup this attribute type
	clients_selectbox_setup($identifier);
	
	## then we need to get any data that was previously entered
	$values = clients_selectbox_getData($ctlData,$id);
	
	## then we get all values in the database
	$db_connection = new DB_Sql();
	$query = "SELECT * FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'_'.$identifier;
	$result_pointer = $db_connection->query($query);

	$return_value = array();
	while($db_connection->next_record()) {
		$return_value[$db_connection->Record["id"]] = $db_connection->Record["text"];
	}

	## now preare the HTMl-Code for it
	$output = '<select multiple name="'.$identifier.'[]" style="width: 178px;" class="default" size="4">';

	foreach($return_value as $key=>$value) {

		if(@in_array($key,$values)) {
			$output .= '<option label="'.$value.'" value="'.$key.'" selected>'.$value.'</option>';
		} else {
			$output .= '<option label="'.$value.'" value="'.$key.'">'.$value.'</option>';
		}
	}

	$output .='</select>';

	## finally fill the template and return it
	## generate the editor url
	$addElementURL = "../clients/attributetypes/selectbox/editor.php?op=create&attribute=".$identifier."&source=".$GLOBALS['_MODULE_DATAOBJECTS_NAME'];
	$addElementURL = $gSession->url($addElementURL);
	$template->set_var('addElementURL',$addElementURL);

	$template->set_var("title",$ctlData['NAME']);
	$template->set_var("attribute",$output);

	## finally fill the template and return it
	return $template->fill_block('attribute');	
}

## =======================================================================        
##  clients_selectbox_deleteData        
## =======================================================================        
##  deletes the checkBox entries for a certain client
##
## =======================================================================        
function clients_selectbox_deleteData($ctlData,$clientID) {	
	## prepare the db object
	$db = new DB_Sql(); 
	
	$identifier = strtolower($ctlData['IDENTIFIER']);
	
	## first we will delete any previously set entries
	$query = "DELETE FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."2".$identifier." WHERE client_id='$clientID'";
	$result_pointer = $db->query($query);		
}


## =======================================================================        
##  clients_selectbox_getData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_selectbox_getData($ctlData,$clientID) {	
	## db object
	$db_connection = new DB_Sql();
	
	$identifier = strtolower($ctlData['IDENTIFIER']);
	
	## in order to get the appropriate data- we need to join the connector, with the 
	## clients and data tables
	$query = "SELECT C.* FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." AS A, ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'2'.$identifier." AS B, ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'_'.$identifier." AS C WHERE A.id=B.client_id AND B.item_id=C.id AND A.id='".$clientID."'";
	$result_pointer = $db_connection->query($query);	

	$value = array();
	while($db_connection->next_record()) {
		$value[] = $db_connection->Record['id'];
	}	
	return $value;
}

## =======================================================================        
##  clients_selectbox_storeInput        
## =======================================================================        
##  storing needs to handle multiple inputs
##
## =======================================================================        
function clients_selectbox_storeInput($ctlData,$clientID) {
	global $Auth,$gSession;

	## now we update the appropriate client
	$db_connection = new DB_Sql(); 
	
	$identifier = strtolower($ctlData['IDENTIFIER']);
	
	##first check if the database was setup
	clients_selectbox_setup($ctlData['IDENTIFIER']);

	 
	## we need to prepare the input - needs to be done properly
	$data = $_POST[$identifier];

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
##  clients_selectbox_setup        
## =======================================================================        
## creates the two required tables if needed
##
## =======================================================================        
function clients_selectbox_setup($identifier) {
	## let's check if we can find the field in the main client table
	## db class
	$db_connection = new DB_Sql();  

	## make the fields lowercase
	$identifier = strtolower($identifier);

	## lets'findout if the tables already exist
	$query = "SHOW TABLES LIKE '".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."%'";
	$result_pointer = $db_connection->query($query);
	
	$exists = false;
	while($db_connection->next_record()) {
		if($db_connection->Record[0] == (DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'_'.$identifier) || $db_connection->Record[0] == strtolower(DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'_'.$identifier)) {
			$exists = true;
		}
	}

	if(!$exists) {
		## the base
		$query = 'CREATE TABLE '.DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'_'.$identifier.' (`id` int(10) NOT NULL auto_increment, `text` varchar(255) NOT NULL default \'\', PRIMARY KEY (`id`))';
		$result_pointer = $db_connection->query($query);
		
		## now the connector
		$query = 'CREATE TABLE '.DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'2'.$identifier.' (`client_id` INT(10) NOT NULL ,`item_id` INT(10) NOT NULL)';
		$result_pointer = $db_connection->query($query);
	}	
	
}


## =======================================================================        
##  clients_selectmenu_setupSearch        
## =======================================================================        
##  this is called by the overviewpage- we will return the options for
##  the search. For this normal text type- these are: the standard search
##  fields+ and our entry in the pulldown menu
##
## =======================================================================        
function clients_selectbox_setupSearch($ctlData,$data,$element_count) {
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
	
	## finally in the case, that we want to specify our own input element we can do this here
	
	## we need to get all possible values
	$values = clients_selectbox_getValues($ctlData['IDENTIFIER']);
	
	## now preare the HTMl-Code for it
	$output = '';
	$output .= '<option label="select" value="-1">select</option>';
	foreach($values as $key=>$value) {
		if($key == $data[$element_count]['value']) {
			$output .= '<option label="'.$value.'" value="'.$key.'" selected>'.$value.'</option>';
		} else {
			$output .= '<option label="'.$value.'" value="'.$key.'">'.$value.'</option>';
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
function clients_selectbox_storeSearchData($i,$ctlData) {
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
##  clients_selectmenu_getValues        
## =======================================================================        
##  returns a list of all possible values
##
## =======================================================================        
function clients_selectbox_getValues($identifier) {
	$identifier = strtolower($identifier);
	## then we get all values in the database
	$db_connection = new DB_Sql();
	$query = "SELECT * FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_".$identifier;
	$result_pointer = $db_connection->query($query);

	$return_value = array();
	while($db_connection->next_record()) {
		$return_value[$db_connection->Record["id"]] = $db_connection->Record["text"];
	}
	
	return $return_value;
}


## =======================================================================        
##  clients_selectmenu_getSearchQuery       
## =======================================================================        
##  this function returns the query- required in order to retrieve 
##  the specified search values. it consists of 3 elements:
##  A) Tables required- seperated by commas
##  B) Joining of the required tables with the client
##  C) the actual search pattern
##
## =======================================================================        
function clients_selectbox_getSearchQuery($ctlData,$searchRow) {
	$identifier = strtolower($ctlData['IDENTIFIER']);
	$tablename = "selectbox_".$identifier."_".$searchRow['count'];

	## the A part	
	$query_A = ', ';
	$query_A .= DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_".$identifier." AS ".$tablename.'_base';
	$query_A .= ', ';
	$query_A .= DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."2".$identifier." AS ".$tablename.'_content';
	
	## the B part
	$query_B = ' AND ';
	$query_B .= $tablename.'_content'.'.client_id='.DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'.id';
	
	## the C part
	$search_value = $searchRow['value'];
	$query_C = ' AND ';
	$query_C .= $tablename.'_content'.'.item_id='.$search_value;
	
	return array('partA'=>$query_A,'partB'=>$query_B,'partC'=>$query_C);
}




## =======================================================================        
##  clients_selectbox_getData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_selectbox_getExportData($ctlData,$clientID) {	
	## db object
	$db_connection = new DB_Sql();
	
	$identifier = strtolower($ctlData['IDENTIFIER']);
	
	## in order to get the appropriate data- we need to join the connector, with the 
	## clients and data tables
	$query = "SELECT C.* FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." AS A, ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'2'.$identifier." AS B, ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'_'.$identifier." AS C WHERE A.id=B.client_id AND B.item_id=C.id AND A.id='".$clientID."'";
	$result_pointer = $db_connection->query($query);	

	$value = '';
	while($db_connection->next_record()) {
		if($value != '') {
			$value .= ';'.$db_connection->Record['text'];
		} else {
			$value .= $db_connection->Record['text'];
		}
	}	
	return $value;
}



## =======================================================================        
##  clients_text_importData        
## =======================================================================        
##  imports the data for all elements of this attribute type in a data set
##
## =======================================================================        
function clients_selectbox_importData($id,$ctlData,$data) {
	global $Auth,$gSession;

	## okay we need to handle all elements of the import that 
	$values = array();
	foreach($ctlData as $current_element) {
		if($current_element['TYPE'] == 'SELECTBOX' && isset($data[$current_element['IDENTIFIER']])) {
			## the user can pass us multiple elements seperated with ;
			$current_values = explode(';',$data[$current_element['IDENTIFIER']]);
			$values[$current_element['IDENTIFIER']] = $current_values;
		}
	}
	
	## prepare the db object
	$db = new DB_Sql();

	## okay for each of the entries for this record we need to handle each one of the values
	foreach($values as $identifier => $current_field) {
		foreach($current_field as $current_value) {
			## okay we have a single entry- first we need to check if we have a element with that name
			$query = "SELECT * FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'_'.$identifier.' WHERE text="'.mysql_real_escape_string($current_value).'"';
			$result_pointer = $db->query($query);
			
			if($db->next_record(MYSQL_ASSOC)) {
				$item_id = $db->Record['id'];
				
				## okay now we shoul try to insert the entry
				$query = "INSERT INTO ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."2".$identifier." (client_id,item_id) values ('$id','$item_id')";
				$result_pointer = $db->query($query);					
			}
		}
	}

}
?>