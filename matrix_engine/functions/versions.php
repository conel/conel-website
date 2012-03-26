<?PHP
## ======================================================================= 
##   webmatrix                                                     
## ======================================================================= 
##   Copyright (c) 2001, 2002, 2003 Stefan Elsner, Workmatrix        
## ======================================================================= 
##   <info@workmatrix.de> 
##
## ======================================================================= 

define('VERSION_DRAFT',0);
define('VERSION_APPROVAL_REQUESTED',1);

## =======================================================================        
##  version_createLocalCopy        
## =======================================================================        
##  locks a page- this is done by calling this function using the page_id
##  and the user_id- it will lock the page and update the structure flags
##  which it will return for processing 
## ======================================================================= 
function version_createLocalCopy($page_id) {
	## for now we create a copy for the page provided
	$return_id = 0;
	
	## get the db object
	$db = new DB_Sql();
	
	## check if we habe a copy of the page already
	$query = "SELECT local_id FROM ".VERSIONS." WHERE page_id='$page_id'";
	$result_pointer = $db->query($query);
	
	if($db->next_record()) {
		## okay we have a copy so we return the copy
		$return_id = $db->Record['local_id'];
	} else {
		## no copy found- create a new copy
		$return_id = page_copyPage($page_id);
		
		## register the copy in the version repository
		$query = "INSERT INTO ".VERSIONS." (page_id,local_id) VALUES ('$page_id','$return_id')";
		$result_pointer = $db->query($query,true);
	}
	
	return $return_id;
}

## =======================================================================        
##  version_removeLocalCopy        
## =======================================================================        
##  removes a page form the version repository
##
## ======================================================================= 
function version_removeLocalCopy($page_id) {
	
	## get the db object
	$db = new DB_Sql();
	
	## check if we habe a copy of the page already
	$query = "DELETE FROM ".VERSIONS." WHERE page_id='$page_id'";
	$result_pointer = $db->query($query);
	
}

## =======================================================================        
##  version_checkIfLocalCopyExists      
## =======================================================================        
##  returns the local copy id if it exists- otherwise null
##
## ======================================================================= 
function version_checkIfLocalCopyExists($page_id) {
	## for now we create a copy for the page provided
	$return_id = null;
	
	## get the db object
	$db = new DB_Sql();
	
	## check if we habe a copy of the page already
	$query = "SELECT local_id FROM ".VERSIONS." WHERE page_id='$page_id'";
	$result_pointer = $db->query($query);
	
	if($db->next_record()) {
		## okay we have a copy so we return the copy
		$return_id = $db->Record['local_id'];
	} 
	
	return $return_id;
}

## =======================================================================        
##  version_getOriginalPage      
## =======================================================================        
##  returns the originals page_id
##
## ======================================================================= 
function version_getOriginalPage($local_id) {
	## for now we create a copy for the page provided
	$return_id = null;
	
	## get the db object
	$db = new DB_Sql();
	
	## check if we habe a copy of the page already
	$query = "SELECT page_id FROM ".VERSIONS." WHERE local_id='$local_id'";
	$result_pointer = $db->query($query);
	
	if($db->next_record()) {
		## okay we have a copy so we return the copy
		$return_id = $db->Record['page_id'];
	} else {
		$return_id = $local_id;
	}
	
	return $return_id;
}

## =======================================================================        
##  version_removeLocalCopy        
## =======================================================================        
##  removes a page form the version repository
##
## ======================================================================= 
function version_requestApproval($page_id,$comment,$user_id,$date) {
	## find the local copy associated with the supplied page_id and add
	## the comment, user_id and date- set the flag to approval requested
	
	## get the db object
	$db = new DB_Sql();
	
	## check if we habe a copy of the page already
	$query = "UPDATE ".VERSIONS." SET comment='".$comment."', user_id='".$user_id."', date='".$date."', flag='".VERSION_APPROVAL_REQUESTED."' WHERE page_id='$page_id'";
	$result_pointer = $db->query($query);
	
}
?>
