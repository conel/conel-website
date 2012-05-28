<?php
	session_start();
	if (!isset($_SESSION['ca']['logged_in'])) {
		$_SESSION['ca']['logged_in'] = FALSE;
	}

	include_once('../matrix_engine/config.php');
	include_once('../matrix_engine/'.CLASSES_DIR.'db_mysql.php');
	include_once('../matrix_engine/'.CLASSES_DIR.'class_mailer.php');
	include_once('caf_functions.php'); // functions used in form

	// instantiate the SQL classes
	$sql = new DB_Sql();
	$sql->connect();
	$debug = 1; // 0 = Don't debug, 1 = debug
	
	$id = (isset($_GET['id']) && $_GET['id'] != '') ? $_GET['id'] : '';

	$_SESSION['ca']['errors'] = array();
	
	if (isset($_GET['logout']) && $_GET['logout'] == 1) {
		$_SESSION['ca']['logged_in'] = FALSE;
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-AU" xml:lang="en-AU">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex" />
<meta name="googlebot" content="noindex" />
<title>Reference ID Search - Online Course Applications</title>
<link href="css/application.css" rel="stylesheet" type="text/css" media="all" />
<link href="css/application_print.css" rel="stylesheet" type="text/css" media="print" />
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/fl_functions.js"></script>
</head>

<body>

<div id="holder">
	<img src="../layout/img/banner_new.gif" width="955" height="84" alt="The College of Haringey, Enfield and North East London" id="banner" />
	<h1 class="hide_print">View Application</h1>
	
<?php
	if ($_SESSION['ca']['logged_in'] == FALSE) {
?>
	<!-- Login -->
	<div class="section">
		<h2>Login</h2>
		<form method="post" action="forgotten.php">
			<table id="admissions_login">
				<tr><td><strong>Username:</strong></td><td><input type="text" name="username" class="text" /></td></tr>
				<tr><td><strong>Password:</strong></td><td><input type="password" name="password" class="text" /></td></tr>
				<tr><td>&nbsp;</td><td><input type="submit" value="Login &gt;" class="submit" /></td></tr>
			</table>
		</form>
	<!-- //Login -->
	</div>
<?php

	}
?>
	
<?php
	if ($_SESSION['ca']['logged_in'] == TRUE) {
?>
	<div class="section view_app">
<?php
	if ($id != '' && is_numeric($id)) {
		$qry = "SELECT * FROM tbl_course_application WHERE id = $id";
		$sql->query($qry, $debug);
		
		$csv_columns = array('learning_difficulty','disability', 'benefits_receiving', 'how_heard_about_course');
		$preview_html = '';
		while($sql->next_record()) {
			// Set up session values for all non-blank fields
			
			unset($_SESSION['caf']);
			$firstname = '';
			$surname = '';
			$datetime_submitted_first = '';
			$datetime_submitted_last = '';
			
			foreach ($sql->Record as $key => $value) {
				if ($key == 'firstname') {
					$firstname = $value;
				}
				if ($key == 'surname') {
					$lastname = $value;
				}
				if ($key == 'datetime_submitted_first') {
					$datetime_submitted_first = $value;
				}
				if ($key == 'datetime_submitted_last') {
					$datetime_submitted_last = $value;
				}
				
				if (!is_numeric($key) && $value != '') {
					// if key is from specified csv columns (see above $csv_columns array) then make the session key an array of its values
					if (in_array($key, $csv_columns)) {
						$csv_to_array = explode('|', $value);
						$_SESSION['caf'][$key] = $csv_to_array;
					} else {
						$_SESSION['caf'][$key] = $value;
					}
				} else if ((!is_numeric($key) && $value != '') && $key == 'form_completed') {
					$completed = $value;
					if ($completed == 1) {
						$_SESSION['caf']['errors'][] = "You have already completed your course application form.";
						$_SESSION['caf']['step_complete'][0] = FALSE;
						$_SESSION['caf']['email_address'] = '';
						break;
					}
				}
			}
			
			// SESSION VALUES SHOULD BE SET NOW WE CAN SEND THE EMAIL
			$preview_html = getVerifyInfo(TRUE);
		}
		unset($_SESSION['caf']);
		
		echo '<div class="hide_print">';
		echo '<p style="font-size:1.1em;"><strong><a href="forgotten.php?show=1">&lt; Back to Applications</a></strong></p>';
		echo '<div id="logout"><a href="view-application.php?logout=1">Log out</a></div>';
		echo '</div>';
		echo "<br /><h2>$firstname $lastname</h2>";
		echo "<p><strong>Started:</strong> $datetime_submitted_first</p>";
		if ($datetime_submitted_last != '') {
			echo "<p><strong>Submitted/Last Saved:</strong> $datetime_submitted_last</p>";
		}
		echo '<br />';
		echo $preview_html;

	} else {
		$_SESSION['ca']['errors'][] = 'No user id given';
	}
	
	if (isset($_SESSION['ca']['errors']) && count($_SESSION['ca']['errors']) > 0) {
		echo '<div class="error">';
		echo '<h2>Errors</h2>';
		echo '<ul>';
		foreach ($_SESSION['ca']['errors'] as $error) {
			echo "<li>$error</li>";
		}
		echo '</ul>';
		echo '</div>';
	}
	
	
	}

	
?>
</div>
</div>
</body>
</html>
