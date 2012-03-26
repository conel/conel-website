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
## linklist_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function linklist_displayInput($xmldata, $data) {
	global $gSession, $Auth, $g_pageID,$input_language;
		
	## we need to load the language specific strings
	include(ENGINE."datatypes/linklist/interface/lang/".$Auth->auth["language"].".php");

	## init the vars
	$return = "";
	
	## we should open our own template
	$template = new Template(ENGINE."datatypes/linklist/interface/");
	$template->set_templatefile(array("linklist" => "interface.tpl","linklistmax" => "interface.tpl","linklist_row" => "interface.tpl","linklist_foot" => "interface.tpl"));
	
	## set the vars
	$template->set_var('element_name',$xmldata['NAME']);
	$template->set_var('element_desc',$xmldata['DESC']);
		
	## check if there is a entry
	if(isset($data['id'])) {
		## prepare the vars
		$linklistID = $data['id'];
	} else {
		$linklistID = null;
	}
	## prepare the url
	$addlinkURL = SITE."datatypes/linklist/editor.php?op=add&page_id=".$g_pageID."&identifier=".$xmldata['NAME']."&language=".$input_language."&linklistID=".$linklistID;

	$addlinkURL = $gSession->url($addlinkURL);	
	
	$deletelinkURL = SITE."datatypes/linklist/editor.php?op=delete&page_id=".$g_pageID."&identifier=".$xmldata['NAME']."&language=".$input_language."&linklistID=".$linklistID;		
	$deletelinkURL = $gSession->url($deletelinkURL);
	
	## the sort link (we will use our own editor, not the admin.php
	$sortURL = SITE."datatypes/linklist/editor.php?op=sort&page_id=".$g_pageID."&identifier=".$xmldata['NAME']."&language=".$input_language."&linklistID=".$linklistID;
	$sortURL = $gSession->url($sortURL);	
	
	## set the vars
	$template->set_var('addlinkItemURL',$addlinkURL);	
	$template->set_var('deletelinkItemURL',$deletelinkURL);
	$template->set_var('sortURL',$sortURL);
	
	if(!$xmldata['TAG']) {
		$template->set_var('element_tag',LANG_LINKLIST_Title);
	} else {
		$template->set_var('element_tag',$xmldata['TAG']);
	}	
	
	## get the number of records
	if(isset($data['length'])) {
		$items = $data['length'];
	} else {
		$items = 0;
	}
	## now check if we are allowed to set another one
	if($items >= $xmldata['MAXCOUNT'] && isset($items) && isset($xmldata['MAXCOUNT'])) {
		## output the stripped down block
		$return = $template->fill_block("linklistmax");
	} else {
		$return = $template->fill_block("linklist");
	}
	
	## loop through all records
	for($i=0; $i< $items; $i++) {;							
		## so we can savely dsiplay the entry
		$decription = $data[$i]["text"];										
		$template->set_var('decription',$decription);
		$template->set_var('linkID',$data[$i]['link']);		
		$return .= $template->fill_block("linklist_row");
	}	

	$return .= $template->fill_block("linklist_foot");
							
	return $return;
}

## =======================================================================        
##  linklist_storeData        
## =======================================================================        
## save the data in the db
## ======================================================================= 
function linklist_storeData($page_id, $identifier) {
	return false;
}

