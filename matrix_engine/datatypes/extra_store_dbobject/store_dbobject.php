<?PHP
## ======================================================================= 
##   webmatrix                                                     
## ======================================================================= 
##   Copyright (c) 2001, 2002, 2003 Stefan Elsner, Workmatrix        
## ======================================================================= 
##   <info@workmatrix.de> 
##
## ======================================================================= 

require_once(ENGINE.'modules/clients/classes/ctlparser.php');
require_once(ENGINE.'classes/class_validate.php');

## =======================================================================        
## store_dbobject_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function store_dbobject_displayInput($xmldata, $data) {
	global $gSession;
	global $Auth;

	## we need to setup some URLs- since we need some more vars
	## as a normal plugin in, we need to select some global vars
	## page_id, low_sub, mode
	global $g_pageID;

	## we need to load the language specific strings
	include(ENGINE."datatypes/extra_store_dbobject/interface/lang/".$Auth->auth["language"].".php");
	
	## init the vars
	$return = "";
	
	## we should open our own template
	$template = new Template(ENGINE."datatypes/extra_store_dbobject/interface");
	$template->set_templatefile(array("linklistadd" => "interface.tpl","linklistedit" => "interface.tpl","linklist_row" => "interface.tpl","linklist_foot" => "interface.tpl"));
	
	## set the vars
	$template->set_var('element_name',$xmldata['NAME']);
	$template->set_var('element_desc',$xmldata['DESC']);
	
	## we got your record to process the data

	## prepare the vars
	$linklistID = $data['id'];
	$basename 	= $xmldata['TEMPLATE'];

	## prepare the url
	$addlinkURL = "datatypes/extra_store_dbobject/editor.php?op=add&page_id=".$g_pageID."&identifier=".$xmldata['NAME']."&linklistID=".$linklistID;
	$addlinkURL = $gSession->url($addlinkURL);	
	
	$deletelinkURL = "datatypes/extra_store_dbobject/editor.php?op=delete&page_id=".$g_pageID."&identifier=".$xmldata['NAME']."&linklistID=".$linklistID;		
	$deletelinkURL = $gSession->url($deletelinkURL);
	
	## the sort link (we will use our own editor, not the admin.php
	$sortURL = "datatypes/extra_store_dbobject/editor.php?op=sort&page_id=".$g_pageID."&identifier=".$xmldata['NAME']."&linklistID=".$linklistID;
	$sortURL = $gSession->url($sortURL);	
	
	## set the vars
	$template->set_var('addlinkItemURL',$addlinkURL);	
	$template->set_var('deletelinkItemURL',$deletelinkURL);
	$template->set_var('sortURL',$sortURL);
	
	if(!$xmldata['TAG']) {
		$template->set_var('element_tag',LANG_store_dbobject_Title);
		$template->set_var('element_desc',LANG_store_dbobject_TitleDesc);
	} else {
		$template->set_var('element_tag',$xmldata['TAG']);
	}	
	
	## get the number of records
	$items = $data['length']; 
	## now check if we are allowed to set another one
	if($items >= 1) {
		## output the stripped down block
		$return = $template->fill_block("linklistedit");
	} else {
		$return = $template->fill_block("linklistadd");
	}
	
	## loop through all records
	for($i=0; $i< $items; $i++) {;
		## display the page title an the id number
						
		## so we can savely dsiplay the entry
		$decription = $data[$i]["text"];							
		$template->set_var('decription',$decription);
						
		$template->set_var('linkID',$data[$i]['link']);		
		$return .= $template->fill_block("linklist_row");
	}	
	$return .= $template->fill_block("linklist_foot");
							
	return $return;
}


