<?php
## =======================================================================        
##  clients_displayInputForm        
## =======================================================================        
##  this is a helper function which calls a couple of other functions
##  in a certain order in order to display the input form
##
## =======================================================================        
function clients_userpage_displayInput($ctlData,$id) {
	global $Auth,$gSession;
		
	## setup 
	clients_userpage_setup($ctlData['IDENTIFIER']);
	## get the data for the marketing plan
	$values = clients_userpage_getData($ctlData,$id);
	
	## and finally we output the input form
	## prepare the template
	$inputFile = "input.tpl";
	$template = new Template(ENGINE.'modules/clients/attributetypes/userpage/interface');
	$template->set_templatefile(array("attribute" => $inputFile));
	
	$output = '';
	foreach($values as $current_value) {
		$page_id = $current_value['page_id'];
			
	## output the first row as a dummy
	$output .= '<tr><td valign="top" colspan="7" bgcolor="#FFFFFF"><img src="../clients/interface/images/blank.gif" width="301" height="2"></td></tr>	
	<tr><td valign="top" colspan="8" bgcolor="#EEEEEE"><img src="../clients/interface/images/blank.gif" width="301" height="1"></td></tr>					
			<tr bgcolor="#EEEEEE">
				<td valign="top" bgcolor="#EEEEEE" colspan="3">&nbsp; '.$page_id.'</td>
				<td valign="middle" width="2" bgcolor="#EEEEEE"><img src="../clients/interface/images/blank.gif" width="2" height="1"></td>
				<td valign="top" bgcolor="#EEEEEE">&nbsp;  </td>
				<td valign="middle" width="2" bgcolor="#EEEEEE"><img src="../clients/interface/images/blank.gif" width="2" height="1"></td>
			</tr>';
	}		
	$template->set_var("ITEMS",$output);	
	## finally fill the template and return it
	
	$template->set_var("title",$ctlData['NAME']);
	$template->set_var("attribute",$ctlData['IDENTIFIER']);
	$template->set_var("value",$value);	

	return $template->fill_block('attribute');	
}

## =======================================================================        
##  clients_userpage_deleteData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_userpage_deleteData($ctlData,$clientID) {	
	## prepare the db object
	$db_connection = new DB_Sql(); 
	
	## first we will delete any previously set entries
	$query = "DELETE FROM ".DB_PREFIX."clients_userpages WHERE client_id='$clientID'";
	$result_pointer = $db_connection->query($query);	
	
}


## =======================================================================        
##  clients_userpage_getData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_userpage_getData($ctlData,$clientID) {	
	## db object
	$db_connection = new DB_Sql();
	
	$query = "SELECT * FROM ".DB_PREFIX."clients_userpages WHERE client_id='$clientID'";
	$result_pointer = $db_connection->query($query,false);	
	
	$value =array();
	while($db_connection->next_record()) {
		$page_id 	= $db_connection->Record['page_id'];
		$client_id 	= $db_connection->Record['client_id'];
		
		$value[] = array('page_id'=>$page_id,'client_id'=>$client_id);
	}	
	return $value;
}

## =======================================================================        
##  clients_userpage_storeInput        
## =======================================================================        
##  processes the data entered by the user- and stores the data
##  in the correct table
##
## =======================================================================        
function clients_userpage_storeInput($ctlData,$clientID) {
	## we dont' store anything because the user editor can't change anything
}

## =======================================================================        
##  clients_checkbox_setup        
## =======================================================================        
## creates the two required tables if needed
##
## =======================================================================        
function clients_userpage_setup() {
	## let's check if we can find the field in the main client table
	## db class
	$db_connection = new DB_Sql();  

	## make the fields lowercase
	$identifier = strtolower($identifier);

	## lets'findout if the tables already exist
	$query = "SHOW TABLES LIKE '".DB_PREFIX."clients_%'";
	$result_pointer = $db_connection->query($query);
	
	$exists = false;
	while($db_connection->next_record()) {
		if($db_connection->Record[0] == (DB_PREFIX.'clients_userpages') || $db_connection->Record[0] == strtolower(DB_PREFIX.'clients_userpages')) {
			$exists = true;
		}
	}

	if(!$exists) {
		## now the connector
		$query = 'CREATE TABLE '.DB_PREFIX.'clients_userpages (`client_id` INT(10) NOT NULL ,`page_id` INT(10) NOT NULL)';
		$result_pointer = $db_connection->query($query);
	}		
}

## =======================================================================        
##  clients_userpage_setupSearch        
## =======================================================================        
##  this is called by the overviewpage- we will return the options for
##  the search. For this normal text type- these are: the standard search
##  fields+ and our entry in the pulldown menu
##
## =======================================================================        
function clients_userpage_setupSearch($ctlData) {
	## we do not support searching of this kind of data
}

## =======================================================================        
##  clients_userpage_getSearchQuery       
## =======================================================================        
##  this function returns the query- required in order to retrieve 
##  the specified search values. it consists of 3 elements:
##  A) Tables required- seperated by commas
##  B) Joining of the required tables with the client
##  C) the actual search pattern
##
## =======================================================================        
function clients_userpage_getSearchQuery($ctlData,$searchRow) {
	## we do not support searching of this kind of data
}

?>