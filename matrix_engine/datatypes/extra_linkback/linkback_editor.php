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
##  linkback_displayInputForm      
## =======================================================================        
##  displays the input form for a certain entry  
##
## ======================================================================= 
function linkback_displayInputForm($vItemID,$vLinkListID,$vPageID,$vIdentifier) {	
	global $Auth, $gSession,$input_language;
	## basically we need to display the page selector
	## optionally highlight the already selected page
	## and set all vars correctly
	
	if($vItemID > 0 && $vLinkListID > 0) {
		## okay we have a selected one
		## let's get it, and find out the page num
		$currentItem = linkback_getItem($vLinkListID,$vItemID);
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
##  linkback_storeItem      
## =======================================================================        
##  stores a link
##  there is now checking if the link has actually changed-  
##  since we only get called when a user does something- so we save
##	a new version everytime
## ======================================================================= 
function linkback_storeItem() {
	global $Auth,$input_language;
	
	$client_id = $Auth->auth["client_id"];	
	
	$return_value = false;

	$vPageID 		= $_POST['page_id'];
	$vTargetID 		= $_POST['selected_page'];
	$vIdentifier 	= $_POST['identifier'];
	$vItemID 		= $_POST['item_id'];

	$db_connection = new DB_Sql();  

	## first get the linklist id
	$vLinkListID = _linkback_getLinklist($vPageID,$vIdentifier);
	_linkback_storeItem($vLinkListID,$vItemID,$vTargetID,$vPageID,$vIdentifier);	

	## now for the targetpage
	$vLinkListID = _linkback_getLinklist($vTargetID,$vIdentifier);
	_linkback_storeItem($vLinkListID,0,$vPageID,$vTargetID,$vIdentifier);
  	
	## since we always create a new version we need to return true
	return true;
}

## =======================================================================        
##  _linkback_getLinklist      
## =======================================================================        
##
## ======================================================================= 
function _linkback_getLinklist($vPageID,$vIdentifier) {
	global $Auth,$input_language;
	$client_id = $Auth->auth["client_id"];	
	
	$db = new DB_Sql();  
	$query = "SELECT id FROM ".DB_PREFIX."linkback WHERE page_id = '$vPageID' AND identifier = '$vIdentifier' AND client_id='$client_id' AND language='$input_language'";
	$result_pointer = $db->query($query);	

	if($db->num_rows() == 0) { 
		## create a new entry
		$vLinkListID = linkback_createList($vPageID,$vIdentifier);
	} else {
		## we already have an entry- get its id
		$db->next_record();
		$vLinkListID = $db->Record["id"];
	}

	return $vLinkListID;
}

## =======================================================================        
##  _linkback_getOrderNumber     
## =======================================================================        
##
## ======================================================================= 
function _linkback_getOrderNumber($vLinkListID) {
	global $Auth;
	$client_id = $Auth->auth["client_id"];	
	
	$db = new DB_Sql(); 
	$query = "SELECT MAX(`order`) FROM ".DB_PREFIX."linkback_item WHERE linkback_id='$vLinkListID' AND client_id='$client_id'";
	$result_pointer = $db->query($query);
	$db->next_record();
	list($linkorder) = $db->Record;		

	return $linkorder;
}



## =======================================================================        
##  _linkback_storeItem     
## =======================================================================        
##
## ======================================================================= 
function _linkback_storeItem($vLinkListID,$vItemID,$vTargetID,$vPageID,$vIdentifier) {
	global $Auth,$input_language;
	$client_id = $Auth->auth["client_id"];	
	
	$db = new DB_Sql(); 
	$query = "SELECT id,target FROM ".DB_PREFIX."linkback_item  WHERE id='$vItemID' AND client_id='$client_id'";
	
	$result_pointer = $db->query($query,true);
		
	if($db->num_rows() == 0) { 
		## no item found
		## prepare the order number
		$linkorder = _linkback_getOrderNumber($vLinkListID);
		$linkorder++;	
		
		## create the item 
		$query = "INSERT ".DB_PREFIX."linkback_item (linkback_id,target,`order`, client_id) values ('$vLinkListID','$vTargetID','$linkorder','$client_id')";
		$result_pointer = $db->query($query);	
	} else {
		## we need to be carful when updating we need to remove the link to the old page first
		
		## we need to delete the corresponding item on the targetpage
		$db->next_record();
		$identifier = $vIdentifier;
		$old_target = $db->Record['target'];
		
		if($old_target != $vTargetID) {
			## okay we are assigning a new target- so we need to remove the link to
			## our page at the old target page
			$query = "SELECT id FROM ".DB_PREFIX."linkback WHERE page_id = '$old_target' AND identifier = '$vIdentifier' AND client_id='$client_id' AND language='$input_language' ";
			$result_pointer = $db->query($query);
			$db->next_record();
			$vOldLinkListID = $db->Record["id"];
			
			## let's delete the item
			$query = "DELETE FROM ".DB_PREFIX."linkback_item WHERE target='$vPageID' AND linkback_id='$vOldLinkListID' AND client_id='$client_id'";
			$result_pointer = $db->query($query);
			echo $query;
		}
		
		
		## finally we will update the item on our page
		$query = "UPDATE ".DB_PREFIX."linkback_item SET target='$vTargetID' WHERE id='$vItemID' AND client_id='$client_id'";
		$result_pointer = $db->query($query);
	}
	
}

## =======================================================================        
##  linkback_storeItem      
## =======================================================================        
##  store the specified icon
##  we need to check if there is a link listID already passed 
##
## ======================================================================= 
function linkback_deletItem($vPageID,$vIdentifier,$vItemID) {
	global $Auth,$input_language;
	$client_id = $Auth->auth["client_id"];	

	## prpeare the db
	$db = new DB_Sql();  

	## we need to find out where the current item points to

	## first we should get the linklist
	$query = "SELECT A.id,B.target FROM ".DB_PREFIX."linkback AS A INNER JOIN ".DB_PREFIX."linkback_item AS B ON A.id=B.linkback_id WHERE A.page_id= '$vPageID' AND A.identifier = '$vIdentifier' AND B.id='$vItemID' AND A.client_id='$client_id' AND A.language='$input_language' ";
	$result_pointer = $db->query($query);

	if($db->next_record()) {
		$vLinkListID = $db->Record["id"];
		$vTarget = $db->Record["target"];
		
		## okay now we need to delete the item on the target page
		$query = "SELECT id FROM ".DB_PREFIX."linkback WHERE page_id = '$vTarget' AND identifier = '$vIdentifier' AND client_id='$client_id' AND language='$input_language' ";
		$result_pointer = $db->query($query);
		## get the item information
		$db->next_record();
		$vTargetLinkListID = $db->Record["id"];
					
		## let's delete the item
		$query = "DELETE FROM ".DB_PREFIX."linkback_item WHERE target='$vPageID' AND linkback_id='$vTargetLinkListID' AND client_id='$client_id' LIMIT 1";
		$result_pointer = $db->query($query);
		
		## finally delete the item on this page
		$query = "DELETE FROM ".DB_PREFIX."linkback_item WHERE id='$vItemID' AND linkback_id='$vLinkListID' AND client_id='$client_id'";
		$result_pointer = $db->query($query);		
	}
}


## =======================================================================        
##  linkback_createList     
## =======================================================================        
##  setups a new list
## ======================================================================= 
function linkback_createList($vPageID,$vIdentifier) {
	global $Auth,$input_language;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];	

	$db_connection = new DB_Sql();  

	$query = "INSERT INTO ".DB_PREFIX."linkback (page_id,identifier, client_id,language) VALUES ('$vPageID','$vIdentifier','$client_id','$input_language')";

	$result_pointer = $db_connection->query($query,true);
	## get the created id
	$return = $db_connection->db_insertid($result_pointer);
	return $return; 
} 

## =======================================================================        
##  linkback_getItem     
## =======================================================================        
##  setups a new list
## ======================================================================= 
function linkback_getItem($vLinkListID,$vItemID) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];	

	$db_connection = new DB_Sql();  
		
	$query = "SELECT structure_text, id,target FROM ".DB_PREFIX."linkback_item AS A INNER JOIN ".STRUCTURE." AS B ON B.page_id=A.target WHERE A.linkback_id='$vLinkListID' AND A.id='$vItemID' AND A.client_id='$client_id'";
	$result_pointer = $db_connection->query($query);
	$db_connection->next_record();
	
	$target = $db_connection->Record["target"];
	
	return $db_connection->Record; 
}

