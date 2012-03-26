<?php
## =======================================================================        
##  clients_displayInputForm        
## =======================================================================        
##  this is a helper function which calls a couple of other functions
##  in a certain order in order to display the input form
##
## =======================================================================        
function clients_subcategories_displayInput($ctlData,$id) {
	global $Auth,$gSession;

	$identifier = strtolower($ctlData['IDENTIFIER']);

	## prepare the template
	$inputFile = "input.tpl";
	$template = new Template(ENGINE.'modules/clients/attributetypes/subcategories/interface');
	$template->set_templatefile(array("attribute" => $inputFile));

	
	## check if we need to setup this attribute type
	clients_subcategories_setup($identifier);
	
	## then we need to get any data that was previously entered
	$selected_values = clients_subcategories_getData($ctlData,$id);	
	$categories = _subcategories_prepareValues($ctlData);
		
	## since we need the selectmenu for all categories- we handle it here first
	$selectmenu = '<select name="'.$ctlData['IDENTIFIER'].'_category" id="'.$ctlData['IDENTIFIER'].'_category" size="1" style="width: 178px;" class="default">';
	foreach($categories  as $parent_option=>$subcategories) {
		if(isset($selected_values[$parent_option])) {
			$selectmenu .= '<option label="'.$subcategories['LABEL'].'" value="'.$parent_option.'" selected="selected">'.$subcategories['LABEL'].'</option>';
		} else {
			$selectmenu .= '<option label="'.$subcategories['LABEL'].'" value="'.$parent_option.'">'.$subcategories['LABEL'].'</option>';
		}
	}
	$selectmenu .= '</select>';	
	
	
	## okay now we need to loop through each parent category and prepare a element
	$output = '';
	foreach($categories as $parent_option=>$subcategories) {
		$parent_label = $subcategories['LABEL'];
		$selected_subvalues = $selected_values[$parent_option];
		
		## okay now we need to loop through the subcategories and display them
		$subcategory_output = '<div id="'.$ctlData['IDENTIFIER'].$parent_option.'_subcategory"><select multiple name="'.$identifier.''.$parent_option.'[]" style="width: 178px;" class="default" size="4">';
		foreach($subcategories['SUBCATEGORIES'] as $key=>$value) {
			if(@in_array($key,$selected_subvalues)) {
				$subcategory_output .= '<option label="'.$value.'" value="'.$key.'" selected="selected">'.$value.'</option>';
			} else {
				$subcategory_output .= '<option label="'.$value.'" value="'.$key.'">'.$value.'</option>';
			}
		}
		$subcategory_output .='</select></div>';
		
		$output .= $subcategory_output;
	}
	
	## set the vars
	$template->set_var("title",$ctlData['NAME']);	
	$template->set_var("selector",$selectmenu);	
	$template->set_var("attributes",$output);

	## finally fill the template and return it
	return $template->fill_block('attribute');	
}

## =======================================================================        
##  _subcategories_prepareValues        
## =======================================================================        
##  helper function to extract the values
##
## =======================================================================        
function _subcategories_prepareValues($ctlData) {
	## first we should prepare teh options available
	$options = split(',',$ctlData["OPTIONS"]);
	$labels = split(',',$ctlData["LABELS"]);
	
	$categories = array();
	foreach($options as $key=>$value) {
		## split the current option
		$current_suboptions = split(':',$value);
		$current_sublabels = split(':',$labels[$key]);
		
		## we know that the first element is always the parent category
		$parent_option= array_shift($current_suboptions);
		$parent_label = array_shift($current_sublabels);
		
		$subcategories = array();
		foreach($current_suboptions as $key=>$value) {
			$subcategories[$value] = $current_sublabels[$key];
		}
		
		$categories[$parent_option] = array('LABEL'=>$parent_label,'SUBCATEGORIES'=>$subcategories);
	}

	return $categories;
}


## =======================================================================        
##  clients_subcategories_deleteData        
## =======================================================================        
##  deletes the checkBox entries for a certain client
##
## =======================================================================        
function clients_subcategories_deleteData($ctlData,$clientID) {	
}


