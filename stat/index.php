<?php

session_start();

//if(!isset($_SESSION['user'])) {
/*
	if(isset($_POST['username']) && isset($_POST['password'])) {
		if($_POST['username']=='coneladmin' && $_POST['password']=='conel8') {
			$_SESSION['user']='admin';
			header('location: index.php');
			exit;
		}
	}
*/
?>

	<form method="post">
		<label id="username">Username:</label><input name="username" type="text" />
		<label id="password">Password:</label><input name="password" type="password" />
		<input type="submit" name="submit" value="Submit" />
	</form>

<?php
//} else {
?>
	<form method="get">
		<label id="logfile">Logfile:</label>
		<select name="logfile" />
			<option value="0">CLG</option>
			<option value="1" <?php echo isset($_GET['logfile']) && $_GET['logfile'] == 1 ? 'selected="selected"' : ''; ?> >VLE</option>
		</select>
		<input type="submit" value="Submit" />
	</form>
<?php

	$logfiles = array('clg.log', 'vle.log');
	
	$logfile = isset($_GET['logfile']) ? (int) $_GET['logfile'] : 0; 
	
	//$log = file_get_contents('vle.log');
	$log = file_get_contents($logfiles[$logfile]);

	//debug($log);

	$lines = explode("\n", $log);
	array_pop($lines);

	//debug($lines);

	$currdate = '';
	$datecount = 0;
	$dates = array();
	$reqbydate = array();

	foreach($lines as $line) {
		$l = explode(' ', $line);
		//debug($l);
		
		if($l[0]!=$currdate) {
			$currdate=$l[0];
			$dates[]=$currdate;
			$datecount++;
		}
		
		$reqbydate[$currdate][$l[1]] = $l[3];
	}

	//print "datecount: $datecount<br>";
	//debug($dates);
	//debug($reqbydate);

	$counter = array();

	foreach($dates as $date) {
		$counter[$date] = array_count_values($reqbydate[$date]);
	}

	//debug($counter);

	print '<table cellpadding="5" cellspacing="5"><tbody><tr>';
	
	foreach($counter as $dt => $cnt) {
		
		arsort(&$cnt);
		//debug($cnt,$dt);
		
		print '<td valign="top">';
		
		print "<table border='1' cellpadding='5' cellspacing='5'>";
		
		print "<thead><tr><th>$dt</th><th>".array_sum($cnt)."</th></tr></thead>";	//(".date('l',strtotime(str_replace('/','-',$dt),time())).")
		
		print '<tbody>';
		
		foreach($cnt as $ip => $hitcount){
			print "<tr " . ( preg_match('/^192./',$ip) ? 'style="color:red;"' : '') . "><td>$ip</td><td>$hitcount</td></tr>";
		}
		
		print '</tbody>';
		
		print '</table>';
		
		print '</td>';
	}
	print '</tr></tbody></table>';
//}

function debug($v,$n='debug'){
	print "$n:<pre><br>";
	print_r ($v);
	print '</pre><br><br>';
}

?>
