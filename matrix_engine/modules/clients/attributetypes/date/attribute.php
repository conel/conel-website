<?php
## =======================================================================        
##  clients_displayInputForm        
## =======================================================================        
##  this is a helper function which calls a couple of other functions
##  in a certain order in order to display the input form
##
## =======================================================================        
function clients_date_displayInput($ctlData,$id) {
	global $Auth,$gSession;
	
	## check if we need to setup this attribute type
	clients_date_setup($ctlData['IDENTIFIER']);
	
	## then we need to get any data that was previously entered (not yet)
	$value = clients_date_getData($ctlData,$id);

	## we got your record to process the data
	if(!empty($value)) {
		## in this case we convert the date
		$value = getdate($value);
	} else {
		$value = getdate();
	}
	
	## and finally we output the input form
	## prepare the template
	$inputFile = "input.tpl";
	$template = new Template(ENGINE.'modules/clients/attributetypes/date/interface');
	$template->set_templatefile(array("attribute" => $inputFile,"attribute_noedit" => $inputFile));

	
	## set the vars accordingly
	$template->set_var('month',$value["mon"]);
	$template->set_var('day',$value["mday"]);
	$template->set_var('year',$value["year"]);
	$template->set_var('minutes',$value["minutes"]);
	$template->set_var('hours',$value["hours"]);	
	
	if(!$xmldata['TAG']) {
		$template->set_var('element_tag',LANG_ElementText);
	} else {
		$template->set_var('element_tag',$ctlData['TAG']);
	}
	
	## fill in the language specific things
	$template->set_var('DAY',LANG_DAY);
	$template->set_var('MONTH',LANG_MONTH);
	$template->set_var('YEAR',LANG_YEAR);
	$template->set_var('HOURS',LANG_HOURS);
	$template->set_var('MINUTES',LANG_MINUTES);
	

	## finally fill the template and return it	
	$template->set_var("title",$ctlData['NAME']);
	$template->set_var("attribute",$ctlData['IDENTIFIER']);
	$template->set_var("value",utility_prepareDate($value,DEFAULT_DATE.' '.DEFAULT_TIME));	
	
	if($ctlData['EDIT'] == "false") {
		return $template->fill_block('attribute_noedit');	
	} else {
		return $template->fill_block('attribute');
	}
}

## =======================================================================        
##  clients_date_deleteData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_date_deleteData($ctlData,$clientID) {	
	## the text element is stored within the main client table-
	## so we don't need to delete anything- it will be gone already
}


## =======================================================================        
##  clients_date_getData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_date_getData($ctlData,$clientID) {	
	## db object
	$db_connection = new DB_Sql();
	
	$query = "SELECT UNIX_TIMESTAMP(`".$ctlData['IDENTIFIER']."`) AS `".$ctlData['IDENTIFIER']."` FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." WHERE id='$clientID'";
	$result_pointer = $db_connection->query($query);

	$value ='';
	if($db_connection->next_record()) {
		
		$value = $db_connection->Record[$ctlData['IDENTIFIER']];
	}	

	return $value;
}


## =======================================================================        
##  clients_text_storeInput        
## =======================================================================        
##  processes the data entered by the user- and stores the data
##  in the correct table
##
## =======================================================================        
function clients_date_validateInput($ctlData,$input_data = null) {
	global $Auth,$gSession;
	
	## we need to prepare the input - needs to be done properly
	$identifier = $ctlData['IDENTIFIER'];
	if(isset($input_data)) {
		$day = intval($input_data[$identifier.'_day']);
		$month = intval($input_data[$identifier.'_month']);
		$year = intval($input_data[$identifier.'_year']);			
	} else {
		$day = intval($_POST[$identifier.'_day']);
		$month = intval($_POST[$identifier.'_month']);
		$year = intval($_POST[$identifier.'_year']);		
	}

	## okay we need to make sure that anything was entered
	if($day > 0 && $month > 0 && $year > 0) {
		## for now everything is okay
		$validation_status = VALID;
	} else {
		$validation_status = STRING_NODATA_ENTERED;
	}
	
	## we should return the error code and the input from the user
	return array('error'=>$validation_status,'data'=>stripslashes($data));
}

