<?php
	
	// While developing, show errors
	ini_set('display_errors',1);
	error_reporting(E_ALL);

	echo '<pre>';
	// Required includes
	include_once('../matrix_engine/config.php');
	include_once('../matrix_engine/'.CLASSES_DIR.'db_mysql.php');
	include_once('../matrix_engine/'.CLASSES_DIR.'class_mailer.php');
			
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
	$body_html .= '<p>You have received a new \'Test\' form submission.<br /><br />Submitted: '.$date_now.'</p>';
	$body_html .= '</body></html>';

	// If database insert successful, send email:
	$mail = new phpmailer();
	$mail->IsHTML(TRUE); // send HTML email
	$mail->IsSMTP(); // use SMTP to send
	// Set Recipient
	// nkowald - 2010-10-07 - Changed recipient as requested by Rosie Gilleece - 
	//$mail->AddAddress('nathankowald@gmail.com', 'Nathan Kowald');
	$mail->AddAddress('NKowald@conel.ac.uk', 'Nathan Kowald');
	//$mail->AddAddress('PWhelpton@staff.conel.ac.uk', 'Peter Whelpton');
	//$mail->AddBCC('NKowald@staff.conel.ac.uk', 'Nathan Kowald');
	$mail->Subject = "Test form submission";
	$mail->From = 'webmaster@staff.conel.ac.uk';
	$mail->FromName = 'Conel Website Notifications';
	$mail->Body = $body_html;
	$mail->SMTPDebug = TRUE;

	$result = $mail->Send(); // send email notification!
	
	
	if ($result) {
		echo '<p>Email sent!</p>';
		exit;
	} else {
		echo '<p>Email failed</p>';
		exit;
	}
	
	echo '</pre>';
			
?>