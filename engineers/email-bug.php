<?php
	session_start();
	
	$sess_lifetime = "7200";
	ini_set(session.gc_maxlifetime, $sess_lifetime);
	
	if (!isset($_SESSION['ws']['logged_in'])) {
		$_SESSION['ws']['logged_in'] = FALSE;
	}
	if ($_SESSION['ws']['logged_in'] === FALSE) {
		header('Location: index.php');
		exit;
	}
	
	include_once('../matrix_engine/config.php');
	include_once('../matrix_engine/'.CLASSES_DIR.'class_mailer.php');

	// Message
	$message = (isset($_POST['bug_message']) && $_POST['bug_message'] != '') ? $_POST['bug_message'] : '';
	$messager = (isset($_POST['bug_engineer']) && $_POST['bug_engineer'] != '') ? $_POST['bug_engineer'] : '';

	// Check referer is index.php
	$referer = $_SERVER['HTTP_REFERER'];

	//echo '<a href="'.$referer.'">Back to Referer</a>';

	// Create email
	$body_html = '
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
	<html>
	<head><title>Bug Report</title>
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
	$date_now = date('d/m/Y, H:i:s');
	$body_html .= '<p>You have received a bug report from '.$messager.'.<br /></p>';
	$body_html .= "<p><strong>Engineer:</strong> $messager</p>";
	$body_html .= "<p>$message</p>";
	$body_html .= "<p>Submitted: $date_now</p>";
	$body_html .= '</body></html>';

	// If database insert successful, send email:
	$mail = new phpmailer();
	$mail->IsHTML(TRUE); // send HTML email
	$mail->IsSMTP(); // use SMTP to send
	$mail->AddAddress('NKowald@staff.conel.ac.uk', 'Nathan Kowald');
	$mail->Subject = "Bug Report - Engineer Worksheets";
	$mail->From = 'webmaster@staff.conel.ac.uk';
	$mail->FromName = 'Conel Website';
	$mail->Body = $body_html;
	//$mail->SMTPDebug = TRUE;

	$result = $mail->Send(); // send email notification!
	
	if ($result) {
		//echo 'Bug report sent successfully!';
		$referer .= "&emailed=2";
		header('Location: '.$referer.'');
		exit;
	} else {
		echo 'Bug report failed to send!';
		exit;
	}
?>