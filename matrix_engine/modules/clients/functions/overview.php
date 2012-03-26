<?php
## =======================================================================        
##  config.php        
## =======================================================================        
##  used to define all vars that need to be set when we install this onto
##  a new server- later we will search an replace the vbars to protect 
##  the code
##
##  TODO:   
##     - check if it works    
## =======================================================================
function displaySetup() {
	global $Auth, $gSession;
	
	
	## multiclient
	$client_id = $Auth->auth["client_id"];	
	
	## process the data entered
	$sort 		= isset($_GET['sort']) ? $_GET['sort'] : '';
	$direction 	= isset($_GET['direction']) ? intval($_GET['direction']) : '1';	
	$selected_group = isset($_GET['filter']) ? intval($_GET['filter']) : (isset($_POST['filter']) ? intval($_POST['filter']) : 0);
	
	if(!empty($sort)) {
		$_SESSION['sort'] = $sort;
	} else if(isset($_SESSION['sort'])) {
		$sort = $_SESSION['sort'];
	}
		

	if($direction != 1) {
		$_SESSION['direction'] = $direction;
 	} else if(isset($_SESSION['direction'])) {
		$direction = $_SESSION['direction'];
	}	
	
	if($selected_group != 0) {
		$_SESSION['filter'] = $selected_group;
	} else if(isset($_SESSION['filter'])) {
		$selected_group = $_SESSION['filter'];
	}	
	
	## we need to get all groups- check if we've found more then one- if yes we display the group tabs		
	$groups = clients_getFilters('visible');
	$group_count = count($groups);
		
	## find out which entry is to be hihglighted
	$highlight = -1;
	for($i=0; $i< $group_count; $i++) {
		if($groups[$i]['id'] == $selected_group) {
			$highlight = $i;
		}
	} 
	

	if($highlight == -1) {
		$selected_group = $groups[0]['id'];
		$highlight = 0;
	}

	## prepare the base URL- it's used for almost all url outputs of this function
	$baseURL = 'module.php?group='.$selected_group;
	$baseURL = $gSession->url($baseURL);
	
	## get the template for now
	$template2 = new Template(ENGINE."modules/clients/interface/");
	$template2->set_templatefile(array("portlets" => "interface.tpl","portlets_header" => "interface.tpl","portlets_row" => "interface.tpl","portlets_row_even" => "interface.tpl","portlets_foot" => "interface.tpl"));


	## get the template for now
	$template = new Template(ENGINE."modules/clients/interface/");
	$template->set_templatefile(array("head" => "interface.tpl","intro" => "interface.tpl","foot" => "interface.tpl"));

	$setup = $gSession->url('groups.php');
	$template->set_var('settingsURL',$setup);

	$tabs = '';	
	$tabsURL = 'module.php?';
	$tabsURL = $gSession->url($tabsURL);	
	if($group_count > 1) {
		## we need to otuput the groups			
		$counter = 0;
		foreach($groups as $current_group) { 
			if($counter + 1 >= $group_count) {
				$tabs .= ui_renderSectionTab($current_group['name'],$tabsURL.'&cmd=applyFilter&filter='.$current_group['id'],$counter,$highlight,false);
			} else {
				$tabs .= ui_renderSectionTab($current_group['name'],$tabsURL.'&cmd=applyFilter&filter='.$current_group['id'],$counter,$highlight,true);			
			}
			$counter++;
		}
		$template->set_var('GROUPTABS',$tabs);
	}
	

	$self = $gSession->url($_SERVER['PHP_SELF']);

	## header and intro element
	$template->set_var('language_inputhead',LANG_MODULE_CLIENTS_Title);
	$template->set_var('language_inputbody',LANG_MODULE_CLIENTS_Edit);
	
	$template->set_var('SearchTitle',LANG_MODULE_CLIENTS_SearchTitle);
	$template->set_var('lang',$Auth->auth["language"]);
	
	## this will be the new code base for the overview functionality.
	
	## first we get the controlfile data for this group
	$ctl_data = _getFieldsFromGroup(1);
	
	## the first step is to get the data required for any search we are doing
	$data = _prepareData($ctl_data);

	## we need to check if the user wants to search something
	$query = _prepareSearch(1,$data,$ctl_data,$sort,$direction);
	
	## now prepare the search panel
	$search_panel = _generateSearchPanel(1,$data);
	
	$template->set_vars($search_panel);
	
	$wt = new ctlparser(MATRIX_BASEDIR.'settings/modules/'.$GLOBALS['_MODULE_DATAOBJECTS_NAME'].'/base.xml');
	$wt->parse();	
	$elements = $wt->getElements();	


	$template->set_var("searchbutton","../../interface/lang/".$Auth->auth["language"]."_button_search.gif");
	$template->set_var("showallbutton","../../interface/lang/".$Auth->auth["language"]."_button_showall.gif");
	
	## specify the target of this page
	$template->set_var('actionURL',$baseURL);
	$template->set_var('resetURL',$baseURL.'&clear_session=1');
	
	## let's set the command
	$output .= '<input type="hidden" name="Session" value="'.$gSession->id.'">';
	$output .= '<input type="hidden" name="cmd" value="">';
	$template->set_var("hiddenfields",$output);
		
	## prepare the javascript link
	$header = '<script src="interface/scripts/portlets.js" type="text/javascript" language="javascript"></script>';
	$template->set_var("HEADER",$header);
	
	$template2->set_var('SEARCHURL',$baseURL);
	
	$template->pfill_block("head");
	$template->pfill_block("intro");

	
	$items_perpage = 25;
	## prepare the paging
	$pager_current_page = isset($_GET['ID']) ? $_GET['ID'] : 1;
	
	if(isset($_GET['ID'])) {
		$_SESSION['id'] = $_GET['ID'];
	} else if(isset($_SESSION['id'])) {
		$pager_current_page = $_SESSION['id'];
		$_GET['ID'] = $_SESSION['id'];
	}

	
	## and the offset
	$pager_offset = ($pager_current_page-1) * $items_perpage;

	## add the query top the query string
	$_SERVER['QUERY_STRING'] = $_SERVER['QUERY_STRING'].'&direction='.$direction.'&filter='.$selected_group.'&sort='.$sort.'&Session='.$gSession->id;
	
	## here we actually search for the clients using the queries inputed
	$searchresult_clients = clients_SearchClients($query,$pager_offset,$items_perpage);

	$template2->set_var('SearchResultTitle',LANG_MODULE_CLIENTS_SearchResultTitle);
	
	$maxcount = $searchresult_clients['count'];
	$searchresult_clients = $searchresult_clients['data'];

	$template2->set_var('SearchResultTitleCount',LANG_MODULE_CLIENTS_SearchResultTitleCount);
	$template2->set_var('COUNT',$maxcount);
	## prepare the pager
	$pager = generatePager($maxcount,$items_perpage,$pager_current_page);
	$template2->set_var('pager',$pager['pager']);

	## we need to preapre the action links for the portlets ui
	$addlinkURL = $baseURL."&cmd=create";
	$template2->set_var('addlinkURL',$addlinkURL);
	
	$deleteURL = $baseURL."&cmd=delete";
	$template2->set_var('deleteURL',$deleteURL);	

	$exportURL = $baseURL."&cmd=export";
	$template2->set_var('exportURL',$exportURL);
	
	$importURL = $baseURL."&cmd=import&group=".$selected_group;
	$template2->set_var('importURL',$importURL);	

	$filterURL = $baseURL."&cmd=filter";
	$template2->set_var('filterURL',$filterURL);	
		
	## output the header
	$template2->pfill_block("portlets");		
		

	$editURL = $baseURL."&cmd=edit";	

	## here we should define which fields we want to display-
	## this will be done via the ctl file. 
	## we then need to create a fancy query that only returns the required fields
	## for the search results page
	## get the group info

	## first we initalize the vars
	for($i = $items; $i < 4; $i++) {
		$template2->set_var('cellcolor'.$i,'#DDDDDD');
		$template2->set_var('header'.$i,'');
		$template2->set_var('sortURL'.$i,'#');
		$template2->set_var('cellicon'.$i,'blank.gif');
	}	

	## get all entries
	$newelements = $wt->getSimplifiedElements();		
	## output the header-elements
	$items = 1;
	foreach($newelements as $key => $value) {
		if(isset($value['OVERVIEW']) && $value['OVERVIEW'] == true) {
			## prepare the identifier
			$identifier = strtolower($value['IDENTIFIER']);
			
			if($sort == '') {
				$sort = $identifier;
			}
			
			## set the highlight
			if($sort == $identifier) {
				if($direction == 0) {
					$cellicon = 'sort_up.gif';
				} else {
					$cellicon = 'sort_down.gif';
				}
				$cellcolor = '#999999';
			} else {
				$cellicon = 'blank.gif';
				$cellcolor = '#DDDDDD';
			}
			
			$template2->set_var('cellicon'.$items,$cellicon);
			$template2->set_var('cellcolor'.$items,$cellcolor);
			$template2->set_var('header'.$items,$value['NAME']);
			$template2->set_var('sortURL'.$items,$baseURL.'&sort='.$identifier.'&direction='.($direction^1));
			$items++;
		}
	}
	
	if($items < 4) {
		for($i = $items; $i < 4; $i++) {
			$template2->set_var('cellcolor'.$i,'#DDDDDD');
			$template2->set_var('header'.$i,'');
			$template2->set_var('sortURL'.$i,'#');
		}
	}
	
	$template2->pfill_block("portlets_header");	
	
	$counter = 0;		

	foreach($searchresult_clients as $current_client) {		
		$items = 0;
		foreach($current_client as $key => $value) {
			$template2->set_var('element'.$items,htmlentities($value));
			$items++;
		}
					
		$template2->set_var('editURL',$editURL.'&client='.$current_client["id"]);
		$template2->set_var('identifier_id',$counter);
		$template2->set_var('client_id',$current_client["id"]);	
		
		if($counter % 2 == 1) {
			$template2->pfill_block("portlets_row");
		} else {
			$template2->pfill_block("portlets_row_even");
		}
		
		$template2->reset_vars();
		$counter++;

	}
	echo '</table>';
	
	$template->pfill_block("foot");
}

