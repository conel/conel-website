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
##  clients_newClient       
## =======================================================================        
##  all functions in this file provide you with the basic client
##  utilities. The basic client kernel needs to be extended by modules
##  like the newsletter module or the shopping module
##
## ======================================================================= 
function clients_newClient($email,$password) {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## the basic client only supports email and password- email needs to be unique	

	## prepare the db-object
	$db = new DB_Sql();
	$insert_query = "insert into ".USER_PAGES." (email, entered, password, client_id) values ('$email', 'now()','$password','$client_id')";
	$result_pointer = $db->query($insert_query);	
	$id = $db->db_insertid($result_pointer);
	
	return $id;
}

## =======================================================================        
##  clients_deleteClient       
## =======================================================================        
##  delete a client- how do we handle 
##
## ======================================================================= 
function clients_deleteClient($id) {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## the basic client only supports email and password- email needs to be unique	

	## prepare the db-object
	$db = new DB_Sql();
	$select_query = "DELETE FROM ".USER_PAGES." WHERE id='$id' AND client_id='$client_id'";
	$result_pointer = $db->query($select_query);	
}
?>
