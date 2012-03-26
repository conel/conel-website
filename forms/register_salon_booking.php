<?php
	
	// While developing, show errors
	//ini_set('display_errors',1);
	//error_reporting(E_ALL);

	$referer = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';

	// First make sure this file hasn't been directly accessed and only run if coming from open day page
	if (isset($_POST['name']) && (strpos($referer,'our_college/hair_and_beauty_salons')) ) {
	
		// This variable sets whether the SQL functions are in debug mode or not
		$debug = 0; // 0 = Don't Debug, 1 = Debug
		
		// Required includes
		include_once('../matrix_engine/config.php');
		include_once('../matrix_engine/'.CLASSES_DIR.'db_mysql.php');
		include_once('../matrix_engine/'.CLASSES_DIR.'class_mailer.php');
		
		// what is the current date and time?
		$date_now_mysql = date('Y-m-d H:i:s'); // "2009-06-21 14:34:04": MySQL timestamp format
		
		// We need to instantiate the SQL class
		$sql = new DB_Sql();
		$sql->connect();
		
		// Set up POST variables to insert, escaping them for security
		$name			= mysql_real_escape_string($_POST['name'], $sql->Link_ID);
		$contact_number = mysql_real_escape_string($_POST['contact_number'], $sql->Link_ID);
		$email			= mysql_real_escape_string($_POST['email'], $sql->Link_ID);
		$salon			= mysql_real_escape_string($_POST['salon'], $sql->Link_ID);
		$centre			= mysql_real_escape_string($_POST['centre'], $sql->Link_ID);
		$preferred_date_and_time	= mysql_real_escape_string($_POST['preferred_date_and_time'], $sql->Link_ID);
		$treatment		= mysql_real_escape_string($_POST['treatment'], $sql->Link_ID);

		$receive_communications = mysql_real_escape_string($_POST['receive_communications'], $sql->Link_ID);
		$receive_communications = ($receive_communications == 'No') ? 'No' : 'Yes';

		// Build INSERT query
		$query = "INSERT INTO tbl_salon_bookings 
		(
			name, 
			contact_number, 
			email, 
			salon, 
			centre, 
			preferred_date_and_time, 
			treatment, 
			receive_communications, 
			datetime_submitted

		) VALUES (

			'$name',
			'$contact_number',
			'$email',
			'$salon', 
			'$centre', 
			'$preferred_date_and_time', 
			'$treatment', 
			'$receive_communications', 
			'$date_now_mysql'
		)";

		// Execute query
		$sql->query($query, $debug);

		if ($sql->num_rows_affected() > 0) {
			

			$date_now = date('d/m/Y, H:i:s');
			
			// Create email
			
			$body_html = '
			<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
			<html>
			<head><title>Salon Booking</title>
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
			$body_html .= '<p>You have received a new Salon Booking.<br /><br />Submitted: '.$date_now.'</p>';
			$body_html .= '<table border="1" cellpadding="2" cellspacing="0">';
			
			foreach($_POST as $post_var => $value) {
				if ($post_var != 'email_confirm' && $post_var != 'receive_communications') {
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
			// nkowald - 2012-03-07 - Change recipient based on which salon this is
			if ($salon == 'Style Zone' && $centre == 'Enfield') {
				$mail->AddAddress('hairandbeauty.enfield@conel.ac.uk', 'Enfield Style Zone');
			} else {
				$mail->AddAddress('hairandbeauty.tott@conel.ac.uk', 'Hair and Beauty Tottenham');
			}
			$mail->Subject = "Salon booking";
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
				header('Location: '.$referer.'?email=failed');
				exit;
			}

			header('Location: http://www.conel.ac.uk/our_college/hair_and_beauty_salons/thank_you_for_your_booking');
			exit;

			
		} else {
			header('Location: '.$referer.'?registration=failed');
			exit;
		}
		
	} else {
		header('Location: http://www.conel.ac.uk');
		exit;
	}
?>
