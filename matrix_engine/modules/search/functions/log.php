<?php

	## =======================================================================        
	##  search_log        
	## =======================================================================        
	##  you need to include and call this when you wan to log searches
	##
	## =======================================================================
	function search_log($query,$results) {
		## okay we will need to stroe information about the search passed to us
		
		## we expect to get the query and the number of results
		## we will fetch the rest from the statistics module
		
		## this means that this module will only work when the reproting
		## module is installed
		
		## we need to fetch: when, word, source page, number of results, user id	
		$cookie_data = $_COOKIE['webmatrixReports'];
		
		$cookie_data = explode('.',$cookie_data);
		
		if(array($cookie_data)) {
			## get the cookie data
			$visitor_id = intval($cookie_data[1]);
			$source_page = intval($cookie_data[2]);
			
			$timestamp = time();
			
			## now prepare the the actual insert query
			$db = new DB_Sql();
			$query = "INSERT INTO ".DB_PREFIX."reports_searchqueries (timestamp,term,results,page_id,visitor_id) VALUES ('".time()."','".$query."','".$results."','".$source_page."','".$visitor_id."')";

			$rp = $db->query($query,false);
		}
	}
?>