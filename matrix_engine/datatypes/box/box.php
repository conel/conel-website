<?PHP
## ======================================================================= 
##   webmatrix                                                     
## ======================================================================= 
##   Copyright (c) 2001, 2002, 2003 Stefan Elsner, Workmatrix        
## ======================================================================= 
##   <info@workmatrix.de> 
##
## =======================================================================        
## Version 1.1    (last changed: 20.4.2004)
## =======================================================================        
##	the box view is the new module which handles container related stuff.
##	and in addtion to the container functionality enables to use of 
##  containers with a different template for each element. As introduced 
##  with the linklist- the entries can be sorted
## ======================================================================= 
include_once(ENGINE.'datatypes/box/functions/store.php');

## =======================================================================        
## box_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function box_displayInput($xmldata, $data) {
	global $gSession, $Auth, $g_pageID,$input_language;
		
	## we need to load the language specific strings
	include(ENGINE."datatypes/box/interface/lang/".$Auth->auth["language"].".php");

	## init the vars
	$return = "";

	## we should open our own template
	$template = new Template(ENGINE."datatypes/box/interface/");
	$template->set_templatefile(array("box" => "interface.tpl","boxmax" => "interface.tpl","box_row" => "interface.tpl","box_foot" => "interface.tpl"));
	
	## set the vars
	$template->set_var('element_name',$xmldata['NAME']);
	$template->set_var('BOXID',$xmldata['NAME']);
	$template->set_var('element_desc',$xmldata['DESC']);
		
	## prepare the vars
	$linklistID = $data['id'];
	$basename 	= $xmldata['TEMPLATE'];
	
	## prepare the url
	$addlinkURL = SITE."datatypes/box/editor.php?op=add&page_id=".$g_pageID."&identifier=".$xmldata['NAME']."&language=".$input_language."&linklistID=".$linklistID."&basename=".$basename;
	$addlinkURL = $gSession->url($addlinkURL);	
	
	$deletelinkURL = SITE."datatypes/box/editor.php?op=delete&page_id=".$g_pageID."&identifier=".$xmldata['NAME']."&language=".$input_language."&linklistID=".$linklistID;		
	$deletelinkURL = $gSession->url($deletelinkURL);
	
	## the sort link (we will use our own editor, not the admin.php
	$sortURL = SITE."datatypes/box/editor.php?op=sort&page_id=".$g_pageID."&identifier=".$xmldata['NAME']."&language=".$input_language."&linklistID=".$linklistID;
	$sortURL = $gSession->url($sortURL);	
	
	## set the vars
	$template->set_var('addlinkItemURL',$addlinkURL);	
	$template->set_var('deletelinkItemURL',$deletelinkURL);
	$template->set_var('sortURL',$sortURL);
	
	if(!$xmldata['TAG']) {
		$template->set_var('element_tag',LANG_box_Title);
	} else {
		$template->set_var('element_tag',$xmldata['TAG']);
	}	
	
	## get the number of records
	$items = $data['length'];
	## now check if we are allowed to set another one
	if($items >= $xmldata['MAXCOUNT'] && isset($items) && isset($xmldata['MAXCOUNT'])) {
		## output the stripped down block
		$return = $template->fill_block("boxmax");
	} else {
		$return = $template->fill_block("box");
	}
	
	## process the entry settings first we need to find out what type the entry field is
	$options = _box_generateOptions($xmldata['ENTRY']);	
	$entries = _box_generateEntries($xmldata['ENTRY']);	
	
	## loop through all records
	for($i=0; $i< $items; $i++) {
		$description = "";	
		## we need to output all specified options
		## so we get them here
		$page_data = page_getPage($data[$i]["target"],$options);
		## and now we loop through the data
		foreach($entries as $current_entry) {
			$element_type = $current_entry['TYPE'];
			$element_name = $current_entry['NAME'];	

			switch($element_type) {
				case 'LINK':
				case 'DATE':
				case 'IMAGE':
				case 'LINKLIST':
				case 'FILE':
				case 'COPYTEXT':				
				case 'TEXT': {
					$target = strtolower($element_type);
					if(function_exists($target."_displayPreview")) {				
						eval("\$preview .= ".$target."_displayPreview(\$current_entry,\$page_data[\$element_name]);");
						if($preview != " " && $preview !="") {
							$description .= $preview.'<br>';
							$preview = '';
						}						
					}
					break;
				}									
													
			}
		}							

		## okay we have contents- we also need to output the template that was used
		$template->set_var('decription',$description);

		$templateInfo = template_getTemplate($data[$i]["target"]);
		if(isset($templateInfo['title'])) {
			$template_text = ''.$templateInfo['title'].'&nbsp;';
		} else {
			$template_text = ''.$data[$i]["item_id"].'&nbsp;';
		}							
		$template->set_var('template',$template_text);
					
		$template->set_var('linkID',$data[$i]['item_id']);		
		$return .= $template->fill_block("box_row");
		
		## reset the vars
		$template->set_var('decription','');
		$template->set_var('template','');
	}
	$return .= $template->fill_block("box_foot");
						
	return $return;
}

