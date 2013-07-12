<?php

//form url: http://www.conel.ac.uk/for_employers/apprenticeships

session_start();

// While developing, show errors
//ini_set('display_errors',1);
//error_reporting(E_ALL);

// First make sure this file hasn't been directly accessed and only run if coming from open day page
if (isset($_POST['fullname'])) {

	// This variable holds whether database input is successful
	$added = FALSE;
	
	// This variable sets whether the SQL functions are in debug mode or not - 
	$debug = 1; // 0 = Don't Debug, 1 = Debug
	
	// Required includes
	include_once('../../matrix_engine/config.php');
	include_once('../../matrix_engine/'.CLASSES_DIR.'db_mysql.php');
	include_once('../../matrix_engine/'.CLASSES_DIR.'class_mailer.php');
	
	/* This script inserts submitted 'Register your interest now' form data and into a database (tbl_open_day) */
	
	// what is the current date and time?
	$date_now_mysql = date('Y-m-d H:i:s'); // "2009-06-21 14:34:04": MySQL timestamp format
	
	// We need to instantiate the SQL class
	$sql = new DB_Sql();
	$sql->connect();
	
	// Set up POST variables to insert, escaping them for security
	$fullname = mysql_real_escape_string($_POST['fullname'], $sql->Link_ID);
	
	//no dob on this form
	//$dob_day = mysql_real_escape_string($_POST['dob_day'], $sql->Link_ID);
	//$dob_month = mysql_real_escape_string($_POST['dob_month'], $sql->Link_ID);
	//$dob_year = mysql_real_escape_string($_POST['dob_year'], $sql->Link_ID);
	//$date_of_birth = $dob_day . "/" . $dob_month . "/" . $dob_year;
	
	$email = mysql_real_escape_string($_POST['email'], $sql->Link_ID);
	$contact_number = mysql_real_escape_string($_POST['contact_number'], $sql->Link_ID);
	$address_line_1 = mysql_real_escape_string($_POST['address_line_1'], $sql->Link_ID);
	$address_line_2 = mysql_real_escape_string($_POST['address_line_2'], $sql->Link_ID);
	$address_line_3 = mysql_real_escape_string($_POST['address_line_3'], $sql->Link_ID);
	$postcode = mysql_real_escape_string($_POST['postcode'], $sql->Link_ID);
	$borough_county = mysql_real_escape_string($_POST['borough_county'], $sql->Link_ID);
	$how_heard = mysql_real_escape_string($_POST['how_heard'], $sql->Link_ID);
	$receive_communications = mysql_real_escape_string($_POST['receive_communications'], $sql->Link_ID);
	$receive_communications = ($receive_communications == 'No') ? 'No' : 'Yes';

	// Build INSERT query
	$query = "INSERT INTO tbl_employers_apprenticeships  
	(fullname, email, contact_number, address_line_1, address_line_2, address_line_3, postcode, borough_county, how_heard, receive_communications, datetime_submitted) 
	VALUES('$fullname', '$email', '$contact_number', '$address_line_1', '$address_line_2', '$address_line_3', '$postcode', '$borough_county', '$how_heard', '$receive_communications', '$date_now_mysql')";
	$sql->query($query,$debug);

	if ($sql->num_rows_affected() > 0) {
		$added = TRUE;
	} else {
		$added = FALSE;
	}
	
	/*
	CREATE TABLE  `conel`.`tbl_employers_apprenticeships` (
	  `id` int(11) NOT NULL auto_increment,
	  `fullname` varchar(50) default NULL,
	  `email` varchar(70) default NULL,
	  `contact_number` varchar(50) default NULL,
	  `address_line_1` varchar(100) default NULL,
	  `address_line_2` varchar(100) default NULL,
	  `address_line_3` varchar(100) default NULL,
	  `postcode` varchar(15) default NULL,
	  `borough_county` varchar(60) default NULL,
	  `how_heard` text default NULL,
	  `receive_communications` varchar(5) default NULL,
	  `datetime_submitted` datetime NOT NULL,
	  PRIMARY KEY  (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1;
	*/
	
	if ($added) {
		
		// Create email
		// ...		
		// no email from this form
					
		header('Location: http://www.conel.ac.uk/registration_successful');
		exit;

		
	} else {
		echo '<p>Failed to add info to database</p>';
		//header('Location: http://www.conel.ac.uk/our_courses/public_services_at_enfield?registration=failed');
		exit;
	}
	
} else {
	header('Location: http://www.conel.ac.uk');
	exit;
}
?>
