<?PHP
## ======================================================================= 
##   webmatrix                                                     
## ======================================================================= 
##   Copyright (c) 2001, 2002, 2003 Stefan Elsner, Workmatrix        
## ======================================================================= 
##   <info@workmatrix.de> 
##
## ======================================================================= 
require("../../../config.php");
require("../../../classes/db_mysql.php");


## Creating Table
$table = "CREATE TABLE ".DB_PREFIX."extra_checkboxgroup (group_id int(10) unsigned NOT NULL auto_increment,  page_id int(10) unsigned NOT NULL default '0',  identifier varchar(50) NOT NULL default '',  text mediumtext,client_id int(10) unsigned NOT NULL default '1',  PRIMARY KEY  (group_id)) TYPE=MyISAM;";

## Open the db-connection
$db_connection = new DB_Sql();
echo_normal("Opening database connection...");

echo_normal("Creating Tables...");
$db_connection->query($drop);


if($db_connection->query($table)) {
	echo_success("Tables successfully installed");
	echo_success("Installation successfull");
} else {
	echo_failed("Could not create table");
	echo_failed("Installation failed");
} 


function echo_success($text) {
    echo "<font color=\"green\"><b>Success!</b></font> $text<br>\n";   	
}

function echo_failed($text) {
	global $failed;
	
	echo "<font color=\"red\"><b>Failed!</b></font> $text<br>\n";
	
	$failed++;
}

function echo_normal($text) {
        echo "$text<br>\n";   	
}
?>
