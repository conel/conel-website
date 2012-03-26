<?php
## =======================================================================        
##  clients_displayInputForm        
## =======================================================================        
##  this is a helper function which calls a couple of other functions
##  in a certain order in order to display the input form
##
## =======================================================================        
function clients_objecturl_displayInput($ctlData,$id) {
	global $Auth,$gSession;
	
	## check if we need to setup this attribute type
	clients_objecturl_setup($ctlData['IDENTIFIER']);
	
	## then we need to get any data that was previously entered (not yet)
	$value = clients_objecturl_getData($ctlData,$id);
	$value = htmlentities($value);
	## and finally we output the input form
	## prepare the template
	$inputFile = "input.tpl";
	$template = new Template(ENGINE.'modules/clients/attributetypes/objecturl/interface');
	$template->set_templatefile(array("attribute" => $inputFile));
		
	## finally fill the template and return it
	
	$template->set_var("title",$ctlData['NAME']);
	$template->set_var("value",str_replace('[ID]',$id,$ctlData['URL']));	

	return $template->fill_block('attribute');
}

## =======================================================================        
##  clients_objecturl_deleteData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_objecturl_deleteData($ctlData,$clientID) {	
	## the text element is stored within the main client table-
	## so we don't need to delete anything- it will be gone already
}


## =======================================================================        
##  clients_objecturl_getData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_objecturl_getData($ctlData,$clientID) {	
}


## =======================================================================        
##  clients_objecturl_storeInput        
## =======================================================================        
##  processes the data entered by the user- and stores the data
##  in the correct table
##
## =======================================================================        
function clients_objecturl_storeInput($ctlData,$clientID) {
}

## =======================================================================        
##  clients_objecturl_storeInput        
## =======================================================================        
##  processes the data entered by the user- and stores the data
##  in the correct table
##
## =======================================================================        
function clients_objecturl_validateInput($ctlData,$input_data = null) {
}


## =======================================================================        
##  clients_objecturl_setup        
## =======================================================================        
##  is called to see if we need to setup this attribute type- and if yes
##  it sets itself up
##
## =======================================================================        
function clients_objecturl_setup($identifier) {	
}

## =======================================================================        
##  clients_objecturl_setupSearch        
## =======================================================================        
##  this is called by the overviewpage- we will return the options for
##  the search. For this normal text type- these are: the standard search
##  fields+ and our entry in the pulldown menu
##
## =======================================================================        
function clients_objecturl_setupSearch($ctlData,$data,$element_count) {
}

## =======================================================================        
##  clients_objecturl_getSearchQuery       
## =======================================================================        
##  this function returns the query- required in order to retrieve 
##  the specified search values. it consists of 3 elements:
##  A) Tables required- seperated by commas
##  B) Joining of the required tables with the client
##  C) the actual search pattern
##
## =======================================================================        
function clients_objecturl_getSearchQuery($ctlData,$data) {

}


## =======================================================================        
##  clients_objecturl_storeSearchData       
## =======================================================================        
##  this function returns the query- required in order to retrieve 
##  the specified search values. it consists of 3 elements:
##  A) Tables required- seperated by commas
##  B) Joining of the required tables with the client
##  C) the actual search pattern
##
## =======================================================================        
function clients_objecturl_storeSearchData($i) {

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
function clients_objecturl_getSearchFields($ctlData) {
}	
	

## =======================================================================        
##  clients_objecturl_getExportData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_objecturl_getExportData($ctlData,$clientID) {	
}



## =======================================================================        
##  clients_objecturl_importData        
## =======================================================================        
##  imports the data for all elements of this attribute type in a data set
##
## =======================================================================        
function clients_objecturl_importData($id,$ctlData,$data) {
}
?>