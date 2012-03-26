<?php

	## =======================================================================        
	##  search_displayOverview     
	## =======================================================================        
	##  we will display the main screen
	##
	## =======================================================================
	function search_displayOverview() {
		global $Auth, $gSession;
		
		define('DATESELECTOR_TYPE_CALENDAR',0);
		define('DATESELECTOR_TYPE_RANGED',1);

		## Handle HTTP GET parameters
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
			

		$baseURL = "module.php";
		$baseURL = $gSession->url($baseURL);

	
		## get the template for now
		$template = new Template("interface/");
		$template->set_templatefile(array("item" => "interface.tpl","body" => "interface.tpl","calendar" => "interface.tpl","daterange" => "interface.tpl"));
		
		
		## handle navigation
		$tabs = '';	
		$tabs .= ui_renderSectionTab('Overview',$tabsURL.'&cmd=applyFilter&filter='.$current_group['id'],0,0,true);
		$tabs .= ui_renderSectionTab('Thesaurus',$tabsURL.'&cmd=applyFilter&filter='.$current_group['id'],1,0,true);
		$tabs .= ui_renderSectionTab('Featured Results',$tabsURL.'&cmd=applyFilter&filter='.$current_group['id'],2,0,false);
		
		$template->set_var('NAVIGATION',$tabs);



		## we need to handle the calendar and date selector here
		$dateselector_type = (isset($_GET['dateselector'])) ? $_GET['dateselector'] : DATESELECTOR_TYPE_CALENDAR;
		
		if($dateselector_type == DATESELECTOR_TYPE_CALENDAR) {
			$template->set_var('SELECTOR_URL',$baseURL.'&dateselector='.DATESELECTOR_TYPE_RANGED);
			$calendar = $template->fill_block("calendar");	
		} else {
			## we want to make sure that we display the current selction of the dates
  			 			
			## startdate
			$template->set_var('sday',date('d',$start));
			$template->set_var('smonth',date('n',$start));
			$template->set_var('syear',date('Y',$start));

			## enddate
			$template->set_var('eday',date('d',$end));
			$template->set_var('emonth',date('n',$end));
			$template->set_var('eyear',date('Y',$end));
		
			$template->set_var('SELECTOR_URL',$baseURL.'&dateselector='.DATESELECTOR_TYPE_CALENDAR);
			$calendar = $template->fill_block("daterange");
		}
		
		$template->set_var('DATESELECTOR',$calendar);
		
		
		## now handle the actual data	
		$currentPage = intval($_GET['p']) > 0 ? intval($_GET['p']) : 1;
		$data = _fetchTerms($start,$end,($currentPage-1)*20);
		
		$total_items = $data['TOTAL_ITEMS'];
		$data = $data['DATA'];
		
		## get the pager data stuff
		$pager = _generatePager($total_items,20);		
		$template->set_var('PAGER',$pager['pager_output']);
		
		$items = '';
		foreach($data as $current_term) {
			## okay we are looping through all entries in order to output them
			$template->set_var('TERM',$current_term['term']);
			$template->set_var('SEARCH_COUNT',$current_term['numberofsearches']);
			$template->set_var('RESULT_COUNT',intval($current_term['results']));
			
			$items .= $template->fill_block("item");
		}
		$template->set_var('ENTRIES',$items);
	
		
		
		$template->pfill_block("body");
	}
	
	

	## =======================================================================        
	##  search_displayOverview     
	## =======================================================================        
	##  we will display the main screen
	##
	## =======================================================================
	function _fetchTerms($start,$end,$offset=0) {
		## here we store the total items
		$total_items = 0;

		## get the database connection
		$db = new DB_Sql();
		
		$query = "SELECT results FROM ".DB_PREFIX."reports_searchqueries WHERE timestamp > $start AND timestamp < $end GROUP BY term";
		$rp = $db->query($query,false);
		$total_items = $db->num_rows();
		
		## okay now we will get the items
		$query = "SELECT term,COUNT(*) AS numberofsearches,AVG(results) AS results FROM ".DB_PREFIX."reports_searchqueries WHERE timestamp > $start AND timestamp < $end GROUP BY term ORDER BY numberofsearches DESC LIMIT $offset,20";
		$rp = $db->query($query,false);
		
		$data = array();
		while($db->next_record(MYSQL_ASSOC)) {
			$data[] = $db->Record;
		}
		
		return array('TOTAL_ITEMS'=>$total_items,'DATA'=>$data);
	}	
	
	
	## =======================================================================
	## module_getHomepageEventListing												
	## =======================================================================
	## generates the main navigation
	## =======================================================================
	function _generatePager($baseURL,$totalItems,$perPage) {
		global $gSession;
		
		## process any input parameters passed
		$currentPage = intval($_GET['p']) > 0 ? intval($_GET['p']) : 1;
		
		## first calculate the totla number of pages
        $totalPages = ceil((float)$totalItems / (float)$perPage);
        ## make sure the current page is within a valid range
        $currentPage = min($currentPage, $totalPages);

		## we can display only max 8 pages- so if we are on the eigth-
		## and there are more items- we need to move the window
		if($totalPages > 8 && $currentPage >= 8) {
			$start = ($currentPage - 6);
			$end = min($start + 8,$totalPages);
		} else {
			$start = 1;
			$end = min(8,$totalPages);
		}

		$pager_output = '';
		for($i= $start; $i <= $end ; $i++) {
			if($i == $currentPage) {
				$pager_output .= '<a href="#" onclick="dataSelectPage(\''.$baseURL.'&p='.$i.'\');">'.$i.'</a> ';
			} else {
				$pager_output .= '<a href="#" class="nextpage" onclick="dataSelectPage(\''.$baseURL.'&p='.$i.'\');">'.$i.'</a> ';
			}
		}
		
		$pager_output .='<a href="'.$baseURL.'&p='.($currentPage+1).'" class="nextpage"> &gt;&gt;</a>';
		
        return array('pager_output'=>$pager_output,'currentPage'=>$currentPage);
	}	
?>