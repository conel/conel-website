<?php
## =======================================================================        
##  clients_displayInputForm        
## =======================================================================        
##  this is a helper function which calls a couple of other functions
##  in a certain order in order to display the input form
##
## =======================================================================        
function clients_image_displayInput($ctlData,$id) {
	global $Auth,$gSession;
	
	## check if we need to setup this attribute type
	clients_image_setup($ctlData['IDENTIFIER']);
	
	## then we need to get any data that was previously entered (not yet)
	$value = clients_image_getData($ctlData,$id);
	$filename = $value['filename'];

	## and finally we output the input form
	## prepare the template
	$inputFile = "input.tpl";
	$template = new Template(ENGINE.'modules/clients/attributetypes/image/interface');
	$template->set_templatefile(array("attribute" => $inputFile));

	## finally fill the template and return it
	if($filename) {
		$img_size = @GetImageSize(MATRIX_UPLOADDIR.$filename);
		$longest_side = max($img_size[0],$img_size[1]);
		if($longest_side > 90) {
			$factor = 90 / $longest_side;
			$img_size[0] = ($img_size[0]*$factor);
			$img_size[1] = ($img_size[1]*$factor);
		}
		$image = '<img src="'.SITE_ROOT.UPLOAD_DIR.$filename .'" alt="'.$alt.'" width="'.$img_size[0].'" height="'.$img_size[1].'">';								
		$template->set_var('file',$image);
	}	
	
	$template->set_var("title",$ctlData['NAME']);
	$template->set_var("DESC",$ctlData['DESCRIPTION']);
	$template->set_var("attribute",$ctlData['IDENTIFIER']);
	$template->set_var("value",$value);	

	return $template->fill_block('attribute');	
}

## =======================================================================        
##  clients_image_deleteData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_image_deleteData($ctlData,$objectID) {	
	## delete the image
	$identifier = $ctlData['IDENTIFIER'];
	## db object
	$db_connection = new DB_Sql();
	$query = "DELETE FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_image WHERE identifier= '$identifier' AND object_id='$objectID'";
	$result_pointer = $db_connection->query($query);	
}


## =======================================================================        
##  clients_image_getData        
## =======================================================================        
##  retrieves the data for a certain field of a certain client
##
## =======================================================================        
function clients_image_getData($ctlData,$objectID) {	
	$identifier = $ctlData['IDENTIFIER'];
	## db object
	$db_connection = new DB_Sql();
	
	$query = "SELECT * FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_image WHERE identifier= '$identifier' AND object_id='$objectID'";
	$result_pointer = $db_connection->query($query);	

	$value = array();
	if($db_connection->next_record()) {
		$value['filename'] 	= $db_connection->Record["filename"];
		$value['identifier'] = $db_connection->Record["identifier"]; 
		$value['width'] = $db_connection->Record["width"];
		$value['height'] = $db_connection->Record["height"];
		$value['alt'] = $db_connection->Record["alt"];
		
	}	
	return $value;
}


## =======================================================================        
##  clients_image_validateInput        
## =======================================================================        
##  processes the data entered by the user- and stores the data
##  in the correct table
##
## =======================================================================        
function clients_image_validateInput($ctlData,$input_data = null) {
	global $Auth,$gSession;
	
	## we need to upload the image- check the type and delete it
	## if it is of the wrong type. otherwise we need to temp store it
	## so that we can later retrieve it for the actual storage process
	$userfile	= $_FILES[$ctlData['IDENTIFIER']]['tmp_name'];
	$file_name	= $_FILES[$ctlData['IDENTIFIER']]['name'];
	$file_size	= $_FILES[$ctlData['IDENTIFIER']]['size'];
	$file_type	= $_FILES[$ctlData['IDENTIFIER']]['type'];

	## okay we check the filename- if it is valid
	## very strict naming of file.. only lowercase letters, numbers and underscores
	$new_name = ereg_replace("[^a-z0-9._]", "", ereg_replace(" ", "_", ereg_replace("%20", "_", strtolower($file_name))));
	
	## check for extention and remove it- sow we can later increment the file name
	if(ereg("(\.)([a-z0-9]{3,5})$", $new_name)) {
		$pos = strrpos($new_name, ".");
		$file_extension = substr($new_name, $pos, strlen($new_name));
		$new_name = substr($new_name, 0, $pos);	
	}
	
	if(empty($new_name)) {
		return array('error'=>0,'data'=>stripslashes($filename));
	}

	## we want to make sure that the file has a correct extension
	$allowed_extensions = array('.jpg','.jpeg','.gif');
	if(!in_array($file_extension,$allowed_extensions)) {
		## wrong filetype
		return array('error'=>-13,'data'=>'');
	}

	$file_name = substr(md5(uniqid('').getmypid()),0,10).$file_extension;

	## okay we first create an upload object
	$f = new file_object();  
	if (($userfile != "none") && ($userfile !="")) { 
		##then we upload the file
		$filename = $f->upload($userfile, $file_name,$file_size,$file_type, MATRIX_UPLOADDIR.'temp/');
	
		if($filename != -1) {
			$img_size = GetImageSize(MATRIX_UPLOADDIR.'temp/'.$filename);

			if($img_size !== false) {
				## we should return the error code and the input from the user
				return array('error'=>0,'data'=>stripslashes($filename));		
			} else {
				return array('error'=>-13,'data'=>'');
			}
		}
	} 
	
	return array('error'=>0,'data'=>stripslashes($filename));
}

