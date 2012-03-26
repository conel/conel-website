<?php
	session_start();
	
	// While developing, show errors
	//ini_set('display_errors',1);
	//error_reporting(E_ALL);

	// First make sure this file hasn't been directly accessed and only run if coming from open day page
	if (isset($_POST['name']) && (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],'staff_bbq')) ) {
	
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
		
		// set up session data to show on receipt page
		$_SESSION['staff_bbq']['attending'] = $_POST['attending'];
		$_SESSION['staff_bbq']['require_transport'] = $_POST['require_transport'];
		$_SESSION['staff_bbq']['name'] = $_POST['name'];
		$_SESSION['staff_bbq']['email'] = $_POST['email'];
		$_SESSION['staff_bbq']['extension'] = $_POST['extension'];
		
		// Set up POST variables to insert, escaping them for security
		$attending = mysql_real_escape_string($_POST['attending'], $sql->Link_ID);
		$require_transport = mysql_real_escape_string($_POST['require_transport'], $sql->Link_ID);
		$name = mysql_real_escape_string($_POST['name'], $sql->Link_ID);
		$email = mysql_real_escape_string($_POST['email'], $sql->Link_ID);
		$extension = mysql_real_escape_string($_POST['extension'], $sql->Link_ID);

		// Build INSERT query
		$query = "INSERT INTO tbl_staff_bbq (attending, require_transport, name, email, extension, datetime_submitted) 
		VALUES('$attending','$require_transport','$name','$email','$extension','$date_now_mysql')";
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
				header('Location: http://www.conel.ac.uk/email_successfully_sent');
				exit;
			} else {
				echo '<p>Email failed</p>';
				header('Location: http://www.conel.ac.uk/news_events/events_calendar/february_2010/open_days_2010?email=failed');
				exit;
			}
			*/

			header('Location: http://www.conel.ac.uk/staff_bbq_registered');
			exit;

			
		} else {
			echo '<p>Failed to add info to database</p>';
			header('Location: http://www.conel.ac.uk/staff_bbq?registration=failed');
			exit;
		}
		
	} else {
		header('Location: http://www.conel.ac.uk');
		exit;
	}
?>