## =======================================================================        
##  linklist_getData     
## =======================================================================        
##  get Data
## ======================================================================= 
function linklist_getData($vPageID,&$page_record) {
	global $Auth,$input_language;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];	

	$db_connection = new DB_Sql();
	
	## now for the linklistitems
	$query = "SELECT link_list_identifier,link_list_id FROM ".LINKLIST." WHERE page_id='$vPageID' AND language='$input_language' AND client_id='$client_id' ORDER BY link_list_identifier";
	$result_pointer = $db_connection->query($query);
	## loop through the resuls and set the vars accordingly
	$old_identifier="";

	$mydev_subquery = "";
	$link_list = array();
	while($db_connection->next_record()) {
		## we need to get the items associated with each linkList on this page
		$identifier = $db_connection->Record["link_list_identifier"];
		$link_list_id = $db_connection->Record["link_list_id"];	
		
		## prepare the subquery to get all entries with one call
		if($mydev_subquery != "") {
			$mydev_subquery .= " OR ";
		}
		$mydev_subquery .= "link_list_id='$link_list_id'";
		$link_list[$link_list_id] = $identifier;
			
		## add this info to the container
		$page_record[$identifier]["type"] = "LINKLIST";
		$page_record[$identifier]["id"] = $link_list_id;	
	}
	if($mydev_subquery != "") {
		$mydev_subquery = 'AND ('.$mydev_subquery.') ';
	}

	$itemCounter = 0;
	## let's get the page name for each linklistitem
	$select_query = "SELECT link_list_id,page_id,structure_text, link_list_item_id,link_list_item_order FROM ".LINKLISTITEM." INNER JOIN ".STRUCTURE." ON ".STRUCTURE.".page_id=".LINKLISTITEM.".link_list_item_target WHERE ".LINKLISTITEM.".client_id='$client_id' ".$mydev_subquery." ORDER BY link_list_id,link_list_item_order";
	$result_pointer = $db_connection->query($select_query);	
	
	$identifier = '';
	while($db_connection->next_record()) {	
		$link_list_id = $db_connection->Record["link_list_id"];
		$text = $db_connection->Record["structure_text"];
		$link = $db_connection->Record["link_list_item_id"];
		$page_id = $db_connection->Record["page_id"];
		$order = $db_connection->Record["link_list_item_order"];
		
		if(isset($link_list[$link_list_id])) {
			if($identifier != $link_list[$link_list_id]) {
				$itemCounter = 0;
			}
			$identifier = $link_list[$link_list_id];
	
			$page_record[$identifier][$itemCounter]["text"] = $text; 
			$page_record[$identifier][$itemCounter]["link"] = $link; 
			$page_record[$identifier][$itemCounter]["page_id"] = $page_id;
			$page_record[$identifier][$itemCounter]["order"] = $order;
			$itemCounter++;			
			$page_record[$identifier]["length"] = $itemCounter;	
		}
	}	
	$db_connection->free();
}

## =======================================================================        
##  linklist_copyData        
## =======================================================================        
##  pass it the sourcepage_id and the targetpage_id. It'll copy all
##  text entries to the new page.
## ======================================================================= 
function linklist_copyData($source_id, $target_id) {
	global $Auth;
	## multiclient
	$client_id = $Auth->auth['client_id'];	

	## data connection
	$db_source = new DB_Sql();
	$db_source_items = new DB_Sql();
	$db_target = new DB_Sql();
	
	## first we need to get all base entries- then we need to create new base entries
	## then we need to copy each base entries items
	
	## get all text elements
	$query = "SELECT link_list_id,link_list_identifier,language FROM ".LINKLIST." WHERE page_id='$source_id' AND client_id='$client_id'";
	$result_pointer = $db_source->query($query,true);

	## loop through the results and copy them over
	while($db_source->next_record()) {
		$link_list_identifier = $db_source->Record['link_list_identifier'];
		$language = $db_source->Record['language'];
		$source_link_list_id = $db_source->Record['link_list_id'];
		## okay now we need to create a new base entry in the target page
		
		## double check if the entry doesn' exist yet
		$query = "SELECT link_list_id FROM ".LINKLIST." WHERE page_id = '$target_id' AND link_list_identifier = '$link_list_identifier' AND client_id = '$client_id' AND language='$input_language'";
		$result_pointer = $db_target->query($query,true);			
	
		if($db_target->num_rows() == 0) { 
			## okay nothing found- let's insert a new base entry
			$query = "INSERT INTO ".LINKLIST." (page_id, link_list_identifier,client_id,language,modified) values ('$target_id', '$link_list_identifier','$client_id','$language',now())";
			$result_pointer = $db_target->query($query,true);
			$link_list_id = $db_target->db_insertid($result_pointer);
			
			## now get all source entries
			$query = "SELECT link_list_item_target,link_list_item_order FROM ".LINKLISTITEM." WHERE link_list_id='$source_link_list_id'";
			$result_pointer = $db_source_items->query($query,true);
			
			while($db_source_items->next_record()) {
				$link_list_item_target = $db_source_items->Record['link_list_item_target'];
				$link_list_item_order = $db_source_items->Record['link_list_item_order'];
				
				## now insert the new entry into the new linklist
				$query = "INSERT INTO ".LINKLISTITEM." (link_list_id, link_list_item_target,link_list_item_order,client_id) values ('$link_list_id', '$link_list_item_target','$link_list_item_order','$client_id')";		
				$result_pointer = $db_target->query($query,true);
			}
		}
	}	
}


