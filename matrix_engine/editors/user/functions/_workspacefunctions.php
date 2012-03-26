<?PHP

## =======================================================================        
##  workspace_getPages        
## =======================================================================        
##  pass it a list of workspaces and it'll return an array
##	containing the pages that are part of the workspaces
##
##  TODO:  
## =======================================================================
function workspace_getPages($workspaces) {
	global $Auth;
	## multiclient
	$client_id = $Auth->auth["client_id"];

	$db_connection = new DB_Sql();
	
	
	## prepare the query
	$query = '';
	foreach($workspaces as $current_workspace) {
		if($query=='') {
			$query .= "workspace_id='".$current_workspace."'";
		} else {
			$query .= " OR workspace_id='".$current_workspace."'";
		}
	}	
	
	## now do the actually query
	$query = "SELECT DISTINCT(workspace_item) FROM ".DB_PREFIX."workspace_item WHERE client_id='$client_id' AND (".$query.")";
	$result_pointer = $db_connection->query($query);

	$results = array();
	while($db_connection->next_record()) {	
		$results[] = $db_connection->Record["workspace_item"];	
	}
	return $results;	
}

## =======================================================================        
##  workspace_getStructureID        
## =======================================================================        
##  pass it a list of workspaces and it'll return an array
##	containing the pages that are part of the workspaces
##
##  TODO:  
## =======================================================================
function workspace_getStructureID($workspaces) {
	global $Auth;
	## multiclient
	$client_id = $Auth->auth["client_id"];

	$db_connection = new DB_Sql();
	
	
	## prepare the query
	$query = '';
	foreach($workspaces as $current_workspace) {
		if($query=='') {
			$query .= "workspace_id='".$current_workspace."'";
		} else {
			$query .= " OR workspace_id='".$current_workspace."'";
		}
	}	
	
	## now do the actually query
	$query = "SELECT DISTINCT(structure_id) FROM ".DB_PREFIX."workspace_item AS a INNER JOIN ".STRUCTURE." AS b ON a.workspace_item=b.page_id WHERE a.client_id='$client_id' AND (".$query.")";
	$result_pointer = $db_connection->query($query);
	
	$results = array();
	while($db_connection->next_record()) {	
		$results[] = $db_connection->Record["structure_id"];	
	}
	return $results;	
}
?>
