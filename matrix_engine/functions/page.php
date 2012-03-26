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
function page_getPage($page_id,$objects=NULL) {
	$page_record = array();	## holds all data

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
				case 'LINK': {
					## get the elements
					$target = strtolower($key);
					@eval("\$element = ".$target."_getData(\$page_id,\$page_record);");	
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
function page_getDataByIdentifier($page_id,$identifier) {
	## we need to return the data for a certain identifier on a certain page
	## first e will fetch the template
	$page_info = page_getPageInfo($page_id);
	
	$templateInfo = template_getTemplateID($page_info['template']);
	$basename = $templateInfo["basename"];

	## parse the xml-file	
	$wt = new xmlparser(HTML_DIR.$basename.".xml");
	$wt->parse();
	$elements = $wt->getElements();
	
	## we need to find the element thatmatches our identifier
	$selected_element = array();
	foreach($elements as $current_element) {
		if($current_element['NAME'] == $identifier) {
			## okay we found the element- now fetch the data
			$selected_element = $current_element;
			$page_record = array();
			$key = $current_element['TYPE'];

			switch($key) {
				case 'COPYTEXT':
				case 'TEXT':
				case 'LINKLIST':
				case 'LISTVIEW':
				case 'IMAGE':
				case 'FILE':
				case 'BOX':
				case 'DATE':	
				case 'LINK': {
					## get the elements
					$target = strtolower($key);
					@eval("\$element = ".$target."_getData(\$page_id,\$page_record);");	
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
		
	if(isset($page_record[$identifier])) {
		$target = strtolower($selected_element['TYPE']);
		@eval("\$element = output_".$target."(\$page_record[\$identifier],\$selected_element,0);");	
		return $element;
	} 
}

## =======================================================================        
##  drop_page        
## =======================================================================        
##  deletes a page and all its elements
##
##  TODO:
##       - delete containers
## ======================================================================= 
function page_deletePage($page_id) {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];

	## prepare the db-object
	$db_connection = new DB_Sql(); 
		
	## first we delet all the menu- so no one will be able to call this page again
	## while we are deleting the rest!
	##structure_deletePageFromMenuID($page_id);

	## delete the page!
	$select_query = "DELETE FROM ".USER_PAGES." WHERE page_id='$page_id' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($select_query);	
	
	page_deletePageContents($page_id);
}

## =======================================================================        
## page_deletePageContents       
## =======================================================================        
## deletes all datatypes but not the page itself
##
## ======================================================================= 
function page_deletePageContents($page_id) {
	## first call the base attributes
	image_deleteData($page_id);
	text_deleteData($page_id);	
	date_deleteData($page_id);
	linklist_deleteData($page_id);
	link_deleteData($page_id);
	box_deleteData($page_id);
	file_deleteData($page_id);		
	## we should get the extra_datatypes here
	## get all installed extras

	include_once(ENGINE."functions/datatypes.php");
	$extras = loadExtraDatatypes();
	## now loop through all extras and execute the functions
	while(list($key,$val) = each($extras)) {
		eval($val."_deleteData(\$page_id);");
	}
}

## =======================================================================        
##  page_createPage        
## =======================================================================        
##  we store the base information for a certain template    
##
##  TODO:
##       - make all pages by default inactive
## =======================================================================        
function page_createPage($title, $template,$type='page') {
	global $Auth;
	$client_id = $Auth->auth["client_id"];
	
	## prepare the db-object
	$db = new DB_Sql();
	// sanitise title text
	$title = mysql_real_escape_string($title);
	$query = "INSERT INTO ".USER_PAGES." (title, template, type, created, modified, client_id) VALUES ('$title', '$template','$type',now(),now(),'$client_id')";
	$rp = $db->query($query);	
	$page_id = $db->db_insertid($rp);
	
	return $page_id;
}

## =======================================================================        
##  page_copyPage      
## =======================================================================        
##  copy a page, gets called with a source_page_id, creates a new page
##  and then calls all datatypes of this page to copy themselfes
##  TODO:
##       - make all pages by default inactive
## =======================================================================        
function page_copyPage($source_id,$type='page') {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## first we need to find out the template and the name of the page-
	## with this info we will create the target page
	$pageInfo = page_getPageInfo($source_id);

	## then create a new page using this info
	$target_id = page_createPage($pageInfo["title"], $pageInfo["template"],$type);
	
	## okay copy the data now
	page_copyContents($source_id,$target_id);
	
	## finally return the newly created pageid	
	return $target_id;
}


## =======================================================================        
##  page_copyContents      
## =======================================================================        
##  copies the contents of a page to another page
##
## =======================================================================        
function page_copyContents($source_id,$target_id) {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## first we need to find out the template and the name of the page-
	## with this info we will create the target page
	$pageInfo = page_getPageInfo($source_id);
	
	## now we need to get all datatypes for the used template- loop through
	## them and call their copy function
	## get the template
	$templateInfo = template_getTemplateID($pageInfo["template"]);
	$basename = $templateInfo["basename"];	

	## parse the xml-file	
	$wt = new xmlparser(HTML_DIR.$basename.".xml");
	$wt->parse();
	$objects = $wt->getObjects();	

	foreach($objects as $current_object => $value) {
		$target = strtolower($current_object);

		switch($current_object) {
			case 'LINKLIST':
			case 'BOX' :
			case 'FILE' :
			case 'IMAGE':
			case 'LINK':
			case 'DATE':
			case 'COPYTEXT' :
			case 'TEXT': {
				## all standard datatypes have the copy function implemented
				eval("\$element = ".$target."_copyData(\$source_id,\$target_id);");				
				break;
			}	
			case 'INCLUDE':
			case 'LISTVIEW':
				## these datatypes don't need to copy themselves so we catch them here
				break;
			default: {			
				## first we try to include the apropriate file 
				@include_once(ENGINE."datatypes/extra_".$target."/".$target.".php");
					
				## now we check if the function exists
				if(function_exists($target."_copyData")) {
					eval("\$element = ".$target."_copyData(\$source_id,\$target_id);");
				}
				break;
			}				
		}
	}
}

## =======================================================================        
##  page_mergePage      
## =======================================================================        
##  merges source into target
## 		- removes contetns of the target
##		- copy the contents from the source
##		- deleet the source page
##
## =======================================================================        
function page_mergePage($source_id,$target_id) {	
	## okay first delete all entries of the targetpage
	page_deletePageContents($target_id);
	
	## then copy the contents of source to the target
	page_copyContents($source_id,$target_id);
}



## =======================================================================        
##  page_storePage        
## =======================================================================        
##  stores all information the user typed into the input form  
##	handles version control by generating a timestamp and passing
##	this timestamp to all datatypes. this way the timestamp for 
##  and update is consistant  
##
##  TODO:
##       - check if it works!
## ======================================================================= 
function page_storePage() {
	
	## get the global pages	
	$page_id 		= $_POST["pageID"];
	$template_id 	= $_POST["templateID"];

	## update the mod-date of the current page
	page_updatePage($page_id);
		
	## get the template

	$templateInfo = template_getTemplateID($template_id);
	$basename = $templateInfo["basename"];

	## parse the xml-file	
	$wt = new xmlparser(HTML_DIR.$basename.".xml");
	$wt->parse();
	$elements = $wt->getElements();
	
	$counter =0;
	while($counter < count($elements)-1) {
		$element_type = $elements[$counter]['TYPE'];
		$element_name = $elements[$counter]['NAME'];
		$target = strtolower($element_type); 

		switch($element_type) {
			case 'LINK':
			case 'DATE':
			case 'LINKLIST':
			case 'IMAGE':
			case 'FILE' :
			case 'BOX' :
			case 'COPYTEXT' :
			case 'TEXT': {
				eval("\$element = ".$target."_storeData(\$page_id,\$element_name);");				
				break;
			}														
			default: {			
				## first we try to include the apropriate file 
				@include_once(ENGINE."datatypes/extra_".$target."/".$target.".php");
					
				## now we check if the function exists
				if(function_exists($target."_storeData")) {
					eval("\$element = ".$target."_storeData(\$page_id,\$element_name);");
				}
				break;
			}				
		}
  		$counter++;
	}
}


## =======================================================================        
##  page_updatePage        
## =======================================================================        
##  update the page- used for updating the modification date   
##
## =======================================================================        
function page_updatePage($id) {
	global $Auth;
	$client_id = $Auth->auth["client_id"];
	$user_id = $Auth->auth['user_id'];

	## prepare the db-object
	$db = new DB_Sql();	
	
	// fetch the users name
	$query = "SELECT firstname,lastname,user_name FROM ".DB_PREFIX."users WHERE user_id ='$user_id'";
	$result_pointer = $db->query($query);
	
	$user = '';
	if($db->next_record()) {
		$user = $db->Record['firstname'].' '.$db->Record['lastname'].' ('.$db->Record['user_name'].')';
	}
	$query = "UPDATE ".USER_PAGES." SET modified = now(), user='".$user."' WHERE page_id = $id";
	$result_pointer = $db->query($query);
}

## =======================================================================        
## page_getPageInfo       
## =======================================================================        
## returns the page_info for a certain page
##    
## =======================================================================
function page_getPageInfo($id) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	$db_connection = new DB_Sql();
	$query = "SELECT * FROM ".USER_PAGES." WHERE page_id ='$id' AND client_id='$client_id' LIMIT 1";
	$result = $db_connection->query($query);	

	$pageInfo = array();
	
	if($db_connection->next_record()) {
		$pageInfo["page_id"] 		= $db_connection->Record["page_id"];
		$pageInfo["title"] 		= $db_connection->Record["title"];
		$pageInfo["template"] 		= $db_connection->Record["template"];
		$pageInfo["homepage"] 		= $db_connection->Record["homepage"];
		$pageInfo["active"] 		= $db_connection->Record["active"];
		$pageInfo["type"] 	= $db_connection->Record["type"];
		$pageInfo["created"] 	= $db_connection->Record["created"];
		$pageInfo["modified"] 	= $db_connection->Record["modified"];
		$pageInfo["user"] 	= $db_connection->Record["user"];
	}
	
	return $pageInfo;
}

## =======================================================================        
## page_setPageName       
## =======================================================================        
## sets the page name in the USER_PAGES table
##    
## =======================================================================
function page_setPageName($vID,$vPageName) {
	global $Auth;
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## convert the text
	$vPageName = convert_html($vPageName);
	
	$db_connection = new DB_Sql();
	$query = "UPDATE ".USER_PAGES." SET title='$vPageName', modified=now() WHERE page_id='$vID' AND client_id='$client_id'";
	$result = $db_connection->query($query);
}


## =======================================================================        
##  template_showTemplate     
## =======================================================================        
##  displays the used template for a certain page 
##
##  TODO: needs error checking
## ======================================================================= 
function page_showPageInfo($page_id) {
	global $gSession,$Auth;

	## first we get the information:
	$templateInfo = template_getTemplate($page_id);
	$structureInfo = structure_getStructureID($page_id);
	$pageInfo = page_getPageInfo($page_id);

	$created = $pageInfo['created'];
	$modified = $pageInfo['modified'];
	$page_id = $pageInfo['page_id'];
	$page_name = $structureInfo['text'];
	$template_name = $templateInfo['title'];
	$template_basename = $templateInfo['basename'];

	## now output the information
	$select_template = new Template(INTERFACE_DIR);
	$select_template->set_templatefile(array("header" => "templateinfo.tpl","body" => "templateinfo.tpl","simplebody" => "templateinfo.tpl","footer" => "templateinfo.tpl"));

	##$select_template->set_var("saveIMG","lang/".$Auth->auth["language"]."_button_save.gif");	
	$select_template->set_var('language_pagename',LANG_PageInfo);
	$select_template->set_var('language_pagenamedesc',LANG_PageInfoDesc);
	$select_template->pfill_block("header");

	$select_template->set_var('language_pagename',LANG_PageName);
	$select_template->set_var('text','<b>'.$page_name.'</b>');
	$select_template->pfill_block("body");

	$select_template->set_var('language_pagename',LANG_PageInfo_Created);
	$select_template->set_var('text','<i>'.$created.'</i>');
	$select_template->pfill_block("simplebody");	

	$select_template->set_var('language_pagename',LANG_PageInfo_Modified);
	$select_template->set_var('text','<i>'.$modified.'</i>');
	$select_template->pfill_block("simplebody");

	$select_template->set_var('language_pagename','Author:');
	$select_template->set_var('text','<i>'.$pageInfo['user'].'</i>');
	$select_template->pfill_block("simplebody");

	$select_template->set_var('language_pagename',LANG_PageInfo_PageId);
	$select_template->set_var('text','<i>'.$page_id.'</i>');
	$select_template->pfill_block("body");
	
	$select_template->set_var('language_pagename',LANG_PageInfo_URL);
	$select_template->set_var('text',getTargetUrl($page_id));
	$select_template->pfill_block("simplebody");

	$select_template->set_var('language_pagename',LANG_PageInfo_Template);
	$select_template->set_var('text','<b>'.$template_name.'</b>');
	$select_template->pfill_block("body");
	
	$select_template->set_var('language_pagename','');
	$select_template->set_var('text','<i>'.$template_basename.'</i>');
	$select_template->pfill_block("simplebody");
	
	$select_template->pfill_block("footer");
}


## =======================================================================        
##  template_showTemplate     
## =======================================================================        
##  displays the used template for a certain page 
##
##  TODO: needs error checking
## ======================================================================= 
function page_showPageTimer($page_id,$edit_element) {
	global $gSession,$Auth;
	
	## prepare the template
	$select_template = new Template(INTERFACE_DIR);
	$select_template->set_templatefile(array("new" => "timer.tpl","open" => "timer.tpl","closed" => "timer.tpl","header" => "timer.tpl","footer" => "timer.tpl"));

	$select_template->set_var('language_pagename',LANG_PageInfo);
	$select_template->set_var('language_pagenamedesc',LANG_PageInfoDesc);
	$select_template->pfill_block("header");
		
	## first check if we already have events assigned to this page
	$events = event_getPageEvents($page_id);

	## check if we have any events
	if(!empty($events)) {
		## okay we need to loop through all events- in order to display them
		foreach($events as $current_event) {
			## prepare the edit url
			$editURL = 'admin.php?op=editTimer&page_id='.$page_id.'&edit='.$current_event['id'];
			$editURL = $gSession->url($editURL);
			$select_template->set_var('editURL',$editURL);
			
			## general settings
			$select_template->set_var('ID',$current_event['id']);
			
			## check if the current element is open for editing
			if($current_event['id'] == $edit_element) {
				## okay we need to preare all the data for the edit mode
				$closeURL = 'admin.php?op=editTimer&page_id='.$page_id;
				$closeURL = $gSession->url($closeURL);
				$select_template->set_var('closeURL',$closeURL); 
			
				## prepare the storeURL
				$storeURL = 'admin.php?op=storeEvent&page_id='.$page_id;
				$storeURL = $gSession->url($storeURL);
				$select_template->set_var('storeURL',$storeURL); 				
			
				$select_template->pfill_block("open");
			} else {
				$select_template->pfill_block("closed");
			}
			
			
		}
	}
	
	## in order to allow the user to add a new element- we will finally display the element
	## to create a new event
		
	## here we prepare the available events
	$available_events = event_getAvailableEvents();
	$event_output = '';
	foreach($available_events as $code=>$name) {
		$event_output .= '<option label="'.$name.'" value="'.$code.'">'.$name.'</option>';
	}	
	$select_template->set_var('EVENTS',$event_output); 
	
	$select_template->pfill_block("new");
	$select_template->pfill_block("footer");
}
?>
