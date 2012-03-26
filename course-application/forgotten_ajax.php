<?php

	function showMatch($email, $ref_id) {
	
		$header_html = '<h4>Match!</h4>';
		$phonetic_html = '';
		$email_html = '';
		
		if ($email != '') {
			// Create link to email user their reference id
			$resume_link = 'http://www.conel.ac.uk/course-application/index.php?email='.$email.'&ref_id='.$ref_id;
			$resume_link = urlencode($resume_link);
			$email_html = '<p><a href="mailto:'.$email.'?subject=Resume Your Online Application&amp;body=To resume your saved application visit: '.$resume_link.' and click \'Continue Application\'.">Email '.$email.' their Reference ID</p>';
		}
		
		if ($ref_id != '') {
			// Convert ref id to phonetic equivalent
			$numbers = array(1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine');
			$alphabet = array('A' => 'Alpha', 'B' => 'Bravo', 'C' => 'Charlie', 'D' => 'Delta', 'E' => 'Echo', 'F' => 'Foxtrot', 'G' => 'Golf', 'H' => 'Hotel', 'I' => 'India', 'J' => 'Juliet', 'K' => 'Kilo', 'L' => 'Lima', 'M' => 'Mike', 'N' => 'November', 'O' => 'Oscar', 'P' => 'Papa', 'Q' => 'Quebec', 'R' => 'Romeo', 'S' => 'Sierra', 'T' => 'Tango', 'U' => 'Uniform', 'V' => 'Victor', 'W' => 'Whiskey', 'X' => 'X-Ray', 'Y' => 'Yankee', 'Z' => 'Zulu');

			$no_chars = strlen($ref_id);
			$chars = array();
			for ($i=0; $i<$no_chars; $i++) {
				$chars[] = substr($ref_id, $i, 1);
			}
			// Make phonetic - http://www.southparkstuff.com/images/stories/epiimgs/epi313/313_cartman_monkey.gif
			$phonetic_html .= '<table id="phonetic">';
			$phonetic_html .= '<tr>';
			$phonetic_html .= '<td rowspan="2" width="60"><img src="images/phone_icon.png" width="50" height="40" alt="Phone Icon" /></td>';
			foreach ($chars as $char) {
				if ($char == '-') $char = '&#8211;';
				$phonetic_html .= "<td><strong>$char</strong></td>";
			}
			$phonetic_html .= '</tr>';
			$phonetic_html .= '<tr>';
			foreach ($chars as $char) {
				$phonetic_html .= '<td>';
				if ($char == '-') {
					$phonetic_html .= 'Hyphen';
				} else if (is_numeric($char)) {
					$phonetic_html .= $numbers[$char];
				} else {
					$phonetic_html .= $alphabet[$char];
				}
				$phonetic_html .= '</td>';
			}
			$phonetic_html .= '</tr>';
			$phonetic_html .= '</table>';
		}
		
		$total_html = $header_html . $phonetic_html . $email_html;
		return $total_html;
		
	}
	
	$email = (isset($_GET['email']) && $_GET['email'] != '') ? $_GET['email'] : '';
	$ref_id = (isset($_GET['ref_id']) && $_GET['ref_id'] != '') ? $_GET['ref_id'] : '';
	
	$content = showMatch($email, $ref_id);
	echo $content;
	
?>