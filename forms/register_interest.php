<?php
	//While developing, show errors
	//ini_set('display_errors',1);
	//error_reporting(E_ALL);

	// First make sure this file hasn't been directly accessed and only run if coming from open day page
	if (isset($_POST['name']) && (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],'open_days_2010')) ) {
	
		// This variable holds whether database input is successful
		$added = FALSE;
		
		// This variable sets whether the SQL functions are in debug mode or not - 
		$debug = 0; // 0 = Don't Debug, 1 = Debug
		
		$send_email = FALSE;
		
		// Required includes
		include_once('matrix_engine/config.php');
		include_once('matrix_engine/'.CLASSES_DIR.'db_mysql.php');
		include_once('matrix_engine/'.CLASSES_DIR.'class_mailer.php');
		
		/* This script inserts submitted 'Register your interest now' form data and into a database (tbl_open_day) */
		
		// what is the current date and time?
		$date_now_mysql = date('Y-m-d H:i:s'); // "2009-06-21 14:34:04": MySQL timestamp format
		
		// We need to instantiate the SQL class
		$sql = new DB_Sql();
		$sql->connect();

		// Set up POST variables to insert, escaping them for security
		$name = mysql_real_escape_string($_POST['name'], $sql->Link_ID);
		$email = mysql_real_escape_string($_POST['email'], $sql->Link_ID);
		$confirm_email = mysql_real_escape_string($_POST['email_confirm'], $sql->Link_ID);
		$telephone = mysql_real_escape_string($_POST['telephone'], $sql->Link_ID);
		// nkowald - 2010-02-25 - Added Postal Address fields
		$address = mysql_real_escape_string($_POST['address'], $sql->Link_ID);
		$postcode = mysql_real_escape_string($_POST['postcode'], $sql->Link_ID);
		// nkowald
		$subject_of_interest = mysql_real_escape_string($_POST['subject_of_interest'], $sql->Link_ID);
		$age_group = mysql_real_escape_string($_POST['age_group'], $sql->Link_ID);
		$what_school_do_you_attend = mysql_real_escape_string($_POST['what_school_do_you_attend'], $sql->Link_ID);
		$which_borough = mysql_real_escape_string($_POST['which_borough_do_you_live_in'], $sql->Link_ID);
		$how_heard = mysql_real_escape_string($_POST['how_did_you_hear_about_open_day'], $sql->Link_ID);
		$open_day_choice = mysql_real_escape_string($_POST['open_day_choice'], $sql->Link_ID);
		// nkowald - 2010-02-10 - Added receive communications value
		$receive_communications = (isset($_POST['receive_communications'])) ? mysql_real_escape_string($_POST['receive_communications'], $sql->Link_ID) : 'y';
		
		// Strip HTML from textarea
		$how_heard = strip_tags($how_heard);

		// Build INSERT query
		$query = "INSERT INTO tbl_open_day (name, email, telephone, address, postcode, subject_of_interest, age_group, what_school_do_you_attend, which_borough_do_you_live_in, how_did_you_hear_about_open_day, open_day, receive_communications, datetime_submitted) 
		VALUES('$name', '$email', '$telephone', '$address', '$postcode', '$subject_of_interest', '$age_group', '$what_school_do_you_attend', '$which_borough', '$how_heard', '$open_day_choice', '$receive_communications', '$date_now_mysql')";
		$sql->query($query,$debug);

		if ($sql->num_rows_affected() > 0) {
			$added = TRUE;
		} else {
			$added = FALSE;
		}
		
		if ($added) {
			
			if ($send_email) {
			
				$date_now = date('d/m/Y, H:i:s');
				
				/* Create email */
				$body_html = '<p>You have received a new \'Open Day - Register Your Interest\' form submission.<br /><br />Submitted: '.$date_now.'</p>';
				$body_html .= '<table border="1" cellpadding="2" cellspacing="0">';
				
				foreach($_POST as $post_var => $value) {
					if ($post_var != 'email_confirm') {
						$fieldname = ucfirst(str_replace('_',' ',$post_var));	
						$body_html .= "<tr><td valign=\"top\"><strong>$fieldname:</strong></td><td valign=\"top\">$value&nbsp;</td></tr>";
					}
				}
				$body_html .= '</table>';
			
				// If database insert successful, send email:
				$mail = new phpmailer();
				$mail->IsHTML(TRUE); // send HTML email
				$mail->IsSMTP(); // use SMTP to send
				// Set Recipient
				$mail->AddAddress('NKowald@staff.conel.ac.uk','Open Day Notifications');
				//$mail->AddBCC('nkowald@staff.conel.ac.uk','Nathan Kowald');
				$mail->Subject = "Open Day - Register your interest form submission";
				$mail->From = 'webmaster@staff.conel.ac.uk';
				$mail->FromName = 'Conel Website Notifications';
				$mail->Body = $body_html;
				//$mail->SMTPDebug = TRUE;

				$result = $mail->Send(); // send email notification!
			
			}
			
			/*
			if ($result) {
				header('Location: http://www.conel.ac.uk/email_successfully_sent');
				exit;
			} else {
				echo '<p>Email failed</p>';
				header('Location: http://www.conel.ac.uk/news_events/event_calendar/november_2009/7_november_2009_open_day?email=failed');
				exit;
			}
			*/

			header('Location: http://www.conel.ac.uk/registration_successful');
			exit;

			
		} else {
			echo '<p>Failed to add info to database</p>';
			header('Location: http://www.conel.ac.uk/news_events/events_calendar/february_2010/open_days_2010?email=failed');
			exit;
		}
		
	} else {
		header('Location: http://www.conel.ac.uk');
		exit;
	}
?>