## =======================================================================        
## _setupSearchElements       
## =======================================================================        
##  returns all clients   
##
##  TODO:
##       - allow to limit the results in order to implement paging
## =======================================================================        
function _setupSearchElements($group,$data) {
	$interface_elements = array();
	
	$fields = _getFieldsFromGroup($group);

	## we need to handle each search element seperately
	## in order to be able to set the values corretly
	$fieldSelector = array();
	$inputSelector = array();
	$inputElement = array();
	$selectedInputElement = array();
	for($i=1; $i<= 3; $i++) {
		## loop through all fields
		foreach($fields as $current_element) {
			## here we start calling all our attribute types
			$type = strtolower($current_element['TYPE']);
			
			## first we try to include the apropriate file 
			include_once(ENGINE."modules/clients/attributetypes/".$type."/attribute.php");

			## now we check if the function exists
			if(function_exists("clients_".$type."_setupSearch")) {
				## no we call the function
				eval("\$element = clients_".$type."_setupSearch(\$current_element,\$data,\$i);");

				## gather all elements for later processing
				$fieldSelector[$i] .= $element['fieldSelector'];
				$inputSelector[$i] .= str_replace('#',$i,$element['inputSelector']);
				$inputElement[$i] .= str_replace('#',$i,$element['inputElement']);
			}							
		}
	}

	return array('fieldSelector'=>$fieldSelector,'inputSelector'=>$inputSelector,'inputElement'=>$inputElement);

}

