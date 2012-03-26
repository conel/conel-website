<?php

	function get_duration($start_date, $end_date) {
	
		$this_year = date('Y'); // Get current date as year
		$this_month = strtolower(date('F')); // Get current date as month

		$months = array('january','february','march','april','may','june','july','august','september','october','november','december');

		$past_months = array();
		// nkowald - 2010-05-24 - Break happened after the array add, updated
		foreach ($months as $month) {
			if ($this_month == $month) {
				break;
			}
			$past_months[] = $month;
		}

		$days_per_month = array(20,15,23,11,19,18,2,0,18,17,21,14);

		$days_full_year = 0;
		$days_full_year = array_sum($days_per_month);

		$qual_start = ereg_replace("[^0-9]", "", $start_date);
		// nkowald - 2012-02-17 - strip first two characters from string
		$qual_start = substr($qual_start, 2);
		$qual_start_month = eregi_replace("[^A-Z]", "", $start_date);
		$qual_end = ereg_replace("[^0-9]", "", $end_date);
		// nkowald - 2012-02-17 - strip first two characters from string
		$qual_end = substr($qual_end, 2);
		$qual_end_month = eregi_replace("[^A-Z]", "", $end_date);
		
		$no_years = $qual_end - $qual_start;
		$no_years = ($no_years == '0' || $no_years == '1') ? '1' : $no_years;
		$year_text = ($no_years > 1) ? 'years' : 'year';
		
		// nkowald - 2010-01-08 - Need to generate "weeks per year" value from start and end course date
		
		// Calculate months between start and end dates
		$month_no_start = (array_search(strtolower($qual_start_month), $months));
		$month_no_end = (array_search(strtolower($qual_end_month), $months));
		
		$months_course = '';
		
		// Work out the number of months
		if ($qual_end > $qual_start) {
			$no_years = $qual_end - $qual_start;
			
			if ($no_years == 1) {
			
				$days_course = 0;
				// work out how many days from course start to end of year
				while ($month_no_start <= 11) {
					$days_course += $days_per_month[$month_no_start];
					$month_no_start++;
				}
				// work out how many days from start of year to end of course
				$month_no = 0;
				while ($month_no <= $month_no_end) {
					$days_course += $days_per_month[$month_no];
					$month_no++;
				}

				$weeks_per_year = round($days_course / 5,0); // 5 working days in a "week"

			} else {
				/* More than 1 year courses */
			
				// use the 1 year duration equation adding total days for n years.
				// work out how many days from course start to end of year
				$days_course = 0;
				while ($month_no_start <= 11) {
					$days_course += $days_per_month[$month_no_start];
					$month_no_start++;
				}
				// work out how many days from start of year to end of course
				$month_no = 0;
				while ($month_no <= $month_no_end) {
					$days_course += $days_per_month[$month_no];
					$month_no++;
				}
				// Now add n years worth of days
				$total_days_full_year = ($no_years - 1) * $days_full_year;
				$days_course += $total_days_full_year;
				
				$weeks_per_year = round($days_course / 5,0); // 5 working days in a "week"
				
			}
		} elseif ($qual_end == $qual_start) {
			// If course is within year, we only need to work out how many weeks from course start to course end
			$month = $month_no_start;
			$days_course = 0;
			while ($month >= $month_no_start && $month <= $month_no_end) {
				$days_course += $days_per_month[$month];
				$month++;
			}
			
			$weeks_per_year = round($days_course / 5, 0); // 5 working days in a "week"
		}
		
		$week_data = '';
		if (is_numeric($weeks_per_year) && $weeks_per_year != 0) {
			$week_data = $weeks_per_year.' weeks, '.$no_years.' '.$year_text;
		} else {
			$week_data = $no_years.' '.$year_text;
		}
		
		return $week_data;
	
	}

	// $filter is an array of occurrence ids to filter by
	function export_csv($filter='') {

		$host = 'localhost';
		$user = 'root';
		$pass = '1ctsql';
		$db = 'conel';
	
		$separator = ',';

		$link = mysql_connect($host, $user, $pass) or die("Can not connect." . mysql_error());
		mysql_select_db($db) or die("Can not connect.");

		$result = mysql_query("SHOW COLUMNS FROM tbloccurrences");
		$i = 0;
		if (mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_assoc($result)) {
				$values[] = '"'.$row['Field'].'"';
				$i++;
			}
		}
		// nkowald - add value for course duration
		$values[] = '"Duration"';
		
		// implode values
		
		$csv_output .= implode(',', $values);
		$csv_output .= "\n";
		
		// If Filter array given, filter by ids
		if ($filter != '' && is_array($filter)) {
			$ids = implode(',', $filter);
			$query = $query = "SELECT * FROM tbloccurrences WHERE id IN ($ids) ORDER BY Unit_id";
		} else {
			$query = "SELECT * FROM tbloccurrences ORDER BY Unit_id";
		}
		
		$values = mysql_query($query);
		
		while ($rowr = mysql_fetch_row($values)) {
			for ($j=0; $j < $i; $j++) {
				// check if separator is used in value, if so enclose in quotes
					$value = '"'.$rowr[$j].'"';
					
					if ($j == ($i - 1)) {
						$csv_output .= $value;	
					} else {
						$csv_output .= $value . $separator;	
					}
					
					if ($j == 4) {
						$start_date = $value;
					} else if ($j == 5) {
						$end_date = $value;
					}
						
			}
			// Get duration based on start and end dates
			$duration = get_duration($start_date, $end_date);
			// strip commas
			//$duration = str_replace(',', ' -', $duration);
			$duration = '"'.$duration.'"';
			
			$csv_output .= "," . $duration;
			$csv_output .= "\n";
		}

		// Convert underscores to hyphens
		$table = 'tbloccurrences';
		// old naming structure - included time
		//$filename = $table."_".date("Y-m-d_H-i",time());
		$filename = $table."_".date("Y-m-d", time());
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: csv" . date("Y-m-d") . ".csv");
		header( "Content-disposition: filename=".$filename.".csv");
		print $csv_output;
		exit;
		
	}
	
	function import_CSV($file) {

	 $filename = $file['csv_file']['name'];
     $chk_ext = explode(".", $filename);

     if(strtolower($chk_ext[1]) == "csv") {
     
         $filename = $_FILES['csv_file']['tmp_name'];
         $handle = fopen($filename, "r");
		 
		 $ins_counter = 0;
         $idnumbers = array();
         while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		 
             if ($ins_counter > 0) {
                 if ($data[0] != '') {
                    $idnumber = $data[0];
					// Stop SQL injection by requiring numbers
					if (is_numeric($idnumber)) {
						$idnumbers[] = $idnumber;
					}
                 }
             }

            $ins_counter++;
		 }
    
         fclose($handle);
		 
		 // We should have an array of idnumbers
		 export_csv($idnumbers);
		 
     } else {
		 $message = "Invalid File";
     }
	 
	}
	
	if (isset($_POST) && isset($_FILES)) {
		import_CSV($_FILES);
	}
	if (isset($_GET['export']) && $_GET['export'] == 'all') {
		export_csv();
	}
	
?>
<!DOCTYPE html>
<html>
    <head>     
        <title>Export Course Occurrences</title>
    </head>
<body>
<style>
	body {
		font-family:Arial, Helvetica, sans-serif;
		padding:0 15px;
	}
</style>
	<h1>Course Occurrences</h1>
	<p>Choose 1. to export a CSV containing all course occurrence data or 2. to export a CSV containing only those course occurrences given in the input CSV file.</p>
	<h2>1. Export all course occurrences</h2>
	<ul><li><a href="occurrences_csv.php?export=all">Export all</a></li></ul>
	<hr />
	<h2>2. Filter by Occurrence IDs</h2>
	<p>Import a CSV of occurrence IDs to export a CSV containing only these occurrences.</p>
	<form action="occurrences_csv.php" enctype="multipart/form-data" method="POST">
		<fieldset>
		<legend>Import CSV</legend>
		<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
		Choose a CSV file to filter by: <input name="csv_file" type="file" />
		<br /><br />
		<input type="submit" value="Get CSV based on input file" />
		</fieldset>
	</form>
</body>
</html>