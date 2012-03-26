<?php
	session_start();
	if (!isset($_SESSION['ws']['logged_in'])) {
		$_SESSION['ws']['logged_in'] = FALSE;
	}
	if ($_SESSION['ws']['logged_in'] === FALSE) {
		header('Location: index.php');
		exit;
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
	
	if (isset($_GET['logout']) && $_GET['logout'] == 1) {
		$_SESSION['ws']['logged_in'] = FALSE;
	}

	if (isset($_SESSION['ws']['logged_in']) && $_SESSION['ws']['logged_in'] === TRUE) {
	
	// Get leaderboard data 
	$query = "SELECT engineer, COUNT(engineer) AS completions FROM college_checklists WHERE emailed = 1 GROUP BY engineer ORDER BY completions DESC, engineer ASC";
	$sql->query($query);
	$engineers = array();
	if ($sql->num_rows() > 0) {
		while($sql->next_record()) {
			$engineers[] = array($sql->Record['engineer'], $sql->Record['completions']);
		}
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-AU" xml:lang="en-AU">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex" />
<meta name="googlebot" content="noindex" />
<title>Leaderboard - Engineer's Worksheet</title>
<script type="text/javascript" src="js/jquery-1.4.js"></script>
<link href="styles/engineers.css" rel="stylesheet" type="text/css" media="all" />
</head>

<body>

<div id="holder">

	<div id="instructions">
		<h1>Leaderboard</h1>
		
		<h2>Completed Worksheets</h2>
		<table class="engineer_rank">
<?php
	$no = 1;
	$arr_count = 0;
	$completions = '';
	foreach ($engineers as $engineer) {
		$completions = $engineer[1];
		if ($arr_count > 0) {
			$last_arr = $arr_count - 1;
			if (isset($engineers[$last_arr][1]) && ($engineers[$last_arr][1] > $completions)) {
				$no++;
			}
		}
		$ws_name = ($completions > 1) ? 'worksheets' : 'worksheet';
		if ($no == 1) {
			echo '<tr class="first"><td class="rank_no"><img src="styles/images/medal.png" width="32" height="32" alt="Medal" /></td><td class="valign_top">'.$engineer[0].'</td><td style="text-align:center;" class="valign_top">'.$completions.' '.$ws_name.'</td></tr>';
		} else {
			echo '<tr><td class="rank_no">'.$no.'</td><td>'.$engineer[0].'</td><td style="text-align:center;">'.$completions.' '.$ws_name.'</td></tr>';
		}
		$arr_count++;
	}
?>
	</table>
	
<?php
$query = "SELECT cch.engineer, COUNT(cch.engineer) AS completes FROM college_computers cc JOIN college_checklists cch ON cc.room = cch.room WHERE cc.complete = 1 and cch.engineer != '' GROUP BY cch.engineer ORDER BY completes DESC";
	$sql->query($query);
	$engineers = array();
	if ($sql->num_rows() > 0) {
		while($sql->next_record()) {
			$engineers[] = array($sql->Record['engineer'], $sql->Record['completes']);
		}
	}

?>
<br />
<h2>Completed Computers</h2>
<table class="engineer_rank">
<?php
	$no = 1;
	$arr_count = 0;
	$completions = '';
	foreach ($engineers as $engineer) {
		$completions = $engineer[1];
		if ($arr_count > 0) {
			$last_arr = $arr_count - 1;
			if (isset($engineers[$last_arr][1]) && ($engineers[$last_arr][1] > $completions)) {
				$no++;
			}
		}
		$ws_name = ($completions > 1) ? 'computers' : 'computer';
		if ($no == 1) {
			echo '<tr class="first"><td class="rank_no"><img src="styles/images/medal.png" width="32" height="32" alt="Medal" /></td><td class="valign_top">'.$engineer[0].'</td><td style="text-align:center;" class="valign_top">'.$completions.' '.$ws_name.'</td></tr>';
		} else {
			echo '<tr><td class="rank_no">'.$no.'</td><td>'.$engineer[0].'</td><td style="text-align:center;">'.$completions.' '.$ws_name.'</td></tr>';
		}
		$arr_count++;
	}
?>
	</table>
	<br /><br />	
	
	<p><a href="index.php">&lt; Back to worksheet</a></p>

	</div>

<?php } ?>
</body>
</html>