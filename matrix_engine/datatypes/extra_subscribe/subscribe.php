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

## =======================================================================        
## subscribe_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function subscribe_displayInput($xmldata, $data) {
	global $gSession;
	global $Auth;

	## we need to setup some URLs- since we need some more vars
	## as a normal plugin in, we need to select some global vars
	## page_id, low_sub, mode
	global $g_pageID;

	## we need to load the language specific strings
	include(ENGINE."datatypes/extra_subscribe/interface/lang/".$Auth->auth["language"].".php");
	
	## init the vars
	$return = "";
	
	## we should open our own template
	$template = new Template(ENGINE."datatypes/extra_subscribe/interface");
	$template->set_templatefile(array("linklistadd" => "interface.tpl","linklistedit" => "interface.tpl","linklist_row" => "interface.tpl","linklist_foot" => "interface.tpl"));
	
	## set the vars
	$template->set_var('element_name',$xmldata['NAME']);
	$template->set_var('element_desc',$xmldata['DESC']);
	
	## we got your record to process the data

	## prepare the vars
	$linklistID = $data['id'];
	$basename 	= $xmldata['TEMPLATE'];

	## prepare the url
	$addlinkURL = "datatypes/extra_subscribe/editor.php?op=add&page_id=".$g_pageID."&identifier=".$xmldata['NAME']."&linklistID=".$linklistID;
	$addlinkURL = $gSession->url($addlinkURL);	
	
	$deletelinkURL = "datatypes/extra_subscribe/editor.php?op=delete&page_id=".$g_pageID."&identifier=".$xmldata['NAME']."&linklistID=".$linklistID;		
	$deletelinkURL = $gSession->url($deletelinkURL);
	
	## the sort link (we will use our own editor, not the admin.php
	$sortURL = "datatypes/extra_subscribe/editor.php?op=sort&page_id=".$g_pageID."&identifier=".$xmldata['NAME']."&linklistID=".$linklistID;
	$sortURL = $gSession->url($sortURL);	
	
	## set the vars
	$template->set_var('addlinkItemURL',$addlinkURL);	
	$template->set_var('deletelinkItemURL',$deletelinkURL);
	$template->set_var('sortURL',$sortURL);
	
	if(!$xmldata['TAG']) {
		$template->set_var('element_tag',LANG_SUBSCRIBE_Title);
		$template->set_var('element_desc',LANG_SUBSCRIBE_TitleDesc);
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
##  subscribe_storeData        
## =======================================================================        
## save the content in the db
## ======================================================================= 
function subscribe_storeData($page_id, $identifier) {
	return false;
}
## =======================================================================        
##  subscribe_getData        
## =======================================================================        
##  get the Data
## ======================================================================= 
function subscribe_getData($vPageID,&$page_record) {
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
##  subscribe_deleteData        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function subscribe_deleteData($vPageID) {
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
##  subscribe_output        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function subscribe_output($item,$structure,$menu_id) {
	## this function gets called when the page is displayed
	## we need to check if the user submited a form
	if(!empty($_POST['subscribe'])) {
		## okay the user submited the form- now we need to
		## store all data. This will be done by using the 
		## client module xml file- and looping through
		## all entries- and call the attributes store routine.
		$wt = new ctlparser(ENGINE.'modules/clients/controlfiles/base.xml');
		$wt->parse();	
		## okay we scanned in the xml file- so we now loop through all the elements
		$elements = $wt->getSimplifiedElements();	
		
		## in order to avoid us overwriting a existing element
		## we need to check if we have a unique element in the xml structure
		## if yes, we will first need to cehck if that element already exists
		## if yes we need to get the clients id assoicated with this entry
		$unique_element = array();
		foreach($elements as $current_element) {	
			if(isset($current_element['UNIQUE'])) {
				$unique_element = $current_element;
				break;
			}
		}
		
		## okay now that we have the unique element we need to check
		## if there is an element with that value
		## we need to check if we have a module for this attributetype
		$type = strtolower($current_element['TYPE']);
		$value = $_POST[$current_element['IDENTIFIER']];

		## first we try to include the apropriate file 
		@include_once(ENGINE."modules/clients/attributetypes/".$type."/attribute.php");
		## now we check if the function exists
		if(function_exists("clients_".$type."_getClient")) {
			## no we call the function
			eval("\$client_id = clients_".$type."_getClient(\$current_element,\$value);");
		}					
		
		## check if we need to create a new client
		if($client_id === false) {
			$db = new DB_Sql();
			
			$query = "INSERT INTO ".CLIENTS." (groupid,entered) values (1,now())";
			$rp = $db->query($query,true);	
			$client_id = $db->db_insertid($rp);
		}
		
		## okay at this point we will have a client id- now loop through all entries
		## and call their storage functions
		
		foreach($elements as $current_element) {

			## here we start calling all our attribute types
			$type = $current_element['TYPE'];
			## we need to check if we have a module for this attributetype
			$type = strtolower($type);
			
			## first we try to include the apropriate file 
			@include_once(ENGINE.'modules/clients/attributetypes/'.$type.'/attribute.php');
			## now we check if the function exists
			if(function_exists("clients_".$type."_storeInput")) {
				## no we call the function
				eval("\$element = clients_".$type."_storeInput(\$current_element,\$client_id);");
			}					
		
		}

		if(isset($item[0]['page_id'])) {
			## okay then we re-route the user to the specified thank you page
			header("Location: ".SITE_ROOT."content.php?page_id=".$item[0]['page_id']);			
			header("Status: 303");
			exit;
		}
	}
}

## =======================================================================        
##  subscribe_copyData        
## =======================================================================        
##  pass it the sourcepage_id and the targetpage_id. It'll copy all
##  text entries to the new page.
## ======================================================================= 
function subscribe_copyData($source_id, $target_id) {
	linklist_copyData($source_id, $target_id);	
}

?>
