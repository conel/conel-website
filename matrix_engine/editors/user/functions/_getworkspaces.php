<?PHP

## =======================================================================        
##  getWorkspaces        
## =======================================================================        
##  returns the list of workspaces available
##
##  TODO:  
## =======================================================================
function getWorkspaces() {
	global $Auth;

	$db_connection = new DB_Sql();
	
	$query = 'SELECT * FROM '.DB_PREFIX.'workspaces WHERE 1';
	$result_pointer = $db_connection->query($query);
	
	$results = array();
	$i=0;
	while($db_connection->next_record()) {
		$results[$i]['id'] = $db_connection->Record["workspace_id"];
		$results[$i]['text'] = $db_connection->Record["name"];
		$i++;
	} 
	return $results;
}

?>