## =======================================================================        
##  store_dbobject_storeData        
## =======================================================================        
## save the content in the db
## ======================================================================= 
function store_dbobject_storeData($page_id, $identifier) {
	return false;
}
## =======================================================================        
##  store_dbobject_getData        
## =======================================================================        
##  get the Data
## ======================================================================= 
function store_dbobject_getData($vPageID,&$page_record) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	$db_connectionMain = new DB_Sql();  

	## now for the linklistitems
	$query = "SELECT link_list_identifier,link_list_id FROM ".LINKLIST." WHERE page_id='$vPageID' AND client_id='$client_id' ORDER BY link_list_identifier";
	$result_pointer = $db_connectionMain->query($query);

	## loop through the resuls and set the vars accordingly
	$db_connection = new DB_Sql();
	$old_identifier="";
	while($db_connectionMain->next_record()) {
		## we need to get the items associated with each linkList on this page
		$identifier = $db_connectionMain->Record["link_list_identifier"];
		$link_list_id = $db_connectionMain->Record["link_list_id"];	
		
		## check if it is a new entry
		if($identifier != $old_identifier) {
			##$counter = 1;
			$old_identifier = $identifier; 
		} 
		## add this info to the container
		$page_record[$identifier]["type"] = "LINKLIST";
		$page_record[$identifier]["id"] = $link_list_id;	
		
		$itemCounter = 0;
		## let's get the page name for each linklistitem
		$select_query = "SELECT page_id,structure_text, link_list_item_id,link_list_item_order FROM ".LINKLISTITEM." INNER JOIN ".STRUCTURE." ON ".STRUCTURE.".page_id=".LINKLISTITEM.".link_list_item_target WHERE link_list_id='$link_list_id' AND ".LINKLISTITEM.".client_id='$client_id' ORDER BY link_list_item_order";
		$result_pointer = $db_connection->query($select_query);	
		while($db_connection->next_record()) {	
			$text = $db_connection->Record["structure_text"];
			$link = $db_connection->Record["link_list_item_id"];
			$page_id = $db_connection->Record["page_id"];
			$order = $db_connection->Record["link_list_item_order"];
			
			$page_record[$identifier][$itemCounter]["text"] = $text; 
			$page_record[$identifier][$itemCounter]["link"] = $link; 
			$page_record[$identifier][$itemCounter]["page_id"] = $page_id;
			$page_record[$identifier][$itemCounter]["order"] = $order;
			$itemCounter++;			
		}		
	$page_record[$identifier]["length"] = $itemCounter;
	}
}

## =======================================================================        
##  store_dbobject_deleteData        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function store_dbobject_deleteData($vPageID) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## data connection
	$db_connection = new DB_Sql();
	
	## we need to find out which linklistitems are related to the current pageID
	## first we should get the linklist
	$query = "SELECT link_list_id FROM ".LINKLIST." WHERE page_id = '$vPageID' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($query);
	
	## get the id
	$db_connection->next_record();
	$vLinkListID = $db_connection->Record["link_list_id"];

	## let's delete the items
	$query = "DELETE FROM ".LINKLISTITEM." WHERE link_list_id='$vLinkListID' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($query);

	## now delete the linklist
	$query = "DELETE FROM ".LINKLIST." WHERE page_id='$vPageID' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($query);
}

## =======================================================================        
##  store_dbobject_output        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function store_dbobject_output($item,$structure,$menu_id) {
	## okay we need to check if the user clicked on submit
	$error_found = true;
	
	## fetch the appropriate control file
	$wt = new ctlparser(MATRIX_BASEDIR.'settings/modules/'.$structure['STORAGE'].'/base.xml');
	$wt->parse();	
	## okay we scanned in the xml file- so we now loop through all the elements
	$elements = $wt->getSimplifiedElements();
	$types = $wt->getObjects();
	
	## generate the default values- these are valid when the user hasn't entered anything yet
	$output = _dbobject_getDefaults($types,$elements,$structure['STORAGE']);

	if(!empty($_POST['datatype']) && $_POST['datatype'] == 'store_dbobject') {
		## okay the user submited the form- 
		$status = _dbobject_validate($types,$elements, $structure['STORAGE']);
		
		$output = $status['OUTPUT'];
		$error_found = $status['ERROR_FOUND'];
		
		## here we need to call any custom functions that do individual validation
		## needs to be invented somehow
		
		
		## okay prepare the error panel
		if($error_found) {
			## if we found an error- we will fetch the provided template and 
			## generate it as the main error message.
			if(isset($structure['TEMPLATE'])) {
				$filename = $structure['TEMPLATE'].".tpl";
				$template = new Template(HTML_DIR);
				$template->set_templatefile(array("body" => $filename)); 
			
				## then we set all the errors we found
				$template->set_vars($output);
				## then add the main panel to the output
				$output['matrix:ERROR'] = $template->fill_block("body");
			}
		}
	}
	
	## okay if the user entered data and it passed the validation tests
	## we can finally start the action of this module, which will always
	## be stroing of the data. Plus any other custom fucntionality added
	## through a user module
	if(!$error_found) {		
		## let's create a new client
		$db = new DB_Sql();
		
		$query = "INSERT INTO ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." (groupid,entered) values (1,now())";
		$rp = $db->query($query,true);	
		$client_id = $db->db_insertid($rp);			

		## okay we can savely store the values
		foreach($elements as $current_element) {
			## here we start calling all our attribute types
			$type = $current_element['TYPE'];
			$identifier = $current_element['IDENTIFIER'];
			$name = $current_element['NAME'];
			## we need to check if we have a module for this attributetype
			$type = strtolower($type);
			
			## first we try to include the apropriate file 
			include_once(ENGINE.'modules/clients/attributetypes/'.$type.'/attribute.php');
			
			## now we check if the function exists
			if(function_exists("clients_".$type."_storeInput")) {
				## no we call the function
				eval("\$element = clients_".$type."_storeInput(\$current_element,\$client_id);");
			}					
		}
		
		## we should now execute any custom functions if they exist
		if($structure['ACTION_FUNCTION']!="") {
			## okay here we check for files to be included
			## we will call the function using the eval			
			eval("\$value = ".$structure['ACTION_FUNCTION']."($client_id,\$structure['STORAGE']);");
		}
		
		## finally we need to display the confirmation page- in order to
		## work very well with ajax we will directly output the page here
		## similar to the filder or alias functionlity
		
		## and redirect to the specified page
		echo page_generatePage($structure['TARGET']);
		exit;			
	}
	
	return $output;
}

