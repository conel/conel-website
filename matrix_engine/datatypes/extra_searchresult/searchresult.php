<?PHP
## ======================================================================= 
##   webmatrix                                                     
## ======================================================================= 
##   Copyright (c) 2001, 2002, 2003 Stefan Elsner, Workmatrix        
## ======================================================================= 
##   <info@workmatrix.de> 
##
## =======================================================================        

## =======================================================================        
## searchresult_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function searchresult_displayInput($xmldata, $data) {
	return "";
}


## =======================================================================        
##  searchresult_storeData        
## =======================================================================        
## save the content in the db
## ======================================================================= 
function searchresult_storeData($page_id, $identifier) {
	return false;
}

## =======================================================================        
##  searchresult_getData        
## =======================================================================        
##  get the Data
## ======================================================================= 
function searchresult_getData($vPageID,&$page_record) {
}

## =======================================================================        
##  searchresult_getMultiData       
## =======================================================================        
##  get the Data for multiple pages
## ======================================================================= 
function searchresult_getMultiData($vItems) {
}

## =======================================================================        
##  searchresult_deleteData        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function searchresult_deleteData($vPageID) {
}

## =======================================================================        
##  output_text        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function searchresult_output($item,$structure,&$layout_template,$menu_id,$page_id) {
	global $Auth;

	##echo $structure['LIMIT'];
	## now open the template- specified in the xml-file
	$basename = $structure['TEMPLATE'];
	$filename = $basename.'.tpl';
	$xmlFile  = $basename.'.xml';	

	$xmlStructure = new xmlparser(HTML_DIR.$xmlFile);
	$xmlStructure->parse();
	## parse the template file
	$objects 		= $xmlStructure->getObjects();
	$xmlStructure 	= $xmlStructure->getElements();	


	$searchresult_template = new Template(HTML_DIR);
	$searchresult_template->set_templatefile(array("empty" => $filename,"body" => $filename)); 

	
	## current_page
	$this_page = $page_id;
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## we need the query form the user- otherwise output the empty part of the template
	$query = "";
	
	if(empty($_POST["query"]) || !isset($_POST["query"])) {
		## check if the get parameters are set
		if(!isset($_GET["query"])) {
			## then output the empty bit of the search
			
			$return_value = array();
			if($searchresult_template->block_items['empty']) {
				$output= $searchresult_template->fill_block("empty");
				$return_value['CONTENT'] = $output;
			}
			$return_value['matrix:QUERY'] = $query;
			$layout_template->set_var('SEARCHRESULT.query',$query);
			return $return_value;
		}
	}
	
	$query = isset($_POST["query"]) ? $_POST["query"] : (isset($_GET["query"]) ? $_GET["query"] : '');

	## if we are still here- we can savely proceed- we need to check the string before though...
	##$query = convert_general();
	
	## Strip non-alpha & non-numeric: 
	$query = ereg_replace("[^[:alnum:] ]+[^[-]]", "", $query); 

	
	##$query = stripslashes($_POST["query"]);
	##$query = str_replace('\'','', $query);
	##$query = str_replace('"','', $query);
	##$query = str_replace('*','', $query);
	$results = search($query);
	
	$db = new DB_Sql();
	
	$found_pages = array();			## used to store the found pages
	## we've got the results, but they are in a raw format
	foreach($results as $current_entry) {
		## now loop through the results, and check the type
		if($current_entry['type']=='page') {
			## this means everything is fine, we need to check if this page is currently visible 
			$pageInfo = structure_getStructureID($current_entry['page_id']);
			if (!checkFlag($pageInfo["flags"],PAGE_INVISIBLE) && checkFlag($pageInfo["flags"],PAGE_ACTIVE)) {
				## check if we alrady stored this page			
				## first check if the page_id id is aleady within the found pages
				if(!in_array($current_entry['page_id'],$found_pages)) {
					$found_pages[] = $current_entry['page_id'];
				}
			}
		} else {
			## if it is a box, we need get the host- and check if the host is active
			## using this query we get: the page id that hosts the content element. 
			$s_query = "SELECT C.page_id,template FROM ".DB_PREFIX."box_item AS A, ".DB_PREFIX."box AS B, ".USER_PAGES." AS C WHERE B.page_id=C.page_id AND A.box_id=B.box_id AND target='".$current_entry['page_id']."' AND A.client_id='$client_id' LIMIT 1";
			$result = $db->query($s_query);
			
			if($db->next_record()) {	
				## check if the page is active
				$pageInfo = structure_getStructureID($db->Record['page_id']);
				if (!checkFlag($pageInfo["flags"],PAGE_INVISIBLE) && checkFlag($pageInfo["flags"],PAGE_ACTIVE)) {	
									
					## now check if we already have this page
					if(!in_array($db->Record['page_id'],$found_pages)) {
						$found_pages[] = $db->Record['page_id'];
						$results[$db->Record['page_id']]['template'] = $db->Record['template'];
						$results[$db->Record['page_id']]['name'] = $pageInfo['text'];
					}				
				}				
			}
		}
	}	

	## rem ove all unwanted pages
	$event_templates = array(65,94,105,50,34);
	$found_pages = filterWithoutTemplate($event_templates,$found_pages,$results);

	## we have prepared to lists ) found_pages- which cotnains a list of page_ids to be processed- and b results- 
	## an array of information abou the found items. 
	
	## now we can futher filter them- this is done by getting an 
	## array containig the pages we woulod like to have- this
	## array is matched against the found_pages array-> as a result
	## we will get a subset of pages that are in both
	/*
	$filter = isset($_POST['filter']) ? intval($_POST['filter']) : intval($_GET['filter']);	
	
	## here we have an array of parent pages- associated with their filter id
	$parent_filter = array(2=>23,3=>25,4=>24);
	$event_templates = array(33,34,35,36,6);
	$shop_templates = array(15);
	
	switch($filter) {
		case 2:
		case 3:
		case 4:
			## okay these are the root entries
			$found_pages = filterByBranch($parent_filter[$filter],$found_pages);
			break;
		case 5:
			## now we need to filter globally by the events templates
			$found_pages = filterByTemplate($event_templates,$found_pages,$results);
			break;
		case 6:
			## now we need to filter globally by the events templates
			$found_pages = filterByTemplate($shop_templates,$found_pages,$results);
			break;	
		case 7:
			## okay now we have to combine the two queries
			$found_pages = filterByTemplate($event_templates,$found_pages,$results);
			break;
		case 8:
			## okay now we have to combine the two queries
			$found_pages = filterByBranch($parent_filter[3],$found_pages);
			$found_pages = filterByTemplate($event_templates,$found_pages,$results);	
			break;
		case 9:
			## okay now we have to combine the two queries
			$found_pages = filterByBranch($parent_filter[4],$found_pages);
			$found_pages = filterByTemplate($event_templates,$found_pages,$results);
			break;
		default:
			## we won't filter anything
		}
		
	*/
	## okay now we have a set of page_ids which we wil process in order to remove
	## duplicates and get the box-pages hosts
	
	## check the pager functionality
	$current_page = isset($_GET['offset']) ? intval($_GET['offset']) : 1;

	$start = (($current_page-1) * $structure['LIMIT']); 
	$end = (($current_page) * $structure['LIMIT'])+1; 
	
	$end = min(count($found_pages),$end);

	if($start < 0) {
		$start = 0;
	}
	
	if($end == 1) {
		$end = $structure['LIMIT']+1;
	}
		
		
	$displayed_pages = array();
	$results_counter = 1;

	for($i=$start ;$i<$end; $i++) {
		if($found_pages[$i]) {
			$page_data = array();
			## get the page 
			$page_data = _page_getPage($found_pages[$i],$objects);	

			## output the page
			$search_results_counter =0;
			
			while($search_results_counter < count($xmlStructure)-1) {											
				## okay first we try to find out what type we have
				## we wrap this up in a switch statemnt- this way we can
				## extend it more easily
				
				$s_element_type = $xmlStructure[$search_results_counter]['TYPE'];
				$s_element_name = $xmlStructure[$search_results_counter]['NAME'];
			   
				switch($s_element_type) {
					case 'TEXT':
					case 'COPYTEXT':
					case 'DATE': 
					case 'LINK' :
					case 'FILE':
					case 'BOX':
					case 'LINKLIST':
					case 'IMAGE': {
						## basically we need to call the function output_"element_type"
						## and the output the results to the template
						$s_target = strtolower($s_element_type); 
						if(isset($page_data[$s_element_name])) {
							
							eval("\$s_element = output_".$s_target."(\$page_data[\$s_element_name],\$xmlStructure[\$search_results_counter],0,".$found_pages[$i].");");	
							if(is_array($s_element)) {
								$searchresult_template->set_vars($s_element);
							} else {
								$searchresult_template->set_var($s_element_name,$s_element);
							}
							
						}
						break;
					}
					case 'INCLUDE': {
						## basically we need to call the function output_"element_type"
						## and the output the results to the template
						$s_target = strtolower($s_element_type); 
						@eval("\$s_element = output_".$s_target."(\$page_data[\$s_element_name],\$xmlStructure[\$search_results_counter],0,".$found_pages[$i].");");	
						if(is_array($s_element)) {
							$searchresult_template->set_vars($s_element);
						} else {
							$searchresult_template->set_var($s_element_name,$s_element);
						}
						break;
					}	
					default: {
						## we need to check if we have a module for this datatype
						$s_target = strtolower($s_element_type);
						## first we try to include the apropriate file 
						@include_once("datatypes/extra_".$s_target."/".$s_target.".php");
						
						## now we check if the function exists
						if(function_exists($s_target."_output")) {
							
							## no we call the function
							## check if the page_record entry is defined
							## if not we need to pass the whole record
							if(isset($page_data[$s_element_name])) {
								eval("\$s_element = ".$s_target."_output(\$page_data[\$s_element_name],\$xmlStructure[\$search_results_counter],0,".$found_pages[$i].");");
							} else {
								eval("\$s_element = ".$s_target."_output(\$page_data,\$xmlStructure[\$search_results_counter],\$searchresult_template,0,".$found_pages[$i].");");
							}	
							if(is_array($s_element)) {
								$searchresult_template->set_vars($s_element);
							} else {
								$searchresult_template->set_var($s_element_name,$s_element);
							}					
						}
						break;
					}				
				}
				
				$search_results_counter++;
			}
	
			## finally we ouptut the link
			$page_id = $found_pages[$i];
			$targetURL = getTargetURL($page_id);	
			$searchresult_template->set_var('matrix:TARGETPAGE',$targetURL);
			$searchresult_template->set_var('matrix:SELF',$this_page);
			$searchresult_template->set_var('matrix:RESULTINDEX',$results_counter);
			$results_counter++;
			
			$searchresult_output.= $searchresult_template->fill_block("body");
			$searchresult_template->reset_vars();
		}
	}
	$return_value = array();
	if(count($found_pages) > 0 && isset($structure['LIMIT']) && ($structure['HIDEPAGEELEMENT'] == false || !isset($structure['HIDEPAGEELEMENT']))) {
		$_SERVER['QUERY_STRING'] .= '&query='.urlencode($query).'&filter='.$filter;
		$results_count = max(count($found_pages),1);
		$page_navigation = searchresult_Pageview_dspPageElement($current_page,$results_count,$structure['LIMIT'],$_GET["offset"], $structure['DELTA'],$structure['NAME']);
		##$layout_template->set_vars($page_navigation);
		$searchresult_template->set_vars($page_navigation);
		$return_value = $page_navigation;
		$return_value['SEARCHRESULT.NAVIGATION'] = '<div class="navigation">
			<h3 class="fl">Page '.$page_navigation['SEARCHRESULT.CurrentPage'].'</h3>
			<div class="fr" style="width:108px;">
			<a href="'.$page_navigation['SEARCHRESULT.previousURL'].'"><img src="layout/img/ico-left.gif" width="52" height="60" alt="Previous" /></a><span class="hdn">|</span>
			<a href="'.$page_navigation['SEARCHRESULT.nextURL'].'"><img src="layout/img/ico-right.gif" width="52" height="60" alt="Next" /></a><span class="hdn">|</span>
		</div>';
		/*
		$layout_template->set_var('SEARCHRESULT.NAVIGATION','<div class="navigation">
			<h3 class="fl">Page '.$page_navigation['SEARCHRESULT.CurrentPage'].'</h3>
			<div class="fr" style="width:108px;">
			<a href="'.$page_navigation['SEARCHRESULT.previousURL'].'"><img src="layout/img/ico-left.gif" width="52" height="60" alt="Previous" /></a><span class="hdn">|</span>
			<a href="'.$page_navigation['SEARCHRESULT.nextURL'].'"><img src="layout/img/ico-right.gif" width="52" height="60" alt="Next" /></a><span class="hdn">|</span>
		</div>');*/
	}	
	
	##$layout_template->set_var('SEARCHRESULT.query',$query);

	
	if($i==0) {
		if($searchresult_template->block_items['empty']) {
			$output= $searchresult_template->fill_block("empty");
			$return_value['CONTENT'] = $output;
		}
	} else {
		$return_value['CONTENT'] = $searchresult_output;		
	}
	$return_value['matrix:QUERY'] = $query;
	$return_value['SEARCHRESULT.query'] = $query;

	return $return_value;
}