## =======================================================================        
##  linkback_promptDelete        
## =======================================================================        
function linkback_promptDelete($yesURL,$noURL) {
	global $gSession,$Auth;
	$db_connectionLayout = new DB_Sql();  

	## prepare the template file
	$select_template = new Template(ENGINE."datatypes/linklist/interface");
	$select_template->set_templatefile(array("body" => "deletelink.tpl"));
	
	$select_template->set_var("yesIMG","lang/".$Auth->auth["language"]."_button_ja.gif");
	$select_template->set_var("noIMG","lang/".$Auth->auth["language"]."_button_nein.gif");
	$select_template->set_var('language_deletepage',LANG_linkback_DeleteTitle);
	$select_template->set_var('language_doyouwant',LANG_linkback_DeleteDesc);
	
	## grab the information for this page
  		
	$select_template->set_var('yesURL',$yesURL);
	$select_template->set_var('noURL',$noURL);
	
	$select_template->pfill_block("body");
}	

## =======================================================================        
##  linkback_displayInputForm      
## =======================================================================        
##  displays the input form for a certain entry  
##
## ======================================================================= 	
function linkback_sort_displayInputForm() {	
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
	$template = new Template(ENGINE."datatypes/extra_linkback/interface/");
	$template->set_templatefile(array("head" => "sort.tpl","linklist" => "sort.tpl","linklist_row" => "sort.tpl","foot" => "sort.tpl"));	
		
	## data conatiner
	$data = array();
	
	## get the infos
	linkback_getData($page_id,$data);
	
	## prepare the data
	$data = $data[$identifier];
	$template->set_var("saveIMG","../../interface/lang/".$Auth->auth["language"]."_button_save.gif");
	$template->set_var('Title',LANG_linkback_SortTitle);
	$template->set_var('Desc',LANG_linkback_SortDesc);
	
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
##  linkback_sort_setItemOrder     
## =======================================================================        
##  actually moves the position of an item 
##
## ======================================================================= 	
function linkback_sort_setItemOrder($vLinklistID,$vLinkID,$vOrder,$vMoveBy) {	
	global $gSession;
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];	

	## db connection
	$db_connection = new DB_Sql();
	
	$select_query = "SELECT MAX(`order`) FROM ".DB_PREFIX."linkback_item WHERE linkback_id='$vLinklistID' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($select_query);
	$db_connection->next_record();
	list($max_order) = $db_connection->Record;		

	if($vMoveBy == 1) {
		$updated_order = $vOrder-1;
    	if ($updated_order < 0) {
      			$updated_order = 0;
    	}

		## next we should check if there is an item with that order
		$query = "SELECT id FROM ".DB_PREFIX."linkback_item WHERE `order`='$updated_order' AND linkback_id=".$vLinklistID." AND client_id=".$client_id;
		$db_result = $db_connection->query($query);
		$db_connection->next_record();
		
		$id_old_order = $db_connection->Record["id"];

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
		$query = "SELECT id FROM ".DB_PREFIX."linkback_item WHERE `order`='$updated_order' AND linkback_id=".$vLinklistID." AND client_id=".$client_id;
		$db_result = $db_connection->query($query);
		$db_connection->next_record();
		
		$id_old_order = $db_connection->Record["id"];
		$element_counter = $db_connection->num_rows();
				    	
    	$menu_order = ($updated_order - 1);
    	if ($menu_order < 0){
      		$menu_order = 0;
    	}

	}
  
  	$update_query = "UPDATE ".DB_PREFIX."linkback_item SET `order`='$menu_order' WHERE id='$id_old_order' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($update_query);
   		
   	$update_query = "UPDATE ".DB_PREFIX."linkback_item SET `order`='$updated_order' WHERE id='$vLinkID' AND client_id='$client_id'";
   	$result_pointer = $db_connection->query($update_query);
}
?>