## =======================================================================        
## _prepareData       
## =======================================================================        
##  we need to prepare a query which returns all required fields of all
##  rows that match the specified criteria sorted be the selected field
##
##  we need to combined the sources, search, sort and the fields from the
##  control file
## =======================================================================        
function _prepareData($fields,$data=null) {	
	## check if we need to flush all data
	$clear_session = isset($_GET['clear_session']) ? true :  false;
	if($clear_session) {
		unset($_SESSION['data']);
		return null;
	}

	## we need to check if the user posted data
	if(!empty($_POST)) {
		## okay we should update the session
		$data = array();
	
		## loop through all search panels
		for($i=1; $i <= 3; $i++) {
			if(isset($_POST['search'.$i]) && $_POST['search'.$i] != -1)	{
				## now get the correct datatype- and call it's storage function
				$current_element = strtolower($_POST['search'.$i]);
				$current_element = $fields[$current_element];
				
				$type = strtolower($current_element['TYPE']);	

				@include_once(ENGINE."modules/clients/attributetypes/".$type."/attribute.php");
				if(function_exists("clients_".$type."_getSearchQuery")) {				
					## no we call the function
					eval("\$data[$i] = clients_".$type."_storeSearchData($i,\$current_element);");
				}				
			}
		}
		
		## finally store the data in the session
		$_SESSION['data'] = $data;
	}
	
	return $_SESSION['data'];
}



