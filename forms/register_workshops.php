<?php

	$referer = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';

	// First make sure this file hasn't been directly accessed and only run if coming from open day page
	if (isset($_POST['name']) && (strpos($referer,'for_schools/workshops')) ) {
	
		// This variable sets whether the SQL functions are in debug mode or not - 
		$debug = 0; // 0 = Don't Debug, 1 = Debug
		
		// Required includes
		include('../matrix_engine/config.php');
		include_once('../matrix_engine/'.CLASSES_DIR.'db_mysql.php');
		include_once('../matrix_engine/'.CLASSES_DIR.'class_mailer.php');
		
		// Instantiate the SQL class
		$sql = new DB_Sql();
		$sql->connect();
		
		// Set up POST variables to insert, escaping them for security
		$name = mysql_real_escape_string($_POST['name'], $sql->Link_ID);
		$school = mysql_real_escape_string($_POST['school'], $sql->Link_ID);
		$address_line_1	= mysql_real_escape_string($_POST['address_line_1'], $sql->Link_ID);
		$address_line_2	= mysql_real_escape_string($_POST['address_line_2'], $sql->Link_ID);
		$address_line_3	= mysql_real_escape_string($_POST['address_line_3'], $sql->Link_ID);
		$postcode = mysql_real_escape_string($_POST['postcode'], $sql->Link_ID);
		$borough_county	= mysql_real_escape_string($_POST['borough_county'], $sql->Link_ID);
		$email = mysql_real_escape_string($_POST['email'], $sql->Link_ID);
		$contact_numbers	= mysql_real_escape_string($_POST['contact_numbers'], $sql->Link_ID);
		$workshop = mysql_real_escape_string($_POST['workshop'], $sql->Link_ID);
		$prefered_place = mysql_real_escape_string($_POST['place'], $sql->Link_ID);
		$prefered_date_day = mysql_real_escape_string($_POST['prefered_date_day'], $sql->Link_ID);
		$prefered_date_month = mysql_real_escape_string($_POST['prefered_date_month'], $sql->Link_ID);
		$prefered_date_year = mysql_real_escape_string($_POST['prefered_date_year'], $sql->Link_ID);
		$prefered_date = $prefered_date_day . "/" . $prefered_date_month . "/" . $prefered_date_year;		
		$num_people_attending = mysql_real_escape_string($_POST['num_people_attending'], $sql->Link_ID);
		$how_heard = mysql_real_escape_string($_POST['how_heard'], $sql->Link_ID);
		
		// what is the current date and time? "2009-06-21 14:34:04": MySQL timestamp format
		$date_now_mysql = date('Y-m-d H:i:s');

		$query = "INSERT INTO tbl_workshops   
		(name, school, address_line_1, address_line_2, address_line_3, postcode, borough_county, email, contact_numbers, prefered_place, prefered_date, num_people_attending, how_heard, datetime_submitted) 
		VALUES('$name', '$school', '$address_line_1', '$address_line_2', '$address_line_3', '$postcode', '$borough_county', '$email', '$contact_numbers', '$prefered_place', '$prefered_date', '$num_people_attending', '$how_heard', '$date_now_mysql')";

		$sql->query($query, $debug);
		
		// If INSERT successful: continue
		if ($sql->num_rows_affected() > 0) {
			
			$date_now = date('d/m/Y, H:i:s');
			
			// Create email
			
			$body_html = '
			<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
			<html>
			<head><title>Workshops Registration</title>
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
			$body_html .= '<p>You have received a new \'Workshops\' form submission.<br /><br />Submitted: '.$date_now.'</p>';
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
			$mail->AddAddress('schools@conel.ac.uk', 'Schools');
			$mail->Subject = "Workshop form submission";
			$mail->From = 'webmaster@conel.ac.uk';
			$mail->FromName = 'Conel Website Notifications';
			$mail->Body = $body_html;
			//$mail->SMTPDebug = TRUE;

			$result = $mail->Send(); // send email notification!
			
			
			if ($result) {
			    header('Location: http://www.conel.ac.uk/registration_successful');
				exit;
			} else {
				echo '<p>Email failed</p>';
				header('Location: '.$referer.'?email=failed');
				exit;
			}

			
		} else {
			header('Location: '.$referer.'?registration=failed');
			exit;
		}
		
	} else {
		header('Location: http://www.conel.ac.uk');
		exit;
	}
?>
