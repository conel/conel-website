<?php
	session_start();
	// Session security
	if (!isset($_SESSION['caf']['initiated'])) {
		session_regenerate_id();
		$_SESSION['caf']['initiated'] = true;
	}


	include_once('../matrix_engine/config.php');
	include_once('../matrix_engine/'.CLASSES_DIR.'db_mysql.php');
	include_once('../matrix_engine/'.CLASSES_DIR.'class_mailer.php');

	// instantiate the SQL classes
	$sql = new DB_Sql();
	$sql->connect();
	// used to display SQL errors
	$debug = 0; // 0 = Don't debug, 1 = debug
	
	include_once('caf_functions.php'); // functions used in form
	
	// constant to hold this page url
	$page_url = "";
	define('THIS_URL', SITE_ROOT . 'course-application/index.php');

	$step = (isset($_GET['step'])) ? $_GET['step'] : 0;
	$resume_url = '<a href="'.THIS_URL.'">'.SITE_ROOT.'/course-application/</a>';
	
	// This session array holds steps "complete" status
	if(!isset($_SESSION['caf']['step_complete'])) {
		$_SESSION['caf']['step_complete'] = array(
			0 => FALSE, 
			1 => FALSE, 
			2 => FALSE, 
			3 => FALSE, 
			4 => FALSE, 
			5 => FALSE,
			6 => FALSE, 
			7 => FALSE,
			8 => FALSE,
			9 => FALSE 
		);
	}
	
	// initialise session values - if no value exists
	if (!isset($_SESSION['caf']['id'])) {
		$_SESSION['caf']['id'] = 0;
	}
	if (!isset($_SESSION['caf']['errors'])) {
		$_SESSION['caf']['errors'] = array();
	}
	if (!isset($_SESSION['caf']['page_step'])) {
		$_SESSION['caf']['page_step'] = 0;
	}
	
	// Check whether user is signed in
	$qry = sprintf("SELECT id FROM tbl_course_application 
		WHERE email_address = '%s' 
		AND reference_id = '%s'",
		mysql_real_escape_string($_SESSION['caf']['email_address'], $sql->Link_ID),
		mysql_real_escape_string($_SESSION['caf']['reference_id'], $sql->Link_ID)
	);

	$sql->query($qry, $debug);
	$_SESSION['caf']['signed_in'] = FALSE;
	if ($sql->num_rows() > 0) {
		$_SESSION['caf']['signed_in'] = TRUE;
	} else {
		$_SESSION['caf']['signed_in'] = FALSE;
	}

	// Check for required fields, redirect to previous page if blank
	// Server-side checking - if people turn JavaScript off
	if (!isset($_GET['error'])) {
		switch($step) {
		
			case 0:
				if ($_SESSION['caf']['signed_in'] === true) { 
					securityStepCheck($step);
				}
				break;
			
			case 1:
				if ($_SESSION['caf']['signed_in'] === true) {
					securityStepCheck($step);
				}
				break;
			
			case 2:
				$continue = TRUE;
				$required_fields = array('course_title_1', 'college_centre_1', 'course_entry_date_1');
				$_SESSION['caf']['missing_fields'] = '';
				foreach ($required_fields as $field) {
					if (isset($_POST[$field]) && $_POST[$field] == '') {
						$_SESSION['caf']['missing_fields'][] = $field; 
						$continue = FALSE;
					}
				}
				if (!$continue) {
					$_SESSION['caf']['errors'][] = "Required fields missing";
					$_SESSION['caf']['page_step'] = $step - 1;
					header('location:'.THIS_URL.'?step='.$_SESSION['caf']['page_step'].'&error=1');
					exit;
				} else {
					if (isset($_POST) && count($_POST) > 0) {
						$_SESSION['caf']['step_complete'][1] = TRUE;
					}
					securityStepCheck($step);
				}
				break;
			
			case 3:
				$continue = TRUE;
				$required_fields = array('firstname', 'surname', 'gender', 'date_of_birth', 'home_address', 'postcode', 'ethnic_group', 'why_want_to_do_course');
				$_SESSION['caf']['missing_fields'] = '';
				foreach ($required_fields as $field) {
					if (isset($_POST[$field]) && $_POST[$field] == '') {
						$_SESSION['caf']['missing_fields'][] = $field; $continue = FALSE;
					}
				}
				if (!$continue) {
					$_SESSION['caf']['errors'][] = "Required fields missing";
					$_SESSION['caf']['page_step'] = $step - 1;
					header('location:'.THIS_URL.'?step='.$_SESSION['caf']['page_step'].'&error=1');
					exit;
				} else {
					if (isset($_POST) && count($_POST) > 0) {
						$_SESSION['caf']['step_complete'][2] = TRUE;
					}
					securityStepCheck($step);
				}
				break;
			
			case 4:
				$continue = TRUE;
				$required_fields = array('do_you_have_a_learning_difficulty_or_disability');
				$_SESSION['caf']['missing_fields'] = '';
				foreach ($required_fields as $field) {
					if (isset($_POST[$field]) && $_POST[$field] == '') {$_SESSION['caf']['missing_fields'][] = $field; $continue = FALSE;}
				}
				if (!$continue) {
					$_SESSION['caf']['errors'][] = "Required fields missing";
					$_SESSION['caf']['page_step'] = $step - 1;
					header('location:'.THIS_URL.'?step='.$_SESSION['caf']['page_step'].'&error=1');
					exit;
				} else {
					if (isset($_POST) && count($_POST) > 0) {
						$_SESSION['caf']['step_complete'][3] = TRUE;
					}
					securityStepCheck($step);
				}
				break;
			
			case 5:
				if (isset($_POST) && count($_POST) > 0) {
					$_SESSION['caf']['step_complete'][4] = TRUE;
				}
				securityStepCheck($step);
				break;
			
			case 6:
				$continue = TRUE;
				$required_fields = array('are_you_employed','are_you_working_as_a_volunteer','relevant_skills_and_experience');
				$_SESSION['caf']['missing_fields'] = '';
				foreach ($required_fields as $field) {
					if (isset($_POST[$field]) && $_POST[$field] == '') {$continue = FALSE; $_SESSION['caf']['missing_fields'][] = $field;}
				}
				if (!$continue) {
					$_SESSION['caf']['errors'][] = "Required fields missing";
					$_SESSION['caf']['page_step'] = $step - 1;
					header('location:'.THIS_URL.'?step='.$_SESSION['caf']['page_step'].'&error=1');
					exit;
				} else {
					if (isset($_POST) && count($_POST) > 0) {
						$_SESSION['caf']['step_complete'][5] = TRUE;
					}
					securityStepCheck($step);
				}
				break;
			
			case 7:
				$continue = TRUE;
				$required_fields = array('nationality','permanent_right_to_live_in_uk','are_you_an_international_student');
				$_SESSION['caf']['missing_fields'] = '';
				foreach ($required_fields as $field) {
					if (isset($_POST[$field]) && $_POST[$field] == '') {
						$continue = FALSE; 
						$_SESSION['caf']['missing_fields'][] = $field;
					}
				}
				if (!$continue) {
					$_SESSION['caf']['errors'][] = "Required fields missing";
					$_SESSION['caf']['page_step'] = $step - 1;
					header('location:'.THIS_URL.'?step='.$_SESSION['caf']['page_step'].'&error=1');
					exit;
				} else {
					if (isset($_POST) && count($_POST) > 0) {
						$_SESSION['caf']['step_complete'][6] = TRUE;
					}
					securityStepCheck($step);
				}
				break;
			
			case 8:
				$continue = TRUE;
				$required_fields = array('how_heard_about_course', 'correct_info_confirm');
				$_SESSION['caf']['missing_fields'] = '';
				foreach ($required_fields as $field) {
					if (isset($_POST[$field]) && $_POST[$field] == '') {$continue = FALSE; $_SESSION['caf']['missing_fields'][] = $field;}
				}
				if (!$continue) {
					$_SESSION['caf']['errors'][] = "Required fields missing";
					$_SESSION['caf']['page_step'] = $step - 1;
					header('location:'.THIS_URL.'?step='.$_SESSION['caf']['page_step'].'&error=1');
					exit;
				} else {
					if (isset($_POST) && count($_POST) > 0) {
						$_SESSION['caf']['step_complete'][7] = TRUE;
						$_SESSION['caf']['step_complete'][8] = TRUE;
					}
					securityStepCheck($step);
				}
				break;
			
			case 9:
				$continue = TRUE;
				$required_fields = array('interview_time', 'interview_location');
				$_SESSION['caf']['missing_fields'] = '';
				foreach ($required_fields as $field) {
					if (isset($_POST[$field]) && $_POST[$field] == '') {$continue = FALSE; $_SESSION['caf']['missing_fields'][] = $field;}
				}
				if (!$continue) {
					$_SESSION['caf']['errors'][] = "Required fields missing";
					$_SESSION['caf']['page_step'] = $step - 1;
					header('location:'.THIS_URL.'?step='.$_SESSION['caf']['page_step'].'&error=1');
					exit;
				} else {
					if ($_SESSION['caf']['step_complete'][8] === true) {
						$_SESSION['caf']['step_complete'][9] = TRUE;
					}
					securityStepCheck($step);
				}
				break;
				
			case 10:
				if (markApplicationComplete()) {
					$_SESSION['caf']['step_complete'][9] = TRUE;
				}
				securityStepCheck($step);
				break;
			
			default:
				if ($step != 0) {
					securityStepCheck($step);
				}
		}
	}
	
	// Array to hold fields that don't exist in the database;
	$not_columns = array('security_code');
	// Array to hold values to be capitalised
	$ucwords_keys = array('firstname', 'surname', 'home_address', 'language_spoken_at_home', 'job_title', 'employer', 'job_address', 'nationality', 'country_of_usual_reference');
	// Array to hold values to be uppercased
	$strtoupper_keys = array('postcode', 'job_postcode');
	
	$sql_query = "";
	
	if ($step > 0 && $step != 10) {
		if (isset($_POST) && $_SESSION['caf']['signed_in'] === true) {
			$sql_query = "UPDATE tbl_course_application SET ";
			foreach ($_POST as $key => $value) {
				if (!in_array($key, $not_columns)) {
					if ($key == 'age_at_31_aug_2010') {
						$age_at = getAge($_POST['date_of_birth']);
						$value = $age_at;
					}
					if ($value != '') {
						if (is_array($value)) {
							$col_value = implode('|',$value);
							$col_value_final = mysql_real_escape_string($col_value, $sql->Link_ID);
							$sql_query .= "$key = '$col_value_final', ";
						} else {
							// nkowald - 2010-06-14 - Added forced uppercase, capitalise values
							if (in_array($key, $ucwords_keys)) {
								$value = ucwords($value);
							}
							if (in_array($key, $strtoupper_keys)) {
								$value = strtoupper($value);
							}
							$col_value = trim(mysql_real_escape_string($value, $sql->Link_ID));
							$sql_query .= "$key = '$col_value', ";
						}
					}
				}
				// nkowald - 2010-06-14 - Added forced uppercase, capitalise values
				if (in_array($key, $ucwords_keys)) {
					$value = ucwords($value);
				}
				if (in_array($key, $strtoupper_keys)) {
					$value = strtoupper($value);
				}
				$_SESSION['caf'][$key] = $value;
			}
			
			$datetime_sub = date('Y-m-d H:i:s'); // 2010-06-02 14:24:04 - MySQL timestamp format
			$sql_query .= sprintf("datetime_submitted_last = '%s', page_step = '%d' ", 
				$datetime_sub,
				$_SESSION['caf']['page_step']
			);
			$sql_query .= sprintf(" WHERE id = %d", mysql_real_escape_string($_SESSION['caf']['id'], $sql->Link_ID));
			$_SESSION['caf']['datetime_submitted_last'] = $datetime_sub;

			if ($_SESSION['caf']['id'] != 0) {
				if (!$sql->query($sql_query, $debug)) {
					$_SESSION['caf']['errors'][] = "Error saving application - invalid query";
					header('location:'.THIS_URL.'?step='.$_SESSION['caf']['page_step'].'&error=2');
					exit;
				}
			}
		}
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-AU" xml:lang="en-AU">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex" />
<meta name="googlebot" content="noindex" />
<title>Course Application Form</title>
<link href="css/application_form.css" rel="stylesheet" type="text/css" media="all" />
<link href="css/application_form_print.css" rel="stylesheet" type="text/css" media="print" />
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/caf_functions.js"></script>
<link media="screen" rel="stylesheet" href="css/colorbox.css" />
<script type="text/javascript" src="js/jquery.colorbox-min.js"></script>
</head>

<body>

<div id="holder">
<img src="../layout/img/banner_new.gif" width="955" height="84" alt="The College of Haringey, Enfield and North East London" id="banner" />
<?php 
	if (isset($_SESSION['caf']['page_step']) && $_SESSION['caf']['page_step'] != 0) {

	// Get percentage from step
	$up_to = ($_SESSION['caf']['page_step'] < $step) ? $step : $_SESSION['caf']['page_step'];
	$width = ($up_to / 10) * 100;

?>
<div id="progress_holder">
	<div id="progress_bar">
		<div id="progress" style="width:<?php echo $width; ?>%;"></div>
	</div>
	<span class="percent"><?php echo $width; ?>%</span>
	<br class="clear_both" />
</div>
<?php } ?>

<h1>Course Application Form</h1>
<?php
if ($step != 0) {
	// remove duplicate errors if they exist
	$_SESSION['caf']['errors'] = array_unique($_SESSION['caf']['errors']);
	if ((isset($_SESSION['caf']['errors'])) && count($_SESSION['caf']['errors']) > 0) {
		echo "<div class=\"error\"><h2>Errors</h2>
			<ul>";
		foreach ($_SESSION['caf']['errors'] as $error) {
			echo "<li>$error</li>";
		}
		echo "</ul></div>";
	}
}

if ($step != 0 && $step != 10) {
	// get the URL of the current page - without query string

	echo '<div id="reference_details_print">';
	echo '<h2>Your Reference Details</h2>';
	echo '<p>Resume a saved application with these details:</p>';
	echo '<table>';
	echo '<tr><td width="120"><strong>Email Address:</strong></td><td> '.$_SESSION['caf']['email_address'].'</td></tr>';
	echo '<tr><td><strong>Reference ID:</strong></td><td>'.$_SESSION['caf']['reference_id'].' </td></tr>';
	echo '<tr><td><strong>Login URL:</strong></td><td>'.$resume_url.'</td></tr>';
	echo '</table>';
	echo '</div>';
}

if ($step > 0) {
	//if (isset($_GET['logout']) && $_GET['logout'] == 1) {
	/*
	if ($step == 10) {
		echo '<a href="logout.php" id="logout">Logout</a>';
	}
	 */
	//}
	// Set up breadcrumb navigation
	$sections = array(
		1 => '1. Course',
		2 => '2. Personal', 
		3 => '3. Support', 
		4 => '4. Qualifications', 
		5 => '5. Employment and Experience', 
		6 => '6. Residence / Fee Status', 
		7 => '7. How Heard', 
		8 => '8. Interview',
		9 => '9. Check Details'
	);
	$steps = '<ul>';
	$class = '';
	$section_txt = '';

	// Step should use $_SESSION['caf']['page_step'] if it's set up
	$step_is = (isset($_SESSION['caf']['page_step']) && $_SESSION['caf']['page_step'] != '') ? 
		$_SESSION['caf']['page_step'] : $step;

	foreach ($sections as $key => $section) {
		if ($key <= $step_is) {
			$class = 'complete';
			$section_txt = '<a href="'.THIS_URL.'?step='.$key.'">'.$section.'</a>';
		} else {
			$class = '';
			$section_txt = $section;
		}
		if ($key == $step) {
			$class .= ' current';
			$section_txt = $section;
		}
		$steps .= "\t<li class=\"$class\">$section_txt</li>\n";
	}
	$steps .= '</ul>';
	
	if ($step != 10) {
		echo "<div id=\"steps\">$steps</div>";
	}

}

	if ($step == 0) {
		// If we've already started: redirect to step 1
?>
	<script type="text/javascript">
		$(document).ready(function() {

			// New applicant form check
			$("#new_applicant").submit(function(e) {

				// check if name is blank
				if ($("#s0_email").val() == '') {
					alert('Please enter your email address to begin');
					$("#s0_email").focus();
					return false;
				}
				
				// check if email address is valid
				if (!isValidEmailAddress($("#s0_email").val())) {
					alert('Invalid email address');
					$("#s0_email").focus();
					return false;
				}

				return true;

			});
			
			// Returning applicant form check
			$("#returning_applicant").submit(function(e) {

				// check if email address is blank
				if ($("#s0_ra_email").val() == '') {
					alert('Please enter your email address');
					$("#s0_ra_email").focus();
					return false;
				}
				
				// check if email address is valid
				if (!isValidEmailAddress($("#s0_ra_email").val())) {
					alert('Invalid email address');
					$("#s0_ra_email").focus();
					return false;
				}
				
				// check if reference id is blank
				if ($("#s0_ra_ref").val() == '') {
					alert('Please enter your reference ID');
					$("#s0_ra_ref").focus();
					return false;
				}

				return true;

			});

		});
	</script>
<?php

		echo '<div class="section">';


		$email_address = (isset($_SESSION['caf']['email_address'])) ? $_SESSION['caf']['email_address'] : '';
		if ($email_address == '') {
			$email_address = (isset($_POST['email_address']) && $_POST['email_address'] != '') ? trim(filter_var($_POST['email_address']), FILTER_SANITIZE_EMAIL) : '';
		}

		$not_form_data = array('datetime_submitted_first', 'form_completed');
		
		//is_valid_email_address
		if (($email_address != '') && (filter_var(trim($email_address), FILTER_VALIDATE_EMAIL)) && 
		($_SESSION['caf']['step_complete'][0] == FALSE)) {

			// check if reference id also posted	
			if (isset($_POST['reference_id']) && $_POST['reference_id'] != '') {
				
				// check for valid reference id
				$ref_id = (!isset($_SESSION['caf']['reference_id'])) ? $_POST['reference_id'] : $_SESSION['caf']['reference_id'];
				$ref_id_stripped = trim(str_replace('-','',$ref_id));
				$valid_ref = ctype_alnum($ref_id_stripped);
				if (!$valid_ref) {
					$_SESSION['caf']['errors'][] = "Invalid Reference ID provided";
					header('location: '.THIS_URL);
					exit;
				}
				// Trim whitespace
				$email_address = trim($email_address);
				$ref_id = trim($ref_id);
				
				$query = sprintf("SELECT * FROM tbl_course_application 
					WHERE email_address = '%s' 
					AND reference_id = '%s'",
					mysql_real_escape_string($email_address, $sql->Link_ID),
					mysql_real_escape_string($ref_id, $sql->Link_ID)
				);
				$sql->query($query, $debug);
				
				$completed = 0;
				if ($sql->num_rows() > 0) {
				
					// There are a few columns where multiple values are converted to CSV, use an array to explode these items
					//$csv_columns = array('learning_difficulty','disability', 'benefits_receiving', 'how_heard_about_course');
					$csv_columns = array('learning_difficulty','disability', 'how_heard_about_course');
				
					// Here we need to fill the session array with all present values in the table
					while($sql->next_record()) {
						// Set up session values for all non-blank fields
						
						foreach ($sql->Record as $key => $value) {
							if (!is_numeric($key) && $value != '' && (!in_array($key, $not_form_data))) {
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
									$_SESSION['caf']['errors'][] = "You have submitted this course application already.<br/>To start a new application please enter your email in the 'New Application' section below.";
									//$_SESSION['caf']['step_complete'][0] = FALSE;
									//$_SESSION['caf']['email_address'] = '';
									session_destroy();
									break;
								}
							}
						}
					}
					
					if ($completed == 0) {
						for ($i=0; $i < $_SESSION['caf']['page_step']; $i++) {
							$_SESSION['caf']['step_complete'][$i] = TRUE;
						}
						
						header('location: '.THIS_URL.'?step='.$_SESSION['caf']['page_step'].'');
						exit;
					}

				} else {
					// email and reference id don't match.
					$_SESSION['caf']['errors'][] = "Incorrect email address or reference id. Please check your details then try again.";
				}
			} else {
							
				// Trim whitespace
				$email_address = trim(mysql_real_escape_string($email_address, $sql->Link_ID));
				
				// TODO implement email domain checking

				// See if this email's already in use - probably won't ever occur, good to have it here anyway
				$query = "SELECT id FROM tbl_course_application WHERE email_address = '$email_address' AND form_completed = 0";
				$sql->query($query, $debug);
				if ($sql->num_rows() > 0) {
					// Email address does exist: redirect to home page
					$_SESSION['caf']['errors'][] = "An incomplete application exists for this email address already. <br />Please log in as a returning applicant to complete your application.";
				} else {
					$_SESSION['caf']['email_address'] = $email_address;
					$_SESSION['caf']['step_complete'][0] = TRUE;
				}
			}
		} else {
			if ($email_address != '' && !filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
				$_SESSION['caf']['errors'][] = "Invalid email address";
			}
		}
		
		
		if (isset($_SESSION['caf']['email_address']) && $_SESSION['caf']['step_complete'][0] === TRUE) {
			
			/* Probably a bit annoying having it auto-print
			echo '<script type="text/javascript">window.print();</script>';
			*/

			$query = "SELECT reference_id FROM tbl_course_application WHERE email_address = '$email_address' AND form_completed = 1";
			$sql->query($query, $debug);

			$ref_ids = array();
			if ($sql->num_rows() > 0) {
				$no_completes = $sql->num_rows();
				++$no_completes;
				while($sql->next_record()) {
					foreach ($sql->Record as $key => $value) {
						$ref_ids[] = $value;
					}
				}
			} else {
				$no_completes = 1;
			}
			
			echo '<h2>Your Reference Details</h2>';
			$unique_ref = strtoupper(uniqid());
			// User's can be confused with zeros and uppercase 0s - strip all cases of
			$unique_ref = str_replace('0', '2', $unique_ref);
			$unique_ref = str_replace('O', 'X', $unique_ref);
			$unique_ref = substr($unique_ref,0,12);
			$unique_ref = wordwrap($unique_ref, 4, "-", true);
			// to avoid duplicate reference ids make the first two digits = V2 <-- where 2 = the second form they've submitted
			$version = 'C' . $no_completes;
			$unique_ref = $version . substr($unique_ref, 2);
			
			// Set the reference id if not set or this reference ids exists already
			if (!isset($_SESSION['caf']['reference_id']) || (count($ref_ids) > 0 && in_array($_SESSION['caf']['reference_id'], $ref_ids))) {
				$_SESSION['caf']['reference_id'] = $unique_ref;
			}
			
			echo '<p>Resume a saved application with these details:</p>';
			echo '<div id="save_details">';

			echo '<table id="reference_details">';
			echo '<tr><td><strong>Email Address:</strong></td><td> '.$_SESSION['caf']['email_address'].'</td></tr>';
			echo '<tr><td><strong>Reference ID:</strong></td><td>'.$_SESSION['caf']['reference_id'].' </td></tr>';
			echo '</table>';
			echo '</div>';

			echo '<p class="important">Print your reference details &mdash; required to resume a saved application.</p> ';
            echo '<p>Your reference details will be emailed to you after entering a correct security code below.</p>';
			echo '<p class="print_this"><img src="../images/printer.png" width="16" height="16" border="0" alt="printer icon" /> <strong><a href="javascript:window.print()">Print</a></strong></p>';
			echo '<br />';
			
			echo '<div class="print_hide">';
			echo '<hr />';
			echo '<h2>Security</h2>';
			echo '<form method="post" action="'.THIS_URL.'?step=1">';
			
			echo '<img src="captchalib.php?width=100&height=40&characters=6" alt="security code" /><br />
			<input id="security_code" name="security_code" type="text" style="width:100px;" /><br />';
			
			if (count($_SESSION['caf']['errors']) > 0) {
				$_SESSION['caf']['errors'] = array_unique($_SESSION['caf']['errors']);
				if ((isset($_SESSION['caf']['errors'])) && count($_SESSION['caf']['errors']) > 0) {
					echo "<div class=\"error\"><h2>Errors</h2>
						<ul>";
					foreach ($_SESSION['caf']['errors'] as $error) {
						echo "<li>$error</li>";
					}
					echo "</ul></div>";
				}
			}
			echo '<p>Please enter the security code shown above to start your application.</p>';
			
			echo '<input type="submit" value="Start Application &gt;" class="submit" />';
			echo '</form>';
			echo '</div>';
		
		} else {
		
			$_SESSION['caf']['errors'] = array_unique($_SESSION['caf']['errors']);
			if ((isset($_SESSION['caf']['errors'])) && count($_SESSION['caf']['errors']) > 0) {
				echo "<div class=\"error\"><h2>Errors</h2>
					<ul>";
				foreach ($_SESSION['caf']['errors'] as $error) {
					echo "<li>$error</li>";
				}
				echo "</ul></div>";
			}
		
			// nkowald - 2011-05-23 - get email and ref id if they exist in the query string
			$passed_email = (isset($_GET['email'])) ? $_GET['email'] : '';
			$passed_refid = (isset($_GET['ref_id'])) ? $_GET['ref_id'] : '';
			
			echo '<div id="new_applicants">';
			echo '<h2>New Application</h2>';
			echo '<p style="line-height:1.35em;"><strong>Please complete your online application in one sitting.<br />It will take you just 15 minutes and then we will be able<br />to get in touch shortly after you submit it.</strong></p>';
			echo '<p>You can save your progress at any time during your application.<br />';
			echo 'Your email address and a unique reference ID are used to identify you.</p>';
			
			echo '<form method="post" action="'.THIS_URL.'" id="new_applicant">';
			echo '<table>';
			echo '<tr><td><label for="s0_email">Email Address:</label></td><td><input type="email" name="email_address" id="s0_email" class="text_home" required /><span id="valid_email"></span>'.$blank_email_msg.'</td></tr>';
			echo '<tr><td colspan="2"><input type="submit" value="Start Application &gt;" class="submit" /></td>';
			echo '</table>';
			echo '</form>';
			echo '</div>';
			
			echo '<div id="returning_applicants">';
			echo '<h2>Returning Applicant?</h2>';
			echo '<p>Please enter your email address and reference ID to continue a saved application.</p>';
			echo '<form method="post" action="'.THIS_URL.'" id="returning_applicant">';
			echo '<table>';
			echo '<tr><td><label for="s0_ra_email">Email Address:</label></td><td><input type="email" name="email_address" id="s0_ra_email" class="text_home" value="'.$passed_email.'" required /></td></tr>';
			echo '<tr><td><label for="s0_ra_ref">Reference ID:</label></td><td><input type="text" name="reference_id" id="s0_ra_ref" class="text_home" value="'.$passed_refid.'" pattern="[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}" requied /><br /></td></tr>';
			echo '<tr><td colspan="2" class="lost_ref_id note">Lost or forgotten your reference ID? Phone us on <strong>020 8442 3055</strong></td></tr>';
			echo '<tr><td colspan="2"><input type="submit" value="Continue Application &gt;" class="submit" /></td></tr>';		
			echo '</table>';
			echo '</form>';
			echo '</div>';
		
		}
		echo '<br class="clear_both" />';
		echo '<br class="clear_both" />';
		echo '</div>';
	}

	if ($step == 1) {

		if(($_SESSION['security_code'] == $_POST['security_code']) && (!empty($_SESSION['security_code'])) ) {
			// Insert your code for processing the form here, e.g emailing the submission, entering it into a database. 
			unset($_SESSION['security_code']);
			$_SESSION['caf']['page_step'] = 1;

			// Email user their reference details
			emailUserReferenceDetails($_SESSION['caf']['email_address']);

			// Create database record for current user
			$datetime_sub = date('Y-m-d H:i:s'); // 2010-06-02 14:24:04 - MySQL timestamp format
			$query = sprintf("INSERT INTO tbl_course_application 
				(datetime_submitted_first, email_address, reference_id, page_step, form_completed) VALUES('%s', '%s', '%s', '%d', '%d')", 
				$datetime_sub,
				mysql_real_escape_string($_SESSION['caf']['email_address'], $sql->Link_ID),
				mysql_real_escape_string($_SESSION['caf']['reference_id'], $sql->Link_ID),
                1,
				0
			);
			$sql->query($query, $debug);
			$row_id = $sql->db_insertid();
			if ($row_id) {
				$_SESSION['caf']['id'] = $row_id;
            } else {
                // SQL Query error
				$_SESSION['caf']['errors'][] = "Could not create a new application, please refresh then try again";
				header('location: '.THIS_URL);
				exit;
            }

	   } else {
			if ($_SESSION['caf']['page_step'] == 0) {
				$_SESSION['caf']['errors'][] = "The security code wasn't entered correctly, try it again.";
				header('location: '.THIS_URL);
				exit;
			}
	   }

?>
<script type="text/javascript">
	$(document).ready(function() {

		// New applicant form check
		$("#s1_course_details").submit(function(e) {

			// check if course title 1 is blank
			if ($("#s1_course_title_1").val() == '') {
				alert('Please enter a course title');
				$("#s1_course_title_1").focus();
				return false;
			}
			
			// check if college centre 1 is blank
			if ($("#s1_college_centre_1").val() == '') {
				alert('Please select a college centre');
				$("#s1_college_centre_1").focus();
				return false;
			}

			// check if course entry date is blank
			if ($("#s1_course_entry_date_1").val() == '') {
				alert('Please enter an entry date');
				$("#s1_course_entry_date_1").focus();
				return false;
			}
			
			return true;

		});

	});
</script>
<div class="section">
	<form method="post" action="<?php echo THIS_URL; ?>?step=2" id="s1_course_details">
	<h2><span>Section 1</span> &#8211; Course Details</h2>
	
	<?php 
		if ($_SESSION['caf']['course_title_1'] == '' || $_SESSION['caf']['course_code_1'] == '' || $_SESSION['caf']['college_centre_1'] == '' || $_SESSION['caf']['course_entry_date_1'] == '' || 
		$_SESSION['caf']['course_title_2'] == '' || $_SESSION['caf']['course_code_2'] == '' || $_SESSION['caf']['college_centre_2'] == '' || $_SESSION['caf']['course_entry_date_2'] == '') {
	?>
	<div class="browse_instructions">
		<h3>Browse for Course: Instructions</h3>
		<p class="note">Clicking the <strong>'Browse for Course'</strong> buttons will take you to our course subjects page.</p>
		<div id="browse_steps" class="note">
		<ol>
			<li>Select the Subject, then Course you wish to apply for.</li>
			<li>Scroll to the bottom of the page to see a choice of college centres and start dates.</li>
			<li>Click the <strong>'Add to Application'</strong> button to add your choice and continue your application.</li>
		</ol>
		</div>
	</div>
	<?php } ?>
	<table summary="Course 1 Details">
		<tr>
			<td><h3 class="courses">Course 1</h3></td>
			<td><input type="submit" id="browse_course_1" value="+ Browse for Course 1" class="submit browse<?php if ((!isset($_SESSION['caf']['course_title_1']) || $_SESSION['caf']['course_title_1'] == '') && (!isset($_SESSION['caf']['course_code_1']) || $_SESSION['caf']['course_code_1'] == '')) { echo ''; } else { echo ' hidden'; } ?>" /></td>
		</tr>
		<tr class="<?php addMissingFieldClass('course_title_1'); ?>">
			<?php if (isset($_SESSION['caf']['course_title_1']) && $_SESSION['caf']['course_title_1'] != '') { $readonly = ' readonly="readonly"'; } else { $readonly = ''; } ?>
			<td width="145"><label for="s1_course_title_1">Course Title:<span class="required">*</span></label></td>
			<td><input type="text" name="course_title_1" class="text" id="s1_course_title_1" maxlength="100" value="<?php getValue('text', 'course_title_1'); ?>" <?php echo $readonly; ?> required /></td>
		</tr>
		<tr>
			<?php if (isset($_SESSION['caf']['course_code_1']) && $_SESSION['caf']['course_code_1'] != '') { $readonly = ' readonly="readonly"'; } else { $readonly = ''; } ?>
			<td><label for="s1_course_code_1">Course Code:</label></td>
			<td><input type="text" name="course_code_1" class="text" id="s1_course_code_1" maxlength="15" value="<?php getValue('text', 'course_code_1'); ?>" <?php echo $readonly; ?> /></td>
		</tr>
		<tr class="<?php addMissingFieldClass('college_centre_1'); ?>">
			<td><label for="s1_college_centre_1">College Centre:<span class="required">*</span></label></td>
			<td>
				<?php
					if (isset($_SESSION['caf']['college_centre_1']) && $_SESSION['caf']['college_centre_1'] != '') {
						echo '<input type="text" id="s1_college_centre_1" name="college_centre_1" value="'.$_SESSION['caf']['college_centre_1'].'" readonly="readonly" class="text" maxlength="15" required />';
					} else {
				?>
					<select name="college_centre_1" id="s1_college_centre_1" required>
						<option value="">Choose centre...</option>
						<option value="Enfield" <?php getValue('select', 'college_centre_1', 'Enfield'); ?>>Enfield</option>
						<option value="Tottenham" <?php getValue('select', 'college_centre_1', 'Tottenham'); ?>>Tottenham</option>
					</select>
				<?php
					}
				?>
			</td>
		</tr>
		<tr class="<?php addMissingFieldClass('course_entry_date_1'); ?>">
			<td><label>Entry Date:<span class="required">*</span></label></td>
			<td>
				<?php if (isset($_SESSION['caf']['course_entry_date_1']) && $_SESSION['caf']['course_entry_date_1'] != '') { $readonly = ' readonly="readonly"'; } else { $readonly = ''; } ?>
				<input type="text" name="course_entry_date_1" class="text" id="s1_course_entry_date_1" maxlength="60" value="<?php getValue('text', 'course_entry_date_1'); ?>" <?php echo $readonly; ?> required />
			</td>
		</tr>
		<tr>
			<td><label>Preferred Entry Date:</label></td>
			<td>
<select id="s1_preferred_entry_month_1" name="preferred_entry_month_1" class="date">
	<option value="">Month...</option>
	<option value="January"<?php getValue('select', 'preferred_entry_month_1', 'January'); ?>>January</option>
	<option value="February"<?php getValue('select', 'preferred_entry_month_1', 'February'); ?>>February</option>
	<option value="March"<?php getValue('select', 'preferred_entry_month_1', 'March'); ?>>March</option>
	<option value="April"<?php getValue('select', 'preferred_entry_month_1', 'April'); ?>>April</option>
	<option value="May"<?php getValue('select', 'preferred_entry_month_1', 'May'); ?>>May</option>
	<option value="June"<?php getValue('select', 'preferred_entry_month_1', 'June'); ?>>June</option>
	<option value="July"<?php getValue('select', 'preferred_entry_month_1', 'July'); ?>>July</option>
	<option value="August"<?php getValue('select', 'preferred_entry_month_1', 'August'); ?>>August</option>
	<option value="September"<?php getValue('select', 'preferred_entry_month_1', 'September'); ?>>September</option>
	<option value="October"<?php getValue('select', 'preferred_entry_month_1', 'October'); ?>>October</option>
	<option value="November"<?php getValue('select', 'preferred_entry_month_1', 'November'); ?>>November</option>
	<option value="December"<?php getValue('select', 'preferred_entry_month_1', 'December'); ?>>December</option>
</select>
			<?php
				$this_year = date('Y');
				$years[] = $this_year;
				$years[] = ++$this_year;
			
				echo "<select id=\"s1_preferred_entry_year_1\" name=\"preferred_entry_year_1\" class=\"date\">\n";
				echo "<option value=\"\">Year...</option>\n";
				foreach ($years as $year) {
					$year_saved = (isset($_SESSION['caf']['preferred_entry_year_1'])) ? $_SESSION['caf']['preferred_entry_year_1'] : '';
					$selected = ($year_saved == $year) ? ' selected="selected"' : '';
					echo "\t<option value=\"$year\"$selected>$year</option>\n";
				}
				echo "</select>";
			?>
			<br /><a href="#" id="clear_course_1">Clear Course 1</a>
			</td>
		</tr>
	</table>
	<br />
	<table summary="Course 2 Details">
		<tr>
			<td><h3 class="courses">Course 2</h3></td>
			<td><input type="submit" id="browse_course_2" value="+ Browse for Course 2" class="submit browse<?php if ((!isset($_SESSION['caf']['course_title_2']) || $_SESSION['caf']['course_title_2'] == '') && (!isset($_SESSION['caf']['course_code_2']) || $_SESSION['caf']['course_code_2'] == '')) { echo ''; } else { echo ' hidden'; } ?>" /></td>
		</tr>
		<tr>
			<?php if (isset($_SESSION['caf']['course_title_2']) && $_SESSION['caf']['course_title_2'] != '') { $readonly = ' readonly="readonly"'; } else { $readonly = ''; } ?>
			<td width="145"><label for="s1_course_title_2">Course Title:</label></td>
			<td><input type="text" name="course_title_2" class="text" id="s1_course_title_2" maxlength="100" value="<?php getValue('text', 'course_title_2'); ?>" <?php echo $readonly; ?> /></td>
		</tr>
		<tr>
			<?php if (isset($_SESSION['caf']['course_code_2']) && $_SESSION['caf']['course_code_2'] != '') { $readonly = ' readonly="readonly"'; } else { $readonly = ''; } ?>
			<td><label for="s1_course_code_2">Course Code:</label></td>
			<td><input type="text" name="course_code_2" class="text" id="s1_course_code_2" maxlength="15" value="<?php getValue('text', 'course_code_2'); ?>" <?php echo $readonly; ?> /></td>
		</tr>
		<tr>
			<td><label for="s1_college_centre_2">College Centre:</label></td>
			<td>
			<?php
				if (isset($_SESSION['caf']['college_centre_2']) && $_SESSION['caf']['college_centre_2'] != '') {
					echo '<input type="text" id="s1_college_centre_2" name="college_centre_2" value="'.$_SESSION['caf']['college_centre_2'].'" readonly="readonly" class="text" maxlength="15" />';
				} else {
			?>
				<select name="college_centre_2" id="s1_college_centre_2">
					<option value="">Choose centre...</option>
					<option value="Enfield" <?php getValue('select', 'college_centre_2', 'Enfield'); ?>>Enfield</option>
					<option value="Tottenham" <?php getValue('select', 'college_centre_2', 'Tottenham'); ?>>Tottenham</option>
				</select>
			<?php
				}
			?>
			</td>
		</tr>
		<tr class="<?php addMissingFieldClass('course_entry_date_2'); ?>">
			<td><label>Entry Date:</label></td>
			<td>
				<?php if (isset($_SESSION['caf']['course_entry_date_2']) && $_SESSION['caf']['course_entry_date_2'] != '') { $readonly = ' readonly="readonly"'; } else { $readonly = ''; } ?>
				<input type="text" name="course_entry_date_2" class="text" id="s1_course_entry_date_2" maxlength="60" value="<?php getValue('text', 'course_entry_date_2'); ?>" <?php echo $readonly; ?> />
			</td>
		</tr>
		<tr>
			<td><label>Preferred Entry Date:</label></td>
			<td>
<select id="s1_preferred_entry_month_2" name="preferred_entry_month_2" class="date">
	<option value="">Month...</option>
	<option value="January"<?php getValue('select', 'preferred_entry_month_2', 'January'); ?>>January</option>
	<option value="February"<?php getValue('select', 'preferred_entry_month_2', 'February'); ?>>February</option>
	<option value="March"<?php getValue('select', 'preferred_entry_month_2', 'March'); ?>>March</option>
	<option value="April"<?php getValue('select', 'preferred_entry_month_2', 'April'); ?>>April</option>
	<option value="May"<?php getValue('select', 'preferred_entry_month_2', 'May'); ?>>May</option>
	<option value="June"<?php getValue('select', 'preferred_entry_month_2', 'June'); ?>>June</option>
	<option value="July"<?php getValue('select', 'preferred_entry_month_2', 'July'); ?>>July</option>
	<option value="August"<?php getValue('select', 'preferred_entry_month_2', 'August'); ?>>August</option>
	<option value="September"<?php getValue('select', 'preferred_entry_month_2', 'September'); ?>>September</option>
	<option value="October"<?php getValue('select', 'preferred_entry_month_2', 'October'); ?>>October</option>
	<option value="November"<?php getValue('select', 'preferred_entry_month_2', 'November'); ?>>November</option>
	<option value="December"<?php getValue('select', 'preferred_entry_month_2', 'December'); ?>>December</option>
</select>
		<?php
			echo "<select id=\"s1_preferred_entry_year_2\" name=\"preferred_entry_year_2\" class=\"date\">\n";
			echo "<option value=\"\">Year...</option>\n";
			foreach ($years as $year) {
				$year_saved = (isset($_SESSION['caf']['preferred_entry_year_2'])) ? $_SESSION['caf']['preferred_entry_year_2'] : '';
				$selected = ($year_saved == $year) ? ' selected="selected"' : '';
				echo "\t<option value=\"$year\"$selected>$year</option>\n";
			}
			echo "</select>";
		?>
				<br /><a href="#" id="clear_course_2">Clear Course 2</a>
			</td>
		</tr>
	</table>
	
	<table>
	<tr>
		<td colspan="2">
			<input type="button" value="Save" class="submit_save" />
			<input type="submit" value="Next &gt;" class="submit" />
		</td>
	</tr>
	</table>
	</form>
	<div id="save_msg"></div>
</div>

<?php 

}

