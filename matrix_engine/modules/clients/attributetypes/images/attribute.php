<?php
## =======================================================================        
##  clients_displayInputForm        
## =======================================================================        
##  this is a helper function which calls a couple of other functions
##  in a certain order in order to display the input form
##
## =======================================================================        
function clients_images_displayInput($ctlData,$id) {
	global $Auth,$gSession;
	
	$identifier = strtolower($ctlData['IDENTIFIER']);

	## prepare the template
	$inputFile = "input.tpl";
	$template = new Template(ENGINE.'modules/clients/attributetypes/images/interface');
	$template->set_templatefile(array("attribute" => $inputFile,"row" => $inputFile));

	
	## check if we need to setup this attribute type
	clients_images_setup($identifier);
	
	## then we need to get any data that was previously entered
	$data = clients_images_getData($ctlData,$id);
	
	## okay we will display the images that are already stored for this
	## element. We will display them in rows
	$output = '';
	$counter = 1;
	foreach($data as $current_image) {
		## prepare the id of this image
		$current_id = $current_image['id'];
		
		## first we prepare the path
		$image_file = ABSOLUTE_UPLOAD_DIR.$GLOBALS['_MODULE_DATAOBJECTS_NAME'] .'/'.$identifier.'/'.$current_image['filename'];

		if($current_image['width'] > 0) {
			## now calculate the size
			$scale = 96/$current_image['width'];
			$width = (int) ($current_image['width']*$scale);
			$height = (int) ($current_image['height']*$scale);
		
			$image = '<a href="#" onclick="toggleSelection(\'images\',\''.$identifier.'\',\''.$current_id.'\');"><img src="'.$image_file.'" width="'.$width.'" height="'.$height.'" border="0" class="images_thumbnail" id="images_'.$identifier.'_'.$current_id.'">';
			
		} else {
			$image = '<a href="#" onclick="toggleSelection(\'images\',\''.$identifier.'\',\''.$current_id.'\');"><img src="../../interface/images/blank.gif" width="96" height="62" border="0" class="images_thumbnail" id="images_'.$identifier.'_'.$current_id.'">';
		}			

		$template->set_var('IMAGE'.$counter,$image);
		
		if($counter == 4) {
			$counter = 0;
			$output .= $template->fill_block('row');
			$template->reset_vars();
		}
		$counter++;
	}
	
	if($counter <= 4) {
		$output .= $template->fill_block('row');
	}
	

	## finally fill the template and return it
	## generate the editor url
	$addElementURL = "../clients/attributetypes/images/editor.php?op=edit&object=".$id."&attribute=".$identifier."&source=".$GLOBALS['_MODULE_DATAOBJECTS_NAME'];
	$addElementURL = $gSession->url($addElementURL);
	$template->set_var('addElementURL',$addElementURL);
	
	$template->set_var("title",$ctlData['NAME']);
	$template->set_var("images",$output);

	## finally fill the template and return it
	return $template->fill_block('attribute');	
}

## =======================================================================        
##  clients_images_deleteData        
## =======================================================================        
##  deletes the checkBox entries for a certain client
##
## =======================================================================        
function clients_images_deleteData($ctlData,$clientID) {	
	## prepare the db object
	$db_connection = new DB_Sql(); 
	
	$identifier = strtolower($ctlData['IDENTIFIER']);
	/*
	## first we will delete any previously set entries
	$query = "DELETE FROM ".DB_PREFIX."clients2".$identifier." WHERE client_id='$clientID'";
	$result_pointer = $db_connection->query($query);	
	*/
}


## =======================================================================        
##  clients_images_getData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_images_getData($ctlData,$clientID) {	
	## we will gather the data required for the 
	## simple display of this object
	$db_connection = new DB_Sql();
	
	$identifier = strtolower($ctlData['IDENTIFIER']);
	
	## we need to fetch all imag elements for this event
	
	## try to fetch the required information
	$query = "SELECT * FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_".$identifier." WHERE element_id='$clientID' ORDER BY order_number DESC";
	$result_pointer = $db_connection->query($query);	

	$data = array();
	while($db_connection->next_record(MYSQL_ASSOC)) {
		$data[] = $db_connection->Record;
	}	

	return $data;
}

## =======================================================================        
##  clients_images_storeInput        
## =======================================================================        
##  storing needs to handle multiple inputs
##
## =======================================================================        
function clients_images_storeInput($ctlData,$clientID) {
	global $Auth,$gSession;

	## now we update the appropriate client
	$db_connection = new DB_Sql(); 
	
	$identifier = strtolower($ctlData['IDENTIFIER']);
	/*
	##first check if the database was setup
	clients_images_setup($ctlData['IDENTIFIER']);

	## we need to prepare the input - needs to be done properly
	$data = $_POST[$identifier];

	## first we will delete any previously set entries
	$query = "SELECT * FROM ".DB_PREFIX."clients2".$identifier." WHERE client_id='$clientID'";
	$result_pointer = $db_connection->query($query);	
	
	if($db_connection->num_rows() > 0) {
		$query = "UPDATE ".DB_PREFIX."clients2".$identifier." SET item_id= '$data' WHERE client_id='$clientID'";
	} else {	
		$query = "INSERT INTO ".DB_PREFIX."clients2".$identifier." (client_id,item_id) values ('$clientID','$data')";
	}
	$result_pointer = $db_connection->query($query);
	*/
}

## =======================================================================        
##  clients_images_setup        
## =======================================================================        
## creates the two required tables if needed
##
## =======================================================================        
function clients_images_setup($identifier) {
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
		## create the table- this element will be linked to the main element via the client_id
		$query = 'CREATE TABLE '.DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'_'.$identifier.' (`id` int(11) NOT NULL auto_increment, `element_id` int(11) NOT NULL default \'0\', `filename` varchar(80) NOT NULL default \'\', `width` smallint(6) NOT NULL default \'0\', `height` smallint(6) NOT NULL default \'0\', `caption` mediumtext NOT NULL, `order_number` smallint(6) NOT NULL default \'0\', PRIMARY KEY  (`id`));';
		$result_pointer = $db_connection->query($query);
	}	
	
}


## =======================================================================        
##  clients_images_setupSearch        
## =======================================================================        
##  this is called by the overviewpage- we will return the options for
##  the search. For this normal text type- these are: the standard search
##  fields+ and our entry in the pulldown menu
##
## =======================================================================        
function clients_images_setupSearch($ctlData,$data,$element_count) {
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
function clients_images_storeSearchData($i,$ctlData) {
	## you can't search for images
}

## =======================================================================        
##  clients_images_getSearchQuery       
## =======================================================================        
##  this function returns the query- required in order to retrieve 
##  the specified search values. it consists of 3 elements:
##  A) Tables required- seperated by commas
##  B) Joining of the required tables with the client
##  C) the actual search pattern
##
## =======================================================================        
function clients_images_getSearchQuery($ctlData,$searchRow) {
	## you can't search for images
}

## =======================================================================        
##  clients_images_getExportData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_images_getExportData($ctlData,$clientID) {	
	## currently images are not exported

}
?>