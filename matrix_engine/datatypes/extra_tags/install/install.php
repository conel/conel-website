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
		
## Open the db-connection
$db_connection = new DB_Sql();
echo_normal("Opening database connection...");

echo_normal("Creating Tables...");

$table = 'CREATE TABLE '.DB_PREFIX.'extra_tags (`id` int(10) NOT NULL auto_increment,`counter` INT(10) NOT NULL, `text` varchar(255) NOT NULL default \'\', PRIMARY KEY (`id`))';
if($db_connection->query($table)) {
	echo_success("Tables successfully installed");
	echo_success("Installation successfull");
} else {
	echo_failed("Could not create table");
	echo_failed("Installation failed");
} 

$table = "CREATE TABLE ".DB_PREFIX."extra_tags2page (page_id int(10) unsigned NOT NULL default '0',`item_id` INT(10) NOT NULL,`identifier` varchar(50) NOT NULL default '')";
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