## =======================================================================        
##  clients_date_storeInput        
## =======================================================================        
##  processes the data entered by the user- and stores the data
##  in the correct table
##
## =======================================================================        
function clients_date_storeInput($ctlData,$clientID) {
	global $Auth,$gSession;
	
	##first check if the database was setup
	clients_date_setup($ctlData['IDENTIFIER']);
	
	$identifier = $ctlData['IDENTIFIER'];
	
	## here we prepare the data
	$day = $_POST[$identifier.'_day'];
	$month = $_POST[$identifier.'_month'];
	$year = $_POST[$identifier.'_year'];
	
	## prepare the date
	$data = mktime(0,0,0,$month,$day,$year);
	
	## now we update the appropriate client
	$db_connection = new DB_Sql();  
	
	$query = "UPDATE ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." SET `".$ctlData['IDENTIFIER']."` = FROM_UNIXTIME('$data') WHERE id='$clientID'";
	$result_pointer = $db_connection->query($query);	
}

## =======================================================================        
##  clients_date_setup        
## =======================================================================        
##  is called to see if we need to setup this attribute type- and if yes
##  it sets itself up
##
## =======================================================================        
function clients_date_setup($identifier) {
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
		$query = 'ALTER TABLE '.DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].' ADD `'.$identifier.'` DATETIME';
		$result_pointer = $db_connection->query($query);
	}	
	
}

## =======================================================================        
##  clients_date_setupSearch        
## =======================================================================        
##  this is called by the overviewpage- we will return the options for
##  the search. For this normal text type- these are: the standard search
##  fields+ and our entry in the pulldown menu
##
## =======================================================================        
function clients_date_setupSearch($ctlData,$data,$element_count) {
	if($ctlData['SEARCHABLE'] != 'no') {
		
		if($ctlData['IDENTIFIER'] == $data[$element_count]['identifier']) {
			$value_box_visibility = '';
			$field_selector = '<option label="'.$ctlData['NAME'].'" value="'.$ctlData['IDENTIFIER'].'" selected>'.$ctlData['NAME'].'</option>';
		} else {
			$value_box_visibility = 'style="display:none;"';
			$field_selector = '<option label="'.$ctlData['NAME'].'" value="'.$ctlData['IDENTIFIER'].'">'.$ctlData['NAME'].'</option>';		
		}		
		## then we must tell the main programm the fucntionality for selecting our desired input form set
		## we want the standard input
		$input_selector = "else if (document.s.search#.value == '".$ctlData['IDENTIFIER']."') { showElement_row#('row#_".$ctlData['IDENTIFIER']."');}";
		
		
		## here we preare the operators
		$operators = array('on','before','after');
		$operators_output = '';
		foreach($operators as $current_operator) {
			if($data[$element_count]['operator'] == $current_operator) {
				$operators_output .= '<option label="'.$current_operator.'" value="'.$current_operator.'" selected>'.$current_operator.'</option>';
			} else {
				$operators_output .= '<option label="'.$current_operator.'" value="'.$current_operator.'">'.$current_operator.'</option>';
			}
		}
		
		## finally in the case, that we want to specify our own input element we can do this here
		$input_element = '<div id="row#_'.$ctlData['IDENTIFIER'].'"  '.$value_box_visibility.'><table border="0" cellspacing="0" cellpadding="0"><tr><td valign="middle">
							<select name="operator#_'.$ctlData['IDENTIFIER'].'">
								'.$operators_output.'
							</select>
							</td><td align="left" valign="top"><img src="../../interface/images/blank.gif" alt="" width="10" height="24" border="0">
							</td><td align="left" valign="top"><table border="0" cellspacing="0" cellpadding="0" >
									<tr>
										<td align="left" valign="top"><img src="../clients/interface/images/blank.gif" alt="" width="1" height="3" border="0"></td>
									</tr>	
									<tr>
										<td align="left" valign="top"><p><input type="text" name="search_value#_'.$ctlData['IDENTIFIER'].'_day" size="5" value="'.$data[$element_count]['day'].'"></p></td>
										<td align="left" valign="top"><img src="../clients/interface/images/blank.gif" alt="" width="5" height="5" border="0"></td>
										<td align="left" valign="top"><p><input type="text" name="search_value#_'.$ctlData['IDENTIFIER'].'_month" size="5" value="'.$data[$element_count]['month'].'"></p></td>		
										<td align="left" valign="top"><img src="../clients/interface/images/blank.gif" alt="" width="5" height="5" border="0"></td>
										<td align="left" valign="top"><p><input type="text" name="search_value#_'.$ctlData['IDENTIFIER'].'_year" size="5" value="'.$data[$element_count]['year'].'"></p></td>		
									</tr></table></td></tr></table></div>';
	
		## finally we return the setting
		return array('fieldSelector'=>$field_selector,'inputSelector'=>$input_selector,'inputElement'=>$input_element);
	}
}

