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
## listview_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function listview_displayInput($xmldata, $data) {
	return "";
}


## =======================================================================        
##  listview_storeData        
## =======================================================================        
## save the content in the db
## ======================================================================= 
function listview_storeData($page_id, $identifier) {
	return false;
}

## =======================================================================        
##  listview_getData        
## =======================================================================        
##  get the Data
## ======================================================================= 
function listview_getData($vPageID,&$page_record) {
}

## =======================================================================        
##  listview_getMultiData       
## =======================================================================        
##  get the Data for multiple pages
## ======================================================================= 
function listview_getMultiData($vItems) {
}

## =======================================================================        
##  listview_deleteData        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function listview_deleteData($vPageID) {
}

## =======================================================================        
##  output_text        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function output_listview($item,$structure,&$layout_template,$menu_id,$page_id) {
	global $Auth;

	## current_page
	$current_page = $page_id;
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## define the vars
	$items = 0;
	$item_counter = 1;

	$storage = "";

	## get the template_id
	$db_connectionMain = new DB_Sql();
	$db_connectionInner = new DB_Sql();
		
	## the listview can be devided into three sections
	
	## data-collection
	
	## data-filter (paging, limit etc.)
	
	## output	
		
	###############################
	## DATA-COLLECTION 
	###############################
	
	## how we collect the data is determined through certain parameters passed to the script
	## currently two methods are defind

	## here we determine what we should search for
	if(isset($structure['FIELD'])) {
		_listview_getByField($page_id,$db_connectionMain,$structure['FIELD'],$structure);
	} else {
		_listview_getByCategory($page_id,$db_connectionMain,$structure);
	}
	
	###############################
	## DATA-FILTER
	###############################

					
	## if the item count is defined we will set it here- 
	## otherwise we will use all records
	if(isset($structure['COUNT'])) {
		$items = $structure['COUNT'];
		
		if($items < 1) {
			$items = 1;
		}		
	} else {
		$items   = $db_connectionMain->num_rows();
		if($items < 1) {
			$items = 1;
		}		
	}

	###############################
	## DATA-OUTPUT
	###############################
	if(isset($structure['TEMPLATE'])) {
		$basename = $structure['TEMPLATE'];
		$xmlFile  = $basename.".xml";
		$filename = $basename.".tpl";
			
		$container_template = new xmlparser(HTML_DIR.$xmlFile);
		$container_template->parse();
		## parse the template file and then loop through it
		$container_elements = $container_template->getElements();
		$objects = $container_template->getObjects();	
		$container_template = new Template(HTML_DIR);
		$container_template->set_templatefile(array("head" => $filename,"body" => $filename,"foot" => $filename,"empty" => $filename,"alternate" => $filename, "active" => $filename, "alternateactive" => $filename)); 
	}
	
	$db_connectionTemplate = new DB_Sql();

	$counter = 1;
	$total_counter = 0;
	while(($db_connectionMain->num_rows() >0) && $db_connectionMain->next_record() && ($item_counter <= $items)) {
		
		$page_id = $db_connectionMain->Record["page_id"];
		##echo $db_connectionMain->Record["template"];

		if(!isset($structure['TEMPLATE'])) {
			## we should get the template name for the current id
			$currentTemplate = $db_connectionMain->Record["template"];
			$select_query = "SELECT basename FROM ".PAGE_TEMPLATE." WHERE template_id='$currentTemplate' AND client_id='$client_id'";
			$result_pointer = $db_connectionTemplate->query($select_query);
			$db_connectionTemplate->next_record();
			
			$basename = $db_connectionTemplate->Record["basename"];
			$xmlFile  = $basename.".xml";
			$filename = $basename.".tpl";
			
			$container_template = new xmlparser(HTML_DIR.$xmlFile);
			$container_template->parse();
			## parse the template file and then loop through it
			$container_elements = $container_template->getElements();	
			$objects = $container_template->getObjects();
			
			$container_template = new Template(HTML_DIR);
			$container_template->set_templatefile(array("head" => $filename,"body" => $filename,"foot" => $filename,"empty" => $filename,"alternate" => $filename, "active" => $filename)); 
		}	
		## okay each page is a row we will ouptut... so first we get the text and set the var
		## and second we'll get the images and then we will output the vars			

		## okay basically what we do, is get all the info for this page:
		$page_record = page_getPage($page_id,$objects);

		$container_counter = 0;
		while($container_counter < count($container_elements)-1) {
			$container_element_name = $container_elements[$container_counter]['NAME'];
			$container_element_type = $container_elements[$container_counter]['TYPE'];
			
			switch($container_element_type) {
				case 'TEXT': 
				case 'COPYTEXT':
				case 'DATE': 
				case 'LINK':
				case 'FILE':
				case 'BOX':  
				case 'LINKLIST':  
				case 'IMAGE': {
					$target = strtolower($container_element_type);
					$function = "output_".$target;
					$element = $function($page_record[$container_element_name],$container_elements[$container_counter],$menu_id,$page_id);					
					if(is_array($element)) {
						$container_template->set_vars($element);
					} else {
						$container_template->set_var($container_element_name,$element);
					}
					break;
				}
				case 'INCLUDE': {
					$target = strtolower($container_element_type);
					$function = "output_".$target;
					$element = $function($page_record,$container_elements[$container_counter],$menu_id,$page_id);					
					if(is_array($element)) {
						$container_template->set_vars($element);
					} else {
						$container_template->set_var($container_element_name,$element);
					}
					break;
				}				
			case 'LISTVIEW': {
				$element = "";
				
				$element = output_listview($page_record,$container_elements[$container_counter],$container_template,$menu_id,$page_id); 
				$container_template->set_var($container_element_name,$element);	
				break;	
			}								
				
				case 'COPYTEXT': {
					$target = strtolower($container_element_type);
					$function = "output_".$target;
					$element = $function($page_record[$container_element_name],$container_elements[$container_counter],$menu_id);					
					$maxchar =  $container_elements[$container_counter]['MAXCHAR'];
					if($maxchar > 1) {
						$oldelement = $element;
						$element    = substr($element, 0, $maxchar);
						if($element != $oldelement) {
							$element .=" ...";
						} 
					}
					$container_template->set_var($container_element_name,$element);
					break;
				}
				default: {
					## we need to check if we have a module for this datatype
					$target = strtolower($container_element_type);
					
					## first we try to include the apropriate file 
					@include_once("datatypes/extra_".$target."/".$target.".php");
					
					## now we check if the function exists
					if(function_exists($target."_output")) {
						
						## no we call the function
						## check if the page_record entry is defined
						## if not we need to pass the whole record
						$function = $target."_output";
					
						if(isset($page_record[$container_element_name])) {
							$element = $function($page_record[$container_element_name],$container_elements[$container_counter],$menu_id);	
						} else {
							$element = $function($page_record,$container_elements[$container_counter],$layout_template,$menu_id,$page_id);	
						}	

						if(is_array($element)) {
							$container_template->set_vars($element);
						} else {
							$container_template->set_var($container_element_name,$element);
						}					
					}
					break;
				}
			}		
			$container_counter++;
		}
		##before we will flush this row, we will insert the global
		## TARGETPAGE... using this var the user can link the page to
		## the associated page
		$targetURL = getTargetURL($page_id);
		$container_template->set_var('matrix:TARGETPAGE',$targetURL);
		$container_template->set_var('matrix:PAGEID',$page_id);
		$container_template->set_var("matrix:COUNTER", $total_counter);
		$container_template->set_var("matrix:ID", $total_counter+1);
		$container_template->set_var('matrix:THISPAGE',$current_page);
		$container_template->set_var('matrix:THISPAGEURL',getTargetURL($current_page));
		$container_template->set_var("matrix:PAGENAME", $db_connectionMain->Record["structure_text"]);

		## her we shouldd check if we should ouptut different elements
		if($page_id != $current_page) {
			## okay we need to display a normal entry
			if($counter%2 && $container_template->block_items['alternate']) {
				$storage .= $container_template->fill_block("alternate");
			} else {
				$storage .= $container_template->fill_block("body");
			}
		} else {
			## this means we need to display the highlight
			if ($structure['HIGHLIGHT']) {
				if($counter%2 && $container_template->block_items['alternateactive']) {
					$storage .= $container_template->fill_block("alternateactive");
				} else if($container_template->block_items['active']) {
					$storage .= $container_template->fill_block("active");
				} else {
					$storage .= $container_template->fill_block("body");
				}
			} else {
				$storage .= $container_template->fill_block("body");
			}
		}
		$counter++;

		## okay if 
		if(isset($structure['COUNT'])) {
			$item_counter ++;
		}
		$total_counter++;
		$container_template->reset_vars();
	}

	if(is_object($container_template)) {
		if($total_counter>=1) {
			## set the total found entries
			$container_template->set_var('matrix:TOTALCOUNT',$total_counter);
			$container_template->set_var('matrix:THISPAGE',$current_page);
			
			$head = $container_template->fill_block("head");
			$foot = $container_template->fill_block("foot");	
			return $head.$storage.$foot;
		} else {
			$storage = $container_template->fill_block("empty");
			return $storage;
		}
	} else {
		return "";
	}

}

