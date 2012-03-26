<?php  
## =======================================================================  
##  api_images.php														  
## =======================================================================  
##  Version: 		0.03													  											  
##  by: 			S. Elsner											  
## =======================================================================  
##  Description:															  
##    * image manipulation functions	  
## =======================================================================  
##
##  22.11.2003:  
##    * inital setup  
##    * images can be stored in the cache
## =======================================================================  

## =======================================================================        
##  _getUserInformation        
## =======================================================================        
##  get's the access rights for a certin user          
##        
## =======================================================================        
function _getUserInformation($user_id) {		
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];

	$db = new DB_Sql();
	
	## get all access objects for this group
	$query = "SELECT user_id,user_name,lastname,firstname,email,group_id FROM ".USERS." WHERE user_id='".$user_id."' AND client_id='$client_id'";
	$result_pointer = $db->query($query);	

	$userInfo = array();
	if($db->next_record()) {
		## prepare the type and subtype
		$userInfo['user_id'] = $db->Record['user_id'];
		$userInfo['user_name'] = $db->Record['user_name'];
		$userInfo['lastname'] = $db->Record['lastname'];
		$userInfo['firstname'] = $db->Record['firstname'];
		$userInfo['email'] = $db->Record['email'];
		$userInfo['group_id'] = $db->Record['group_id'];
	}
	return $userInfo;
}
        
## =======================================================================        
##  _getUserAccessRights        
## =======================================================================        
##  get's the access rights for a certin user          
##        
## =======================================================================        
function _getUserAccessRights($user_id) {		
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];

	$db = new DB_Sql();
	
	## get all access objects for this group
	$query = "SELECT type,subtype, object, access, ".ACCESS.".group_id FROM ".ACCESS." INNER JOIN ".USERS." ON ".ACCESS.".group_id=".USERS.".group_id WHERE user_id='".$user_id."' AND ".USERS.".client_id='$client_id'";
	$result_pointer = $db->query($query);	
	
	$access_rights = array();
	while($db->next_record()) {
		## prepare the type and subtype
		$type 		= $db->Record["type"];
		$subtype 	= $db->Record["subtype"];
		$object 	= $db->Record["object"];
		
		$access_rights['group_id'] = $db->Record["group_id"];

		if((!empty($subtype)) && (!empty($object))) {
			$access_rights[$type][$subtype][$object]['access'] = $db->Record["access"];
		} else if(!empty($subtype)) {
			$access_rights[$type][$subtype]['access'] = $db->Record["access"];	
		} else {	
			$access_rights[$type]['access'] = $db->Record["access"];
		}
	}
	return $access_rights;
}
        
## =======================================================================        
##  _setUserAccessRights        
## =======================================================================        
##  used for setting the accessRights      
##        
## =======================================================================        
function _setUserAccessRights($group_id,$type,$subtype,$object,$access) {		
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];

	$db = new DB_Sql();

	## okay we are ready to setup access rights
	$query = "INSERT INTO ".ACCESS." (type,subtype,object,access,group_id,client_id) VALUES('$type','$subtype','$object','$access','$group_id','$client_id')";
	$result_pointer = $db->query($query);

}


## =======================================================================        
##  _getGroupAccessRights        
## =======================================================================        
##  get's the access rights for a certin user          
##        
## =======================================================================        
function _getGroupAccessRights($group_id) {		
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];

	$db = new DB_Sql();
	
	## get all access objects for this group
	$query = "SELECT type,subtype, object, access, group_id FROM ".ACCESS." WHERE group_id='".$group_id."' AND client_id='$client_id'";
	$result_pointer = $db->query($query);	

	$access_rights = array();
	$access_rights['group_id'] = $group_id;
	while($db->next_record()) {
		## prepare the type and subtype
		$type 		= $db->Record["type"];
		$subtype 	= $db->Record["subtype"];
		$object 	= $db->Record["object"];

		if((!empty($subtype)) && (!empty($object))) {
			$access_rights[$type][$subtype][$object]['access'] = $db->Record["access"];
		} else if(!empty($subtype)) {
			$access_rights[$type][$subtype]['access'] = $db->Record["access"];	
		} else {	
			$access_rights[$type]['access'] = $db->Record["access"];
		}
	}
	return $access_rights;
}

?>
