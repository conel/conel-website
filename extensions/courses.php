<?php

	## =======================================================================
	## module_getCourse												
	## =======================================================================
	## gets course details for the course page
	## =======================================================================
	//--------------------------------------------------------------------
	function module_getCourse($id,$params) {

		// get the course id
		$baseURL = getTargetURL($params['page_id']);
		$course_id = substr($_GET['url'],strlen($baseURL));

		if(!empty($course_id) && strlen($course_id) > 2) {
			
			// we have a three level hierachy- we have:
			// units -> occurences -> timetable

			// prepare the course id
			$db = new DB_Sql();
			$course_id = mysql_real_escape_string($course_id);

			// see if we can find the course
			$query = "SELECT * FROM tblunits WHERE id='$course_id' LIMIT 1";
			$rp = $db->query($query);

			$output = array();
			if($db->next_record(MYSQL_ASSOC)) {
				// we need to prepare the data and fetch the additional data accordingly
				foreach($db->Record as $key => $val) {
					if($val != 'NULL') {
						$output[$key] = utf8_encode($val);         
					}
				}
			}
			// The following data is displayed in the '13course.tpl' template
			
			// Page Heading
			// nkowald - 2010-07-08 - Added to be used by application add buttton
			$course_title = htmlentities($output['Description'], ENT_QUOTES,'UTF-8');
			$course_code = htmlentities($output['id'], ENT_QUOTES,'UTF-8');
			$output['Description'] = htmlentities($output['Description'], ENT_QUOTES,'UTF-8');
			
			// nkowald - 2011-007-19 - Set up array of subject images
			$pbanners = array();
			$pbanners['AGRICULTUR'] = '';
			$pbanners['APMEDFRSCI'] = 'banner-apmedfrsci.jpg';
			$pbanners['ARTSMEDIA']  = 'banner-artsmedia.jpg';
			$pbanners['BUSIACCNTS'] = 'banner-busiaccnts.jpg';
			$pbanners['CAREHEALTH'] = 'banner-carehealth.jpg';
			$pbanners['COMPUTING']  = 'banner-computing.jpg';
			$pbanners['CONSTRBUI']  = 'banner-constrbui.jpg';
			$pbanners['ENGMATHICT'] = 'banner-engmathict.jpg';
			$pbanners['ESOL']		= 'banner-esol.jpg';
			$pbanners['FORGNLANG']  = 'banner-forgnlang.jpg';
			$pbanners['HAIRBEAU']   = 'banner-hairbeau.jpg';
			$pbanners['LEISTOUR']   = 'banner-leistour.jpg';
			$pbanners['SPORTFIT']   = 'banner-sportfit.jpg';
			$pbanners['SUPPLEARN']  = 'banner-supplearn.jpg';
			$pbanners['TEACHSUP']   = 'banner-teachsup.jpg';
			$pbanners['UNISERV']    = 'banner-uniserv.jpg';

			// Subject Banners
			// nkowald - 2011-07-19 - Adding subject banners to these pages
			if (!empty($output['Subject_ID'])) {
				$subject = $output['Subject_ID'];
				if (isset($pbanners[$subject])) {
					$output['Subject_ID'] = $pbanners[$subject];
				} else {
					$output['Subject_ID'] = 'banner-computing.jpg';
				}
			}
			   
			// Course Code
			if (! empty($output['id'])) {
					
				$output['id'] = '<li class="clearfix"><p class="clearfix"><span class="title1">Course Code</span><span class="info"><span class="ccode">'.htmlentities($output['id'], ENT_QUOTES,'UTF-8').'</span>';

				$hdir = MATRIX_UPLOADDIR_DOCS.'handbooks/'.$subject.'/';

				if(is_dir($hdir)) {
					
					$hbooks = array("$course_code.doc","$course_code.pdf",'default.doc','default.docx');
									
					foreach($hbooks as $hbook) {					
				
						if(is_file($hdir.$hbook)) {					
							$output['id'] .= '<span class="hbook"><a href="'.SITE_URL.'/docs/handbooks/'.$subject.'/'.$hbook.'">Download handbook</a></span>';
							break;
						}					
					}				
				}

				$output['id'] .= '</span></p></li>';
			}
			
			// What qualifications do I need?
			if (!empty($output['Prerequisites'])) {
				$output['Prerequisites'] = '<li class="clearfix"><p class="clearfix"><span class="title1">What qualifications do I need?</span><span class="info">'.htmlentities($output['Prerequisites'], ENT_QUOTES,'UTF-8').'</span></p></li>';
			}
			// What will I learn?
			if (!empty($output['Content'])) {
				$output['Content'] = '<li class="clearfix"><p class="clearfix"><span class="title1">What will I learn?</span><span class="info">'.htmlentities($output['Content'], ENT_QUOTES,'UTF-8').'</span></p></li>';
			}
			// How will I be taught?
			if (!empty($output['Howtaught'])) {
				$output['Howtaught'] = '<li class="clearfix"><p class="clearfix"><span class="title1">How will I be taught?</span><span class="info">'.htmlentities($output['Howtaught'], ENT_QUOTES,'UTF-8').'</span></p></li>';
			}
			// How will I be assessed?
			if (!empty($output['Assessment'])) {
				$output['Assessment'] = '<li class="clearfix"><p class="clearfix"><span class="title1">How will I be assessed?</span><span class="info">'.htmlentities($output['Assessment'], ENT_QUOTES,'UTF-8').'</span></p></li>';
			}
			// What could I do next?
			if (!empty($output['Leadsto'])) {
				$output['Leadsto'] = '<li class="clearfix"><p class="clearfix"><span class="title1">What could I do next?</span><span class="info">'.htmlentities($output['Leadsto'], ENT_QUOTES,'UTF-8').'</span></p></li>';
			}			
			// How would this benefit my employer?
			if (!empty($output['Employers_benefit'])) {
				$output['Employers_benefit'] = '<li class="clearfix"><p class="clearfix"><span class="title1">How would this benefit my employer?</span><span class="info">'.htmlentities($output['Employers_benefit'], ENT_QUOTES,'UTF-8').'</span></p></li>';
			}
			
			// now we fetch the other data required
			$query = "SELECT * FROM tbloccurrences WHERE Unit_id LIKE '$course_id'";
			$rp = $db->query($query);

			//$occurrence_count = $db->num_rows(); // don't think this is used
			$occur_array = array();
			$i = 0;
			
			// nkowald - 2010-05-24 - Generate length from start/end dates
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
			
			// Set up array to hold "course days" for each month
			// $days_per_month[0] = January (20 days) and so on.
			// nkowald - 2011-11-11 - These days are worked out from the college calendar, how many days courses run per month
			$days_per_month = array(20,15,23,11,19,18,2,0,18,17,21,14);
			
			// nkowald - 2011-11-11 - There's an easier way
			/*
			foreach($days_per_month as $mnth) {
				$days_full_year += $mnth;
			}
			*/
			$days_full_year = 0;
			$days_full_year = array_sum($days_per_month);
			
			while($db->next_record(MYSQL_ASSOC)) {
			
				// This weeks_per_year (from DB) value no longer used - auto calculated by start and end dates so all occurrences have valid data
				$weeks_per_year = ereg_replace("[^0-9]", "", $db->Record['Weeks_per_acyear']);
				$qual_start = ereg_replace("[^0-9]", "", $db->Record['Qual_start']);
				// nkowald - 2012-02-17 - strip first two characters from string
				$qual_start = substr($qual_start, 2);

				$qual_start_month = eregi_replace("[^A-Z]", "", $db->Record['Qual_start']);
				$qual_end = ereg_replace("[^0-9]", "", $db->Record['Qual_end']);
				// nkowald - 2012-02-17 - strip first two characters from string
				$qual_end = substr($qual_end, 2);

				$qual_end_month = eregi_replace("[^A-Z]", "", $db->Record['Qual_end']);
				$no_years = $qual_end - $qual_start;
				$no_years = ($no_years == '0' || $no_years == '1') ? '1' : $no_years;
				$year_text = ($no_years > 1) ? 'years' : 'year';
				$tuition_fee = ereg_replace("[^0-9]", "", $db->Record['Tuition_fee']);
				$examreg_fee = ereg_replace("[^0-9]", "", $db->Record['Examreg_fee']);
				$materials_fee = ereg_replace("[^0-9]", "", $db->Record['Materials_fee']);
				$day_text = ($db->Record['Days_per_week'] == "1") ? "day" : "days";
				$location = ($db->Record['Location'] != '') ? $db->Record['Location'] : '';
				$days_per_week = $db->Record['Days_per_week'];
				$hours_per_week = $db->Record['Hours_per_week'];
				
				// Total fee calculation
				$material_actual = ($materials_fee != '') ? $materials_fee : 0;
				$tuition_actual = ($tuition_fee != '') ? $tuition_fee : 0;
				$examreg_actual = ($examreg_fee != '') ? $examreg_fee : 0;
				$total_fees = $material_actual + $tuition_actual + $examreg_actual;
			
				$occurrences = "";
				
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
					
					$weeks_per_year = round($days_course / 5,0); // 5 working days in a "week"
				}
				
				if (!is_numeric($weeks_per_year) || $weeks_per_year == '') {
					$calculated_weeks = $weeks_course;
				}
				
				// We need to add bracket text about courses that accept late enrolments
				$bracket_text = FALSE;

				// If qualification is in the past, show bracket text
				if (($qual_start < $this_year) || (in_array($this_month,$past_months) && $qual_start <= $this_year)) {
					$bracket_text = TRUE;
				}
				
				// When qualification starts and ends
				if ($bracket_text) {
					$occurrences .= '<span class="info">Starts: '.$db->Record['Qual_start'].' <em class="lep">(Late Enrolments Possible)</em><br />Ends: '.$db->Record['Qual_end'].'</span></p>';
				} else {
					$occurrences .= '<span class="info">Starts: '.$db->Record['Qual_start'].'<br />Ends: '.$db->Record['Qual_end'].'</span></p>';
				}
				
				if (is_numeric($weeks_per_year) && $weeks_per_year != 0) {
					$occurrences .= '<p class="clearfix"><span class="title1"></span><span class="info">Course length: &nbsp;'.$weeks_per_year.' weeks, '.$no_years.' '.$year_text.'&nbsp;&nbsp;</span></p>';
				} else {
					$occurrences .= '<p class="clearfix"><span class="title1"></span><span class="info">Course length: &nbsp;'.$no_years.' '.$year_text.'&nbsp;&nbsp;</span></p>';
				}
				// Change to check for number, else display 'To be confirmed'
				if (is_numeric($days_per_week) && is_numeric($hours_per_week)) {
					$occurrences .= '<p class="clearfix"><span class="title1"></span><span class="info">Study hours: '.$hours_per_week.'&nbsp;over&nbsp;'.$days_per_week.' '.$day_text.' per week'.'</span></p>';
				}
				// If Tuition_fee is a number show it with a pound sign
				if (is_numeric($tuition_fee) && $tuition_fee != '0') {
					$occurrences .= '<p class="clearfix"><span class="title1"></span><span class="info">Tuition fee: &nbsp;&pound;'.$tuition_fee.'&nbsp;&nbsp;</span></p>';
				} else {
					$occurrences .= '<p class="clearfix"><span class="title1"></span><span class="info">Tuition fee: &nbsp;&pound; TBC &nbsp;&nbsp;</span></p>';
				}
				
				// If Examreg_fee is a number show it with a pound sign
				if (is_numeric($examreg_fee) && $examreg_fee != '0') {
					$occurrences .= '<p class="clearfix"><span class="title1"></span><span class="info">Exam fee: &nbsp;&pound;'.$examreg_fee.'&nbsp;&nbsp;</span></p>';
				}
				// If Materials_fee is a number show it with a pound sign
				if (is_numeric($materials_fee) && $materials_fee != '0') {			
					$occurrences .= '<p class="clearfix"><span class="title1"></span><span class="info">Materials fee: &nbsp;&pound;'.$materials_fee.'<br /> </span></p>';
				}
				// nkowald - 2010-05-25 - Show total fee
				if (is_numeric($total_fees) && $total_fees != '0') {			
					$occurrences .= '<p class="clearfix"><span class="title1"></span><span class="info"><strong style="font-weight:bold;">Total fee:</strong> &nbsp;&pound;'.$total_fees.'<br /> </span></p>';
				}
				
				
				// If Location column set
				if (($location != "") && (strtolower($location) == "tottenham centre" || strtolower($location) == "enfield centre")) {			
					$occurrences .= '<p class="clearfix"><span class="title1"></span><span class="info">Location: &nbsp;'.$location.'<br /> </span></p>';
				}
				// More info link
				$occurrences .= '<p class="clearfix"><span class="title1"></span>
				<span class="info"><a class="HAcss1" href="/for_learners/financial_support/fees_and_financial_support">More info on fees</a></span></p>';
				
				// nkowald - 2010-07-08 - If Course application session is open, and courses free then show the add to application button
				// nkowald - 2010-02-15 - Karen wants this to show always
				if (isset($_SESSION['caf']['signed_in'])) {
					$submit_value = '&nbsp;Add to Application&nbsp;';
				} else {
					$submit_value = '&nbsp;Apply Now&nbsp;';
				}
				
				// free course slots?
				if ( 
					(!isset($_SESSION['caf']['course_code_1']) || $_SESSION['caf']['course_code_1'] == '') 
					|| 
					(!isset($_SESSION['caf']['course_code_2']) || $_SESSION['caf']['course_code_2'] == '') 
				) {
					$occurrences .= '<form action="'.SITE_ROOT.'course-application/caf_add_to_application.php" method="post">';
					$occurrences .= '<input type="hidden" name="course_title" value="'.$course_title.'" />';
					$occurrences .= '<input type="hidden" name="course_code" value="'.$course_code.'" />';
					// strip 'center from location'
					$form_location = $location;
					$occurrences .= '<input type="hidden" name="location" value="'.$form_location.'" />';
					// nkowald - 2011-02-23 - Added entry date
					if ($qual_start == $qual_end) {
						$start_html = $qual_start_month;
					} else {
						$start_html = $qual_start_month . ' ' . $qual_start;
					}
					$end_html = $qual_end_month . ' ' . $qual_end;
					$choice_html = $start_html . " - " . $end_html;
					$occurrences .= '<input type="hidden" name="course_entry_date" value="'.$choice_html.'" />';
			
					$occurrences .= '<p class="clearfix"><span class="title1"></span><span class="info"><input type="submit" value="'.$submit_value.'" class="submit browse" /></span></p>';
					$occurrences .= '</form>';
				}

				
				$year_i = $qual_end . $i;
				$occur_array[$year_i] = $occurrences;
				$i++;
				
			}
			
			// Sort these occurrences by most recent date first
			krsort($occur_array, SORT_NUMERIC);
			
			$output_html = '';
			$optionNr = 1;
			
			foreach($occur_array as $occurrence) {
				$output_html .= "<ul class=\"fact top_b\"><li class=\"clearfix noborder\">";
				$output_html .= '<h3 class="hidden">Option ' . $optionNr . '</h3>';
				$output_html .= '<p class="clearfix"><span class="title1 option">Option '.$optionNr.'</span>';
				$output_html .= $occurrence;
				$output_html .= "</li></ul><div class=\"hrc4\">&nbsp;</div>";
				
				// increase option number by one
				$optionNr++;
			}
			
			$output['OCCURENCES'] = $output_html;
			
		}
		

		return $output;
		
	} // module_getCourse
	
	
