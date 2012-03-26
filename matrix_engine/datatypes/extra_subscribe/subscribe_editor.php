<?PHP
## ======================================================================= 
##   webmatrix                                                     
## ======================================================================= 
##   Copyright (c) 2001, 2002, 2003 Stefan Elsner, Workmatrix        
## ======================================================================= 
##   <info@workmatrix.de> 
##
## ======================================================================= 


## =======================================================================        
##  subscribe_displayInputForm      
## =======================================================================        
##  displays the input form for a certain entry  
##
## ======================================================================= 
function subscribe_displayInputForm($vItemID,$vLinkListID,$vPageID,$vIdentifier) {	
	## basically we need to display the page selector
	## optionally highlight the already selected page
	## and set all vars correctly
	
	if($vItemID > 0 && $vLinkListID > 0) {
		## okay we have a selected one
		## let#s get it, and find out the page num
		$currentItem = subscribe_getItem($vLinkListID,$vItemID);
		$varsToBeSet["targetPageID"] = $currentItem["link_list_item_target"];
	}
	
	## first get the page structure
	$menuItems = structure_getStructure();
	
	$varsToBeSet["page_id"] = $vPageID;
	$varsToBeSet["identifier"] = $vIdentifier;
	$varsToBeSet["ItemID"] = $vItemID;

	$targetURL="editor.php?op=add&page_id=".$vPageID."&identifier=".$vIdentifier."&linklistID=".$vLinkListID."&linkItemID=".$vItemID;
	##$varsToBeSet["item_id"] = $vLinkListID;
	page_selector_drawMenu($menuItems,$varsToBeSet,$targetURL);
	
	## this is it- we are done
} 
	
## =======================================================================        
##  subscribe_storeItem      
## =======================================================================        
##  store the specified icon
##  we need to check if there is a link listID already passed 
##
## ======================================================================= 
function subscribe_storeItem() {
	global $HTTP_POST_VARS;
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	
	$vPageID = $_POST['page_id'];
	$vTargetID = $_POST['selected_page'];
	$vIdentifier = $_POST['identifier'];
	$vItemID = $_POST['item_id'];

	$db_connection = new DB_Sql();  

	## first we need to find out if the LinkList already exists
	$query = "SELECT link_list_id FROM ".LINKLIST." WHERE page_id = '$vPageID' AND link_list_identifier = '$vIdentifier' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($query);	

	if($db_connection->num_rows() == 0) { 
		## create a new entry
		$vLinkListID = subscribe_createList($vPageID,$vIdentifier);
	} else {
		## we already have an entry- get its id
		$db_connection->next_record();
		$vLinkListID = $db_connection->Record["link_list_id"];
	}
	
	## now for the actual item
	## we need to find out the current order id
	$lock_query = "LOCK TABLE ".LINKLISTITEM." write";
	$result_pointer = $db_connection->query($lock_query);	
	
	$select_query = "select max(link_list_item_order) from ".LINKLISTITEM." where link_list_id='$vLinkListID' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($select_query);
	$db_connection->next_record();
	list($linkorder) = $db_connection->Record;		

  	$linkorder++;
	
	$query = "SELECT link_list_item_id FROM ".LINKLISTITEM." WHERE link_list_item_id='$vItemID' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($query);
		
	if($db_connection->num_rows() == 0) { 
		## we havent' found anything let's create a new entry
		
		$query = "INSERT ".LINKLISTITEM." (link_list_id,link_list_item_target,link_list_item_order, client_id) values ('$vLinkListID','$vTargetID','$linkorder','$client_id')";
		$result_pointer = $db_connection->query($query);	
	} else {
		## then we need to update the current entry
		$query = "UPDATE ".LINKLISTITEM." SET link_list_item_target='$vTargetID' WHERE link_list_item_id='$vItemID' AND client_id='$client_id'";
		$result_pointer = $db_connection->query($query);
	}
	
	$lock_query = "UNLOCK table";
	$result_pointer = $db_connection->query($lock_query);
}

## =======================================================================        
##  subscribe_deletItem      
## =======================================================================        
##  store the specified icon
##  we need to check if there is a link listID already passed 
##
## ======================================================================= 
function subscribe_deletItem($vPageID,$vIdentifier,$vItemID) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	$db_connection = new DB_Sql();  

	## first we should get the linklist
	$query = "SELECT link_list_id FROM ".LINKLIST." WHERE page_id = '$vPageID' AND link_list_identifier = '$vIdentifier' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($query);
	## get the id
	$db_connection->next_record();
	$vLinkListID = $db_connection->Record["link_list_id"];
					
	## let's delete the item
	$query = "DELETE FROM ".LINKLISTITEM." WHERE link_list_item_id='$vItemID' AND link_list_id='$vLinkListID' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($query);
}


## =======================================================================        
##  subscribe_createList     
## =======================================================================        
##  setups a new list
## ======================================================================= 
function subscribe_createList($vPageID,$vIdentifier) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	$db_connection = new DB_Sql();  

	$query = "INSERT INTO ".LINKLIST." (page_id,link_list_identifier, client_id) VALUES ('$vPageID','$vIdentifier','$client_id')";
	$result_pointer = $db_connection->query($query);
	## get the created id
	$return = $db_connection->db_insertid($result_pointer);
	return $return; 
} 

## =======================================================================        
##  subscribe_getItem     
## =======================================================================        
##  setups a new list
## ======================================================================= 
function subscribe_getItem($vLinkListID,$vItemID) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	$db_connection = new DB_Sql();  
		
	$query = "SELECT structure_text, link_list_item_id,link_list_item_target FROM ".LINKLISTITEM." INNER JOIN ".STRUCTURE." ON ".STRUCTURE.".page_id=".LINKLISTITEM.".link_list_item_target WHERE link_list_id='$vLinkListID' AND link_list_item_id='$vItemID' AND ".LINKLISTITEM.".client_id='$client_id' ORDER BY link_list_item_id";
	$result_pointer = $db_connection->query($query);
	$db_connection->next_record();
	
	$target = $db_connection->Record["link_list_item_target"];
	
	return $db_connection->Record; 
}

## =======================================================================        
##  subscribe_promptDelete        
## =======================================================================        
function subscribe_promptDelete($yesURL,$noURL) {
	global $gSession,$Auth;
	$db_connectionLayout = new DB_Sql();  

	## prepare the template file
	$select_template = new Template(ENGINE."datatypes/extra_subscribe/interface");
	$select_template->set_templatefile(array("body" => "deletelink.tpl"));
	
	$select_template->set_var("yesIMG","lang/".$Auth->auth["language"]."_button_ja.gif");
	$select_template->set_var("noIMG","lang/".$Auth->auth["language"]."_button_nein.gif");
	$select_template->set_var('language_deletepage',LANG_subscribe_DeleteTitle);
	$select_template->set_var('language_doyouwant',LANG_subscribe_DeleteDesc);
	
	## grab the information for this page
  		
	$select_template->set_var('yesURL',$yesURL);
	$select_template->set_var('noURL',$noURL);
	
	$select_template->pfill_block("body");
}	



?>
