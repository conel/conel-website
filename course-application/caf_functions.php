<?php 
	
	define('ADMISSIONS_EMAIL', 'admissions@conel.ac.uk');

	// Functions used by the Course Application Form - index.php
	function getAge($birthdate) {
		list($d, $m, $Y) = explode("/", $birthdate);
		$years = 2010 - $Y;
		
		// 31 Aug 2010
		$target_date = date('md', mktime(0,0,0,8,31,2010));
		if ($target_date < $m.$d) { $years--; }
		return $years;
	}

	function getRequiredFields() {
		$required_fields = array();

		$required_fields[2] = array('course_title_1', 'college_centre_1', 'course_entry_date_1');
		$required_fields[3] = array('firstname', 'surname', 'gender', 'date_of_birth', 'home_address', 'postcode', 'ethnic_group', 'why_want_to_do_course');
		$required_fields[4] = array('do_you_have_a_learning_difficulty_or_disability');
		$required_fields[6] = array('are_you_employed','are_you_working_as_a_volunteer','relevant_skills_and_experience');
		$required_fields[7] = array('nationality','permanent_right_to_live_in_uk','are_you_an_international_student');
		$required_fields[8] = array('how_heard_about_course', 'correct_info_confirm');
		$required_fields[9] = array('interview_time', 'interview_location');

		return $required_fields;
	}

	function checkRequiredsSet(Array $requireds_for_step) {
		foreach ($requireds_for_step as $step) {
			if (!isset($_SESSION['caf'][$step]) || $_SESSION['caf'][$step] == '') {
				return false;
			}
		}
		return true;
	}

	// Check that all required fields exist in the session
	function requiredsCheck($step) {
		$requireds = getRequiredFields();
		foreach ($requireds as $key => $req) {
			if ($key > $step) break;
			if (checkRequiredsSet($req) === false) {
				$step = $key - 1;
				$_SESSION['caf']['page_step'] = $step;
				$_SESSION['caf']['errors'][] = "Required fields missing";
				header('location:'.THIS_URL.'?step='.$step);
				exit;
			}
		}
	}

	// Make sure people can't get to steps they should'nt be able to
	// Must be "signed in" to be in a step
	function securityStepCheck($step) {

		// check signed in
		if ((isset($_SESSION['caf']['email_address']) && $_SESSION['caf']['email_address'] != '') 
			&& isset($_SESSION['caf']['reference_id']) && $_SESSION['caf']['reference_id'] != ''
			&& ($_SESSION['caf']['signed_in'] === true)) {
		} else {
            $current_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            if ($current_url != THIS_URL && count($_SESSION['caf']['errors']) > 0) {
			    header('location: '.THIS_URL);
			    exit;
            }
		}
		if ($step == 0) {
			if (isset($_SESSION['caf']['page_step']) && $_SESSION['caf']['page_step'] != 0) {
				header('location: '.THIS_URL.'?step='.$_SESSION['caf']['page_step'].'');
				exit;
            }
		}

		$prev_step = $step - 1;
		
		$unrequired = array(4,8,9);
		if ((in_array($step, $unrequired)) && ($_SESSION['caf']['step_complete'][$prev_step] === TRUE)) {
			$_SESSION['caf']['step_complete'][$step] = TRUE;
		}

		// user is signed in, now check TRUE previous step
		foreach ($_SESSION['caf']['step_complete'] as $key => $value) {
			if ($value === TRUE && $key > $_SESSION['caf']['page_step']) {
				$_SESSION['caf']['page_step'] = $key;
			}
		}
		if ($prev_step <= $_SESSION['caf']['page_step']) {
			if ($_SESSION['caf']['step_complete'][$prev_step] === FALSE) {
				header('location: '.THIS_URL.'?step='.$_SESSION['caf']['page_step'].'');
				exit;
			}
		} else {
			// HACKY! Add one to page step so it redirects to the correct page
			header('location: '.THIS_URL.'?step='.$_SESSION['caf']['page_step'].'');
			exit;
		}
	}
	
	function addMissingFieldClass($fieldname) {

		if (in_array($fieldname, $_SESSION['caf']['missing_fields'])) {
			echo 'missing_field';
		}
	}
	
	function markApplicationComplete() {
	
		$sql = new DB_Sql();
		$sql->connect();
		// must be logged in and page step = 9 to complete
		if((isset($_SESSION['caf']['signed_in'])) && ($_SESSION['caf']['signed_in'] === TRUE)) {
			if (isset($_SESSION['caf']['page_step']) && $_SESSION['caf']['page_step'] == 9) {
				$query = "UPDATE tbl_course_application SET form_completed='1' WHERE id='".$_SESSION['caf']['id']."'";
				return ($sql->query($query, $debug));
			} else {
				$_SESSION['caf']['errors'][] = "Steps missing";
				return FALSE;
			}
		} else {
			$_SESSION['caf']['errors'][] = "Not signed in";
			return FALSE;
		}
		
	}

	function getSectionHTML(array $section_keys, $is_email=false) {

		$html = "<table class=\"verify_info_table\" cellspacing=\"0\" cellspacing=\"1\" border=\"1\" width=\"100%\">\n";
		foreach ($section_keys as $key => $value) {
			$session_val = $_SESSION['caf'][$key];
			$value_output = ($session_val == '') ? '&#8211;' : $session_val;
			if (is_array($value_output)) {
				$list_html = "<ul>";
				foreach($value_output as $val) { $list_html .= "<li>$val</li>"; }
				$list_html .= "</ul>";
				$value_output = $list_html;
			}
			$td_width = ($is_email) ? ' width="250"' : ' width="225"';
			$html .= '<tr><td '.$td_width.'><strong>'.$value.':</strong></td><td>'.$value_output.'</td></tr>';
		}
		$html .= "</table>";
		return $html;
		
	}
	
	function getVerifyInfo($is_email = FALSE) {
	
		$body_html = ''; // variable to hold submission preview
		
		// Wish there was a nicer way (less code) to do this
		$section_1_keys = array(
			// nkowald - 2011-02-23 - Removing generic entry date here. Adding it on a course basis.
			//'entry_date' => 'Year and Month of Entry',
			'course_title_1' => 'Course Title 1',
			'course_code_1' => 'Course Code 1',
			'college_centre_1' => 'College Centre 1',
			'course_entry_date_1' => 'Entry Date 1',
			'preferred_entry_month_1' => 'Preferred Entry Month 1',
			'course_title_2' => 'Course Title 2',
			'course_code_2' => 'Course Code 2',
			'college_centre_2' => 'College Centre 2',
			'course_entry_date_2' => 'Entry Date 2',
			'preferred_entry_month_2' => 'Preferred Entry Month 2',
		);
		$section_2_keys = array(
			'title' => 'Title',
			'firstname' => 'Firstname',
			'surname' => 'Surname',
			'gender' => 'Gender',
			'date_of_birth' => 'Date of Birth',
			'home_address' => 'Home Address', 
			'postcode' => 'Postcode',
			'telephone_home_work' => 'Home/Work Telephone',
			'telephone_mobile' => 'Mobile Telephone',
			'email_address' => 'Email Address',
			'ethnic_group' => 'Your Ethnic Group',
			'ethnic_group_other' => 'Any Other',
			'language_spoken_at_home' => 'What language do you speak at home?',
			'why_want_to_do_course' => 'Why do you want to do this course?'
		);
		$section_3_keys = array(
			'do_you_have_a_learning_difficulty_or_disability' => 'Do you have a learning difficulty or disability?',
			'learning_difficulty' => 'Learning Difficulty',
			'other_learning_difficulty' => 'Other Learning Difficulty',
			'disability' => 'Disability',
			'other_disability' => 'Other Disability',
			'support_needed' => 'Will you need support for your learning difficulty / disability?',
			'statement_special_ed_needs' => 'Do you have a statement of special educational needs?'
		);
		$section_4_keys = array(
			'name_of_school_college_attended_1' => 'Name of school / college attended 1',
			'year_attended_from_1' => 'Year Attended From 1',
			'year_attended_to_1' => 'Year Attended To 1',
			'subject_1' => 'Subject 1',
			'course_grades_obtained_1' => 'Course Grades Obtained 1',
			
			'name_of_school_college_attended_2' => 'Name of school / college attended 2',
			'year_attended_from_2' => 'Year Attended From 2',
			'year_attended_to_2' => 'Year Attended To 2',
			'subject_2' => 'Subject 2',
			'course_grades_obtained_2' => 'Course Grades Obtained 2',
			
			'name_of_school_college_attended_3' => 'Name of school / college attended 3',
			'year_attended_from_3' => 'Year Attended From 3',
			'year_attended_to_3' => 'Year Attended To 3',
			'subject_3' => 'Subject 3',
			'course_grades_obtained_3' => 'Course Grades Obtained 3',
			
			'name_of_school_college_attended_4' => 'Name of school / college attended 4',
			'year_attended_from_4' => 'Year Attended From 4',
			'year_attended_to_4' => 'Year Attended To 4',
			'subject_4' => 'Subject 4',
			'course_grades_obtained_4' => 'Course Grades Obtained 4',
			
			'name_of_school_college_attended_5' => 'Name of school / college attended 5',
			'year_attended_from_5' => 'Year Attended From 5',
			'year_attended_to_5' => 'Year Attended To 5',
			'subject_5' => 'Subject 5',
			'course_grades_obtained_5' => 'Course Grades Obtained 5',
			
			'name_of_school_college_attended_6' => 'Name of school / college attended 6',
			'year_attended_from_6' => 'Year Attended From 6',
			'year_attended_to_6' => 'Year Attended To 6',
			'subject_6' => 'Subject 6',
			'course_grades_obtained_6' => 'Course Grades Obtained 6',
			
			'name_of_school_college_attended_7' => 'Name of school / college attended 7',
			'year_attended_from_7' => 'Year Attended From 7',
			'year_attended_to_7' => 'Year Attended To 7',
			'subject_7' => 'Subject 7',
			'course_grades_obtained_7' => 'Course Grades Obtained 7',
			
			'name_of_school_college_attended_8' => 'Name of school / college attended 8',
			'year_attended_from_8' => 'Year Attended From 8',
			'year_attended_to_8' => 'Year Attended To 8',
			'subject_8' => 'Subject 8',
			'course_grades_obtained_8' => 'Course Grades Obtained 8',
			
			'name_of_school_college_attended_9' => 'Name of school / college attended 9',
			'year_attended_from_9' => 'Year Attended From 9',
			'year_attended_to_9' => 'Year Attended To 9',
			'subject_9' => 'Subject 9',
			'course_grades_obtained_9' => 'Course Grades Obtained 9',
			
			'name_of_school_college_attended_10' => 'Name of school / college attended 10',
			'year_attended_from_10' => 'Year Attended From 10',
			'year_attended_to_10' => 'Year Attended To 10',
			'subject_10' => 'Subject 10',
			'course_grades_obtained_10' => 'Course Grades Obtained 10',
			
		);
		$section_5_keys = array(
			'are_you_employed' => 'Are you employed?',
			'are_you_working_as_a_volunteer' => 'Are you working as a volunteer?',
			'job_title' => 'Job Title',
			'employer' => 'Employer',
			'job_address' => 'Address',
			'job_postcode' => 'Postcode',
			'relevant_skills_and_experience' => 'What experience and skills do you have that are relevant to your course?'
		);
		$section_6_keys = array(
			'nationality' => 'What is your nationality?',
			'permanent_right_to_live_in_uk' => 'Do you have a permanent right to live in the UK?',
			'have_been_a_resident_past_3_years' => 'If yes, have you been resident in the UK for the past three years?',
			'country_of_usual_residence' => 'If not resident in the UK, please give your country of usual/permanent residence',
			'EU_EAA_citizen' => 'Are you and EU/EAA citizen?',
			'date_of_arrival_in_uk_eu_eea' => 'If born outside the UK/EU/EEA, your date of arrival in the UK/EU/EAA',
			'are_you_a_refugee' => 'Are you a refugee with full refugee status/indefinite leave to remain?',
			'do_you_have_limited_leave_as_a_refugee' => 'Do you have limited leave to enter/remain as a refugee including exceptional/discretionary leave, humanitarian protection?',
			'asylum_seeker' => 'Are you an asylum seeker?',
			'application_for_asylum_date' => 'If yes, when did you apply for asylum?',
			'support_from_ukba_asylum_or_social_services' => 'Are you receiving support from the UK Border Agency Asylum Support Service or Social Services?',
			'do_you_have_a_visa_to_stay_in_uk' => 'Do you have a visa to stay in the UK?',
			'visa_type' => 'Visa Type',
			'visa_type_other' => 'Other Visa',
			'visa_issue_date' => 'Visa issue date',
			'visa_expiry_date' => 'Visa expiry date',
			'are_you_an_international_student' => 'Are you an international student?'
			//'benefits_receiving' => 'Are you receiving any of the following benefits?',
		);
		$section_7_keys = array(
			'how_heard_about_course' => 'How did you hear about the course',
			'how_heard_other' => 'How heard other',
			'receive_communications' => 'Receive communications?',
			'correct_info_confirm' => 'I confirm that the information I have given in this form is correct',
			'easy_to_complete' => 'Was this form easy to complete?',
			'how_can_we_improve_the_form' => 'If not, please tell us how we could improve it'
		);

		$section_8_keys = array(
			'interview_time' => 'Interview Time',
			'interview_location' => 'Interview Location'
		);

		
		$body_html .= '<h3>Section 1 &#8211; Course Details</h3>';
		if (!$is_email) {
			$body_html .= "<a href=\"".THIS_URL."?step=1\">Edit these details</a><br class=\"clear_both\" />";
		}
		$body_html .= "<table class=\"verify_info_table\" cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">\n";
		foreach ($section_1_keys as $key => $value) {
			$session_val = $_SESSION['caf'][$key];
			if ($key == 'preferred_entry_month_1') {
				$value = 'Preferred Entry Date 1';
				$value_output = $_SESSION['caf']['preferred_entry_month_1'] . ' ' . $_SESSION['caf']['preferred_entry_year_1'];
			} else if ($key == 'preferred_entry_month_2') {
				$value = 'Preferred Entry Date 2';
				$value_output = $_SESSION['caf']['preferred_entry_month_2'] . ' ' . $_SESSION['caf']['preferred_entry_year_2'];
			} else {
				$value_output = ($session_val == '') ? '&#8211;' : $session_val;
			}
			if (is_array($value_output)) {
				$list_html = "<ul>";
				foreach($value_output as $val) { $list_html .= "<li>$val</li>"; }
				$list_html .= "</ul>";
				$value_output = $list_html;
			}
			$td_width = ($is_email) ? ' width="250"' : ' width="225"';
			$body_html .= '<tr><td '.$td_width.'><strong>'.$value.':</strong></td><td>'.$value_output.'</td></tr>';
		}
		$body_html .= "</table>\n";
		
		
		$body_html .= '<h3>Section 2 &#8211; Personal Details</h3>';
		if (!$is_email) {
			$body_html .= "<a href=\"".THIS_URL."?step=2\">Edit these details</a><br class=\"clear_both\" />";
		}
		$body_html .= getSectionHTML($section_2_keys, $is_email);
		
		
		$body_html .= '<h3>Section 3 &#8211; Support at the College of Haringey, Enfield and North East London</h3>';
		if (!$is_email) {
			$body_html .= "<a href=\"".THIS_URL."?step=3\">Edit these details</a><br class=\"clear_both\" />";
		}
		$body_html .= getSectionHTML($section_3_keys, $is_email);
		
		
		$body_html .= '<h3>Section 4 &#8211; Your Qualifications</h3>';
		if (!$is_email) {
			$body_html .= "<a href=\"".THIS_URL."?step=4\">Edit these details</a><br class=\"clear_both\" />";
		}
		$body_html .= "<table class=\"verify_info_table\" cellspacing=\"0\" border=\"1\" width=\"100%\">\n";
		foreach ($section_4_keys as $key => $value) {
			$session_val = $_SESSION['caf'][$key];
			$value_output = $session_val;
			if ($value_output != '') {
				$td_width = ($is_email) ? ' width="250"' : ' width="225"';
				$body_html .= '<tr><td '.$td_width.'><strong>'.$value.':</strong></td><td>'.$value_output.'</td></tr>';
			}
		}
		$body_html .= "</table>";
		
		$body_html .= '<h3>Section 5 &#8211; Your Employment and Experience</h3>';
		if (!$is_email) {
			$body_html .= "<a href=\"".THIS_URL."?step=5\">Edit these details</a><br class=\"clear_both\" />";
		}
		$body_html .= getSectionHTML($section_5_keys, $is_email);
		
		
		$body_html .= '<h3>Section 6 &#8211; Residence and Fee Status</h3>';
		if (!$is_email) {
			$body_html .= "<a href=\"".THIS_URL."?step=6\">Edit these details</a><br class=\"clear_both\" />";
		}
		$body_html .= getSectionHTML($section_6_keys, $is_email);
		
		
		$body_html .= '<h3>Section 7 &#8211; How Did You Hear About The Course?</h3>';
		if (!$is_email) {
			$body_html .= "<a href=\"".THIS_URL."?step=7\">Edit these details</a><br class=\"clear_both\" />";
		}
		$body_html .= getSectionHTML($section_7_keys, $is_email);

		$body_html .= '<h3>Section 8 &#8211; Interview</h3>';
		if (!$is_email) {
			$body_html .= "<a href=\"".THIS_URL."?step=8\">Edit these details</a><br class=\"clear_both\" />";
		}
		$body_html .= getSectionHTML($section_8_keys, $is_email);

		return $body_html;
	}

	function getHTMLEmailHeader() {

		$header = '
		<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
		<html>
		<head><title>Course Application</title>
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

		return $header;

	}
	
	function emailCompletedApplication($body_html='', $email_address='') {
		
		/* Create email */
		$email_html = getHTMLEmailHeader();
		$email_html .= $body_html;
		$email_html .= '</body></html>';

		$mail = new phpmailer();
		$mail->IsHTML(TRUE); // send HTML email
		$mail->IsSMTP(); // use SMTP to send

		// Set Recipient - If email_address given: it's sending to user
		if ($email_address != '') {
			// Send to applicant
			$from_name = $_SESSION['caf']['firstname'] . " " . $_SESSION['caf']['surname'];
			$from_name = ($from_name == '') ? $_SESSION['caf']['email_address'] : $from_name;
			$mail->AddAddress($email_address, $email_address);
			$mail->From = ADMISSIONS_EMAIL;
			$mail->FromName = 'Conel Admissions';
		} else {
			// Send to admissions
			$mail->AddAddress(ADMISSIONS_EMAIL,'Admissions');
			$from_name = $_SESSION['caf']['firstname'] . " " . $_SESSION['caf']['surname'];
			$from_name = ($from_name == '') ? $_SESSION['caf']['email_address'] : $from_name;
			$mail->From = $_SESSION['caf']['email_address'];
			$mail->FromName = $from_name;
		}
		$mail->Subject = "Course Application Submission";
		$mail->Body = $email_html;
		//$mail->SMTPDebug = TRUE;

		$result = $mail->Send(); // send email notification!
	}

	
	// takes field name as input, checks if session with same name exists, returns value html
	// $type = fieldtype [text, textarea, select, radio, checkbox]
	// $fieldname = the name of the field
	// $value = only used with radio and checkboxes - fiels where more than one name exists
	function getValue($type='', $fieldname='', $value='') {
		
		$html = '';
		if (isset($_SESSION['caf'][$fieldname]) && $_SESSION['caf'][$fieldname] != '') {
			switch($type) {
			
				case 'text':
				case 'textarea':
					$html = $_SESSION['caf'][$fieldname];
				break;
				
				case 'select':
				if ($_SESSION['caf'][$fieldname] == $value) {
					$html = 'selected="selected"';
				}
				break;
				
				case 'radio':
				case 'checkbox_single':
				if ($_SESSION['caf'][$fieldname] == $value) {
					$html = 'checked="checked"';
				}
				break;
				
				case 'checkbox':
				if (in_array($value, $_SESSION['caf'][$fieldname])) {
					$html = 'checked="checked"';
				}
				
			}
			
		}
		
		echo $html;
		
	}
	
	function showOrHideQual($qual_no) {
	
		if (!is_int($qual_no)) { return false; }
		
		$show = FALSE;
		// for the given qualification, check session data exists
		// array of session names to check
		$qual_fields = array(
			"name_of_school_college_attended_$qual_no",
			"year_attended_from_$qual_no",
			"year_attended_to_$qual_no",
			"subject_course_grades_$qual_no",
			"obtained_$qual_no"
		);
		
		foreach ($qual_fields as $field) {
			if (isset($_SESSION['caf'][$field]) && $_SESSION['caf'][$field] != '') {
				$show = TRUE;
			}
		}
		
		/*
		if ($show == TRUE) {
			echo 'style="display:block;" ';
		} else {
			echo 'style="display:none;" ';
		}
		*/
		return $show;
	}
	
	function getDisabledStatus($slot_num) {
		if (isset($_SESSION['caf']['slot_'.$slot_num]) && $_SESSION['caf']['slot_'.$slot_num] == TRUE) {
			// check if course title and course code are blank, if so: enable select
			if (isset($_SESSION['caf']['course_title_'.$slot_num]) && $_SESSION['caf']['course_title_'.$slot_num] != '') {
				if (isset($_SESSION['caf']['course_code_'.$slot_num]) && $_SESSION['caf']['course_code_'.$slot_num] != '') {
					echo ' disabled="disabled" ';
				}
			}
		} else {
			echo '';
			$_SESSION['caf']['college_centre_'.$slot_num] = '';
		}
	}

	function getExcludedMondays() {

		$mon_excludes = array(
			'4/6/2012',
			'2/7/2012',
			'27/8/2012',
			'29/10/2012',
			'24/12/2012',
			'31/12/2012',
			'18/2/2012',
			'1/4/2013',
			'8/4/2013',
			'6/5/2013',
			'27/5/2013',
			'1/7/2013',
			'15/7/2013',
			'22/7/2013',
			'29/7/2013',
			'5/8/2013',
			'12/8/2013',
			'26/8/2013'
		);
		return $mon_excludes;
	}

	function getJSExcludeDays() {
		$excluded_mondays = getExcludedMondays();
		$quoted_excludes = array();
		foreach ($excluded_mondays as $mon) {
			$quoted_excludes[] = '"'.$mon.'"';
		}
		$js_disabled_days = implode(',', $quoted_excludes);
		return $js_disabled_days;
	}

	function isExcludedMonday($monday) {
		// Mondays to exclude (half term, staff dev. days, bank holidays)
		$monday_excludes = getExcludedMondays();
		$monday_formatted = date('j/n/Y', $monday);

		if (in_array($monday_formatted, $monday_excludes)) {
			return true;
		} else {
			return false;
		}
	}

	function daysNotice($unixtime) {
		$processing_date = strtotime('-7 days', $unixtime);
		if (time() > $processing_date) {
			return true;
		}
		return false;
	}

	function isValidMonday($unixtime) {
		if (!isExcludedMonday($unixtime) && !daysNotice($unixtime)) {
			return true;
		} else {
			return false;
		}
	}

	function getNextFourMondays() {

		$mondays = array();
		$max_mon = mktime(1,0,0,6,25,2012); 
		$next_mon = strtotime('next monday');

		while(count($mondays) <= 3 && $next_mon < $max_mon) {
			if (isValidMonday($next_mon)) {
				$mondays[] = $next_mon;
			}
			$next_mon = strtotime('+1 week', $next_mon);
		}

		return $mondays;
	}

	function getAdmissionsFooter() {

		$html = '<p>Kind regards,<br /><br />
		Learner Recruitment Team<br />
		E-mail: <a href="mailto:'.ADMISSIONS_EMAIL.'" target="_blank">'.ADMISSIONS_EMAIL.'</a><br />
		Tel: 020 8442 3055 / 020 8442 3103</p>';

		return $html;
	}

	function emailUserReferenceDetails($to_email='') {

		$date_now = date('d/m/Y, H:i:s');
		
		/* Create email */
		$email_html = getHTMLEmailHeader();
		$email_html .= '<p><strong>Application Started:</strong> '.$date_now.'</p>';
		$email_html .= '<p>Thank you for starting a course application with the College of Haringey, Enfield and North East London.</p>';
		$email_html .= '<p>Your reference details are provided below. You can save your progress at any time during your application and re-login using the \'Resume Application\' link below.</p>';
		
		$email_html .= '<h3>Reference Details</h3>';
		$email_html .= '<table cellspacing="0" cellpadding="2">';
		$email_html .= '<tr><td width="120"><strong>Email Address:</strong></td><td> '.$_SESSION['caf']['email_address'].'</td></tr>';
		$email_html .= '<tr><td><strong>Reference ID:</strong></td><td>'.$_SESSION['caf']['reference_id'].' </td></tr>';
		$resume_link = 'http://www.conel.ac.uk/course-application/index.php?email='.$_SESSION['caf']['email_address'].'&ref_id='.$_SESSION['caf']['reference_id'];
		$email_html .= '<tr><td><strong>Resume Link:</strong></td><td><a href="'.$resume_link.'">Resume Application</a></td></tr>';
		$email_html .= '<tr><td></td></tr>';
		$email_html .= '</table>';
		
		$email_html .= getAdmissionsFooter();
		$email_html .= '</body></html>';

		$mail = new phpmailer();
		$mail->IsHTML(TRUE); // send HTML email
		$mail->IsSMTP(); // use SMTP to send
		// Set Recipient
		$mail->AddAddress($to_email, $to_email);
		$mail->Subject = "Course Application - Reference Details";
		$mail->From = ADMISSIONS_EMAIL;
		$mail->FromName = 'Conel Admissions';
		$mail->Body = $email_html;
		//$mail->SMTPDebug = TRUE;

		$result = $mail->Send(); // send email notification!

	}

	function isValidCourseCode($course_code='') {
		if (strlen($course_code) != 8) {
			return false;
		} else {
			return true;
		}
	}

?>
