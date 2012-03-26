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
##  form_displayInputForm      
## =======================================================================        
##  displays the input form for a certain entry  
##
## ======================================================================= 
function form_displayInputForm($vItemID,$vLinkListID,$vPageID,$vIdentifier) {	
	## basically we need to display the page selector
	## optionally highlight the already selected page
	## and set all vars correctly
	
	if($vItemID > 0 && $vLinkListID > 0) {
		## okay we have a selected one
		## let#s get it, and find out the page num
		$currentItem = form_getItem($vLinkListID,$vItemID);
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
##  form_storeItem      
## =======================================================================        
##  store the specified icon
##  we need to check if there is a link listID already passed 
##
## ======================================================================= 
function form_storeItem() {
	## multiclient
	global $Auth;
	$client_id = $Auth->auth["client_id"];
	
	## prepare the input vars
	$vPageID 		= $_POST['page_id'];
	$vTargetID 		= $_POST['selected_page'];
	$vIdentifier 	= $_POST['identifier'];
	$vItemID 		= $_POST['item_id'];

	$db = new DB_Sql();  

	## check if we already have the base list entry
	$query = "SELECT link_list_id FROM ".LINKLIST." WHERE page_id = '$vPageID' AND link_list_identifier = '$vIdentifier' AND client_id='$client_id'";
	$result_pointer = $db->query($query);	

	if($db->num_rows() == 0) { 
		## create a new entry
		$vLinkListID = form_createList($vPageID,$vIdentifier);
	} else {
		## fetch the id of the existing entry
		$db->next_record();
		$vLinkListID = $db->Record["link_list_id"];
	}
		
	$query = "SELECT MAX(link_list_item_order) AS item_order FROM ".LINKLISTITEM." WHERE link_list_id='$vLinkListID' AND client_id='$client_id'";
	$rp = $db->query($query);
	
	if($db->next_record(MYSQL_ASSOC)) {
		$linkorder	= $db->Record['item_order'];		
		$linkorder++;
	
		$query = "SELECT link_list_item_id FROM ".LINKLISTITEM." WHERE link_list_item_id='$vItemID' AND client_id='$client_id'";
		$rp = $db->query($query);
		
		if($db->num_rows() == 0) { 
			## we havent' found anything let's create a new entry
			$query = "INSERT ".LINKLISTITEM." (link_list_id,link_list_item_target,link_list_item_order, client_id) values ('$vLinkListID','$vTargetID','$linkorder','$client_id')";
			$rp = $db->query($query);	
		} else {
			## then we need to update the current entry
			$query = "UPDATE ".LINKLISTITEM." SET link_list_item_target='$vTargetID' WHERE link_list_item_id='$vItemID' AND client_id='$client_id'";
			$rp = $db->query($query);
		}
	}
}

## =======================================================================        
##  form_deletItem      
## =======================================================================        
##  store the specified icon
##  we need to check if there is a link listID already passed 
##
## ======================================================================= 
function form_deletItem($vPageID,$vIdentifier,$vItemID) {
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
##  form_createList     
## =======================================================================        
##  setups a new list
## ======================================================================= 
function form_createList($vPageID,$vIdentifier) {
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
##  form_getItem     
## =======================================================================        
##  setups a new list
## ======================================================================= 
function form_getItem($vLinkListID,$vItemID) {
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
##  form_promptDelete        
## =======================================================================        
function form_promptDelete($yesURL,$noURL) {
	global $gSession,$Auth;
	$db_connectionLayout = new DB_Sql();  

	## prepare the template file
	$select_template = new Template(ENGINE."datatypes/extra_subscribe/interface");
	$select_template->set_templatefile(array("body" => "deletelink.tpl"));
	
	$select_template->set_var("yesIMG","lang/".$Auth->auth["language"]."_button_ja.gif");
	$select_template->set_var("noIMG","lang/".$Auth->auth["language"]."_button_nein.gif");
	$select_template->set_var('language_deletepage',LANG_form_DeleteTitle);
	$select_template->set_var('language_doyouwant',LANG_form_DeleteDesc);
	
	## grab the information for this page
  		
	$select_template->set_var('yesURL',$yesURL);
	$select_template->set_var('noURL',$noURL);
	
	$select_template->pfill_block("body");
}	



?>
