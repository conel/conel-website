<?php
	## =======================================================================
	## user_modules.php														
	## =======================================================================
	## 
	## use this file to define user functions which will be called via the
	## include tag
    ##
	## =======================================================================
	## =======================================================================
	## module_prepareSearch											
	## =======================================================================
	##  
	##  
	## =======================================================================
	function module_prepareSearch($id,$params) {
		// we need to get the options and labels from the db
		$db = new DB_Sql();
		$query = "SELECT ID, Description FROM tblsubject WHERE 1=1 ORDER BY Description";
		$rp = $db->query($query);
		
		$subjects = array();
		while($db->next_record()) {
			$subjects[$db->Record['ID']] = $db->Record['Description'];
		}	
		
		// prepare the output
		$output = '';
		if(!isset($_GET['interest']) || $_GET['interest'] == 0) {
			$output .= '<option label="Select a subject area" value="0" selected="selected">-- Select a subject area</option>';
		} else {
			$output .= '<option label="Select a subject area" value="0">-- Select a subject area</option>';
		}		
	
		foreach($subjects as $key => $value) {
			if(isset($_GET['interest']) && $_GET['interest'] == $key) {
				$output .= '<option label="'.$value.'" value="'.$key.'" selected="selected">'.$value.'</option>';
			} else {
				$output .= '<option label="'.$value.'" value="'.$key.'">'.$value.'</option>';
			}
		}
		
		// Build Location Search Drop-down
		$location = (isset($_GET['location']) && $_GET['location'] != '' && (strtolower($_GET['location']) == 'tottenham centre' || strtolower($_GET['location']) == 'enfield')) ? strtolower($_GET['location']) : '';
		$location_options = '<option value="">Select a location</option>';
		if ($location != '') {
			if ($location == 'tottenham centre') {
				$location_options .= '<option label="Tottenham Centre" value="tottenham centre" selected="selected">Tottenham Centre</option>';
				$location_options .= '<option label="Enfield" value="enfield">Enfield</option>';
			} else {
				$location_options .= '<option label="Tottenham Centre" value="tottenham centre">Tottenham Centre</option>';
				$location_options .= '<option label="Enfield" value="enfield" selected="selected">Enfield</option>';
			}
		} else {
			$location_options .= '<option label="Tottenham Centre" value="tottenham centre">Tottenham Centre</option>';
			$location_options .= '<option label="Enfield" value="enfield">Enfield</option>';
		}
		
		return array('SUBJECTS'=>$output, 'LOCATIONS'=>$location_options);

	}


	## =======================================================================
	## _searchDoCourseSearch										
	## =======================================================================
	##  
	##  
	## =======================================================================
	function _searchDoCourseSearch($keyword,$params,$location) {
		// prepare the query
		$keyword = substr($keyword,0,14);
		$keywords = explode(' ',$keyword);
		$keywords[] = $keyword;
			
		// do the actual search
		$db = new DB_Sql();
		
		// prepare the subject code
		$subquery = '';
		if(isset($_GET['interest']) && $_GET['interest'] != '0') {
			// we need to filter by interest
			$subquery = ' AND U.Subject_ID="'.mysql_real_escape_string($_GET['interest']).'"';
		}
		
		if(isset($location) && $location != '' && ($location == 'enfield' || $location == 'tottenham centre')) {
			if ($location == 'enfield') { 
				$location = 'Enfield';
			} else {
				$location = 'Tottenham Centre';
			}
			$subquery .= " AND OC.Location = '$location'";
		}

		// since we need to weight each field differently we need to do an query for each field
		$weighting = array('id'=>10,'Description'=>10,'Keywords'=>5,'Content'=>3,'Prerequisites'=>1,'Assessment'=>1,'Leadsto'=>1,'Howtaught'=>1);
		$results = array();			
		$grand_total = array();
		$employer_results = array();	
		$all_results = array();	
		
		if(empty($keyword)) {
		
			// prepare the actual query	
			if ((isset($_GET['interest']) && $_GET['interest'] == '0') && (isset($location))) {
				$query = "SELECT DISTINCT U.id, U.Employers_benefit FROM tblunits U LEFT JOIN tbloccurrences OC ON U.id=OC.Unit_id WHERE 1=1 ".$subquery;
			} else {	
				$query = "SELECT DISTINCT U.id, U.Employers_benefit FROM tblunits U LEFT JOIN tbloccurrences OC ON U.id=OC.Unit_id WHERE 1=1 ".$subquery;
			}
			$result = $db->query($query);
			
			while($db->next_record(MYSQL_ASSOC)) {				
				if($params['parameter'] == 1) {			
					//if(empty($db->Record['Employers_benefit'])) {
						if(isset($results[$db->Record['id']])) {
							$results[$db->Record['id']] += $current_weight;
						} else {
							$results[$db->Record['id']] = $current_weight;
						}
					//}
				} else {
					if(!empty($db->Record['Employers_benefit'])) {
						if(isset($results[$db->Record['id']])) {
							$results[$db->Record['id']] += $current_weight;
						} else {
							$results[$db->Record['id']] = $current_weight;
						}
					}					
				}
				
				if(!empty($db->Record['Employers_benefit'])) {
					if(isset($employer_results[$db->Record['id']])) {
						$employer_results[$db->Record['id']] += $current_weight;
					} else {
						$employer_results[$db->Record['id']] = $current_weight;
					}
					
					if(isset($all_results[$db->Record['id']])) {
						$all_results[$db->Record['id']] += $current_weight;
					} else {
						$all_results[$db->Record['id']] = $current_weight;
					}
							
				} else {
					if(isset($all_results[$db->Record['id']])) {
						$all_results[$db->Record['id']] += $current_weight;
					} else {
						$all_results[$db->Record['id']] = $current_weight;
					}
				}
				
				if(!isset($grand_total[$db->Record['id']])) {
					$grand_total[$db->Record['id']] = $current_weight;
				}
			}
		} else {
			foreach($keywords as $current_keyword) {
				foreach($weighting as $current_field => $current_weight) {
					// prepare the actual query						
					$query = "SELECT DISTINCT U.id, U.Employers_benefit FROM tblunits U LEFT JOIN tbloccurrences OC ON U.id = OC.Unit_id WHERE U.$current_field REGEXP '".$current_keyword."*'".$subquery;
					$result = $db->query($query);
					
					while($db->next_record(MYSQL_ASSOC)) {				
						if($params['parameter'] == 1) {			
							//if(empty($db->Record['Employers_benefit'])) {
								if(isset($results[$db->Record['id']])) {
									$results[$db->Record['id']] += $current_weight;
								} else {
									$results[$db->Record['id']] = $current_weight;
								}
							//}
						} else {
							if(!empty($db->Record['Employers_benefit'])) {
								if(isset($results[$db->Record['id']])) {
									$results[$db->Record['id']] += $current_weight;
								} else {
									$results[$db->Record['id']] = $current_weight;
								}
							}					
						}
								
						if(!empty($db->Record['Employers_benefit'])) {
							if(isset($employer_results[$db->Record['id']])) {
								$employer_results[$db->Record['id']] += $current_weight;
							} else {
								$employer_results[$db->Record['id']] = $current_weight;
							}
							
							if(isset($all_results[$db->Record['id']])) {
								$all_results[$db->Record['id']] += $current_weight;
							} else {
								$all_results[$db->Record['id']] = $current_weight;
							}							
							
						} else {
							if(isset($all_results[$db->Record['id']])) {
								$all_results[$db->Record['id']] += $current_weight;
							} else {
								$all_results[$db->Record['id']] = $current_weight;
							}
						}						
						
						if(!isset($grand_total[$db->Record['id']])) {
							$grand_total[$db->Record['id']] = $current_weight;
						}
					}
				}
			}
		}
		arsort($results);

		// store the search results

		$query = mysql_real_escape_string($keyword);
		search_log($query,1,mysql_real_escape_string($_GET['interest']),count($results));

		return array('results'=>$results,'grand_total'=>$grand_total,'all'=>$all_results,'employer'=>$employer_results);
	}



	## =======================================================================
	## module_searchDoCourseSearch										
	## =======================================================================
	##  
	##  
	## =======================================================================
	function module_searchDoCourseSearch($id,$params) {
		// nkowald - 2010-05-17: Added query option to this result
		if((isset($_GET['keyword']) && $_GET['keyword'] != 'keyword') || (isset($_GET['query']) && $_GET['query'] != 'Search term')) {
			// prepare the query
			$title = '<a id="main" href="#" class="actives"><span>Refine your Search</span></a>';
			$keyword = (isset($_GET['keyword']) && $_GET['keyword'] != '') ? mysql_real_escape_string($_GET['keyword']) : (isset($_GET['query']) && $_GET['query'] != '') ? mysql_real_escape_string($_GET['query']) : '';
			$location = '';
			$location = mysql_real_escape_string($_GET['location']);
			$results = _searchDoCourseSearch($keyword,$params,$location);

			$grand_total = $results['grand_total'];
			
			$all_results = $results['all'];
			$employer_results = $results['employer'];
			
			$results = $results['results'];
			
			// modified by nkowald 12:34:56 7/8/9 - Both GET variables need to be present in query string
			$current_page_pages = intval($_GET['p']) > 0 ? intval($_GET['p']) : 1;
			$keyword = (isset($keyword) && $keyword != '') ? $keyword : '';
			$interest = (isset($_GET['interest']) && $_GET['interest'] != '') ? $_GET['interest'] : '0';
			$location = (isset($_GET['location']) && $_GET['location'] != '') ? $_GET['location'] : '';
			$query_string = '&amp;p='.$current_page_pages.'&amp;keyword='.$keyword.'&amp;query='.$keyword.'&amp;interest='.$interest.'&amp;location='.$location;
			
			// prepare the pager
			$items_per_page = 15;
			$total_items = count($results);
			$number_of_pages = ceil($total_items/$items_per_page);
			$current_page = intval($_GET['p']) > 0 ? intval($_GET['p']) : 1;
			
			if($total_items > $items_per_page) {				
				## then we need to create the links
				$pager = '';
				
				$url = (!empty($tag)) ? getTargetURL($params['page_id']).'/'.$tag : getTargetURL($params['page_id']);
				for($i = 1; $i<= $number_of_pages; $i++) {
					if($i == $current_page) {
						$pager .= '<a href="'.$url.'?p='.$i.$query_string.'" class="active">'.$i.'</a> ';
					} else {
						$pager .= '<a href="'.$url.'?p='.$i.$query_string.'">'.$i.'</a> ';
					}
				}
				##$pager .= '</ul>';
				
				// now caluclate the seocnd pager
				$prev_next_pager = array();
				if($current_page > 1) {
					$prev_pager = '<a href="'.$url.'?p='.($current_page-1).$query_string.'"><img src="/layout/img/prev.gif" width="11" height="12" alt="previous page" /></a>';
				} else {
					$prev_pager = '<a><img src="/layout/img/blank.gif" width="11" height="12" alt="previous page" /></a>';
				}
				if($current_page < $number_of_pages) {
					$next_pager = '<a href="'.$url.'?p='.($current_page+1).$query_string.'"><img src="/layout/img/next.gif" width="11" height="12" alt="next page" /></a>';
				}	
				
				$pager_output = '<p class="pager">'.$prev_pager.' Pages'.$pager.''.$next_pager.'</p>';
			} else {
				$pager_output = '<p class="pager">&nbsp;</p>';
			}	

			
			## now we need to pass this to the output routine
			$search = _searchDisplayCourseResults($results,$raw_results,(($current_page-1) * $items_per_page), $items_per_page);
			if ($search != '') {
				$search = '<ul class="rel_courses">'.$search.'</ul>';
			} else {
				$search = '<p class="noresults">No results found.</p><div class="hr_nores"><hr/></div>';
				$pager_output = '';
			}
			// we need to output the tabs
			
			// modified by nkowald 12:34:56 7/8/9 - Both GET variables need to be present in query string
			$keyword = (isset($_GET['keyword']) && $_GET['keyword'] != '') ? $_GET['keyword'] : '';
			$interest = (isset($_GET['interest']) && $_GET['interest'] != '') ? $_GET['interest'] : '0';
			$location = (isset($_GET['location']) && $_GET['location'] != '') ? $_GET['location'] : '';
			$query_string = '?keyword='.$keyword.'&amp;interest='.$interest.'&amp;location='.$location;
			
			$tabs = '<ul class="clearfix tabs">';
			
			if($total_items > 0) {
				if(($params['parameter'] != 1)) {
					if(count($all_results) > 0) {
						// nkowald - 2009-10-26 - Updated Course URL
						$tabs .= '<li><a href="/our_courses/course_search/'.$query_string.'"><span>';
						$tabs .= count($all_results);
						$tabs .= ' results found in courses for learners</span></a></li>';
					} 
				} else {
					// nkowald - 2009-10-26 - Updated Course URL
					$tabs .= '<li><a class="active" href="/our_courses/course_search/'.$query_string.'"><span>';
					$tabs .= count($all_results);
					$tabs .= ' results found in courses for learners</span></a></li>';
				}
					
				if(($params['parameter'] != 1)) {
					// nkowald - 2009-10-26 - Updated Course URL
					$tabs .= '<li><a class="active" href="/our_courses/course_search/course_search_employers/'.$query_string.'"><span>';
					$tabs .= count($employer_results);
					$tabs .= ' results found in courses for employers</span></a></li>';
				} else {
					if(count($employer_results) > 0) {
						// nkowald - 2009-10-26 - Updated Course URL
						$tabs .= '<li><a href="/our_courses/course_search/course_search_employers/'.$query_string.'"><span>';
						$tabs .= count($employer_results);
						$tabs .= ' results found in courses for employers</span></a></li>';
					}
				}					
					
			}
			// n8kowald - 20/07/09
			
			$tabs .= '</ul>';
			if ($tabs == '<ul class="clearfix tabs"></ul>') {
				$tabs = '';
			}

		
			return array('SEARCH'=>$search,'PAGER'=>$pager_output,'QUERY'=>htmlentities($_GET['query']),'RESULTCOUNT'=>$output_items,'TABS'=>$tabs, 'KEYWORDS'=>htmlentities($_GET['keyword']),'TITLE'=>$title);
		}
	}
	## =======================================================================
	## _searchDisplayCourseResults											
	## =======================================================================
	function _searchDisplayCourseResults($result_set,$raw_results,$start_item,$items_per_page) {
		## we need to loop through all results and output them accroding to their template
		$output = '';
		$counter = 0;

		// do the actual search
		$db = new DB_Sql();
	
		foreach($result_set as $current_page => $current_weight) {
			if($counter >= $start_item && $counter < ($start_item+$items_per_page)) {

				$data = array();
				$query = "SELECT B.Description AS subject,A.Description FROM tblunits AS A  INNER JOIN tblsubject AS B ON A.Subject_ID = B.ID WHERE A.id='$current_page' LIMIT 1";
				$result = $db->query($query);
				
				if($db->next_record(MYSQL_ASSOC)) {
					// nkowald - 2009-10-26 - Updated Course URL
					$output .= '<li class="clearfix"><a href="/our_courses/course_search/course/'.$current_page.'" class="clearfix"><span class="result_course">'.htmlentities($db->Record['Description']).'</span><span class="result_info">'.htmlentities($db->Record['subject']).'</span></a></li>';

				
				}
			}
			
			$counter++;
		}

		if(!array_values($result_set)){
			$output .= '';
		}

		
		return $output;
	}	

	## =======================================================================
	## module_searchSwitchSearch										
	## =======================================================================
	##  
	##  
	## =======================================================================
	function module_searchSwitchSearch($id,$params) {
		if(isset($_GET['search']) && $_GET['search'] == 'courses') {
			// re-direct to the course search
			// nkowald - 2009-10-26 - Updated Course URL
			//header("Location: ".SITE_URL.'/courses/course_search?keyword='.urlencode($_GET['query']));			
			header("Location: ".SITE_URL.'/our_courses/course_search?keyword='.urlencode($_GET['query']));			
			header("Status: 303");
			exit;			
		}

	}

	## =======================================================================
	## module_searchDoSearch										
	## =======================================================================
	##  
	##  
	## =======================================================================
	function module_searchDoSearch($id,$params) {
		## check if we have a search term
		if(isset($_GET['query']) && $_GET['query'] != 'Search term' && $_GET['query'] != '') {

			
			## okay we can do a search- this needs to be done in two steps
			## first we search for the headlines/titles/page names
			$box_templates = array(59,78,76,73,77,93,58,84,68,75,54,71,57,92,72,55,89,61,60,63,74);
			$page_templates = array(40,86,42,62,45,50,65,66,41,70,81,79,82,38,39,43,44,49,51);
			
			## prepare the query
			$keyword = mysql_real_escape_string($_GET['query']);
			
			## cut off the string
			$keyword = trim(substr($keyword,0,14));
			
			## okay it is time to search
			$db = new DB_Sql();
			// old query (was naughtily retreiving deactivated pages).
			//$query = "SELECT A.page_id,A.identifier,B.template,text FROM ".PAGE_CONTENT." AS A INNER JOIN ".USER_PAGES." AS B ON A.page_id=B.page_id WHERE text REGEXP '".$keyword."*' AND identifier !='META_keywords'";
			// Get all "activated" and "show in menu" results
			$query = "SELECT A.page_id, A.identifier, B.template, A.text 
					  FROM (webmatrix_page_content AS A INNER JOIN webmatrix_user_pages AS B ON A.page_id=B.page_id) 
					  INNER JOIN webmatrix_structure AS S ON A.page_id=S.page_id 
					  WHERE S.structure_flag NOT IN (0,11) 
					  AND A.text REGEXP '".$keyword."*' 
					  AND A.identifier !='META_keywords'";
					  
			$result = $db->query($query);

			$pages = array();
			while($db->next_record(MYSQL_ASSOC)) {
			
				## first we need to check if this is a box or a page
				if(in_array($db->Record['template'],$box_templates)) {
					## okay it is a box- we need to find the page for this box
					$pages[] = _searchFindPage($db->Record['page_id']);
				} else {
					$pages[] = array('page_id'=>$db->Record['page_id'],'identifier'=>$db->Record['identifier'],'template'=>$db->Record['template']);
				}
			}
			
			
					
			## depending on the page type we need to weight the results here
			## the templates listed here area all content level templates
			$weights[10] = array(40=>'HEADLINE',86=>'HEADLINE',42=>'HEADLINE',69=>'HEADLINE',62=>'HEADLINE',45=>'HEADLINE',50=>'HEADLINE',65=>'HEADLINE',66=>'HEADLINE',41=>'HEADLINE',70=>'HEADLINE');
			
			## since we only have one weight- all others will be weighted with 1
			foreach($pages as $key => $current_page) {
				$current_page_type = $current_page['template'];

				if(in_array($current_page_type,$page_templates)) {			
					if($weights[10][$current_page_type] == $current_page['identifier']) {
						$current_weight += 10;
					} else {
						$current_weight +=1;
					}
					
					$pages[$key]['weight'] = $current_weight;
					$current_weight = 0;
				}
			}
			
			$results = array();
			$raw_results = array();
			## we have weighted everything- now we need to group the results together
			foreach($pages as $current_page) {
				if($current_page['weight'] > 0 ) {
					if(isset($results[$current_page['page_id']])) {
						$results[$current_page['page_id']] += $current_page['weight'];
					} else {
						$results[$current_page['page_id']] = $current_page['weight'];
					}
				
					$raw_results[$current_page['page_id']] = $current_page;
				}
			}
			
			## finally sort the weighted results
			arsort($results);
			
			// log the search - 
			search_log(mysql_real_escape_string($_GET['query']),2,'',count($results));
			
			// prepare the pager
			$items_per_page = 15;
			$total_items = count($results);
			$number_of_pages = ceil($total_items/$items_per_page);
			$current_page = intval($_GET['p']) > 0 ? intval($_GET['p']) : 1;
			
			if($total_items > $items_per_page) {				
				## then we need to create the links
				$pager = '';
				
				$url = (!empty($tag)) ? getTargetURL($params['page_id']).'/'.$tag : getTargetURL($params['page_id']);
				for($i = 1; $i<= $number_of_pages; $i++) {
					if($i == $current_page) {
						$pager .= '<a href="'.$url.'?p='.$i.'&#x26;query='.htmlentities($_GET['query']).'" class="active">'.$i.'</a> ';
					} else {
						$pager .= '<a href="'.$url.'?p='.$i.'&#x26;query='.htmlentities($_GET['query']).'">'.$i.'</a> ';
					}
				}
				##$pager .= '</ul>';
				
				// now caluclate the seocnd pager
				$prev_next_pager = array();
				if($current_page > 1) {
					$prev_pager = '<a href="'.$url.'?p='.($current_page-1).'&#x26;query='.htmlentities($_GET['query']).'"><img src="/layout/img/prevg.gif" width="11" height="12" alt="previous page" /></a>';
				} else {
					$prev_pager = '<a><img src="/layout/img/blank.gif" width="11" height="12" alt="previous page" /></a>';
				}
				if($current_page < $number_of_pages) {
					$next_pager = '<a href="'.$url.'?p='.($current_page+1).'&#x26;query='.htmlentities($_GET['query']).'"><img src="/layout/img/nextg.gif" width="11" height="12" alt="next page" /></a>';
				}	
				
				$pager_output = '<p class="pagerg">'.$prev_pager.' Pages'.$pager.''.$next_pager.'</p>';
			} else {
				$pager_output = '<p class="pagerg">&nbsp;</p>';
			}

			
			## now we need to pass this to the output routine
			$search = _searchDisplayResults($results,$raw_results,(($current_page-1) * $items_per_page), $items_per_page);
			
			$tabs = '<ul class="clearfix tabs">';
			
			// nkowald - 2009-10-09 - Need to display result count in tabular form, like in course search results page
			if($total_items > 0 ) {
				$this_page = $_SERVER['PHP_SELF'];
				$tabs .= '<li><a href="'.$this_page.'" class="active"><span>Found '.$total_items.' website matches</span></a></li>';
			}
			
			$course_results = _searchDoCourseSearch($keyword,array('parameter'=>1));
			if((count($course_results['results'])) > 0) {
				// nkowald - 2009-10-26 - Updated Course URL
				$tabs .= '<li><a href="/our_courses/course_search/?keyword='.urlencode($keyword).'&amp"><span>'.(count($course_results['results'])).' results found in courses for learners</span></a></li>';
			}	
			if(count($course_results['grand_total']) - (count($course_results['results']))  > 0) {
				// nkowald - 2009-10-26 - Updated Course URL
				$tabs .= '<li><a href="/our_courses/course_search/course_search_employers/?keyword='.urlencode($keyword).'"><span>'.(count($grand_total) - count($course_results['results'])).' results found in courses for employers</span></a></li>';
			}
			
			$tabs .= '</ul>';
			if ($tabs == '<ul class="clearfix tabs">') {
				$tabs = '';
			}
			
			// nkowald - 2009-10-09 - Need to display result count in tabular form, like in course search results page
			$result_count = '';
			if($total_items > 0 ) {
				$result_count = '<p class="searchtop">Found '.$total_items.' pages on the conel website containing your search term.</p>';
			}
			
			if ($search == '') {
				$search = '<p class="nores">Found no pages on the conel website containing your search term.</p><div class="nores"><hr/></div>';
				$pager_output = '';
			}
			
			
			
			return array('SEARCH'=>$search,'PAGER'=>$pager_output,'QUERY'=>htmlentities($_GET['query']),'RESULTCOUNT'=>$result_count,'TABS'=>$tabs);
		} else {
			$search = '<p class="nores">Please enter a search term to start your search.</p><div class="nores"><hr/></div>';
			return array('SEARCH'=>$search);
		}
	}



	## =======================================================================
	## _searchFindPage											
	## =======================================================================
	function _searchDisplayResults($result_set,$raw_results,$start_item,$items_per_page) {
		## we need to loop through all results and output them accroding to their template
		$output = '';
		$counter = 0;
		
		foreach($result_set as $current_page => $current_weight) {
			if($counter >= $start_item && $counter < ($start_item+$items_per_page)) {

				$data = array();
				text_getData($current_page,$data);
				date_getData($current_page,$data);

				
				## depending on the underlying template we need to otuput different elements
				$elements = array();
				
				$elements['HEADLINE'] = $data['HEADLINE']['text'];
				$elements['BODY'] = $data['INTRO']['text'];
				

				
				switch($raw_results[$current_page]['template']) {
					case 1:
						$elements['HEADLINE'] = $data['HEADLINE']['text'];
						$elements['BODY'] = $data['INTRO']['text'];
						break;
					case 30:
						$elements['HEADLINE'] = $data['HEADLINE']['text'];
						$elements['BODY'] = $data['INTRO']['text'];
						break;
					case 31:
						$elements['HEADLINE'] = $data['TITLE']['text'].' - Posted '.date('d.m.y',$data['DATE']['date']);
						$elements['BODY'] = '';
						break;
					
				}

				$content = $elements['BODY']." ";
				$content = substr($content,0,250);
				$elements['BODY'] = substr($content,0,strrpos($content,' '));				
							
				
				
				if(isset($data['DATE']['date'])) {
					$date = date('d.m.y',$data['DATE']['date']).'<br />';
				} else {
					$date = '';
				}
				
				
				if(isset($data['HEADLINE']['text'])) {
					$headline = $data['HEADLINE']['text'].'<br />';
				} else {
					$headline = '';
				}
				
				$output .= '<li class="clearfix"><a href="'.getTargetURL($current_page).'" class="clearfix"><span class="title">'.$elements['HEADLINE'].'</span><span class="info">'.getTargetURL($current_page).'</span></a></li>';
				
			}
			
			$counter++;
		}

		if(!empty($output)) {
			$output = '<ul class="search">'.$output.'</ul>';
		}

		if(!array_values($result_set)){
			$output .= '';
		}
		
		
		return $output;
	}	



	## =======================================================================
	## _searchFindPage											
	## =======================================================================
	function _searchFindPage($box_id) {
		## we need to find the page this box belongs to
		$db = new DB_Sql();
		$query = "SELECT C.page_id,C.template FROM ".DB_PREFIX."box_item AS A, ".DB_PREFIX."box AS B, ".USER_PAGES." AS C WHERE B.page_id=C.page_id AND A.box_id=B.box_id AND target='".$box_id."' LIMIT 1";
		$result = $db->query($query);

		if($db->next_record()) {	
			## check if the page is active
			return array('page_id'=>$db->Record['page_id'],'template'=>$db->Record['template']);			
		}
	}
	
	

	## =======================================================================        
	##  search_log        
	## =======================================================================        
	##  you need to include and call this when you wan to log searches
	##
	## =======================================================================
	function search_log($query,$type,$subject,$results) {			
		$timestamp = time();
		
		## now prepare the the actual insert query
		$db = new DB_Sql();
		$query = "INSERT INTO ".DB_PREFIX."reports_searchqueries (timestamp,term,subject,search_type,results) VALUES ('".time()."','".$query."','".$subject."',".$type.",".$results.")";

		$rp = $db->query($query,false);
	}	
?>