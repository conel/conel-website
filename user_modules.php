<?php
	session_start();
	require(MATRIX_BASEDIR.'extensions/search.php');
	require(MATRIX_BASEDIR.'extensions/courses.php');
	require(MATRIX_BASEDIR.'extensions/subscription.php');
	
	include_once(ENGINE.'/datatypes/extra_subject_checkboxgroup/subject_checkboxgroup.php');

	## =======================================================================
	## module_getGlobals											
	## =======================================================================
	## 
	## =======================================================================
	function module_getGlobals($id,$params) {
		// init the vars
		$home_id = 172;
		$data = array();
		
		image_getData($home_id,$data);
		linklist_getData($home_id,$data);
		
		// first we handle the add banner
		if(isset($data['AD'])) {
			$banner = '<a href="'.getTargetURL($data['ADLINK'][0]['page_id']).'" class="fl"><img src="/images/'.$data['AD']['filename'].'" width="110" height="114" alt="'.$data['AD']['alt'].'" /></a>';
		}
		
		// nkowald - 2010-01-14 - Set up Enfield CSS if coming from Enfield's site
		if (FROM_ENFIELD) {
			$enfield_css = '<link href="/pop-up/styles/pop-up.css" rel="stylesheet" type="text/css" media="screen" />';
		} else {
			$enfield_css = '';
		}
		
		// nkowald - 2010-01-14 - Set up lightbox code if coming from Enfield's site
		/*
		if (FROM_ENFIELD) {
			$enfield_inc = 
			'<!-- Pop-up code -->
			<script type="text/javascript" src="/pop-up/js/pop-up.js"></script>
			<div id="slimbox">
				<div id="message">
					<div id="logo"><a href="#" id="logo_link"><img border="0" src="http://www.conel.ac.uk/images/logo_300.gif" width="300" alt="The College of Haringey, Enfield and North East London" /></a></div>
					<div id="message_text">
						<p><strong>Enfield College</strong> merged with the College of North East London (CONEL) in August 2009 to form <strong>The College of Haringey, Enfield and North East London</strong>.</p>
						<p>The former Enfield College is now our Enfield Centre.</p>
						<p>You have been redirected to the website for the new merged College. Welcome - we hope you find all of the information that you need here.</p>
						<p class="close_link"><a href="#" id="button_link">Close this message and continue to www.conel.ac.uk</a></p>
					</div>
					<a href="#" id="button_close"><img src="/pop-up/images/button-close.png" alt="" width="48" height="48" /></a>
				</div>
			</div>
			<!-- // pop-up code -->';
		} else {
			$enfield_inc = '';
		}
		 */
		$enfield_inc;
		
		// nkowald - 2010-01-26 - add a footer variable to global modules - we can just update this to add some code to pages now
		$page_info = structure_getPathURL($params['page_id']);
		// replaces slashes in page url
		$page_info = str_replace('/','---',$page_info);
		// build url
		
		$footer_code = '<script type="text/javascript" src="/layout/js/form.js"></script>
		<script type="text/javascript">
	    var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
	    document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
		</script>
		<script type="text/javascript">var pageTracker = _gat._getTracker("UA-3415972-1"); pageTracker._trackPageview();</script>
		<script type="text/javascript" src="http://www.conel.ac.uk/clickheat/js/clickheat.js"></script>
		<script type="text/javascript">
		//<![CDATA[
			clickHeatSite = "";
			clickHeatGroup = "'.$page_info.'";
			clickHeatServer = "http://www.conel.ac.uk/clickheat/click.php";
			initClickHeat(); 
		//]]>
		</script>';

	if (isset($_SESSION['staff_bbq']['attending']) && $_SERVER['REDIRECT_QUERY_STRING'] == 'url=staff_bbq_registered') {
		$staff_bbq_content = "
			<div id=\"staff_bbq_registered\">
			<p>Thank you <b>".$_SESSION['staff_bbq']['name']."</b> for registering your attendance.</p>
			<p>Please print a copy of this ticket and bring it along with you on the day.</p>
			<h3>Ticket</h3>
			<table>
			<tr>
				<td width=\"127\"><strong>Attending:</strong>&nbsp;</td><td> ".$_SESSION['staff_bbq']['attending']."</td>
			</tr>
			<tr>
				<td><strong>Require Transport:</strong>&nbsp;</td><td> ".$_SESSION['staff_bbq']['require_transport']."</td>
			</tr>
			<tr>
				<td><strong>Name:</strong>&nbsp;</td><td> ".$_SESSION['staff_bbq']['name']."</td>
			</tr>
			<tr>
				<td><strong>Email:</strong>&nbsp;</td><td> ".$_SESSION['staff_bbq']['email']."</td>
			</tr>
			<tr>
				<td><strong>Extension:</strong>&nbsp;</td><td> ".$_SESSION['staff_bbq']['extension']."</td>
			</tr>
			</table>
			</div>
		";
	} else {
		$staff_bbq_content = "";
	}
	
	// nkowald - 2011-03-24 - Adding course guides check
	//if ($_SESSION['course_guide'] == TRUE) {
		// Show selected course guides
		$cg_content = '<p>Thank you for submitting the form.<br />Course guides are available below:</p>';
		
		//if ($_SESSION['selected_guides'] && is_array($_SESSION['selected_guides'])) {
			$cg_content .= '<ul class="extra">';
			//foreach ($_SESSION['selected_guides'] as $guide) {
			
				//if ($guide == "YPCG EC") {
					//$cg_content .= '<li><a href="http://www.conel.ac.uk/docs/YPCG_Enfield_2011_12.pdf"><span><img src="/layout/img/pdf.gif" alt="PDF file" /> Young Person\'s Course Guide 2011-12 Enfield Centre, 2.68 MB</span></a></li>';
				//} 
				//if ($guide == "ACG EC") {
					//$cg_content .= '<li><a href="http://www.conel.ac.uk/docs/MCG_Enfield_2011_12.pdf" target="_blank"><span><img src="/layout/img/pdf.gif" alt="PDF file" /> Enfield Adult Course Guide 2011-12, 9.78 MB</span></a></li>';
				//}
				//if ($guide == "YPCG TC") {
					//$cg_content .= '<li><a href="http://www.conel.ac.uk/docs/YPCG_Tottenham_2011_12.pdf"><span><img src="/layout/img/pdf.gif" alt="PDF file" /> Young Person\'s Course Guide 2011-12 Tottenham Centre, 3.24 MB</span></a></li>';
				//}
				//if ($guide == "ACG TC") {
					$cg_content .= '<li><a href="http://www.conel.ac.uk/docs/ACG_2012_13.pdf" target="_blank"><span><img src="/layout/img/pdf.gif" alt="PDF file" /> Adult Course Guide 2012-13, 2.57 MB</span></a></li>';
				//}
				// Adding new (combined course guide)
				//if ($guide == "YPCG") {
					$cg_content .= '<li><a href="http://www.conel.ac.uk/docs/YPCG_2012_13.pdf" target="_blank"><span><img src="/layout/img/pdf.gif" alt="PDF file" /> Young Person\'s Course Guide 2012-13, 15.45 MB</span></a></li>';
				//}
			//}
			$cg_content .= '</ul>';
		//}
	//} else {
		//$cg_content = '<p>Please fill out <a href="/our_courses/download_a_course_guide">our form</a> to access course guides.</p>';
	//}
	
	if ($_SERVER['REQUEST_URI'] != '/our_courses/get_a_course_guide/course_guides') {
		$cg_content = '';
	}
	
	// nkowald - 2011-07-04 - Need to the current page here so facebook like button can use it.
	$current_page = urlencode("http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
		
		return array('BANNER'=>$banner, 'ENFIELD_CSS'=>$enfield_css, 'ENFIELD_INC'=>$enfield_inc, 'FOOTER_CODE'=>$footer_code, 'STAFF_BBQ_CONTENT'=>$staff_bbq_content, 'COURSE_GUIDES'=>$cg_content, 'THIS_PAGE'=>$current_page);
	}
	
	// nkowald - 2011-03-22 - New banner module, woo!
	function module_getBanners() {
		$db = new DB_Sql();	
		$query = "SELECT link, img_url FROM ".DB_PREFIX."banners WHERE active = 1 ORDER BY position ASC";
		$rp = $db->query($query);
		$banners = array();
		$i = 0;
		while($db->next_record(MYSQL_ASSOC)) {
			$banners[$i]['link'] = $db->Record['link'];
			$banners[$i]['img_url'] = $db->Record['img_url'];
			$i++;
		}
		$output = '
		<div class="container">
			<div class="wt-rotator">
				<div class="screen">
					<noscript>
						<!-- placeholder 1st image when javascript is off -->
						<img src="/layout/img/hero2.jpg" alt="Default Banner" />
					</noscript>
				</div>
				<div class="c-panel">
					<div class="buttons">
						<div class="prev-btn"></div>
						<div class="play-btn"></div>    
						<div class="next-btn"></div>               
					</div>
					<div class="thumbnails"><ul>';
				
		// If no banners exist in the webmatrix_banners table, show a default banner

		if (count($banners) == 0) {
			$output .= '<li><a href="/layout/img/hero2.jpg"><img src="/layout/img/hero2.jpg" alt="Default Banner" /></a><a href="#"></a></li>';
		} else {
			foreach ($banners as $banner) {
				$output .= "<li><a href=\"".$banner['img_url']."\"><img src=\"".$banner['img_url']."\" alt=\"Home Banner\" width=\"955\" height=\"196\" /></a><a href=\"".$banner['link']."\"></a></li>\n";
			}
		}
		$output .= '</ul></div></div></div></div>';
		
		return $output;
	}

	## =======================================================================
	## module_boxNews										
	## =======================================================================
	## 
	## =======================================================================
	function module_boxNews($id,$params) {
		// fetch the  subjects for this page
		// get the course id
		$data = array();
		subject_checkboxgroup_getData($params['page_id'],$data);

		// prepare the target audiences
		$audience[] = 4;
		if (!empty($data['TARGET']['text'])) {
			foreach($data['TARGET']['text'] as $current_audience) {
				$audience[] = $current_audience;
			}
		}
		
		// prepare the subquery
		$targets = '('.join(',',$audience).')';
		
		$db = new DB_Sql();	
		$output = array();

		// prepare the query
		$query = "SELECT DISTINCT(C.page_id),C.date FROM ".DB_PREFIX."user_pages AS B INNER JOIN ".DB_PREFIX."page_date AS C ON B.page_id=C.page_id INNER JOIN ".DB_PREFIX."extra_checkboxgroup AS D ON B.page_id=D.page_id WHERE D.identifier='TARGET' AND D.text IN $targets AND C.identifier='DATE' AND B.template=65 ORDER BY C.date DESC LIMIT 5";
		$rp = $db->query($query);

		$output = '';
		while($db->next_record(MYSQL_ASSOC)) {			
			$current_page = $db->Record['page_id'];
			$data = array();
			
			text_getData($current_page,$data);
			date_getData($current_page,$data);
			
			$output .= '<li><a href="'.getTargetURL($current_page).'">'.htmlentities(stripslashes($data['HEADLINE']['text'])).'<span>'.date('j F Y',$data['DATE']['date']).'</span></a></li>';
		}

		$return_value = '';
		if(!empty($output)) {
			$return_value = '<div class="box"><h3>Latest News</h3><ul class="news">'.$output.'</ul><div class="box_bottom1"><hr/></div></div>';
		}
		
		return $return_value;
	}
	
	// nkowald - 2009-08-12: Added Dynamic Subject List as had to be manually added before
	function module_outputSubjectLinks() {
		$db = new DB_Sql();	
		$output = array();

		// prepare the query
		$query = "SELECT * FROM tblsubject";
		$rp = $db->query($query);

		$output = '<div class="box"><h3>Subjects</h3><ul>';
		while($db->next_record(MYSQL_ASSOC)) {
			$subject = htmlentities($db->Record['Description']);
			if ($subject != 'Not Applicable') {
				// convert subject to url format
				$subject_page = str_replace(' ','_',strtolower($subject));
				// build url
				// nkowald - 2009-10-26 - Updated Courses URL
				$subject_url = '/our_courses/subjects/'.$subject_page;
				$output .= '<li><a href="'.$subject_url.'"><span>'.$subject.'</span></a></li>';
			}
		}
		$output .= '</ul><div class="box_bottom"><hr /></div></div>';
		
		return $output;
	} //module_outputSubjectLinks
	
	
	## =======================================================================
	## module_boxEvents												
	## =======================================================================
	## gets name and link of parent page
	## =======================================================================
	function module_boxEvents($id,$params) {
		// fetch the  subjects for this page
		// get the course id
		$data = array();
		subject_checkboxgroup_getData($params['page_id'],$data);

		// prepare the target audiences
		$audience[] = 4;
		foreach($data['TARGET']['text'] as $current_audience) {
			$audience[] = $current_audience;
		}
		
		// prepare the subquery
		$targets = '('.join(',',$audience).')';
		
		$db = new DB_Sql();	
		$output = array();

		// prepare the query
		$query = "SELECT DISTINCT(C.page_id),C.date FROM ".DB_PREFIX."user_pages AS B INNER JOIN ".DB_PREFIX."page_date AS C ON B.page_id=C.page_id INNER JOIN ".DB_PREFIX."extra_checkboxgroup AS D ON B.page_id=D.page_id WHERE D.identifier='TARGET' AND D.text IN $targets AND C.identifier='DATE' AND B.template=66 AND C.date > ".mktime(23,59,59,date("m"),date("d")-1,date("Y"))." ORDER BY C.date DESC LIMIT 5";
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
	

	## =======================================================================
	## module_getEventsNewsHome												
	## =======================================================================
	## gets name and link of parent page
	## =======================================================================
	function module_getEventsNewsHome($id,$params) {
		// we need to display all events/news  (by audience)
		$db = new DB_Sql();	
		 
		switch($params['parameter']) {
			case '1':
				$targets = '(1,4)';
				break;
			case '2':
				$targets = '(2,4)';
				break;
			case '3':
				$targets = '(3,4)';
				break;
			default:
				$targets = '(4)';
		}
				
		// prepare the query
		$query = "SELECT DISTINCT(A.page_id),C.date FROM ".DB_PREFIX."extra_checkboxgroup AS A INNER JOIN ".DB_PREFIX."user_pages AS B ON A.page_id=B.page_id INNER JOIN ".DB_PREFIX."page_date AS C ON B.page_id=C.page_id INNER JOIN ".DB_PREFIX."extra_checkboxgroup AS D ON A.page_id=D.page_id WHERE D.identifier='TARGET' AND D.text IN $targets AND C.identifier='DATE' AND B.template IN(66,65) ORDER BY C.date DESC LIMIT 10";
		$rp = $db->query($query);

		$output = '';
		$count = 0;
		while($db->next_record(MYSQL_ASSOC)) {		
			$current_page = $db->Record['page_id'];
			$page = structure_getStructureID($current_page);
			if (!checkFlag($page["flags"],PAGE_INVISIBLE) && checkFlag($page["flags"],PAGE_ACTIVE)) {
				$data = array();	
				
				text_getData($current_page,$data);
				date_getData($current_page,$data);

				$date = date('j F Y',$data['DATE']['date']);
				$news_title = htmlentities(stripslashes($data['HEADLINE']['text']));
				
				// nkowald - 2009-11-02 - Need to get thumbnail image if it exists
				$db2 = new DB_Sql();
				$query = "SELECT image_id FROM webmatrix_image where page_id = '$current_page'";
				$rp = $db2->query($query);
				while($db2->next_record(MYSQL_ASSOC)) {
					$image_id = $db2->Record['image_id'];
				}
				// If an image is found, get the filename
				if ($image_id != '') {
					$db3 = new DB_Sql();
					$query = "SELECT filename FROM webmatrix_image_data where image_id = '$image_id'";
					$rp = $db3->query($query);
					while($db3->next_record(MYSQL_ASSOC)) {
						$filename = $db3->Record['filename'];
					}
				}
				// We want to use the thumbnail version so need to add '_100_100' onto the end of the filename
				if ($filename != '') {
					$no_extension = implode('.', explode('.', $filename, -1));
					$filename = $no_extension . '_100_100.jpg';
				}
				
				if ($current_page == 2274) {
					$thumbnail = '<a href="'.getTargetURL($current_page).'"><img src="/images/news_icon1.gif" alt="" width="55" height="55" /></a>';
				} else if ($current_page == 2361) {
					$thumbnail = '<a href="'.getTargetURL($current_page).'"><img src="/images/lsis_logo_small.gif" alt="" width="55" height="55" /></a>';
				} else {
					$thumbnail = ($filename != '') 
					? '<a href="'.getTargetURL($current_page).'"><img src="/images/'.$filename.'" alt="" width="55" height="55" /></a>' 
					: 
					'<a href="'.getTargetURL($current_page).'"><img src="/layout/img/icon_calendar.gif" alt="" width="55" height="55" /></a>';
				}
				//$no_chars = 42;
				//$news_title = (strlen($news_title) > $no_chars) ? substr($news_title,0,$no_chars) . "..." : $news_title;
				$output .= '<p><span class="home_news_link"><a href="'.getTargetURL($current_page).'" class="underline" title="'.htmlentities(stripslashes($data['HEADLINE']['text'])).' - '.$date.'">'.$news_title.'</a></span><span class="home_news_img">'.$thumbnail.'</span></p>';
				$count++;
			}
			
			// Home News & Events should only show latest two events
			if ($count > 2) {
				break;
			}
		}

		return $output;

	}



	## =======================================================================
	## module_getHomepageImage												
	## =======================================================================
	## gets name and link of parent page
	## =======================================================================
	function module_getEventsSubjects($id,$params) {
		// fetch the  subjects for this page
		$data = array();
		subject_checkboxgroup_getData($params['page_id'],$data);
		
		if(isset($data['SUBJECT']['text'])) {
			// prepare the db
			$db = new DB_Sql();
			
			// fetch the subjects
			$subjects = $data['SUBJECT']['text'];
			
			$subquery = array();
			foreach($subjects as $current_subject) {
				$subquery[] = "'".$current_subject."'";
			}
			
			$subquery = join(',',$subquery);
			
			// prepare the query
			$query = "SELECT DISTINCT(A.page_id),C.date FROM ".DB_PREFIX."extra_checkboxgroup AS A INNER JOIN ".DB_PREFIX."user_pages AS B ON A.page_id=B.page_id INNER JOIN ".DB_PREFIX."page_date AS C ON B.page_id=C.page_id WHERE C.identifier='DATE' AND A.identifier='SUBJECT' AND A.text IN($subquery) AND B.template=66 AND C.date > ".mktime(23,59,59,date("m"),date("d")-1,date("Y"))." ORDER BY C.date DESC LIMIT 5";
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



	## =======================================================================
	## module_getHomepageImage												
	## =======================================================================
	## gets name and link of parent page
	## =======================================================================
	function module_getNewsSubjects($id,$params) {
		// fetch the  subjects for this page
		$data = array();
		subject_checkboxgroup_getData($params['page_id'],$data);
		
		if(isset($data['SUBJECT']['text'])) {
			// prepare the db
			$db = new DB_Sql();
			
			// fetch the subjects
			$subjects = $data['SUBJECT']['text'];
			
			$subquery = array();
			foreach($subjects as $current_subject) {
				$subquery[] = "'".$current_subject."'";
			}
			
			$subquery = join(',',$subquery);
			
			/*
			// Modified: 9/6/2009 - nkowald: Get events from current year onwards
			$current_year = date('Y'); // Current date as year - eg. 2009
			$query = "SELECT DISTINCT(A.page_id),C.date FROM ".DB_PREFIX."extra_checkboxgroup AS A INNER JOIN ".DB_PREFIX."user_pages AS B ON A.page_id=B.page_id INNER JOIN ".DB_PREFIX."page_date AS C ON B.page_id=C.page_id WHERE C.identifier='DATE' AND A.identifier='SUBJECT' AND A.text IN($subquery) AND B.template=65 AND FROM_UNIXTIME(C.date,'%Y') >= $current_year ORDER BY C.date DESC LIMIT 5";
			*/
			
			// Modified: 9/6/2009 - nkowald: Get events from a year ago onwards
			$stamp = mktime(now-8766); // 8766 hours in a year (approx.)
			$date_events_from = date('Y-m-d H:i:s',$stamp); // Date one year ago
			$query = "SELECT DISTINCT(A.page_id),C.date FROM ".DB_PREFIX."extra_checkboxgroup AS A INNER JOIN ".DB_PREFIX."user_pages AS B ON A.page_id=B.page_id INNER JOIN ".DB_PREFIX."page_date AS C ON B.page_id=C.page_id WHERE C.identifier='DATE' AND A.identifier='SUBJECT' AND A.text IN($subquery) AND B.template=65 AND FROM_UNIXTIME(C.date) >= DATE_FORMAT('$date_events_from','%Y-%m-%d %H:%i:%s') ORDER BY C.date DESC LIMIT 5";
			
			$rp = $db->query($query);

			$output = '';
			while($db->next_record(MYSQL_ASSOC)) {			
				$current_page = $db->Record['page_id'];
				$data = array();
				
				text_getData($current_page,$data);
				date_getData($current_page,$data);
				
				$output .= '<li><a href="'.getTargetURL($current_page).'">'.htmlentities(stripslashes($data['HEADLINE']['text'])).'<span>'.date('j F Y',$data['DATE']['date']).'</span></a></li>';
			}
			
			$return_value = '';
			if(!empty($output)) {
				$return_value = '<div class="box"><h3>Latest News</h3><ul class="news">'.$output.'</ul><div class="box_bottom1"><hr/></div></div>';
			}
			
			return $return_value;
		}
	}
	
	## =======================================================================
	## module_getHomepageImage												
	## =======================================================================
	## gets name and link of parent page
	## =======================================================================
	function module_getPanels($id,$params) {
		// fetch the  subjects for this page
		$data = array();
		subject_checkboxgroup_getData($params['page_id'],$data);
		
		if(isset($data['SUBJECT']['text'])) {
			// prepare the db
			$db = new DB_Sql();
			
			// fetch the subjects
			$subjects = $data['SUBJECT']['text'];
			
			$subquery = array();
			foreach($subjects as $current_subject) {
				$subquery[] = "'".$current_subject."'";
			}
			
			$subquery = join(',',$subquery);
			
			// prepare the query
			$query = "SELECT A.page_id FROM ".DB_PREFIX."extra_checkboxgroup AS A INNER JOIN ".DB_PREFIX."user_pages AS B ON A.page_id=B.page_id WHERE A.identifier='SUBJECT' AND A.text IN($subquery) AND B.template=62  ORDER BY RAND() LIMIT 1";
			$rp = $db->query($query);

			if($db->next_record(MYSQL_ASSOC)) {
				$page_id = $db->Record['page_id'];
				return _page_generatePage($page_id,'l_testimonial');
			}
		}
	}

## =======================================================================        
##  generate_page        
## =======================================================================        
##  generates a page identified by a page_id  
##
##  TODO:       
## ======================================================================= 
function _page_generatePage($page_id,$template) {
	global $Auth,$input_language;
			
	## multiclient
	$client_id = $Auth->auth["client_id"];

	$output = '';
	$menu_id 		= isset($pageInfo["id"]) ? $pageInfo["id"] : 0;
		
	$dbConnection = new DB_Sql();

	$xmlFile  = $template.".xml";
	$filename = $template.".tpl";


	## prepare the template file
	$layout_template = new Template(HTML_DIR);
	$layout_template->set_templatefile(array("pager"=>$filename,"head" => $filename,"body" => $filename,"foot" => $filename)); 
	
	## here we set the global vars- the user can set them within the templates:
	$layout_template->set_var("matrix:TITLE",$menu_text);
	$layout_template->set_var("matrix:PAGEID",$page_id);
	$layout_template->set_var("matrix:TARGETPAGE",getTargetURL($page_id));
	$layout_template->set_var("matrix:MODDATE",utility_prepareDate(strtotime($modified),DEFAULT_DATE));
	
	##$output .= $layout_template->fill_block("head");

	## for the body we need to examine the xml file- to find out 
	## what type of form elements we need to position
	$wt = new xmlparser(HTML_DIR.$xmlFile);
	$wt->parse();
	
	## okay we scanned in the xml file- so we now loop through all the elements
	$elements = $wt->getNormalElements();	
	$objects = $wt->getNormalObjects();	
	
	## we should get the page content
	$page_record = page_getPage($page_id,$objects);

	$counter =0;
	$num_elements = count($elements);
	while($counter < $num_elements) {
		## store the output
		$storage = ' ';	
	
		## okay first we try to find out what type we have
		## we wrap this up in a switch statemnt- this way we can extend it more easily
		$element_type = $elements[$counter]['TYPE'];
		$element_name = $elements[$counter]['NAME'];

		switch($element_type) {
			case 'TEXT':
			case 'COPYTEXT':
			case 'DATE': 
			case 'LINK' :
			case 'FILE':
			case 'BOX':
			case 'LINKLIST':
			case 'IMAGE': {
				## get the data and set the var in the template
				$target = strtolower($element_type); 
				if(isset($page_record[$element_name])) {
					$function = "output_".$target;
					$element = $function($page_record[$element_name],$elements[$counter],$menu_id,$page_id);

					if(is_array($element)) {
						$layout_template->set_vars($element);
					} else {
						$layout_template->set_var($element_name,$element);
					}
				}
				break;
			}
			case 'INCLUDE' : {
				## basically we need to call the function output_"element_type"
				## and the output the results to the template
				$target = strtolower($element_type); 
				$function = "output_".$target;
				$element = $function('',$elements[$counter],$menu_id,$page_id);				
				if(is_array($element)) {
					$layout_template->set_vars($element);
				} else {
					$layout_template->set_var($element_name,$element);
				}
				break;
			}								
			case 'LISTVIEW': {
				$element = "";
				
				$element = output_listview($page_record,$elements[$counter],$layout_template,$menu_id,$page_id); 
				$layout_template->set_var($element_name,$element);	
				break;	
			}																					
			case 'DIVIDER': {
				break;
			}
			default: {
				## we need to check if we have a module for this datatype
				$target = strtolower($element_type);
				
				## first we try to include the apropriate file 
				@include_once("datatypes/extra_".$target."/".$target.".php");
				## now we check if the function exists
				
				if(function_exists($target."_output")) {
					## no we call the function
					## check if the page_record entry is defined
					## if not we need to pass the whole record
					$function = $target."_output";
					if(isset($page_record[$element_name])) {
						$element = $function($page_record[$element_name],$elements[$counter],$menu_id,$page_id);
					} else {
						$element = $function($page_record,$elements[$counter],$layout_template,$menu_id,$page_id);
					}	
					
					if(is_array($element)) {
						$layout_template->set_vars($element);
					} else {
						$layout_template->set_var($element_name,$element);
					}
										
				}
				break;
			}
		}

		$counter++;
	}

	## this is it- so we will flush the template here		
	$output .= $layout_template->fill_block("body");

	return $output;
}

	
	## =======================================================================
	## _outputOccurence											
	## =======================================================================
    //modified 8/4/2009 by Huseyin Altindag
	function _outputOccurence($data,$occurrence_count=1) {
	
		// init the vars
		$output = '';
		static $optionNr=1;
    
		// we need to fetch the timetable information for this occurrence
		$db = new DB_Sql();
		$query = "SELECT * FROM tbloccurrences WHERE ID = '".$data['ID']."'";
		
		$rp = $db->query($query);

		$sOptionText="Option ".$optionNr;
		
		// When qualification starts and ends
		$output = '<h3 class="hidden">' . $sOptionText . '</h3>';
		$output .= '<p class="clearfix"><span class="title1">'.$sOptionText.'</span>
		<span class="info">Starts: '.$data['Qual_start'].'<br />
		Ends: '.$data['Qual_end'].'</span></p>';
		
		// nkowald - 2009-10-26 - Adding course length field
		$weeks_per_year = ereg_replace("[^0-9]", "", $data['Weeks_per_acyear']);
		$qual_start = ereg_replace("[^0-9]", "", $data['Qual_start']);
		$qual_end = ereg_replace("[^0-9]", "", $data['Qual_end']);
		$no_years = $qual_end - $qual_start;
		$no_years = ($no_years == '0' || $no_years == '1') ? '1' : $no_years;
		$year_text = ($no_years > 1) ? 'years' : 'year';
		
		if (is_numeric($weeks_per_year) && $weeks_per_year != '0') {
			$output .= '<p class="clearfix"><span class="title1"></span><span class="info">Course length: &nbsp;'.$weeks_per_year.' weeks per year, '.$no_years.' '.$year_text.'&nbsp;&nbsp;</span></p>';
		} else {
			$output .= '<p class="clearfix"><span class="title1"></span><span class="info">Course length: &nbsp;'.$no_years.' '.$year_text.'&nbsp;&nbsp;</span></p>';
		}
		
		// Modified: 10/6/2009 - nkowald
		// Change to check for number, else display 'To be confirmed'
		if (is_numeric($data['Hours_per_week']) && is_numeric($data['Days_per_week'])) {
		    $day_text = ($data['Days_per_week'] == "1") ? "day" : "days";
		    $output .= '<p class="clearfix"><span class="title1"></span><span class="info">Study hours: '.$data['Hours_per_week'].'&nbsp;over&nbsp;'.$data['Days_per_week'].' '.$day_text.' per week'.'</span></p>';
		} else {
			// nkowald - 2009-10-26 - Leaving fields out if there is no data
			//$output .= '<p class="clearfix"><span class="title1"></span><span class="info">Study hours: To be confirmed'.'</span></p>';
		}
		
		// Modified: 9/6/2009 - nkowald
		// If Tuition_fee is a number show it with a pound sign
		$tuition_fee = ereg_replace("[^0-9]", "", $data['Tuition_fee']);
		if (is_numeric($tuition_fee) && $tuition_fee != '0') {
			$output .= '<p class="clearfix"><span class="title1"></span><span class="info">Tuition fee: &nbsp;&pound;'.$tuition_fee.'&nbsp;&nbsp;</span></p>';
		} else {
			// nkowald - 2009-10-26 - Leaving fields out if there is no data
			//$output .= '<p class="clearfix"><span class="title1"></span><span class="info">Tuition fee: &nbsp;To be confirmed</span></p>';
		}

		// If Examreg_fee is a number show it with a pound sign
		$examreg_fee = ereg_replace("[^0-9]", "", $data['Examreg_fee']);
		if (is_numeric($examreg_fee) && $examreg_fee != '0') {
			$output .= '<p class="clearfix"><span class="title1"></span><span class="info">Exam fee: &nbsp;&pound;'.$examreg_fee.'&nbsp;&nbsp;</span></p>';
		} else {
			// nkowald - 2009-10-26 - Leaving fields out if there is no data
		   //$output .= '<p class="clearfix"><span class="title1"></span><span class="info">Exam fee: &nbsp;To be confirmed</span></p>';
		}
		
		// If Materials_fee is a number show it with a pound sign
		$materials_fee = ereg_replace("[^0-9]", "", $data['Materials_fee']);
		if (is_numeric($materials_fee) && $materials_fee != '0') {			
			$output .= '<p class="clearfix"><span class="title1"></span><span class="info">Materials fee: &nbsp;&pound;'.$materials_fee.'<br /> </span></p>';
		} else {
			// nkowald - 2009-10-26 - Leaving fields out if there is no data
			//$output .= '<p class="clearfix"><span class="title1"></span><span class="info">Materials fee: &nbsp;To be confirmed'.'<br /> </span></p>';
		}
		
		// nkowald - 2009-10-09 - Added 'Location' data to occurrences on course pages
		// If Location column set
		$location = ($data['Location'] != '') ? $data['Location'] : '';
		if (($location != "") && ($location == "Tottenham Site" || $location == "Enfield Site")) {			
			$output .= '<p class="clearfix"><span class="title1"></span><span class="info">Location: &nbsp;'.$location.'<br /> </span></p>';
		}
		
		// More info link
		// nkowald - 2009-10-26 - Updated Courses URL
		$output .= '<p class="clearfix"><span class="title1"></span><span class="info"><a class="HAcss1" href="/our_courses/fees_and_financial_support">More info on fees</a></span></p>';
		  
		$optionNr++; //increase by 1 Option 1,2,3......

		return $output;
		
	} //_outputOccurence()


	## =======================================================================
	## module_ParentLink												
	## =======================================================================
	## gets name and link of parent page
	## =======================================================================
	function module_ParentLink($id,$params) {
		$page_id = $params['page_id'];
		$parent_page_id = structure_getParentPageID($page_id);
		## fetch the data for this page
		$data = array();
		text_getData($parent_page_id,$data);
		

		$return_value = array();
		$return_value['PHEADLINE'] = $data['HEADLINE']['text'];
		$return_value['PINTRO'] = $data['INTRO']['text'];
		$return_value['PLINK'] = getTargetURL($parent_page_id);
		
		return $return_value;
	}

	## =======================================================================
	## module_JumptoParent												
	## =======================================================================
	## gets name and link of parent page
	## =======================================================================
	function module_JumptoParent($id,$params) {
		$page_id = $params['page_id'];
		$parent_page_id = structure_getParentPageID($page_id);
		## fetch the data for this page
		header("Location: ".SITE_URL.getTargetURL($parent_page_id));			
		header("Status: 303");
		exit;		
	}


	## =======================================================================
	## module_getHomepageImage												
	## =======================================================================
	## gets name and link of parent page
	## =======================================================================
	function module_getHomepageImage($id,$params) {
		
		$pages = structure_getAllSubPages($id);
		$current_feature = array_rand($pages);

		## okay now we need to output the whole thing
		$output['QUOTE'] = _pageCustomCreatePage($pages[$current_feature]['page_id'],'l_storyq');
		$output['IMAGE'] = _pageCustomCreatePage($pages[$current_feature]['page_id'],'l_storyi');
	
		return $output;
	}
	
		
	## =======================================================================        
	##  generate_page        
	## =======================================================================        
	##  generates a page identified by a page_id  
	##
	##  TODO:       
	## ======================================================================= 
	function _pageCustomCreatePage($page_id,$filename) {
		global $Auth,$input_language;
				
		## multiclient
		$client_id = $Auth->auth["client_id"];
	
		$output = '';
		
		$pageInfo = frontend_getPageInfo($page_id,$client_id);
		$page_id 		= intval($pageInfo["page_id"]);
		$menu_id 		= isset($pageInfo["id"]) ? $pageInfo["id"] : 0;	
				
		$dbConnection = new DB_Sql();
				
		if($page_type == 'folder') {
			## we need to output the folder by calling
			## the folder output functions
			folder_outputFolder($page_id);
			exit();	
		}
		
		$xmlFile  = $filename.".xml";
		$filename = $filename.".tpl";
	
		if ($filename == ".tpl") {
			## maybe we can come with some good default behavior
			exit();
		}
				
		## prepare the template file
		$layout_template = new Template(HTML_DIR);
		$layout_template->set_templatefile(array("pager"=>$filename,"head" => $filename,"body" => $filename,"foot" => $filename)); 
		
		## here we set the global vars- the user can set them within the templates:
		$layout_template->set_var("matrix:TITLE",$menu_text);
		$layout_template->set_var("matrix:PAGEID",$page_id);
		$layout_template->set_var("matrix:TARGETPAGE",getTargetURL($page_id));
		$layout_template->set_var("matrix:MODDATE",utility_prepareDate(strtotime($modified),DEFAULT_DATE));
	
	
		## for the body we need to examine the xml file- to find out 
		## what type of form elements we need to position
		$wt = new xmlparser(HTML_DIR.$xmlFile);
		$wt->parse();
		
		## okay we scanned in the xml file- so we now loop through all the elements
		$elements = $wt->getElements();	
		$objects = $wt->getObjects();	
		
		## we should get the page content
		$page_record = page_getPage($page_id,$objects);
	
		$counter =0;
		$num_elements = count($elements);
		while($counter < $num_elements) {
			## store the output
			$storage = ' ';	
		
			## okay first we try to find out what type we have
			## we wrap this up in a switch statemnt- this way we can extend it more easily
			$element_type = $elements[$counter]['TYPE'];
			$element_name = $elements[$counter]['NAME'];
	
			switch($element_type) {
				case 'TEXT':
				case 'COPYTEXT':
				case 'DATE': 
				case 'LINK' :
				case 'FILE':
				case 'BOX':
				case 'LINKLIST':
				case 'IMAGE': {
					## get the data and set the var in the template
					$target = strtolower($element_type); 
					if(isset($page_record[$element_name])) {
						$function = "output_".$target;
						$element = $function($page_record[$element_name],$elements[$counter],$menu_id,$page_id);
	
						if(is_array($element)) {
							$layout_template->set_vars($element);
						} else {
							$layout_template->set_var($element_name,$element);
						}
					}
					break;
				}
				case 'INCLUDE' : {
					## basically we need to call the function output_"element_type"
					## and the output the results to the template
					$target = strtolower($element_type); 
					$function = "output_".$target;
					$element = $function('',$elements[$counter],$menu_id,$page_id);				
					if(is_array($element)) {
						$layout_template->set_vars($element);
					} else {
						$layout_template->set_var($element_name,$element);
					}
					break;
				}								
				case 'LISTVIEW': {
					$element = "";
					
					$element = output_listview($page_record,$elements[$counter],$layout_template,$menu_id,$page_id); 
					$layout_template->set_var($element_name,$element);	
					break;	
				}	
			
																				
				case 'DIVIDER': {
					break;
				}
				default: {
					## we need to check if we have a module for this datatype
					$target = strtolower($element_type);
					
					## first we try to include the apropriate file 
					@include_once("datatypes/extra_".$target."/".$target.".php");
					## now we check if the function exists
					
					if(function_exists($target."_output")) {
						## no we call the function
						## check if the page_record entry is defined
						## if not we need to pass the whole record
						$function = $target."_output";
						if(isset($page_record[$element_name])) {
							$element = $function($page_record[$element_name],$elements[$counter],$menu_id,$page_id);
						} else {
							$element = $function($page_record,$elements[$counter],$layout_template,$menu_id,$page_id);
						}	
						
						if(is_array($element)) {
							$layout_template->set_vars($element);
						} else {
							$layout_template->set_var($element_name,$element);
						}
											
					}
					break;
				}
			}
	
			$counter++;
		}
	
		## this is it- so we will flush the template here		
		$output .= $layout_template->fill_block("body");
	
		return $output;
	}
		
	
	## =======================================================================
	## module_Sitemap												
	## =======================================================================
	## generates the main navigation
	## =======================================================================
	function module_Sitemap($id,$params) {
		// we need to fetch all pages- start with the root level
		return _sitemap_Level(0);
	}	
	
	function _sitemap_Level($structure_id) {
		// init the forbidden templates
		$hidden_templates = array(56,80,70,87,48);
	
		// we need to fetch all pages- start with the root level
		$pages = structure_getAllSubPages($structure_id);

		$class = '';
		$subclass = '';
		if($structure_id == 0) {
			$class = ' class="sitemap"';
			$subclass = ' class="main"';
		}
				
		$output = '';
		foreach($pages as $current_page) {
			if(!checkFlag($current_page["structure_flag"],PAGE_INVISIBLE) && checkFlag($current_page["structure_flag"],PAGE_ACTIVE) && !in_array($current_page['template'],$hidden_templates)) {
				// process only with we are active				
				$output .= '<li '.$subclass.'><a href="'.getTargetURL($current_page["page_id"]).'"><span '.$subclass.'>'.htmlentities($current_page['text'], ENT_QUOTES,'UTF-8').'</span></a>';
				
				// handle the subject pages- they need to retrieve the courses underneath them
				if($current_page['template'] == 45) {
					// fetch the courses
					$courses = _getSubjectCourses($current_page['page_id']);
					
					if(!empty($courses)) {
						$output .= '<ul>';
						foreach($courses as $current_course) {
							$output .= '<li><a href="/our_courses/course_search/course/'.$current_course["id"].'"><span>'.htmlentities($current_course['Description'], ENT_QUOTES,'UTF-8').'</span></a></li>';
						}
						$output .= '</ul>';
					}
					
				} else {
					$output .= _sitemap_Level($current_page['id']);
				}
				
				$output .= '</li>';

				
				// onyl if we are active we will go a level deeper
				
			}
		}
		
		if(!empty($output)) {
			$output = '<ul'.$class.'>'.$output.'</ul>';
			return $output;
		}
		
		return '';
	}
	
	## =======================================================================
	## module_Navigation												
	## =======================================================================
	## generates the main navigation
	## =======================================================================
	function module_Navigation($id,$params) {
		// init the forbidden templates
		$hidden_templates = array(56,80,70,87,48,67,64);
		
		$return_value = array();
		
		$area_id = -1;
		$area_title = ''; ## holds the main section
		$section_title = ''; ## holds the name of the current_page
		$subsection_title = '';
		$subsubsection_title = '';
		
		$pageTitle 			= "";
		$page_structure_id 	= $id;
		$lastPageTitle 		= "";
		$levelcounter 		= 0;
		$tree_array 		= array();

		## first entry needs to be the current entry
		$tree_array[] 			= $id;		
		while((strpos($pageTitle,'column')===false) && isset($page_structure_id)) {
			$page = structure_getPage($page_structure_id);
						
			$lastPageTitle 			= $pageTitle;
			$pageTitle 				= isset($page['text']) ? $page['text'] : null;
			$page_structure_id 		= isset($page['parent']) ? $page['parent'] : null;
			$page_structure_menu_id = isset($page['id']) ? $page["id"] : null;
			$tree_array[] 			= $page_structure_menu_id;
			$levelcounter++;
		}
		
		## turn the array upside down
		$tree_array = array_reverse($tree_array);
		
		$url_addition = '';

		## now we should get the correct tree structure (only the items we need for our pages)
		$pages = array();
		foreach($tree_array as $current_menu_id) {
			if(!isset($current_menu_id)) {
				$current_menu_id =0;
			}
			$pages[] = structure_getAllSubPages($current_menu_id);
		}
		
		
		## okay now we should loop thorugh all entries and fill them with the
		## correct design- and into the correct variable
		$level_1 = '';
		$dots = '';
		## first the top_level navigation
		$counter = 0;
		foreach($pages[0] as $current_page) {
			
			if (!checkFlag($current_page["structure_flag"],PAGE_INVISIBLE) && checkFlag($current_page["structure_flag"],PAGE_ACTIVE) && !in_array($current_page['template'],$hidden_templates)) {
				// prepare the classes
				if($counter == 0) {
					$class = 'first';
					$dots = 'class="homeactive"';
				} else {
					$class = '';
				}
				
				// nkowald - Make News and Events read 'News'
				if ($current_page['text'] == 'News & Events') {
					$current_page['text'] = 'News';
				}
				
				// nkowald - 2009-10-27 - Need to add a class to <li> to style it.
				$li_class_name = str_replace('/','',getTargetURL($current_page["page_id"]));
				$li_class = ($li_class_name != '') ? ' class="'.$li_class_name.'"' : '';
			
				if($current_page["id"] == $tree_array[1]) {
					$level_1 .= '<li'.$li_class.'><a href="'.getTargetURL($current_page["page_id"]).'" class="active '.$class.'"><span '.$dots.'>'.htmlentities($current_page['text'], ENT_QUOTES,'UTF-8').'</span></a></li>';
				} else {
					if($class != ''){
						$level_1 .= '<li'.$li_class.'><a href="'.getTargetURL($current_page["page_id"]).'" class="'.$class.'"><span>'.htmlentities($current_page['text'], ENT_QUOTES,'UTF-8').'</span></a></li>';
					} else {
						$level_1 .= '<li'.$li_class.'><a href="'.getTargetURL($current_page["page_id"]).'"><span>'.htmlentities($current_page['text'], ENT_QUOTES,'UTF-8').'</span></a></li>';
					}
				}					
				$dots = '';
				$counter++;
			}

		}

		$level_2 = _generateLevel(1,$pages,$tree_array);
		
		// nkowald - 2009-10-09 - Need styles to be specific to the root page we're on
		// Gets top-level page - TODO: there's GOT to be a better way than this.. very tired so this will have to do 
		$friendly_name_url = substr($_SERVER['REQUEST_URI'],1);
		$friendly_name_url = substr($friendly_name_url,0,strlen($friendly_name_url));
		if (strpos($friendly_name_url,'/')) {
			$top_level = substr($friendly_name_url,0,strpos($friendly_name_url,'/'));
		} else {
			$top_level = substr($friendly_name_url,0);
		}
		$section_left = " menu_$top_level";
		$section_top = " topmenu_$top_level";
		$section = $top_level;
		
		return array('LEVEL_1'=>$level_1,'LEVEL_2'=>$level_2,'SECTION_LEFT'=>$section_left,'SECTION_TOP'=>$section_top,'SECTION'=>$section);		
	}	
	
	
	function _generateLevel($current_level,$pages,$tree_array) {
		// init the forbidden templates
		$hidden_templates = array(56,80,70,87,48,67,64);
		
		$output = '';
		foreach($pages[$current_level] as $current_page) {
			if (!checkFlag($current_page["structure_flag"],PAGE_INVISIBLE) && checkFlag($current_page["structure_flag"],PAGE_ACTIVE) && !in_array($current_page['template'],$hidden_templates)) {

				if($current_page["id"] == $tree_array[$current_level+1]) {
					$output .= '<li><a href="'.getTargetURL($current_page["page_id"]).'" class="active"><span>'.htmlentities($current_page['text'], ENT_QUOTES,'UTF-8').'</span></a>';
					
					$level_sub = _generateLevel($current_level+1,$pages,$tree_array);
					
					if(!empty($level_sub)) {
						$output .= '<ul>'.$level_sub.'</ul>';
					}
					
					$output .= '</li>';
						
				} else {
					$output .= '<li><a href="'.getTargetURL($current_page["page_id"]).'"><span>'.htmlentities($current_page['text'], ENT_QUOTES,'UTF-8').'</span></a></li>';
				}					
			}

		}
		
		return $output;
	}
	
	// nkowald - 2009-10-28 - Added course search 
	function showCourseSearch() {
		$output = '
		<h2>Course Search</h2>
				<form method="get" action="/our_courses/course_search">
					<div class="clearfix">
						<label for="keyword" class="fl top_space">Enter your Keyword</label>
						<input type="text" name="keyword" id="keyword" value="" class="w190" />
					</div>
					<div class="clearfix">
						<label for="interest" class="fl top_space">What are your interests?</label>
						<select name="interest" id="interest" class="w195"><option label="Select a subject area" value="0" selected="selected">-- Select a subject area</option><option label="Applied Medical and Forensic Science" value="APMEDFRSCI">Applied Medical and Forensic Science</option><option label="Arts and Media" value="ARTSMEDIA">Arts and Media</option><option label="Business and Accounting" value="BUSIACCNTS">Business and Accounting</option><option label="Care and Health" value="CAREHEALTH">Care and Health</option><option label="Computing" value="COMPUTING">Computing</option><option label="Construction and the Built Environment" value="CONSTRBUI">Construction and the Built Environment</option><option label="English for Speakers of Other Languages" value="ESOL">English for Speakers of Other Languages</option><option label="English Maths and ICT" value="ENGMATHICT">English Maths and ICT</option><option label="Foreign Languages" value="FORGNLANG">Foreign Languages</option><option label="Hair and Beauty" value="HAIRBEAU">Hair and Beauty</option><option label="Not Applicable" value="N/A">Not Applicable</option><option label="Preparation for Uniformed Services" value="UNISERV">Preparation for Uniformed Services</option><option label="Sport and Fitness" value="SPORTFIT">Sport and Fitness</option><option label="Supported Learning" value="SUPPLEARN">Supported Learning</option><option label="Teacher Education" value="TEACHSUP">Teacher Education</option></select>
					</div>
					<div class="clearfix">
						<label for="location" class="fl top_space">Location you wish to study at</label>
						<select name="location" id="location" class="w195"><option value="">Select a location</option><option label="Tottenham Centre" value="tottenham">Tottenham Centre</option><option label="Enfield Centre" value="enfield">Enfield Centre</option></select>
					</div>
					<input type="image" src="/layout/img/go.gif" alt="Search" class="fl top_space" />
				</form>
		';
		return $output;
	} // showCourseSearch()

	// nkowald - 2009-11-02: Output random banner class
	/*
	function module_outputRandomClass() {
	
		$output = '';
		$class_colours = array(
			'contact_us',
			'for_learners',
			'our_courses',
			'for_employers',
			'news_events',
			'our_college'
		);
		
		// Select a colour randomly
		$rand = rand(0,(count($class_colours) - 1));
		$output = $class_colours[$rand];
		
		return $output;
		
	} //module_outputSubjectLinks
	*/
	
	// nkowald - 2009-11-02: Output random hero images
	function module_outputRandomHero() {
	
		$output = '';
		$hero_images = array(
			'hero1',
			'hero2',
			'hero4',
			'hero5',
			'hero6'
		);
		
		// Select a colour randomly
		$rand = rand(0,(count($hero_images) - 1));
		$output = $hero_images[$rand];
		
		return $output;
	} //module_outputSubjectLinks

	// nkowald - 2010-05-17: Pulling subject topic codes from the database
	function module_getSubjectInterests() {
		
		$db = new DB_Sql();
		$query = "SELECT * FROM tblsubject WHERE ID != 'AGRICULTUR' ORDER BY Description ASC";
		$rp = $db->query($query);

		$output = '';
		$topic = array();
		
		while($db->next_record(MYSQL_ASSOC)) {
			$k = $db->Record['ID'];
			$v = $db->Record['Description'];
			if ($k != 'N/A') {
				$topic[$k] = $v;
			}
		}
		
		// nkowald - 2012-04-20 - Add Apprenticeships value
		$output .= "\t<option value=\"\">Select a subject area</option>\n";
		$output .= "\t<option value=\"APPRENTICESHIPS\">Apprenticeships and Pre-Apprenticeships</option>\n";
		foreach($topic as $key => $value) {
			// nkowald - 2012-04-01 - Capitalise value
			$output .= "\t<option label=\"$value\" value=\"$key\">$value</option>\n";
		}
		
		return $output;
	}
	
	function module_getRandomQuotePic() {
		$output = '';
		$profile_alts = array(
			1 => array('name' => 'Aaron Kelly', 'quote' => 'IT Practitioner BTEC Level 3 - Aaron', 'url' => '/for_learners/success_stories/aaron_kelly'),
			2 => array('name' => 'Alex Neocleous', 'quote' => 'Pathway to Independent Living - Alex', 'url' => '/for_learners/success_stories/alex_neocleous'),
			3 => array('name' => 'Azad Ahmad', 'quote' => 'Airport Operations Level 2 - Azad', 'url' => '/for_learners/success_stories/azad_ahmad'),
			4 => array('name' => 'Carol Craig', 'quote' => 'Access to Higher Education Diplomoa - Carol', 'url' => '/for_learners/success_stories/carol_craig'),
			5 => array('name' => 'Cass-Shawn Kedroe', 'quote' => 'Public Services BTEC Level 2 - Cass-Shawn', 'url' => '/for_learners/success_stories/cass_shawn_kedroe'),
			6 => array('name' => 'Daisy Cabascango', 'quote' => 'Basic Construction Certificate - Daisy', 'url' => '/for_learners/success_stories/daisy_cabascango'),
			7 => array('name' => 'Daniel Zurawski', 'quote' => 'IT BTEC Diploma Level 3 - Daniel', 'url' => '/for_learners/success_stories/daniel_zurawski'),
			8 => array('name' => 'Daniela Cerri', 'quote' => 'Hairdressing Level 3 - Daniela', 'url' => '/for_learners/success_stories/daniela_cerri'),
			9 => array('name' => 'David Wood', 'quote' => 'Plumbing Apprenticeship Level 3 - David', 'url' => '/for_learners/success_stories/david_wood'),
			10 => array('name' => 'Donna Kavanagh', 'quote' => 'Diploma to teach in the Lifelong Sector (DTTLS) - Donna', 'url' => '/for_learners/success_stories/donna_kavanagh'),
			11 => array('name' => 'Fatma Ispir', 'quote' => 'Health and Social Care BTEC Level 2 - Fatma', 'url' => '/for_learners/success_stories/fatma_ispir'),
			12 => array('name' => 'Feysal Mohamed', 'quote' => 'Skills for Work and Life Entry 3 (Literacy and Numeracy) - Feysal', 'url' => '/for_learners/success_stories/feysal_mohamed'),
			13 => array('name' => 'Gokhan Bektas', 'quote' => 'ESOL Entry 2 - Gokhan', 'url' => '/for_learners/success_stories/gokhan_bektas'),
			14 => array('name' => 'Justina Subaciute', 'quote' => 'ESOL Skills for Study and Work Level 1 - Justina', 'url' => '/for_learners/success_stories/justina_subaciute'),
			15 => array('name' => 'Kyron Williams', 'quote' => 'Sports Development and Fitness BTEC Level 3 - Kyron', 'url' => '/for_learners/success_stories/kyron_williams'),
			16 => array('name' => 'Lauren Kyriacou', 'quote' => 'Business BTEC Level 3 - Lauren', 'url' => '/for_learners/success_stories/lauren_kyriacou'),
			17 => array('name' => 'Lewis Warner', 'quote' => 'Sports Development and Fitness BTEC Level 3 - Lewis', 'url' => '/for_learners/success_stories/lewis_warner'),
			18 => array('name' => 'Marlon Amaechi', 'quote' => 'Nursing - Access to Higher Education Diploma - Marlon', 'url' => '/for_learners/success_stories/marlon_amaechi'),
			19 => array('name' => 'Mina Abdurahman', 'quote' => 'Art and Design BTEC Diploma Level 3 - Mina', 'url' => '/for_learners/success_stories/mina_abdurahman'),
			20 => array('name' => 'Nura Mubarak', 'quote' => 'IT and Business BTEC Level 3 - Nura', 'url' => '/for_learners/success_stories/nura_mubarak'),
			21 => array('name' => 'Olga Obineche', 'quote' => 'Spanish Entry 3 - Olga', 'url' => '/for_learners/success_stories/olga_obineche'),
			22 => array('name' => 'Roman Barenga', 'quote' => 'Science BTEC Level 2 - Roman', 'url' => '/for_learners/success_stories/roman_barenga'),
			23 => array('name' => 'Sonny Kane', 'quote' => 'Access to Engineering - Sonny', 'url' => '/for_learners/success_stories/sonny_kane'),
			24 => array('name' => 'Stacy Byfield', 'quote' => 'English Level 1 - Stacy', 'url' => '/for_learners/success_stories/stacy_byfield'),
			25 => array('name' => 'Stephen Mafolabomi', 'quote' => 'Business BTEC Level 3 - Stephen', 'url' => '/for_learners/success_stories/stephen_mafolabomi')
		);
		
		// output a random number
		$no_profiles = count($profile_alts);
		$rand_num = rand(1, $no_profiles);
		
		$rand_quote = '<a href="http://www.conel.ac.uk/course-application/three-easy-steps.php"><img src="/layout/img/banner-apply-now-2012.gif" alt="Apply Now" width="363" height="136" /></a>';
		$rand_profile_pic = '<a href="'.$profile_alts[$rand_num]['url'].'"><img src="/layout/img/profile'.$rand_num.'.jpg" alt="'.$profile_alts[$rand_num]['name'].'" width="227" height="221" style="border:0;" /></a>';
		
		// nkowald - 2012-01-18 - Changing the image and link temporarily
		
		//$rand_profile_pic = '<a href="http://www.conel.ac.uk/arkacademyenfield"><img src="/layout/img/arkweb.jpg" alt="ARK Schools" width="227" height="221" style="border:0;" /></a>';
		
		return array('RANDOM_QUOTE'=>$rand_quote, 'RANDOM_PROFILE'=>$rand_profile_pic);
	}
	
	function module_setUpTubepress($id, $params) {
	
		$tubepress_base_url = 'http://www.conel.ac.uk/tubepress';
		include_once('tubepress/sys/classes/TubePressPro.class.php');
		
		$page_id = $params['page_id'];
		
		// Get video tag from page_id
		$db = new DB_Sql();
		$query = "SELECT * FROM webmatrix_extra_checkboxgroup WHERE page_id = $page_id";
		$db->query($query);

		$subject_code = '';
		while($db->next_record(MYSQL_ASSOC)) {
			$subject_code = $db->Record['text'];
		}
		
		$subject_code = ($subject_code == '') ? 'OTHER' : $subject_code;
		
		try {
			$video_gallery = TubePressPro::getHtmlForShortcode("resultsPerPage='16' playerLocation='shadowbox' mode='tag' tagValue='$subject_code' thumbWidth='150' thumbHeight='88' searchResultsRestrictedToUser='CONELwebmaster' filter_racy='true' hd='true' hqThumbs='true' embeddedWidth='640' embeddedHeight='390' ajaxPagination='true' uploaded='true' orderBy='published' relativeDates='true'");
		} catch (Exception $e) {
			$video_gallery = 'No videos found.';
			//$video_gallery = $e->getMessage(); // Official exception message (currently 'Zero videos found')
		}
		
		$head_code = TubePressPro::getHtmlForHead(false);
		
		return array('TP_HEAD_CODE' => $head_code, 'TP_GALLERY' => $video_gallery);
		
	}
	
	function module_getFactsOrMsg($id, $parameters) {

		$page_id = $parameters['page_id'];
		
		$db = new DB_Sql();
		$query = "SELECT text FROM webmatrix_page_content where page_id = $page_id AND identifier = 'IMPORTANT_MSG'";
		$rp = $db->query($query);

		$important_msg = '';
		while($db->next_record(MYSQL_ASSOC)) {
			$important_msg = $db->Record['text'];
		}
		
		if ($important_msg == '') {
			$facts_or_msg = '
			<h3 class="hidden">Fast Facts</h3>
                <ul id="fact_scroller">
                    <li>15 curriculum areas and state-of-the-art facilities</li>
                    <li>Learner support, careers guidance and welfare services</li>
                    <li>96% of students feel the College is a safe place to learn</li>
                    <li>Combine study and sport with our Football Academy</li>
                    <li>Extra-curricular trips abroad</li>
                    <li>Fantastic tutors, learning mentors and study support</li>
                    <li>Progression links with Birkbeck University of London</li>
                    <li>Progression links with Middlesex University </li>
                    <li>Progression links with Canterbury Christ Church University</li>
                    <li>Vast range of apprenticeships to choose from</li>
                </ul>';
		} else {
			$facts_or_msg = '<span>'.$important_msg.'</span>';
		}
		
		return $facts_or_msg;
		
	}
	
?>
