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
##  lock_lockpage        
## =======================================================================        
##  locks a page- this is done by calling this function using the page_id
##  and the user_id- it will lock the page and update the structure flags
##  which it will return for processing 
## ======================================================================= 
function lock_lockpage($page_id,$user_id) {
	## get the db object
	$db = new DB_Sql();
	
	## first check if a lock for this page already exists
	$query = "SELECT user_id FROM ".LOCKS." WHERE page_id='$page_id'";
	$result_pointer = $db->query($query);
	
	if($db->next_record()) {
		## okay a lock exists - let's replace it with the new lock
		$query = "UPDATE ".LOCKS." SET user_id='$user_id', modified=now() WHERE page_id='$page_id'";
		$result_pointer = $db->query($query);
	} else {
		## okay this means we are creating a new lock- so first we delete all locks for this user
		## and update the structure accordingly
		$query = "SELECT page_id FROM ".LOCKS." WHERE user_id='$user_id'";
		$result_pointer = $db->query($query);
		
		while($db->next_record()) {
			$current_page_id = $db->Record['page_id'];
			structure_unsetStateID($current_page_id,PAGE_PENDING);
		}
			
		$query = "DELETE FROM ".LOCKS." WHERE user_id='$user_id'";
		$result_pointer = $db->query($query);
		
		## create the new entry
		$query = "INSERT INTO ".LOCKS." (page_id,user_id,modified) VALUES('$page_id','$user_id',now())";
		$result_pointer = $db->query($query);
	}
	
	## finally we should update the structure flags
	structure_setStateID($page_id,PAGE_PENDING);
}

## =======================================================================        
##  lock_unlockpages        
## =======================================================================        
##  remove a lock from a page- this is done by simply deleting the 
##  lock and updating the structure flags
##  which it will return for processing 
## ======================================================================= 
function lock_unlockpages($user_id) {
	## get the db object
	$db = new DB_Sql();
	
	## first check if a lock for this page already exists
	$query = "SELECT page_id FROM ".LOCKS." WHERE user_id='$user_id'";
	$result_pointer = $db->query($query);

	while($db->next_record()) {
		$current_page_id = $db->Record['page_id'];
		structure_unsetStateID($current_page_id,PAGE_PENDING);
	}	

	$query = "DELETE FROM ".LOCKS." WHERE user_id='$user_id'";
	$result_pointer = $db->query($query);
	
	## we have updated the locks for this users- now we should delete
	## all locks (for all users) that timed out
	$creation_time = time() - (60*SESSION_LIFETIME);
	
	## now delete all locks that are older then the creation_time
	$query = "SELECT page_id FROM ".LOCKS." WHERE modified < FROM_UNIXTIME($creation_time) AND user_id != -1";
	$result_pointer = $db->query($query);
	
	while($db->next_record()) {
		$current_page_id = $db->Record['page_id'];
		structure_unsetStateID($current_page_id,PAGE_PENDING);
	}
	$query = "DELETE FROM ".LOCKS." WHERE modified < FROM_UNIXTIME($creation_time) AND user_id != -1"; 
	$result_pointer = $db->query($query);
}


## =======================================================================        
##  lock_unlockpage        
## =======================================================================        
##  remove a lock from a page- this is done by simply deleting the 
##  lock and updating the structure flags
##  which it will return for processing 
## ======================================================================= 
function lock_unlockpage($page_id) {
	## get the db object
	$db = new DB_Sql();
	
	## first check if a lock for this page already exists
	$query = 'DELETE FROM '.LOCKS.' WHERE page_id=\''.$page_id.'\'';
	$result_pointer = $db->query($query);
	
	## finally we should update the structure flags
	structure_unsetStateID($page_id,PAGE_PENDING);
}

## =======================================================================        
##  lock_pagenotlocked     
## =======================================================================        
##  check if a page is locked- 
##  if it is locked by the same user or if it is not locked-> true
##  if it is lcoked by another user- and still valid -> return false
## ======================================================================= 
function lock_pageislocked($page_id,$user_id) {
	## get the db object
	$db = new DB_Sql();
	
	## init the return value
	$pageislocked = true;
	
	## first check if a lock for this page already exists
	$query = 'SELECT user_id,UNIX_TIMESTAMP(modified) AS modified FROM '.LOCKS.' WHERE page_id=\''.$page_id.'\'';
	$result_pointer = $db->query($query);
	
	if($db->next_record()) {
		$locked_user = $db->Record['user_id'];
		$modified = $db->Record['modified'];

		## check if the lock already timed out
		if(time() > $modified+(60*SESSION_LIFETIME)) {
			## the lock timed out now- so we will clear the lock for this page and return false
			lock_unlockpage($page_id);
			$pageislocked = false;
			echo 'time out';
		}
		
		## check if it is the same user
		if($user_id == $locked_user) {
			## okay the user is allowed to edit the page
			$pageislocked = false;
		} 
		
	} else {
		## no lock found
		$pageislocked = false;
	}
	
	return $pageislocked;
}
?>