//--------------------------------------------------------------------

	## =======================================================================
	## module_getHomepageImage												
	## =======================================================================
	## gets name and link of parent page
	## =======================================================================
	function module_getEventsCourses($id,$params) {
		// fetch the  subjects for this page
		// get the course id
		$baseURL = getTargetURL($params['page_id']);
		$course_id = substr($_GET['url'],strlen($baseURL));

		//echo "I am in module_getEventsCourses"."<br>";

		if(!empty($course_id) && strlen($course_id) > 2) {
			// we have a three level hierachy- we have: 
			// units -> occurences -> timetables
			// prepare the db
			$db = new DB_Sql();	
			$course_id = mysql_real_escape_string($course_id);

			// see if we can find the course
			$query = "SELECT Subject_ID,Employers_benefit FROM tblunits WHERE id='$course_id' LIMIT 1";
			$rp = $db->query($query);
			
			$output = array();
			if($db->next_record(MYSQL_ASSOC)) {
				// we need to prepare the data and fetch the additional data accordingly
				$subquery = "'".$db->Record['Subject_ID']."'";
				
				if(!empty($db->Record['Employers_benefit'])) {
					$targets = '(2,4)';
				} else {
					$targets = '(1,4)';
				}
				
				// prepare the query
				// old query
				//$query = "SELECT DISTINCT(A.page_id),C.date FROM ".DB_PREFIX."extra_checkboxgroup AS A INNER JOIN ".DB_PREFIX."user_pages AS B ON A.page_id=B.page_id INNER JOIN ".DB_PREFIX."page_date AS C ON B.page_id=C.page_id INNER JOIN ".DB_PREFIX."extra_checkboxgroup AS D ON A.page_id=D.page_id WHERE D.identifier='TARGET' AND D.text IN $targets AND C.identifier='DATE' AND A.identifier='SUBJECT' AND  A.text IN($subquery) AND B.template=66 ORDER BY C.date DESC LIMIT 5";
				
				/*			
				// Modified: 9/6/2009 - nkowald: Get events courses from current year onwards
				$current_year = date('Y'); // Current date as year - eg. 2009
				$query = "SELECT DISTINCT(A.page_id),C.date FROM ".DB_PREFIX."extra_checkboxgroup AS A INNER JOIN ".DB_PREFIX."user_pages AS B ON A.page_id=B.page_id INNER JOIN ".DB_PREFIX."page_date AS C ON B.page_id=C.page_id INNER JOIN ".DB_PREFIX."extra_checkboxgroup AS D ON A.page_id=D.page_id WHERE D.identifier='TARGET' AND D.text IN $targets AND C.identifier='DATE' AND A.identifier='SUBJECT' AND A.text IN($subquery) AND B.template=66 AND FROM_UNIXTIME(C.date,'%Y') >= $current_year ORDER BY C.date DESC LIMIT 5";
				*/

				// Modified: 9/6/2009 - nkowald: Get events courses from a year ago onwards
				$stamp = mktime(now-8766); // 8765 hours in a year (approx.)
				$date_events_from = date('Y-m-d H:i:s',$stamp); // Date one year ago
				$query = "SELECT DISTINCT(A.page_id),C.date FROM ".DB_PREFIX."extra_checkboxgroup AS A INNER JOIN ".DB_PREFIX."user_pages AS B ON A.page_id=B.page_id INNER JOIN ".DB_PREFIX."page_date AS C ON B.page_id=C.page_id INNER JOIN ".DB_PREFIX."extra_checkboxgroup AS D ON A.page_id=D.page_id WHERE D.identifier='TARGET' AND D.text IN $targets AND C.identifier='DATE' AND A.identifier='SUBJECT' AND  A.text IN($subquery) AND B.template=66 AND FROM_UNIXTIME(C.date) >= DATE_FORMAT('$date_events_from','%Y-%m-%d %H:%i:%s') ORDER BY C.date DESC LIMIT 5";
								
				$rp = $db->query($query);
	
				$output = '';
				while($db->next_record(MYSQL_ASSOC)) {			
					$current_page = $db->Record['page_id'];
					$data = array();
					
					text_getData($current_page,$data);
					date_getData($current_page,$data);
					
					$output .= '<li><a href="'.getTargetURL($current_page).'">'.htmlentities(stripslashes($data['HEADLINE']['text'])).'<span>'.htmlentities(stripslashes($data['DATEV']['text'])).', '.htmlentities(stripslashes($data['LOCATION']['text'])).'</span></a></li>';
				}
				
				$return_value = '';
				if(!empty($output)) {
					$return_value = '<div class="box"><h3>Events</h3><ul class="news">'.$output.'</ul><div class="box_bottom1"><hr/></div></div>';
				}
				
				return $return_value;

			}
		}
	}

	## =======================================================================
	## module_getHomepageImage												
	## =======================================================================
	## gets name and link of parent page
	## =======================================================================
	function module_getNewsCourses($id,$params) {
		// fetch the  subjects for this page
		// get the course id
		$baseURL = getTargetURL($params['page_id']);
		$course_id = substr($_GET['url'],strlen($baseURL));


		if(!empty($course_id) && strlen($course_id) > 2) {
			// we have a three level hierachy- we have: 
			// units -> occurences -> timetables
			// prepare the db
			$db = new DB_Sql();	
			$course_id = mysql_real_escape_string($course_id);

			// see if we can find the course
			$query = "SELECT Subject_ID,Employers_benefit FROM tblunits WHERE id='$course_id' LIMIT 1";
			$rp = $db->query($query);
			
			$output = array();
			if($db->next_record(MYSQL_ASSOC)) {
				// we need to prepare the data and fetch the additional data accordingly
				$subquery = "'".$db->Record['Subject_ID']."'";
				
				if(!empty($db->Record['Employers_benefit'])) {
					$targets = '(2,4)';
				} else {
					$targets = '(1,4)';
				}
				
				// prepare the query
				// old query
				//$query = "SELECT DISTINCT(A.page_id),C.date FROM ".DB_PREFIX."extra_checkboxgroup AS A INNER JOIN ".DB_PREFIX."user_pages AS B ON A.page_id=B.page_id INNER JOIN ".DB_PREFIX."page_date AS C ON B.page_id=C.page_id INNER JOIN ".DB_PREFIX."extra_checkboxgroup AS D ON A.page_id=D.page_id WHERE D.identifier='TARGET' AND D.text IN $targets AND C.identifier='DATE' AND A.identifier='SUBJECT' AND  A.text IN($subquery) AND B.template=65 ORDER BY C.date DESC LIMIT 5";
				
				/*			
				// Modified: 9/6/2009 - nkowald: Get news courses from current year onwards
				$current_year = date('Y'); // Current date as year - eg. 2009
				$query = "SELECT DISTINCT(A.page_id),C.date FROM ".DB_PREFIX."extra_checkboxgroup AS A INNER JOIN ".DB_PREFIX."user_pages AS B ON A.page_id=B.page_id INNER JOIN ".DB_PREFIX."page_date AS C ON B.page_id=C.page_id INNER JOIN ".DB_PREFIX."extra_checkboxgroup AS D ON A.page_id=D.page_id WHERE D.identifier='TARGET' AND D.text IN $targets AND C.identifier='DATE' AND A.identifier='SUBJECT' AND  A.text IN($subquery) AND B.template=65 AND FROM_UNIXTIME(C.date,'%Y') >= $current_year ORDER BY C.date DESC LIMIT 5";
				*/

				// Modified: 9/6/2009 - nkowald: Get news courses from a year ago onwards
				$stamp = mktime(now-8766); // 8765 hours in a year (approx.)
				$date_events_from = date('Y-m-d H:i:s',$stamp); // Date one year ago
				$query = "SELECT DISTINCT(A.page_id),C.date FROM ".DB_PREFIX."extra_checkboxgroup AS A INNER JOIN ".DB_PREFIX."user_pages AS B ON A.page_id=B.page_id INNER JOIN ".DB_PREFIX."page_date AS C ON B.page_id=C.page_id INNER JOIN ".DB_PREFIX."extra_checkboxgroup AS D ON A.page_id=D.page_id WHERE D.identifier='TARGET' AND D.text IN $targets AND C.identifier='DATE' AND A.identifier='SUBJECT' AND  A.text IN($subquery) AND B.template=65 AND FROM_UNIXTIME(C.date) >= DATE_FORMAT('$date_events_from','%Y-%m-%d %H:%i:%s') ORDER BY C.date DESC LIMIT 5";		
				
				$rp = $db->query($query);
	
				$output = '';
				while($db->next_record(MYSQL_ASSOC)) {		
					$current_page = $db->Record['page_id'];
					$data = array();
					
					text_getData($current_page,$data);
					date_getData($current_page,$data);
					//echo "Here is module_getNewsCourses: ".$current_page."---".$data['DATE']['date']."=".$data['HEADLINE']['text']."<br>";
					$output .= '<li><a href="'.getTargetURL($current_page).'">'.htmlentities(stripslashes($data['HEADLINE']['text'])).'<span>'.date('j F Y',$data['DATE']['date']).'</span></a></li>';
				}
				
				$return_value = '';
				if(!empty($output)) {
					$return_value = '<div class="box"><h3>Latest News</h3><ul class="news">'.$output.'</ul><div class="box_bottom1"><hr/></div></div>';
				}
				
				return $return_value;

			}
		}
	}	


	## =======================================================================
	## module_getHomepageImage												
	## =======================================================================
	## gets name and link of parent page
	## =======================================================================
	function module_getPanelsCourse($id,$params) {
		// fetch the  subjects for this page
		// get the course id
		$baseURL = getTargetURL($params['page_id']);
		$course_id = substr($_GET['url'],strlen($baseURL));

		if(!empty($course_id) && strlen($course_id) > 2) {
			// we have a three level hierachy- we have: 
			// units -> occurences -> timetables
			// prepare the db
			$db = new DB_Sql();	
			$course_id = mysql_real_escape_string($course_id);

			// see if we can find the course
			$query = "SELECT Subject_ID,Employers_benefit FROM tblunits WHERE id='$course_id' LIMIT 1";
			$rp = $db->query($query);
			
			$output = array();
			if($db->next_record(MYSQL_ASSOC)) {
				// we need to prepare the data and fetch the additional data accordingly
				$subquery = "'".$db->Record['Subject_ID']."'";
				
				if(!empty($db->Record['Employers_benefit'])) {
					$targets = '(2,4)';
				} else {
					$targets = '(1,4)';
				}
				
				// prepare the query
				$query = "SELECT A.page_id FROM ".DB_PREFIX."extra_checkboxgroup AS A INNER JOIN ".DB_PREFIX."user_pages AS B ON A.page_id=B.page_id INNER JOIN ".DB_PREFIX."extra_checkboxgroup AS C ON A.page_id=C.page_id WHERE C.identifier='TARGET' AND C.text IN $targets AND A.identifier='SUBJECT' AND A.text IN($subquery) AND B.template=62  ORDER BY RAND() LIMIT 1";
				$rp = $db->query($query);
	
				if($db->next_record(MYSQL_ASSOC)) {
					$page_id = $db->Record['page_id'];
					return _page_generatePage($page_id,'l_testimonial');
				}
			}
		}

	}
	
	

	## =======================================================================
	## module_getSubjectCourses										
	## =======================================================================
	## gets name and link of parent page
	## =======================================================================
	function module_getSubjectCourses($id,$params) {
		$courses = _getSubjectCourses($params['page_id']);
		
		if(is_array($courses)) {			
			// now prepare the output
			$output = '';
			foreach($courses as $current_course) {
				// nkowald - 2009-10-26 - Changed the courses URL
				$output .= '<li class="clearfix"><a href="/our_courses/course_search/course/'.$current_course['id'].'" class="clearfix"><span class="title">'.$current_course['id'].'</span><span class="info">'.htmlentities($current_course['Description'], ENT_QUOTES,'UTF-8').'</span></a></li>';
			}
			
			if(!empty($output)) {
				$output = '<ul class="rel_courses topbg">'.$output.'</ul><div class="hrc1"><hr /></div>';
			}
			return $output;
		}
	}
	
	## =======================================================================
	## _getSubjectCourses										
	## =======================================================================
	## gets name and link of parent page
	## =======================================================================
	function _getSubjectCourses($page_id) {
		// fetch the  subjects for this page
		$data = array();
		subject_checkboxgroup_getData($page_id,$data);
		
		if(isset($data['SUBJECT']['text'])) {
			// prepare the db connection
			$db = new DB_Sql();
		
			$subjects = $data['SUBJECT']['text'];
			
			// for each subject we need to get the courses
			$courses = array();
			foreach($subjects as $current_subject) {
				$query = "SELECT id,Description FROM tblunits WHERE Subject_ID='$current_subject' ORDER BY Description";
				$rp = $db->query($query);
				while($db->next_record(MYSQL_ASSOC)) {
					$courses[] = $db->Record;        
				} //while
			} //foreach
			
			return $courses;
		}
	}
	
	// nkowald - 2012-03-19 - Get Apprenticeships Courses
	function module_getApprenticeshipCourses($id, $params) {
	
		// Only run if current page is 'apprenticeships'
		if ($params['page_id'] == 4424) {
		
			$db = new DB_Sql();
            $query = "SELECT id, Description FROM tblunits WHERE (Description LIKE ('%Pre Apprenticeship%') AND id NOT LIKE ('AP%')) OR id LIKE ('AP%') ORDER BY Subject_ID, id, Description";
			$rp = $db->query($query);
			while($db->next_record(MYSQL_ASSOC)) {
				$courses[] = $db->Record;        
			}
			if(is_array($courses)) {			
				// now prepare the output
				$output = '';
				foreach($courses as $current_course) {
					$output .= '<li class="clearfix"><a href="/our_courses/course_search/course/'.$current_course['id'].'" class="clearfix"><span class="title">'.$current_course['id'].'</span><span class="info">'.htmlentities($current_course['Description'], ENT_QUOTES,'UTF-8').'</span></a></li>';
				}
				
				if(!empty($output)) {
					$output = '<ul class="rel_courses topbg">'.$output.'</ul><div class="hrc1"><hr /></div>';
				}
				return $output;
			}
		} else {
			return '';
		}
	}	
	// sszabo - 2012-09-25 - Get Housing Courses
	function module_getHousingCourses($id, $params) {
		// Only run if current page is 'housing'
		if ($params['page_id'] == 4378) {
		
			$db = new DB_Sql();
            $query = "SELECT id, Description FROM tblunits WHERE Description LIKE '%Housing%' AND id NOT LIKE 'AP%' ORDER BY Subject_ID, id, Description";
			$rp = $db->query($query);
			while($db->next_record(MYSQL_ASSOC)) {
				$courses[] = $db->Record;        
			}
			if(is_array($courses)) {			
				// now prepare the output
				$output = '';
				foreach($courses as $current_course) {
					$output .= '<li class="clearfix"><a href="/our_courses/course_search/course/'.$current_course['id'].'" class="clearfix"><span class="title">'.$current_course['id'].'</span><span class="info">'.htmlentities($current_course['Description'], ENT_QUOTES,'UTF-8').'</span></a></li>';
				}
				
				if(!empty($output)) {
					$output = '<ul class="rel_courses topbg">'.$output.'</ul><div class="hrc1"><hr /></div>';
				}
				return $output;
			}
		} else {
			return '';
		}
	}

	## =======================================================================
	## module_getSubjectCoursesCount										
	## =======================================================================
	## gets name and link of parent page
	## =======================================================================	
	function module_getSubjectCoursesCount($id,$params) {

		$db = new DB_Sql();
		$cnt = 0;
					
		switch($params['page_id']) {
			case 4424:	
				$query = "SELECT COUNT(id) as cnt FROM tblunits WHERE (Description LIKE ('%Pre Apprenticeship%') AND id NOT LIKE ('AP%')) OR id LIKE ('AP%')";
				$db->query($query);	
				$db->next_record();
				$cnt = $db->Record["cnt"];				
			break;
			case 4378:
				$query = "SELECT COUNT(id) as cnt FROM tblunits WHERE Description LIKE '%Housing%' AND id NOT LIKE 'AP%'";
				$db->query($query);	
				$db->next_record();
				$cnt = $db->Record["cnt"];							
			break;
			default:
				$data = array();
				subject_checkboxgroup_getData($params['page_id'],$data);
				
				if(isset($data['SUBJECT']['text'])) {

					$subjects = $data['SUBJECT']['text']; 
					
					// nkowald - modified 22/06/2009 - need to removed multiple subject values in array
					$subjects = array_unique($subjects);
					
					foreach($subjects as $current_subject) {
						$query = "SELECT id FROM tblunits WHERE Subject_ID='$current_subject'";
						$rp = $db->query($query);
						while($db->next_record(MYSQL_ASSOC)) {
							$cnt++; // how many courses
						}//while
			   
					} //foreach
				} //if
			break;
		}
		
		if ($cnt == 0) {
			$output = '';
		} else if ($cnt == 1) {
			$output = '1 course';
		} else {
			$output = $cnt.' courses';
		}
		
		return $output;	
	}
?>
