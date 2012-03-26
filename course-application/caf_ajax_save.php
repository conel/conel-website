<?php
	session_start();

	include_once('../matrix_engine/config.php');
	include_once('../matrix_engine/'.CLASSES_DIR.'db_mysql.php');
	include_once('../matrix_engine/'.CLASSES_DIR.'class_mailer.php');
	
	// instantiate the SQL classes
	$sql = new DB_Sql();
	$sql->connect();
	
	// Array to hold fields that don't exist in the database;
	$not_columns = array('security_code');
	// Array to hold values to be capitalised
	$ucwords_keys = array('firstname', 'surname', 'home_address', 'language_spoken_at_home', 'job_title', 'employer', 'job_address', 'nationality', 'country_of_usual_reference');
	// Array to hold values to be uppercased
	$strtoupper_keys = array('postcode', 'job_postcode');
	
	if ($_SESSION['caf']['signed_in'] == TRUE) {
		if (isset($_POST)) {
			$sql_query = "UPDATE tbl_course_application SET ";
			foreach ($_POST as $key => $value) {
				if (!in_array($key, $not_columns)) {
					// nkowald - 2011-02-24 - Removed this check as a user may be wanting to remove previously saved info
					//if ($value != '') {
						// checkboxes and radio button handling
						if (is_array($value)) {
							$csv_value = implode('|',$value);
							$col_value = mysql_real_escape_string($csv_value, $sql->Link_ID);
						} else {
							// nkowald - 2010-06-14 - Added forced uppercase, capitalise values
							if (in_array($key, $ucwords_keys)) {
								$value = ucwords($value);
							}
							if (in_array($key, $strtoupper_keys)) {
								$value = strtoupper($value);
							}
							$col_value = mysql_real_escape_string($value, $sql->Link_ID);
						}
						$sql_query .= "$key = '$col_value', ";
					//}
				}
				// nkowald - 2010-06-14 - Added forced uppercase, capitalise values
				if (in_array($key, $ucwords_keys)) {
					$value = ucwords($value);
				}
				if (in_array($key, $strtoupper_keys)) {
					$value = strtoupper($value);
				}
				$_SESSION['caf'][$key] = $value;
			}
			
			$datetime_sub = date('Y-m-d H:i:s'); // 2010-06-02 14:24:04 - MySQL timestamp format
			$sql_query .= "datetime_submitted_last = '$datetime_sub', page_step = '".$_SESSION['caf']['page_step']."' ";
			$sql_query .= " WHERE id = ".$_SESSION['caf']['id']."";
			if ($_SESSION['caf']['id'] != 0) {
				if (!$sql->query($sql_query, $debug)) {
					echo "FAIL";
				}
			}
		}
	}
?>