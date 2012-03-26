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
			$output['Description'] = htmlentities($output['Description'], ENT_QUOTES,'UTF-8');
			    
			// Course Code
			if (!empty($output['id'])) {
				$output['id'] = '<li class="clearfix"><p class="clearfix"><span class="title1">Course Code</span><span class="info">'.htmlentities($output['id'], ENT_QUOTES,'UTF-8').'</span></p></li>';
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
			
			while($db->next_record(MYSQL_ASSOC)) {
			
				$weeks_per_year = ereg_replace("[^0-9]", "", $db->Record['Weeks_per_acyear']);
				$qual_start = ereg_replace("[^0-9]", "", $db->Record['Qual_start']);
				$qual_end = ereg_replace("[^0-9]", "", $db->Record['Qual_end']);
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
			
				$occurrences = "";
				
				// When qualification starts and ends
				$occurrences .= '<span class="info">Starts: '.$db->Record['Qual_start'].'<br />Ends: '.$db->Record['Qual_end'].'</span></p>';
				
				if (is_numeric($weeks_per_year) && $weeks_per_year != '0') {
					$occurrences .= '<p class="clearfix"><span class="title1"></span><span class="info">Course length: &nbsp;'.$weeks_per_year.' weeks per year, '.$no_years.' '.$year_text.'&nbsp;&nbsp;</span></p>';
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
				}
				// If Examreg_fee is a number show it with a pound sign
				if (is_numeric($examreg_fee) && $examreg_fee != '0') {
					$occurrences .= '<p class="clearfix"><span class="title1"></span><span class="info">Exam fee: &nbsp;&pound;'.$examreg_fee.'&nbsp;&nbsp;</span></p>';
				}
				// If Materials_fee is a number show it with a pound sign
				if (is_numeric($materials_fee) && $materials_fee != '0') {			
					$occurrences .= '<p class="clearfix"><span class="title1"></span><span class="info">Materials fee: &nbsp;&pound;'.$materials_fee.'<br /> </span></p>';
				}
				// If Location column set
				if (($location != "") && (strtolower($location) == "tottenham centre" || strtolower($location) == "enfield centre")) {			
					$occurrences .= '<p class="clearfix"><span class="title1"></span><span class="info">Location: &nbsp;'.$location.'<br /> </span></p>';
				}
				// More info link
				$occurrences .= '<p class="clearfix"><span class="title1"></span>
				<span class="info"><a class="HAcss1" href="/our_courses/fees_and_financial_support">More info on fees</a></span></p>';
				
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
				$output_html .= '<p class="clearfix"><span class="title1">Option '.$optionNr.'</span>';
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
				}//while
			}//foreach
			
			return $courses;
		}
	}
	
	
	
	## =======================================================================
	## module_getSubjectCoursesCount										
	## =======================================================================
	## gets name and link of parent page
	## =======================================================================	
	function module_getSubjectCoursesCount($id,$params) {
		$data = array();
		subject_checkboxgroup_getData($params['page_id'],$data);
		if(isset($data['SUBJECT']['text'])) {
			$cnt = 0;
			$db = new DB_Sql();
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