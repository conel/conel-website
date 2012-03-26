<?php
	session_start();
	
	$sess_lifetime = "7200";
	ini_set('session.gc_maxlifetime', $sess_lifetime);
	
	if (!isset($_SESSION['ws']['logged_in'])) {
		$_SESSION['ws']['logged_in'] = FALSE;
	}
	
	include_once('../matrix_engine/config.php');
	include_once('../matrix_engine/'.CLASSES_DIR.'db_mysql.php');

	// instantiate the SQL classes
	$sql = new DB_Sql();
	$sql->Database = 'worksheets';
	$sql->connect();
	$debug = 0; // 0 = Don't debug, 1 = debug

	// Secure bit
	$_SESSION['ws']['errors'] = array();
	
	if (isset($_POST['username']) && isset($_POST['password'])) {
		
		// Check login details
		if ($_POST['username'] == '') {
			$_SESSION['ws']['errors'][] = 'Blank username';
		}
		if ($_POST['password'] == '') {
			$_SESSION['ws']['errors'][] = 'Blank password';
		}
		
		if (count($_SESSION['ws']['errors']) == 0) {
		
			$username = stripslashes($_POST['username']);
			$username = mysql_real_escape_string($username, $sql->Link_ID);
			
			$password = stripslashes($_POST['password']);
			$password = mysql_real_escape_string($password, $sql->Link_ID);
			$encrypted_password = md5($password);
			
			$query = "SELECT * FROM college_users WHERE username='$username' AND password='$encrypted_password'";
			$sql->query($query, $debug);
			
			if ($sql->num_rows() > 0) {
				$_SESSION['ws']['logged_in'] = TRUE;
			} else {
				$_SESSION['ws']['errors'][] = 'Incorrect login details';
				$_SESSION['ws']['logged_in'] = FALSE;
			}
			
		}
	}
	
	if (isset($_GET['logout']) && $_GET['logout'] == 1) {
		$_SESSION['ws']['logged_in'] = FALSE;
	}

	if (isset($_SESSION['ws']['logged_in']) && $_SESSION['ws']['logged_in'] === TRUE) {

	$p = (isset($_GET['p']) && $_GET['p'] != '') ? mysql_real_escape_string($_GET['p'], $sql->Link_ID) : '';
	$s = (isset($_GET['s']) && $_GET['s'] != '') ? mysql_real_escape_string($_GET['s'], $sql->Link_ID) : '';
	$b = (isset($_GET['b']) && $_GET['b'] != '') ? mysql_real_escape_string($_GET['b'], $sql->Link_ID) : '';
	$f = (isset($_GET['f']) && $_GET['f'] != '') ? mysql_real_escape_string($_GET['f'], $sql->Link_ID) : '';
	$r = (isset($_GET['r']) && $_GET['r'] != '') ? mysql_real_escape_string($_GET['r'], $sql->Link_ID) : '';
	$emailed = (isset($_GET['emailed']) && $_GET['emailed'] != '') ? $_GET['emailed'] : '';

	// If Room is FOUND display it instead of the form
	$room_found = FALSE;
	$message = '';
	$monitors_exist = 0;

	// Look for a match
	//if ($p != '' && $s != '' && $b != '' && $f != '' && $r != '') {
	$work_on_msg = FALSE;
	if ($r != '' && $s != '') {
		$computers = array();
		$query = "SELECT id, computer_name, asset_tag, serial_no, order_no, complete, work_on, teaching_curric, manufacturer, model, image_replace, active_whiteboard FROM college_computers WHERE site = '$s' AND room = '$r' ORDER BY computer_name ASC";
		$sql->query($query);
		if ($sql->num_rows() > 0) {
			$room_found = TRUE;
			$c = 0;
			while($sql->next_record()) {
				$computers[$c]['id'] = $sql->Record['id'];
				$computers[$c]['computer_name'] = $sql->Record['computer_name'];
				$computers[$c]['asset_tag'] = $sql->Record['asset_tag'];
				$computers[$c]['serial_no'] = $sql->Record['serial_no'];
				$computers[$c]['order_no'] = $sql->Record['order_no'];
				$computers[$c]['complete'] = $sql->Record['complete'];
				$computers[$c]['teaching_curric'] = $sql->Record['teaching_curric'];
				$computers[$c]['manufacturer'] = $sql->Record['manufacturer'];
				$computers[$c]['model'] = $sql->Record['model'];
				$computers[$c]['image_replace'] = $sql->Record['image_replace'];
				$computers[$c]['active_whiteboard'] = ($sql->Record['active_whiteboard'] == 0) ? 'No' : 'Yes';
				$computers[$c]['work_on'] = $sql->Record['work_on'];
				if ($sql->Record['work_on'] == 0) { $work_on_msg = TRUE; }
				$c++;
			}

			// Get monitors if they exist
			$monitors = array();
			$query = "SELECT id, monitor_no, asset_tag, serial_no, order_no, complete FROM college_monitors WHERE site = '$s' AND room = '$r' ORDER BY id";
			$sql->query($query);
			if ($sql->num_rows() > 0) {
				$monitors_exist = 1;
				$c = 0;
				while($sql->next_record()) {
					$monitors[$c]['id'] = $sql->Record['id'];
					$monitors[$c]['monitor_no'] = $sql->Record['monitor_no'];
					$monitors[$c]['asset_tag'] = $sql->Record['asset_tag'];
					$monitors[$c]['serial_no'] = $sql->Record['serial_no'];
					$monitors[$c]['order_no'] = $sql->Record['order_no'];
					$monitors[$c]['complete'] = $sql->Record['complete'];
					$c++;
				}
			} else {
				$c = 0;
				foreach ($computers as $computer) {
					$monitors[$c]['id'] = '';
					$monitors[$c]['monitor_no'] = '';
					$monitors[$c]['asset_tag'] = '';
					$monitors[$c]['serial_no'] = '';
					$monitors[$c]['order_no'] = '';
					$monitors[$c]['complete'] = '';
					$c++;
				}
			}

		} else {
			//$message = 'Room not found, search again';
		}

	}
	$match_html = '';
	
	// If room is not found based on at LEAST site and room, then check if room parameter is set
	if ($room_found === FALSE && $r != '' && $p == '' && $s == '' && $b == '' && $f == '') {
		// Look for matches on room including site as some room names ANNOYINGLY exist in both Tottenham and Enfield (looking at you C2002).
		$query = "SELECT DISTINCT site, room, phase FROM college_computers WHERE room = '$r' ORDER BY site, phase";
		$sql->query($query);
		$no_results = $sql->num_rows();
		
		// Found more than one
		if ($no_results > 1) {
			// More than one room found for this search, display all possible results
			$match_html .= '<h1>More than one match for '.$r.'</h1>';
			$match_html .= '<p>Select the match you want below.</p><br />';
			
			$match_html .= '<table id="match_table"><tr><th>Phase</th><th>Site</th><th>Building</th><th>Floor</th><th>Room</th><th>Selection</th></tr>';
			
			$matches = array();
			while($sql->next_record()) {
				$matches[] = array($sql->Record['site'], $sql->Record['room']);	
			}
			
			foreach ($matches as $value) {
				// for each match we need to look up details for it
				$query = "SELECT cc.phase, cc.site, cc.building, cc.floor, cc.room, cb.name FROM college_computers cc JOIN college_buildings cb ON cc.building = cb.id WHERE cc.room = '".$value[1]."' AND cc.site = ".$value[0]." LIMIT 1";
				$sql->query($query);
				if ($sql->num_rows() > 0) {
					while($sql->next_record()) {
						$phase = $sql->Record['phase'];
						$site = $sql->Record['site'];
						$building = $sql->Record['building'];
						$floor = $sql->Record['floor'];
						$room = $sql->Record['room'];
						$building_name = $sql->Record['name'];
						
						$match_link = "<a href=\"http://www.conel.ac.uk/engineers/index.php?p=$phase&s=$site&b=$building&f=$floor&r=$room\">View Room</a>";
						$site_name = ($site == 1) ? 'Tottenham' : 'Enfield';
						$match_html .= "<tr><td>$phase</td><td>$site_name</td><td>$building_name</td><td>$floor</td><td>$room</td><td>$match_link</td></tr>";
					}
				}
			}
			$match_html .= "</table>";
			
		} else if ($no_results == 1) {
			// Found one match, redirect to match!
			$query = "SELECT phase, site, building, floor, room FROM college_computers WHERE room = '$r' LIMIT 1";
			$sql->query($query);
			if ($sql->num_rows() > 0) {
				while($sql->next_record()) {
					$phase = $sql->Record['phase'];
					$site = $sql->Record['site'];
					$building = $sql->Record['building'];
					$floor = $sql->Record['floor'];
					$room = $sql->Record['room'];
				}
				// Now redirect to this match!
				$redirect_url = "http://www.conel.ac.uk/engineers/index.php?p=$phase&s=$site&b=$building&f=$floor&r=$room";
				header('Location: '.$redirect_url.'');
				exit;
			}
		}
	}
	

		$checklist_exists = 0;
		// Work out if checklist data exists for this room. Set a boolean value to pass to the email/update
		$query = "SELECT * FROM college_checklists WHERE site = '$s' AND room = '$r'";
		$sql->query($query);
		if ($sql->num_rows() > 0) {
			$checklist_exists = 1;
		}

		// Get all phases from the college_computers table
		$phases = array();
		$query = "SELECT DISTINCT phase FROM college_computers";
		$sql->query($query);
		if ($sql->num_rows() > 0) {
			while($sql->next_record()) {
				$phases[] = $sql->Record['phase'];
			}
		}

		if ($checklist_exists == 1) {
			$query = "SELECT * FROM college_checklists WHERE site = '$s' AND room = '$r'";
			$sql->query($query);
			if ($sql->num_rows() > 0) {
				while($sql->next_record()) {
					$task1 = $sql->Record['task1'];
					$task2 = $sql->Record['task2'];
					$task3 = $sql->Record['task3'];
					$task4 = $sql->Record['task4'];
					$task5 = $sql->Record['task5'];
					$task6 = $sql->Record['task6'];
					$task7 = $sql->Record['task7'];
					$task8 = $sql->Record['task8'];

					$engineer_is = $sql->Record['engineer'];
					$notes = $sql->Record['notes'];

					$has_been_emailed = $sql->Record['emailed'];
					$email_date = $sql->Record['email_date'];
				}
			}
		} else {
			$task1 = '';
			$task2 = '';
			$task3 = '';
			$task4 = '';
			$task5 = '';
			$task6 = '';
			$task7 = '';
			$task8 = '';

			$engineer_is = '';
			$notes = '';

			$has_been_emailed = '';
			$email_date = '';
		}
		$buildings = array();
		$query = "SELECT * FROM college_buildings ORDER BY site DESC, name";

		if ($p != '' && is_numeric($p)) {
			if ($s != '' && is_numeric($s) && $s == 1 || $s == 2) {
				$query = "SELECT DISTINCT cb.id, cb.name FROM college_computers cc INNER JOIN college_buildings cb ON cc.building = cb.id WHERE cc.phase = $p and cc.site = $s ORDER BY cc.site ASC, cb.name";
			} else {
				$query = "SELECT DISTINCT cb.id, cb.name FROM college_computers cc INNER JOIN college_buildings cb ON cc.building = cb.id WHERE cc.phase = $p ORDER BY cc.site ASC, cb.name";
			}
		}
		$sql->query($query);
		if ($sql->num_rows() > 0) {
			while($sql->next_record()) {
				$building_id = $sql->Record['id'];
				$buildings[$building_id] = $sql->Record['name'];
			}
		}

		// Get all floors from the college_computers table
		$floors = array();
		$query = "SELECT DISTINCT floor FROM college_computers ORDER BY floor ASC";
		if ($p != '' && is_numeric($p)) {
			if ($s != '' && is_numeric($s) && $s == 1 || $s == 2) {
				if ($b != '' && is_numeric($b)) {
					$query = "SELECT DISTINCT floor FROM college_computers WHERE phase = $p AND site = $s AND building = $b ORDER BY floor ASC";
				} else {
					$query = "SELECT DISTINCT floor FROM college_computers WHERE phase = $p AND site = $s ORDER BY floor ASC";
				}
			} else {
				$query = "SELECT DISTINCT floor FROM college_computers WHERE phase = $p ORDER BY floor ASC";
			}
		}
		$sql->query($query);
		if ($sql->num_rows() > 0) {
			while($sql->next_record()) {
				$floors[] = $sql->Record['floor'];
			}
		}

		// Get all rooms
		$rooms = array();
		$query = "SELECT DISTINCT cc.room, ch.emailed AS complete FROM college_computers cc LEFT JOIN college_checklists ch ON cc.room = ch.room ORDER BY cc.room ASC";
		
		if ($p != '' && is_numeric($p)) {
			if ($s != '' && is_numeric($s) && $s == 1 || $s == 2) {
				if ($b != '' && is_numeric($b)) {
					if ($f != '') {
						$query = "SELECT DISTINCT cc.room, ch.emailed AS complete FROM college_computers cc LEFT JOIN college_checklists ch ON cc.room = ch.room WHERE cc.phase = $p AND cc.site = $s AND cc.building = $b AND cc.floor = '$f' ORDER BY cc.room ASC";
						//$query = "SELECT DISTINCT room FROM college_computers WHERE phase = $p AND site = $s AND building = $b AND floor = '$f' ORDER BY room ASC";
					} else {
						$query = "SELECT DISTINCT cc.room, ch.emailed AS complete FROM college_computers cc LEFT JOIN college_checklists ch ON cc.room = ch.room WHERE cc.phase = $p AND cc.site = $s AND cc.building = $b ORDER BY cc.room ASC";
						//$query = "SELECT DISTINCT room FROM college_computers WHERE phase = $p AND site = $s AND building = $b ORDER BY room ASC";
					}
				} else {
					$query = "SELECT DISTINCT cc.room, ch.emailed AS complete FROM college_computers cc LEFT JOIN college_checklists ch ON cc.room = ch.room WHERE cc.phase = $p AND cc.site = $s ORDER BY cc.room ASC";
					//$query = "SELECT DISTINCT room FROM college_computers WHERE phase = $p AND site = $s ORDER BY room ASC";
				}
			} else {
				$query = "SELECT DISTINCT cc.room, ch.emailed AS complete FROM college_computers cc LEFT JOIN college_checklists ch ON cc.room = ch.room WHERE cc.phase = $p ORDER BY cc.room ASC";
				//$query = "SELECT DISTINCT room FROM college_computers WHERE phase = $p ORDER BY room ASC";
			}
		}
		$sql->query($query);
		if ($sql->num_rows() > 0) {
			while($sql->next_record()) {
				$completed = ($sql->Record['complete'] == 1) ? 1 : 0;
				$rooms[] = array($sql->Record['room'], $completed);
			}
		}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-AU" xml:lang="en-AU">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex" />
<meta name="googlebot" content="noindex" />
<title>Engineer's Worksheet</title>
<script type="text/javascript" src="js/jquery-1.4.js"></script>
<!--<script type="text/javascript" src="js/iphone-style-checkboxes.js"></script>-->
<script type="text/javascript" src="js/jquery.colorbox-min.js"></script>
<script type="text/javascript" src="js/functions.js"></script>
<link href="styles/engineers.css" rel="stylesheet" type="text/css" media="all" />
<!--<link href="styles/style.css" rel="stylesheet" type="text/css" media="all" />-->
<link href="styles/colorbox.css" rel="stylesheet" type="text/css" media="all" />
</head>

<body>
<div id="room_select">
<form action="<?php echo $_SERVER["PHP_SELF"];?>" method="get" name="filter_selection">
<table width="100%">
	<tr>
		<td>Phase:</td>
		<td>
			<select name="p" onchange="this.form.submit()">
				<option value="">--</option>
<?php
	foreach ($phases as $phase) {
		$selected = '';
		if ($phase == $p) {
			$selected = ' selected="selected"';
		}
		echo '<option value="'.$phase.'"'.$selected.'>'.$phase.'</option>';
	}
?>	
			</select>
		</td>
		<td>Site:</td>
		<td>
			<select name="s" onchange="this.form.submit();">
				<option value="">--</option>
				<option value="1"<?php if ($s == 1) { echo ' selected="selected"'; } ?>>Tottenham</option>
				<option value="2"<?php if ($s == 2) { echo ' selected="selected"'; } ?>>Enfield</option>	
			</select>
		</td>
		<td>Building:</td>
		<td>
			<select name="b" onchange="this.form.submit()">
				<option value="">--</option>
<?php
	foreach ($buildings as $key => $value) {
		$selected = '';
		if ($key == $b) {
			$selected = ' selected="selected"';
		}
		echo "<option value=\"$key\"$selected>$value</option>\n";
	}
?>	
			</select>
		</td>
		<td>Floor:</td>
		<td>
			<select name="f" onchange="this.form.submit()">
				<option value="">--</option>
<?php
	foreach ($floors as $floor) {
		$selected = '';
		if ($floor == $f) {
			$selected = ' selected="selected"';
		}
		echo '<option value="'.$floor.'"'.$selected.'>'.$floor.'</option>';
	}
?>	
			</select>
		</td>
		<td>Room:</td>
		<td>
			<select name="r" onchange="this.form.submit()">
				<option value="">--</option>
<?php

	foreach ($rooms as $room) {
		$selected = '';
		if ($room[0] == $r) {
			$selected = ' selected="selected"';
		}
		$completed = ($room[1] == 1) ? '**' : '';
		echo "<option value=\"".$room[0]."\"$selected>".$room[0]."".$completed."</option>\n";
	}
?>	
			</select>
		</td>
		<td><input type="submit" value="View" id="view_filters" /></td>
		<td style="text-align:right;" width="200">
			<a href="index.php" title="Reset Filters">Reset</a>
			&nbsp;
			<a href="#" id="bug_found">Found a bug?</a>
			&nbsp;&nbsp;&nbsp;
			<a href="index.php?logout=1">Logout</a>
		</td>
	</tr>
</table>
</form>
</div>

<div id="holder">
<?php 
	
	if ($message != '') {
		echo '<p class="status">'.$message.'</p>';
	}

		if (!$room_found) {
		
			if ($match_html != '') {
			
				echo '<div id="instructions">';
				echo $match_html;
				echo '</div>';
				
			} else {
?>

	<div id="instructions">
		<h1>Select Room</h1>
		<p><strong>Use the form above to select a room</strong></p>
		<p>Choosing the filter elements on the left will refresh the choices on the right.</p>
		<br /><br />
		<p><strong>Selecting rooms not in current phase</strong></p>
		<p>If you don't know which phase a room comes under <a href="index.php">click here</a> to reset filters then select the room from the total list of rooms. 
		<br />It will redirect and fill in the filters for you.</p>
	</div>

<?php 
}
} ?>

<?php if ($room_found) {
?>
<h1 id="room_heading">Room <?php echo $r; ?><?php if ($has_been_emailed != '') { echo '&nbsp; <span class="completed">&ndash; Completed**</span>'; } ?></h1>
<form action="email.php?email=1" method="post" id="worksheet_form">

<div id="save_state"><input type="submit" value="Save" /></div>

<div id="checklist">
<table>
	<thead>
		<tr>
			<th>PC Name</th>
			<th>Asset Tag</th>
			<th>Serial Number</th>
			<th>Order Number</th>
			<th>Complete</th>
		</tr>
	</thead>
	<tbody>
<?php
	$x = 0;
	foreach ($computers as $value) {
		$work_class = ($value['work_on'] == 0) ? ' class="dont_work_on"' : ''; 
		
		if ($work_on_msg === TRUE && $x == 0) { echo '<tr><td colspan="5" class="error">Don\'t do the computers in red &ndash; see \'Engineer Notes\' for more info<br /><br /></td></tr>'; }
		echo '<tr'.$work_class.'>';
		
		if ($value['image_replace'] != '' && $value['active_whiteboard'] == 'Yes') {
echo '<td class="valign_top"><span title="'.$value['manufacturer'].' - '.$value['model'].' 
('.$value['teaching_curric'].')
Active Whiteboard: '.$value['active_whiteboard'].'
'.$value['image_replace'].'" class="vtip">'.$value['computer_name'].'</span></td>';	
		} else if ($value['image_replace'] != '' && $value['active_whiteboard'] == 'No') {
echo '<td class="valign_top"><span title="'.$value['manufacturer'].' - '.$value['model'].' 
('.$value['teaching_curric'].')
'.$value['image_replace'].'" class="vtip">'.$value['computer_name'].'</span></td>';
		} else {
echo '<td class="valign_top"><span title="'.$value['manufacturer'].' - '.$value['model'].' 
('.$value['teaching_curric'].')" class="vtip">'.$value['computer_name'].'</span></td>';
		}

			$asset_tag = $value['asset_tag'];
			if ($asset_tag != '') {
				$asset_tag_html = '<input type="text" name="'.$value['id'].'_asset_tag" class="asset_tag disabled" value="'.$value['asset_tag'].'" />';
			} else {
				$asset_tag_html = '<input type="text" name="'.$value['id'].'_asset_tag" class="asset_tag" />';
			}
			echo '<td class="valign_top">'.$asset_tag_html.'</td>';
			$serial_no = $value['serial_no'];
			if ($serial_no != '') {
				$serial_no_html = '<input type="text" name="'.$value['id'].'_serial_no" class="serial_no disabled" value="'.$value['serial_no'].'" />';
			} else {
				$serial_no_html = '<input type="text" name="'.$value['id'].'_serial_no" class="serial_no" />';
			}
			echo '<td class="valign_top">'.$serial_no_html.'</td>';

			$order_no = $value['order_no'];
			if ($order_no != '') {
				$order_no_html = '<input type="text" name="'.$value['id'].'_order_no" class="order_no disabled" value="'.$value['order_no'].'" />';
			} else {
				$order_no_html = '<input type="text" name="'.$value['id'].'_order_no" class="order_no" />';
			}
			echo '<td class="valign_top">'.$order_no_html.'</td>';
			$checked = (isset($value['complete']) && $value['complete'] == 1) ? ' checked="checked"' : '';
			echo '<td style="text-align:center;">
<label for="comp_'.$value['id'].'"><input type="checkbox" class="checkbox" name="complete_'.$value['id'].'" id="comp_'.$value['id'].'" value="1"'.$checked.' /></label>
				</td>';

		echo '</tr>';
		$x++;
	}

?>
	</tbody>
</table>

<br />

<table>
	<thead>
		<tr>
			<th>Monitor</th>
			<th>Asset Tag</th>
			<th>Serial Number</th>
			<th>Order Number</th>
			<th>Complete</th>
		</tr>
	</thead>
	<tbody>
<?php
	$no = 1;
	
	echo '<!-- ';
	echo '<pre>';
	var_dump($monitors);
	echo '</pre>';
	echo '--> ';
	
	foreach ($monitors as $value) {
		echo '<tr>';
			echo '<td class="valign_top">Monitor '.$no.'</td>';
			$asset_tag = $value['asset_tag'];
			if ($asset_tag != '') {
				$asset_tag_html = '<input type="text" name="asset_tag_'.$no.'" class="asset_tag disabled" value="'.$value['asset_tag'].'" />';
			} else {
				$asset_tag_html = '<input type="text" name="asset_tag_'.$no.'" class="asset_tag" />';
			}
			echo '<td class="valign_top">'.$asset_tag_html.'</td>';
			$serial_no = $value['serial_no'];
			if ($serial_no != '') {
				$serial_no_html = '<input type="text" name="serial_no_'.$no.'" class="serial_no disabled" value="'.$value['serial_no'].'" />';
			} else {
				$serial_no_html = '<input type="text" name="serial_no_'.$no.'" class="serial_no" />';
			}
			echo '<td class="valign_top">'.$serial_no_html.'</td>';

			$order_no = $value['order_no'];
			if ($order_no != '') {
				$order_no_html = '<input type="text" name="order_no_'.$no.'" class="order_no disabled" value="'.$value['order_no'].'" />';
			} else {
				$order_no_html = '<input type="text" name="order_no_'.$no.'" class="order_no" />';
			}
			echo '<td class="valign_top">'.$order_no_html.'</td>';
			$checked = (isset($value['complete']) && $value['complete'] == 1) ? ' checked="checked"' : '';
			echo '<td style="text-align:center;">
<label for="mon_'.$no.'"><input type="checkbox" class="checkbox" name="monitor_complete_'.$no.'" id="mon_'.$no.'" value="1"'.$checked.' /></label>
				</td>';

		echo '</tr>';
		$no++;
	}
?>
</tbody>
</table>
</div>

<div id="task_complete">
	<h2>Checklist (All boxes must be completed)</h2>
	<table width="100%">
		<tr>
			<th>Task</th>
			<th>Complete</th>
		</tr>
		<tr class="r0">
			<td class="valign_top"><label for="task1">Asset tag PC and Monitor</label></td>
			<?php $checked = (isset($task1) && $task1 == '1') ? ' checked="checked"' : ''; ?>
			<td class="center"><input type="checkbox" name="task1" id="task1" value="1" <?php echo $checked; ?>/></td>
		</tr>
		<tr class="r1">
			<td class="valign_top"><label for="task2">Rename computer (Only necessary if new install)</label></td>
			<?php $checked = (isset($task2) && $task2 == '1') ? ' checked="checked"' : ''; ?>
			<td class="center"><input type="checkbox" name="task2" id="task2" value="1" <?php echo $checked; ?>/></td>
		</tr>
		<tr class="r0">
			<td class="valign_top"><label for="task3">Configure and test Active Whiteboard</label></td>
			<?php $checked = (isset($task3) && $task3 == '1') ? ' checked="checked"' : ''; ?>
			<td class="center"><input type="checkbox" name="task3" id="task3" value="1" <?php echo $checked; ?> /></td>
		</tr>
		<tr class="r1">
			<td class="valign_top"><label for="task4">Peripherals installed and configured tested</label></td>
			<?php $checked = (isset($task4) && $task4 == '1') ? ' checked="checked"' : ''; ?>
			<td class="center"><input type="checkbox" name="task4" id="task4" value="1" <?php echo $checked; ?> /></td>
		</tr>
		<tr class="r0">
			<td class="valign_top"><label for="task5">AMT and SCCM working</label></td>
			<?php $checked = (isset($task5) && $task5 == '1') ? ' checked="checked"' : ''; ?>
			<td class="center"><input type="checkbox" name="task5" id="task5" value="1" <?php echo $checked; ?> /></td>
		</tr>
		<tr class="r1">
			<td class="valign_top"><label for="task6">EPO Agent installed correctly</label></td>
			<?php $checked = (isset($task6) && $task6 == '1') ? ' checked="checked"' : ''; ?>
			<td class="center"><input type="checkbox" name="task6" id="task6" value="1" <?php echo $checked; ?>/></td>
		</tr>
		<tr class="r0">
			<td class="valign_top"><label for="task7">Outlook opened correctly from test user account</label></td>
			<?php $checked = (isset($task7) && $task7 == '1') ? ' checked="checked"' : ''; ?>
			<td class="center"><input type="checkbox" name="task7" id="task7" value="1" <?php echo $checked; ?> /></td>
		</tr>
		<tr class="r1">
			<td class="valign_top"><label for="task8">Check printer installed from test user account</label></td>
			<?php $checked = (isset($task8) && $task8 == '1') ? ' checked="checked"' : ''; ?>
			<td class="center"><input type="checkbox" name="task8" id="task8" value="1" <?php echo $checked; ?> /></td>
		</tr>
	</table>

	<br />
	<hr />

	<h2>Engineer</h2>
	<table>
		<tr>
			<td class="form_label">Your Name:</td>
			<td>
				<select name="engineer" id="engineer_choice">
					<option value="">Select Yourself...</option>
					<?php 
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
						foreach ($engineers as $engineer) {
							if ($engineer_is != '' && $engineer_is == $engineer) {
								echo '<option value="'.$engineer.'" selected="selected">'.$engineer.'</option>';
							} else {
								echo '<option value="'.$engineer.'">'.$engineer.'</option>';
							}
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="form_label valign_top">Date:</td>
			<td class="date"><?php echo date('d/m/Y', time()); ?></td>
		</tr>
<?php
	if ($has_been_emailed != '') {
		echo '<tr>';
			echo '<td class="form_label valign_top">Date Emailed:</td>';
			echo '<td class="date">'.date('d/m/Y - G:i:s', $email_date).'</td>';
		echo '</tr>';
	}
?>
		<tr>
			<td colspan="2" class="form_label">Notes: <br />
				<textarea name="notes" id="notes" cols="35" rows="6"><?php echo $notes; ?></textarea>
				<br />
				<span id="charlimitinfo">450 characters left</span> 
			</td>
		</tr>	
		<tr>
			<td colspan="2">
				<input type="hidden" name="room" value="<?php echo $r; ?>" />
				<input type="hidden" name="site" value="<?php echo $s; ?>" />
				<?php
					if ($has_been_emailed == '') {
				?>
					<input type="submit" value="Email Completed Worksheet" />
				<?php } else { ?>
					<p style="color:red; font-weight:bold;">Worksheet already submitted.<br /><br />You can update this worksheet using the 'Save' button.</span>
				<?php } ?>
			</td>
		</tr>
	</table>
</form>

</div>
<br class="clear_both" />
<?php } ?>
<?php
	$valid_codes = array('1', '0', '2', '3', '42', '69');
	if ($emailed != '' && in_array($emailed, $valid_codes)) {
	// colourbox code to pop-up message
	echo '
	<script type="text/javascript">
		$(document).ready(function(){
			$.colorbox({
				onOpen:function(){ $("#message").show(); },
				onCleanup:function(){ $("#message").hide(); },
					width:"50%", inline:true, href:"#message"
			});
		});
	</script>
	';
	echo '<div id="message">';
		if ($emailed == '1') {
			echo '<h2>Email Sent</h1>';
			echo '<p>Completed worksheet saved and sent to Cheryl.</p>';
			$images = array();
			$images[1] = '<img src="images/1.gif" width="450" height="297" alt="Thumbs Up!" />';
			$images[2] = '<img src="images/2.gif" width="300" height="200" alt="Thumbs Up!" />';
			$images[3] = '<img src="images/3.gif" width="500" height="286" alt="Thumbs Up!" />';
			$images[4] = '<img src="images/4.gif" width="259" height="264" alt="Thumbs Up!" />';
			$images[5] = '<img src="images/5.gif" width="300" height="169" alt="Thumbs Up!" />';
			$images[6] = '<img src="images/6.gif" width="500" height="281" alt="Thumbs Up!" />';
			$images[7] = '<img src="images/7.gif" width="464" height="350" alt="Thumbs Up!" />';
			$images[8] = '<img src="images/8.gif" width="277" height="197" alt="Thumbs Up!" />';
			$images[9] = '<img src="images/9.gif" width="261" height="238" alt="Thumbs Up!" />';
			$images[10] = '<img src="images/10.gif" width="200" height="270" alt="Thumbs Up!" />';
			$images[11] = '<img src="images/11.jpg" width="563" height="385" alt="Thumbs Up!" />';
			$images[12] = '<img src="images/12.jpg" width="360" height="360" alt="Thumbs Up!" />';
			$images[13] = '<img src="images/13.gif" width="408" height="214" alt="Thumbs Up!" />';
			$images[14] = '<img src="images/14.gif" width="320" height="240" alt="Thumbs Up!" />';
			
			$rand_image = rand(1,14);

			echo '<p>'.$images[$rand_image].'</p>';
			
		} else if ($emailed == '0') {
			echo '<h2>Email Failed</h1>';
			echo '<p>Completed worksheet saved but not <em>sent</em> to Cheryl.</p>'; 
			echo '<p><img src="images/yuno.png" width="229" height="161" alt="Thumbs Up!" /></p>';
			echo '<p><strong>WHY U NO SEND MAILR?</strong></p>';
			echo '<p>Tell me about this fail by using the \'Found a bug?\' link, K THX BYE - Nathan</p>';
		} else if ($emailed == '2') {
			echo '<h2>Bug Report Sent</h1>';
			echo '<p>Thanks for submitting this bug report.</p>';
			echo '<p><img src="images/cat-borg.jpg" width="500" height="361" alt="Borg cat!" /></p>';
		} else if ($emailed == '3') {
			echo '<h2>You are smart :)</h1>';
		} else if ($emailed == '42') {
			echo '<h2>Booooring.</h1>';
		}else if ($emailed == '69') {
			echo '<h2>You have a dirty mind. Har har.</h1>';
		}
	echo '</div>';
	}
?>
<div id="email_bugs">
	<h2>Found a bug?</h2>
	<p class="highlight">If you've found a bug or incorrect data use this form to send me a message.<br />Include a description of the bug and your name &ndash; in case I need more info.<br />&mdash; Nathan (phone ext. 3113)</p>
	<br />
	<form action="email-bug.php" method="post">
		<?php
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
		echo '<strong>Engineer:</strong> ';
		echo '<select name="bug_engineer">';
		echo '<option value="">Select Yourself...</option>';
		foreach ($engineers as $engineer) {
			if ($engineer_is != '' && $engineer_is == $engineer) {
				echo '<option value="'.$engineer.'" selected="selected">'.$engineer.'</option>';
			} else {
				echo '<option value="'.$engineer.'">'.$engineer.'</option>';
			}
		}
		echo '</select><br /><br />';
?>
		<strong>Bug Description:</strong> <br />
		<textarea name="bug_message" cols="30" rows="6"></textarea>
		<br />
		<input type="submit" value="Email Bug Report" />
	</form>
	<br />
	<p class="smaller"><strong>Note:</strong> You may find rooms showing on the wrong floor. This is due to incomplete data provided to me.<br />If you come across this you can filter by 'Site' then select the room from the total list of rooms for the site you're working at.</p>
</div>
</div>
<div id="footer"><a href="leaderboard.php">Leaderboard</a> :)</div>
<?php } else { 

	// Not logged in
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-AU" xml:lang="en-AU">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex" />
<meta name="googlebot" content="noindex" />
<title>Engineer's Worksheet</title>
<script type="text/javascript" src="js/jquery-1.4.js"></script>
<link href="styles/engineers.css" rel="stylesheet" type="text/css" media="all" />
</head>

<body>
<div id="holder">
	<h1>Engineer's Worksheet</h1>
	<!-- Login -->
	<div class="section">
		<h2>Login</h2>
		<form method="post" action="index.php">
			<table id="engineers_login">
				<tr><td><strong>Username:</strong></td><td><input type="text" name="username" class="text" /></td></tr>
				<tr><td><strong>Password:</strong></td><td><input type="password" name="password" class="text" /></td></tr>
				<tr><td>&nbsp;</td><td><input type="submit" value="Login &gt;" class="submit" /></td></tr>
			</table>
		</form>
	<!-- //Login -->
<?php
		if (isset($_SESSION['ws']['errors']) && count($_SESSION['ws']['errors']) > 0) {
			echo '<div class="error">';
			echo '<h2>Errors</h2>';
			echo '<ul>';
			foreach ($_SESSION['ws']['errors'] as $error) {
				echo "<li>$error</li>";
			}
			echo '</ul>';
			echo '</div>';
		}
	
?>
	</div>
</div>
<?php } ?>
</body>
</html>