## =======================================================================        
##  _listview_getByField    
## =======================================================================        
##  this is an internal function for the listview function
##  it handles the queries for a Category request
##
##  TODO: 
##     - test if it works
## ======================================================================= 
function _listview_getByField($page_id,&$db,$field,$xmldata=null) {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];	

	if(isset($xmldata) && isset($xmldata['SCOPE'])) {
		switch($xmldata['SCOPE']) {
			case 'BRANCH':
				## 	we need to get all pages in our branch
				$select_query = "SELECT structure_id FROM ".STRUCTURE." WHERE page_id = '$page_id' AND ".STRUCTURE.".client_id=$client_id LIMIT 1";
				$result_pointer = $db->query($select_query);
				$db->next_record();
		
				## get the structure_id of the current page
				$structure_id = $db->Record["structure_id"];		
		
				## get all pages
				$items = structure_getBranch($structure_id);

				##if(count($items) > 0) {
					## prepare the query
					$query = join(',',$items);
					$additional_query = "AND A.page_id IN (".$query.")";
				##}
				break;
		}
	}
	

	## this should be changed we should support operators
	## equal,notequal, contains
	## each item needs to exploded- and added to the search query
	$query = '';
	
	$parameters = explode(",",$field);
	foreach($parameters as $current_parameter) {
		$new_query = '';
		
		## notequal
		$pos = strpos($current_parameter,'notequal');
		if($pos !== false) {
			$subparameter = explode('notequal',$current_parameter);
			$new_query .= "A.identifier='".trim($subparameter[0])."' AND A.text != '".trim($subparameter[1])."'";
		} else {
			## check if equal exists
			$pos = strpos($current_parameter,'equal');
			if($pos !== false) {
				$subparameter = explode('equal',$current_parameter);
				$new_query .= "A.identifier='".trim($subparameter[0])."' AND A.text = '".trim($subparameter[1])."'";
			}
		}
		## contains
		$pos = strpos($current_parameter,'contains');
		if($pos !== false) {
			$subparameter = explode('contains',$current_parameter);
			$new_query .= "A.identifier='".trim($subparameter[0])."' AND A.text LIKE '%".trim($subparameter[1])."%'";
		}
		
		if($new_query != '') {
			if($query !='') {
				$query .= ' AND '.$new_query;
			} else {
				$query .= $new_query;
			}
		}			
	}
	
	$select_query = "SELECT DISTINCT A.page_id FROM ".PAGE_CONTENT." AS A INNER JOIN ".STRUCTURE." AS B ON A.page_id=B.page_id WHERE ".$query." ".$additional_query." AND A.client_id ='".$client_id."' ORDER BY B.structure_order";
	$result_pointer = $db->query($select_query);
}


