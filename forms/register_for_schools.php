<?php
	
	// While developing, show errors
	ini_set('display_errors',1);
	error_reporting(E_ALL);

	// First make sure this file hasn't been directly accessed and only run if coming from open day page
	if (isset($_POST['name']) && (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],'for_schools')) ) {
	
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
		$name = mysql_real_escape_string($_POST['name'], $sql->Link_ID);
		$job_title = mysql_real_escape_string($_POST['job_title'], $sql->Link_ID);
		$school = mysql_real_escape_string($_POST['school_represented'], $sql->Link_ID);
		$address_line_1 = mysql_real_escape_string($_POST['address_line_1'], $sql->Link_ID);
		$address_line_2 = mysql_real_escape_string($_POST['address_line_2'], $sql->Link_ID);
		$address_line_3 = mysql_real_escape_string($_POST['address_line_3'], $sql->Link_ID);
		// Spam capture method
		$address_line_4 = mysql_real_escape_string($_POST['address_line_4'], $sql->Link_ID);
		// If not yes, make it look like it's been submitted but don't email or save the results
		if ($address_line_4 != 'yes') {
			header('Location: http://www.conel.ac.uk/registration_successful');
			exit;
		}
		$postcode = mysql_real_escape_string($_POST['postcode'], $sql->Link_ID);
		$borough_county = mysql_real_escape_string($_POST['borough_county'], $sql->Link_ID);
		$email = mysql_real_escape_string($_POST['email'], $sql->Link_ID);
		$telephone_landline = mysql_real_escape_string($_POST['telephone_landline'], $sql->Link_ID);
		$telephone_mobile = mysql_real_escape_string($_POST['telephone_mobile'], $sql->Link_ID);
		$receive_communications = mysql_real_escape_string($_POST['receive_communications'], $sql->Link_ID);
		$receive_communications = ($receive_communications == 'No') ? 'No' : 'Yes';
		
		// Further Spam protection
		if (filter_var($address_line_1, FILTER_VALIDATE_URL) || filter_var($address_line_1, FILTER_VALIDATE_URL) || filter_var($address_line_1, FILTER_VALIDATE_URL)) {
			header('Location: http://www.conel.ac.uk/registration_successful');
			exit;
		}

		// Build INSERT query
		$query = "INSERT INTO tbl_for_schools 
		(name, job_title, school, address_line_1, address_line_2, address_line_3, postcode, borough_county, email, telephone_landline, telephone_mobile, receive_communications, datetime_submitted) 
		VALUES('$name', '$job_title', '$school', '$address_line_1', '$address_line_2', '$address_line_3', '$postcode', '$borough_county', '$email', '$telephone_landline', '$telephone_mobile', '$receive_communications', '$date_now_mysql')";
		$sql->query($query,$debug);

		if ($sql->num_rows_affected() > 0) {
			$added = TRUE;
		} else {
			$added = FALSE;
		}
		
		if ($added) {
			
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
			$body_html .= '<p>You have received a new \'For Schools\' form submission.<br /><br />Submitted: '.$date_now.'</p>';
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
			//$mail->AddAddress('marketing@conel.ac.uk', 'Marketing');
			$mail->AddAddress('schools@conel.ac.uk', 'Schools');
			$mail->Subject = "For Schools form submission";
			$mail->From = 'webmaster@staff.conel.ac.uk';
			$mail->FromName = 'Conel Website Notifications';
			$mail->Body = $body_html;
			//$mail->SMTPDebug = TRUE;

			$result = $mail->Send(); // send email notification!
			
			
			if ($result) {
				header('Location: http://www.conel.ac.uk/registration_successful');
				exit;
			} else {
				echo '<p>Email failed</p>';
				header('Location: http://www.conel.ac.uk/for_schools?registration=failed');
				exit;
			}

			header('Location: http://www.conel.ac.uk/registration_successful');
			exit;

			
		} else {
			echo '<p>Failed to add info to database</p>';
			header('Location: http://www.conel.ac.uk/for_schools?registration=failed');
			exit;
		}
		
	} else {
		header('Location: http://www.conel.ac.uk/');
		exit;
	}
?>