if ($step == 2) {

?>
<script type="text/javascript">
	$(document).ready(function() {

        function limitChars(textid, limit, infodiv) {
            var text = $('#'+textid).val();    
            var textlength = text.length;
            if (textlength > limit) {
                $('#' + infodiv).html(''+limit+' character limit reached');
                $('#'+textid).val(text.substr(0,limit));
                return false;
            } else {
                $('#' + infodiv).html(''+ (limit - textlength) +' characters left');
                return true;
            }
        }

        limitChars('s2_why_want_to', 1500, 'charlimitinfo');

        $(function(){
             $('#s2_why_want_to').keyup(function(){
                 limitChars('s2_why_want_to', 1500, 'charlimitinfo');
             })
        });
        
		// New applicant form check
		$("#s2_personal_details").submit(function(e) {

			// check if first name is blank
			if ($("#s2_firstname").val() == '') {
				alert('Please enter your firstname');
				$("#s2_firstname").focus();
				return false;
			}
			
			// check if surname is blank
			if ($("#s2_surname").val() == '') {
				alert('Please enter your surname');
				$("#s2_surname").focus();
				return false;
			}
			
			// check if gender is blank
			var gender = $("input[name='gender']:checked").val();
			var gender = $("input[name='gender']:checked").val();
			
			if (gender == 'Male') {
				//
			} else if (gender == 'Female') {
				//
			} else {
				alert('Please select a gender');
				return false;
			}
			
			// check if date of birth is blank
			if ($("#s2_dob").val() == '') {
				alert('Please enter your date of birth');
				$("#s2_dob").focus();
				return false;
			}
			
			var dob_regexp = /^([0-9]){2}(\/){1}([0-9]){2}(\/)([0-9]){4}$/;
			if (dob_regexp.test($("#s2_dob").val()) != true) {
				alert('enter date of birth in dd/mm/yyyy format');
				$("#s2_dob").focus();
				return false;
			}
			
			// check if home address is blank
			if ($("#s2_home_address").val() == '') {
				alert('Please enter your home address');
				$("#s2_home_address").focus();
				return false;
			}
			
			// check if postcode is blank
			if ($("#s2_postcode").val() == '') {
				alert('Please enter your postcode');
				$("#s2_postcode").focus();
				return false;
			}
			
			// check if ethnic group is blank
			if ($("#s2_ethnic_group").val() == '') {
				alert('Please select your ethnic group');
				$("#s2_ethnic_group").focus();
				return false;
			}
			
			// check if why want to do course is blank
			if ($("#s2_why_want_to").val() == '') {
				alert('Please enter why you want to do this course');
				$("#s2_why_want_to").focus();
				return false;
			}

			// check if home/work telephone blank
			if (($("#s2_tel_home_work").val() == '') && ($("#s2_tel_mob").val() == '')) {
				alert("Please enter your 'home/work' or 'mobile' telephone number.\nAt least one is required.");
				$("#s2_tel_home_work").focus();
				return false;
			}
			
			return true;

		});

	});
</script>
<div class="section">
	<form method="post" action="<?php echo THIS_URL; ?>?step=3" id="s2_personal_details">
	<h2><span>Section 2</span> &#8211; Personal Details</h2>
	<table summary="Your Personal Details">
		<tr>
			<td width="240"><label for="s2_title">Title:</label></td>
			<td>
				<select name="title" id="s2_title">
				<?php
					$titles = array('Mr', 'Ms', 'Miss', 'Mrs', 'Dr', 'Other');
					foreach($titles as $title) {
						$selected = (isset($_SESSION['caf']['title']) && $_SESSION['caf']['title'] == $title) ? ' selected="selected"' : '';
						echo "\t<option value=\"$title\"$selected>$title</option>\n";
					}
				?>
				</select>
			</td>
		</tr>
		<tr class="<?php addMissingFieldClass('firstname'); ?>">
			<td><label for="s2_firstname">First name(s):<span class="required">*</span></label></td>
			<td><input type="text" name="firstname" class="text capitalise" id="s2_firstname" maxlength="30" value="<?php getValue('text', 'firstname'); ?>" required /></td>
		</tr>
		<tr class="<?php addMissingFieldClass('surname'); ?>">
			<td><label for="s2_surname">Family name / surname:<span class="required">*</span></label></td>
			<td><input type="text" name="surname" class="text capitalise" id="s2_surname" maxlength="40" value="<?php getValue('text', 'surname'); ?>" required /></td>
		</tr>
		<tr class="<?php addMissingFieldClass('gender'); ?>">
			<td><label>Gender:<span class="required">*</span></label></td>
			<td>
				<input type="hidden" name="gender" value="" />
				<input type="radio" name="gender" value="Male" id="s2_male" <?php getValue('radio', 'gender', 'Male'); ?> class="radio" /> <label for="s2_male">Male</label> 
				<input type="radio" name="gender" value="Female" id="s2_female" <?php getValue('radio', 'gender', 'Female'); ?> class="radio" /> <label for="s2_female">Female</label>
			</td>
		</tr>
		<tr class="<?php addMissingFieldClass('date_of_birth'); ?>">
			<td><label for="s2_dob">Date of Birth:<span class="required">*</span></label></td>
			<td>
				<input type="text" name="date_of_birth" class="date" id="s2_dob" maxlength="10" value="<?php getValue('text', 'date_of_birth'); ?>" placeholder="dd/mm/yyyy" required pattern="\d{2}/\d{2}/\d{4}" /> <span class="note">(dd/mm/yyyy)</span>
				<input type="hidden" name="age_at_31_aug_2010" value="" />
			</td>
		</tr>
		<tr class="<?php addMissingFieldClass('home_address'); ?>">
			<td><label for="s2_home_address">Home Address:<span class="required">*</span></label></td>
			<td><textarea name="home_address" id="s2_home_address" cols="40" rows="2" maxlength="200" class="capitalise" required><?php getValue('textarea', 'home_address'); ?></textarea></td>
		</tr>
		<tr class="<?php addMissingFieldClass('postcode'); ?>">
			<td><label for="s2_postcode">Postcode:<span class="required">*</span></label></td>
			<td><input type="text" name="postcode" class="text uppercase" id="s2_postcode" maxlength="10" value="<?php getValue('text', 'postcode'); ?>" required /></td>
		</tr>
		<tr>
			<td colspan="2"><label>Telephone<span class="required">*</span></label> (At least one phone number required)</td>
		</tr>
		<tr>
			<td><label for="s2_tel_home_work">Home/Work telephone:</label></td>
			<td><input type="text" name="telephone_home_work" class="text" id="s2_tel_home_work" maxlength="20" value="<?php getValue('text', 'telephone_home_work'); ?>" /></td>
		</tr>
		<tr>
			<td><label for="s2_tel_mob">Mobile telephone:</label></td>
			<td>
				<input type="text" name="telephone_mobile" class="text" id="s2_tel_mob" maxlength="20" value="<?php getValue('text', 'telephone_mobile'); ?>" />
			</td>
		</tr>
		<tr>
			<td><label for="s2_email">Email address:</label></td>
			<td><input type="email" name="email_address" class="text" disabled="disabled" id="s2_email" value="<?php getValue('text', 'email_address'); ?>" /><br /><br /></td>
		</tr>
		<tr class="<?php addMissingFieldClass('ethnic_group'); ?>">
			<td><label for="s2_ethnic_group">Your ethnic group:<span class="required">*</span></label></td>
			<td>
				<select name="ethnic_group" id="s2_ethnic_group" required>
					<option value="" <?php getValue('select', 'ethnic_group', ''); ?>>Please Select...</option>
					<optgroup label="White">
						<option value="English/Welsh/Scottish/Northern Irish/British" <?php getValue('select', 'ethnic_group', 'English/Welsh/Scottish/Northern Irish/British'); ?>>English/Welsh/Scottish/Northern Irish/British</option>
						<option value="Irish" <?php getValue('select', 'ethnic_group', 'Irish'); ?>>Irish</option>
						<option value="Gypsy or Irish Traveller" <?php getValue('select', 'ethnic_group', 'Gypsy or Irish Traveller'); ?>>Gypsy or Irish Traveller</option>
						<option value="Any other white background" <?php getValue('select', 'ethnic_group', 'Any other white background'); ?>>Any other white background</option>
					</optgroup>
					<optgroup label="Mixed/Multiple ethnic group">
						<option value="White and Black Caribbean" <?php getValue('select', 'ethnic_group', 'White and Black Caribbean'); ?>>White and Black Caribbean</option>
						<option value="White and Black African" <?php getValue('select', 'ethnic_group', 'White and Black African'); ?>>White and Black African</option>
						<option value="White and Asian" <?php getValue('select', 'ethnic_group', 'White and Asian'); ?>>White and Asian</option>
						<option value="Any other Mixed/multiple ethnic background" <?php getValue('select', 'ethnic_group', 'Any other Mixed/multiple ethnic background'); ?>>Any other Mixed/multiple ethnic background</option>
					</optgroup>
					<optgroup label="Asian/Asian British">
						<option value="Indian" <?php getValue('select', 'ethnic_group', 'Indian'); ?>>Indian</option>
						<option value="Pakistani" <?php getValue('select', 'ethnic_group', 'Pakistani'); ?>>Pakistani</option>
						<option value="Bangladeshi" <?php getValue('select', 'ethnic_group', 'Bangladeshi'); ?>>Bangladeshi</option>
						<option value="Chinese" <?php getValue('select', 'ethnic_group', 'Chinese'); ?>>Chinese</option>
						<option value="Any other Asian Background" <?php getValue('select', 'ethnic_group', 'Any other Asian Background'); ?>>Any other Asian Background</option>
					</optgroup>
					<optgroup label="Black/African/Caribbean/Black British">
						<option value="African" <?php getValue('select', 'ethnic_group', 'African'); ?>>African</option>
						<option value="Caribbean" <?php getValue('select', 'ethnic_group', 'Caribbean'); ?>>Caribbean</option>
						<option value="Any other Black/African/Caribbean background" <?php getValue('select', 'ethnic_group', 'Any other Black/African/Caribbean background'); ?>>Any other Black/African/Caribbean background</option>
					</optgroup>
					<optgroup label="Other ethnic group">
						<option value="Arab" <?php getValue('select', 'ethnic_group', 'Arab'); ?>>Arab</option>
						<option value="Any other ethnic group" <?php getValue('select', 'ethnic_group', 'Any other ethnic group'); ?>>Any other ethnic group</option>
					</optgroup>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for="s2_ethnic_group_other">Any other:</label></td>
			<td><input type="text" name="enthic_group_other" maxlength="40" value="<?php getValue('text', 'enthic_group_other'); ?>" id="s2_ethnic_group_other" class="text" /></td>
		</tr>
		<tr>
			<td><label for="s2_language_at_home">What language do you speak at home?</label></td>
			<td><input type="text" name="language_spoken_at_home" maxlength="40" class="text capitalise" id="s2_language_at_home" value="<?php getValue('text', 'language_spoken_at_home'); ?>" /><br /><br /></td>
		</tr>
		<tr class="<?php addMissingFieldClass('why_want_to_do_course'); ?>">
			<td colspan="2">
				<h4><label for="s2_why_want_to">Why do you want to do this course?:<span class="required">*</span></label></h4>
				<p>Please use this box to tell us about your reasons for doing the course, including your future job / career / university plans.</p>
				<textarea name="why_want_to_do_course" cols="40" rows="10" maxlength="1500" style="width:800px;" id="s2_why_want_to" required><?php getValue('textarea', 'why_want_to_do_course'); ?></textarea>
                <br />
                <span id="charlimitinfo">1500 characters left</span>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			<?php
				$back_step = $step - 1;
				$back_url = THIS_URL . "?step=$back_step";
				$back_button = "<input type=\"button\" value=\"&lt; Back\" class=\"submit_back\" onclick=\"javascript:window.location.href='$back_url'\" />";
				echo $back_button;
			?>			
				<input type="button" value="Save" class="submit_save" />
				<input type="submit" value="Next &gt;" class="submit" />
			</td>
		</tr>
	</table>
	</form>
	<noscript>Click your browser's back button to get to the previous page</noscript>
	<div id="save_msg"></div>
</div>

<?php
	}

	if ($step == 3) {

?>
<script type="text/javascript">
	$(document).ready(function() {

		function show_hide_questions() {
			var disability_state = $("input[name='do_you_have_a_learning_difficulty_or_disability']:checked").val();
			if (disability_state == 'yes') {
				$("tr.toggle_disability").show();
			} else if (disability_state == 'no') {
				$("tr.toggle_disability").hide();
			} else {
				$("tr.toggle_disability").hide();
			}
		}
		
		show_hide_questions();
		
		// add onclick event for disability radio buttons
		$("input[name='do_you_have_a_learning_difficulty_or_disability']").click(function() {
			show_hide_questions();
		});
		
		// New applicant form check
		$("#s3_support_form").submit(function(e) {
			
			// check if disability is blank
			var disability = $("input[name='do_you_have_a_learning_difficulty_or_disability']:checked").val();
			
			if (disability == 'yes') {
				//
			} else if (disability == 'no') {
				//
			} else {
				alert('Please answer do you have a learning difficulty or disability');
				return false;
			}

				return true;

		});
		
		$('#s3_ld_other').click(function(e) {
			$('#s3_ld_other_difficulty').focus();
		});
		
		$('#s3_d_other').click(function(e) {
			$('#s3_d_other_text').focus();
		});

	});

</script>
<div class="section">
	<form method="post" action="<?php echo THIS_URL; ?>?step=4" id="s3_support_form">
	<h2><span>Section 3</span> &#8211; Support at the College of Haringey, Enfield and North East London</h2>
	<table summary="Support Details">
		<tr>
<td colspan="2" class="note">We are committed to supporting people with learning difficulties, disabilites, mental health or other support needs.</td>
		</tr>
		<tr class="<?php addMissingFieldClass('do_you_have_a_learning_difficulty_or_disability'); ?>">
			<td width="300"><label>Do you have a learning difficulty or disability?:<span class="required">*</span></label></td>
			<td>
				<input type="hidden" name="do_you_have_a_learning_difficulty_or_disability" value="" />
				<input type="radio" name="do_you_have_a_learning_difficulty_or_disability" value="yes" id="s3_ldod_yes" <?php getValue('radio', 'do_you_have_a_learning_difficulty_or_disability', 'yes'); ?> class="radio" /> <label for="s3_ldod_yes">Yes</label>
				<input type="radio" name="do_you_have_a_learning_difficulty_or_disability" value="no" id="s3_ldod_no" <?php getValue('radio', 'do_you_have_a_learning_difficulty_or_disability', 'no'); ?> class="radio" /> <label for="s3_ldod_no">No</label>
			</td>
		</tr>
		<tr class="toggle_disability">
			<td colspan="2" class="note">
			If you have answered yes, please tick the boxes below.<br />If you have a mobility difficulty or visual impairment we need to know so that
			we can protect your health and safety, e.g. in case of emergency exit.
			</td>
		</tr>
		<tr class="toggle_disability">
			<td>
				<table width="340" class="checkboxes">
					<tr>
						<td>
							<h4>Learning difficulty:</h4>
							<input type="hidden" name="learning_difficulty" value="" />
						</td>
					</tr>
					<tr><td><input type="checkbox" name="learning_difficulty[]" value="Autism spectrum disorder (20)" id="s3_ld_autismsd" <?php getValue('checkbox', 'learning_difficulty', 'Autism spectrum disorder (20)'); ?> class="checkbox" /> <label for="s3_ld_autismsd">Autism spectrum disorder</label></td></tr>
					<tr><td><input type="checkbox" name="learning_difficulty[]" value="Dyscalculia (11)" id="s3_ld_dyscalculia" <?php getValue('checkbox', 'learning_difficulty', 'Dyscalculia (11)'); ?> class="checkbox" /> <label for="s3_ld_dyscalculia">Dyscalculia</label></td></tr>
					<tr><td><input type="checkbox" name="learning_difficulty[]" value="Dyslexia (10)" id="s3_ld_dyslexia" <?php getValue('checkbox', 'learning_difficulty', 'Dyslexia (10)'); ?> class="checkbox" /> <label for="s3_ld_dyslexia">Dyslexia</label></td></tr>
					<tr><td><input type="checkbox" name="learning_difficulty[]" value="Moderate learning difficulty (01)" id="s3_ld_mld" <?php getValue('checkbox', 'learning_difficulty', 'Moderate learning difficulty (01)'); ?> class="checkbox" /> <label for="s3_ld_mld">Moderate learning difficulty</label></td></tr>
					<tr><td><input type="checkbox" name="learning_difficulty[]" value="Multiple learning difficulties (90)" id="s3_ld_multld" <?php getValue('checkbox', 'learning_difficulty', 'Multiple learning difficulties (90)'); ?> class="checkbox" /> <label for="s3_ld_multld">Multiple learning difficulties</label></td></tr>
					<tr><td><input type="checkbox" name="learning_difficulty[]" value="Severe learning difficulty (02)" id="s3_ld_sldiff" <?php getValue('checkbox', 'learning_difficulty', 'Severe learning difficulty (02)'); ?> class="checkbox" /> <label for="s3_ld_sldiff">Severe learning difficulty</label></td></tr>
					<tr><td><input type="checkbox" name="learning_difficulty[]" value="Other specific learning difficulty (19)" id="s3_ld_osldiff" <?php getValue('checkbox', 'learning_difficulty', 'Other specific learning difficulty (19)'); ?> class="checkbox" /> <label for="s3_ld_osldiff">Other specific learning difficulty</label></td></tr>
					<tr><td><input type="checkbox" name="learning_difficulty[]" value="Other (97)" id="s3_ld_other" <?php getValue('checkbox', 'learning_difficulty', 'Other (97)'); ?> class="checkbox" /> <label for="s3_ld_other">Other - please specify below</label></td></tr>
					<tr><td><br /><label for="s3_ld_other_difficulty">Other learning difficulty:</label> <input type="text" maxlength="60" name="other_learning_difficulty" class="text" id="s3_ld_other_difficulty" value="<?php getValue('text', 'other_learning_difficulty'); ?>" /></td></tr>
				</table>			
			</td>
			<td>
				<table class="checkboxes">
					<tr>
						<td>
							<h4>Disability:</h4>
							<input type="hidden" name="disability" value="" />
						</td>
					</tr>
					<tr><td><input type="checkbox" name="disability[]" value="Aspergers syndrome (10)" id="s3_d_as" <?php getValue('checkbox', 'disability', 'Aspergers syndrome (10)'); ?> class="checkbox" /> <label for="s3_d_as">Aspergers syndrome</label></td></tr>
					<tr><td><input type="checkbox" name="disability[]" value="Blind / serious visual impairment (01)" id="s3_d_blind" <?php getValue('checkbox', 'disability', 'Blind / serious visual impairment (01)'); ?> class="checkbox" /> <label for="s3_d_blind">Blind / serious visual impairment</label></td></tr>
					<tr><td><input type="checkbox" name="disability[]" value="Deaf / hearing impairment (02)" id="s3_d_deaf" <?php getValue('checkbox', 'disability', 'Deaf / hearing impairment (02)'); ?> class="checkbox" /> <label for="s3_d_deaf">Deaf / hearing impairment</label></td></tr>
					<tr><td><input type="checkbox" name="disability[]" value="Emotional, behavioural difficulties (06)" id="s3_d_emot_behav" <?php getValue('checkbox', 'disability', 'Emotional, behavioural difficulties (06)'); ?> class="checkbox" /> <label for="s3_d_emot_behav">Emotional, behavioural difficulties</label></td></tr>
					<tr><td><input type="checkbox" name="disability[]" value="Mental health difficulty, e.g. depression, serious anxiety (07)" id="s3_d_mhd" <?php getValue('checkbox', 'disability', 'Mental health difficulty, e.g. depression, serious anxiety (07)'); ?> class="checkbox" /> <label for="s3_d_mhd">Mental health difficulty, e.g. depression, serious anxiety</label></td></tr>
					<tr><td><input type="checkbox" name="disability[]" value="Mobility difficulty (03)" id="s3_d_mobility" <?php getValue('checkbox', 'disability', 'Mobility difficulty (03)'); ?> class="checkbox" /> <label for="s3_d_mobility">Mobility difficulty</label></td></tr>
					<tr><td><input type="checkbox" name="disability[]" value="Multiple disabilities (90)" id="s3_d_mult_disab" <?php getValue('checkbox', 'disability', 'Multiple disabilities (90)'); ?> class="checkbox" /> <label for="s3_d_mult_disab">Multiple disabilites</label></td></tr>
					<tr><td><input type="checkbox" name="disability[]" value="Profound complex disabilities (09)" id="s3_d_pcd" <?php getValue('checkbox', 'disability', 'Profound complex disabilities (09)'); ?> class="checkbox" /> <label for="s3_d_pcd">Profound complex disabilities</label></td></tr>
					<tr><td><input type="checkbox" name="disability[]" value="Other physical disability (04)" id="s3_d_opd" <?php getValue('checkbox', 'disability', 'Other physical disability (04)'); ?> class="checkbox" /> <label for="s3_d_opd">Other physical disability</label></td></tr>
					<tr><td><input type="checkbox" name="disability[]" value="Other medical condition, e.g. asthma, diabetes, epilepsy, sickle cell (05)" id="s3_d_omc" <?php getValue('checkbox', 'disability', 'Other medical condition, e.g. asthma, diabetes, epilepsy, sickle cell (05)'); ?> class="checkbox" /> <label for="s3_d_omc">Other medical condition, e.g. asthma, diabetes, epilepsy, sickle cell</label></td></tr>
					<tr><td><input type="checkbox" name="disability[]" value="Temporary disability after illness, e.g. post viral or accident (08)" id="s3_d_temp_disab" <?php getValue('checkbox', 'disability', 'Temporary disability after illness, e.g. post viral or accident (08)'); ?> class="checkbox" /> <label for="s3_d_temp_disab">Temporary disability after illness, e.g. post viral or accident - please describe below</label></td></tr>
					<tr><td><input type="checkbox" name="disability[]" value="Other (97)" id="s3_d_other" <?php getValue('checkbox', 'disability', 'Other (97)'); ?> class="checkbox" /> <label for="s3_d_other">Other - please specify below</label></td></tr>
					<tr><td><br /><label for="s3_d_other_text">Other disability:</label> <input type="text" maxlength="60" name="other_disability" class="text" id="s3_d_other_text" value="<?php getValue('text', 'other_disability'); ?>" /></td></tr>
				</table>						
			</td>
		</tr>
		<tr class="toggle_disability">
			<td><label>Will you need support for your learning difficulty / disability?</label></td>
			<td>
				<input type="radio" name="support_needed" value="yes" id="s3_sup_needed_yes" <?php getValue('radio', 'support_needed', 'yes'); ?> class="radio" /> <label for="s3_sup_needed_yes">Yes</label> 
				<input type="radio" name="support_needed" value="no" id="s3_sup_needed_no" <?php getValue('radio', 'support_needed', 'no'); ?> class="radio"/> <label for="s3_sup_needed_no">No</label>
			</td>
		</tr>
		<tr class="toggle_disability">
			<td><label>Do you have a statement of special educational needs?</label></td>
			<td>
				<input type="radio" name="statement_special_ed_needs" value="yes" id="s3_state_special_ed_yes" <?php getValue('radio', 'statement_special_ed_needs', 'yes'); ?> class="radio" /> <label for="s3_state_special_ed_yes">Yes</label>
				<input type="radio" name="statement_special_ed_needs" value="no" id="s3_state_special_ed_no" <?php getValue('radio', 'statement_special_ed_needs', 'no'); ?> class="radio" /> <label for="s3_state_special_ed_no">No</label>
			</td>
		</tr>
		<tr>
			<td colspan="2">

			<?php
				$back_step = $step - 1;
				$back_url = THIS_URL . "?step=$back_step";
				$back_button = "<input type=\"button\" value=\"&lt; Back\" class=\"submit_back\" onclick=\"javascript:window.location.href='$back_url'\" />";
				echo $back_button;
			?>
				<input type="button" value="Save" class="submit_save" />
				<input type="submit" value="Next &gt;" class="submit" />
			</td>
		</tr>
	</table>
	</form>
	<noscript>Click your browser's back button to get to the previous page</noscript>
	<div id="save_msg"></div>
</div>

<?php
	}

	if ($step == 4) {
	
?>
<div class="section">
	<h2><span>Section 4</span> &#8211; Your Qualifications</h2>
	<table>
		<tr>
			<td colspan="2">Enter your most recent first</td>
		</tr>
	</table>
	
	<form method="post" action="<?php echo THIS_URL; ?>?step=5">
	<table id="qualification_tbl_1" summary="Qualification 1">
		<tr>
			<td  width="170"><h3>Qualification 1</h3></td>
			<td>
				<table style="margin-bottom:15px;">
					<tr>
						<td><label for="s4_name_of_school_1">Name of school / college attended:</label>&nbsp;</td>
						<td><input type="text" name="name_of_school_college_attended_1" maxlength="50" class="text" id="s4_name_of_school_1" value="<?php getValue('text', 'name_of_school_college_attended_1'); ?>" /></td>
					</tr>
					<tr>
						<td><label>Year attended:</label></td>
						<td><table>
								<tr>
									<td align="right"><label for="s4_yatt_from_1">From:</label>&nbsp;</td>
									<td><input type="text" name="year_attended_from_1" maxlength="10" class="date" id="s4_yatt_from_1" value="<?php getValue('text', 'year_attended_from_1'); ?>" /></td>
									<td align="right">&nbsp;&nbsp;<label for="s4_yatt_to_1">To:</label>&nbsp;</td>
									<td><input type="text" name="year_attended_to_1" class="date" maxlength="10" id="s4_yatt_to_1" value="<?php getValue('text', 'year_attended_to_1'); ?>" /></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td><label for="s4_s_1">Subject:</label></td>
						<td><input type="text" name="subject_1" class="text" id="s4_s_1" maxlength="40" value="<?php getValue('text', 'subject_1'); ?>" /></td>
					</tr>
					<tr>
						<td><label for="s4_cgo_1">Course Grades Obtained:</label></td>
						<td><input type="text" name="course_grades_obtained_1" class="text" maxlength="30" id="s4_cgo_1" value="<?php getValue('text', 'course_grades_obtained_1'); ?>" /></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr <?php if (showOrHideQual(2)) { echo 'style="display:none;"'; } ?>>
			<td colspan="2"><a href="#" id="qual_1" class="add_qualification">Add Qualification</a></td>
		</tr>
	</table>
	
	<table id="qualification_tbl_2" <?php if (showOrHideQual(2)) { echo 'style="display:block;" '; } ?> summary="Qualification 2">
		<tr>
			<td  width="170"><h3>Qualification 2</h3></td>
			<td>
				<table style="margin-bottom:15px;">
					<tr>
						<td><label for="s4_name_of_school_2">Name of school / college attended:</label>&nbsp;</td>
						<td><input type="text" name="name_of_school_college_attended_2" maxlength="50" class="text" id="s4_name_of_school_2" value="<?php getValue('text', 'name_of_school_college_attended_2'); ?>" /></td>
					</tr>
					<tr>
						<td><label>Year attended:</label></td>
						<td><table>
								<tr>
									<td align="right"><label for="s4_yatt_from_2">From:</label>&nbsp;</td>
									<td><input type="text" name="year_attended_from_2" maxlength="10" class="date" id="s4_yatt_from_2" value="<?php getValue('text', 'year_attended_from_2'); ?>" /></td>
									<td align="right">&nbsp;&nbsp;<label for="s4_yatt_to_2">To:</label>&nbsp;</td>
									<td><input type="text" name="year_attended_to_2" maxlength="10" class="date" id="s4_yatt_to_2" value="<?php getValue('text', 'year_attended_to_2'); ?>" /></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td><label for="s4_s_2">Subject:</label></td>
						<td><input type="text" name="subject_2" class="text" id="s4_s_2" maxlength="40" value="<?php getValue('text', 'subject_2'); ?>" /></td>
					</tr>
					<tr>
						<td><label for="s4_cgo_2">Course Grades Obtained:</label></td>
						<td><input type="text" name="course_grades_obtained_2" class="text" maxlength="30" id="s4_cgo_2" value="<?php getValue('text', 'course_grades_obtained_2'); ?>" /></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr <?php if (showOrHideQual(3)) { echo 'style="display:none;"'; } ?>>
			<td colspan="2"><a href="#" id="qual_2" class="add_qualification">Add Qualification</a></td>
		</tr>
	</table>
	
	<table id="qualification_tbl_3" <?php if (showOrHideQual(3)) { echo 'style="display:block;" '; } ?> summary="Qualification 3">
		<tr>
			<td  width="170"><h3>Qualification 3</h3></td>
			<td>
				<table style="margin-bottom:15px;">
					<tr>
						<td><label for="s4_name_of_school_3">Name of school / college attended:</label>&nbsp;</td>
						<td><input type="text" name="name_of_school_college_attended_3" maxlength="50" class="text" id="s4_name_of_school_3" value="<?php getValue('text', 'name_of_school_college_attended_3'); ?>" /></td>
					</tr>
					<tr>
						<td><label>Year attended:</label></td>
						<td><table>
								<tr>
									<td align="right"><label for="s4_yatt_from_3">From:</label>&nbsp;</td>
									<td><input type="text" name="year_attended_from_3" maxlength="10" class="date" id="s4_yatt_from_3" value="<?php getValue('text', 'year_attended_from_3'); ?>" /></td>
									<td align="right">&nbsp;&nbsp;<label for="s4_yatt_to_3">To:</label>&nbsp;</td>
									<td><input type="text" name="year_attended_to_3" maxlength="10" class="date" id="s4_yatt_to_3" value="<?php getValue('text', 'year_attended_to_3'); ?>" /></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td><label for="s4_s_3">Subject:</label></td>
						<td><input type="text" name="subject_3" class="text" id="s4_s_3" maxlength="40" value="<?php getValue('text', 'subject_3'); ?>" /></td>
					</tr>
					<tr>
						<td><label for="s4_cgo_3">Course Grades Obtained:</label></td>
						<td><input type="text" name="course_grades_obtained_3" class="text" maxlength="30" id="s4_cgo_3" value="<?php getValue('text', 'course_grades_obtained_3'); ?>" /></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr <?php if (showOrHideQual(4)) { echo 'style="display:none;"'; } ?>>
			<td colspan="2"><a href="#" id="qual_3" class="add_qualification">Add Qualification</a></td>
		</tr>
	</table>
	
	<table id="qualification_tbl_4" <?php if (showOrHideQual(4)) { echo 'style="display:block;" '; } ?> summary="Qualification 4">
		<tr>
			<td  width="170"><h3>Qualification 4</h3></td>
			<td>
				<table style="margin-bottom:15px;">
					<tr>
						<td><label for="s4_name_of_school_4">Name of school / college attended:</label>&nbsp;</td>
						<td><input type="text" name="name_of_school_college_attended_4" maxlength="50" class="text" id="s4_name_of_school_4" value="<?php getValue('text', 'name_of_school_college_attended_4'); ?>" /></td>
					</tr>
					<tr>
						<td><label>Year attended:</label></td>
						<td><table>
								<tr>
									<td align="right"><label for="s4_yatt_from_4">From:</label>&nbsp;</td>
									<td><input type="text" name="year_attended_from_4" maxlength="10" class="date" id="s4_yatt_from_4" value="<?php getValue('text', 'year_attended_from_4'); ?>" /></td>
									<td align="right">&nbsp;&nbsp;<label for="s4_yatt_to_4">To:</label>&nbsp;</td>
									<td><input type="text" name="year_attended_to_4" maxlength="10" class="date" id="s4_yatt_to_4" value="<?php getValue('text', 'year_attended_to_4'); ?>" /></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td><label for="s4_s_4">Subject:</label></td>
						<td><input type="text" name="subject_4" class="text" id="s4_s_4" maxlength="40" value="<?php getValue('text', 'subject_4'); ?>" /></td>
					</tr>
					<tr>
						<td><label for="s4_cgo_4">Course Grades Obtained:</label></td>
						<td><input type="text" name="course_grades_obtained_4" class="text" maxlength="30" id="s4_cgo_4" value="<?php getValue('text', 'course_grades_obtained_4'); ?>" /></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr <?php if (showOrHideQual(5)) { echo 'style="display:none;"'; } ?>>
			<td colspan="2"><a href="#" id="qual_4" class="add_qualification">Add Qualification</a></td>
		</tr>
	</table>
	
	<table id="qualification_tbl_5" <?php if (showOrHideQual(5)) { echo 'style="display:block;" '; } ?> summary="Qualification 5">
		<tr>
			<td  width="170"><h3>Qualification 5</h3></td>
			<td>
				<table style="margin-bottom:15px;">
					<tr>
						<td><label for="s4_name_of_school_5">Name of school / college attended:</label>&nbsp;</td>
						<td><input type="text" name="name_of_school_college_attended_5" maxlength="50" class="text" id="s4_name_of_school_5" value="<?php getValue('text', 'name_of_school_college_attended_5'); ?>" /></td>
					</tr>
					<tr>
						<td><label>Year attended:</label></td>
						<td><table>
								<tr>
									<td align="right"><label for="s4_yatt_from_5">From:</label>&nbsp;</td>
									<td><input type="text" name="year_attended_from_5" maxlength="10" class="date" id="s4_yatt_from_5" value="<?php getValue('text', 'year_attended_from_5'); ?>" /></td>
									<td align="right">&nbsp;&nbsp;<label for="s4_yatt_to_5">To:</label>&nbsp;</td>
									<td><input type="text" name="year_attended_to_5" maxlength="10" class="date" id="s4_yatt_to_5" value="<?php getValue('text', 'year_attended_to_5'); ?>" /></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td><label for="s4_s_5">Subject:</label></td>
						<td><input type="text" name="subject_5" class="text" id="s4_s_5" maxlength="40" value="<?php getValue('text', 'subject_5'); ?>" /></td>
					</tr>
					<tr>
						<td><label for="s4_cgo_5">Course Grades Obtained:</label></td>
						<td><input type="text" name="course_grades_obtained_5" maxlength="30" class="text" id="s4_cgo_5" value="<?php getValue('text', 'course_grades_obtained_5'); ?>" /></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr <?php if (showOrHideQual(6)) { echo 'style="display:none;"'; } ?>>
			<td colspan="2"><a href="#" id="qual_5" class="add_qualification">Add Qualification</a></td>
		</tr>
	</table>
	
	<table id="qualification_tbl_6" <?php if (showOrHideQual(6)) { echo 'style="display:block;" '; } ?> summary="Qualification 6">
		<tr>
			<td  width="170"><h3>Qualification 6</h3></td>
			<td>
				<table style="margin-bottom:15px;">
					<tr>
						<td><label for="s4_name_of_school_6">Name of school / college attended:</label>&nbsp;</td>
						<td><input type="text" name="name_of_school_college_attended_6" maxlength="50" class="text" id="s4_name_of_school_6" value="<?php getValue('text', 'name_of_school_college_attended_6'); ?>" /></td>
					</tr>
					<tr>
						<td><label>Year attended:</label></td>
						<td><table>
								<tr>
									<td align="right"><label for="s4_yatt_from_6">From:</label>&nbsp;</td>
									<td><input type="text" name="year_attended_from_6" maxlength="10" class="date" id="s4_yatt_from_6" value="<?php getValue('text', 'year_attended_from_6'); ?>" /></td>
									<td align="right">&nbsp;&nbsp;<label for="s4_yatt_to_6">To:</label>&nbsp;</td>
									<td><input type="text" name="year_attended_to_6" maxlength="10" class="date" id="s4_yatt_to_6" value="<?php getValue('text', 'year_attended_to_6'); ?>" /></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td><label for="s4_s_6">Subject:</label></td>
						<td><input type="text" name="subject_6" class="text" maxlength="40" id="s4_s_6" value="<?php getValue('text', 'subject_6'); ?>" /></td>
					</tr>
					<tr>
						<td><label for="s4_cgo_6">Course Grades Obtained:</label></td>
						<td><input type="text" name="course_grades_obtained_6" maxlength="30" class="text" id="s4_cgo_6" value="<?php getValue('text', 'course_grades_obtained_6'); ?>" /></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr <?php if (showOrHideQual(7)) { echo 'style="display:none;"'; } ?>>
			<td colspan="2"><a href="#" id="qual_6" class="add_qualification">Add Qualification</a></td>
		</tr>
	</table>
	
	<table id="qualification_tbl_7" <?php if (showOrHideQual(7)) { echo 'style="display:block;" '; } ?> summary="Qualification 7">
		<tr>
			<td  width="170"><h3>Qualification 7</h3></td>
			<td>
				<table style="margin-bottom:15px;">
					<tr>
						<td><label for="s4_name_of_school_7">Name of school / college attended:</label>&nbsp;</td>
						<td><input type="text" name="name_of_school_college_attended_7" maxlength="50" class="text" id="s4_name_of_school_7" value="<?php getValue('text', 'name_of_school_college_attended_7'); ?>" /></td>
					</tr>
					<tr>
						<td><label>Year attended:</label></td>
						<td><table>
								<tr>
									<td align="right"><label for="s4_yatt_from_7">From:</label>&nbsp;</td>
									<td><input type="text" name="year_attended_from_7" maxlength="10" class="date" id="s4_yatt_from_7" value="<?php getValue('text', 'year_attended_from_7'); ?>" /></td>
									<td align="right">&nbsp;&nbsp;<label for="s4_yatt_to_7">To:</label>&nbsp;</td>
									<td><input type="text" name="year_attended_to_7" maxlength="10" class="date" id="s4_yatt_to_7" value="<?php getValue('text', 'year_attended_to_7'); ?>" /></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td><label for="s4_s_7">Subject:</label></td>
						<td><input type="text" name="subject_7" class="text" maxlength="40" id="s4_s_7" value="<?php getValue('text', 'subject_7'); ?>" /></td>
					</tr>
					<tr>
						<td><label for="s4_cgo_7">Course Grades Obtained:</label></td>
						<td><input type="text" name="course_grades_obtained_7" maxlength="30" class="text" id="s4_cgo_7" value="<?php getValue('text', 'course_grades_obtained_7'); ?>" /></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr <?php if (showOrHideQual(8)) { echo 'style="display:none;"'; } ?>>
			<td colspan="2"><a href="#" id="qual_7" class="add_qualification">Add Qualification</a></td>
		</tr>
	</table>
	
	<table id="qualification_tbl_8" <?php if (showOrHideQual(8)) { echo 'style="display:block;" '; } ?> summary="Qualification 8">
		<tr>
			<td  width="170"><h3>Qualification 8</h3></td>
			<td>
				<table style="margin-bottom:15px;">
					<tr>
						<td><label for="s4_name_of_school_8">Name of school / college attended:</label>&nbsp;</td>
						<td><input type="text" name="name_of_school_college_attended_8" maxlength="50" class="text" id="s4_name_of_school_8" value="<?php getValue('text', 'name_of_school_college_attended_8'); ?>" /></td>
					</tr>
					<tr>
						<td><label>Year attended:</label></td>
						<td><table>
								<tr>
									<td align="right"><label for="s4_yatt_from_8">From:</label>&nbsp;</td>
									<td><input type="text" name="year_attended_from_8" maxlength="10" class="date" id="s4_yatt_from_8" value="<?php getValue('text', 'year_attended_from_8'); ?>" /></td>
									<td align="right">&nbsp;&nbsp;<label for="s4_yatt_to_8">To:</label>&nbsp;</td>
									<td><input type="text" name="year_attended_to_8" maxlength="10" class="date" id="s4_yatt_to_8" value="<?php getValue('text', 'year_attended_to_8'); ?>" /></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td><label for="s4_s_8">Subject:</label></td>
						<td><input type="text" name="subject_8" class="text" maxlength="40" id="s4_s_8" value="<?php getValue('text', 'subject_8'); ?>" /></td>
					</tr>
					<tr>
						<td><label for="s4_cgo_8">Course Grades Obtained:</label></td>
						<td><input type="text" name="course_grades_obtained_8" maxlength="30" class="text" id="s4_cgo_8" value="<?php getValue('text', 'course_grades_obtained_8'); ?>" /></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr <?php if (showOrHideQual(9)) { echo 'style="display:none;"'; } ?>>
			<td colspan="2"><a href="#" id="qual_8" class="add_qualification">Add Qualification</a></td>
		</tr>
	</table>
	
	<table id="qualification_tbl_9" <?php if (showOrHideQual(9)) { echo 'style="display:block;" '; } ?> summary="Qualification 9">
		<tr>
			<td  width="170"><h3>Qualification 9</h3></td>
			<td>
				<table style="margin-bottom:15px;">
					<tr>
						<td><label for="s4_name_of_school_9">Name of school / college attended:</label>&nbsp;</td>
						<td><input type="text" name="name_of_school_college_attended_9" maxlength="50" class="text" id="s4_name_of_school_9" value="<?php getValue('text', 'name_of_school_college_attended_9'); ?>" /></td>
					</tr>
					<tr>
						<td><label>Year attended:</label></td>
						<td><table>
								<tr>
									<td align="right"><label for="s4_yatt_from_9">From:</label>&nbsp;</td>
									<td><input type="text" name="year_attended_from_9" maxlength="10" class="date" id="s4_yatt_from_9" value="<?php getValue('text', 'year_attended_from_9'); ?>" /></td>
									<td align="right">&nbsp;&nbsp;<label for="s4_yatt_to_9">To:</label>&nbsp;</td>
									<td><input type="text" name="year_attended_to_9" maxlength="10" class="date" id="s4_yatt_to_9" value="<?php getValue('text', 'year_attended_to_9'); ?>" /></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td><label for="s4_s_9">Subject:</label></td>
						<td><input type="text" name="subject_9" class="text" maxlength="40" id="s4_s_9" value="<?php getValue('text', 'subject_9'); ?>" /></td>
					</tr>
					<tr>
						<td><label for="s4_cgo_9">Course Grades Obtained:</label></td>
						<td><input type="text" name="course_grades_obtained_9" maxlength="30" class="text" id="s4_cgo_9" value="<?php getValue('text', 'course_grades_obtained_9'); ?>" /></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr <?php if (showOrHideQual(10)) { echo 'style="display:none;"'; } ?>>
			<td colspan="2"><a href="#" id="qual_9" class="add_qualification">Add Qualification</a></td>
		</tr>
	</table>
	
	<table id="qualification_tbl_10" <?php if (showOrHideQual(10)) { echo 'style="display:block;" '; } ?> summary="Qualification 10">
		<tr>
			<td  width="170"><h3>Qualification 10</h3></td>
			<td>
				<table style="margin-bottom:15px;">
					<tr>
						<td><label for="s4_name_of_school_10">Name of school / college attended:</label>&nbsp;</td>
						<td><input type="text" name="name_of_school_college_attended_10" maxlength="50" class="text" id="s4_name_of_school_10" value="<?php getValue('text', 'name_of_school_college_attended_10'); ?>" /></td>
					</tr>
					<tr>
						<td><label>Year attended:</label></td>
						<td><table>
								<tr>
									<td align="right"><label for="s4_yatt_from_10">From:</label>&nbsp;</td>
									<td><input type="text" name="year_attended_from_10" maxlength="10" class="date" id="s4_yatt_from_10" value="<?php getValue('text', 'year_attended_from_10'); ?>" /></td>
									<td align="right">&nbsp;&nbsp;<label for="s4_yatt_to_10">To:</label>&nbsp;</td>
									<td><input type="text" name="year_attended_to_10" maxlength="10" class="date" id="s4_yatt_to_10" value="<?php getValue('text', 'year_attended_to_10'); ?>" /></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td><label for="s4_s_10">Subject:</label></td>
						<td><input type="text" name="subject_10" class="text" id="s4_s_10" maxlength="40" value="<?php getValue('text', 'subject_10'); ?>" /></td>
					</tr>
					<tr>
						<td><label for="s4_cgo_10">Course Grades Obtained:</label></td>
						<td><input type="text" name="course_grades_obtained_10" class="text" maxlength="30" id="s4_cgo_10" value="<?php getValue('text', 'course_grades_obtained_10'); ?>" /></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
		
	<table>
		<tr>
			<td colspan="2">
				<?php
				$back_step = $step - 1;
				$back_url = THIS_URL . "?step=$back_step";
				$back_button = "<input type=\"button\" value=\"&lt; Back\" class=\"submit_back\" onclick=\"javascript:window.location.href='$back_url'\" />";
				echo $back_button;
				?>
				<input type="button" value="Save" class="submit_save" />
				<input type="submit" value="Next &gt;" class="submit" />
			</td>
		</tr>
	</table>
	</form>
	<noscript>Click your browser's back button to get to the previous page</noscript>
	<div id="save_msg"></div>
</div>

<?php
	}
