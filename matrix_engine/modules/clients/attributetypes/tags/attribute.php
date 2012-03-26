<?php
## =======================================================================        
##  clients_displayInputForm        
## =======================================================================        
##  this is a helper function which calls a couple of other functions
##  in a certain order in order to display the input form
##
## =======================================================================        
function clients_tags_displayInput($ctlData,$id) {
	global $Auth,$gSession;
	
	$identifier = strtolower($ctlData['IDENTIFIER']);

	## prepare the template
	$inputFile = "input.tpl";
	$template = new Template(ENGINE.'modules/clients/attributetypes/tags/interface');
	$template->set_templatefile(array("attribute" => $inputFile));

	
	## check if we need to setup this attribute type
	clients_tags_setup($identifier);
	
	## then we need to get any data that was previously entered
	$values = clients_tags_getData($ctlData,$id);
	
	## for now we only want the text elements
	$value = join(',',array_values($values));
	
	$template->set_var("title",$ctlData['NAME']);
	$template->set_var("attribute",$ctlData['IDENTIFIER']);
	$template->set_var("value",$value);		

	## finally fill the template and return it
	return $template->fill_block('attribute');	
}

## =======================================================================        
##  clients_tags_deleteData        
## =======================================================================        
##  deletes the checkBox entries for a certain client
##
## =======================================================================        
function clients_tags_deleteData($ctlData,$clientID) {	
	## prepare the db object
	$db = new DB_Sql();
	$db_inner = new DB_Sql(); 
	
	$identifier = strtolower($ctlData['IDENTIFIER']);
	
	## in order to keep the number of tags as low as possible- we need to delete them one by one
	## and check if they are used somewhere else
	$query = "SELECT item_id FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."2".$identifier." WHERE client_id='$clientID'";
	$result_pointer = $db->query($query);
	
	while($db->next_record(MYSQL_ASSOC)) {
		## okay now we need to update the counter for this tag
		$tag_id = $db->Record['item_id'];
		
		## okay now decrease the counter for this tag
		$query = "UPDATE ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_".$identifier." SET counter = counter -1 WHERE id='$tag_id'";
		$db_inner->query($query);
	}
	
	## now deleet all tags
	$query = "DELETE FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."2".$identifier." WHERE client_id='$clientID'";
	$result_pointer = $db->query($query);	
	
	## finally clean up the mein tag table
	$query = "DELETE FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_".$identifier." WHERE counter <= 0";
	$result_pointer = $db->query($query);	
		
}


## =======================================================================        
##  clients_tags_getData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_tags_getData($ctlData,$clientID) {	
	## db object
	$db_connection = new DB_Sql();
	
	$identifier = strtolower($ctlData['IDENTIFIER']);
	##first check if the database was setup
	clients_tags_setup($identifier);
	
	## in order to get the appropriate data- we need to join the connector, with the 
	## clients and data tables
	$query = "SELECT C.* FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." AS A, ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'2'.$identifier." AS B, ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'_'.$identifier." AS C WHERE A.id=B.client_id AND B.item_id=C.id AND A.id='".$clientID."'";
	$result_pointer = $db_connection->query($query);	

	$value = array();
	while($db_connection->next_record(MYSQL_ASSOC)) {
		$value[$db_connection->Record['id']] = $db_connection->Record['text'];
	}	
	return $value;
}

## =======================================================================        
##  clients_tags_storeInput        
## =======================================================================        
##  storing needs to handle multiple inputs
##
## =======================================================================        
function clients_tags_storeInput($ctlData,$clientID) {
	global $Auth,$gSession;

	## check the setup
	$identifier = strtolower($ctlData['IDENTIFIER']);
	clients_tags_setup($ctlData['IDENTIFIER']);

	## first we will delete any previously set entries
	clients_tags_deleteData($ctlData,$clientID);
	
	## finally stroe the element
	_tags_storeInput($clientID,$identifier,$_POST[$identifier]);	
}

## =======================================================================        
##  clients_tags_storeInput        
## =======================================================================        
##  storing needs to handle multiple inputs
##
## =======================================================================        
function _tags_storeInput($id,$identifier,$tags) {
	global $Auth,$gSession;

	## okay first we need to prepare the input
	$tags = htmlentities($tags);
	
	## okay now split it up and clean them
	$tags = split(',',$tags);
	$tags = array_unique($tags);
		
	## prepare the db object
	$db_connection = new DB_Sql();
	
	## now we need to process each of the tags
	foreach($tags as $current_tag) {
		if(!empty($current_tag)) {
			$current_tag = mysql_real_escape_string(trim($current_tag));
			
			## okay first we need to check if the tag already exists
			$query = "SELECT id FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_".$identifier."  WHERE text='$current_tag'";
			$result_pointer = $db_connection->query($query);	
	
			if($db_connection->num_rows() > 0) {
				
				$db_connection->next_record();
				$tag_id = $db_connection->Record['id'];
				
				## update the counter
				$query = "UPDATE ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_".$identifier." SET counter = counter +1 WHERE id='$tag_id'";
				$db_connection->query($query);	
			} else {
				## okay we need to insert the new tag
				$query = "INSERT INTO ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_".$identifier." (text,counter) values ('$current_tag',1)";
				$rp = $db_connection->query($query);
				$tag_id = $db_connection->db_insertid($rp);	
			}
			
			## okay we now have the tag id- let's connect it to the object
			$query = "INSERT INTO ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."2".$identifier." (client_id,item_id) values ('$id','$tag_id')";
			$result_pointer = $db_connection->query($query);
		}
	}	
}
## =======================================================================        
##  clients_tags_setup        
## =======================================================================        
## creates the two required tables if needed
##
## =======================================================================        
function clients_tags_setup($identifier) {
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
		$query = 'CREATE TABLE '.DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'_'.$identifier.' (`id` int(10) NOT NULL auto_increment,`counter` INT(10) NOT NULL, `text` varchar(255) NOT NULL default \'\', PRIMARY KEY (`id`))';
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
function clients_tags_setupSearch($ctlData,$data,$element_count) {
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
	$values = clients_tags_getValues($ctlData['IDENTIFIER']);
	
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
function clients_tags_storeSearchData($i,$ctlData) {
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
function clients_tags_getValues($identifier) {
	$identifier = strtolower($identifier);
	##first check if the database was setup
	clients_tags_setup($identifier);	
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
function clients_tags_getSearchQuery($ctlData,$searchRow) {
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
##  clients_tags_getData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_tags_getExportData($ctlData,$clientID) {	
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
function clients_tags_importData($id,$ctlData,$data) {
	global $Auth,$gSession;

	## we are responsible to import all elements of our type
	$values = array();

	foreach($ctlData as $current_element) {
		if($current_element['TYPE'] == 'TAGS' && isset($data[$current_element['IDENTIFIER']])) {
			## clear this elements tags
			clients_tags_deleteData($current_element,$id);
			
			## okay now we need to process this as we would if the user has entered a string
			_tags_storeInput($id,$current_element['IDENTIFIER'],$data[$current_element['IDENTIFIER']]);
		}
	}
	

}

?>