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
	include_once('../matrix_engine/'.CLASSES_DIR.'class_mailer.php');

	// instantiate the SQL classes
	$sql = new DB_Sql();
	$sql->Database = 'worksheets';
	$sql->connect();
	$debug = 1; // 0 = Don't debug, 1 = debug
	
	if (isset($_POST['submit'])) {
	
     $fname = $_FILES['sel_file']['name']; 
     $chk_ext = explode(".", $fname);

     if(strtolower($chk_ext[1]) == "csv") {
     
         $filename = $_FILES['sel_file']['tmp_name'];
         $handle = fopen($filename, "r");
		 
		 // some data comes from form
		 $site = $_POST['site'];
		 $phase = $_POST['phase'];
    
		 $ins_counter = 0;
         while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		 
			 // Every record will contain a computer so check against that
			 if ($data[3] != '') {
				 $building = $data[0];
				 $floor_no = $data[1];
				 if ($data[2] != '') {
					$room = $data[2];
				 } else {
					$room = $room;
				 }
				 $computer_name = $data[3];
				 $manufacturer = $data[4];
				 $model = $data[5];
				 $teach_curric = $data[6];
				 $active_wboard = ($data[7] == 1) ? 1 : 0;
				 $img_replace = $data[8];
				 $serial_no = $data[9];
				 $asset_tag = ($data[10] == 'NONE') ? '' : $data[10];
				 
				$query = "INSERT into college_computers (
						site,
						phase,
						building,
						floor,
						room,
						computer_name,
						manufacturer,
						model,
						teaching_curric,
						active_whiteboard,
						image_replace,
						serial_no,
						asset_tag
					) values(
						'$site', 
						'$phase',
						'$building',
						'$floor_no',
						'$room', 
						'$computer_name', 
						'$manufacturer', 
						'$model', 
						'$teach_curric', 
						'$active_wboard', 
						'$img_replace', 
						'$serial_no', 
						'$asset_tag'
					)";
						
				$sql->query($query, $debug);
				$ins_counter++;
			 }
		 }

    
         fclose($handle);
		 $message = "Successfully Imported $ins_counter records";
     } else {
		 $message = "Invalid File";
     }    
}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-AU" xml:lang="en-AU">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex" />
<meta name="googlebot" content="noindex" />
<title>Import CSV Data</title>
<link href="styles/engineers.css" rel="stylesheet" type="text/css" media="all" />
<script type="text/javascript" src="layout/js/jquery-1.6.min.js"></script>
</head>

<body>
<div id="holder">
<?php
	if (isset($message) && $message != '') {
		echo '<p class="status">'.$message.'</p>';
	}
?>
	<h1>Import CSV</h1>
	<br class="clear_both" />

<form action="<?php echo $_SERVER["PHP_SELF"];?>" method="post" enctype="multipart/form-data" >
<table id="add_csv">
	<tr>
		<td class="label" width="80">Site: </td>
		<td>
			<select name="site">
				<option>Select Site...</option>
				<option value="1">Tottenham</option>
				<option value="2">Enfield</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="label">Phase: </td>
		<td>
			<select name="phase">
				<option>Select Phase...</option>
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
				<option value="6">6</option>
				<option value="7">7</option>
				<option value="8">8</option>
				<option value="9">9</option>
				<option value="10">10</option>
				<option value="11">11</option>
			</select>
		</td>
	</tr>

<!--
	<tr>
		<td class="label">Buildings: </td>
		<td>
<?php

	/*
	echo '<div class="tot">';
	echo '<p class="centre"><strong>Tottenham</strong></p>';

	foreach ($tot_buildings as $key => $name) {
		echo '<input type="checkbox" name="building" value="'.$key.'" id="build'.$key.'" /> <label for="build'.$key.'">'.$name.'</label><br />';
	}

	echo '</div>';
	
	echo '<div class="enf">';
	echo '<p class="centre"><strong>Enfield</strong></p>';

	foreach ($enf_buildings as $key => $name) {
		echo '<input type="checkbox" name="building" value="'.$key.'" id="build'.$key.'" /> <label for="build'.$key.'">'.$name.'</label><br />';
	}
	echo '</div>';
	 */
?>
	<br class="clear_both" />
	</td>
	</tr>
-->
	<tr>
		<td class="label">CSV file:</td>		
		<td><input type="file" name="sel_file" />
		<p class="key"><strong>CSV Format:</strong><br />Building, Floor, Room, Computer Name, Manufacturer, Model, Teaching/Curric, Active Whiteboard PC [1 = yes, 0 = no], Image/Replace, Serial Number, Asset Tag</p>
		</td>		
	</tr>
	<tr>
		<td>&nbsp;</td>		
		<td><input type="submit" name="submit" value="Import" class="submit" /></td>		
	</tr>
</table>
</form>
</div>
</body>
</html>