?>
<?php 

	if ($step == 5) {

?>
<script type="text/javascript">
	$(document).ready(function() {

        function limitChars(textid, limit, infodiv) {
            var text = $('#'+textid).val();    
            var textlength = text.length;
            if (textlength > limit) {
                $('#' + infodiv).html(''+limit+' character limit reached');
                $('#'+textid).val(text.substr(0,limit));
                return false;
            } else {
                $('#' + infodiv).html(''+ (limit - textlength) +' characters left');
                return true;
            }
        }

        limitChars('s5_relevant_skills', 1000, 'charlimitinfo');

        $(function(){
             $('#s5_relevant_skills').keyup(function(){
                 limitChars('s5_relevant_skills', 1000, 'charlimitinfo');
             })
        });

		// New applicant form check
		$("#s5_employment_and_exp").submit(function(e) {

			// check if employment is blank
			var employment = $("input[name='are_you_employed']:checked").val();
			if (employment == 'yes') {
			} else if (employment == 'no') {
			} else {
				alert('Please answer are you employed');
				return false;
			}
			
			// check if volunteer is blank
			var vteer = $("input[name='are_you_working_as_a_volunteer']:checked").val();
			if (vteer == 'yes') {
			} else if (vteer == 'no') {
			} else {
				alert('Please answer are you working as a volunteer');
				return false;
			}
		
			// check if experience and skills is blank
			var name = $("#s5_relevant_skills").val();
			if (name == '') {
				alert('Please enter your experience and skills');
				$("#s5_relevant_skills").focus();
				return false;
			}

			return true;

		});
			
	});
</script>
<div class="section">
	<form method="post" action="<?php echo THIS_URL; ?>?step=6" id="s5_employment_and_exp">
	<h2><span>Section 5</span> &#8211; Your Employment and Experience</h2>
	<table summary="Your Employment and Experience">
		<tr class="<?php addMissingFieldClass('are_you_employed'); ?>">
			<td width="270"><label>Are you employed?:<span class="required">*</span></label></td>
			<td>
				<input type="hidden" name="are_you_employed" value="" />
				<input type="radio" name="are_you_employed" value="yes" id="s5_ru_employed_yes" <?php getValue('radio', 'are_you_employed', 'yes'); ?> class="radio"/> <label for="s5_ru_employed_yes">Yes</label>
				<input type="radio" name="are_you_employed" value="no" id="s5_ru_employed_no" <?php getValue('radio', 'are_you_employed', 'no'); ?> class="radio"/> <label for="s5_ru_employed_no">No</label>
			</td>
		</tr>
		<tr class="<?php addMissingFieldClass('are_you_working_as_a_volunteer'); ?>">
			<td><label>Are you working as a volunteer?:<span class="required">*</span></label></td>
			<td>
				<input type="hidden" name="are_you_working_as_a_volunteer" value="" />
				<input type="radio" name="are_you_working_as_a_volunteer" value="yes" id="s5_ru_volunteer_yes" <?php getValue('radio', 'are_you_working_as_a_volunteer', 'yes'); ?> class="radio" /> <label for="s5_ru_volunteer_yes">Yes</label>
				<input type="radio" name="are_you_working_as_a_volunteer" value="no" id="s5_ru_volunteer_no" <?php getValue('radio', 'are_you_working_as_a_volunteer', 'no'); ?> class="radio" /> <label for="s5_ru_volunteer_no">No</label>
			</td>
		</tr>
		<tr>
			<td><label for="s5_job_title">Job Title:</label></td>
			<td><input type="text" name="job_title" class="text capitalise" maxlength="50" id="s5_job_title" value="<?php getValue('text', 'job_title'); ?>" /></td>
		</tr>
		<tr>
			<td><label for="s5_employer">Employer:</label></td>
			<td><input type="text" name="employer" class="text capitalise" maxlength="100" id="s5_employer" value="<?php getValue('text', 'employer'); ?>" /></td>
		</tr>
		<tr>
			<td><label for="s5_job_address">Address:</label></td>
			<td><textarea name="job_address" id="s5_job_address" maxlength="200" class="capitalise"><?php getValue('text', 'job_address'); ?></textarea></td>
		</tr>
		<tr>
			<td><label for="s5_job_postcode uppercase">Postcode:</label></td>
			<td><input type="text" name="job_postcode" class="text uppercase" maxlength="10" id="s5_job_postcode" value="<?php getValue('textarea', 'job_postcode'); ?>" /><br /><br /></td>
		</tr>
		<tr class="<?php addMissingFieldClass('relevant_skills_and_experience'); ?>">
			<td  width="300">
				<label for="s5_relevant_skills">What experience and skills do you have that are relevant to your course?:<span class="required">*</span><br /><br />
				Include any paid / voluntary work and any responsibilites you have had.</label>
			</td>
            <td>
                <textarea name="relevant_skills_and_experience" cols="40" maxlength="1000" rows="10" id="s5_relevant_skills" style="width:630px;" required><?php getValue('textarea', 'relevant_skills_and_experience'); ?></textarea>
                <span id="charlimitinfo">1000 characters left</span>
            </td>
		</tr>
		<tr>
			<td colspan="2">
			<?php
				$back_step = $step - 1;
				$back_url = THIS_URL . "?step=$back_step";
				$back_button = "<input type=\"button\" value=\"&lt; Back\" class=\"submit_back\" onclick=\"javascript:window.location.href='$back_url'\" />";
				echo $back_button;
			?>
				<input type="button" value="Save" class="submit_save" />
				<input type="submit" value="Next &gt;" class="submit" />
			</td>
		</tr>
	</table>
	</form>
	<noscript>Click your browser's back button to get to the previous page</noscript>
	<div id="save_msg"></div>
</div>

<?php
	}

	if ($step == 6) {
?>
<script type="text/javascript">
	$(document).ready(function() {

		// If NO selected on permanent right to live in UK: show extra questions
		if ($('#s6_settled_no').is(':checked') == true) {
			$('#residence_questions').show();
		} else {
			$('#residence_questions').hide();
		}
		// New applicant form check
		$("#s6_residence_fee_status").submit(function(e) {

			// check if experience and skills is blank
			var nationality = $("#s6_nationality").val();
			if (nationality == '') {
				alert('Please answer \'What is your nationality?\'');
				$("#s6_nationality").focus();
				return false;
			}
			
			// check if right to live in UK is blank
			var right_to_live = $("input[name='permanent_right_to_live_in_uk']:checked").val();
			if (right_to_live == 'yes') {
			} else if (right_to_live == 'no') {
			} else {
				alert('Please answer \'Do you have the permanent right to live in the UK?\'');
				return false;
			}
			
			// check if international student is blank
			var international_student = $("input[name='are_you_an_international_student']:checked").val();
			if (international_student == 'yes') {
			} else if (international_student == 'no') {
			} else {
				alert('Please answer \'Are you an international student?\'');
				return false;
			}

			return true;

		});
			
	});
</script>
<div class="section">
	<form method="post" action="<?php echo THIS_URL; ?>?step=7" id="s6_residence_fee_status">
	<h2><span>Section 6</span> &#8211; Residence and Fee Status</h2>
	<table summary="Residence and Fee Status">
		<tr>
			<td colspan="2">
				<h2>Residence status</h2>
				<span class="note">Please note: all learners need to show a passport, birth certificate or Home Office/UK Border Agency documents when you enrol.</span>
			</td>
		</tr>
		<tr class="<?php addMissingFieldClass('nationality'); ?>">
			<td width="350"><label for="s6_nationality">What is your nationality?:<span class="required">*</span></label></td>
			<td><input type="text" name="nationality" class="text capitalise" maxlength="40" id="s6_nationality" value="<?php getValue('text', 'nationality'); ?>" required /></td>
		</tr>
		<tr class="<?php addMissingFieldClass('permanent_right_to_live_in_uk'); ?>">
			<td><label>Do you have the permanent right to live in the UK?:<span class="required">*</span><br /><span class="note">(e.g. British citizen, settled status, indefinite leave)</span></label></td>
			<td>
				<input type="radio" name="permanent_right_to_live_in_uk" value="yes" id="s6_settled_yes" <?php getValue('radio', 'permanent_right_to_live_in_uk', 'yes'); ?> class="radio"/><label for="s6_settled_yes">Yes</label>
				<input type="radio" name="permanent_right_to_live_in_uk" value="no" id="s6_settled_no" <?php getValue('radio', 'permanent_right_to_live_in_uk', 'no'); ?> class="radio"/><label for="s6_settled_no">No</label>
			</td>
		</tr>
		<tr>
			<td><label>If yes, have you been resident in the UK for the past three years?:</label></td>
			<td>
				<input type="radio" name="have_been_a_resident_past_3_years" value="yes" id="s6_res_past3yrs_yes" <?php getValue('radio', 'have_been_a_resident_past_3_years', 'yes'); ?> class="radio" /><label for="s6_res_past3yrs_yes">Yes</label>  
				<input type="radio" name="have_been_a_resident_past_3_years" value="no" id="s6_res_past3yrs_no" <?php getValue('radio', 'have_been_a_resident_past_3_years', 'no'); ?> class="radio" /><label for="s6_res_past3yrs_no">No</label>  
			</td>
		</tr>
	</table>
	<table id="residence_questions">
		<tr>
			<td width="350"><label for="s6_cour">If not resident in the UK, please give your country of usual/permanent residence:</label></td>
			<td><input type="text" name="country_of_usual_residence" maxlength="50" class="text capitalise" id="s6_cour" value="<?php getValue('text', 'country_of_usual_residence'); ?>" /></td>
		</tr>
		<tr>
			<td><label>Are you an EU/EEA citizen?</label></td>
			<td>
				<input type="radio" name="EU_EAA_citizen" value="yes" id="s6_eu_eaa_citizen_yes" <?php getValue('radio', 'EU_EAA_citizen', 'yes'); ?> class="radio" /><label for="s6_eu_eaa_citizen_yes">Yes</label>  
				<input type="radio" name="EU_EAA_citizen" value="no" id="s6_eu_eaa_citizen_no" <?php getValue('radio', 'EU_EAA_citizen', 'no'); ?> class="radio" /><label for="s6_eu_eaa_citizen_no">No</label>  
			</td>
		</tr>
		<tr>
			<td><label for="s6_date_of_uk_arrival">If born ouside the UK/EU/EEA, your date of arrival in the UK/EU/EEA:</label></td>
			<td><input type="text" name="date_of_arrival_in_uk_eu_eea" maxlength="10" class="date" id="s6_date_of_uk_arrival" value="<?php getValue('text', 'date_of_arrival_in_uk_eu_eea'); ?>" /> <span class="note">(dd/mm/yyyy)</span></td>
		</tr>
		<tr>
			<td><label>Are you a refugee with full refugee status/indefinite leave to remain?:</label></td>
			<td>
				<input type="radio" name="are_you_a_refugee" value="yes" id="s6_refugee_yes" <?php getValue('radio', 'are_you_a_refugee', 'yes'); ?> class="radio" /><label for="s6_refugee_yes">Yes</label>  
				<input type="radio" name="are_you_a_refugee" value="no" id="s6_refugee_no" <?php getValue('radio', 'are_you_a_refugee', 'no'); ?> class="radio" /><label for="s6_refugee_no">No</label> 
			</td>
		</tr>
		<tr>
			<td><label>Do you have limited leave to enter/remain as a refugee including exceptional/discretionary leave, humanitarian protection?:</label></td>
			<td>
				<input type="radio" name="do_you_have_limited_leave_as_a_refugee" value="yes" id="s6_limited_leave_yes" <?php getValue('radio', 'do_you_have_limited_leave_as_a_refugee', 'yes'); ?> class="radio" /><label for="s6_limited_leave_yes">Yes</label>  
				<input type="radio" name="do_you_have_limited_leave_as_a_refugee" value="no" id="s6_limited_leave_no" <?php getValue('radio', 'do_you_have_limited_leave_as_a_refugee', 'no'); ?> class="radio" /><label for="s6_limited_leave_no">No</label> 
			</td>
		</tr>
		<tr>
			<td><label>Are you an asylum seeker?:</label></td>
			<td>
				<input type="radio" name="asylum_seeker" value="yes" id="s6_asylum_skr_yes" <?php getValue('radio', 'asylum_seeker', 'yes'); ?> class="radio" /><label for="s6_asylum_skr_yes">Yes</label> 
				<input type="radio" name="asylum_seeker" value="no" id="s6_asylum_skr_no" <?php getValue('radio', 'asylum_seeker', 'no'); ?> class="radio" /><label for="s6_asylum_skr_no">No</label>
			</td>
		</tr>
		<tr>
			<td><label for="s6_asylum_when_app">If yes, when did you apply for asylum?</label></td>
			<td><input type="text" name="application_for_asylum_date" maxlength="10" class="date" id="s6_asylum_when_app" value="<?php getValue('text', 'application_for_asylum_date'); ?>" /> <span class="note">(dd/mm/yyyy)</span></td>
		</tr>
		<tr>
			<td><label>Are you receiving support from the UK Border Agency Asylum Support Service or Social Services?:</label></td>
			<td>
				<input type="radio" name="support_from_ukba_asylum_or_social_services" value="yes" id="s6_supp_from_as_or_ss_yes" <?php getValue('radio', 'support_from_ukba_asylum_or_social_services', 'yes'); ?> class="radio" /><label for="s6_supp_from_as_or_ss_yes">Yes</label>
				<input type="radio" name="support_from_ukba_asylum_or_social_services" value="no" id="s6_supp_from_as_or_ss_no" <?php getValue('radio', 'support_from_ukba_asylum_or_social_services', 'no'); ?> class="radio" /><label for="s6_supp_from_as_or_ss_no">No</label>
			</td>
		</tr>
		<tr>
			<td><label>Do you have a visa to stay in the UK?:</label></td>
			<td>
				<input type="radio" name="do_you_have_a_visa_to_stay_in_uk" value="yes" id="s6_visa_to_stay_yes" <?php getValue('radio', 'do_you_have_a_visa_to_stay_in_uk', 'yes'); ?> class="radio" /><label for="s6_visa_to_stay_yes">Yes</label>
				<input type="radio" name="do_you_have_a_visa_to_stay_in_uk" value="no" id="s6_visa_to_stay_no" <?php getValue('radio', 'do_you_have_a_visa_to_stay_in_uk', 'no'); ?> class="radio" /><label for="s6_visa_to_stay_no">No</label>
			</td>
		</tr>
		<tr>
			<td>If yes, please tick one of the boxes:</td>
			<td>
				<input type="radio" name="visa_type" value="Spouse visa" id="s6_spouse_visa" <?php getValue("radio", "visa_type", "Spouse visa"); ?> class="radio" /> <label for="s6_spouse_visa">Spouse visa</label>
				<input type="radio" name="visa_type" value="Student visa" id="s6_student_visa" <?php getValue("radio", "visa_type", "Student visa"); ?> class="radio" /> <label for="s6_student_visa">Student visa</label>
				<input type="radio" name="visa_type" value="Visitor's visa" id="s6_visitors_visa" <?php getValue("radio", "visa_type", "Visitor's visa"); ?> class="radio" /> <label for="s6_visitors_visa">Visitor's visa</label>
				<input type="radio" name="visa_type" value="Other visa" id="s6_other_visa" <?php getValue("radio", "visa_type", "Other visa"); ?> class="radio" /> <label for="s6_other_visa">Other visa<span class="note"> - please describe:</span></label>
			</td>
		</tr>
		<tr>
			<td><label for="s6_visa_type_other">Other visa:</label></td>
			<td><input type="text" name="visa_type_other" class="text" maxlength="40" id="s6_visa_type_other" value="<?php getValue('text', 'visa_type_other'); ?>" /></td>
		</tr>
		<tr>
			<td><label for="s6_visa_issue_date">Visa issue date:</label></td>
			<td><input type="text" name="visa_issue_date" class="text" maxlength="10" id="s6_visa_issue_date" value="<?php getValue('text', 'visa_issue_date'); ?>" /></td>
		</tr>
		<tr>
			<td><label for="s6_visa_exp_date">Visa expiry date:</label></td>
			<td><input type="text" name="visa_expiry_date" class="text" maxlength="10" id="s6_visa_exp_date" value="<?php getValue('text', 'visa_expiry_date'); ?>" /></td>
		</tr>
		</table>
		
		<table>
		<tr>
			<td colspan="2"><h2 class="fee_status">Fee status</h2></td>
		</tr>
		<tr class="<?php addMissingFieldClass('are_you_an_international_student'); ?>">
			<td width="350"><label for="s6_international_stud">Are you an international student?:<span class="required">*</span></label></td>
			<td>
				<input type="radio" name="are_you_an_international_student" value="yes" id="s6_international_stud_yes" <?php getValue('radio', 'are_you_an_international_student', 'yes'); ?> class="radio" /><label for="s6_international_stud_yes">Yes</label>
				<input type="radio" name="are_you_an_international_student" value="no" id="s6_international_stud_no" <?php getValue('radio', 'are_you_an_international_student', 'no'); ?> class="radio" /><label for="s6_international_stud_no">No</label>
			</td>
		</tr>
		<!--
		<tr>
			<td><label>Are you receiving any of the following benefits?:</label></td>
			<td>
				<table class="checkboxes">
					<tr>
						<td>
							<input type="hidden" name="benefits_receiving" value="" />
							<input type="checkbox" name="benefits_receiving[]" value="Employment and support allowance" id="s6_ben_rec_emp_supp_allow" <?php getValue("checkbox", "benefits_receiving", "Employment and support allowance"); ?> class="checkbox" /> <label for="s6_ben_rec_emp_supp_allow">Employment and support allowance</label> <br />
							<input type="checkbox" name="benefits_receiving[]" value="Council tax benefit" id="s6_ben_rec_ctax" <?php getValue("checkbox", "benefits_receiving", "Council tax benefit"); ?> class="checkbox" /> <label for="s6_ben_rec_ctax">Council tax benefit</label> <br />
							<input type="checkbox" name="benefits_receiving[]" value="Housing benefit" id="s6_ben_rec_housing" <?php getValue("checkbox", "benefits_receiving", "Housing benefit"); ?> class="checkbox" /> <label for="s6_ben_rec_housing">Housing benefit</label> <br />
							<input type="checkbox" name="benefits_receiving[]" value="Income support" id="s6_ben_rec_inc_supp" <?php getValue("checkbox", "benefits_receiving", "Income support"); ?> class="checkbox" /> <label for="s6_ben_rec_inc_supp">Income support</label> <br />
							<input type="checkbox" name="benefits_receiving[]" value="Jobseeker's allowance" id="s6_ben_rec_job_allow" <?php getValue("checkbox", "benefits_receiving", "Jobseeker's allowance"); ?> class="checkbox" /> <label for="s6_ben_rec_job_allow">Jobseeker's allowance</label> <br />
							<input type="checkbox" name="benefits_receiving[]" value="Pension credit (Guarantee Credit)" id="s6_ben_rec_pens_cred" <?php getValue("checkbox", "benefits_receiving", "Pension credit (Guarantee Credit)"); ?> class="checkbox" /> <label for="s6_ben_rec_pens_cred">Pension credit (Guarantee Credit)</label> <br />
							<input type="checkbox" name="benefits_receiving[]" value="Working tax credit (household income below 15,276)" id="s6_ben_rec_wtc" <?php getValue("checkbox", "benefits_receiving", "Working tax credit (household income below 15,276)"); ?> class="checkbox" /> <label for="s6_ben_rec_wtc">Working tax credit (household income below &pound;15,276)</label> <br />
							<input type="checkbox" name="benefits_receiving[]" value="Unwaged dependant of anyone on these benefits" id="s6_ben_rec_udoaotb" <?php getValue("checkbox", "benefits_receiving", "Unwaged dependant of anyone on these benefits"); ?> class="checkbox" /> <label for="s6_ben_rec_udoaotb">Unwaged dependant of anyone on these benefits</label> <br />
						</td>
					</tr>
				</table>
			</td>
		</tr>
		-->
		<tr>
			<td colspan="2">
			<?php
				$back_step = $step - 1;
				$back_url = THIS_URL . "?step=$back_step";
				$back_button = "<input type=\"button\" value=\"&lt; Back\" class=\"submit_back\" onclick=\"javascript:window.location.href='$back_url'\" />";
				echo $back_button;
			?>
				<input type="button" value="Save" class="submit_save" />
				<input type="submit" value="Next &gt;" class="submit" />
			</td>
		</tr>
		</table>
	</form>
	<noscript>Click your browser's back button to get to the previous page</noscript>
	<div id="save_msg"></div>
</div>
<?php 
	}

	if ($step == 7) {

?>
<script type="text/javascript">
	$(document).ready(function() {

        function limitChars(textid, limit, infodiv) {
            var text = $('#'+textid).val();    
            var textlength = text.length;
            if (textlength > limit) {
                $('#' + infodiv).html(''+limit+' character limit reached');
                $('#'+textid).val(text.substr(0,limit));
                return false;
            } else {
                $('#' + infodiv).html(''+ (limit - textlength) +' characters left');
                return true;
            }
        }

        limitChars('s7_how_can_we_improve_the_form', 1000, 'charlimitinfo');
        $(function(){
             $('#s7_how_can_we_improve_the_form').keyup(function(){
                 limitChars('s7_how_can_we_improve_the_form', 1000, 'charlimitinfo');
             })
        });

		$('#s7_hh_other_pspec').click(function(e) {
			$('#s7_hh_other').focus();
		});
		
		// New applicant form check
		$("#s7_how_heard_about").submit(function(e) {
			
			// check if how heard is blank
			var chars = 0;
			var how_heard = '';
			how_heard = $("table.checkboxes input[type='checkbox']:checked");
			if (how_heard != '') {
				chars = how_heard.length;
			}
			
			if (chars == 0) {
				alert('Please tick how you heard about the course');
				return false;
			}

			// nkowald - 2011-10-06 - Form check correct information 
			if (!$("#s7_confirm_correct_info").attr('checked')) {
				alert('Please confirm that you have provided correct information to proceed');
				return false;
			}
			
			return true;

		});

	});
</script>
<div class="section">
	<form method="post" action="<?php echo THIS_URL; ?>?step=8" id="s7_how_heard_about">
	<h2 style="float:left;"><span>Section 7</span> &#8211; How Did You Hear About The Course?</h2><span class="required">*</span>
	<br class="clear_both" />
	<input type="hidden" name="how_heard_about_course" value="" />
	<table class="checkboxes <?php addMissingFieldClass('how_heard_about_course'); ?>" summary="How did you hear about the course?" style="width:880px;">

<?php 
		// Much easier to update if I add these values via an array
		$how_heard = array();
		$how_heard[] = 'I study at the College';
		$how_heard[] = 'Course Guide';
		$how_heard[] = 'At my school';
		$how_heard[] = 'College website';
		$how_heard[] = 'Open Day';
		$how_heard[] = 'Careers Adviser/Connexions';
		$how_heard[] = 'Internet searches';
		$how_heard[] = 'Poster - shopping centre';
		$how_heard[] = 'Employer';
		$how_heard[] = 'Hotmail courses';
		$how_heard[] = 'Poster - bus';
		$how_heard[] = 'Trade Union';
		$how_heard[] = 'Facebook';
		$how_heard[] = 'Poster - tube';
		$how_heard[] = 'Job Centre';
		$how_heard[] = 'Twitter';
		$how_heard[] = 'Leaflet handed out on street';
		$how_heard[] = 'Library';
		$how_heard[] = 'Text message';
		$how_heard[] = 'Leaflet to my home';
		$how_heard[] = 'Radio advert';
		$how_heard[] = 'Email';
		$how_heard[] = 'Newspaper/Magazine advert';
		$how_heard[] = 'Other, please specify';
		$how_heard[] = 'I saw the College building/banner';
		$how_heard[] = 'Friends/family';

		$no_td = 0;
		$c = 1;
		foreach($how_heard as $how) {
			// Create a new table row ever three tds
			if ($no_td == 0) { echo "<tr id=\"how_heard\">\n"; }
			echo "\t<td><input type=\"checkbox\" name=\"how_heard_about_course[]\" value=\"$how\" id=\"s7_hh_$c\" ";
			getValue("checkbox", "how_heard_about_course", $how);
			echo " class=\"checkbox\" /> <label for=\"s7_hh_$c\">$how</label></td>\n";
			++$no_td;
			if ($no_td == 3) {
				echo "</tr>\n";
				$no_td = 0;
			}
			$c++;
		}
?>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td><label for="s7_hh_other">Other:</label>&nbsp; <input type="text" name="how_heard_other" maxlength="40" id="s7_hh_other" class="text" value="<?php getValue('text', 'how_heard_other'); ?>" /></td>
		</tr>
	
	</table>
	
	<h2>Receive Communications?</h2>
	<p style="line-height:1.5em;">We would like to send you information by email or text message from time to time on new courses or events that may be of interest to you.<br />We will not share your details with any third parties, and you will be able at any time to choose to stop receiving messages from us.
	<br /><strong>If you don't want to receive this information, please tick 'No'.</strong></label>
	</p>
	<p>
<!-- Default to 'yes' --><input type="hidden" value="yes" name="receive_communications" />
<input type="checkbox" value="no" name="receive_communications" id="s7_receive_comms_no" <?php getValue('checkbox_single', 'receive_communications', 'no'); ?>/> <label for="s7_receive_comms_no">No</label>
</p>

	<h2>Correct Information<span class="required" style="font-size:1em;">*</span></h2>
	<p style="line-height:1.5em;">I confirm that the information I have given in this form is correct. I give my consent to the College of Haringey, Enfield and North East London to record and process this information, on the understanding that the College complies with the Data Protection Act 1998.</p>
	<p><input type="checkbox" value="yes" name="correct_info_confirm" id="s7_confirm_correct_info" <?php getValue('checkbox_single', 'correct_info_confirm', 'yes'); ?> required /> <label for="s7_confirm_correct_info">Yes, I confirm</label>
	<br />
	<br />
	
	<h2 style="float:left;">Help us Improve</h2>
	<br class="clear_both" />
	<table summary="Help us help you">
		<tr>
			<td width="225"><label for="s7_easy_to_complete_yes">Was this form easy to complete?:</label></td>
			<td>
				<input type="radio" name="easy_to_complete" value="yes" checked="checked" id="s7_easy_to_complete_yes" <?php getValue('radio', 'easy_to_complete', 'yes'); ?>/> <label for="s7_easy_to_complete_yes">Yes</label>
				<input type="radio" name="easy_to_complete" value="no" id="s7_easy_to_complete_no"  <?php getValue('radio', 'easy_to_complete', 'no'); ?>/> <label for="s7_easy_to_complete_no">No</label>
			</td>
		</tr>
		<tr>
			<td><label for="s7_how_can_we_improve_the_form">If not, please tell us how we<br /> could improve it:</label></td>
			<td>
				<textarea name="how_can_we_improve_the_form" id="s7_how_can_we_improve_the_form" cols="40" maxlength="1000" rows="8" style="width:500px;"><?php getValue('textarea', 'how_can_we_improve_the_form'); ?></textarea>
                <br />
                <span id="charlimitinfo">1000 characters left</span>
			</td>
		</tr>
		<tr>
			<td colspan="3">
			<br />
			<?php
				$back_step = $step - 1;
				$back_url = THIS_URL . "?step=$back_step";
				$back_button = "<input type=\"button\" value=\"&lt; Back\" class=\"submit_back\" onclick=\"javascript:window.location.href='$back_url'\" />";
				echo $back_button;
			?>
				<input type="button" value="Save" class="submit_save" />
				<input type="submit" value="Next &gt;" class="submit" />
			</td>
		</tr>
	</table>
	</form>
	<br />
	<noscript>Click your browser's back button to get to the previous page</noscript>
	<div id="save_msg"></div>
</div>

<?php
	}

	if ($step == 8) {
	//$js_disabled_days = getJSExcludeDays();

?>
<link media="screen" rel="stylesheet" href="css/colorbox.css" />
<!--
<link media="screen" rel="stylesheet" href="css/jquery-ui.css" />
<script type="text/javascript" src="js/jquery-ui-1.8.20.custom.min.js"></script>
-->
<script type="text/javascript">
	$(document).ready(function() {

		// New applicant form check
		$("#s8_interview").submit(function(e) {
			
			// check if interview time is blank
			var interview = $("input[name='interview_time']:checked").val();

			if (typeof interview == 'undefined') {
				alert('Please choose an interview time');
				return false;
			}

			// check if interview location is blank
			var interview_loc = $("input[name='interview_location']:checked").val();
			if (typeof interview_loc == 'undefined') {
				alert('Please choose an interview location');
				return false;
			}

			return true;

		});

	});
</script>
<div class="section">

<form method="post" action="<?php echo THIS_URL; ?>?step=9" id="s8_interview">
<h2><span>Section 8</span> &#8211; Interview</h2>
<h2>Reference Form</h2>
<ol>
	<li><img src="../images/printer.png" width="16" height="16" border="0" alt="printer icon" /> <a href="http://www.conel.ac.uk/docs/reference_form_0.pdf" target="_blank">Print the Student Reference Form</a></li>
	<li><strong>Section 1</strong> to be completed by yourself.</li>
	<li><strong>Section 2</strong> to be completed by your referee.</li>
	<li>Bring your printed, completed reference form to your interview.</li>
</ol>
<br />
<p>You do not need to give us a reference for English/literacy, Maths/numeracy, English for Speakers of Other Languages (ESOL) and short part-time ICT courses. For other courses you need to give us a reference.</p>
<br />
<?php
		// Book an interview time
		echo '<h2>Book your Interview</h2>';
		echo '<p>We interview every Monday from 4-6 PM. Interviews start at 4 PM.</p>
			<p>Please select a convenient interview date and location below.</p>
		   <p>If you can\'t attend a date below select \'Other\'. We will contact you to arrange an alternative interview date and time.</p>';

		$mondays = getNextFourMondays();
		echo '<table class="interview_time">
		<tr><th width="160">Date</th><th>Time<span class="required">*</span></th></tr>';
		if (count($mondays) == 0) {
			echo '<tr><td colspan="2">No interview times are currently available. Please select \'Other\'.</td></tr>';
		} else {
			foreach ($mondays as $key => $mon) {
				$mon_format = date('l, j F Y', $mon);
				echo '<tr>';
				echo "<td>$mon_format</td>";
				$value1 = $mon_format .', 4-6 PM';
				echo '<td><input type="radio" name="interview_time" value="'.$value1.'" id="time_'.$key.'_4-6" class="radio" ';
				if ($_SESSION['caf']['interview_time'] == $value1) { 
					echo 'checked="checked"';
				}
				echo '/><label for="time_'.$key.'_4-6">4:00 &ndash; 6:00 PM</label> &nbsp;';
				echo '</tr>';
			}
		}
		echo '<tr><td>Other Date/Time:</td>
			<td><input type="radio" name="interview_time" value="Other" id="other" class="radio" ';
		if ($_SESSION['caf']['interview_time'] == 'Other') {
			echo 'checked="checked"';
		}
		echo ' />'; 
		echo '<label for="other">Other</label>';
		echo '</td>';
		echo '</table>';

        echo '<br />';
        echo '<p>Please select your interview location.</p>';
        echo '<table width="312" class="interview_time">';
        echo '<tr><th colspan="4">Interview Location<span class="required">*</span></th></tr>';
        echo '<tr><td valign="top">
            <input type="radio" name="interview_location" value="Tottenham" id="interview_tottenham"';
        if ($_SESSION['caf']['interview_location'] == 'Tottenham') { 
            echo 'checked="checked"';
        }
        echo '/></td>
            <td valign="top">
            <label for="interview_tottenham">Tottenham Centre<br />
            High Road<br />
            London<br />
            N15 4RU<br />
            </label>

            <br />
            <div class="address_links">
                <p><a href="http://www.conel.ac.uk/docs/tottenham_centres_map_0.pdf" target="_blank" style="text-decoration: underline; text-color: #0000ff;">Download a Map</a></p>
                <p><a href="http://maps.google.co.uk/maps?q=The+College+of+Haringey,+Enfield+and+North+East+London+High+Road+London+N15+4RU&hl=en&ll=51.586187,-0.072296&spn=0.00269,0.006866&sll=51.65291,-0.046177&sspn=0.005372,0.009645&t=h&z=18" target="_blank" style="text-decoration: underline; text-color: #0000ff;">Google Map</a></p>
            </div>
        </td>';

        echo '<td valign="top">
            <input type="radio" name="interview_location" value="Enfield" id="interview_enfield" ';
        if ($_SESSION['caf']['interview_location'] == 'Enfield') { 
            echo 'checked="checked"';
        }
        echo '/>
            </td>
            <td valign="top">
            <label for="interview_enfield">Enfield Centre<br />
            73 Hertford Road<br />
            Enfield<br />
            Middlesex<br />
            EN3 5HA<br />
            </label>

            <br />
            <div class="address_links">
                <p><a href="http://www.conel.ac.uk/docs/enfield_centre_map_0.pdf" target="_blank" style="text-decoration: underline; text-color: #0000ff;">Download a Map</a></p>
                <p><a href="http://maps.google.co.uk/maps?q=73+Hertford+Road+Enfield+Middlesex+EN3+5HA&hl=en&ll=51.65291,-0.046177&spn=0.005372,0.009645&sll=51.654843,-0.046713&sspn=0.002686,0.004823&t=h&z=17" target="_blank" style="text-decoration: underline; text-color: #0000ff;">Google Map</a></p>
            </div>
        </td>';


        echo '</tr></table>';
        echo '<br />';

?>
	<?php
		$back_step = $step - 1;
		$back_url = THIS_URL . "?step=$back_step";
		$back_button = "<input type=\"button\" value=\"&lt; Back\" class=\"submit_back\" onclick=\"javascript:window.location.href='$back_url'\" />";
		echo $back_button;
	?>
	<input type="button" value="Save" class="submit_save" />
	<input type="submit" value="Submit &gt;" class="submit final_submit" />
</form>
<noscript>Click your browser's back button to get to the previous page</noscript>
</div>

<?php
	}

	if ($step == 9) {

		// Display user's submitted information nicely allowing them to make changes
		echo '
		<script type="text/javascript">
		$(document).ready(function(){
			$("#verify").click(function(event){
				event.preventDefault();
				$.colorbox.close();
			});
			$("#modal").show();
			$.colorbox({
				width:"50%", 
				inline:true, 
				href:"#modal",
				onCleanup:function(){ $("#modal").hide(); }
				});
		});
		</script>';
		
		echo '<div class="section">';
		
		echo '<div id="modal">';
			echo '<h2>Ready to submit?</h2>';
			echo '<form method="post" action="'.THIS_URL.'?step=10">';
			echo '<strong>Yes:</strong> <input type="submit" value="Submit Application" class="submit final_submit" /> &nbsp;';
			echo '<strong>No:</strong> <input type="submit" value="Check details" class="submit verify" id="verify" />';
			echo '</form>';
		echo '</div>';
		
		echo "<h2><span>Final Step!</span> &ndash; <span style=\"color:#000;\">Please verify your information</span></h2>";
		
		echo "<div class=\"browse_instructions\">
		<h3>Your application is not complete until you click 'Submit Application'</h3>
		<p>To edit your information, click the 'Edit these details' link in the section you want to edit.</p>
		<p>If your information is correct click 'Submit Application' to send us your completed application.</p>";
		echo '</div>';
		
		echo '<form method="post" action="'.THIS_URL.'?step=10">';
		echo '<input type="submit" value="Submit Application &gt;" class="submit" />';
		echo '</form>';
		echo '<br />';
		
		$body_html .= '<div id="verify_info">';
		$is_email = FALSE;
		$body_html .= getVerifyInfo($is_email);
		echo $body_html;
		echo '<form method="post" action="'.THIS_URL.'?step=10">';
		echo '<input type="submit" value="Submit Application &gt;" class="submit" />';
		echo '</form>';
		echo '</div>';
		echo "<p><br /><img src=\"..//images/printer.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"printer icon\" /> <a href=\"javascript:window.print()\">Print this page</a> for your records.</p>";
		echo '</div>';
	}
	
	if ($step == 10) {
	
		$firstname = $_SESSION['caf']['firstname'];
		$email_address = $_SESSION['caf']['email_address'];
		$datetime_last_submitted = $_SESSION['caf']['datetime_submitted_last'];
		$ref_id = $_SESSION['caf']['reference_id'];
		$interview_time = $_SESSION['caf']['interview_time'];
        $interview_centre = $_SESSION['caf']['interview_location'];

        // Build interview address
        if ($interview_centre == 'Tottenham') {
            $interview_address = 
                '<h4>Address</h4>
				Tottenham Centre<br />
                The College of Haringey, Enfield and North East London<br />
                High Road<br />
                London<br />
                N15 4RU<br />
                <p><a href="http://www.conel.ac.uk/docs/tottenham_centres_map_0.pdf" target="_blank" style="text-decoration: underline; text-color: #0000ff;">Download a Map</a><br />
                <a href="http://maps.google.co.uk/maps?q=The+College+of+Haringey,+Enfield+and+North+East+London+High+Road+London+N15+4RU&hl=en&ll=51.586187,-0.072296&spn=0.00269,0.006866&sll=51.65291,-0.046177&sspn=0.005372,0.009645&t=h&z=18" target="_blank" style="text-decoration: underline; text-color: #0000ff;">Google Map</a></p>';
        } else {
            $interview_address = 
                '<h4>Address</h4>
				Enfield Centre<br />
				College of Haringey, Enfield and North East London<br />
                73 Hertford Road<br />
                Enfield<br />
                Middlesex<br />
                EN3 5HA<br />
                <p><a href="http://www.conel.ac.uk/docs/enfield_centre_map_0.pdf" target="_blank" style="text-decoration: underline; text-color: #0000ff;">Download a Map</a><br />
                <a href="http://maps.google.co.uk/maps?q=73+Hertford+Road+Enfield+Middlesex+EN3+5HA&hl=en&ll=51.65291,-0.046177&spn=0.005372,0.009645&sll=51.654843,-0.046713&sspn=0.002686,0.004823&t=h&z=17" target="_blank" style="text-decoration: underline; text-color: #0000ff;">Google Map</a></p>';
        }

		$date_now = date('d/m/Y, H:i:s');
		$is_email = TRUE;
		$body_html = getVerifyInfo($is_email);

		// Reference Details
		$ref_details = '<h3>Reference Details</h3>';
		$ref_details .= '<table>';
		$ref_details .= '<tr><td width="120"><strong>Email Address:</strong></td><td> '.$_SESSION['caf']['email_address'].'</td></tr>';
		$ref_details .= '<tr><td><strong>Reference ID:</strong></td><td>'.$_SESSION['caf']['reference_id'].' </td></tr>';
		$ref_details .= '</table>';


		// Interview Details - Applicant
		if ($interview_time != 'Other') {
			$interview_details_app = '<p>Your interview is confirmed.</p>';
			$interview_details_app .= '<div id="interview_details">';
			$interview_details_app .= '<h2>Interview</h2>';
			$interview_details_app .= '<p><span class="hide"><strong>Date:</strong> </span><span class="interview_date">'.$interview_time.'</span></p>';
			$interview_details_app .= $interview_address;
			$interview_details_app .= '</div>';
			$interview_details_app .= "<p>Unable to attend? <a href=\"mailto:admissions@conel.ac.uk?subject=Unable to attend my interview&body=%0D%0DPreferred Interview Date:%0D%0DMy Details%0DOld Interview Date: ".$interview_time."%0DEmail: ".$_SESSION['caf']['email_address']."%0DReference ID: ".$_SESSION['caf']['reference_id']."\">Contact us</a> to arrange a different interview date and time.</p>";

		} else {
			$interview_details_app = '<div id="interview_details">';
			$interview_details_app .= '<h2>Interview</h2>';
			$interview_details_app .= '<p>We will contact you to arrange an interview date and time.</p>';
			$interview_details_app .= '</div>';
		}


		// Interview Details - Admissions
		if ($interview_time != 'Other') {
			$interview_details_admis .= '<div id="interview_details">';
			$interview_details_admis .= '<h3>Interview Chosen</h3>';
            $interview_details_admis .= '<table>';
			$interview_details_admis .= '<tr><td width="120"><strong>Date:</strong></td><td>'.$interview_time.'</td></tr>';
			$interview_details_admis .= '<tr><td><strong>Location:</strong></td><td>'.$interview_centre.'</td></tr>';
            $interview_details_admis .= '</table>';
			$interview_details_admis .= '</div>';

		} else {
			$interview_details_admis = '<div id="interview_details">';
			$interview_details_admis .= '<h3>Interview</h3>';
			$interview_details_admis .= '<p><strong>Date:</strong> Applicant has chosen \'Other\' for their interview date.<br />Please contact them to arrange a suitable interview date<br />';
			$interview_details_admis .= '<strong>Location:</strong> '.$interview_centre.'</p>';
			$interview_details_admis .= '</div>';
        }


		/* Email Admissions */
		$staff_html = '<h2>Course Application</h2>';
		$staff_html .= '<p>You have received a new course application.</p>';
		$staff_html .= $ref_details;
		$staff_html .= $interview_details_admis;
		$staff_html .= $body_html;
        $staff_html .= '<p><b>Submitted:</b> '.$date_now.'</p>';

		emailCompletedApplication($staff_html);

		/* Email Applicant */
		$applicant_html = '<h2>Your Completed Application</h2>';
		$applicant_html .= '<p>Thank you '.$firstname.' for completing a course application with the College of Haringey, Enfield and North East London.</p>'; 
		$applicant_html .= $interview_details_app;
        $applicant_html .= '<br />';
		$applicant_html .= $ref_details;
		$applicant_html .= '<p>Please quote these reference details for all enquiries relating to this application.</p>';
        $applicant_html .= '<br />';
		$applicant_html .= '<h3>Application</h3>';
		$applicant_html .= '<p>Here\'s a copy of your submitted application for your records.</p>';
		$applicant_html .= '<p><b>Submitted:</b> '.$date_now.'</p><br />';
		$applicant_html .= $body_html;
        $applicant_html .= '<br />';
		$applicant_html .= getAdmissionsFooter();

		emailCompletedApplication($applicant_html, $email_address);

		echo '<div class="section">';
		echo '<h2>Course Application Complete</h2>';
		//echo "<p><strong>Completed:</strong> ".$datetime_last_submitted."</p><br />";
		echo '<p>Thank you '.$firstname.' for completing a course application.</p>';
		echo '<br />';

		echo $interview_details_app;
		echo '<br />';
		echo '<br />';

		echo '<div id="reference_details_show">';
		echo '<h3>Reference Details</h3>';
		echo '<p>Please quote these reference details for all enquiries relating to this application.</p>';
		echo '<table>';
		echo '<tr><td width="120"><strong>Email Address:</strong></td><td> '.$email_address.'</td></tr>';
		echo '<tr><td><strong>Reference ID:</strong></td><td>'.$ref_id.' </td></tr>';
		//echo '<tr><td><strong>Login URL:</strong></td><td>'.$resume_url.'</td></tr>';
		echo '</table>';
		echo '</div>';

		echo '<br />';

		echo '<p><img src="../images/printer.png" width="16" height="16" border="0" alt="printer icon" /> <a href="javascript:window.print()">Print this page</a></p>';

		echo '<p><br /><input type="button" value="Return to website &gt;" class="submit_back" onclick="javascript:window.location.href=\'http://www.conel.ac.uk\'" /><noscript><a href="http://www.conel.ac.uk">Return to website &gt;</a></noscript></p>';

		echo '</div>';

		unset($_SESSION['caf']);
		session_destroy();
	}
	
	// Clear errors session after page rendered
	$_SESSION['caf']['errors'] = array();
?>
</div>
<script type="text/javascript"> 
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script> 
<script type="text/javascript">var pageTracker = _gat._getTracker("UA-3415972-1"); pageTracker._trackPageview();</script> 
</body>
</html>
