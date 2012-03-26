<?php
	// While developing, show errors
	//ini_set('display_errors',1);
	//error_reporting(E_ALL);

	// First make sure this file hasn't been directly accessed and only run if coming from open day page
	if (isset($_POST['name']) && (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],'taster_days')) ) {
	
		// This variable holds whether database input is successful
		$added = FALSE;
		
		// This variable sets whether the SQL functions are in debug mode or not - 
		$debug = 0; // 0 = Don't Debug, 1 = Debug
		
		// Required includes
		include_once('../matrix_engine/config.php');
		include_once('../matrix_engine/'.CLASSES_DIR.'db_mysql.php');
		include_once('../matrix_engine/'.CLASSES_DIR.'class_mailer.php');
		
		/* This script inserts submitted 'Register your interest now' form data and into a database (tbl_open_day) */
		
		// what is the current date and time?
		$date_now_mysql = date('Y-m-d H:i:s'); // "2009-06-21 14:34:04": MySQL timestamp format
		
		// We need to instantiate the SQL class
		$sql = new DB_Sql();
		$sql->connect();
		
		// Set up POST variables to insert, escaping them for security
		$interested_in_csv = (is_array($_POST['interested_in'])) ? implode(', ',$_POST['interested_in']) : $_POST['interested_in'];
		$interested_in = mysql_real_escape_string($interested_in_csv, $sql->Link_ID);
		$name = mysql_real_escape_string($_POST['name'], $sql->Link_ID);
		$email = mysql_real_escape_string($_POST['email'], $sql->Link_ID);
		$telephone = mysql_real_escape_string($_POST['telephone'], $sql->Link_ID);
		$address = mysql_real_escape_string($_POST['address'], $sql->Link_ID);
		$postcode = mysql_real_escape_string($_POST['postcode'], $sql->Link_ID);
		$what_school_do_you_attend = mysql_real_escape_string($_POST['what_school_do_you_attend'], $sql->Link_ID);
		$which_borough = mysql_real_escape_string($_POST['which_borough_do_you_live_in'], $sql->Link_ID);
		$how_heard = mysql_real_escape_string($_POST['how_did_you_hear_about_us'], $sql->Link_ID);
		
		// Strip HTML from textarea
		$how_heard = strip_tags($how_heard);

		// Build INSERT query
		$query = "INSERT INTO tbl_taster_day (interested_in,name,email,telephone,address,postcode,what_school_do_you_attend,which_borough_do_you_live_in,how_did_you_hear_about_us,datetime_submitted) 
		VALUES('$interested_in','$name','$email','$telephone','$address','$postcode','$what_school_do_you_attend','$which_borough','$how_heard','$date_now_mysql')";
		$sql->query($query,$debug);

		if ($sql->num_rows_affected() > 0) {
			$added = TRUE;
		} else {
			$added = FALSE;
		}
		
		if ($added) {
			
			/*
			$date_now = date('d/m/Y, H:i:s');
			
			// Create email
			$body_html = '<p>You have received a new \'Taster Days\' form submission.<br /><br />Submitted: '.$date_now.'</p>';
			$body_html .= '<table border="1" cellpadding="2" cellspacing="0">';
			
			foreach($_POST as $post_var => $value) {
				if ($post_var != 'email_confirm') {
					$fieldname = ucfirst(str_replace('_',' ',$post_var));	
					$body_html .= "<tr><td valign=\"top\"><strong>$fieldname:</strong></td><td valign=\"top\"> $value</td></tr>";
				}
			}
			$body_html .= '</table>';
		
			// If database insert successful, send email:
			$mail = new phpmailer();
			$mail->IsHTML(TRUE); // send HTML email
			$mail->IsSMTP(); // use SMTP to send
			// Set Recipient
			$mail->AddAddress('NKowald@staff.conel.ac.uk','Taster Day Notifications');
			$mail->Subject = "Taster Days - Register your interest form submission";
			$mail->From = 'webmaster@staff.conel.ac.uk';
			$mail->FromName = 'Conel Website Notifications';
			$mail->Body = $body_html;
			//$mail->SMTPDebug = TRUE;

			$result = $mail->Send(); // send email notification!

			if ($result) {
				header('Location: http://conel-dev/email_successfully_sent');
				exit;
			} else {
				echo '<p>Email failed</p>';
				header('Location: http://conel-dev/news_events/events_calendar/february_2010/open_days_2010?email=failed');
				exit;
			}
			*/

			header('Location: http://www.conel.ac.uk/registration_successful');
			exit;

			
		} else {
			echo '<p>Failed to add info to database</p>';
			header('Location: http://www.conel.ac.uk/our_courses/taster_days?registration=failed');
			exit;
		}
		
	} else {
		header('Location: http://www.conel.ac.uk');
		exit;
	}
?>