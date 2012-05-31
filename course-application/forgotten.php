<?php
	session_start();
	if (!isset($_SESSION['ca']['logged_in'])) {
		$_SESSION['ca']['logged_in'] = FALSE;
	}

	include('../matrix_engine/config.php');
	include('../matrix_engine/'.CLASSES_DIR.'db_mysql.php');
	include('../matrix_engine/'.CLASSES_DIR.'class_mailer.php');

    include('Cache.class.php');

	// instantiate the SQL classes
	$sql = new DB_Sql();
	$sql->connect();
	$debug = 1; // 0 = Don't debug, 1 = debug
	
	$show = (isset($_GET['show']) && $_GET['show'] != '') ? $_GET['show'] : 1;
	
	if ($show > 3 || !is_numeric($show)) $show = 1;
	
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

    function buildJSONFromArray(Array $data, $show) {

        $json = "{\r\n";
        $json .= "\t\"aaData\": [\r\n";

        $c = 1;
        foreach ($data as $d) {
            
            $id = (isset($d['id']) && $d['id'] != '') ? $d['id'] : '';
            $email = (isset($d['ea']) && $d['ea'] != '') ? $d['ea'] : '';
            $ref_id = (isset($d['rid']) && $d['rid'] != '') ? $d['rid'] : '';
            $firstname = (isset($d['fn']) && $d['fn'] != '') ? $d['fn'] : '&ndash;';
            $surname = (isset($d['sn']) && $d['sn'] != '') ? $d['sn'] : '&ndash;';

            // Incomplete Applications
            if ($show == 1) {

                $page_step = (isset($d['ps']) && $d['ps'] != '')  ? $d['ps'] : '';
                $percent = ($page_step != '') ? round((($page_step / 9) * 100), 0) . '%' : '0%';
                $resume_link = urlencode('http://www.conel.ac.uk/course-application/index.php?email='.$email.'&ref_id='.$ref_id);
                $actions = "<a href=\"view-application.php?id=$id\" title=\"View Application\"><img src=\"images/icon-view.png\" width=\"16\" height=\"16\" alt=\"View\" /></a> <a href=\"mailto:$email&amp;subject=Resume Your Online Application&amp;Body=To resume your saved application visit: $resume_link and click 'Continue Application'.\" title=\"Email User Their Reference ID\"><img src=\"images/icon-email.png\" width=\"16\" height=\"16\" alt=\"Email User\" /></a>";
                $json_data = array($email, $ref_id, addcslashes($firstname, '"'), addcslashes($surname, '"'), $percent, addcslashes($actions, '"'));

            } else if ($show == 2) {

                // Incomplete Applications
                $actions = "<a href=\"view-application.php?id=$id\" title=\"View Application\"><img src=\"images/icon-view.png\" width=\"16\" height=\"16\" alt=\"View\" /></a>";
                $json_data = array($email, $ref_id, addcslashes($firstname, '"'), addcslashes($surname, '"'), addcslashes($actions, '"'));

            }

            $json .= "\t\t[\r\n";

            // Add content
            $n = 1;
            foreach ($json_data as $jd) {
                $json .= "\t\t\t\"$jd\"";
                if ($n != count($json_data)) $json .= ",";
                $json .= "\r\n";
                $n++;
            }

            if ($c != count($data)) {
                $json .= "\t\t],\r\n";
            } else {
                $json .= "\t\t]\r\n";
            }
            
            $c++;
        }

        $json .= "\t]\r\n";
        $json .= "}";

        return $json;

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
<link href="css/jquery.dataTables.css" rel="stylesheet" type="text/css" media="all" />
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery.dataTables.min.js"></script>
</head>

<body>

<div id="holder">
	<img src="../layout/img/banner_new.gif" width="955" height="84" alt="The College of Haringey, Enfield and North East London" id="banner" />
	<h1>Course Applications</h1>

    <noscript><div id="js_error">This page requires JavaScript.<br />Follow <a href="http://www.enable-javascript.com/" target="_blank">these instructions</a> to enable JavaScript in your web browser.</div></noscript>
	
	
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
			<a href="forgotten.php?show=1"<?php if ($show == 1) echo ' class="active"'; ?>>Incomplete</a> | 
			<a href="forgotten.php?show=2"<?php if ($show == 2) echo ' class="active"'; ?>>Complete</a> | 
			<!--<a href="/course-application/forgotten.php?show=3"<?php //if ($show == 3) echo ' class="active"'; ?>>Incomplete (since 01/09/11)</a> | -->
			<a href="interviews.php">Interviews</a>
		</p>
		<div id="logout"><a href="forgotten.php?logout=1">Log out</a></div>
		<br />
		
<?php
	if ($show == 1) {

?>
<script type="text/javascript">
$(document).ready(function() {

    $.ajaxSetup({'type': 'POST', 'url':'get_json_data.php', 'dataType': 'json' });
    $.extend($.fn.dataTable.defaults, {'sServerMethod': 'POST' });
    
    $('#incomplete').dataTable({
        'bProcessing': true,
        'sAjaxSource': $.ajaxSettings.url,
        'fnServerParams':function(aoData) { 
            aoData.push({"name":"wcY2E7kmMKDfyMB0E2", "value":"incomplete"});
        },
        'bDeferRender': true,
        'iDisplayLength':10,
        'aoColumns': [
                /* email */     null,
                /* ref id */    null,
                /* firstname */ null,
                /* surname */   null,
                /* complete */  null,
                /* actions */  {'bSearchable': false, 'bSortable': false}
        ]
        
    });
});
</script>
		
<?php
        // Set cache time 
        $cache_time = 3600;
        Cache::init('incomplete-applications.cache', $cache_time);
        if (Cache::cacheFileExists() === true) {
            $incompletes = Cache::getCache();
        } else {
            $query = "SELECT id, firstname, surname, email_address, reference_id, page_step FROM tbl_course_application WHERE form_completed = 0 ORDER BY email_address ASC";
            $sql->query($query, $debug);
            if ($sql->num_rows() > 0) {
                $incompletes = array();
                $i = 0;
                while($sql->next_record()) {
                    $incompletes[$i]['id'] = $sql->Record['id'];
                    $incompletes[$i]['fn'] = $sql->Record['firstname'];
                    $incompletes[$i]['sn'] = $sql->Record['surname'];
                    $incompletes[$i]['ea'] = $sql->Record['email_address'];
                    $incompletes[$i]['rid'] = $sql->Record['reference_id'];
                    $incompletes[$i]['ps'] = $sql->Record['page_step'];
                    $i++;
                }
                Cache::setCache($incompletes);
            }
        }
        
        $filename = 'incomplete.txt';
        Cache::init($filename, $cache_time);
        if (Cache::cacheFileExists() === false) {
            $json = buildJSONFromArray($incompletes, $show);
            $filepath = dirname($_SERVER['DOCUMENT_ROOT']) . '/secure/' . $filename;
            file_put_contents($filepath, $json);
        }

        $html = '<h2>Incomplete</h2>';
        $html .= '<p id="incomplete_count">'.number_format(count($incompletes)).' incomplete applications</p>';
        $html .= '<table class="application_stats" id="incomplete">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Email</th>';
        $html .= '<th>Reference ID</th>';
        $html .= '<th>Firstname</th>';
        $html .= '<th>Surname</th>';
        $html .= '<th>Complete</th>';
        $html .= '<th>Actions</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        $html .= '<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '<br />';
        $html .= '<br />';
        $html .= '<br />';
        echo $html;
	}
							
	if ($show == 3) {
		
		$query = "SELECT id, firstname, surname, email_address, reference_id, page_step FROM tbl_course_application WHERE datetime_submitted_first >= '2011-09-01 00:00:00' AND form_completed = 0 ORDER BY email_address ASC";

		$sql->query($query, $debug);
		if ($sql->num_rows() > 0) {
			
?>

<script type="text/javascript">
	$(document).ready(function() {
		$('table.application_stats a.email_user').bind('click', function(e) {
			$(e.target).closest('tr').children('td,th').css('background-color','#FFFF79');
		});

        $('#since_sept').dataTable({
            'iDisplayLength':10,
            'aoColumns': [
                    /* no */        null,
                    /* email */     null,
                    /* ref id */    null,
                    /* firstname */ null,
                    /* surname */   null,
                    /* complete */  null,
                    /* actions */  {'bSearchable': false, 'bSortable': false}
            ]
        });
        
	});

    function isIE() {
        return (navigator.appName == 'Microsoft Internet Explorer') ? true : false;
    }
        
    function create_email(email, resume_link) {
        resume_link = encodeURIComponent(resume_link);
        var link = 'mailto:' + email + '&subject=Resume Your Online Application&Body=Dear Applicant,%0D%0DThe online application you submitted to the College of Haringey Enfield and North East London is incomplete.%0DTo resume your saved application visit: '+ resume_link +' then click \'Continue Application\'.%0D%0DYou may like to come along to our College Open Days in May to see our teaching facilities and speak with tutors about your course options, you can register your attendance on http://www.conel.ac.uk/for_learners/open_days %0D%0DPlease do not hesitate to contact us if you have any queries.%0D%0DKind regards,%0D%0DLearner Recruitment Team%0DE-mail: admissions@conel.ac.uk%0DTel: 020 8442 3055 / 020 8442 3103';
        if (isIE()) {
            window.open(link);
        } else {
            document.location.href = link;
        }
    }
</script>

<?php 
			echo '<h2>Incomplete - since 1 September 2011</h2>';
			echo '<p id="incomplete_count">'.number_format($sql->num_rows()).' incomplete applications</p>';
			echo '<table class="application_stats" id="since_sept">';
			echo '<thead>';
			echo '<tr>';
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
					//$resume_link = urlencode($resume_link);
                    //$open_days_link = 'http://www.conel.ac.uk/for_learners/open_days';
                    //$open_days_link = urlencode($open_days_link);

                    echo '<td style="text-align:center;"><!--<a href="view-application.php?id='.$id.'" title="View Application"><img src="images/icon-view.png" width="16" height="16" alt="View User\'s Application" /></a>-->';
                    /* old
                    <a href="mailto:'.$sql->Record['email_address'].'&amp;subject=Resume Your Online Application&amp;Body=Dear Applicant,%0D%0D
                    The online application you submitted to the College of Haringey Enfield and North East London is incomplete.%0DTo resume your saved application visit: '.$resume_link.' then click \'Continue Application\'.%0DYou may like to come along to our College Open Days in May to see our teaching facilities and speak with tutors about your course options, you can register your attendance on '.$open_days_link.'%0D%0D
                    Please do not hesitate to contact us if you have any queries.%0D%0D
                    Kind regards,%0D%0D
                    Learner Recruitment Team%0D
                    E-mail: admissions@conel.ac.uk%0D
                    Tel: 020 8442 3055 / 020 8442 3103" title="Email User" class="email_user" target="_blank">
                    */
                    echo '<a href="javascript:create_email(\''.$sql->Record['email_address'].'\', \''.$resume_link.'\');" title="Email User" class="email_user"><img src="images/icon-email.png" width="16" height="16" alt="Email User Reference ID" /></a></td>';
				echo '</tr>';
				$i++;
			}
			echo '</tbody>';
			echo '</table>';
			echo '<br />';
			echo '<br />';
			echo '<br />';
		}
	}

	if ($show == 2) {

?>
<script type="text/javascript">
$(document).ready(function() {

    $.ajaxSetup({'type': 'POST', 'url':'get_json_data.php', 'dataType': 'json' });
    $.extend($.fn.dataTable.defaults, {'sServerMethod': 'POST' });
    
    $('#complete').dataTable({
        'bProcessing': true,
        'sAjaxSource': $.ajaxSettings.url,
        'fnServerParams':function(aoData) { 
            aoData.push({"name":"wcY2E7kmMKDfyMB0E2", "value":"complete"});
        },
        'bDeferRender': true,
        'iDisplayLength':10,
        'aoColumns': [
                /* email */     null,
                /* ref id */    null,
                /* firstname */ null,
                /* surname */   null,
                /* actions */  {'bSearchable': false, 'bSortable': false}
        ]
        
    });
});
</script>
		
<?php
    // Set cache time 
    $cache_time = 3600;
    Cache::init('complete-applications.cache', $cache_time);
    if (Cache::cacheFileExists() === true) {
        $completes = Cache::getCache();
    } else {
        $query = "SELECT id, firstname, surname, email_address, reference_id FROM tbl_course_application WHERE form_completed = 1 ORDER BY email_address ASC";
        $sql->query($query, $debug);
        if ($sql->num_rows() > 0) {
            $completes = array();
            $i = 0;
            while($sql->next_record()) {
                $completes[$i]['id'] = $sql->Record['id'];
                $completes[$i]['fn'] = $sql->Record['firstname'];
                $completes[$i]['sn'] = $sql->Record['surname'];
                $completes[$i]['ea'] = $sql->Record['email_address'];
                $completes[$i]['rid'] = $sql->Record['reference_id'];
                $i++;
            }
            Cache::setCache($completes);
        }
    }
    $filename = 'complete.txt';
    Cache::init($filename, $cache_time);
    if (Cache::cacheFileExists() === false) {
        $json = buildJSONFromArray($completes, $show);
        $filepath = dirname($_SERVER['DOCUMENT_ROOT']) . '/secure/' . $filename;
        file_put_contents($filepath, $json);
    }

    $html = '<h2>Complete</h2>';
    $html .= '<p id="complete_count">'.number_format(count($completes)).' completed applications</p>';
    $html .= '<table class="application_stats" id="complete">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th>Email</th>';
    $html .= '<th width="130">Reference ID</th>';
    $html .= '<th>Firstname</th>';
    $html .= '<th>Surname</th>';
    $html .= '<th>Actions</th>';
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';
    $html .= '<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
    $html .= '</tbody>';
    $html .= '</table>';
    $html .= '<br />';
    $html .= '<br />';
    $html .= '<br />';

    echo $html;

	}
						
?>	
	</div>
<?php
	}
?>
</div>
</div>
</body>
</html>