## =======================================================================        
##  clients_subcategories_getData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_subcategories_getData($ctlData,$clientID) {	
	## prpeare the data
	$identifier = strtolower($ctlData['IDENTIFIER']);
	
	## db object
	$db = new DB_Sql();
	
	$query = "SELECT ".$ctlData['IDENTIFIER']." FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." WHERE id='$clientID'";
	$result_pointer = $db->query($query);	
	
	$value ='';
	if($db->next_record()) {
		$value = $db->Record[$ctlData['IDENTIFIER']];	
	}	
	
	## now we need to get the subcategories asccoicated with this user
	$query = "SELECT item_id FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."2".$identifier." WHERE client_id='$clientID'";
	$result_pointer = $db->query($query);	
	
	$return_value = array();
	while($db->next_record()) {
		$return_value[$value][] = $db->Record['item_id'];
	}
	
	if(empty($return_value)) {
		$return_value[$value] = array();
	}
	return $return_value;
}

## =======================================================================        
##  clients_subcategories_storeInput        
## =======================================================================        
##  storing needs to handle multiple inputs
##
## =======================================================================        
function clients_subcategories_storeInput($ctlData,$clientID) {
	global $Auth,$gSession;
	
	##first check if the database was setup
	clients_subcategories_setup($ctlData['IDENTIFIER']);
	$identifier = strtolower($ctlData['IDENTIFIER']);

	## okay the db is setup correcty now we need to find out the main category
	$main_category = $_POST[$ctlData['IDENTIFIER'].'_category'];
	$subcategories = $_POST[$ctlData['IDENTIFIER'].$main_category];

	## now we update the appropriate client
	$db = new DB_Sql();  
	$query = "UPDATE ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." SET ".$ctlData['IDENTIFIER']."= '$main_category' WHERE id='$clientID'";
	
	$result_pointer = $db->query($query);	
	
	## now store the subcategories
	$query = "DELETE FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."2".$identifier." WHERE client_id='$clientID'";
	$result_pointer = $db->query($query);	

	## now insert the values
	if(isset($subcategories) && is_array($subcategories)) {
		foreach($subcategories as $current_entry) {
			## now create the content	
			$query = "INSERT INTO ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."2".$identifier." (client_id,item_id) values ('$clientID','$current_entry')";
			$result_pointer = $db->query($query);	
		}
	}	
	
}

## =======================================================================        
##  clients_subcategories_setup        
## =======================================================================        
## creates the two required tables if needed
##
## =======================================================================        
function clients_subcategories_setup($identifier) {
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
		
		## finally we will create the connector table
		## now the connector
		$query = 'CREATE TABLE '.DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'2'.$identifier.' (`client_id` INT(10) NOT NULL ,`item_id` INT(10) NOT NULL)';
		$result_pointer = $db_connection->query($query);		
	}	
}

## =======================================================================        
##  clients_subcategories_setupSearch        
## =======================================================================        
##  this is called by the overviewpage- we will return the options for
##  the search. For this normal text type- these are: the standard search
##  fields+ and our entry in the pulldown menu
##
## =======================================================================        
function clients_subcategories_setupSearch($ctlData,$data,$element_count) {
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
		$categories = _subcategories_prepareValues($ctlData);
		$count = count($categories);
		
		## now preare the HTMl-Code for it
		$output = '';
		$output .= '<option label="select" value="-1">select</option>';
		foreach($categories as $key =>$value) {
			$value = $value['LABEL'];
		
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
function clients_subcategories_storeSearchData($i,$ctlData) {
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
##  clients_subcategories_getSearchQuery       
## =======================================================================        
##  this function returns the query- required in order to retrieve 
##  the specified search values. it consists of 3 elements:
##  A) Tables required- seperated by commas
##  B) Joining of the required tables with the client
##  C) the actual search pattern
##
## =======================================================================        
function clients_subcategories_getSearchQuery($ctlData,$searchRow) {
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
function clients_subcategories_getSearchFields($ctlData,$searchRow) {
	$identifier = strtolower($ctlData['IDENTIFIER']);

	if($ctlData['OVERVIEW'] == 'true') {
		## if we need to display this field- check if we are already installed
		clients_subcategories_setup($identifier);

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
##  clients_statistics_getExportData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_subcategories_getExportData($ctlData,$clientID) {
	$selected_values = clients_subcategories_getData($ctlData,$clientID);
	$categories = _subcategories_prepareValues($ctlData);
	
	$output = array();
	foreach($categories  as $parent_option=>$subcategories) {
		if(isset($selected_values[$parent_option])) {
			$output[] = $subcategories['LABEL'];
		} 
	}
	
	foreach($categories as $parent_option=>$subcategories) {
		$selected_subvalues = $selected_values[$parent_option];
		
		foreach($subcategories['SUBCATEGORIES'] as $key=>$value) {
			if(@in_array($key,$selected_subvalues)) {
				$output[] = $value;
			} 
		}
	}
	
	return join(';',$output);
}	
?>