## =======================================================================        
##  _dbobject_validate  
## =======================================================================        
##  this is a helper function that validates the input
## ======================================================================= 
function _dbobject_validate($types,$elements, $storage_container) {
	## init the return value
	$output = array();
	$error_found = false;

	## before we start- we will try to include the required files
	foreach($types as $type=>$value) {
		## get the settings for the storage module
		$type = strtolower($type);
		@include_once(ENGINE.'modules/'.$storage_container.'/settings.php');
		@include_once(ENGINE.'modules/clients/attributetypes/'.$type.'/attribute.php');
		@include_once(MATRIX_BASEDIR.'settings/modules/'.$storage_container.'/error_strings/'.$type.'.php');
	}
	
	## then we will go through each element and validate it
	foreach($elements as $current_element) {
		## here we start calling all our attribute types
		$type = strtolower($current_element['TYPE']);
		$identifier = $current_element['IDENTIFIER'];
		$name = $current_element['NAME'];

		## check if the function exists
		if(function_exists("clients_".$type."_validateInput")) {
			## no we call the function
			eval("\$element = clients_".$type."_validateInput(\$current_element,\$client_id);");
				
			## we need to stor the error code- if any error was returned- we
			## will return the appropriate error stirng as well as the data that
			## was previously entered.
			if($element['error'] != VALIDATE_VALID) {
				## we found an error- set the flag
				$error_found = true;
				
				## now prepare the error string
				eval("\$error_string = \$_DBOBJECT_".$type."_ERROR_STRING[\$element['error']];");					
				$output[$identifier.'_error'] = sprintf($error_string,$name);	
				$output[$identifier.'_highlight'] = 'Error';
			}
				
			## store the data in case nay other field contains an error
			$output[$identifier.'_value'] = $element['data'];
			## for later usage we store all input vars					
		}
	}	
	return array('OUTPUT'=>$output,'ERROR_FOUND'=>$error_found);
}


## =======================================================================        
##  _dbobject_getDefaults  
## =======================================================================        
##  this is a helper function that validates the input
## ======================================================================= 
function _dbobject_getDefaults($types,$elements, $storage_container) {
	## init the return value
	$output = array();

	## before we start- we will try to include the required files
	foreach($types as $type=>$value) {
		## get the settings for the storage module
		$type = strtolower($type);
		@include_once(ENGINE.'modules/'.$storage_container.'/settings.php');
		@include_once(ENGINE.'modules/clients/attributetypes/'.$type.'/attribute.php');
	}
	
	## then we will go through each element and validate it
	foreach($elements as $current_element) {
		## here we start calling all our attribute types
		$type = strtolower($current_element['TYPE']);
		$identifier = $current_element['IDENTIFIER'];
		$name = $current_element['NAME'];

		## check if the function exists
		if(function_exists("clients_".$type."_getDefaults")) {
			## no we call the function
			eval("\$element = clients_".$type."_getDefaults(\$current_element,\$client_id);");
				
			## store the data in case nay other field contains an error
			$output[$identifier.'_value'] = $element;
			## for later usage we store all input vars					
		}
	}	
	return $output;
}

## =======================================================================        
##  store_dbobject_copyData        
## =======================================================================        
##  pass it the sourcepage_id and the targetpage_id. It'll copy all
##  text entries to the new page.
## ======================================================================= 
function store_dbobject_copyData($source_id, $target_id) {
	linklist_copyData($source_id, $target_id);	
}

?>