## =======================================================================        
##  clients_image_storeInput        
## =======================================================================        
##  processes the data entered by the user- and stores the data
##  in the correct table
##
## =======================================================================        
function clients_image_storeInput($ctlData,$objectID) {
	global $Auth,$gSession;
	
	##first check if the database was setup
	clients_image_setup($ctlData['IDENTIFIER']);

	## we need to prepare the input - needs to be done properly
	$userfile	= $_FILES[$ctlData['IDENTIFIER']]['tmp_name'];
	$file_name	= $_FILES[$ctlData['IDENTIFIER']]['name'];
	$file_size	= $_FILES[$ctlData['IDENTIFIER']]['size'];
	$file_type	= $_FILES[$ctlData['IDENTIFIER']]['type'];

	## okay we first create an upload object
	$f = new file_object();  
	if (($userfile != "none") && ($userfile !="")) { 
		##then we upload the file
		$filename = $f->upload($userfile, $file_name,$file_size,$file_type, MATRIX_UPLOADDIR.'profiles/');
	
		if($filename != -1) {
			$img_size = GetImageSize(MATRIX_UPLOADDIR.'images/'.$filename);

			$db_connection = new DB_Sql();

			## first check if the entry already exists
			$select_query = "SELECT id FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_image WHERE identifier = '".$ctlData['IDENTIFIER']."' AND object_id= '$objectID'";
			$result_pointer = $db_connection->query($select_query);
			$db_connection->next_record();
			$id = $db_connection->Record['id'];
			
			if($db_connection->num_rows() == 0) { 
				## first we need to regsiter the image in your image_data table
				$query = "INSERT INTO ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_image (object_id,identifier,filename, width, height) values ('$objectID','".$ctlData['IDENTIFIER']."','profiles/$filename','$img_size[0]','$img_size[1]')";
				$result_pointer = $db_connection->query($query);
			} else {
				$db_connection->next_record();
				$update_query = "UPDATE ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_image SET filename = 'profiles/$filename', width= '$img_size[0]',height='$img_size[1]' WHERE id = '$id' AND object_id ='$objectID'";
				$result_pointer = $db_connection->query($update_query);			
			}
		}
	}	
}

## =======================================================================        
##  clients_image_setup        
## =======================================================================        
##  is called to see if we need to setup this attribute type- and if yes
##  it sets itself up
##
## =======================================================================        
function clients_image_setup($identifier) {
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
		if($db_connection->Record[0] == (DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'_image') || $db_connection->Record[0] == strtolower(DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'_image')) {
			$exists = true;
		}
	}

	if(!$exists) {
		## the base
		$query = 'CREATE TABLE '.DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'_image (
		`id` int(10) NOT NULL auto_increment, 
		`object_id` int(10),
		`identifier` varchar(255) NOT NULL default \'\',
		`filename` varchar(255) NOT NULL default \'\',
		`width` int(10),
		`height` int(10),
		PRIMARY KEY (`id`))';
		$result_pointer = $db_connection->query($query);
	}	

}

## =======================================================================        
##  clients_image_setupSearch        
## =======================================================================        
##  this is called by the overviewpage- we will return the options for
##  the search. For this normal text type- these are: the standard search
##  fields+ and our entry in the pulldown menu
##
## =======================================================================        
function clients_image_setupSearch($ctlData) {
}

## =======================================================================        
##  clients_image_getSearchQuery       
## =======================================================================        
##  this function returns the query- required in order to retrieve 
##  the specified search values. it consists of 3 elements:
##  A) Tables required- seperated by commas
##  B) Joining of the required tables with the client
##  C) the actual search pattern
##
## =======================================================================        
function clients_image_getSearchQuery($ctlData,$searchRow) {
}

## =======================================================================        
##  clients_image_getSearchFields       
## =======================================================================        
##  this function returns the query- required in order to retrieve 
##  the specified search values. it consists of 3 elements:
##  A) Tables required- seperated by commas
##  B) Joining of the required tables with the client
##  C) the actual search pattern
##
## =======================================================================        
function clients_image_getSearchFields($ctlData,$searchRow) {
}

?>