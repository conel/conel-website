<?php
	session_start();

	include_once('../matrix_engine/config.php');
	include_once('../matrix_engine/'.CLASSES_DIR.'db_mysql.php');
	include_once('../matrix_engine/'.CLASSES_DIR.'class_mailer.php');

	// instantiate the SQL classes
	$sql = new DB_Sql();
	$sql->connect();
	// used to display SQL errors
	$debug = 0; // 0 = Don't debug, 1 = debug
	
	include_once('caf_functions.php'); // functions used in form
	
	// Check whether user is signed in
	//$qry = "SELECT * FROM tbl_course_application WHERE page_step = 9 and form_completed = 0";
    $qry = "SELECT * FROM tbl_course_application WHERE datetime_submitted_first >= '2011-09-01 00:00:00' AND page_step = 9 AND form_completed = 0 ORDER BY email_address";
	$sql->query($qry, $debug);
	
	$csv_columns = array('learning_difficulty','disability', 'benefits_receiving', 'how_heard_about_course');
	
	$c = 0;
	while($sql->next_record()) {
		// Set up session values for all non-blank fields
		
		unset($_SESSION['caf']);
        $_SESSION['caf']['signed_in'] = TRUE;

		foreach ($sql->Record as $key => $value) {
			if (!is_numeric($key) && $value != '') {
				// if key is from specified csv columns (see above $csv_columns array) then make the session key an array of its values
				if (in_array($key, $csv_columns)) {
					$csv_to_array = explode('|', $value);
					$_SESSION['caf'][$key] = $csv_to_array;
				} else {
					$_SESSION['caf'][$key] = $value;
				}
			} else if ((!is_numeric($key) && $value != '') && $key == 'form_completed') {
				$completed = $value;
				if ($completed == 1) {
					$_SESSION['caf']['errors'][] = "You have already completed your course application form.";
					$_SESSION['caf']['step_complete'][0] = FALSE;
					$_SESSION['caf']['email_address'] = '';
					break;
				}
			}
		}
		
		// SESSION VALUES SHOULD BE SET NOW WE CAN SEND THE EMAIL
		$email_html = getVerifyInfo(TRUE);
		
		// SEND THE EMAIL
		//emailCompletedApplication($email_html);
        //markApplicationComplete();
		$c++;
	}
	echo '<p>'.$c.' emails sent!</p>';

?>
</div>
</body>
</html>
