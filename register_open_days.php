<?php
	// While developing, show errors
	//ini_set('display_errors',1);
	//error_reporting(E_ALL);

	// First make sure this file hasn't been directly accessed and only run if coming from open day page
	if (isset($_POST['firstname']) && (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],'open_days')) ) {
	
		// This variable holds whether database input is successful
		$added = FALSE;
		
		// This variable sets whether the SQL functions are in debug mode or not - 
		$debug = 0; // 0 = Don't Debug, 1 = Debug
		
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
		// If array: convert to CSV before inserting into the database
		if (!is_array($_POST['open_day_registering_for'])) {
			if (($_POST['open_day_registering_for'] != 'Enfield Centre - 03/11/2010') && ($_POST['open_day_registering_for'] != 'Tottenham Centre - 24/11/2010')) {
				// Someone's tampering with the form, don't import into the database
				header('Location: http://www.conel.ac.uk/news_events/events_calendar/november_2010/open_days?email=failed');
				exit;
			}
		}
		$open_day_registering_for = (is_array($_POST['open_day_registering_for'])) ? implode('; ', $_POST['open_day_registering_for']) : $_POST['open_day_registering_for'];
		$open_day_registering_for = mysql_real_escape_string($open_day_registering_for, $sql->Link_ID);
		$enfield_workshop_415pm_to_5pm = mysql_real_escape_string($_POST['enfield_workshop_415pm_to_5pm'], $sql->Link_ID);
		$enfield_workshop_515pm_to_6pm = mysql_real_escape_string($_POST['enfield_workshop_515pm_to_6pm'], $sql->Link_ID);
		$tottenham_workshop_415pm_to_5pm = mysql_real_escape_string($_POST['tottenham_workshop_415pm_to_5pm'], $sql->Link_ID);
		$tottenham_workshop_515pm_to_6pm = mysql_real_escape_string($_POST['tottenham_workshop_515pm_to_6pm'], $sql->Link_ID);	
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
		// Convert courses_of_interest to CSV values
		$courses_of_interest = (is_array($_POST['courses_of_interest'])) ? implode('; ', $_POST['courses_of_interest']) : $_POST['courses_of_interest'];
		$courses_of_interest = mysql_real_escape_string($courses_of_interest, $sql->Link_ID);
		$receive_communications = mysql_real_escape_string($_POST['receive_communications'], $sql->Link_ID);
		$receive_communications = ($receive_communications == 'No') ? 'No' : 'Yes';


		// Build INSERT query
		$query = "INSERT INTO tbl_open_day_nov_2010 (open_day_registering_for, enfield_workshop_415pm_to_5pm, enfield_workshop_515pm_to_6pm, tottenham_workshop_415pm_to_5pm, tottenham_workshop_515pm_to_6pm, firstname, surname, date_of_birth, email, telephone_landline, telephone_mobile, address_line_1, address_line_2, address_line_3, postcode, borough_county, what_secondary_school_did_you_attend, courses_of_interest, receive_communications, datetime_submitted) 
		VALUES('$open_day_registering_for', '$enfield_workshop_415pm_to_5pm', '$enfield_workshop_515pm_to_6pm', '$tottenham_workshop_415pm_to_5pm', '$tottenham_workshop_515pm_to_6pm', '$firstname','$surname', '$date_of_birth', '$email', '$telephone_landline', '$telephone_mobile', '$address_line_1', '$address_line_2', '$address_line_3', '$postcode', '$borough_county', '$what_secondary_school_did_you_attend', '$courses_of_interest', '$receive_communications', '$date_now_mysql')";
		$sql->query($query,$debug);

		if ($sql->num_rows_affected() > 0) {
			$added = TRUE;
		} else {
			$added = FALSE;
		}
		
		if ($added) {
			
			$date_now = date('d/m/Y, H:i:s');
			
			/* Create email */
			
			/*
			$body_html = '
			<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
			<html>
			<head><title>Course Application 2010-11</title>
			<style type="text/css">
			body, table td {
				font-family:Arial, Helvetica, sans-serif;
				font-size:13px; 
				line-height:1.3em;
			}
			table {
				width:100%;
			}
			table td {
				vertical-align:top;
				padding:3px;
			}
			</style>
			</head>
			<body>';
			$body_html .= '<p>You have received a new Open Days registration.<br /><br />Submitted: '.$date_now.'</p>';
			$body_html .= '<table border="1" cellpadding="2" cellspacing="0">';
			$dob_entered = false;
			
			foreach($_POST as $post_var => $value) {
				if (is_array($value)) {
					// convert value to CSV
					$fieldname = ucfirst(str_replace('_',' ',$post_var));
					$body_html .= "<tr><td valign=\"top\"><strong>$fieldname:</strong></td><td valign=\"top\"> ";
					$body_html .= "<ul>";
					foreach ($value as $val) {
						$body_html .= "<li>$val</li>";
					}
					$body_html .= "</ul>";
					$body_html .= "</td></tr>";
				} else if ($post_var == 'dob_day' || $post_var == 'dob_month' || $post_var == 'dob_year') {
					if ($dob_entered == false) {
						$dob = $_POST['dob_day'] . "/" . $_POST['dob_month'] . "/" . $_POST['dob_year'];
						$body_html .= "<tr><td valign=\"top\"><strong>Date of birth:</strong></td><td valign=\"top\"> $dob</td></tr>";
						$dob_entered = true;
					}
				} else if ($post_var != 'email_confirm') {
					$fieldname = ucfirst(str_replace('_',' ',$post_var));
					$value = ($value != '') ? $value : '&ndash;';
					$body_html .= "<tr><td valign=\"top\"><strong>$fieldname:</strong></td><td valign=\"top\"> $value</td></tr>";
				}
			}
			$body_html .= '</table>';
			$body_html .= '</body></html>';
		
			// If database insert successful, send email:
			$mail = new phpmailer();
			$mail->IsHTML(TRUE); // send HTML email
			$mail->IsSMTP(); // use SMTP to send
			// Set Recipient
			//$mail->AddAddress('NKowald@staff.conel.ac.uk','Open Day Notifications');
			$mail->AddAddress('admissions@staff.conel.ac.uk','Admissions');
			$mail->Subject = "Open Days Registration";
			$mail->From = 'webmaster@staff.conel.ac.uk';
			$mail->FromName = 'Conel Website Notifications';
			$mail->Body = $body_html;
			//$mail->SMTPDebug = TRUE;

			$result = $mail->Send(); // send email notification!
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
			header('Location: http://www.conel.ac.uk/news_events/events_calendar/november_2010/open_days?email=failed');
			exit;
		}
		
	} else {
		header('Location: http://www.conel.ac.uk');
		exit;
	}
?>