## =======================================================================        
##  linklist_deleteData        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function linklist_deleteData($vPageID) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];	

	## data connection
	$db_connection = new DB_Sql();
	
	## we need to find out which linklistitems are related to the current pageID
	## first we should get the linklist
	$query = "SELECT link_list_id FROM ".LINKLIST." WHERE page_id = '$vPageID' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($query);
	
	## get the id
	$db_connection->next_record();
	$vLinkListID = $db_connection->Record["link_list_id"];

	## let's delete the items
	$query = "DELETE FROM ".LINKLISTITEM." WHERE link_list_id='$vLinkListID' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($query);

	## now delete the linklist
	$query = "DELETE FROM ".LINKLIST." WHERE page_id='$vPageID' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($query);
}

## =======================================================================        
##  output_linklist       
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function output_linklist($item,$structure,$menu_id) {	
	## here we store the output that is generated
	$storage = "";

	## first we open the xml-file
	if(isset($structure['TEMPLATE'])) {
		$basename = $structure['TEMPLATE'];
		$xmlFile  = $basename.".xml";
		$filename = $basename.".tpl";
				
		$xmlStructure = new xmlparser(HTML_DIR.$xmlFile);
		$xmlStructure->parse();
		## parse the template file
		$objects = $xmlStructure->getObjects();
		$xmlStructure = $xmlStructure->getElements();	
		
		## now we open the template file for output
		$templateFile = new Template(HTML_DIR);
		$templateFile->set_templatefile(array("head" => $filename,"body" => $filename,"foot" => $filename,"empty" => $filename,"alternate" => $filename)); 
	}

	## get the page names
	$page_names		= structure_getMultiPageName($item);
	
	$total_counter = 0;
	## let's generate each selected page using the normal generate page
	for($i=0;$i<$item["length"]; $i++) {
		$page_record = page_getPage($item[$i]["page_id"],$objects);
		$page_id = $item[$i]["page_id"];
		## now loop through all xmlelements
		$counter =0;
		$num_elements = count($xmlStructure)-1;
		
		## reset the template
		$templateFile->varkeys = array();  
		$templateFile->varvals = array(); 
		
		while($counter < $num_elements) {					
			## okay first we try to find out what type we have
			## we wrap this up in a switch statemnt- this way we can
			## extend it more easily
			$element_type = $xmlStructure[$counter]['TYPE'];
			$element_name = $xmlStructure[$counter]['NAME'];
			
			switch($element_type) {
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
					$target = strtolower($element_type); 
					if(isset($page_record[$element_name])) {
						eval("\$element = output_".$target."(\$page_record[\$element_name],\$xmlStructure[\$counter],$menu_id);");	
						if(is_array($element)) {
							$templateFile->set_vars($element);
						} else {
							$templateFile->set_var($element_name,$element);
						}
					}
					break;
				}	
				case 'INCLUDE': {
					$target = strtolower($element_type); 
					eval("\$element = output_".$target."('',\$xmlStructure[\$counter],$menu_id,".$item[$i]["page_id"].");");	
					if(is_array($element)) {
						$templateFile->set_vars($element);
					} else {
						$templateFile->set_var($element_name,$element);
					}			
					break;
				}	
				case 'LISTVIEW': {
					$element = "";
					
					$element = output_listview($page_record,$xmlStructure[$counter],$layout_template,$menu_id,$page_id); 
					$templateFile->set_var($element_name,$element);	
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
						if(isset($page_record[$element_name])) {
							eval("\$element = ".$target."_output(\$page_record[\$element_name],\$xmlStructure[\$counter],$menu_id);");
						} else {
							eval("\$element = ".$target."_output(\$page_record,\$xmlStructure[\$counter],\$layout_template,\$menu_id,\$page_id);");
						}	
						if(is_array($element)) {
							$templateFile->set_vars($element);
						} else {
							$templateFile->set_var($element_name,$element);
						}
											
					}
					break;
				}
			}
			$counter++;
		}
		## now output the internal vars
		$page_id = $item[$i]["page_id"];
		$targetURL = getTargetURL($page_id);	
		
		$templateFile->set_var('matrix:TARGETPAGE',$targetURL);
		$templateFile->set_var('matrix:PAGETITLE',$page_names[$page_id]['name']);
		$templateFile->set_var('matrix:PAGEID',$page_id);
		$templateFile->set_var("matrix:COUNTER", $i);
		
		$storage .= $templateFile->fill_block("body");

		$total_counter++;
	}
	
	
	if($total_counter >= 1) {

		$head = $templateFile->fill_block("head");
		$foot = $templateFile->fill_block("foot");
	
		$return = array();
		$return[$structure['NAME']] = $head.$storage.$foot;
		$return["matrix:MAXCOUNT:".$structure['NAME']] = $item["length"];
	} else {
		$return = $templateFile->fill_block("empty");
	}

	return $return;		
}

?>