## =======================================================================        
##  get_page        
## =======================================================================        
##  gets all info for a page, and stores the result into an array
##  which it will return for processing 
##
##	you need to supply a objects array- this way
##	we make sure that only the used objects are loaded  
##
##  @param 	int		$page_id 
##  @param 	array	$objects	list of objects to load 
##  @return	array 
##
##  TODO:
##       - check for a valid page id
## ======================================================================= 
function _page_getPage($page_id,$objects=NULL) {
	$page_record = array();				## holds all data
	if(is_array($objects)) {
		while (list ($key, $val) = each ($objects)) { 
			switch($key) {
				case 'COPYTEXT':
				case 'TEXT':
				case 'LINKLIST':
				case 'LISTVIEW':
				case 'IMAGE':
				case 'FILE':
				case 'BOX':
				case 'DATE':
				case 'INCLUDE':			
				case 'LINK': {
					## get the elements
					$target = strtolower($key);
					eval("\$element = ".$target."_getData(\$page_id,\$page_record);");	
					break;
				}		
				default: {
					## check if a module exists, if yes we load the data
					$target = strtolower($key);
					@include_once(ENGINE."datatypes/extra_".$target."/".$target.".php");

					## now we check if the function exists
					if(function_exists($target."_getData")) {
						
						eval("\$element = ".$target."_getData(\$page_id,\$page_record);");				
					}
					break;
				}
					
			}				
		}
	}	
	return $page_record;
}

