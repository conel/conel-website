<?php
## =======================================================================        
## newsletter_reportsGetOpens   
## =======================================================================        
## updates the newsletter's name
##    
## =======================================================================
function newsletter_reportsGetOpens($newsletter_id) {	
	## save the name
	$db_connection = new DB_Sql();

	$insert_query = "SELECT count(*) as total_opens FROM ".DB_PREFIX."reports_newsletter_open WHERE newsletter_id='$newsletter_id'";
	$result_pointer = $db_connection->query($insert_query);
	
	if($db_connection->next_record()) {
		return $db_connection->Record["total_opens"];
	} else {
		return 0;
	}
}

## =======================================================================        
## newsletter_reportsGetOpens   
## =======================================================================        
## updates the newsletter's name
##    
## =======================================================================
function newsletter_reportsGetUnsubscribes($newsletter_id) {	
	## save the name
	$db_connection = new DB_Sql();

	$insert_query = "SELECT count(*) as total_opens FROM ".DB_PREFIX."reports_newsletter_unsubscribes WHERE newsletter_id='$newsletter_id'";
	$result_pointer = $db_connection->query($insert_query);
	
	if($db_connection->next_record()) {
		return $db_connection->Record["total_opens"];
	} else {
		return 0;
	}
}

## =======================================================================        
## newsletter_reportsGetTotalLinks   
## =======================================================================        
## updates the newsletter's name
##    
## =======================================================================
function newsletter_reportsGetTotalLinks($newsletter_id) {	
	## save the name
	$db_connection = new DB_Sql();

	$data = array();

	## first we need to get the total numbe rof links clicked
	$query = "SELECT COUNT(*) AS total_links FROM ".DB_PREFIX."reports_newsletter_externallinks WHERE newsletter_id='$newsletter_id' GROUP BY url";
	$rp = $db_connection->query($query);
	
	$data['total_links'] = $db_connection->num_rows();

	## first we need to get the total numbe rof links clicked
	$query = "SELECT COUNT(*) AS total_clients FROM ".DB_PREFIX."reports_newsletter_externallinks WHERE newsletter_id='$newsletter_id' GROUP BY client_id";
	$rp = $db_connection->query($query);
	$data['total_clients'] = $db_connection->num_rows();
	
	return $data;
}

## =======================================================================        
## newsletter_reportsGetLinksClicked   
## =======================================================================        
## updates the newsletter's name
##    
## =======================================================================
function newsletter_reportsGetLinksClicked($newsletter_id) {	
	## save the name
	$db_connection = new DB_Sql();

	## first we need to get the total numbe rof links clicked
	$query = "SELECT url,COUNT(*) AS clicks FROM ".DB_PREFIX."reports_newsletter_externallinks WHERE newsletter_id='$newsletter_id' GROUP BY url";
	$rp = $db_connection->query($query);
	
	$data = array();
	while($db_connection->next_record()) {
		$data[$page_id]['url'] = $db_connection->Record["url"];
		$data[$page_id]['clicks'] = $db_connection->Record["clicks"];
	}
	
	return $data;
}

## =======================================================================        
## newsletter_reportsGetOpensOverTime   
## =======================================================================        
## updates the newsletter's name
##    
## =======================================================================
function newsletter_reportsGetOpensOverTime($newsletter_id) {	
	## save the name
	$db_connection = new DB_Sql();

	$query = "SELECT COUNT(*) as opens, hour FROM ".DB_PREFIX."reports_newsletter_open WHERE newsletter_id='".$newsletter_id."' GROUP BY hour ORDER BY hour";
	$result_pointer = $db_connection->query($query);

	$data = array();
	while($db_connection->next_record()) {
		$data[$db_connection->Record["hour"]]['hour'] = $db_connection->Record["hour"];
		$data[$db_connection->Record["hour"]]['opens'] = $db_connection->Record["opens"];
	}
	
	return $data;
}


## =======================================================================        
## newsletter_reportsGetUnsubscribesOverTime   
## =======================================================================        
## updates the newsletter's name
##    
## =======================================================================
function newsletter_reportsGetUnsubscribesOverTime($newsletter_id) {	
	## save the name
	$db_connection = new DB_Sql();

	$query = "SELECT COUNT(*) as opens, hour FROM ".DB_PREFIX."reports_newsletter_unsubscribes WHERE newsletter_id='".$newsletter_id."' GROUP BY hour ORDER BY hour";
	$result_pointer = $db_connection->query($query);

	$data = array();
	while($db_connection->next_record()) {
		$data[$db_connection->Record["hour"]]['hour'] = $db_connection->Record["hour"];
		$data[$db_connection->Record["hour"]]['opens'] = $db_connection->Record["opens"];
	}
	
	return $data;
}

## =======================================================================        
## newsletter_reportsGetOpensOverTime   
## =======================================================================        
## updates the newsletter's name
##    
## =======================================================================
function newsletter_reportsGetBouncesOverTime($newsletter_id) {	
	## save the name
	$db_connection = new DB_Sql();

	$query = "SELECT COUNT(*) as bounces, hour FROM ".DB_PREFIX."reports_newsletter_bounces WHERE newsletter_id='".$newsletter_id."' GROUP BY hour ORDER BY hour";
	$result_pointer = $db_connection->query($query);

	$data = array();
	while($db_connection->next_record()) {
		$data[$db_connection->Record["hour"]]['hour'] = $db_connection->Record["hour"];
		$data[$db_connection->Record["hour"]]['bounces'] = $db_connection->Record["bounces"];
	}
	
	return $data;
}
?>