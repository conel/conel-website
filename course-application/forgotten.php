<?php
	session_start();
	if (!isset($_SESSION['ca']['logged_in'])) {
		$_SESSION['ca']['logged_in'] = FALSE;
	}

	include_once('../matrix_engine/config.php');
	include_once('../matrix_engine/'.CLASSES_DIR.'db_mysql.php');
	include_once('../matrix_engine/'.CLASSES_DIR.'class_mailer.php');

	// instantiate the SQL classes
	$sql = new DB_Sql();
	$sql->connect();
	$debug = 1; // 0 = Don't debug, 1 = debug
	
	$show = (isset($_GET['show']) && $_GET['show'] != '') ? $_GET['show'] : 1;
	$order = (isset($_GET['order']) && $_GET['order'] != '') ? $_GET['order'] : 'email_address';
	$pos = (isset($_GET['pos']) && $_GET['pos'] != '') ? $_GET['pos'] : 'ASC';
	
	// Make sure no SQL injections can occur
	$valid_pos = array('ASC', 'DESC');
	if (!in_array($pos, $valid_pos)) {
		$pos = 'ASC';
	}
	
	if ($show > 3 || !is_numeric($show)) $show = 1;
	
	$valid_orders = array('firstname', 'surname', 'email_address', 'reference_id', 'page_step');
	
	$_SESSION['ca']['errors'] = array();
	
	if (isset($_POST['username']) && isset($_POST['password'])) {
		
		// Check login details
		if ($_POST['username'] == '') {
			$_SESSION['ca']['errors'][] = 'Blank username';
		}
		if ($_POST['password'] == '') {
			$_SESSION['ca']['errors'][] = 'Blank password';
		}
		
		if (count($_SESSION['ca']['errors']) == 0) {
		
			$username = stripslashes($_POST['username']);
			$username = mysql_real_escape_string($username, $sql->Link_ID);
			
			$password = stripslashes($_POST['password']);
			$password = mysql_real_escape_string($password, $sql->Link_ID);
			$encrypted_password = md5($password);
			
			$query = "SELECT * FROM tbl_ca_logins WHERE username='$username' AND password='$encrypted_password'";
			$sql->query($query, $debug);
			
			if ($sql->num_rows() > 0) {
				$_SESSION['ca']['logged_in'] = TRUE;
			} else {
				$_SESSION['ca']['errors'][] = 'Incorrect login details';
				$_SESSION['ca']['logged_in'] = FALSE;
			}
			
		}
	}
	
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
<link href="css/application_form.css" rel="stylesheet" type="text/css" media="all" />
<link href="css/application_form_print.css" rel="stylesheet" type="text/css" media="print" />
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/fl_functions.js"></script>
</head>

<body>

<div id="holder">
	<img src="../layout/img/banner_new.gif" width="955" height="84" alt="The College of Haringey, Enfield and North East London" id="banner" />
	<h1>Course Applications</h1>
	
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
<?php
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
	
	echo '</div>';
	
	}
?>
	
