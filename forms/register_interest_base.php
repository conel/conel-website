<?php
	session_start();
	
	// While developing, show errors
	//ini_set('display_errors',1);
	//error_reporting(E_ALL);

	$table_name = "tbl_science_courses";
	
	if (isset($_POST['firstname'])) {
	
		// This variable holds whether database input is successful
		$added = FALSE;
		
		// This variable sets whether the SQL functions are in debug mode or not - 
		$debug = 0; // 0 = Don't Debug, 1 = Debug
		
		// Required includes
		include_once('../matrix_engine/config.php');
		include_once('../matrix_engine/'.CLASSES_DIR.'db_mysql.php');		
		
		// what is the current date and time?
		$date_now_mysql = date('Y-m-d H:i:s'); // "2009-06-21 14:34:04": MySQL timestamp format
		
		// We need to instantiate the SQL class
		$sql = new DB_Sql();
		$sql->connect();
		
		// Set up POST variables to insert, escaping them for security
		$firstname = mysql_real_escape_string($_POST['firstname'], $sql->Link_ID);
		$surname = mysql_real_escape_string($_POST['surname'], $sql->Link_ID);
		$dob_day = mysql_real_escape_string($_POST['dob_day'], $sql->Link_ID);
		$dob_month = mysql_real_escape_string($_POST['dob_month'], $sql->Link_ID);
		$dob_year = mysql_real_escape_string($_POST['dob_year'], $sql->Link_ID);
		$date_of_birth = $dob_day . "/" . $dob_month . "/" . $dob_year;
		$email = mysql_real_escape_string($_POST['email'], $sql->Link_ID);
		$telephone_landline = mysql_real_escape_string($_POST['telephone_landline'], $sql->Link_ID);
		$telephone_mobile = mysql_real_escape_string($_POST['telephone_mobile'], $sql->Link_ID);
		$address_line_1 = mysql_real_escape_string($_POST['address_line_1'], $sql->Link_ID);
		$address_line_2 = mysql_real_escape_string($_POST['address_line_2'], $sql->Link_ID);
		$address_line_3 = mysql_real_escape_string($_POST['address_line_3'], $sql->Link_ID);
		$postcode = mysql_real_escape_string($_POST['postcode'], $sql->Link_ID);
		$borough_county = mysql_real_escape_string($_POST['borough_county'], $sql->Link_ID);
		$what_secondary_school_did_you_attend = mysql_real_escape_string($_POST['what_secondary_school_did_you_attend'], $sql->Link_ID);
		$how_did_you_hear_about_us = mysql_real_escape_string($_POST['message'], $sql->Link_ID);
		$receive_communications = mysql_real_escape_string($_POST['receive_communications'], $sql->Link_ID);
		$receive_communications = ($receive_communications == 'No') ? 'No' : 'Yes';

		// Build INSERT query
		$query = "INSERT INTO $table_name  
		(firstname, surname, date_of_birth, email, telephone_landline, telephone_mobile, address_line_1, address_line_2, address_line_3, postcode, borough_county, what_secondary_school_did_you_attend, how_did_you_hear_about_us, receive_communications, datetime_submitted) 
		VALUES('$firstname', '$surname', '$date_of_birth', '$email', '$telephone_landline', '$telephone_mobile', '$address_line_1', '$address_line_2', '$address_line_3', '$postcode', '$borough_county', '$what_secondary_school_did_you_attend', '$how_did_you_hear_about_us', '$receive_communications', '$date_now_mysql')";
		
		$sql->query($query,$debug);

		if ($sql->num_rows_affected() > 0) {
			$added = TRUE;
		} else {
			$added = FALSE;
		}
		
		if ($added) {
			header('Location: http://www.conel.ac.uk/registration_successful');
			exit;			
		} else {
			echo '<p>Failed to add info to database</p>';
			header('Location: http://www.conel.ac.uk/our_courses/pre_apprenticeships_science?registration=failed');
			exit;
		}		
	} else {
		header('Location: http://www.conel.ac.uk');
		exit;
	}
?>
