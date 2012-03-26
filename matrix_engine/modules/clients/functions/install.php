<?php

## =======================================================================        
##  clients_install        
## =======================================================================        
##  this function checks if all required tables are setup.
##  if not they will get installed- this is used to allow
##  to easily add the system to a webmatrix installation
##  we now support installing the tables into a different db
##
##  TODO:
##  
## ======================================================================= 
function clients_install($host=null,$database=null,$user=null,$password=null) {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];

	## prepare the db-object
	$db_connection = new DB_Sql($host,$database,$user,$password);
	
	## get all client related tables
	$query = "SHOW TABLES LIKE '".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."%'";
	
	$result_pointer = $db_connection->query($query);
	
	## first check if the clients table exists
	$existing_tables = array();
	while($db_connection->next_record()) {
		## first check if the main table exists
		$existing_tables[] = $db_connection->Record[0];
	}
	
	## check if the clients main table exist
	if(!in_array(DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'],$existing_tables)) {
		## okay we need to install the clients table
		$query = "CREATE TABLE ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." (`id` int(11) NOT NULL auto_increment,`client_id` int(10) NOT NULL default '1',`groupid` int(10) NOT NULL default '1',`email` varchar(255) NOT NULL default '',`entered` datetime default NULL,`first_name` varchar(255) NOT NULL default '',`last_name` varchar(255) NOT NULL default '',PRIMARY KEY  (`id`))";
		$result_pointer = $db_connection->query($query);
	}
	
	## check if the search cache is installed
	if(!in_array(DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'_searchcache',$existing_tables)) {
		## okay we need to install the clients table
		$query = "CREATE TABLE ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_searchcache (`id` varchar(32) NOT NULL default '0',`query` mediumtext NOT NULL,`user_id` int(10) NOT NULL default '0',PRIMARY KEY  (`id`), KEY `user_id` (`user_id`))";
		$result_pointer = $db_connection->query($query);
	}

	## check if the groups table is installed
	if(!in_array(DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'_groups',$existing_tables)) {
		## okay we need to install the clients table
		$query = "CREATE TABLE ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_groups (`id` int(10) NOT NULL auto_increment,`groupname` varchar(200) NOT NULL default '',`controlfile` varchar(200) NOT NULL default '',PRIMARY KEY  (`id`))";
		$result_pointer = $db_connection->query($query);
		## insert a dummy entry
		$query = "INSERT INTO ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_groups VALUES (1, '".LANG_NoName."', 'base')";
		$result_pointer = $db_connection->query($query);
	}

	## check if the filter table is installed
	if(!in_array(DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].'_filters',$existing_tables)) {
		## okay we need to install the clients table
		$query = "CREATE TABLE ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_filters (`id` int(11) NOT NULL auto_increment,`query` mediumtext NOT NULL,`searchdata` mediumtext NOT NULL,`name` varchar(255) NOT NULL default '',`visible` char(1) NOT NULL default '0',PRIMARY KEY  (`id`))";
		$result_pointer = $db_connection->query($query,true);

		## we need to install the all filter- which is default
		$query = "INSERT INTO `".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."_filters` VALUES (1, 'SELECT * FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." WHERE 1=1 ', 'a:0:{}', 'All', '1');";
		$result_pointer = $db_connection->query($query,true);

	}


}

?>