<?php
	if ($_SESSION['ca']['logged_in'] == TRUE) {
?>
	<!-- Submissions View -->
	<div class="section">
	
		<div id="forgot_details">
		<p>
			<strong>Show:</strong>&nbsp;
			<a href="/course-application/forgotten.php?show=1"<?php if ($show == 1) echo ' class="active"'; ?>>Incomplete</a> | 
			<a href="/course-application/forgotten.php?show=2"<?php if ($show == 2) echo ' class="active"'; ?>>Completed</a> | 
			<a href="/course-application/forgotten.php?show=3"<?php if ($show == 3) echo ' class="active"'; ?>>Incomplete (since 01/09/11)</a>
		</p>
		<div id="logout"><a href="forgotten.php?logout=1">Log out</a></div>
		<br />
		
<?php
	if ($show == 1) {
		
		if (in_array($order, $valid_orders)) {
			$query = "SELECT id, firstname, surname, email_address, reference_id, page_step FROM tbl_course_application WHERE form_completed = 0 ORDER BY $order $pos";
		} else {
			$query = "SELECT id, firstname, surname, email_address, reference_id, page_step FROM tbl_course_application WHERE form_completed = 0 ORDER BY email_address $pos";
		}
		$sql->query($query, $debug);
		if ($sql->num_rows() > 0) {
			
			echo '<h2>Incomplete</h2>';
			echo '<p id="incomplete_count">'.number_format($sql->num_rows()).' incomplete applications</p>';
			echo '<!--<p class="email_filter"><strong>Filter by Email:</strong> &nbsp;<input type="text" name="filter" id="filter_incomplete" value="" /></p>-->';
			echo '<table class="application_stats">';
			echo '<thead>';
			echo '<tr>';
				// In the link we need to switch position to whatever it's currently set to
				if ($pos == 'ASC') { $pos = 'DESC'; } else { $pos = 'ASC'; }
				echo '<th><a href="http://www.conel.ac.uk/course-application/forgotten.php?show=1&amp;order=email_address&amp;pos='.$pos.'">Email</a></th>';
				echo '<th width="130"><a href="http://www.conel.ac.uk/course-application/forgotten.php?show=1&amp;order=reference_id&amp;pos='.$pos.'">Reference ID</a></th>';
				echo '<th><a href="http://www.conel.ac.uk/course-application/forgotten.php?show=1&amp;order=firstname&amp;pos='.$pos.'">Firstname</a></th>';
				echo '<th><a href="http://www.conel.ac.uk/course-application/forgotten.php?show=1&amp;order=surname&amp;pos='.$pos.'">Surname</a></th>';
				echo '<th><a href="http://www.conel.ac.uk/course-application/forgotten.php?show=1&amp;order=page_step&amp;pos='.$pos.'">Complete</a></th>';
				echo '<th>Actions</th>';
			echo '</tr>';
			echo '</thead>';
			echo '<tbody>';

			$i = 0;
			while($sql->next_record()) {
				
				$row_class = ($i % 2 == 0) ? 'r0' : 'r1';
				
				$id = (isset($sql->Record['id']) && $sql->Record['id'] != '') ? $sql->Record['id'] : '';
				$firstname = (isset($sql->Record['firstname']) && $sql->Record['firstname'] != '') ? $sql->Record['firstname'] : '&#8211;';
				$surname = (isset($sql->Record['surname']) && $sql->Record['surname'] != '') ? $sql->Record['surname'] : '&#8211;';
				$page_step = (isset($sql->Record['page_step']) && $sql->Record['page_step'] != '')  ? $sql->Record['page_step'] : '';
				$percent = ($page_step != '') ? round((($page_step / 9) * 100), 0) . '%' : '0%';
				
				echo '<tr class="'.$row_class.'">';
					echo '<td class="email_add">'. $sql->Record['email_address'] . '</td>';
					echo '<td class="ref_id">'. $sql->Record['reference_id'] . '</td>';
					echo '<td>'. $firstname . '</td>';
					echo '<td>'. $surname . '</td>';
					echo '<td style="text-align:center;">'.$percent.'</td>';
					$resume_link = 'http://www.conel.ac.uk/course-application/index.php?email='.$sql->Record['email_address'].'&ref_id='.$sql->Record['reference_id'];
					$resume_link = urlencode($resume_link);
					echo '<td style="text-align:center;"><a href="view-application.php?id='.$id.'" title="View Application"><img src="images/icon-view.png" width="16" height="16" alt="View User\'s Application" /></a> <a href="mailto:'.$sql->Record['email_address'].'&amp;subject=Resume Your Online Application&amp;Body=To resume your saved application visit: '.$resume_link.' and click \'Continue Application\'." title="Email User Their Reference ID"><img src="images/icon-email.png" width="16" height="16" alt="Email User Reference ID" /></a></td>';
				echo '</tr>';
				$i++;
			}
			echo '</tbody>';
			echo '</table>';
			echo '<br />';
		}
	}
							
	if ($show == 3) {
		
		if (in_array($order, $valid_orders)) {
			$query = "SELECT id, firstname, surname, email_address, reference_id, page_step FROM tbl_course_application WHERE form_completed = 0 AND datetime_submitted_first >= '2011-09-01 00:00:00' ORDER BY $order $pos";
		} else {
			$query = "SELECT id, firstname, surname, email_address, reference_id, page_step FROM tbl_course_application WHERE datetime_submitted_first >= '2011-09-01 00:00:00' AND form_completed = 0 ORDER BY email_address $pos";
		}
		$sql->query($query, $debug);
		if ($sql->num_rows() > 0) {
			
?>

<script type="text/javascript">
	$(document).ready(function() {
		$('table.application_stats a.email_user').bind('click', function(e) {
			$(e.target).closest('tr').children('td,th').css('background-color','#FFFF79');
		});
	});
</script>

<?php 
			echo '<h2>Incomplete - since 1 September 2011</h2>';
			echo '<p id="incomplete_count">'.number_format($sql->num_rows()).' incomplete applications</p>';
			echo '<!--<p class="email_filter"><strong>Filter by Email:</strong> &nbsp;<input type="text" name="filter" id="filter_incomplete" value="" /></p>-->';
			echo '<table class="application_stats">';
			echo '<thead>';
			echo '<tr>';
				// In the link we need to switch position to whatever it's currently set to
				if ($pos == 'ASC') { $pos = 'DESC'; } else { $pos = 'ASC'; }
				echo '<th>&nbsp;</th>';
				echo '<th>Email</th>';
				echo '<th width="130">Reference ID</th>';
				echo '<th>Firstname</th>';
				echo '<th>Surname</th>';
				echo '<th>Complete</th>';
				echo '<th>Action</th>';
			echo '</tr>';
			echo '</thead>';
			echo '<tbody>';

			$i = 0;
			while($sql->next_record()) {
				
				$row_class = ($i % 2 == 0) ? 'r0' : 'r1';
				
				$id = (isset($sql->Record['id']) && $sql->Record['id'] != '') ? $sql->Record['id'] : '';
				$firstname = (isset($sql->Record['firstname']) && $sql->Record['firstname'] != '') ? $sql->Record['firstname'] : '&#8211;';
				$surname = (isset($sql->Record['surname']) && $sql->Record['surname'] != '') ? $sql->Record['surname'] : '&#8211;';
				$page_step = (isset($sql->Record['page_step']) && $sql->Record['page_step'] != '')  ? $sql->Record['page_step'] : '';
				$percent = ($page_step != '') ? round((($page_step / 9) * 100), 0) . '%' : '0%';
				
				echo '<tr class="'.$row_class.'">';
					$person_no = $i + 1;
					echo "<td style=\"text-align:center;\">$person_no</td>";
					echo '<td class="email_add">'. $sql->Record['email_address'] . '</td>';
					echo '<td class="ref_id">'. $sql->Record['reference_id'] . '</td>';
					echo '<td>'. $firstname . '</td>';
					echo '<td>'. $surname . '</td>';
					echo '<td style="text-align:center;">'.$percent.'</td>';
					$resume_link = 'http://www.conel.ac.uk/course-application/index.php?email='.$sql->Record['email_address'].'&ref_id='.$sql->Record['reference_id'];
					$resume_link = urlencode($resume_link);
					echo '<td style="text-align:center;"><!--<a href="view-application.php?id='.$id.'" title="View Application"><img src="images/icon-view.png" width="16" height="16" alt="View User\'s Application" /></a>-->
					<a href="mailto:'.$sql->Record['email_address'].'&amp;subject=Resume Your Online Application&amp;Body=Dear Applicant,%0D%0D
					The online application you submitted to the College of Haringey Enfield and North East London is incomplete.%0D
					To resume your saved application visit: '.$resume_link.' then click \'Continue Application\'.%0D
					Once we receive a completed application from you, you will be invited for immediate interview with a tutor.%0D%0D%0D
					You are invited to come along to the next College Open Days to see our teaching facilities and speak with tutors about your course options.%0D
					Tottenham Centre: Saturday 12th May 2012, 10am-12pm%0D
					Enfield Centre: Saturday 26th May 2012, 10am-12pm%0D%0D%0D
					Please do not hesitate to contact us if you have any queries.%0D%0D
					Kind regards,%0D%0D
					Learner Recruitment Team%0D
					E-mail: admissions@conel.ac.uk%0D
					Tel: 020 8442 3055 / 020 8442 3103" title="Email User" class="email_user"><img src="images/icon-email.png" width="16" height="16" alt="Email User Reference ID" /></a></td>';
				echo '</tr>';
				$i++;
			}
			echo '</tbody>';
			echo '</table>';
			echo '<br />';
		}
	}

	if ($show == 2) {

		if (in_array($order, $valid_orders)) {
			$query = "SELECT id, firstname, surname, email_address, reference_id FROM tbl_course_application WHERE form_completed = 1 ORDER BY $order $pos";
		} else {
			$query = "SELECT id, firstname, surname, email_address, reference_id FROM tbl_course_application WHERE form_completed = 1 ORDER BY email_address $pos";
		}
		
		$sql->query($query, $debug);
		if ($sql->num_rows() > 0) {
			
			echo '<h2>Completed</h2>';
			echo '<p id="complete_count">'.number_format($sql->num_rows()).' completed applications</p>';
			echo '<!--<p class="email_filter"><strong>Filter by Email:</strong> &nbsp;<input type="text" name="filter" id="filter_complete" value="" /></p>-->';
			echo '<table class="application_stats">';
			echo '<thead>';
			echo '<tr>';
				// In the link we need to switch position to whatever it's currently set to
				if ($pos == 'ASC') { $pos = 'DESC'; } else { $pos = 'ASC'; }
				echo '<th><a href="http://www.conel.ac.uk/course-application/forgotten.php?show=2&amp;order=email_address&amp;pos='.$pos.'">Email</a></th>';
				echo '<th width="130"><a href="http://www.conel.ac.uk/course-application/forgotten.php?show=2&amp;order=reference_id&amp;pos='.$pos.'">Reference ID</a></th>';
				echo '<th><a href="http://www.conel.ac.uk/course-application/forgotten.php?show=2&amp;order=firtname&amp;pos='.$pos.'">Firstname</a></th>';
				echo '<th><a href="http://www.conel.ac.uk/course-application/forgotten.php?show=2&amp;order=surname&amp;pos='.$pos.'">Surname</a></th>';
				echo '<th>Actions</th>';
			echo '</tr>';
			echo '</thead>';
			echo '<tbody>';

			$i = 0;
			while($sql->next_record()) {
				
				$row_class = ($i % 2 == 0) ? 'r0' : 'r1';
				
				$id = (isset($sql->Record['id']) && $sql->Record['id'] != '') ? $sql->Record['id'] : '';
				$firstname = (isset($sql->Record['firstname']) && $sql->Record['firstname'] != '') ? $sql->Record['firstname'] : '&#8211;';
				$surname = (isset($sql->Record['surname']) && $sql->Record['surname'] != '') ? $sql->Record['surname'] : '&#8211;';
				
				echo '<tr class="'.$row_class.'">';
					echo '<td class="email_add">'. $sql->Record['email_address'] . '</td>';
					echo '<td class="ref_id">'. $sql->Record['reference_id'] . '</td>';
					echo '<td>'. $firstname . '</td>';
					echo '<td>'. $surname . '</td>';
					echo '<td style="text-align:center;"><a href="view-application.php?id='.$id.'" title="View Application"><img src="images/icon-view.png" width="16" height="16" alt="View User\'s Application" /></a></td>';
				echo '</tr>';
				$i++;
			}
			echo '</tbody>';
			echo '</table>';
			echo '<br />';
			
		}
	}
						
?>	
	<div id="student_found"></div>
	</div>
	<!-- //Submissions View -->
<?php
	}
?>
</div>
</div>
</body>
</html>