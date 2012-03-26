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
##  box_structure_storePage        
## =======================================================================        
##  we store the base information for a certain template    
##
##  TODO:
##       - make all pages by default inactive
## =======================================================================        
function box_structure_storePage($box_id,$page_id) {
	global $gSession,$Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	$db_connectionLayout = new DB_Sql();  

	$lock_query = "LOCK TABLE ".DB_PREFIX."box_item write";
	$result_pointer = $db_connectionLayout->query($lock_query);	

	$select_query = "select max(item_order) from ".DB_PREFIX."box_item WHERE box_id='$box_id' AND client_id='$client_id'";
	$result_pointer = $db_connectionLayout->query($select_query);
	$db_connectionLayout->next_record();
	list($item_order) = $db_connectionLayout->Record;		
	$item_order++;
	
	$query = "INSERT INTO ".DB_PREFIX."box_item (box_id,target,item_order,client_id) values ('$box_id','$page_id','$item_order','$client_id')";
	$result_pointer = $db_connectionLayout->query($query);
	$structure_id = $db_connectionLayout->db_insertid($result_pointer);
	
	$lock_query = "UNLOCK table";
	$result_pointer = $db_connectionLayout->query($lock_query);
	
	return $structure_id;	
}


## =======================================================================        
##  box_storeBox        
## =======================================================================        
##  create the box_object iof it doesn't exists.
##  return the box id anyways   
##
##  TODO:
##       - make all pages by default inactive
## =======================================================================        
function box_storeBox($vPageID,$vIdentifier) {
	global $Auth,$input_language;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	$db_connection = new DB_Sql();  

	## first we need to check if the entry already exists
	$query = "SELECT box_id FROM ".DB_PREFIX."box WHERE page_id = '$vPageID' AND identifier = '$vIdentifier' AND client_id='$client_id' AND language='$input_language'";
	$result_pointer = $db_connection->query($query);	

	if($db_connection->num_rows() == 0) { 
		## create a new entry
		$box_id = _createBox($vPageID,$vIdentifier);
	} else {
		## we already have an entry- get its id
		$db_connection->next_record();
		$box_id = $db_connection->Record["box_id"];
	}

	return $box_id;
}

## =======================================================================        
##  _createBox     
## =======================================================================        
##  internal: create a box base element
## ======================================================================= 
function _createBox($vPageID,$vIdentifier) {
	global $Auth,$input_language;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];	

	## db object
	$db = new DB_Sql();  

	$query = "INSERT INTO ".DB_PREFIX."box (page_id,identifier, client_id,language) VALUES ('$vPageID','$vIdentifier','$client_id','$input_language')";
	$result_pointer = $db->query($query);
	## get the created id
	$return = $db->db_insertid($result_pointer);
	return $return; 
} 
?>
