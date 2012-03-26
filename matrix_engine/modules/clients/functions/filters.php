<?php
## =======================================================================        
## clients_displayInputName     
## =======================================================================        
## displays a dialog for naming a filter
## input the newsletter id, and if available the old text+ the actionURL
##    
## =======================================================================
function clients_displayInputName($text,$actionURL,$current_name=null) {
	global $gSession,$Auth;
	## prepare the template file
	$select_template = new Template(ENGINE.'modules/clients/interface/');
	$select_template->set_templatefile(array("header" => "namefilter.tpl","body" => "namefilter.tpl","footer" => "namefilter.tpl"));

	$select_template->set_var("saveIMG","lang/".$Auth->auth["language"]."_button_save.gif");
	$select_template->set_var('language_pagename',LANG_MODULE_CLIENTS_FilterTitle);
	$select_template->set_var('language_pagenamedesc',LANG_MODULE_CLIENTS_FilterTitleDesc);

	$select_template->set_var('text',$text);
	
	if(isset($current_name)) {
		$select_template->set_var('menu_text',$current_name);
	}
	
	$select_template->set_var('actionURL',$actionURL);
	## okay now we start the ouput
	$select_template->pfill_block("header");	
	$select_template->pfill_block("body");
	
	$output .= '<input type="hidden" name="cmd" value="savefilter">'; 
		
	$select_template->set_var('hiddenfields',$output);
	
	$select_template->pfill_block("footer");
}

## =======================================================================        
## clients_storeFilter    
## =======================================================================        
## stores the filter- we will get a query id, and a filter name-
## the queryid will be converted to a actual query. and then both are
## stored in the database
##    
## =======================================================================
function clients_storeFilter($data, $text) {
	## we need to store the query as well as the operations that led to the query
	## so we will generate everything using the data that is used to generate the query

	## get the field info
	$ctl_data = _getFieldsFromGroup(1);
	$query = _prepareSearch(1,$data,$ctl_data,1,1);

	## prepare the data for storage
	$searchdata = serialize($data);

	## prepare the db-object
	$db = new DB_Sql();
	
	## then insert everything into the db
	## first we neew to determine the number of clients
	$query = 'INSERT INTO '.DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_filters (name,query,searchdata) VALUES('$text', \"$query\",'$searchdata')";
	$rp = $db->query($query);
}


## =======================================================================        
##  clients_getFilters        
## =======================================================================        
##  returns all clients   
##
##  TODO:
##       - allow to limit the results in order to implement paging
## =======================================================================        
function clients_getFilters($filter='') {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## prepare the db-object
	$db = new DB_Sql();
	
	if($filter == '') {
		## then get the correct values
		$query = "SELECT * FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_filters AS A ORDER BY A.name";
	} else {
		$query = "SELECT * FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_filters WHERE visible='1' ORDER BY name";
	}

	$result = $db->query($query);	
	$counter = 0;
	$return_value = array();
	while($db->next_record(MYSQL_ASSOC)) {
		$return_value[] = $db->Record;
	}
	return $return_value;	
}


## =======================================================================        
##  clients_toggleFilterVisibility        
## =======================================================================        
##  returns all clients   
##
##  TODO:
##       - allow to limit the results in order to implement paging
## =======================================================================        
function clients_toggleFilterVisibility($filter_id) {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## prepare the db-object
	$db = new DB_Sql();
	
	$query = "UPDATE ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_filters SET visible = NOT visible WHERE id='$filter_id'";
	$result = $db->query($query);	
}

## =======================================================================        
##  clients_getFilter        
## =======================================================================        
##  returns all clients   
##
##  TODO:
##       - allow to limit the results in order to implement paging
## =======================================================================        
function clients_getFilter($id) {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## prepare the db-object
	$db = new DB_Sql();

	## then get the correct values
	$query = "SELECT * FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_filters WHERE id='$id'";

	$result = $db->query($query);	
	$counter = 0;
	$return_value = array();
	if($db->next_record(MYSQL_ASSOC)) {
		$return_value = $db->Record;
	}
	return $return_value;	
}


## =======================================================================        
##  clients_deleteClients        
## =======================================================================        
##  deletes all clients (and their attributes) that are identified
##  by the array supplied.
##
##  TODO:
##  
## ======================================================================= 
function clients_deleteFilters($filter_list) {
	## just call the single delete function a couple of times
	if(is_array($filter_list)) {
		foreach($filter_list as $current_filter) {
			clients_deleteFilter($current_filter);
		}
	}
}

## =======================================================================        
##  clients_deleteClient        
## =======================================================================        
##  deletes a single client- calls all it's attributes and deletes them
## as well.
##
##  TODO:
##  
## ======================================================================= 
function clients_deleteFilter($id) {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## prepare the db-object
	$db_connection = new DB_Sql();
	
	$query = "DELETE FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_filters WHERE id='".$id."'";
	$rp = $db_connection->query($query);	
}
?>