## =======================================================================        
##  box_storeData        
## =======================================================================        
## save the data in the db
## ======================================================================= 
function box_storeData($page_id, $identifier) {
	return false;
}

## =======================================================================        
##  box_getData     
## =======================================================================        
##  get Data
## ======================================================================= 
function box_getData($vPageID,&$page_record) {
	global $Auth,$input_language;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];	

	$db_connectionMain = new DB_Sql();  

	## now for the linklistitems
	$query = 'SELECT identifier,box_id FROM '.DB_PREFIX."box WHERE page_id='$vPageID' AND client_id='$client_id' AND language='$input_language' ORDER BY identifier";
	$result_pointer = $db_connectionMain->query($query);

	## loop through the resuls and set the vars accordingly
	$db_connection = new DB_Sql();
	$old_identifier="";
	while($db_connectionMain->next_record()) {
		## we need to get the items associated with each linkList on this page
		$identifier 	= $db_connectionMain->Record['identifier'];
		$box_id 		= $db_connectionMain->Record['box_id'];	
		
		## check if it is a new entry
		if($identifier != $old_identifier) {
			$old_identifier = $identifier; 
		} 
		## add this info to the container
		$page_record[$identifier]['type'] = "BOX";
		$page_record[$identifier]['id'] = $box_id;	
		
		## now get all entries for the current box
		$select_query = 'SELECT target, item_order, box_item_id FROM '.DB_PREFIX."box_item WHERE box_id='$box_id' AND client_id='$client_id' ORDER BY item_order";
		$result_pointer = $db_connection->query($select_query);	
		
		$itemCounter = 0;
		while($db_connection->next_record()) {	
			$page_record[$identifier][$itemCounter]["item_id"] 	= $db_connection->Record["box_item_id"];
			$page_record[$identifier][$itemCounter]["target"] 	= $db_connection->Record["target"];
			$page_record[$identifier][$itemCounter]["order"] 	= $db_connection->Record["item_order"];
			$itemCounter++;			
		}		
	$page_record[$identifier]['length'] = $itemCounter;
	}	
}

## =======================================================================        
##  box_deleteData        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function box_deleteData($vPageID) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];	

	## data connection
	$db_connection = new DB_Sql();
	$db = new DB_Sql();
	
	## we get a page id.... we need to check whioch box elements sit on this page
	$query = "SELECT box_id FROM ".DB_PREFIX."box WHERE page_id = '$vPageID' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($query);
	
	## get the id
	while($db_connection->next_record()) {
		$box_id = $db_connection->Record["box_id"];
	
		## now get all pages that are associated with the items
		$query = "SELECT target FROM ".DB_PREFIX."box_item WHERE box_id = '$box_id' AND client_id='$client_id'";
		$result_pointer = $db->query($query);
	
		$pages_to_delete = array();
		while($db->next_record()) {
			$pages_to_delete[] = $db->Record["target"];	
		}

		## form a query out of the whole thing
		$sub_query="";	
		foreach ($pages_to_delete as $current_item) {
			page_deletePage($current_item);
		}
	}

	## clean up the box container
	$query = "DELETE FROM ".DB_PREFIX."box WHERE page_id='$vPageID' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($query);	

		
	## let's delete all entries in the box_item table
	$query = "DELETE FROM ".DB_PREFIX."box_item WHERE box_id='$box_id' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($query);	
}

