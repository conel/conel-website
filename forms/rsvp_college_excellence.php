<?php
	session_start();
	
	// While developing, show errors
	//ini_set('display_errors',1);
	//error_reporting(E_ALL);

	// First make sure this file hasn't been directly accessed and only run if coming from open day page
	if (isset($_POST['firstname']) && (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],'/for_learners/college_excellence_awards')) ) {
	
		// This variable holds whether database input is successful
		$added = FALSE;
		
		// This variable sets whether the SQL functions are in debug mode or not - 
		$debug = 1; // 0 = Don't Debug, 1 = Debug
		
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
		$firstname = mysql_real_escape_string($_POST['firstname'], $sql->Link_ID);
		$surname = mysql_real_escape_string($_POST['surname'], $sql->Link_ID);
		$ce_awards_2011_winner = mysql_real_escape_string($_POST['ce_awards_2011_winner'], $sql->Link_ID);
		$guest1_name = mysql_real_escape_string($_POST['guest1_name'], $sql->Link_ID);
		$guest1_contact = mysql_real_escape_string($_POST['guest1_contact'], $sql->Link_ID);
		$guest2_name = mysql_real_escape_string($_POST['guest2_name'], $sql->Link_ID);
		$guest2_contact = mysql_real_escape_string($_POST['guest2_contact'], $sql->Link_ID);
		$job_title = mysql_real_escape_string($_POST['job_title'], $sql->Link_ID);
		$organisation = mysql_real_escape_string($_POST['organisation'], $sql->Link_ID);
		$email = mysql_real_escape_string($_POST['email'], $sql->Link_ID);
		$telephone_landline = mysql_real_escape_string($_POST['telephone_landline'], $sql->Link_ID);
		$telephone_mobile = mysql_real_escape_string($_POST['telephone_mobile'], $sql->Link_ID);
		$address_line_1 = mysql_real_escape_string($_POST['address_line_1'], $sql->Link_ID);
		$address_line_2 = mysql_real_escape_string($_POST['address_line_2'], $sql->Link_ID);
		$address_line_3 = mysql_real_escape_string($_POST['address_line_3'], $sql->Link_ID);
		$postcode = mysql_real_escape_string($_POST['postcode'], $sql->Link_ID);
		$borough_county = mysql_real_escape_string($_POST['borough_county'], $sql->Link_ID);
		$receive_communications = mysql_real_escape_string($_POST['receive_communications'], $sql->Link_ID);
		$receive_communications = ($receive_communications == 'No') ? 'No' : 'Yes';

		// Build INSERT query
		$query = "INSERT INTO tbl_rsvp_excellence_awards_2011   
		(firstname, surname, ce_awards_2011_winner, guest1_name, guest1_contact, guest2_name, guest2_contact, job_title, organisation, email, telephone_landline, telephone_mobile, address_line_1, address_line_2, address_line_3, postcode, borough_county, receive_communications, datetime_submitted) 
		VALUES('$firstname', '$surname', '$ce_awards_2011_winner', '$guest1_name', '$guest1_contact', '$guest2_name', '$guest2_contact', '$job_title', '$organisation', '$email', '$telephone_landline', '$telephone_mobile', '$address_line_1', '$address_line_2', '$address_line_3', '$postcode', '$borough_county', '$receive_communications', '$date_now_mysql')";
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
			
			$body_html = '
			<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
			<html>
			<head><title>Event Stewarding Registration</title>
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
			$body_html .= '<p>You have received a new \'Event Stewarding Course\' form submission.<br /><br />Submitted: '.$date_now.'</p>';
			$body_html .= '<table border="1" cellpadding="2" cellspacing="0">';
			
			foreach($_POST as $post_var => $value) {
				if ($post_var != 'email_confirm') {
					$fieldname = ucfirst(str_replace('_',' ',$post_var));	
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
			// nkowald - 2010-10-07 - Changed recipient as requested by Rosie Gilleece - 
			$mail->AddAddress('JStewart@staff.conel.ac.uk', 'Janet Stewart');
			//$mail->AddAddress('PWhelpton@staff.conel.ac.uk', 'Peter Whelpton');
			//$mail->AddBCC('NKowald@staff.conel.ac.uk', 'Nathan Kowald');
			$mail->Subject = "Event Stewarding form submission";
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
			header('Location: http://www.conel.ac.uk/for_learners/college_excellence_awards?registration=failed');
			exit;
		}
		
	} else {
		header('Location: http://www.conel.ac.uk/');
		exit;
	}
?>