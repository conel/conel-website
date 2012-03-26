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
## newsletter_listview_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function newsletter_listview_displayInput($xmldata, $data) {
	return "";
}


## =======================================================================        
##  newsletter_listview_storeData        
## =======================================================================        
## save the content in the db
## ======================================================================= 
function newsletter_listview_storeData($page_id, $identifier) {
	return false;
}

## =======================================================================        
##  newsletter_listview_getData        
## =======================================================================        
##  get the Data
## ======================================================================= 
function newsletter_listview_getData($vPageID,&$page_record) {
}

## =======================================================================        
##  newsletter_listview_getMultiData       
## =======================================================================        
##  get the Data for multiple pages
## ======================================================================= 
function newsletter_listview_getMultiData($vItems) {
}

## =======================================================================        
##  newsletter_listview_deleteData        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function newsletter_listview_deleteData($vPageID) {
}

## =======================================================================        
##  output_text        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function newsletter_listview_output($item,$structure,&$layout_template,$menu_id,$page_id) {
	global $Auth;

	## current_page
	$current_page = $page_id;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## define the vars
	$items = 0;
	$item_counter = 1;

	$storage = "";
			
	## okay so all we need is the var name and then we generate
	## the correct Form fields
	## now we should prepare the link for adding an element
	## the template var is called: addlinkURL
	## store the container
	
	## get the template_id
	$db_connectionMain = new DB_Sql();
	$db_connectionInner = new DB_Sql();
	##

	## here we determine what we should search for
	_newsletter_getSendNewsletters($db_connectionMain);

				
	## if the item count is defined we will set it here- 
	## otherwise we will use all records
	if(isset($structure['COUNT'])) {
		$items = $structure['COUNT'];
		$totalItems = $db_connectionMain->num_rows();
		
		if($items < 1) {
			$items = 1;
		}
		if (isset($_GET['offset'])) {
			$offset = $_GET['offset'];
			##  we want to display the page from its beginning, not the end
				if ($offset > 0) {
					$offset--;
				}
				$newcount = ($offset * $items);
			
			##  set the db pointer to the first entry of the current page
				$count = 0;
				while ($count < $newcount) {
					$db_connectionMain->next_record();
					$count++;
				}
			
		}
		
	} else {
		$items = $totalItems = $db_connectionMain->num_rows();
		if($items < 1) {
			$items = 1;
		}		
	}
	
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
		$container_template->set_templatefile(array("head" => $filename,"emptybody" => $filename,"rowstart" => $filename,"rowend_alternate" => $filename,"rowstart_alternate" => $filename,"rowend" => $filename,"body" => $filename,"foot" => $filename,"empty" => $filename,"active" => $filename,"alternate" => $filename)); 
	}
	
	$db_connectionTemplate = new DB_Sql();

	$counter = 1;
	$total_counter = 0;
	$row_items = 1;
	$rows = 1;
	while($db_connectionMain->next_record() && ($item_counter <= $items)) {

		$page_id = $db_connectionMain->Record["page_id"];
		$page_title = $db_connectionMain->Record["structure_text"];
		
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
			$container_template->set_templatefile(array("head" => $filename,"rowstart" => $filename,"rowend" => $filename,"emptybody" => $filename,"body" => $filename,"foot" => $filename,"empty" => $filename,"alternate" => $filename)); 
		}			

		## okay basically what we do, is get all the info for this page:
		$page_record = page_getPage($page_id,$objects);
	
		$container_counter = 0;
		while($container_counter < count($container_elements)-1) {
			$container_element_name = $container_elements[$container_counter]['NAME'];
			$container_element_type = $container_elements[$container_counter]['TYPE'];
			
			switch($container_element_type) {
				case 'INCLUDE' :
				case 'TEXT': 
				case 'DATE': 
				case 'LINK':
				case 'BOX': 
				case 'FILE': 
				case 'IMAGE': {
					$target = strtolower($container_element_type);
					eval("\$element = output_".$target."(\$page_record[\$container_element_name],\$container_elements[\$container_counter],$menu_id);");
					if(is_array($element)) {
						$container_template->set_vars($element);
					} else {
						$container_template->set_var($container_element_name,$element);
					}
					break;
				}							
				case 'COPYTEXT': {
					$target = strtolower($container_element_type);
					eval("\$element = output_".$target."(\$page_record[\$container_element_name],\$container_elements[\$container_counter],$menu_id);");
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
						eval("\$element = ".$target."_output(\$page_record[\$container_element_name],\$container_elements[\$container_counter],$menu_id);");
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
		$container_template->set_var('matrix:PAGENAME',$page_title);
		$container_template->set_var('matrix:PAGEID',$page_id);
		$container_template->set_var("matrix:COUNTER", $total_counter);
		
		## we want to output different rows.
		## is this the beginning of a row
		if($row_items == 1) {
			## beginning of a row
			if($container_template->block_items['rowstart_alternate']) {
				if($rows%2) {
					$storage .= $container_template->fill_block("rowstart_alternate");
				} else {
					$storage .= $container_template->fill_block("rowstart");
				}				
			} else {
				$storage .= $container_template->fill_block("rowstart");
			}	
			
			$rows ++;	
		} 
		## her we shouldd check if we should ouptut different elements
		if($container_template->block_items['alternate']) {
			if($counter%2) {
				if ($structure['HIGHLIGHT'] && $container_template->block_items['active']) {
					if ($page_id != $current_page) {
						$storage .= $container_template->fill_block("alternate");
					} else {
						$storage .= $container_template->fill_block("active");
					}
				} else {
					$storage .= $container_template->fill_block("alternate");
				}			
			} else {
				if ($structure['HIGHLIGHT'] && $container_template->block_items['active']) {
					if ($page_id != $current_page) {
						$storage .= $container_template->fill_block("body");
					} else {
						$storage .= $container_template->fill_block("active");
					}
				} else {
					$storage .= $container_template->fill_block("body");
				}
			}	
			$counter++;
		} else {
			if ($structure['HIGHLIGHT'] && $container_template->block_items['active']) {
				if ($page_id != $current_page) {
					$storage .= $container_template->fill_block("body");
				} else {
					$storage .= $container_template->fill_block("active");
				}
			} else {
				$storage .= $container_template->fill_block("body");
			}
		}

		if($row_items == ($structure['ITEMCOUNT'])) {
			## the end of a row
			if($container_template->block_items['rowend_alternate']) {
				if($rows%2) {
					$storage .= $container_template->fill_block("rowend_alternate");
				} else {
					$storage .= $container_template->fill_block("rowend");
				}				
			} else {				
				$storage .= $container_template->fill_block("rowend");
			}
			## reset the counter
			$row_items=0;
		} 
		
		$row_items++;
				
		## okay if 
		if(isset($structure['COUNT'])) {
			$item_counter ++;
		}
		$total_counter++;
	}

	if(isset($structure['COUNT']) && ($structure['HIDEPAGEELEMENT'] == false || !isset($structure['HIDEPAGEELEMENT']))) {
		$page_navigation = internalnewsletter_listview_dspPageElement($page_id,$db_connectionMain,$totalItems,$items,$_GET["offset"], $structure['DELTA'],$structure['NAME']);
		$layout_template->set_vars($page_navigation);
		$container_template->set_vars($page_navigation);
	}

	
	## check if there are leftovers
	if(($row_items-1) < $structure['ITEMCOUNT']) {
		for($i=($row_items-1); $i <= $structure['ITEMCOUNT']; $i++) {
			if ($container_template->block_items['emptybody']) {
				$storage .= $container_template->fill_block("emptybody");
			}
		}
	}		 
	
	
	## check if the rows can be maped completely onto the items
	if((($row_items-1) % $structure['ITEMCOUNT'])!= 0) {
		## the end of a row
		if($container_template->block_items['rowend_alternate']) {
			if($rows%2) {
				$storage .= $container_template->fill_block("rowend_alternate");
			} else {
				$storage .= $container_template->fill_block("rowend");
			}				
		} else {				
			$storage .= $container_template->fill_block("rowend");
		}
	}
	
	if(is_object($container_template)) {
		
		if($total_counter>=1) {
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
function _newsletter_getSendNewsletters(&$db) {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];	
	
	$select_query = "SELECT * FROM ".USER_PAGES." AS A INNER JOIN ".DB_PREFIX."newsletter AS B ON A.page_id = B.page_id WHERE status=2 AND A.client_id ='".$client_id."' ORDER BY B.send";
	$result_pointer = $db->query($select_query);
}



## =======================================================================        
##  internalnewsletter_listview_dspPageElement   
## =======================================================================        
##  this function generates the page element
##
##  TODO: 
##     - create a abstract function- which is able to handle
##       different html styles
## ======================================================================= 
function internalnewsletter_listview_dspPageElement($page_id,&$db,$totalItems, $itemsPerPage,$offset, $delta,$identifier) {
	##  calculate how many pages we have to render
		$nrOfPages = ceil($totalItems / $itemsPerPage);

	##  load the pager class
		require_once ENGINE.'classes/class_html_pager.php';

	##  Settings
	##	(there are more of'em in the class constructor, these are the most commonly used)
		$params['totalItems'] = $nrOfPages;
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
?>
