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
## listviewfield_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function listviewfield_displayInput($xmldata, $data) {
	return "";
}


## =======================================================================        
##  listviewfield_storeData        
## =======================================================================        
## save the content in the db
## ======================================================================= 
function listviewfield_storeData($page_id, $identifier) {
	return false;
}

## =======================================================================        
##  listviewfield_getData        
## =======================================================================        
##  get the Data
## ======================================================================= 
function listviewfield_getData($vPageID,&$page_record) {
}

## =======================================================================        
##  listviewfield_getMultiData       
## =======================================================================        
##  get the Data for multiple pages
## ======================================================================= 
function listviewfield_getMultiData($vItems) {
}

## =======================================================================        
##  listviewfield_deleteData        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function listviewfield_deleteData($vPageID) {
}

## =======================================================================        
##  output_text        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function listviewfield_output($item,$structure,&$layout_template,$menu_id,$page_id) {
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
	## here we determine what we should search for
	_listviewfield_getByCategory($page_id,$db_connectionMain,$structure);

	
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
				case 'INCLUDE' :
				case 'TEXT': 
				case 'COPYTEXT':
				case 'DATE': 
				case 'LINK':
				case 'FILE':
				case 'BOX':  
				case 'LINKLIST':  
				case 'IMAGE': {
					$target = strtolower($container_element_type);
					eval("\$element = output_".$target."(\$page_record[\$container_element_name],\$container_elements[\$container_counter],$menu_id,$page_id);");
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
						## check if the page_record entry is defined
						## if not we need to pass the whole record
						if(isset($page_record[$container_element_name])) {
							eval("\$element = ".$target."_output(\$page_record[\$container_element_name],\$container_elements[\$container_counter],$menu_id);");
						} else {
							eval("\$element = ".$target."_output(\$page_record,\$container_elements[\$container_counter],\$layout_template,\$menu_id,\$page_id);");
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
		$container_template->set_var("matrix:PAGENAME", $db_connectionMain->Record["structure_text"]);

		## her we shouldd check if we should ouptut different elements
		if(!empty($page_record)) {
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
		}

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
##  _listviewfield_getByCategory    
## =======================================================================        
##  this is an internal function for the listview function
##  it handles the queries for a Category request
##
##  TODO: 
##     - test if it works
## ======================================================================= 
function _listviewfield_getByCategory($page_id,&$db,$xmldata) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];	

	$category 	= $xmldata['CATEGORY'];
	$sortby		= isset($xmldata['SORTBY']) ? $xmldata['SORTBY']: "";					
	$sortorder	= isset($xmldata['ORDERDIRECTION']) ? $xmldata['ORDERDIRECTION']: "";
	$field	 	= $xmldata['FIELD'];				
		
	$select_query   = "SELECT template_id FROM ".PAGE_TEMPLATE." WHERE basename='$category' AND client_id=".$client_id;
	$result_pointer = $db->query($select_query);	
	$db->next_record();

	$template_id         = $db->Record["template_id"];
	
	$fields = explode("=", $field);
	
	$field_identifier = $fields[0];
	$field_content = $fields[1];
	

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
			$secondpart = "INNER JOIN ".PAGE_DATE." AS D ON B.page_id = D.page_id WHERE B.template='$template_id' AND (structure_flag >= 2) AND D.identifier='".$sort_control[1]."' AND D.client_id='".$client_id."' AND C.identifier='$field_identifier' AND C.text LIKE '%$field_content%' ORDER BY $sort date ".$sortorder;
		} else {
			## we have a custom sort oder:
			$secondpart = "INNER JOIN ".PAGE_CONTENT." ON B.page_id = ".PAGE_CONTENT.".page_id WHERE B.template='$template_id' AND (structure_flag >= 2) AND identifier='".$sort_control[0]."' AND ".PAGE_CONTENT.".client_id='".$client_id."' AND C.identifier='$field_identifier' AND C.text LIKE '%$field_content%' ORDER BY $sort text ".$sortorder;
		}
	} else {
		$secondpart = "WHERE B.template='$template_id' AND (structure_flag >= 2) AND B.client_id=$client_id AND C.identifier='$field_identifier' AND C.text LIKE '%$field_content%' ORDER BY $sort structure_order ".$sortorder;
	}	
		
		
	$select_query = "SELECT A.page_id,structure_text, B.template FROM ".STRUCTURE." AS A INNER JOIN ".USER_PAGES." AS B ON A.page_id = B.page_id INNER JOIN ".PAGE_CONTENT." AS C ON A.page_id=C.page_id ".$secondpart;					
	$result_pointer = $db->query($select_query);		
}


## =======================================================================        
##  internallistviewfield_dspPageElement   
## =======================================================================        
##  this function generates the page element
##
##  TODO: 
##     - create a abstract function- which is able to handle
##       different html styles
## ======================================================================= 
function internallistviewfield_dspPageElement($page_id,&$db,$totalItems, $itemsPerPage,$offset, $delta,$identifier) {
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
