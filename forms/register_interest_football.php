<?php
	// While developing, show errors
	//ini_set('display_errors',1);
	//error_reporting(E_ALL);

	
	// First make sure this file hasn't been directly accessed and only run if coming from open day page
	if (isset($_POST['name']) && (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],'football_academy')) ) {
		
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
		$date_now_mysql = date('Y-m-d H:i:s'); // "2009-06-21 14:34:04": MySQL 'DATETIME' format
		
		// We need to instantiate the SQL class
		$sql = new DB_Sql();
		$sql->connect();

		// Set up POST variables to insert, escaping them for security
		$name = mysql_real_escape_string($_POST['name'], $sql->Link_ID);
		$address_line_1 = mysql_real_escape_string($_POST['address_line_1'], $sql->Link_ID);
		$address_line_2 = mysql_real_escape_string($_POST['address_line_2'], $sql->Link_ID);
		$address_line_3 = mysql_real_escape_string($_POST['address_line_3'], $sql->Link_ID);
		$postcode = mysql_real_escape_string($_POST['postcode'], $sql->Link_ID);
		$telephone_landline = mysql_real_escape_string($_POST['telephone_landline'], $sql->Link_ID);
		$telephone_mobile = mysql_real_escape_string($_POST['telephone_mobile'], $sql->Link_ID);
		$email_address = mysql_real_escape_string($_POST['email_address'], $sql->Link_ID);
		$age_at_31_august = mysql_real_escape_string($_POST['age_at_31_august'], $sql->Link_ID);


		// Build INSERT query
		$query = "INSERT INTO tbl_football_academy (name, address_line_1, address_line_2, address_line_3, postcode, telephone_landline, telephone_mobile, email_address, age_at_31_august, datetime_submitted) 
		VALUES('$name', '$address_line_1','$address_line_2','$address_line_3','$postcode','$telephone_landline', '$telephone_mobile','$email_address','$age_at_31_august','$date_now_mysql')";
		$sql->query($query,$debug);

		if ($sql->num_rows_affected() > 0) {
			$added = TRUE;
		} else {
			$added = FALSE;
		}
		
		if ($added) {
			
			/*
			if ($send_email) {
			
				$date_now = date('d/m/Y, H:i:s');

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
				$mail->AddAddress('nkowald@conel.ac.uk', 'Nathan Kowald');
				//$mail->AddBCC('nkowald@staff.conel.ac.uk','Nathan Kowald');
				$mail->Subject = "Football Academy - Register your interest form submission";
				$mail->From = 'webmaster@staff.conel.ac.uk';
				$mail->FromName = 'Conel Website Notifications';
				$mail->Body = $body_html;
				//$mail->SMTPDebug = TRUE;

				$result = $mail->Send(); // send email notification!
			
			}
			*/
			
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
			header('Location: http://www.conel.ac.uk/for_learners/football_academy?email=failed');
			exit;
		}
		
	} else {
		header('Location: http://www.conel.ac.uk');
		exit;
	}
?>