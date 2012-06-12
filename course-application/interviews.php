<?php
	session_start();
	if (!isset($_SESSION['ca']['logged_in'])) {
		$_SESSION['ca']['logged_in'] = FALSE;
	}

	include_once('../matrix_engine/config.php');
	include_once('../matrix_engine/'.CLASSES_DIR.'db_mysql.php');
	include_once('../matrix_engine/'.CLASSES_DIR.'class_mailer.php');
	include_once('caf_functions.php');

	// instantiate the SQL classes
	$sql = new DB_Sql();
	$sql->connect();
	$debug = 1; // 0 = Don't debug, 1 = debug
	
	$_SESSION['ca']['errors'] = array();
	
	if (isset($_GET['logout']) && $_GET['logout'] == 1) {
		$_SESSION['ca']['logged_in'] = FALSE;
	}

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
			
            $query = sprintf(
                "SELECT * FROM tbl_ca_logins WHERE username='%s' AND password='%s'", 
                $username,
                $encrypted_password
            );
			$sql->query($query, $debug);
			
			if ($sql->num_rows() > 0) {
				$_SESSION['ca']['logged_in'] = TRUE;
			} else {
				$_SESSION['ca']['errors'][] = 'Incorrect login details';
				$_SESSION['ca']['logged_in'] = FALSE;
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
<title>Interview Dates - Online Course Applications</title>
<link href="css/application.css" rel="stylesheet" type="text/css" media="all" />
<link href="css/application_print.css" rel="stylesheet" type="text/css" media="print" />
<link href="css/admissions_print.css" rel="stylesheet" type="text/css" media="print" />
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/interviews.js"></script>
</head>

<body>

<div id="holder">
    <div class="header">
	<img src="../layout/img/banner_new.gif" width="955" height="84" alt="The College of Haringey, Enfield and North East London" id="banner" />
	<h1>Course Applications</h1>

<?php
	if ($_SESSION['ca']['logged_in'] == FALSE) {
?>
	<!-- Login -->
	<div class="section">
		<h2>Login</h2>
		<form method="post" action="interviews.php">
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

<noscript><div id="js_error">This page requires JavaScript.<br />Follow <a href="http://www.enable-javascript.com/" target="_blank">these instructions</a> to enable JavaScript in your web browser.</div></noscript>

<!-- Dates View -->
</div>
<div class="section">

    <div id="forgot_details">
    <p class="page_choice">
        <strong>Show:</strong>&nbsp;
        <a href="forgotten.php?show=1">Incomplete</a> | 
        <a href="forgotten.php?show=2">Completed</a> | 
        <a href="interviews.php" class="active">Interviews</a>
    </p>
    <div id="logout"><a href="interviews.php?logout=1">Log out</a></div>
    <br />


    <div id="interview_dates">
    
<?php

    function getApplicantsForDate($date) {
        global $sql;
        global $debug;

        $applicants = array();

        $query = sprintf(
            "SELECT ca.id, ca.datetime_submitted_last, ca.interview_location, ca.email_address, ca.firstname, CONCAT(ca.title, ' ', ca.firstname, ' ', ca.surname) as name, ca.course_title_1, tu.Subject_ID
            FROM tbl_course_application ca 
            LEFT JOIN tblunits tu ON ca.course_code_1 = tu.id 
            WHERE ca.interview_time = '%s' 
            AND ca.form_completed = 1 
            ORDER BY ca.interview_location, tu.Subject_ID, ca.datetime_submitted_last",
            $date
        );

        $sql->query($query, $debug);
        if ($sql->num_rows() > 0) {
            $c = 0;
            while($sql->next_record()) {
                $applicants[$c]['id'] = $sql->Record['id'];
                $applicants[$c]['name'] = $sql->Record['name'];
                $applicants[$c]['email'] = $sql->Record['email_address'];
                $applicants[$c]['firstname'] = $sql->Record['firstname'];
                $applicants[$c]['completed'] = $sql->Record['datetime_submitted_last'];
                $applicants[$c]['location'] = $sql->Record['interview_location'];
                $applicants[$c]['curric_area'] = ($sql->Record['Subject_ID'] != '') ? $sql->Record['Subject_ID'] : $sql->Record['course_title_1'];
                $c++;
            }
        }
        return $applicants;
    }

    // @param: $applicant array containing: 'email', 'firstname', 'interview date', 'centre'
    function generate_mailto(Array $applicant) {

        if ($applicant == '' && !is_array($applicant)) return '';
        if ($applicant['interview_date'] == 'Other') $applicant['interview_date'] = '   <XXXX-INSERT-INTERVIEW-DATE-HERE-XXXX>   ';

        $link = "mailto:".$applicant['email']."&subject=Interview Schedule&Body=Dear ".$applicant['firstname'].",%0D%0DThank you for your application to join a course at The College of Haringey, Enfield and North East London.%0D%0DAn interview has been scheduled for you on ".$applicant['interview_date']." at the ".$applicant['centre']." Centre. Please report to the main reception on arrival.%0D%0DIf you have any enquiries regarding your Interview, please contact the Learner Recruitment Team on 0208 442 3103 or via email admissions@conel.ac.uk.%0D%0DRegards%0D%0DLearner Recruitment Team%0D%0DTottenham Centre%0DHigh Road%0DTottenham%0DN15 4RU";

        return $link;
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
            $date = $sql->Record['interview_time'];
            $date_days = substr($date, 0, strlen($date) - 6);
            $unixtime = strtotime($date_days);
            // add a day (should show interview dates for current day)
            $unixtime_plus_a_day = strtotime('+1 day', $unixtime);

            if ($date == 'Other' || $unixtime_plus_a_day > time()) {
                $interview_dates[] = array(
                    'date' => $date, 
                    'count' => $sql->Record['count'], 
                    'unixtime' => $unixtime
                );
                if ($date == 'Other') $other_key = $c;
                $c++;
            }
        }
        // Make 'Other' the first item
        if ($other_key !== false) {
            $other_array = $interview_dates[$other_key];
            unset($interview_dates[$other_key]);
            array_unshift($interview_dates, $other_array);
        }


        echo '<h2>Interview Dates</h2>';
        echo '<p>Click show to view applicants for date and location.</p>';
        echo '<br />';

    $locations = array('', 'Tottenham', 'Enfield');

    $curric_areas = array('AGRICULTUR', 'APMEDFRSCI', 'ARTSMEDIA', 'BUSIACCNTS', 'CAREHEALTH', 'COMPUTING', 'CONSTRBUI', 'ENGMATHICT', 'ESOL', 'FORGNLANG', 'HAIRBEAU', 'LEISTOUR', 'SPORTFIT', 'SUPPLEARN', 'TEACHSUP', 'UNISERV');
    
    $active = '<img src="images/active.png" alt="active" width="16" height="16" title="Date can be chosen" />';
    $inactive = '<img src="images/inactive.png" alt="inactive" width="16" height="16" title="Date can no longer be chosen" />';

    $print_data = array();

    foreach ($interview_dates as $key => $date) {
        $date_chosen = $date['date'];
        $icon = $active;
        if ($date_chosen != 'Other') {
            // Show if this date is currently choosable on the application form - via an icon
            $expired = daysNotice($date['unixtime']);
            $icon = ($expired === true) ? $inactive : $active;
        }

        echo '<h3>'.$icon.'&nbsp; '.$date_chosen.' <span> &mdash; '.$date['count'].' applicants</span> &nbsp;<a href="#" class="toggle" id="view_'.$key.'">show</a></h3>';

        $all_applicants = getApplicantsForDate($date_chosen);

        if (empty($all_applicants)) {
            echo '<p>No applicants chose this date.</p>';
            continue;
        }
    
        // Show applicants by location
        echo '<div class="location date_'.$key.'">';
        foreach ($locations as $kee => $loc) {

            $centre = ($loc == '') ? 'No Centre' : $loc;
            $applicants = getApplicantsByLocation($all_applicants, $loc);
            if (empty($applicants)) continue;

            echo PHP_EOL . PHP_EOL . '<h4>'.$centre.' <span>&ndash; '.count($applicants).' applicants</span> &nbsp;<a href="#" class="toggle_centre" id="centre_'.$key.'_'.$kee.'">show</a></h4>' . PHP_EOL;
            echo '<div class="centre location_'.$key.'_'.$kee.'">' . PHP_EOL;
            if ($loc == '') echo '<p>Contact applicant asking which location they want to attend.</p>';
            echo '<p><a href="interviews.php?date='.$key.'&amp;centre='.$kee.'">Print this list</a></p>' . PHP_EOL;

            echo '<table class="application_stats">' . PHP_EOL;
            echo "\t" . '<tr><th>&nbsp;</th><th>Name</th><th>Completed</th><th>Curriculum Area</th><th class="center">Application</th></tr>' . PHP_EOL;
            $c = 0;
            foreach ($applicants as $app) {
                $row_class = ($c % 2 == 0) ? 'r0' : 'r1';
                $curric_area = (strlen($app['curric_area']) > 30) ? substr($app['curric_area'], 0, 30) . '...' : $app['curric_area'];
                $curric_colour = (in_array($curric_area, $curric_areas)) ? $curric_area : 'no_curric';
                $app_details = array(
                    'email' => $app['email'],
                    'firstname' => $app['firstname'],
                    'interview_date' => $date_chosen,
                    'centre' => $centre
                );
                $mailto = generate_mailto($app_details);

                echo "\t" . '<tr class="'.$row_class.'">' . PHP_EOL;
                echo "\t\t" . '<td>'.($c + 1).'.</td>' . PHP_EOL;
                echo "\t\t" . '<td width="270">'.$app['name'].'</td>' . PHP_EOL;
                echo "\t\t" . '<td width="195">'.$app['completed'].'</td>' . PHP_EOL;
                echo "\t\t" . '<td width="240" class="'.$curric_colour.'">'.$curric_area.'</td>' . PHP_EOL;
                echo "\t\t" . '<td class="center">
                    <a href="view-application.php?id='.$app['id'].'" target="_blank"><img src="/course-application/images/icon-view.png" width="16" height="16" alt="View"></a> 
                    <a href="'.$mailto.'" class="email"><img src="/course-application/images/icon-email.png" width="16" height="16" alt="Email"></a></td>' . PHP_EOL;
                echo "\t" . '</tr>'. PHP_EOL;

                $c++;
                $print_data[$key][$kee][] = array($app['name'], $curric_area);
            }
            echo '</table>' . PHP_EOL;

            echo '</div>' . PHP_EOL;
            echo '<br />';

        }
        echo '</div>' . PHP_EOL;

    } // foreach

    echo '<br />';
} else {
    echo '<p>No interview dates found</p>';
}


echo '</div>' . PHP_EOL;
echo '</div>' . PHP_EOL;
echo '</div>' . PHP_EOL;
echo '</div>' . PHP_EOL;

    if (isset($_GET['date']) && is_numeric($_GET['date'])) {
        if (isset($_GET['centre']) && in_array($_GET['centre'], array(0,1,2))) {
            $date = $_GET['date'];
            $centre = $_GET['centre'];
            $centre_name = $locations[$centre];
            $interview_date = $interview_dates[$date]['date'];
            
            $print_html = '<div id="print_list">';
            $print_html .= '<div class="padding">';
            $print_html .= '<a id="close_list" href="#">&lt; Back to list</a>';
            $print_html .= "<h2>$interview_date</h2>";
            $print_html .= "<h3>$centre_name</h3>";
            $print_html .= '<table class="application_stats"><thead><tr><th width="280">Name</th><th width="140">Curriculum Area</th><th width="30">Attended</th><th>Comments</th></tr></thead>';
            $print_html .= '<tbody>';
            foreach ($print_data[$date][$centre] as $no => $data) {
                $row_class = ($no % 2 == 0) ? 'r0' : 'r1';
                $curric_colour = (isset($curric_colours[$data[1]])) ? ' style="color:'.$curric_colours[$data[1]].'; font-weight:bold;"' : ' style="color:#000;" ';
                $print_html .= '<tr class="'.$row_class.'"><td width="280">'.($no + 1).'. &nbsp; '.$data[0].'</td><td'.$curric_colour.' width="140">'.$data[1].'</td><td width="30">&nbsp;</td><td>&nbsp;</td></tr>';
            }
            $print_html .= '</tbody></table>';
            $print_html .= '<script type="text/javascript">window.print();</script>';
            $print_html .= '</div>';
            $print_html .= '</div>';
            echo $print_html;
        }
    }

}
?>
</body>
</html>