## =======================================================================        
##  clients_date_getSearchQuery       
## =======================================================================        
##  this function returns the query- required in order to retrieve 
##  the specified search values. it consists of 3 elements:
##  A) Tables required- seperated by commas
##  B) Joining of the required tables with the client
##  C) the actual search pattern
##
## =======================================================================        
function clients_date_getSearchQuery($ctlData,$data) {
	if($ctlData['SEARCHABLE'] != 'no') {
		$identifier = strtolower($ctlData['IDENTIFIER']);
	
		## the A part	
		$query_A = '';
		
		## the B part
		$query_B = '';
		
		## the C part
		$search_value_day = $data['day'];
		$search_value_month = $data['month'];
		$search_value_year = $data['year'];
		$date = mktime(0,0,0,$search_value_month,$search_value_day,$search_value_year);
		
		## check the operator
		$operator = $data['operator'];
		
		$op = '=';
		if($operator == 'on') {
			$op = ' = ';
		} else if($operator == 'before') { 
			$op = ' < ';
		}  else if($operator == 'after') { 
			$op = ' > ';
		}
		$query_C = ' AND ';
		$query_C .= DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'.'.$identifier.$op."FROM_UNIXTIME('".$date."')";

		return array('partA'=>$query_A,'partB'=>$query_B,'partC'=>$query_C);
	}
}


## =======================================================================        
##  clients_text_storeSearchData       
## =======================================================================        
##  this function returns the query- required in order to retrieve 
##  the specified search values. it consists of 3 elements:
##  A) Tables required- seperated by commas
##  B) Joining of the required tables with the client
##  C) the actual search pattern
##
## =======================================================================        
function clients_date_storeSearchData($i,$ctlData) {
	## init the return value
	$data = array();

	## we target everything through the search element counter
	if(!empty($_POST['search_value'.$i.'_'.$ctlData['IDENTIFIER'].'_day'])) {
		$data['datatype'] = 'custom';
		$data['search_element'] = "row#_".$ctlData['IDENTIFIER'];
		$data['count'] = $i;
		$data['identifier'] = $_POST['search'.$i];
		$data['operator'] = $_POST['operator'.$i.'_'.$ctlData['IDENTIFIER']];
		$data['day'] = $_POST['search_value'.$i.'_'.$ctlData['IDENTIFIER'].'_day'];
		$data['month'] = $_POST['search_value'.$i.'_'.$ctlData['IDENTIFIER'].'_month'];
		$data['year'] = $_POST['search_value'.$i.'_'.$ctlData['IDENTIFIER'].'_year'];
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
function clients_date_getSearchFields($ctlData,$searchRow) {
	$identifier = strtolower($ctlData['IDENTIFIER']);

	if($ctlData['OVERVIEW'] == 'true') {
		## if we need to display this field- check if we are already installed
		clients_date_setup($identifier);

		$query_tableselection = '';
		$query_columnselection = ',DATE_FORMAT('.DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'.'.$identifier.',"%e.%m.%Y") AS '.$identifier;
		$query_sort = DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'.'.$identifier;
	} else {
		$query_tableselection = '';
		$query_columnselection = '';
	}
	
	return array('identifier'=>$identifier,'table'=>$query_tableselection,'column'=>$query_columnselection,'sort'=>$query_sort);
	
}


## =======================================================================        
##  clients_date_getExportData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_date_getExportData($ctlData,$clientID) {	
	return clients_date_getData($ctlData,$clientID);
}


## =======================================================================        
##  clients_text_importData        
## =======================================================================        
##  imports the data for all elements of this attribute type in a data set
##
## =======================================================================        
function clients_date_importData($id,$ctlData,$data) {
	global $Auth,$gSession;

	## we are responsible to import all elements of our type
	$values = array();
	foreach($ctlData as $current_element) {
		if($current_element['TYPE'] == 'DATE' && isset($data[$current_element['IDENTIFIER']])) {
			$current_value = strtotime($data[$current_element['IDENTIFIER']]);
			$values[] = $current_element['IDENTIFIER']."=FROM_UNIXTIME('".$current_value."')";
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