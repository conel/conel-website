<?php
## =======================================================================        
##  clients_displayInputForm        
## =======================================================================        
##  this is a helper function which calls a couple of other functions
##  in a certain order in order to display the input form
##
## =======================================================================        
function clients_reviews_displayInput($ctlData,$id) {
	global $Auth,$gSession;
	
	$identifier = strtolower($ctlData['IDENTIFIER']);

	## prepare the template
	$inputFile = "input.tpl";
	$template = new Template(ENGINE.'modules/clients/attributetypes/reviews/interface');
	$template->set_templatefile(array("attribute" => $inputFile));
	
	## check if we need to setup this attribute type
	clients_reviews_setup($identifier);
	
	## basically we need to display statistics about the number of
	## reviews written for this entry- as well as the date of the last entry
	
	## additional functionality will be handled by the external editor
	## (coule be a extra module and we will link this editor directly)
	
	## then we need to get any data that was previously entered
	$data = clients_reviews_getData($ctlData,$id);
	
	$template->set_var("review_count",$data['count']);
	$template->set_var("review_lastpost",$data['latest_entry']);
	$template->set_var("review_rating",round($data['average_rating'],2).' ('.$data['standard_variation'].')');

	## finally fill the template and return it
	return $template->fill_block('attribute');	
}

## =======================================================================        
##  clients_reviews_deleteData        
## =======================================================================        
##  deletes the checkBox entries for a certain client
##
## =======================================================================        
function clients_reviews_deleteData($ctlData,$clientID) {	
	## deleting the date should normally remove all reviews for this 
	## element. But we need to check if this makes sense
}


## =======================================================================        
##  clients_reviews_getData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_reviews_getData($ctlData,$elementID) {	
	## we will gather the data required for the 
	## simple display of this object
	$db_connection = new DB_Sql();
	
	$identifier = strtolower($ctlData['IDENTIFIER']);
	
	## try to fetch the required information
	$query = "SELECT entered FROM ".DB_PREFIX."reviews WHERE eventid='$elementID' ORDER BY entered DESC";
	$result_pointer = $db_connection->query($query);	

	$data = array();
	while($db_connection->next_record()) {
		$data['count'] = $db_connection->num_rows();
		$data['latest_entry'] = $db_connection->Record['entered'];
	}	

	$query = "SELECT AVG(rating) as average_rating, STD(rating) as standard_variation FROM ".DB_PREFIX."reviews WHERE eventid='$elementID' GROUP BY eventid";
	$result_pointer = $db_connection->query($query);	

	if($db_connection->next_record()) {
		$data['average_rating'] = $db_connection->Record['average_rating'];
		$data['standard_variation'] = $db_connection->Record['standard_variation'];
	}	
	
	return $data;
}

## =======================================================================        
##  clients_reviews_storeInput        
## =======================================================================        
##  storing needs to handle multiple inputs
##
## =======================================================================        
function clients_reviews_storeInput($ctlData,$clientID) {
	##storage etc. will be handled by an extra module
}

## =======================================================================        
##  clients_reviews_setup        
## =======================================================================        
## creates the two required tables if needed
##
## =======================================================================        
function clients_reviews_setup($identifier) {
}


## =======================================================================        
##  clients_reviews_setupSearch        
## =======================================================================        
##  this is called by the overviewpage- we will return the options for
##  the search. For this normal text type- these are: the standard search
##  fields+ and our entry in the pulldown menu
##
## =======================================================================        
function clients_reviews_setupSearch($ctlData,$data,$element_count) {
	## you can't search for images
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
function clients_reviews_storeSearchData($i,$ctlData) {
	## you can't search for images
}

## =======================================================================        
##  clients_reviews_getSearchQuery       
## =======================================================================        
##  this function returns the query- required in order to retrieve 
##  the specified search values. it consists of 3 elements:
##  A) Tables required- seperated by commas
##  B) Joining of the required tables with the client
##  C) the actual search pattern
##
## =======================================================================        
function clients_reviews_getSearchQuery($ctlData,$searchRow) {
	## you can't search for images
}

## =======================================================================        
##  clients_reviews_getExportData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_reviews_getExportData($ctlData,$clientID) {	
	## currently images are not exported

}
?>