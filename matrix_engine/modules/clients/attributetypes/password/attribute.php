<?php
## =======================================================================        
##  clients_displayInputForm        
## =======================================================================        
##  this is a helper function which calls a couple of other functions
##  in a certain order in order to display the input form
##
## =======================================================================        
function clients_password_displayInput($ctlData,$id) {
	global $Auth,$gSession;
	
	$identifier = strtolower($ctlData['IDENTIFIER']);

	## prepare the template
	$inputFile = "input.tpl";
	$template = new Template(ENGINE.'modules/clients/attributetypes/password/interface');
	$template->set_templatefile(array("attribute" => $inputFile));

	
	## check if we need to setup this attribute type
	clients_password_setup($identifier);
	
	## then we need to get any data that was previously entered
	$values = clients_password_getData($ctlData,$id);
	

	## now preare the HTMl-Code for it
	$output = '&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;';

	## finally fill the template and return it
	## generate the editor url
	$addElementURL = "attributetypes/password/editor.php?op=edit&element_id=".$id."&attribute=".$identifier."&source=".$GLOBALS['_MODULE_DATAOBJECTS_NAME'];
	$addElementURL = $gSession->url($addElementURL);
	$template->set_var('addElementURL',$addElementURL);

	$template->set_var("title",$ctlData['NAME']);
	$template->set_var("attribute",$output);

	## finally fill the template and return it
	return $template->fill_block('attribute');	
}

## =======================================================================        
##  clients_password_deleteData        
## =======================================================================        
##  deletes the checkBox entries for a certain client
##
## =======================================================================        
function clients_password_deleteData($ctlData,$clientID) {	
	## the text element is stored within the main client table-
	## so we don't need to delete anything- it will be gone already	
}


## =======================================================================        
##  clients_password_getData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_password_getData($ctlData,$clientID) {	
	## db object
	$db_connection = new DB_Sql();
	
	$query = "SELECT ".$ctlData['IDENTIFIER']." FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." WHERE id='$clientID'";
	$result_pointer = $db_connection->query($query);	
	
	$value ='';
	if($db_connection->next_record()) {
		$value = stripslashes($db_connection->Record[$ctlData['IDENTIFIER']]);
	}	
	return $value;
}

## =======================================================================        
##  clients_password_storeInput        
## =======================================================================        
##  storing needs to handle multiple inputs
##
## =======================================================================        
function clients_password_storeInput($ctlData,$clientID) {
	global $Auth,$gSession;

	##first check if the database was setup
	clients_text_setup($ctlData['IDENTIFIER']);
	
	## we need to prepare the input - needs to be done properly
	
	if(!empty($_POST[$ctlData['IDENTIFIER']])) {
		$data = $_POST[$ctlData['IDENTIFIER']];
		$data = mysql_real_escape_string($data);

		## now we update the appropriate client
		$db_connection = new DB_Sql();  
	
		$query = "UPDATE ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." SET ".$ctlData['IDENTIFIER']."= '$data' WHERE id='$clientID'";
		$result_pointer = $db_connection->query($query);
	}
}

## =======================================================================        
##  clients_password_storeInput        
## =======================================================================        
##  processes the data entered by the user- and stores the data
##  in the correct table
##
## =======================================================================        
function clients_password_validateInput($ctlData,$clientID) {
	global $Auth,$gSession;

	## we need to prepare the input - needs to be done properly
	$data = $_POST[$ctlData['IDENTIFIER']];
	
	## check if we have any validation commands
	$validation = $ctlData['VALIDATE'];
	$maxlength = $ctlData['MAXLENGTH'];
	$minlength = $ctlData['MINLENGTH'];

	if($ctlData['CONFIRM'] == true) {
		## okay we need to check if the password field was entered twice
		if($_POST[$ctlData['IDENTIFIER']] != $_POST[$ctlData['IDENTIFIER'].'_confirm']) {
			$validation_status = PASSWORDNOTEQUAL;
			return array('error'=>$validation_status,'data'=>$data);
		}
	}
	
	if(!empty($validation)) {
		## make sure the params get passed correctly	
		eval("\$validation = $validation;");
	
		## now prepare the validation class
		$validation_status = Validate::string($data,array('format'=>$validation,'max_length'=>$maxlength,'min_length'=>$minlength));
	} else {
		$validation_status = Validate::string($data,array());
	}
		
	## we should return the error code and the input from the user
	return array('error'=>$validation_status,'data'=>$data);
}


## =======================================================================        
##  clients_password_setup        
## =======================================================================        
## creates the two required tables if needed
##
## =======================================================================        
function clients_password_setup($identifier) {
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
		$query = 'ALTER TABLE '.DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].' ADD '.$identifier.' VARCHAR(80) NOT NULL';
		$result_pointer = $db_connection->query($query);
	}	
		
}

## =======================================================================        
##  clients_text_getExportData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_password_getExportData($ctlData,$clientID) {	
	return clients_password_getData($ctlData,$clientID);
}
?>