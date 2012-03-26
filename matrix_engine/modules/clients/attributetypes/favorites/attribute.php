<?php

## =======================================================================        
##  favorites
## =======================================================================        
##  stores/links dbobject elements to an dbobject entry. This is typically
##  used to store favorites for users that are logged. For the backend
##  we need fetch the desired field that should be displayed. The favorites
##  will be displayed in two columns
##
## =======================================================================        


## =======================================================================        
##  clients_favorites_displayInput        
## =======================================================================        
##  this is a helper function which calls a couple of other functions
##  in a certain order in order to display the input form
##
## =======================================================================        
function clients_favorites_displayInput($ctlData,$id) {
	global $Auth,$gSession;

	## check if we need to setup this attribute type
	clients_favorites_setup($ctlData['IDENTIFIER']);
	
	## then we need to get any data that was previously entered (not yet)
	$names = clients_favorites_getData($ctlData,$id);

	## and finally we output the input form
	## prepare the template
	$inputFile = "input.tpl";
	$template = new Template(ENGINE.'modules/clients/attributetypes/favorites/interface');
	$template->set_templatefile(array("attribute" => $inputFile,"rowstart" => $inputFile,"body" => $inputFile,"rowend" => $inputFile));
		
	## finally fill the template and return it
	$output = '';
	$counter = 0;
	
	## first we start with the element
	$output .= $template->fill_block('rowstart');
	$open = true;
	foreach($names as $object_id=>$object_name) {
		if(!$open) {
			$output .= $template->fill_block('rowstart');	
			$open = true;
		}
		## set the name
		$template->set_var("NAME",$object_name);
		$output .= $template->fill_block('body');	
		
		$counter++;
		if($counter %2 == 0) {
			$output .= $template->fill_block('rowend');	
			$open = false;
		}		
	}	
	
	if($open) {
		$output .= $template->fill_block('rowend');	
	}	

	return $output;		
}

## =======================================================================        
##  clients_favorites_deleteData        
## =======================================================================        
##  deletes the checkBox entries for a certain client
##
## =======================================================================        
function clients_favorites_deleteData($ctlData,$clientID) {	
	## now delete function	
}


## =======================================================================        
##  clients_favorites_getData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_favorites_getData($ctlData,$clientID) {	
	## db object
	$db_connection = new DB_Sql();
	
	$query = "SELECT target_id FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'_'.$ctlData['IDENTIFIER']." WHERE id='$clientID'";
	$result_pointer = $db_connection->query($query);	

	$objects = array();
	while($db_connection->next_record()) {
		$objects[] = $db_connection->Record['target_id'];
	}
	
	## okay now we need to fetch the name of the object by calling the objects functions
	$names = array();
	if(isset($ctlData['TARGETNAME'])) {
		## then we need to split it
		$name_element = split(':',$ctlData['TARGETNAME']);
		$type = strtolower($name_element[0]);
		
		## we need to include the settings file and the correct attribute
		## in order to recvoer the old settings we will stroe them here
		$current_settings['_MODULE_DATAOBJECTS_NAME'] = $GLOBALS['_MODULE_DATAOBJECTS_NAME'];
		$current_settings['_MODULE_DATAOBJECTS_DBPREFIX'] = $GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'];
		$current_settings['_MODULE_DATAOBJECTS_COOKIE'] = $GLOBALS['_MODULE_DATAOBJECTS_COOKIE'];
	
		@include(ENGINE.'modules/'.$ctlData['TARGET'].'/settings.php');
		include_once(ENGINE.'modules/clients/attributetypes/'.$type.'/attribute.php');	
		
		## loop through all entries and fetch their names
		foreach($objects as $current_object) {	
			eval("\$names[\$current_object] = clients_text_getData(array('IDENTIFIER'=>\$name_element[1]),\$current_object);");
		}

		## restore our settings again
		$GLOBALS['_MODULE_DATAOBJECTS_NAME'] = $current_settings['_MODULE_DATAOBJECTS_NAME'];
		$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'] = $current_settings['_MODULE_DATAOBJECTS_DBPREFIX'];
		$GLOBALS['_MODULE_DATAOBJECTS_COOKIE'] = $current_settings['_MODULE_DATAOBJECTS_COOKIE'];
	
	} else {
		## in this case we will only return the ids
		foreach($objects as $current_object) {
			$names[$current_object] = $current_object;
		}
	}
	
	## finally return the array containing the objects names
	return $names;
}

## =======================================================================        
##  clients_favorites_storeInput        
## =======================================================================        
##  storing needs to handle multiple inputs
##
## =======================================================================        
function clients_favorites_storeInput($ctlData,$clientID) {
	## the user cannot stroe anything in this object	
}

## =======================================================================        
##  clients_favorites_setup        
## =======================================================================        
## creates the two required tables if needed
##
## =======================================================================        
function clients_favorites_setup($identifier) {
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
		$query = 'CREATE TABLE '.DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'_'.$identifier.' (`id` int(10) NOT NULL, `target_id` int(10) NOT NULL,`entered` datetime default NULL, target VARCHAR(50) NOT NULL,`note` mediumtext,`alert` TINYINT(1),PRIMARY KEY  (`id`),KEY `target_id` (`target_id`),KEY `alert` (`alert`))';
		$result_pointer = $db_connection->query($query);
	}	
	
}


## =======================================================================        
##  clients_favorites_setupSearch        
## =======================================================================        
##  this is called by the overviewpage- we will return the options for
##  the search. For this normal text type- these are: the standard search
##  fields+ and our entry in the pulldown menu
##
## =======================================================================        
function clients_favorites_setupSearch($ctlData,$data,$element_count) {
	## now search
	## check if we need to setup this attribute type
	clients_favorites_setup($ctlData['IDENTIFIER']);	
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
function clients_favorites_storeSearchData($i,$ctlData) {
	## now search
}



## =======================================================================        
##  clients_favorites_getValues        
## =======================================================================        
##  returns a list of all possible values
##
## =======================================================================        
function clients_favorites_getValues($identifier) {
	## now search
}



## =======================================================================        
##  clients_favorites_getSearchQuery       
## =======================================================================        
##  this function returns the query- required in order to retrieve 
##  the specified search values. it consists of 3 elements:
##  A) Tables required- seperated by commas
##  B) Joining of the required tables with the client
##  C) the actual search pattern
##
## =======================================================================        
function clients_favorites_getSearchQuery($ctlData,$searchRow) {
	## now search
}

## =======================================================================        
##  clients_favorites_getExportData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_favorites_getExportData($ctlData,$clientID) {	
	## currently no export
}
?>