## =======================================================================        
## _prepareSearch       
## =======================================================================        
##  we need to prepare a query which returns all required fields of all
##  rows that match the specified criteria sorted be the selected field
##
##  we need to combined the sources, search, sort and the fields from the
##  control file
## =======================================================================        
function _prepareSearch($group,$data,$fields,$sort,$direction) {
	global $Auth;
	$query_parts = '';

	## here we loop through the search fields 
	if(is_array($data)) {
		foreach($data as $search_id => $current_search) {
			$identifier = $current_search['identifier'];
			## check if we have a field that matches that criteria
			if(isset($fields[$identifier])) {
				$type = strtolower($fields[$identifier]['TYPE']);	
				
				
				@include_once(ENGINE."modules/clients/attributetypes/".$type."/attribute.php");
				if(function_exists("clients_".$type."_getSearchQuery")) {
					
					## no we call the function
					eval("\$query_parts[] = clients_".$type."_getSearchQuery(\$fields[\$identifier],\$current_search);");
				}
			}
		}
	}
	
	## loop through all fields
	foreach($fields as $current_element) {			
		## no matter what happends we need to get the searchfields
		if(isset($current_element['OVERVIEW']) && $current_element['OVERVIEW'] == 'true') {
			## first we try to include the appropriate file 
			$type = strtolower($current_element['TYPE']);
			
			@include_once(ENGINE."modules/clients/attributetypes/".$type."/attribute.php");				
			if(function_exists("clients_".$type."_getSearchFields")) {
				## now we call the function	
				eval("\$field_parts[] = clients_".$type."_getSearchFields(\$current_element,0);");

			}	
		}			
	}

	$sort_section = '';
	$column_selection = '';
	$table_selection = '';
	$partA= '';
	$partB= '';
	$partC= '';
	if(is_array($query_parts)) {
		## okay first we join the parts together		
		foreach($query_parts as $current_element) {
			## check if the a part already exists
			$partA .= $current_element['partA'];
			$partB .= $current_element['partB'];
			$partC .= $current_element['partC'];
		}
	}
	
	if(is_array($field_parts)) {
		foreach($field_parts as $current_part) {
			if($current_part['column'] != '') {
				$column_selection .= $current_part['column'];
				
				## now do the sorting stuff
				if($sort == '') {
					$sort = $current_part['identifier'];
				}				
				if($current_part['identifier'] == $sort) {
					$sort_section = $current_part['sort'];
				}
			}
		}
	}
	
	if($sort_section != '') {
		$directions = ($direction == 1) ? ' ASC' : ' DESC';
	
		$sort_section = 'ORDER BY '.$sort_section.$directions;
	}
	
	## let's try to make a usefull search
	$query = "SELECT ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].".id".$column_selection." FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." ".$partA." WHERE groupid=".$group." ".' '.$partB." ".$partC." GROUP BY ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX'].".id ".$sort_section;

	return $query;

}