## =======================================================================        
##  internalListview_dspPageElement   
## =======================================================================        
##  this function generates the page element
##
##  TODO: 
##     - create a abstract function- which is able to handle
##       different html styles
## ======================================================================= 
function searchresult_Pageview_dspPageElement($page_id,$totalItems, $itemsPerPage,$offset, $delta,$identifier) {
	##  calculate how many pages we have to render
		$nrOfPages = ceil($totalItems / $itemsPerPage);

	##  load the pager class
		require_once ENGINE.'classes/class_html_pager.php';

	##  Settings
	##	(there are more of'em in the class constructor, these are the most commonly used)
		$params['totalItems'] = $totalItems;
		$params['perPage'] = $itemsPerPage;
		$params['currentPage'] = $offset;
		$params['urlVar'] = 'offset';
		$params['delta'] = $delta;
		$params['prevImg'] = '';
		$params['nextImg'] = '';
		$pager = &new Html_pager($params);
	
	##  get the rendered page linklist
		$links = $pager->getLinks();
		
		## generate the array containing all elements returned
		$return = array();
		foreach($links as $key=>$val) {
			$return[$identifier.'.'.$key] = $val;
		}

	return $return;

}


## =======================================================================        
##  filterByBranch        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function filterByBranch($root_id,$found_pages) {
	## 2. try to filter them - if they belong to a certain branch of the website.
	$branch = structure_getBranch($root_id);
	$sub_set = array();
	
	foreach($found_pages as $current_page) {
		## check if the found page is in the branch
		if(in_array($current_page,$branch)) {
			$sub_set[] = $current_page;
		}
	}
	return $sub_set;
}

## =======================================================================        
##  filterByTemplate        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function filterByTemplate($templates,$found_pages,$results) {
	## filter by a certain templater or template list
	$sub_set = array();
	foreach($found_pages as $current_page) {
		## check the template
		if(in_array($results[$current_page]['template'],$templates)) { 
			$sub_set[] = $current_page;
		}
	}
	return $sub_set;
}

## =======================================================================        
##  filterWithoutTemplate        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function filterWithoutTemplate($templates,$found_pages,$results) {
	## filter by a certain templater or template list
	$sub_set = array();
	foreach($found_pages as $current_page) {
		## check the template
		
		if(!in_array($results[$current_page]['template'],$templates)) { 
			$sub_set[] = $current_page;
		}
	}
	return $sub_set;
}
?>
