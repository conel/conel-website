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
##  linklist_displayInputForm      
## =======================================================================        
##  displays the input form for a certain entry  
##
## ======================================================================= 
function linklist_displayInputForm($vItemID,$vLinkListID,$vPageID,$vIdentifier) {	
	global $Auth, $gSession,$input_language;
	## basically we need to display the page selector
	## optionally highlight the already selected page
	## and set all vars correctly
	
	if($vItemID > 0 && $vLinkListID > 0) {
		## okay we have a selected one
		## let's get it, and find out the page num
		$currentItem = linklist_getItem($vLinkListID,$vItemID);
		$varsToBeSet["targetPageID"] = $currentItem["link_list_item_target"];
	}
	
	## first get the page structure
	$menuItems = structure_getStructure();
	
	$varsToBeSet["page_id"] = $vPageID;
	$varsToBeSet["identifier"] = $vIdentifier;
	$varsToBeSet["ItemID"] = $vItemID;
	$varsToBeSet["language"] = $input_language;	

	$targetURL="editor.php?op=add&page_id=".$vPageID."&identifier=".$vIdentifier."&linklistID=".$vLinkListID."&linkItemID=".$vItemID."&language=".$input_language;
	##$varsToBeSet["item_id"] = $vLinkListID;
	page_selector_drawMenu($menuItems,$varsToBeSet,$targetURL);
	
	## this is it- we are done
} 
	
## =======================================================================        
##  linklist_storeItem      
## =======================================================================        
##  stores a link
##  there is now checking if the link has actually changed-  
##  since we only get called when a user does something- so we save
##	a new version everytime
## ======================================================================= 
function linklist_storeItem() {
	global $Auth,$input_language;
	
	$client_id = $Auth->auth["client_id"];	
	
	$return_value = false;

	$vPageID 		= $_POST['page_id'];
	$vTargetID 		= $_POST['selected_page'];
	$vIdentifier 	= $_POST['identifier'];
	$vItemID 		= $_POST['item_id'];

	$db_connection = new DB_Sql();  

	## first we need to find out if the LinkList already exists - and grab the latest
	$query = "SELECT link_list_id FROM ".LINKLIST." WHERE page_id = '$vPageID' AND link_list_identifier = '$vIdentifier' AND client_id='$client_id' AND language='$input_language'";
	$result_pointer = $db_connection->query($query);	

	if($db_connection->num_rows() == 0) { 
		## create a new entry
		$vLinkListID = linklist_createList($vPageID,$vIdentifier);
	} else {
		## we already have an entry- get its id
		$db_connection->next_record();
		$vLinkListID = $db_connection->Record["link_list_id"];
	}
	
	## now for the actual item
	## we need to find out the current order id
	
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
			
	## since we always create a new version we need to return true
	return true;
}

## =======================================================================        
##  linklist_storeItem      
## =======================================================================        
##  store the specified icon
##  we need to check if there is a link listID already passed 
##
## ======================================================================= 
function linklist_deletItem($vPageID,$vIdentifier,$vItemID) {
	global $Auth,$input_language;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];	

	$db_connection = new DB_Sql();  

	## first we should get the linklist
	$query = "SELECT link_list_id FROM ".LINKLIST." WHERE page_id = '$vPageID' AND link_list_identifier = '$vIdentifier' AND client_id='$client_id' AND language='$input_language' ";
	$result_pointer = $db_connection->query($query);
	## get the id
	$db_connection->next_record();
	$vLinkListID = $db_connection->Record["link_list_id"];
					
	## let's delete the item
	$query = "DELETE FROM ".LINKLISTITEM." WHERE link_list_item_id='$vItemID' AND link_list_id='$vLinkListID' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($query);
}


## =======================================================================        
##  linklist_createList     
## =======================================================================        
##  setups a new list
## ======================================================================= 
function linklist_createList($vPageID,$vIdentifier) {
	global $Auth,$input_language;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];	

	$db_connection = new DB_Sql();  

	$query = "INSERT INTO ".LINKLIST." (page_id,link_list_identifier, client_id,language) VALUES ('$vPageID','$vIdentifier','$client_id','$input_language')";
	$result_pointer = $db_connection->query($query);
	## get the created id
	$return = $db_connection->db_insertid($result_pointer);
	return $return; 
} 

