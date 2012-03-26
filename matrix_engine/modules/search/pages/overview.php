<?php

	## =======================================================================        
	## search_overview   
	## =======================================================================        
	## handles the main functions of this page
	##
	## =======================================================================
	function search_overview($params) {
		## okay we have the params - we need to check what to do next
		$action = $params[1];

		switch($action) {
			case 'view':
				search_overviewView();
				break;
			case 'component':
				## okay we will pass the control to the pages main function
				eval("\$element = search_overview".ucfirst($params[1]).ucfirst($params[2])."();");	
				echo $element;
				break;
			default:
				## if we don't receive anything we will default to view
				search_overviewView();
				break;
		}
	}
	

	## =======================================================================        
	## search_overviewView    
	## =======================================================================        
	## this is the main fucntion for this page- it handles the complete 
	## display of all elements- if no ajax is used we will need to handle 
	## all functionality in here
	##
	## =======================================================================
	function search_overviewView() {
		global $gSession;
		## basically we need to populate the scafoold for this page
		## with the right components
		
		## get the base template
		$template_filename = 'interface.tpl';
		$template = new Template("interface/");
		$template->set_templatefile(array("body" => $template_filename));
		
		## first get the calendar coomponent
		$calendar = search_overviewComponentCalendar();
		$template->set_var('DATESELECTOR',$calendar);
		
		## now we handle the main component
		$entries = search_overviewComponentData();
		$template->set_var('DATACOMPONENT',$entries);
		
		## finally pass the session id to the whole template
		$template->set_var('SESSION',$gSession->id);
		
		$template->pfill_block("body");
	}
	

	## =======================================================================        
	## search_overviewView    
	## =======================================================================        
	## this is the main fucntion for this page- it handles the complete 
	## display of all elements- if no ajax is used we will need to handle 
	## all functionality in here
	##
	## =======================================================================
	function search_overviewComponentCalendar() {
		global $gSession;
		## basically we need to populate the scafoold for this page
		## with the right components
		
		## get the base template
		$template_filename = 'interface.tpl';
		$template = new Template("interface/");
		$template->set_templatefile(array("calendar" => $template_filename));
		
		$baseURL = "/matrix_engine/modules/search/overview/component/dateselector";
		$baseURL = $gSession->url($baseURL);		

		$template->set_var('actionURL',$baseURL);
		
		## okay for a given date we need to find out the month the start date is in
		$dates = _search_GetDates();
		
		$current_month = date('n',$dates['START']);
		$current_year = date('Y',$dates['START']);
		
		## okay we need to find out how many days this month has
		$month_days = date('t',$dates['START']);

		## okay let's get the first day of this month
		## 0 equals sunday, 6 equals saturday
		$current_month = 12;
		$current_year = 2006;
		$month_days = 31;
		$firstday = date('w', mktime(0,0,0,$current_month,1,$current_year));
		$firstweek = date('W',$dates['START']);
		
		## using the first day of the month we need to calculate the offset to start with
		$offset = 2 - $firstday;

		$output = '';
		for($j = $offset; $j <= $month_days;) {
			## open a new row for this week
			$output .= '<tr id="w'.$firstweek.'"><td class="week" align="right"><a href="#" onclick="calendarSelectWeek(\'#w'.$firstweek.'\',\''.$j.'\',\''.$current_month.'\',\''.$current_year.'\');">'.sprintf("%02d", $firstweek).'</a></td>';
			for($i=1; $i<= 7 && $j <= $month_days; $i++, $j++) {
				## handle each week
				if($j < 1) {
					$output .= '<td class="day">&nbsp;</td>';
				} else {
					$output .= '<td class="day"><a href="#" onclick="calendarSelectDay(this,\''.$j.'\',\''.$current_month.'\',\''.$current_year.'\');">'.$j.'</a></td>';
				}
			}
			## close the week row
			$output .= '</tr>';
			$firstweek++;
		}
		
		$template->set_var('CALENDAR',$output);

		return $template->fill_block("calendar");	
	}	
	
	
	## =======================================================================        
	## search_overviewView    
	## =======================================================================        
	## this is the main fucntion for this page- it handles the complete 
	## display of all elements- if no ajax is used we will need to handle 
	## all functionality in here
	##
	## =======================================================================
	function search_overviewComponentDateselector() {
		global $gSession;
		## basically we need to populate the scafoold for this page
		## with the right components
		## Handle HTTP GET parameters
		## get the dates
		$dates = _search_GetDates();
		$start = $dates['START'];
		$end = $dates['END']; 
		
		## get the base template
		$template_filename = 'interface.tpl';
		$template = new Template("interface/");
		$template->set_templatefile(array("daterange" => $template_filename));
		
		$baseURL = "module.php";
		$baseURL = $gSession->url($baseURL);		
		
		## startdate
		$template->set_var('sday',date('d',$start));
		$template->set_var('smonth',date('n',$start));
		$template->set_var('syear',date('Y',$start));

		## enddate
		$template->set_var('eday',date('d',$end));
		$template->set_var('emonth',date('n',$end));
		$template->set_var('eyear',date('Y',$end));
		
		## prpeare the switch url
		$baseURL = "/matrix_engine/modules/search/overview/component/calendar";
		$baseURL = $gSession->url($baseURL);	
		$template->set_var('actionURL',$baseURL);
		
		## when the user clicks on view data- we need to update ourselves
		## and the data component- the links are handled here
		
		
		return $template->fill_block("daterange");	
	}	
	
	## =======================================================================        
	## search_overviewView    
	## =======================================================================        
	## this is the main fucntion for this page- it handles the complete 
	## display of all elements- if no ajax is used we will need to handle 
	## all functionality in here
	##
	## =======================================================================
	function search_overviewComponentData() {
		global $gSession;

		## get the dates
		$dates = _search_GetDates();
		$start = $dates['START'];
		$end = $dates['END'];

		## basically we need to populate the scafoold for this page
		## with the right components
		
		## get the base template
		$template_filename = 'interface.tpl';
		$template = new Template("interface/");
		$template->set_templatefile(array("datacomponent" => $template_filename,"item" => $template_filename));
		
		## now handle the actual data	
		$currentPage = intval($_GET['p']) > 0 ? intval($_GET['p']) : 1;
		$data = _fetchTerms($start,$end,($currentPage-1)*20);
		
		$total_items = $data['TOTAL_ITEMS'];
		$data = $data['DATA'];
		
		## get the pager data stuff
		$dates = _search_PrepareDates($start,$end);
		$pager = _generatePager('&'.$dates,$total_items,20);		
		
		$items = '';
		foreach($data as $current_term) {
			## okay we are looping through all entries in order to output them
			$template->set_var('TERM',$current_term['term']);
			$template->set_var('SEARCH_COUNT',$current_term['numberofsearches']);
			$template->set_var('RESULT_COUNT',intval($current_term['results']));
			
			$items .= $template->fill_block("item");
		}
	
		$template->set_var('ENTRIES',$items);
		$template->set_var('PAGER',$pager['pager_output']);
		

	
		return $template->fill_block("datacomponent");

	}	
	
	
	## =======================================================================        
	## _search_GetDates  
	## =======================================================================        
	## this is the main fucntion for this page- it handles the complete 
	## display of all elements- if no ajax is used we will need to handle 
	## all functionality in here
	##
	## =======================================================================
	function _search_GetDates() {	
		## prepare the dates
		$day	= isset($_GET['day'])	? $_GET['day']		: 0;
		$week	= isset($_GET['week'])	? $_GET['week']		: 0;
		$month	= isset($_GET['month'])	? $_GET['month']	: date('n');
		$year	= isset($_GET['year'])	? $_GET['year']		: date('Y');
		
		if($day == 0) {
			## process the parameters into somthing we can us to call the tracker
			$start = mktime(0,   0,  0, $month, 1, $year);
			$end   = mktime(23, 59, 59, $month, date('t', $start), $year);

		} else if ($week) {
			$day_oftheweek = date("w",mktime(0, 0, 0, $month, $day, $year)) -1;
			$start = mktime(0, 0, 0, $month, $day-$day_oftheweek, $year);
			$end = mktime(23, 59, 59, $month, $day+(6-$day_oftheweek), $year);
		} else {
			$start = mktime(0,   0,  0, $month, $day, $year);
			$end   = mktime(23, 59, 59, $month, $day, $year);
		} 
		
		return array('START'=>$start,'END'=>$end);
	}	
	
	
	## =======================================================================        
	## _search_GetDates  
	## =======================================================================        
	## this is the main fucntion for this page- it handles the complete 
	## display of all elements- if no ajax is used we will need to handle 
	## all functionality in here
	##
	## =======================================================================
	function _search_PrepareDates($start,$end) {	
		## init the vars
		$url = '';
		
		## we need to prepare the dates for outputting
		if(date('d',$start) == date('d',$end) && date('n',$start) == date('n',$end) && date('Y',$start) == date('Y',$end)) {
			## okay it's the same day
			$url = 'day='.date('d',$start).'&month='.date('n',$start).'&year='.date('Y',$start);
		} else {	
			## for now we will assume week
			$url = 'day='.date('d',$start).'&month='.date('n',$start).'&year='.date('Y',$start).'&week=1';
		}
		
		return $url;
	}	
?>