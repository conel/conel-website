<?php
	session_start();
	
	if (isset($_SESSION['caf'])) {
	
		//require_once('extensions/captchalib.php');
		include_once('../matrix_engine/config.php');
		include_once('../matrix_engine/'.CLASSES_DIR.'db_mysql.php');
		include_once('../matrix_engine/'.CLASSES_DIR.'class_mailer.php');

	function isValidCourseCode($course_code='') {
		if (strlen($course_code) != 8) {
			return false;
		}
	}
		
		// instantiate the SQL classes
		$sql = new DB_Sql();
		$sql->connect();
	
		if (isset($_POST)) {
			$course_title = (isset($_POST['course_title']) && ($_POST['course_title'] != '')) ? $_POST['course_title'] : '';
			$course_code = (isset($_POST['course_code']) && ($_POST['course_code'] != '')) ? $_POST['course_code'] : '';
			$location = (isset($_POST['location']) && ($_POST['location'] != '')) ? $_POST['location'] : '';
			// nkowald - 2011-02-23 - Added course entry date
			$course_entry_date = (isset($_POST['course_entry_date']) && ($_POST['course_entry_date'] != '')) ? $_POST['course_entry_date'] : '';
			
			// EBS used to change the format of centre text, this should be able to handle all formats thrown at it (if format includes either tottenham or enfield).
			if ($location != '') {
				$location = strtolower($location);
				if (strpos($location, 'tottenham') === FALSE) {
					$location = 'Enfield';
				} else {
					$location = 'Tottenham';
				}
			}
			
			// Check which slot this course selection should be placed into
			if (!isset($_SESSION['caf']['course_code_1']) || $_SESSION['caf']['course_code_1'] == '' || isValidCourseCode($_SESSION['caf']['course_code_1']) === false) {
				// Use slot 1
				$_SESSION['caf']['course_title_1'] = $course_title;
				$_SESSION['caf']['course_code_1'] = $course_code;
				$_SESSION['caf']['college_centre_1'] = $location;
				// nkowald - 2011-02-23 - Added course entry date
				$_SESSION['caf']['course_entry_date_1'] = $course_entry_date;
				
				// Need to lock select input if added from website
				$_SESSION['caf']['slot_1'] = TRUE;
				
				// now session course variables set, redirect to course page
				$redirect_url = 'http://' . $_SERVER['HTTP_HOST'] . '/course-application/index.php?step=1';
				header("location: $redirect_url");
				exit;
			} else if ((!isset($_SESSION['caf']['course_title_2']) || $_SESSION['caf']['course_title_2'] == '') && (!isset($_SESSION['caf']['course_code_2']) || $_SESSION['caf']['course_code_2'] == '')) {
				// Use slot 2
				$_SESSION['caf']['course_title_2'] = $course_title;
				$_SESSION['caf']['course_code_2'] = $course_code;
				$_SESSION['caf']['college_centre_2'] = $location;
				// nkowald - 2011-02-23 - Added course entry date
				$_SESSION['caf']['course_entry_date_2'] = $course_entry_date;
				
				// Need to lock select input if added from website
				$_SESSION['caf']['slot_2'] = TRUE;
				
				// now session course variables set, redirect to course page
				$redirect_url = 'http://' . $_SERVER['HTTP_HOST'] . '/course-application/index.php?step=1';
				header("location: $redirect_url");
				exit;
			}
			
		} else {
			// No post so redirect to well, the page we came from
			if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != '') {
				$redirect_url = $_SERVER['HTTP_REFERER'];
			} else {
				$redirect_url = 'http://' . $_SERVER['HTTP_HOST'] . '/course-application/';
			}
			header("location: $redirect_url");
			exit;
		}
	} else {
		// nkowald - 2011-02-15 - Now we're allowing them to apply now, add their chosen course to session
		
		//$redirect_url = 'http://' . $_SERVER['HTTP_HOST'];
		//header("location: $redirect_url");
		//exit;
		unset($_SESSION['caf']);
		$_SESSION['caf'] = array();
		
		if (isset($_POST)) {
			$course_title = (isset($_POST['course_title']) && ($_POST['course_title'] != '')) ? $_POST['course_title'] : '';
			$course_code = (isset($_POST['course_code']) && ($_POST['course_code'] != '')) ? $_POST['course_code'] : '';
			$location = (isset($_POST['location']) && ($_POST['location'] != '')) ? $_POST['location'] : '';
			
			// EBS used to change the format of centre text, this should be able to handle all formats thrown at it (if format includes either tottenham or enfield).
			if ($location != '') {
				$location = strtolower($location);
				if (strpos($location, 'tottenham') === FALSE) {
					$location = 'Enfield';
				} else {
					$location = 'Tottenham';
				}
			}
			
			// Check which slot this course selection should be placed into
			if ((!isset($_SESSION['caf']['course_title_1']) || $_SESSION['caf']['course_title_1'] == '') && (!isset($_SESSION['caf']['course_code_1']) || $_SESSION['caf']['course_code_1'] == '')) {
				// Use slot 1
				$_SESSION['caf']['course_title_1'] = $course_title;
				$_SESSION['caf']['course_code_1'] = $course_code;
				$_SESSION['caf']['college_centre_1'] = $location;
				
				// Need to lock select input if added from website
				$_SESSION['caf']['slot_1'] = TRUE;
				
				// now session course variables set, redirect to 'new applicant page'
				$redirect_url = 'http://' . $_SERVER['HTTP_HOST'] . '/course-application/';
				header("location: $redirect_url");
				exit;
			}
		}
	}
?>