## =======================================================================        
## _generateSearchPanel      
## =======================================================================        
##  returns all clients   
##
##  TODO:
##       - allow to limit the results in order to implement paging
## =======================================================================        
function _generateSearchPanel($group,$data) {
	## init the vars
	$return_value = array();

	## here is what we do:
	## we open up the control file- we call each attributetype and recieve the required information back
	$interface_elements = _setupSearchElements($group,$data);

	## this should be done by the attributes themselves- becaus they need to handle the search 
	for($i=1; $i<= 3; $i++) {
		if($_POST['search'.$i] == '-1') {
			$fields[$i] .= '<option label="Not specified" value="-1" selected>Not specified</option>';
		} else {
			$fields[$i] .= '<option label="Not specified" value="-1">Not specified</option>';
		}
	}	
	
	## set the input fields
	foreach($_POST as $key=>$value) {
		$return_value[$key] = $value;
	}	
	
	## try to setup values from a filter
	if(is_array($data)) {
		foreach($data as $key => $current_element) {
			$return_value['search_value'.$key.'_standard'] = $current_element['value'];
		}
	}
	
	## prepare the search options
	$search_field = array('contains'=>'1','equals'=>'2','does not equal'=>'3');

	## add the custom fields:
	for($i = 1; $i<=3; $i++) {
		## we need to determine the visibility of each element
		if(isset($data[$i])) {
			$return_value["VISIBLE_".$i] = '';
		} else {
			$return_value["VISIBLE_".$i] = 'style="display:none;"';
		}
		
		if(isset($data[$i]['datatype'])) {
			$return_value["inputElementHighlight".$i] = str_replace('#',$i,$data[$i]['search_element']);
			$return_value["STANDARD_INPUT_".$i] = 'style="display:none;"';
		} else {
			$return_value["inputElementHighlight".$i] = str_replace('#',$i,'row#_standard');
		}

		$operator = '';
		foreach($search_field as $key => $value) {
			if($data[$i]['operator'] == $value) {
				$operator .= '<option label="'.$key.'" value="'.$value.'" selected>'.$key.'</option>';
			} else {
				$operator .= '<option label="'.$key.'" value="'.$value.'">'.$key.'</option>';
			}
		}
		
		$return_value["operator".$i] = $operator;
		$return_value["inputFields".$i] = $fields[$i].$interface_elements['fieldSelector'][$i];
		$return_value["inputSelector".$i] = $interface_elements['inputSelector'][$i];
		$return_value["inputElements".$i] = $interface_elements['inputElement'][$i];
	}
	
	## we need to generate the search parameters
	

	
	return $return_value;
}



## =======================================================================        
##  _getFieldsFromGroup        
## =======================================================================        
##  returns all clients   
##
##  TODO:
##       - allow to limit the results in order to implement paging
## =======================================================================        
function _getFieldsFromGroup($group) {	
	$wt = new ctlparser(MATRIX_BASEDIR.'settings/modules/'.$GLOBALS['_MODULE_DATAOBJECTS_NAME'].'/base.xml');
	$wt->parse();	
	$elements = $wt->getElements();	
	
	## now process the fields- and prepare them for later use
	$fields = array();
	
	## now loop through all ctl-Elements
	foreach($elements as $current_row) {
		## process the rows- first we need to find out how many entries
		## are in this row 
		
		foreach($current_row as $current_element) {
			if(is_array($current_element)) {
				## here we start calling all our attribute types
				$type = strtolower($current_element['TYPE']);
				$identifier = strtolower($current_element['IDENTIFIER']);
				
				$fields[$identifier] = $current_element;
			}
		}
	}	

	## return an array with all fields for this group
	return $fields;	
}


## =======================================================================        
##  clients_getClients        
## =======================================================================        
##  returns all clients   
##
##  TODO:
##       - allow to limit the results in order to implement paging
## =======================================================================        
function clients_SearchClients($query,$offset,$items_perpage) {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];

	## prepare the db-object
	$db_connection = new DB_Sql();
	$rp 	= $db_connection->query($query);
	$max_entries = $db_connection->num_rows();
	
	## then get the correct values
	$query 	= $query." LIMIT $offset,$items_perpage";
	$result = $db_connection->query($query);

	$counter = 0;
	$return_value = array();
	while($db_connection->next_record()) {
		foreach($db_connection->Record as $key => $val) {
			if(intval($key) !== $key) {
				$return_value[$counter][$key] = stripslashes($val);
			}
		}
		$counter++;
	}
	return array('count'=>$max_entries,'data'=>$return_value);	
}

function generatePager($totalItems, $itemsPerPage,$current_page) {
	
	## init the vars
	$return_value = array();

	## setup the pager object
	$params['totalItems'] = $totalItems;
	$params['perPage'] = $itemsPerPage;
	$params['currentPage'] = $current_page;
	$params['lastPagePre'] = '';
	$params['lastPagePost'] = '';
	$params['lastPageText'] = ' ';
	
	$params['firstPagePre'] = '';
	$params['firstPagePost'] = '';
	$params['firstPageText'] = ' ';
	
	$params['separator'] = '|';
	$params['prevImg'] = '<<';
	$params['nextImg'] = '>>';
	
	$params['urlVar'] = 'ID';
	
	
	## create the object
	$pager = new Html_pager($params);
	
	## get the required info
	$links = $pager->getLinks();
	$limits = $pager->getPageItems();	
	
	$return_value['pager'] = $links['all'];
	$return_value['limits'] = $limits;
	

	return $return_value;
}

?>