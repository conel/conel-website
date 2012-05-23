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
<title>Interview Dates - Online Course Applications</title>
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
	<!-- Dates View -->
	<div class="section">
	
		<div id="forgot_details">
		<p>
			<strong>Show:</strong>&nbsp;
			<a href="/course-application/forgotten.php?show=1"<?php if ($show == 1) echo ' class="active"'; ?>>Incomplete</a> | 
			<a href="/course-application/forgotten.php?show=2"<?php if ($show == 2) echo ' class="active"'; ?>>Completed</a> | 
			<!--<a href="/course-application/forgotten.php?show=3"<?php //if ($show == 3) echo ' class="active"'; ?>>Incomplete (since 01/09/11)</a> | -->
			<a href="/course-application/interview-dates.php" class="active">Interviews</a>
		</p>
		<div id="logout"><a href="forgotten.php?logout=1">Log out</a></div>
		<br />
        <div id="interview_dates">
		
<?php
        function getApplicantsForDate($date) {
            global $sql;
            global $debug;

            $query = sprintf(
                "SELECT id, datetime_submitted_last, interview_location, title, firstname, surname FROM tbl_course_application WHERE interview_time = '%s' AND form_completed = 1 ORDER BY interview_location, datetime_submitted_last",
                $date
            );

            $sql->query($query, $debug);
            if ($sql->num_rows() > 0) {
                $applicants = array();
                $c = 0;
                while($sql->next_record()) {
                    $applicants[$c]['id'] = $sql->Record['id'];
                    $applicants[$c]['name'] = $sql->Record['title'] . ' ' . $sql->Record['firstname'] . ' ' . $sql->Record['surname'];
                    $applicants[$c]['completed'] = $sql->Record['datetime_submitted_last'];
                    $applicants[$c]['location'] = $sql->Record['interview_location'];
                    $c++;
                }
                return $applicants;
            } else {
                return false;
            }
        }

        function getApplicantsByLocation(Array $applicants, $location) {
            $app_by_loc = array();
            foreach ($applicants as $app) {
                if ($app['location'] == $location) {
                    $app_by_loc[] = $app;
                }
            }
            return $app_by_loc;
        }
		
		$query = "SELECT DISTINCT interview_time, COUNT(*) AS count FROM tbl_course_application WHERE interview_time != '' AND form_completed = 1 GROUP BY interview_time ORDER BY interview_time ASC";
		$sql->query($query, $debug);
		if ($sql->num_rows() > 0) {
            $interview_dates = array();
            $c = 0;
            $other_key = false;
			while($sql->next_record()) {
                $interview_dates[] = array('date' => $sql->Record['interview_time'], 'count' => $sql->Record['count']);
                if ($sql->Record['interview_time'] == 'Other') $other_key = $c;
                $c++;
            }
            // Make 'Other' the first item
            if ($other_key !== false) {
                $other_array = $interview_dates[$other_key];
                unset($interview_dates[$other_key]);
                array_unshift($interview_dates, $other_array);
            }

			echo '<h2>Interview Dates</h2>';
            echo '<p>Click show to view applicants for date.</p>';
            echo '<br />';

            // jQuery slide toggle
echo "\n" . '<script type="text/javascript">';
echo "
$(document).ready(function() {
    $('a.toggle').click(function(e) {
        e.preventDefault();
        var num = $(this).attr('id').replace('view_', '');
        var toggle_div = '.date_' + num;
        $(toggle_div).slideToggle();
        if ($(this).html() == 'show') {
            $(this).html('hide');
        } else {
            $(this).html('show');
        }
    });
})";
echo "</script>\n";

            $locations = array('', 'Tottenham', 'Enfield');

            foreach ($interview_dates as $key => $date) {
                $date_chosen = $date['date'];
                echo '<h3>'.$date_chosen.' <span> &mdash; '.$date['count'].' applicants</span> &nbsp;<a href="#" class="toggle" id="view_'.$key.'">show</a></h3>';
                if ($all_applicants = getApplicantsForDate($date_chosen)) {

                    // Show applicants by location
                    echo '<div class="location date_'.$key.'">';
                    foreach ($locations as $loc) {
                        $loc_name = ($loc == '') ? 'No Centre' : $loc;
                        $applicants = getApplicantsByLocation($all_applicants, $loc);
                        echo '<h4>'.$loc_name.' <span>&ndash; '.count($applicants).' applicants</span></h4>' . "\n";
                        if (count($applicants) > 0) {
                            if ($loc == '') echo '<p>Contact applicant asking which location they want to attend.</p>';
                            echo '<table class="application_stats">' . "\n";
                            echo '<tr><th>&nbsp;</th><th>Name</th><th>Location</th><th>Completed</th><th class="center">Application</th></tr>';
                            $c = 0;
                            foreach ($applicants as $app) {
                                $row_class = ($c % 2 == 0) ? 'r0' : 'r1';
                                echo '<tr class="'.$row_class.'">';
                                echo '<td>'.($c + 1).'.</td>';
                                echo '<td width="270">'.$app['name'].'</td>';
                                echo '<td>'.$loc_name.'</td>';
                                echo '<td>'.$app['completed'].'</td>';
                                echo '<td class="center"><a href="http://www.conel.ac.uk/course-application/view-application.php?id='.$app['id'].'" target="_blank">View</a></td>';
                                echo '</tr>';
                                $c++;
                            }
                            echo "</table>\n";
                            echo '<br />';
                        }
                    }
                    echo '</div>';
                } else {
                    echo '<p>No applicants chose this date.</p>';
                }
            } // foreach $interview_dates

			echo '<br />';
        } else {
            echo '<p>No interview dates found</p>';
        }

        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

?>
</div>
</body>
</html>