## =======================================================================        
##  box_copyData        
## =======================================================================        
##  pass it the sourcepage_id and the targetpage_id. It'll copy all
##  box entries to the new page.
## ======================================================================= 
function box_copyData($source_id, $target_id) {
	global $Auth,$input_language;
	## multiclient
	$client_id = $Auth->auth['client_id'];	

	## db object
	$db = new DB_Sql();  
	
	## let's get the current data
	$page_record = array();
	box_getData($source_id,$page_record);
	
	## we need to loop throuhg all boxes
	foreach($page_record as $identifier => $data) {
		## create a box list in for the target page
		$query = "INSERT INTO ".DB_PREFIX."box (page_id,identifier, client_id,language) VALUES ('$target_id','$identifier','$client_id','$input_language')";
		$result_pointer = $db->query($query);
		## get the created id
		$targetBoxID = $db->db_insertid($result_pointer);
		## okay for each target element- we will store the order and copy the page
		foreach($data as $current_data) {
			if(is_array($current_data)) {
				$source = $current_data['target'];
				$order = $current_data['order'];
			
				## then we need to copy the data over
				$newTarget = page_copyPage($source,'box');
			
				## now register the page with new newly created base element 
				box_structure_storePage($targetBoxID,$newTarget);
			}
		}
	}
}



## =======================================================================        
##  output_box       
## =======================================================================        
##  outputs the page for each entry
## ======================================================================= 
function output_box($item,$structure,$menu_id,$page_id=0) {

	## if we are in slideshow mode- we expect to get the current page passed
	$identifier = $structure['NAME'];
	$slideshow = isset($structure['SLIDESHOW']) ? strtolower($structure['SLIDESHOW']) : false;
	if($slideshow == 'true') {
		$current_slide = intval($_GET[$identifier.'_slide']);
		
		$start_slide = ($current_slide >= 0) ? $current_slide : 0;
		$end_slide 	= min($item['length'],($current_slide+1));
		
		## prepare the previous and next buttons
		$next = ($end_slide== $item['length']) ? 0 : $end_slide;

		$prev = ($start_slide > 0) ? ($start_slide-1) : ($item['length']-1);
	} else {
		$start_slide = 0;
		$end_slide = $item['length'];
	}
	## optionally you can provide a template which will be outputed before and after
	if(isset($structure['BOXTEMPLATE'])) {
		$basename = $structure['BOXTEMPLATE'];
		$filename = $basename.".tpl";
		$template = new Template(HTML_DIR);
		$template->set_templatefile(array("head" => $filename,"foot" => $filename,"empty" => $filename,"navigation" => $filename)); 

		## prepare the next and previous link if we are in slideshowmode
		$baseURL = getTargetURL($page_id).'&'.$identifier.'_slide=';
		$template->set_var($identifier.'.next',$baseURL.$next);
		$template->set_var($identifier.'.previous',$baseURL.$prev);
	}	
	
	## here we store the output that is generated
	$storage = "";

	if($item["length"] > 0 ) {
		## output the header if it is set
		if(isset($structure['BOXTEMPLATE']) && $template->block_items['head']) {
			$storage .= 	$template->fill_block("head");
		}		
		## so we should loop through each item and generate the page for it
		for($i=$start_slide;$i<$end_slide; $i++) {
			$target_page = $item[$i]['target'];
			$storage .= page_generatePage($target_page,0,array('matrix:COUNTER'=>($i+1)));
		}
		if($slideshow == 'true') {
			## output the footer or the navigation element
			if(isset($structure['BOXTEMPLATE'])) {
				if(isset($template->block_items['foot']) && ($item['length'] == 1)) {
					$storage .= $template->fill_block("foot");
				} else if(isset($template->block_items['navigation'])) {
					$storage .= $template->fill_block("navigation");
				}
			}
		} else {
			if(isset($structure['BOXTEMPLATE']) && isset($template->block_items['foot'])) {
				$storage .= 	$template->fill_block("foot");
			}
		}
	} else {
		if(isset($structure['BOXTEMPLATE']) && $template->block_items['empty']) {
			$storage = 	$template->fill_block("empty");
		}
	}
	return $storage;
}

## =======================================================================        
##  _box_generateDescription       
## =======================================================================        
## generates the description for the input forms
## ======================================================================= 
function _box_generateOptions($entry) {
	## first we need to find out what type the entry field is
	$entries = explode(",", $entry);	
	
	$options = array();
	foreach($entries as $current_entry) {
		$type	 			= explode(":", $current_entry);
		$options[$type[0]]	= true;
	}
	return $options;
}

## =======================================================================        
##  _box_generateDescription       
## =======================================================================        
## generates the description for the input forms
## ======================================================================= 
function _box_generateEntries($entry) {
	## first we need to find out what type the entry field is
	$entries = explode(",", $entry);	
	
	$options = array();
	$i = 0;
	foreach($entries as $current_entry) {
		$type	 			= explode(":", $current_entry);
		$options[$i]['TYPE'] = $type[0];
		$options[$i]['NAME'] = $type[1];
		$i++;
	}
	return $options;
}

?>
