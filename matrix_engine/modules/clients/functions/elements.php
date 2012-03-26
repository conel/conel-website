<?php

## =======================================================================        
##  clients_deleteClients        
## =======================================================================        
##  deletes all clients (and their attributes) that are identified
##  by the array supplied.
##
##  TODO:
##  
## ======================================================================= 
function clients_deleteClients($client_list) {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	if(is_array($client_list)) {
		foreach($client_list as $current_client) {
			clients_deleteClient($current_client);
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
function clients_deleteClient($id) {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## prepare the db-object
	$db_connection = new DB_Sql();
	
	## finally call all attributtypes and their delete function
	$wt = new ctlparser(MATRIX_BASEDIR.'settings/modules/'.$GLOBALS['_MODULE_DATAOBJECTS_NAME'].'/base.xml');
	$wt->parse();
	
	## okay we scanned in the xml file- so we now loop through all the elements
	$elements = $wt->getElements();	

	## let's process all rows
	foreach($elements as $current_row) {
		## process the rows- first we need to find out how many entries
		## are in this row 

		$counter = 1;
		foreach($current_row as $current_element) {
			## here we start calling all our attribute types
			$type = $current_element['TYPE'];
			switch($type) {
				default: {
					## we need to check if we have a module for this attributetype
					$type = strtolower($type);
					## first we try to include the apropriate file 
					@include_once(ENGINE."modules/clients/attributetypes/".$type."/attribute.php");
					## now we check if the function exists
					if(function_exists("clients_".$type."_deleteData")) {
						## no we call the function
						eval("\$element = clients_".$type."_deleteData(\$current_element,\$id);");
					}					
				}
				break;
			}
			$counter++;				
		}
	}	

	$query = "DELETE FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." WHERE id='".$id."' AND client_id='$client_id'";
	$rp = $db_connection->query($query);	
}

	## =======================================================================        
	##  clients_storeClient        
	## =======================================================================        
	##  store a client
	##
	## =======================================================================        
	function clients_storeClient($client = 0) {
		global $Auth;
	
		## multiclient
		$client_id = $Auth->auth["client_id"];
		
		## we need to prepare the input - needs to be done properly
		$email 		= $_POST['email'];
	
		## prepare the db-object
		$db = new DB_Sql();
	
		## if we are creating a new client- the id will be zero- so create one 
		if($client ==0) {
			$query 	= "INSERT INTO ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." (entered,client_id) values (now(),$client_id)";
			$rp 	= $db->query($query,true);	
			$client	= $db->db_insertid($rp);
		}
		
		## in order to store the remaining fields we need to get the structure file
		$wt = new ctlparser(MATRIX_BASEDIR.'settings/modules/'.$GLOBALS['_MODULE_DATAOBJECTS_NAME'].'/base.xml');
		$wt->parse();	
		$elements = $wt->getElements();	
		
		## let's process all rows
		foreach($elements as $current_row) {
			foreach($current_row as $current_element) {
				## here we start calling all our attribute types
				$type = strtolower($current_element['TYPE']);
	
				## first we try to include the apropriate file 
				@include_once(ENGINE."modules/clients/attributetypes/".$type."/attribute.php");
				## now we check if the function exists
				if(function_exists("clients_".$type."_storeInput")) {
					## no we call the function
					eval("\$element = clients_".$type."_storeInput(\$current_element,\$client);");
				}									
			}
		}
	
		## now that we have saved the main data, we will save the linked data
		return $client;
	}



## =======================================================================        
##  clients_getClients        
## =======================================================================        
##  returns all clients   
##
##  TODO:
##       - allow to limit the results in order to implement paging
## =======================================================================        
function clients_getClients($subquery,$offset,$items_perpage,$group) {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## prepare the db-object
	$db_connection = new DB_Sql();

	## first we neew to determine the number of clients
	$query = "SELECT * FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." AS A WHERE groupid=$group $subquery ORDER BY A.last_name";
	$rp = $db_connection->query($query);
	$max_entries = $db_connection->num_rows();
	
	## then get the correct values
	$query = "SELECT * FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." AS A WHERE groupid=$group $subquery ORDER BY A.last_name LIMIT $offset,$items_perpage";

	$result = $db_connection->query($query);	
	$counter = 0;
	$return_value = array();
	while($db_connection->next_record(MYSQL_ASSOC)) {
		foreach($db_connection->Record as $key => $val) {
			$return_value[$counter][$key] = $val;
		}
		$counter++;
	}
	return array('count'=>$max_entries,'data'=>$return_value);	
}

## =======================================================================        
##  clients_getClient      
## =======================================================================        
##  returns a specific client   
##  gathers all data for the specified client.
## =======================================================================        
function clients_getClient($id) {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## prepare the db-object
	$db_connection = new DB_Sql();

	## later on, we will allow the pages to be sorted in different ways- for now just the standard
	$query = "SELECT * FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." WHERE id='$id' AND client_id='$client_id'";
	$result = $db_connection->query($query);	

	$return_value = array();
	if($db_connection->next_record(MYSQL_ASSOC)) {
		foreach($db_connection->Record as $key => $val) {
			$return_value[$key] 			= $val;
		}
	}
	
	## we got almost everything- now we will get the extra data elements
	## NEEDS TO BE DONE
	return $return_value;	
}


## =======================================================================        
##  clients_getClient      
## =======================================================================        
##  returns a specific client   
##  gathers all data for the specified client.
## =======================================================================        
function clients_getClientDetail($id) {
	global $Auth,$gSession;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## here we will load the control file	
	$wt = new ctlparser(MATRIX_BASEDIR.'settings/modules/'.$GLOBALS['_MODULE_DATAOBJECTS_NAME'].'/base.xml');
	$wt->parse();	
	
	## okay we scanned in the xml file- so we now loop through all the elements
	$elements = $wt->getElements();	

	## let's process all rows
	$object_info = array();
	foreach($elements as $current_row) {
		## process the rows- first we need to find out how many entries
		## are in this row 	
		
		$counter = 1;
		foreach($current_row as $current_element) {
			## here we start calling all our attribute types
			$type = strtolower($current_element['TYPE']);
			
			## first we try to include the apropriate file 
			@include_once(ENGINE."modules/clients/attributetypes/".$type."/attribute.php");
			## now we check if the function exists
			if(function_exists("clients_".$type."_getData")) {
				## no we call the function
				eval("\$element = clients_".$type."_getData(\$current_element,\$id);");
				$object_info[$current_element['IDENTIFIER']] = $element;

			}					

			$counter++;				
		}
	}
	
	return $object_info;	
}

## =======================================================================        
##  portlets_dispayInputForm        
## =======================================================================        
##  returns an array with all pages of a certain category    
##
##  TODO:
##       - make all pages by default inactive
## =======================================================================        
function clients_displayInputForm($id=null,$page=0) {
	global $Auth,$gSession;

	## multiclient
	$client_id = $Auth->auth["client_id"];

	$inputFile = "input.tpl";
	$input_template = new Template(ENGINE.'modules/clients/interface');
	$input_template->set_templatefile(array("row" => $inputFile));
	
	## here we will load the control file	
	$wt = new ctlparser(MATRIX_BASEDIR.'settings/modules/'.$GLOBALS['_MODULE_DATAOBJECTS_NAME'].'/base.xml');
	$wt->parse();	
	$page_elements = $wt->getPagedElements();

	
	## we support multiple pages- so we need to check which one of the pages
	## needs to be displayed
	$elements = $page_elements[$page];
	
	## okay we scanned in the xml file- so we now loop through all the elements
	

	## let's process all rows
	foreach($elements as $current_row) {
		## process the rows- first we need to find out how many entries
		## are in this row 

		## set the groupname
		if(is_array($current_row)) {
			$input_template->set_var("group_name",$current_row['NAME']);
		}
		
		$counter = 1;
		foreach($current_row as $current_element) {
			## here we start calling all our attribute types
			$type = $current_element['TYPE'];
			switch($type) {
				default: {
					
					## we need to check if we have a module for this attributetype
					$type = strtolower($type);
					
					## first we try to include the apropriate file 
					@include_once(ENGINE."modules/clients/attributetypes/".$type."/attribute.php");
					## now we check if the function exists
					if(function_exists("clients_".$type."_displayInput")) {
						## no we call the function
						eval("\$element = clients_".$type."_displayInput(\$current_element,\$id);");
						$input_template->set_var("column".$counter,$element);	
					}					
				}
				break;
			}
			$counter++;				
		}
		$return_value .= $input_template->fill_block("row");
 		$input_template->reset_vars();
	}
		

	## okay let's use this file, as a template
	## prepare the template file- we changed to structure of the template file
	$inputFile = "input.tpl";
	$input_template = new Template(ENGINE.'modules/clients/interface');
	$input_template->set_templatefile(array("header" => $inputFile,"body" => $inputFile,"footer" => $inputFile));

	$tabs = '';	
	$tabsURL = 'module.php?group=1';
	$tabsURL = $gSession->url($tabsURL);	
	
	$page_count = count($page_elements) -1;
	if($page_count > 1) {
		$counter = 0;
		for($i = 0; $i < $page_count; $i++) {
			if($counter +1 >= $page_count) {
				$tabs .= ui_renderSubmitTab($page_elements[$i]['NAME'],$counter,$counter,$page,false);
			} else {
				$tabs .= ui_renderSubmitTab($page_elements[$i]['NAME'],$counter,$counter,$page,true);			
			}
			$counter++;
		}
	}
	
	$input_template->set_var("PAGES",$tabs);
	
	$input_template->set_var("PAGE",$page);

	## language
	$input_template->set_var("saveIMG","../../interface/lang/".$Auth->auth["language"]."_button_save.gif");
	$input_template->set_var("backIMG","interface/lang/".$Auth->auth["language"]."_buttonoverview.gif");

	$input_template->set_var('language_header',LANG_MODULE_CLIENTS_EditTitle);
	$input_template->set_var('language_description',LANG_MODULE_CLIENTS_EditDesc);
	
	$actionURL = "module.php";
	$actionURL = $gSession->url($actionURL);
	$input_template->set_var('actionURL',$actionURL);
	
	$backURL = "module.php?group=1&query=".$_GET['query'];
	$backURL = $gSession->url($backURL);
	$input_template->set_var('backURL',$backURL);

	## generate the editor url
	$addElementURL = "editor.php?op=create";
	$addElementURL = $gSession->url($addElementURL);
	$input_template->set_var('addElementURL',$addElementURL);	
	
	$input_template->set_var("attributes",$return_value);
		
	## the next step is to ouput the head
	$input_template->pfill_block("header");
	$input_template->pfill_block("body");

	## here we initialize the hiddenfields	
	$output =  '<input type="hidden" name="query" value="'.$_GET['query'].'">';
	$output =  '<input type="hidden" name="client" value="'.$id.'">';
	$output .=  '<input type="hidden" name="group" value="1">';
	$output .=  '<input type="hidden" name="cmd" value="store">';
	$output .=  '<input type="hidden" name="op" value="">';
	$output .= '<input type="hidden" name="Session" value="'.$gSession->id.'">';
	$input_template->set_var("hiddenfields",$output);
	
	$input_template->pfill_block("footer");

	
}

?>