## =======================================================================        
##  linklist_getItem     
## =======================================================================        
##  setups a new list
## ======================================================================= 
function linklist_getItem($vLinkListID,$vItemID) {
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
##  linklist_promptDelete        
## =======================================================================        
function linklist_promptDelete($yesURL,$noURL) {
	global $gSession,$Auth;
	$db_connectionLayout = new DB_Sql();  

	## prepare the template file
	$select_template = new Template(ENGINE."datatypes/linklist/interface");
	$select_template->set_templatefile(array("body" => "deletelink.tpl"));
	
	$select_template->set_var("yesIMG","lang/".$Auth->auth["language"]."_button_ja.gif");
	$select_template->set_var("noIMG","lang/".$Auth->auth["language"]."_button_nein.gif");
	$select_template->set_var('language_deletepage',LANG_LINKLIST_DeleteTitle);
	$select_template->set_var('language_doyouwant',LANG_LINKLIST_DeleteDesc);
	
	## grab the information for this page
  		
	$select_template->set_var('yesURL',$yesURL);
	$select_template->set_var('noURL',$noURL);
	
	$select_template->pfill_block("body");
}	

## =======================================================================        
##  linklist_displayInputForm      
## =======================================================================        
##  displays the input form for a certain entry  
##
## ======================================================================= 	
function linklist_sort_displayInputForm() {	
	global $gSession, $Auth;

	## check the params
	if(isset($_GET['page_id'])) {
		$page_id 	= $_GET['page_id'];
		$identifier	= $_GET['identifier'];
		$linklistID	= $_GET['linklistID'];
		$subcmd		= $_GET['subcmd'];
		$lowsub		= $_GET['lowsub'];
	} else {
		$page_id 	= $_POST['page_id'];
		$identifier	= $_POST['identifier'];
		$linklistID	= $_POST['linklistID'];
		$subcmd		= $_POST['subcmd'];
		$lowsub		= $_POST['lowsub'];
	}
	## template related stuff
	$template = new Template(ENGINE."datatypes/linklist/interface/");
	$template->set_templatefile(array("head" => "sort.tpl","linklist" => "sort.tpl","linklist_row" => "sort.tpl","foot" => "sort.tpl"));	
		
	## data conatiner
	$data = array();
	
	## get the infos
	linklist_getData($page_id,$data);
	
	## prepare the data
	$data = $data[$identifier];
	$template->set_var("saveIMG","../../interface/lang/".$Auth->auth["language"]."_button_save.gif");
	$template->set_var('Title',LANG_LINKLIST_SortTitle);
	$template->set_var('Desc',LANG_LINKLIST_SortDesc);
	
	$actionURL = "editor.php?op=doSort";
	$actionURL = $gSession->url($actionURL);
	$template->set_var('actionURL',$actionURL);
	
	## output the header
	$template->pfill_block("head");
	$template->pfill_block("linklist");	
	
	## prepare the vars
	$linklistID = $data['id'];

	## loop through all records
	$items = $data['length']; 

	for($i=0; $i< $items; $i++) {;
		## so we can savely dsiplay the entry
		$decription = $data[$i]["text"];							
		$template->set_var('decription',$decription);
						
		$template->set_var('linkID',$data[$i]['link']);	
		$template->set_var('order',$data[$i]['order']);		
		$return .= $template->pfill_block("linklist_row");
	}	
	
	## we should set all required vars here
	$template->set_var('op',"doSort");
	$template->set_var('Session',$gSession->id);		
	$template->set_var('page_id',$page_id);
	$template->set_var('identifier',$identifier);
	$template->set_var('linklistID',$linklistID);
	$template->set_var('subcmd',$subcmd);
	$template->set_var('lowsub',$lowsub);

	## finally set the close url
	$template->set_var('closeOP',"closeEditor");
	$template->pfill_block("foot");
} 
	

## =======================================================================        
##  linklist_sort_setItemOrder     
## =======================================================================        
##  actually moves the position of an item 
##
## ======================================================================= 	
function linklist_sort_setItemOrder($vLinklistID,$vLinkID,$vOrder,$vMoveBy) {	
	global $gSession;
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];	

	## db connection
	$db_connection = new DB_Sql();
	
	$select_query = "select max(link_list_item_order) from ".LINKLISTITEM." where link_list_id='$vLinklistID' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($select_query);
	$db_connection->next_record();
	list($max_order) = $db_connection->Record;		

	if($vMoveBy == 1) {
		$updated_order = $vOrder-1;
    	if ($updated_order < 0) {
      			$updated_order = 0;
    	}

		## next we should check if there is an item with that order
		$query = "SELECT link_list_item_id FROM ".LINKLISTITEM." WHERE link_list_item_order='$updated_order' AND link_list_id=".$vLinklistID." AND client_id=".$client_id;
		$db_result = $db_connection->query($query);
		$db_connection->next_record();
		
		$id_old_order = $db_connection->Record["link_list_item_id"];

		$element_counter = $db_connection->num_rows();

		if($max_order <=0) {
			$max_order = $element_counter;
		}
		
		$menu_order = $updated_order+1;
    	if ($menu_order > $max_order) {
      		$menu_order = $max_order;
   		}		
    }

	if ($vMoveBy == 0) {
		$updated_order = ($vOrder + 1);
    	if ($updated_order > $max_order){
      		$updated_order = $max_order;
    	}

		## next we should check if there is an item with that order
		$query = "SELECT link_list_item_id FROM ".LINKLISTITEM." WHERE link_list_item_order='$updated_order' AND link_list_id=".$vLinklistID." AND client_id=".$client_id;
		$db_result = $db_connection->query($query);
		$db_connection->next_record();
		
		$id_old_order = $db_connection->Record["link_list_item_id"];
		$element_counter = $db_connection->num_rows();
				    	
    	$menu_order = ($updated_order - 1);
    	if ($menu_order < 0){
      		$menu_order = 0;
    	}

	}
  
  	$update_query = "UPDATE ".LINKLISTITEM." SET link_list_item_order='$menu_order' WHERE link_list_item_id='$id_old_order' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($update_query);
   		
   	$update_query = "UPDATE ".LINKLISTITEM." SET link_list_item_order='$updated_order' WHERE link_list_item_id='$vLinkID' AND client_id='$client_id'";
   	$result_pointer = $db_connection->query($update_query);
}
?>
