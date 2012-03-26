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
	include_once('../matrix_engine/'.CLASSES_DIR.'db_mysql.php');
	include_once('../matrix_engine/'.CLASSES_DIR.'class_mailer.php');

	// instantiate the SQL classes
	$sql = new DB_Sql();
	$sql->Database = 'worksheets';
	$sql->connect();
	$debug = 0; // 0 = Don't debug, 1 = debug

	// Email - if not email, redirects to same page else just saves (for ajax saving)
	$email_this = (isset($_GET['email']) && $_GET['email'] == 1) ? TRUE : FALSE;

	// Room
	$room = (isset($_POST['room']) && $_POST['room'] != '') ? mysql_real_escape_string($_POST['room'], $sql->Link_ID) : '';
	$site = (isset($_POST['site']) && $_POST['site'] != '') ? mysql_real_escape_string($_POST['site'], $sql->Link_ID) : '';

	// Check referer is index.php
	$referer = $_SERVER['HTTP_REFERER'];
	/*
	if (strstr($referer, 'http://www.conel.ac.uk/engineers/index.php') && isset($_POST) && $room != '') {
	}
	*/

	// Get ids of all computers tied to this room
	$query = "SELECT id FROM college_computers WHERE room = '$room' AND site = '$site' ORDER BY computer_name";
	$sql->query($query);
	if ($sql->num_rows() > 0) {
		$comp_ids = array();
		while($sql->next_record()) {
			$comp_ids[] = $sql->Record['id'];
		}
	}

	// Loop over computer entries and update values
	foreach ($comp_ids as $comp) {

		// Complete
		$complete_str = 'complete_' . $comp;
		$complete_val = $_POST[$complete_str];
		$complete_comp = (isset($complete_val) && $complete_val == 1) ? 1 : 0;

		// Asset Tag
		$asset_str = $comp . '_asset_tag';
		$asset = (isset($_POST[$asset_str])) ? mysql_real_escape_string($_POST[$asset_str], $sql->Link_ID) : '';

		// Serial No
		$serial_str = $comp . '_serial_no';
		$serial = (isset($_POST[$serial_str])) ? mysql_real_escape_string($_POST[$serial_str], $sql->Link_ID) : '';
		
		// Order No
		$order_str = $comp . '_order_no';
		$order = (isset($_POST[$order_str])) ? mysql_real_escape_string($_POST[$order_str], $sql->Link_ID) : '';

		// Update the record in the database
		$query = "UPDATE college_computers SET complete = '$complete_comp', asset_tag = '$asset', serial_no = '$serial', order_no = '$order' WHERE id = $comp";

		$sql->query($query, $debug);

	}

	// Enter checklist data into checklists table
	// Whitelist
	$engineers = array(
		'Abdalla Alkhalaf',
		'Anwar Dubbad',
		'Daniel Ansah', 
		'Harry Collins',
		'Joe Olds',
		'Joe Safe', 
		'John Ghann',
		'Jonathan Locke',
		'Kevin Mupondori',
		'Ranjan Goonatilaka', 
		'Serkan Sozcu',
		'Shane Dacosta'
	);	
	$engineer = (isset($_POST['engineer']) && in_array($_POST['engineer'], $engineers)) ? mysql_real_escape_string($_POST['engineer'], $sql->Link_ID) : '';

	$task1 = (isset($_POST['task1']) && $_POST['task1'] == 1) ? 1 : 0;
	$task2 = (isset($_POST['task2']) && $_POST['task2'] == 1) ? 1 : 0;
	$task3 = (isset($_POST['task3']) && $_POST['task3'] == 1) ? 1 : 0;
	$task4 = (isset($_POST['task4']) && $_POST['task4'] == 1) ? 1 : 0;
	$task5 = (isset($_POST['task5']) && $_POST['task5'] == 1) ? 1 : 0;
	$task6 = (isset($_POST['task6']) && $_POST['task6'] == 1) ? 1 : 0;
	$task7 = (isset($_POST['task7']) && $_POST['task7'] == 1) ? 1 : 0;
	$task8 = (isset($_POST['task8']) && $_POST['task8'] == 1) ? 1 : 0;

	$notes = (isset($_POST['notes']) && $_POST['notes'] != '') ? mysql_real_escape_string($_POST['notes'], $sql->Link_ID) : '';

	// Info collected: update database
	// Look for checklists via a database call instead of an unreliable flag
	$query = "SELECT id FROM college_checklists WHERE site = $site AND room = '$room'";
	$sql->query($query);
	if ($sql->num_rows() > 0) {
		$checklist_exists = TRUE;
	} else {
		$checklist_exists = FALSE;
	}
	
	$date_now = time();

	// If data does not exist we can add date_created and do an INSERT
	if ($checklist_exists === FALSE) {
		$query = "INSERT INTO college_checklists (site, room, task1, task2, task3, task4, task5, task6, task7, task8, engineer, notes, date_created) VALUES ('$site', '$room', '$task1', '$task2', '$task3', '$task4', '$task5', '$task6', '$task7', '$task8', '$engineer', '$notes', '$date_now')";
	} else {
		// We're updating a room's checklist
		$query = "UPDATE college_checklists SET site = '$site', task1 = '$task1', task2 = '$task2', task3 = '$task3', task4 = '$task4', task5 = '$task5', task6 = '$task6', task7 = '$task7', task8 = '$task8', engineer = '$engineer', notes = '$notes', date_modified = '$date_now' WHERE room = '$room'";
	}
	$sql->query($query, $debug);
	
	
	$query = "SELECT id FROM college_monitors WHERE site = $site AND room = '$room'";
	$sql->query($query);
	if ($sql->num_rows() > 0) {
		$monitors_exist = TRUE;
	} else {
		$monitors_exist = FALSE;
	}

	$count = 1;
	// using no of computers to work out how many monitors to add/update
	foreach ($comp_ids as $notused) {
		$monitor_no = $count;
		$asset_tag = (isset($_POST['asset_tag_'.$count])) ? mysql_real_escape_string($_POST['asset_tag_'.$count], $sql->Link_ID) : '';
		$serial_no = (isset($_POST['serial_no_'.$count])) ? mysql_real_escape_string($_POST['serial_no_'.$count], $sql->Link_ID) : '';
		$order_no = (isset($_POST['order_no_'.$count])) ? mysql_real_escape_string($_POST['order_no_'.$count], $sql->Link_ID) : '';
		$complete_mon = (isset($_POST['monitor_complete_'.$count]) && $_POST['monitor_complete_'.$count] == '1') ? 1 : 0;
		$count++;

		if ($monitors_exist === FALSE) {
			$query = "INSERT INTO college_monitors (site, room, monitor_no, asset_tag, serial_no, order_no, complete) VALUE ('$site', '$room','$monitor_no','$asset_tag','$serial_no','$order_no', '$complete_mon')";
		} else {
			$query = "UPDATE college_monitors SET asset_tag = '$asset_tag', serial_no = '$serial_no', order_no = '$order_no', complete = '$complete_mon' WHERE room = '$room' AND site = '$site' AND monitor_no = '$monitor_no'";
		}
		$sql->query($query, $debug);
	}
	
	if (!$email_this) {
		// Return 'saved' message
		echo 'Saved';
		//echo '<a href="'.$referer.'">Back to Referer</a>';

	} else {
		//echo '<a href="'.$referer.'">Back to Referer</a>';

			$date_now = date('d/m/Y, H:i:s');
			
			// Create email
			
			$body_html = '
			<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
			<html>
			<head><title>Engineer Worksheet</title>
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
			$body_html .= '<p>You received an engineer worksheet from '.$_POST['engineer'].'.<br /><br /><strong>Submitted:</strong> '.$date_now.'</p>';
			
			// Phase, Site, Building, Floor, Room
			$query = "SELECT cc.*, cb.name FROM college_computers cc JOIN college_buildings cb ON cc.building = cb.id WHERE cc.room = '$room' AND cc.site = '$site' ORDER BY cc.computer_name";
			$sql->query($query);
			$ws_url;
			if ($sql->num_rows() > 0) {
				$i = 0;
				$computers = array();
				while($sql->next_record()) {
					$computers[$i]['computer_name'] = $sql->Record['computer_name'];
					$computers[$i]['asset_tag'] = $sql->Record['asset_tag'];
					$computers[$i]['serial_no'] = $sql->Record['serial_no'];
					$computers[$i]['order_no'] = $sql->Record['order_no'];
					$computers[$i]['complete'] = ($sql->Record['complete'] == 1) ? 'Yes' : 'No';
					
					if ($i == 0) {
						$phase = $sql->Record['phase'];
						$site_name = (isset($sql->Record['site']) && $sql->Record['site'] == 1) ? 'Tottenham' : 'Enfield';
						$building = $sql->Record['building'];
						$building_name = $sql->Record['name'];
						$floor = $sql->Record['floor'];
						$i++;
						
						// Build URL to link to worksheet page
						$ws_url = "http://www.conel.ac.uk/engineers/index.php?p=$phase&s=".$sql->Record['site']."&b=$building&f=$floor&r=$room";
						//$ws_url = urlencode($ws_url);
					}
					$i++;
				}
			}
			$body_html .= '<h2>Engineer</h2>';
			$body_html .= '<p>'.$engineer.'</p>';
			
			$body_html .= '<h2>Worksheet Details</h2>';
			$body_html .= '<table border="1" cellpadding="2" cellspacing="0" width="300">';
			$body_html .= '<tr><td><strong>Phase:</strong></td><td>'.$phase.'</td></tr>';
			$body_html .= '<tr><td><strong>Site:</strong></td><td>'.$site_name.'</td></tr>';
			$body_html .= '<tr><td><strong>Building:</strong></td><td>'.$building_name.'</td></tr>';
			$body_html .= '<tr><td><strong>Floor:</strong></td><td>'.$floor.'</td></tr>';
			$body_html .= '<tr><td><strong>Room:</strong></td><td>'.$room.'</td></tr>';
			$body_html .= '</table>';

			$body_html .= '<h2>Computers</h2>';
			$body_html .= '<table border="1" cellpadding="2" cellspacing="0" width="440">';
			$body_html .= '<tr><th>Computer Name</th><th>Asset Tag</th><th>Serial No</th><th>Order No</th><th>Complete</th></tr>';
			foreach($computers as $key => $value) {
				$body_html .= '<tr>';
				foreach ($computers[$key] as $k => $v) {
					$fieldname = ucfirst(str_replace('_',' ',$k));	
					$body_html .= "<td valign=\"top\">$v</td>";
				}
				$body_html .= '</tr>';
			}
			$body_html .= '</table>';
			
			$query = "SELECT * FROM college_monitors WHERE room = '$room' AND site = '$site'";
			$sql->query($query);
			if ($sql->num_rows() > 0) {
				$i = 0;
				$monitors = array();
				while($sql->next_record()) {
					$monitors[$i]['monitor_no'] = $sql->Record['monitor_no'];
					$monitors[$i]['asset_tag'] = $sql->Record['asset_tag'];
					$monitors[$i]['serial_no'] = $sql->Record['serial_no'];
					$monitors[$i]['order_no'] = $sql->Record['order_no'];
					$monitors[$i]['complete'] = ($sql->Record['complete'] == 1) ? 'Yes' : 'No';
					$i++;
				}
			}
			$body_html .= '<h2>Monitors</h2>';
			$body_html .= '<table border="1" cellpadding="2" cellspacing="0" width="440">';
			$body_html .= '<tr><th>Monitor No</th><th>Asset Tag</th><th>Serial No</th><th>Order No</th><th>Complete</th></tr>';
			foreach($monitors as $key => $value) {
				$body_html .= '<tr>';
				foreach ($monitors[$key] as $k => $v) {
					$fieldname = ucfirst(str_replace('_',' ',$k));	
					$body_html .= "<td valign=\"top\">$v</td>";
				}
				$body_html .= '</tr>';
			}
			$body_html .= '</table>';
			
			$task1 = (isset($task1) && $task1 == 1) ? 'Yes' : 'No';
			$task2 = (isset($task2) && $task2 == 1) ? 'Yes' : 'No';
			$task3 = (isset($task3) && $task3 == 1) ? 'Yes' : 'No';
			$task4 = (isset($task4) && $task4 == 1) ? 'Yes' : 'No';
			$task5 = (isset($task5) && $task5 == 1) ? 'Yes' : 'No';
			$task6 = (isset($task6) && $task6 == 1) ? 'Yes' : 'No';
			$task7 = (isset($task7) && $task7 == 1) ? 'Yes' : 'No';
			$task8 = (isset($task8) && $task8 == 1) ? 'Yes' : 'No';

			// Task Completion
			$body_html .= '<h2>Task Checklist</h2>';
			$body_html .= '
			<table border="1" cellpadding="2" cellspacing="0" width="440">
				<tr>
					<th>Task</th>
					<th>Complete</th>
				</tr>
				<tr>
					<td class="valign_top">Asset tag PC and Monitor</td>
					<td>'.$task1.'</td>
				</tr>
				<tr>
					<td class="valign_top">Rename computer (Only necessary if new install)</td>
					<td>'.$task2.'</td>
				</tr>
				<tr>
					<td class="valign_top">Configure and test Active Whiteboard</td>
					<td>'.$task3.'</td>
				</tr>
				<tr>
					<td class="valign_top">Peripherals installed and configured tested</td>
					<td>'.$task4.'</td>
				</tr>
				<tr>
					<td class="valign_top">AMT and SCCM working</td>
					<td>'.$task5.'</td>
				</tr>
				<tr>
					<td class="valign_top">EPO Agent installed correctly</td>
					<td>'.$task6.'</td>
				</tr>
				<tr>
					<td class="valign_top">Outlook opened correctly from test user account</td>
					<td>'.$task7.'</td>
				</tr>
				<tr>
					<td class="valign_top">Check printer installed from test user account</td>
					<td>'.$task8.'</td>
				</tr>
			</table>';
			
			if (isset($_POST['notes']) && $_POST['notes'] != '') {
				$body_html .= '<h2>Notes</h2>';
				// Replace \r\n with a break tag
				$notes_are = (isset($_POST['notes'])) ? $_POST['notes'] : $notes;
				$notes_content = str_replace("\r\n", "<br />", $notes_are);
				$body_html .= '<p>'.$notes_content.'</p>';
			}
			
			$body_html .= '<br />You can view this worksheet online by <a href="'.$ws_url.'">clicking here</a>.';
			
			$body_html .= '</body></html>';
		
			// If database insert successful, send email:
			$mail = new phpmailer();
			$mail->IsHTML(TRUE); // send HTML email
			$mail->IsSMTP(); // use SMTP to send
			//$mail->AddAddress('NKowald@staff.conel.ac.uk', 'Nathan Kowald');
			$mail->AddAddress('CAdams@staff.conel.ac.uk', 'Cheryl Adams');
			$mail->AddBCC('NKowald@staff.conel.ac.uk', 'Nathan Kowald');
			$mail->Subject = "Engineer Worksheet - Room $room";
			$mail->From = 'webmaster@staff.conel.ac.uk';
			$mail->FromName = 'Conel Website';
			$mail->Body = $body_html;
			//$mail->SMTPDebug = TRUE;

			$result = $mail->Send(); // send email notification!
			
			if ($result) {
				// If email was sent successfully: update college_checklists and set emailed = 1 and email_date
				$date_now = time();
				$query = "UPDATE college_checklists SET emailed = '1', email_date = '$date_now' WHERE room = '$room' AND site = '$site'";
				$sql->query($query, $debug);

				$referer .= '&emailed=1';
				header('Location: '.$referer.'');
				exit;
			} else {
				/*
				echo '<p>Email failed</p>';
				echo '<p><a href="'.$referer.'">Go back</a> and submit it again.</p>'; 
				exit;
				*/
				$referer .= '&emailed=0';
				header('Location: '.$referer.'');
				exit;
			}
	}
?>
