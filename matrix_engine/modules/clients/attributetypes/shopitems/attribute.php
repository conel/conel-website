<?php
## =======================================================================        
##  clients_displayInputForm        
## =======================================================================        
##  this is a helper function which calls a couple of other functions
##  in a certain order in order to display the input form
##
## =======================================================================        
function clients_shopitems_displayInput($ctlData,$id) {
	global $Auth,$gSession;
	
	## check if we need to setup this attribute type
	clients_shopitems_setup($ctlData['IDENTIFIER']);
	
	## then we need to get any data that was previously entered (not yet)
	$values = clients_shopitems_getData($ctlData,$id);
	##$value = htmlentities($value);
	## and finally we output the input form
	## prepare the template
	$inputFile = "input.tpl";
	$template = new Template(ENGINE.'modules/clients/attributetypes/shopitems/interface');
	$template->set_templatefile(array("attribute" => $inputFile));
		
	## finally fill the template and return it
	
	$template->set_var("title",$ctlData['NAME']);
	$template->set_var("attribute",$ctlData['IDENTIFIER']);
	$template->set_var("today",$values['today']);
	$template->set_var("month",$values['month']);	

	return $template->fill_block('attribute');	
}

## =======================================================================        
##  clients_shopitems_deleteData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_shopitems_deleteData($ctlData,$clientID) {	
	## the text element is stored within the main client table-
	## so we don't need to delete anything- it will be gone already
}


## =======================================================================        
##  clients_shopitems_getData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_shopitems_getData($ctlData,$clientID) {	
	## db object
	$db_connection = new DB_Sql();
	/*
	$today = mktime(0, 0, 0, date("m"), date("d"), date("Y"));	
	
	$query = "SELECT visitors FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'_'.$ctlData['IDENTIFIER']." WHERE object_id='$clientID' AND `timestamp` = $today";
	$result_pointer = $db_connection->query($query);	
	
	$values = array();
	if($db_connection->next_record()) {
		$values['today'] = stripslashes($db_connection->Record['visitors']);
	}
	
	## now we handle this month
	$month_start = mktime(0, 0, 0, date("m"), 1, date("Y"));	
	$month_end = mktime(0, 0, 0, date("m"), date("j"), date("Y"));
	
	$query = "SELECT visitors FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'_'.$ctlData['IDENTIFIER']." WHERE object_id='$clientID' AND timestamp BETWEEN $month_start AND $month_end ";
	$result_pointer = $db_connection->query($query);	
	
	if($db_connection->next_record()) {
		$values['month'] = stripslashes($db_connection->Record['visitors']);
	}
	*/
	return $values;
}


## =======================================================================        
##  clients_shopitems_storeInput        
## =======================================================================        
##  processes the data entered by the user- and stores the data
##  in the correct table
##
## =======================================================================        
function clients_shopitems_storeInput($ctlData,$clientID) {
}

## =======================================================================        
##  clients_shopitems_storeInput        
## =======================================================================        
##  processes the data entered by the user- and stores the data
##  in the correct table
##
## =======================================================================        
function clients_shopitems_validateInput($ctlData,$clientID) {
}


## =======================================================================        
##  clients_shopitems_setup        
## =======================================================================        
##  is called to see if we need to setup this attribute type- and if yes
##  it sets itself up
##
## =======================================================================        
function clients_shopitems_setup($identifier) {
	## let's check if we can find the field in the main client table
	$db_connection = new DB_Sql();  

	## make the fields lowercase
	$identifier = strtolower($identifier);
	
	## lets'findout if the tables already exist
	$query = "SHOW TABLES LIKE '".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."%'";
	$result_pointer = $db_connection->query($query);
	
	$exists = false;
	while($db_connection->next_record()) {

		if($db_connection->Record[0] == (DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'_'.$identifier)) {
			$exists = true;
		}
	}
	

	if(!$exists) {
		## the base
		$query = 'CREATE TABLE '.DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'_'.$identifier." (
		`product_id` int(11) NOT NULL default '0',
  `product` varchar(55) NOT NULL default '0',
  `quantity` int(10) NOT NULL default '0',
  `status` int(1) NOT NULL default '0',
  `object_id` int(10) NOT NULL default '0',
  `price` float NOT NULL default '0',
  `vat` float NOT NULL default '0',
  `shipping` float NOT NULL default '0',
  `subtotal` float NOT NULL default '0',
  KEY `product` (`product`),
  KEY `order_id` (`object_id`));";
		
		$result_pointer = $db_connection->query($query);
	}	
}

## =======================================================================        
##  clients_shopitems_setupSearch        
## =======================================================================        
##  this is called by the overviewpage- we will return the options for
##  the search. For this normal text type- these are: the standard search
##  fields+ and our entry in the pulldown menu
##
## =======================================================================        
function clients_shopitems_setupSearch($ctlData,$data,$element_count) {
}

## =======================================================================        
##  clients_shopitems_getSearchQuery       
## =======================================================================        
##  this function returns the query- required in order to retrieve 
##  the specified search values. it consists of 3 elements:
##  A) Tables required- seperated by commas
##  B) Joining of the required tables with the client
##  C) the actual search pattern
##
## =======================================================================        
function clients_shopitems_getSearchQuery($ctlData,$data) {
}


## =======================================================================        
##  clients_shopitems_storeSearchData       
## =======================================================================        
##  this function returns the query- required in order to retrieve 
##  the specified search values. it consists of 3 elements:
##  A) Tables required- seperated by commas
##  B) Joining of the required tables with the client
##  C) the actual search pattern
##
## =======================================================================        
function clients_shopitems_storeSearchData($i) {
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
function clients_shopitems_getSearchFields($ctlData) {
	$identifier = strtolower($ctlData['IDENTIFIER']);
}	
	

## =======================================================================        
##  clients_shopitems_getExportData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_shopitems_getExportData($ctlData,$clientID) {	
	return clients_shopitems_getData($ctlData,$clientID);
}
?>