## =======================================================================        
##  _listview_getByCategory    
## =======================================================================        
##  this is an internal function for the listview function
##  it handles the queries for a Category request
##
##  TODO: 
##     - test if it works
## ======================================================================= 
function _listview_getByCategory($page_id,&$db,$xmldata) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];	

	$category 	= $xmldata['CATEGORY'];
	$scope		= isset($xmldata['SCOPE']) ? $xmldata['SCOPE']: "";
	$sortby		= isset($xmldata['SORTBY']) ? $xmldata['SORTBY']: "";					
	$sortorder	= isset($xmldata['ORDERDIRECTION']) ? $xmldata['ORDERDIRECTION']: "";
	$sort		= isset($xmldata['SORT']) ? $xmldata['SORT']." ".$sortorder.",": "";					## can have the following values:

	## set up the scope
	$scope 		    = strtoupper($scope);
					
	## handle multiple categories (these are templates of pages)
	$category_array = explode(",",$category);
	
	## get the template ids
	$template_query="";	
	foreach ($category_array as $stringitem) {
		if($template_query != "") {
			$template_query .= " OR ";
		} 
		$template_query .= "basename ='$stringitem'";
	}
		
	$select_query   = "SELECT template_id FROM ".PAGE_TEMPLATE." WHERE (".$template_query.") AND client_id=".$client_id;
	$result_pointer = $db->query($select_query);	


	## we need to check if we found multiple template ids,
	## if yes we need to prepare another query sting!
	if($db->num_rows() >= 1) {
		$template_query="";
		while($db->next_record()) {
			if($template_query != "") {
				$template_query .= " OR ";
			} 
			$template_id     = $db->Record["template_id"];
			$template_query .= USER_PAGES.".template ='$template_id'";
		}
	} else {
		$template_id         = $db->Record["template_id"];
		$template_query      = USER_PAGES.".template ='$template_id'";
	}	
	
	##  prepare the sorting
	if($sortorder!="ASC" && $sortorder!="DESC") {
		$sortorder = "";
	}
	
	## prepare the sort order
	if($sortby != "") {	
		## we implement two ways to sort- textual content or date content
		$sort_control = explode(":", $sortby);
		if(count($sort_control) > 1) {
			## okay for now we only implement DATE as an additional sort option
			$secondpart = "INNER JOIN ".PAGE_DATE." ON ".USER_PAGES.".page_id = ".PAGE_DATE.".page_id WHERE (".$template_query.") AND (structure_flag >= 2) AND identifier='".$sort_control[1]."' AND ".PAGE_DATE.".client_id='".$client_id."' ORDER BY $sort date ".$sortorder;
		} else {
			## we have a custom sort oder:
			$secondpart = "INNER JOIN ".PAGE_CONTENT." ON ".USER_PAGES.".page_id = ".PAGE_CONTENT.".page_id WHERE (".$template_query.") AND (structure_flag >= 2) AND identifier='".$sort_control[0]."' AND ".PAGE_CONTENT.".client_id='".$client_id."' ORDER BY $sort text ".$sortorder;
		}
	} else {
		$secondpart = "WHERE (".$template_query.") AND (structure_flag >= 2) AND ".USER_PAGES.".client_id=$client_id ORDER BY $sort structure_order ".$sortorder;
	}	
	
	## now get either all pages or 
	## only the submenu pages for this page_id
	if($scope == "GLOBAL") {		
		$select_query = "SELECT ".STRUCTURE.".page_id,structure_text, ".USER_PAGES.".template FROM ".STRUCTURE." INNER JOIN ".USER_PAGES." ON ".STRUCTURE.".page_id = ".USER_PAGES.".page_id ".$secondpart;					
		$result_pointer = $db->query($select_query);		
	} else if($scope == "BRANCH") {	

		## 	we need to get all pages in our branch
		$select_query = "SELECT structure_id, structure_text FROM ".STRUCTURE." WHERE page_id = '$page_id' AND ".STRUCTURE.".client_id=$client_id LIMIT 1";
		$result_pointer = $db->query($select_query);
		$db->next_record();
		## get the structure_id of the current page
		$structure_id = $db->Record["structure_id"];		
		
		## get all pages
		$items = structure_getBranch($structure_id);

		$query = "";
		## let's prepare a query filter	
		for($i=0;$i<count($items); $i++) {
			if($query != "") {
				$query .= " OR ";
			}
			$targetPage= $items[$i];
			$query .= STRUCTURE.".page_id ='$targetPage'";
		}
		if($query !="") {
			$select_query = "SELECT ".STRUCTURE.".page_id, structure_text, ".USER_PAGES.".template FROM ".STRUCTURE." INNER JOIN ".USER_PAGES." ON ".STRUCTURE.".page_id = ".USER_PAGES.".page_id WHERE (".$template_query.") AND (".$query.") AND (structure_flag >= 2) AND ".USER_PAGES.".client_id=$client_id ORDER BY $sort structure_order ".$sortorder;
			$result_pointer = $db->query($select_query);
		}
	} else if($scope == "LEVELUP") {	
		## 	we need to first get the parentppage of the currentpage- and then we will proceed normally
		$select_query = "SELECT B.structure_id,B.structure_parent,B.structure_text FROM ".STRUCTURE." AS A INNER JOIN ".STRUCTURE." AS B ON A.structure_parent=B.structure_id WHERE A.page_id = '$page_id' AND A.client_id=$client_id LIMIT 1";
		$result_pointer = $db->query($select_query);
		$db->next_record();
		## get the structure_id of the current page
		$structure_parent = $db->Record["structure_parent"];		

		$select_query = "SELECT page_id, structure_id,structure_text FROM ".STRUCTURE." WHERE structure_parent = '$structure_parent' AND ".STRUCTURE.".client_id=$client_id";
		$result_pointer = $db->query($select_query);

		$query = "";
		## let's prepare a query filter	
		while($db->next_record()) {
			if($query != "") {
				$query .= " OR ";
			}
			$targetPage= $db->Record["page_id"];	
			$query .= STRUCTURE.".page_id ='$targetPage'";
		}
			
		if($query !="") {
			$select_query = "SELECT ".STRUCTURE.".page_id,structure_text, ".USER_PAGES.".template FROM ".STRUCTURE." INNER JOIN ".USER_PAGES." ON ".STRUCTURE.".page_id = ".USER_PAGES.".page_id WHERE (".$template_query.") AND (".$query.") AND (structure_flag >= 2) AND ".USER_PAGES.".client_id=$client_id ORDER BY $sort structure_order ".$sortorder;
			$result_pointer = $db->query($select_query);
		}		
	} else if($scope == "LEVELUPBRANCH") {	
		## 	we need to first get the parentppage of the currentpage- and then we will proceed normally
		$select_query = "SELECT B.structure_id,B.structure_parent,B.structure_text FROM ".STRUCTURE." AS A INNER JOIN ".STRUCTURE." AS B ON A.structure_parent=B.structure_id WHERE A.page_id = '$page_id' AND A.client_id=$client_id LIMIT 1";
		$result_pointer = $db->query($select_query);
		$db->next_record();
		## get the structure_id of the current page
		$structure_parent = $db->Record["structure_id"];
		
		## get the page id for this page
		$pageInfo = structure_getPage($structure_parent);
		$page_id = $pageInfo['page_id'];

		## 	we need to get all pages in our branch
		$select_query = "SELECT structure_id, structure_text FROM ".STRUCTURE." WHERE page_id = '$page_id' AND ".STRUCTURE.".client_id=$client_id LIMIT 1";
		$result_pointer = $db->query($select_query);
		$db->next_record();
		## get the structure_id of the current page
		$structure_id = $db->Record["structure_id"];		
		
		## get all pages
		$items = structure_getBranch($structure_id);

		$query = "";
		## let's prepare a query filter	
		for($i=0;$i<count($items); $i++) {
			if($query != "") {
				$query .= " OR ";
			}
			$targetPage= $items[$i];
			$query .= STRUCTURE.".page_id ='$targetPage'";
		}
		if($query !="") {
			$select_query = "SELECT ".STRUCTURE.".page_id, structure_text, ".USER_PAGES.".template FROM ".STRUCTURE." INNER JOIN ".USER_PAGES." ON ".STRUCTURE.".page_id = ".USER_PAGES.".page_id WHERE (".$template_query.") AND (".$query.") AND (structure_flag >= 2) AND ".USER_PAGES.".client_id=$client_id ORDER BY $sort structure_order ".$sortorder;
			$result_pointer = $db->query($select_query);
		}
	
	} else if($scope == "LEVEL") {	
		## 	we need to get all pages in our branch
		$select_query = "SELECT structure_id,structure_parent,structure_text FROM ".STRUCTURE." WHERE page_id = '$page_id' AND ".STRUCTURE.".client_id=$client_id LIMIT 1";
		$result_pointer = $db->query($select_query);
		$db->next_record();
		## get the structure_id of the current page
		$structure_parent = $db->Record["structure_parent"];		

		$select_query = "SELECT page_id, structure_id,structure_text FROM ".STRUCTURE." WHERE structure_parent = '$structure_parent' AND ".STRUCTURE.".client_id=$client_id";
		$result_pointer = $db->query($select_query);

		$query = "";
		## let's prepare a query filter	
		while($db->next_record()) {
			if($query != "") {
				$query .= " OR ";
			}
			$targetPage= $db->Record["page_id"];	
			$query .= STRUCTURE.".page_id ='$targetPage'";
		}
			
		if($query !="") {
			$select_query = "SELECT ".STRUCTURE.".page_id,structure_text, ".USER_PAGES.".template FROM ".STRUCTURE." INNER JOIN ".USER_PAGES." ON ".STRUCTURE.".page_id = ".USER_PAGES.".page_id WHERE (".$template_query.") AND (".$query.") AND (structure_flag >= 2) AND ".USER_PAGES.".client_id=$client_id ORDER BY $sort structure_order ".$sortorder;
			$result_pointer = $db->query($select_query);
		}
	} else if($scope == "PAGE") {	
		## this means we should get the subpages of the page with the specified name
		
		if(isset($xmldata['PAGEID'])) {
			## 	we need to get all pages in our branch
			$select_query = "SELECT page_id,structure_id,structure_parent,structure_text FROM ".STRUCTURE." WHERE page_id = '".$xmldata['PAGEID']."' AND ".STRUCTURE.".client_id=$client_id LIMIT 1";
			$result_pointer = $db->query($select_query);
			$db->next_record();
		} else {
			$pagename = $xmldata['PAGENAME'];
			$select_query = "SELECT page_id,structure_id,structure_parent,structure_text FROM ".STRUCTURE." WHERE structure_text = '$pagename' AND ".STRUCTURE.".client_id=$client_id LIMIT 1";
			$result_pointer = $db->query($select_query);
			$db->next_record();
		}			

		## now we should get all pages that match the category criteria and lives below the current page
		$structure_parent = $db->Record["structure_id"];		

		$select_query = "SELECT page_id, structure_id,structure_text FROM ".STRUCTURE." WHERE structure_parent = '$structure_parent' AND ".STRUCTURE.".client_id=$client_id ";
		$result_pointer = $db->query($select_query);

		$query = "";
		## let's prepare a query filter	
		while($db->next_record()) {
			if($query != "") {
				$query .= " OR ";
			}
			$targetPage= $db->Record["page_id"];	
			$query .= STRUCTURE.".page_id ='$targetPage'";
		}
			
		if($query !="") {
			$select_query = "SELECT ".STRUCTURE.".page_id,structure_text, ".USER_PAGES.".template FROM ".STRUCTURE." INNER JOIN ".USER_PAGES." ON ".STRUCTURE.".page_id = ".USER_PAGES.".page_id WHERE (".$template_query.") AND (".$query.") AND (structure_flag >= 2) AND ".USER_PAGES.".client_id=$client_id ORDER BY $sort structure_order ".$sortorder;
			$result_pointer = $db->query($select_query);
		}	
	
	} else {	
		## first we will determine, what the current menu id is for this page
		$select_query = "SELECT structure_id, structure_text FROM ".STRUCTURE." WHERE page_id = '$page_id' LIMIT 1";
		$result_pointer = $db->query($select_query);
		$db->next_record();
		
		$structure_id = $db->Record["structure_id"];
		## okay now we will define the subpages
		if($sortby != "") {	
			## we implement two ways to sort- textual content or date content
			$sort_control = explode(":", $sortby);
			if(count($sort_control) > 1) {
				## okay for now we only implement DATE as an additional sort option
				$secondpart = "INNER JOIN ".PAGE_DATE." ON ".USER_PAGES.".page_id = ".PAGE_DATE.".page_id WHERE (".$template_query.") AND (structure_flag >= 2) AND identifier='".$sort_control[1]."' AND ".PAGE_DATE.".client_id='".$client_id."' AND structure_parent=".$structure_id." AND (structure_flag >= 2) ORDER BY $sort date ".$sortorder;
			}
		}
		
		if($sortby != "") {	
			## we have a custom sort oder:
			##$secondpart = "INNER JOIN ".PAGE_CONTENT." ON ".USER_PAGES.".page_id = ".PAGE_CONTENT.".page_id WHERE (".$template_query.") AND structure_parent=".$structure_id." AND (structure_flag >= 2) AND identifier='".$sortby."' AND ".USER_PAGES.".client_id=$client_id ORDER BY $sort text ".$sortorder;
			$select_query = "SELECT ".STRUCTURE.".page_id, structure_text, ".USER_PAGES.".template FROM ".STRUCTURE." INNER JOIN ".USER_PAGES." ON ".STRUCTURE.".page_id = ".USER_PAGES.".page_id ".$secondpart;
		} else {
			$select_query = "SELECT ".STRUCTURE.".page_id, structure_text, ".USER_PAGES.".template FROM ".STRUCTURE." INNER JOIN ".USER_PAGES." ON ".STRUCTURE.".page_id = ".USER_PAGES.".page_id WHERE (".$template_query.") AND structure_parent=".$structure_id." AND (structure_flag >= 2) AND ".USER_PAGES.".client_id=$client_id ORDER BY $sort structure_order ".$sortorder;
		}					

		$result_pointer = $db->query($select_query);
	}
}


?>
