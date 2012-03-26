<?php

## =======================================================================        
##  newsletter_pageConnect2Newsletter       
## =======================================================================        
##  links a certain page to a newsletter
##
## ======================================================================= 
function newsletter_pageConnect2Newsletter($id,$page_id) {
	global $Auth,$gSession;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## prepare the db-object
	$db_connection = new DB_Sql();

	## first get the page id for this newsletter
	$query = "UPDATE ".DB_PREFIX."newsletter SET page_id='$page_id' WHERE id='$id'";
	$rp = $db_connection->query($query);		
}

## =======================================================================        
##  newsletter_pageDisConnect2Newsletter       
## =======================================================================        
##  links a certain page to a newsletter
##
## ======================================================================= 
function newsletter_pageDisConnect2Newsletter($id) {
	global $Auth,$gSession;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## prepare the db-object
	$db_connection = new DB_Sql();

	## first get the page id for this newsletter
	$query = "UPDATE ".DB_PREFIX."newsletter SET page_id='0' WHERE id='$id'";
	$rp = $db_connection->query($query);		
}

## =======================================================================        
##  newsletter_pageDisplayStatusOverview        
## =======================================================================        
##  this function should return the status of this object. 
## 	the status of the page object could be that: a) no content was entered
## 	or that a certain content has beend entered (show when, and the approx size)
##
##  TODO:
##  
## ======================================================================= 
function newsletter_pageDisplayStatusOverview($id,$status) {
	global $Auth,$gSession;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## prepare the db-object
	$db_connection = new DB_Sql();

	## first get the page id for this newsletter
	$query = "SELECT page_id,name FROM ".DB_PREFIX."newsletter WHERE id='$id'";
	$rp = $db_connection->query($query);
	$db_connection->next_record();
	
	$page_id = $db_connection->Record["page_id"];
	$name = $db_connection->Record["name"];
	
	$select_template = new Template('interface/');
	$select_template->set_templatefile(array("page" => "page_overview.tpl","nopage" => "page_overview.tpl","send" => "page_overview.tpl"));
	$select_template->set_var("LANG",$Auth->auth["language"]);	
	
	$select_template->set_var("LANG_MODULE_Newsletter_ContentTitle",LANG_MODULE_Newsletter_ContentTitle);	
	$select_template->set_var("LANG_MODULE_Newsletter_ContentTitleDesc",LANG_MODULE_Newsletter_ContentTitleDesc);	
	
	if($status == 2) {
		## first try to estimate the size of the page
		$newsletter = newsletter_pageGeneratePage($page_id);
		$newsletter = _formatFileSize(strlen($newsletter));
		
		## page found
		## prepare the action url
		$actionURL = 'page.php?op=editpage&id='.$id.'&page_id='.$page_id;
		$actionURL = $gSession->url($actionURL);
		$select_template->set_var("actionURL",$actionURL);
		
		$previewURL = 'page.php?op=preview&page_id='.$page_id;
		$previewURL = $gSession->url($previewURL);
		$select_template->set_var("previewURL",$previewURL);
		
		$deleteURL = 'page.php?op=delete&id='.$id.'&page_id='.$page_id;
		$deleteURL = $gSession->url($deleteURL);
		$select_template->set_var("deleteURL",$deleteURL);
						
		
		$select_template->set_var("name",$name);
		$select_template->set_var("size",$newsletter);
			
		return $select_template->fill_block("send");		
	}
	
	if($page_id > 0) {
		## okay we already have a page
		
		## first try to estimate the size of the page
		$newsletter = newsletter_pageGeneratePage($page_id);
		$newsletter = _formatFileSize(strlen($newsletter));
		
		## page found
		## prepare the action url
		$actionURL = 'page.php?op=editpage&id='.$id.'&page_id='.$page_id;
		$actionURL = $gSession->url($actionURL);
		$select_template->set_var("actionURL",$actionURL);
		
		$previewURL = 'page.php?op=preview&page_id='.$page_id;
		$previewURL = $gSession->url($previewURL);
		$select_template->set_var("previewURL",$previewURL);
		
		$deleteURL = 'page.php?op=delete&id='.$id.'&page_id='.$page_id;
		$deleteURL = $gSession->url($deleteURL);
		$select_template->set_var("deleteURL",$deleteURL);
						
		
		$select_template->set_var("name",$name);
		$select_template->set_var("size",$newsletter);
			
		return $select_template->fill_block("page");	
	} else {
		## new page needs to be created;
		## prepare the action url
		$actionURL = 'page.php?op=create&id='.$id;
		$actionURL = $gSession->url($actionURL);
		$select_template->set_var("actionURL",$actionURL);
		return $select_template->fill_block("nopage");		
	}		
}

## =======================================================================        
##  newsletter_pageGetTemplateList        
## =======================================================================        
##  get's all templates or some if the parent flag is set
##
##  NEW:
##  	- checks if the flag hiddenor notselectable are set
## ======================================================================= 
function newsletter_pageGetTemplateList() {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## prepare the db-object
	$db_connection = new DB_Sql();
	
	## we should get them all		
	$select_query = "SELECT title,icon,template_id,description,basename FROM ".DB_PREFIX."newsletter_template WHERE client_id='$client_id' ORDER BY title";
	$result_pointer = $db_connection->query($select_query);
	
			
	## we must have found somthing by this time:
	## so start processing
	$templates = array();
	$counter   = 0;
	while($db_connection->next_record()) {
		## get the data
		$templates[$counter]['title'] 		= $db_connection->Record["title"];
		$templates[$counter]['icon']		= $db_connection->Record["icon"];
		$templates[$counter]['template_id'] = $db_connection->Record["template_id"];
		$templates[$counter]['description'] = $db_connection->Record["description"];
		$templates[$counter]['basename'] 	= $db_connection->Record["basename"];
		
		$counter++;
	}

	return $templates;		
}

## =======================================================================        
##  newsletter_displayTemplateList        
## =======================================================================        
##  displays the page which allows the user to select a template   
##
##  TODO:
##       - cheeck the code- optimize
## ======================================================================= 
function newsletter_displayTemplateList($templates,$newsletter_id) {
	global $gSession,$Auth;
			 
	## prepare the template file
	$select_template = new Template(INTERFACE_DIR);
	$select_template->set_templatefile(array("header" => "selectlayout.tpl","body" => "selectlayout.tpl","footer" => "selectlayout.tpl"));

	$actionURL = "page.php";
	$actionURL = $gSession->url($actionURL);
	$select_template->set_var('actionURL',$actionURL);
	
	## prepare the language stuff
	$select_template->set_var("saveIMG","lang/".$Auth->auth["language"]."_button_save.gif");
	$select_template->set_var('language_selectlayouthead',LANG_SelectLayout);
	$select_template->set_var('language_selectlayoutbody',LANG_SelectLayoutDescription);
	$select_template->set_var('language_selectlayouttitle',LANG_SelectLayoutTitle);	
	## flush the header
	$select_template->pfill_block("header");
		
	$counter = 0;
	foreach($templates as $current_template) {
		## now we set the vars for this row
		$select_template->set_var('title',$current_template['title']);		
		if($current_template['icon']!="-1" && !empty($current_template['icon']) ) {
			$select_template->set_var('icon','../../../layout/icons/'.$current_template['icon']);
		} else {
			$select_template->set_var('icon','../../interface/images/blank.gif');
		}
		$select_template->set_var('description',$current_template['description']);
		$select_template->set_var('template_id',$current_template['template_id']);
		
		
		if($counter == 0) {
			$select_template->set_var('status','checked');
		} else {
			$select_template->set_var('status','');
		}
			
		## and flush this row
		$select_template->pfill_block("body");
		$counter++;
	}	
		
	## here we initialize the hiddenfields	
	$output =  '<input type="hidden" name="op" value="storetemplate">';
	$output .= '<input type="hidden" name="id" value="'.$newsletter_id.'">';
	$output .= '<input type="hidden" name="mode" value="create">';
	$output .= '<input type="hidden" name="Session" value="'.$gSession->id.'">';
	$select_template->set_var("hiddenfields",$output);
	## and finally flush the footer
	$select_template->pfill_block("footer");
}


## =======================================================================        
##  display_input_form        
## =======================================================================        
##  displays the input form for the page and template ids submited   
##
## 
## ======================================================================= 
## this will be a complete re-write of this function.
## we need to display the input form. All we need is
## the template_id and a page_id if available and the current 
## MODE we are in.
##
## we first have to re-write the page creation process
## 
## ======================================================================= 
function newsletter_pageDisplayInputForm($template_id,$page_id,$mode) {
	global $gSession,$Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	if(!$page_id) {
		## we should return an error- how?
		## don't know yet
		exit();
	}

	## ======================================================================= 
	## some datatypes require additonal information so we need to setup some global vars
	## -> we need to handle this later
	## ======================================================================= 
	global $g_pageID;
	$g_pageID	= $page_id;

	## setup up is complete, we can start processing the rest
	## ======================================================================= 
	
	## we don't know for sure, if we get the template id, ut we always get
	## the page_id so we should just get the required template info via the page_id
	$templateInfo 	= newsletter_pageGetTemplate($page_id);
	$filename 		= $templateInfo['basename'];
	$template_id 	= $templateInfo['template_id']; 

	$xmlFile  = $filename.".xml";
	$inputFile = $filename."i.tpl";
	$filename = $filename.".tpl";
	
	## okay let's use this file, as a template
	## prepare the template file- we changed to structure of the template file
	$inputFile = "master_input.tpl";
	$input_template = new Template(INTERFACE_DIR);
	$input_template->set_templatefile(array("head" => $inputFile,"intro" => $inputFile,"foot" => $inputFile));

	## helper texts
	$input_template->set_var("saveIMG","lang/".$Auth->auth["language"]."_button_save.gif");
	$input_template->set_var('language_deletelementdesc',LANG_DeleteElementDescription);
	$input_template->set_var('language_inputhead',LANG_EnterData);
	$input_template->set_var('language_inputbody',LANG_EnterDataDescription);
	
	$actionURL = "page.php";
	$actionURL = $gSession->url($actionURL);
	$input_template->set_var('actionURL',$actionURL);


	## here we initialize the hiddenfields	
	$output =  '<input type="hidden" name="op" value="store">';
	$output .= '<input type="hidden" name="mode" value="'.$mode.'">';
	$output .= '<input type="hidden" name="templateID" value="'.$template_id.'">';
	$output .= '<input type="hidden" name="pageID" value="'.$page_id.'">';
	$output .= '<input type="hidden" name="Session" value="'.$gSession->id.'">';
	$input_template->set_var("hiddenfields",$output);

	$output = _generate_inputForms($templateInfo['basename'],$page_id);
	
	## set the datatype specific header elements
	$input_template->set_var('HEADER',$output['header']);
			
	## the next step is to ouput the head
	$input_template->pfill_block("head");
	$input_template->pfill_block("intro");
	
	
	print $output['body'];

	
	$input_template->pfill_block("foot");
}

## =======================================================================        
##  _generate_inputForms       
## =======================================================================        
## this is a general helper function, it can be called by various
## other functions- but it's meinly called from the "display_input_form"
## reads the provided xml-file and generates a form for the datatypes
##
##  TODO:
##       - check if it works!
## =======================================================================
function _generate_inputForms($basename,$page_id) {
	global $gSession,$Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	$xmlFile  = $basename.".xml";
	$filename = $basename.".tpl";
	
	## okay let's use this file, as a template prepare the template file- 
	$inputFile = "master_input.tpl";
	$input_template = new Template(INTERFACE_DIR);
	$input_template->set_templatefile(array("divider" => $inputFile));
	
	## parse the xml-data file
	$wt = new xmlparser(HTML_DIR.$xmlFile);
	$wt->parse();

	$elements 	= $wt->getElements();	
	$objects 	= $wt->getObjects();	
	
	## we should get the page content
	$page_record = page_getPage($page_id,$objects);	
	
	$counter = 0;
	$output = "";
	while($counter < count($elements)-1) {
		## okay first we try to find out what type we have
		$element_type = $elements[$counter]['TYPE'];
		$element_name = $elements[$counter]['NAME'];
		$element_desc = isset($elements[$counter]['DESC']) ? $elements[$counter]['DESC'] : '';
		$element_data = isset($page_record[$element_name]) ? $page_record[$element_name] : array();
		
		$input_template->set_var('element_desc',$element_desc);

		switch($element_type) {
			case 'DIVIDER': {
				if($elements[$counter]['TAG']) {
					$input_template->set_var('element_tag',$elements[$counter]['TAG']);
				}
				$output .= $input_template->fill_block("divider");
				
				break;
			}
			case 'LINK':
			case 'DATE':
			case 'IMAGE':
			case 'LINKLIST':
			case 'FILE':
			case 'BOX':
			case 'COPYTEXT':				
			case 'TEXT': {
				$target = strtolower($element_type); 
				eval("\$element = ".$target."_displayInput(\$elements[\$counter],\$element_data);");
				$output .= $element;
				break;
			}					
			default: {
				## unknown datatype- check if we have an extra for it
				$target = strtolower($element_type);
				## check for the file 
				@include_once("datatypes/extra_".$target."/".$target.".php");
				## check if the function exists
				if(function_exists($target."_displayInput")) {
					eval("\$element = ".$target."_displayInput(\$elements[\$counter],\$element_data);");
					if(is_array($element)) {
						$header[$target] = $element['header'];
						$output .= $element['body'];
					} else {
						$output .= $element;
					}
				}
				
				break;
			}				
												
		}
		$counter++;
	}
	$header = join("\n",$header);
	return array('header'=>$header,'body'=>$output);
}

## =======================================================================        
##  page_storePageContents        
## =======================================================================        
##  stores all information the user typed into the input form  
##	handles version control by generating a timestamp and passing
##	this timestamp to all datatypes. this way the timestamp for 
##  and update is consistant  
##
##  TODO:
##       - check if it works!
## ======================================================================= 
function newsletter_pageStoreContents() {
	
	## get the global pages	
	$page_id 		= $_POST["pageID"];
	$template_id 	= $_POST["templateID"];

	## update the mod-date of the current page
	page_updatePage($page_id);
		
	## get the template
	$templateInfo = newsletter_pageGetTemplate($page_id);
	$basename = $templateInfo["basename"];
	
	## parse the xml-file	
	$wt = new xmlparser(HTML_DIR.$basename.".xml");
	$wt->parse();
	$elements = $wt->getElements();

	$counter =0;
	while($counter < count($elements)) {
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
##  template_getTemplate      
## =======================================================================        
##  gets the Template Info via a page_id 
##
##  TODO: needs error checking
## ======================================================================= 
function newsletter_pageGetTemplate($page_id) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	$templateInfo = array();
	
	## database connection
	$db_connection = new DB_Sql();
	
	## grab the information for this page
	$query = "SELECT icon,description,basename,template_id, A.title FROM ".DB_PREFIX."newsletter_template AS A INNER JOIN ".USER_PAGES." ON A.template_id=".USER_PAGES.".template WHERE page_id = '$page_id' AND ".USER_PAGES.".client_id='$client_id'";
	$result_pointer = $db_connection->query($query);
	$db_connection->next_record();

	$templateInfo['icon'] = $db_connection->Record["icon"];
	$templateInfo['description'] = $db_connection->Record["description"];
	$templateInfo['basename'] = $db_connection->Record["basename"];
	$templateInfo['template_id'] = $db_connection->Record["template_id"];
	$templateInfo['title'] = $db_connection->Record["title"];
	$templateInfo['parent'] = $db_connection->Record["parent"];

	## return the templateInfo
	return $templateInfo;
}

## =======================================================================        
##  frontend_getPageInfo        
## =======================================================================        
##  mainly gets the page id. If it finds one, we get some additional data
##
##  TODO:       
## ======================================================================= 
function newsletter_getPageInfo($page_id=0,$client_id) {
	## init the page record
	$pageInfo = array();
	
	$dbConnection = new DB_Sql();
	$query = "SELECT modified, template FROM ".USER_PAGES." AS A WHERE A.page_id='$page_id' AND A.client_id='$client_id'";
	$result_pointer = $dbConnection->query($query);
	$dbConnection->next_record();

	$pageInfo["page_id"]	 = $page_id;
	$pageInfo["template_id"] = $dbConnection->Record["template"];
	$pageInfo["modified"]	 = $dbConnection->Record["modified"];
	
	return $pageInfo;
}	


## =======================================================================        
##  generate_page        
## =======================================================================        
##  generates a page identified by a page_id  
##
##  TODO:       
## ======================================================================= 
function newsletter_pageGeneratePage($page_id=0,$type='') {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];

	$output = '';
	$pageInfo = newsletter_getPageInfo($page_id,$client_id);
	
	$page_id 		= intval($pageInfo["page_id"]);
	$template_id	= $pageInfo["template_id"];
	$modified		= $pageInfo["modified"];
		
	## $menu_text 		= isset($pageInfo["text"]) ? $pageInfo["text"] : ''; ## this needs to become the ttitle of the newsletter
			
	$dbConnection = new DB_Sql();
				
	## grab the information for this page
	$select_query = "SELECT basename FROM ".DB_PREFIX."newsletter_template WHERE template_id='$template_id' AND client_id='$client_id'";
	$result_pointer = $dbConnection->query($select_query);
	
	$dbConnection->next_record();
	$filename = $dbConnection->Record["basename"];
	$xmlFile  = $filename.$type.".xml";
	$filename = $filename.$type.".tpl";
	
	if ($filename == ".tpl") {
		## maybe we can come with some good default behavior
		exit();
	}
			
	## prepare the template file
	$layout_template = new Template(HTML_DIR);
	$layout_template->set_templatefile(array("head" => $filename,"body" => $filename,"foot" => $filename)); 
	
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
	$num_elements = count($elements)-1;
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
					
					eval("\$element = output_".$target."(\$page_record[\$element_name],\$elements[\$counter],0,\$type);");	
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
				
				eval("\$element = output_".$target."('',\$elements[\$counter],0,$page_id);");	

				if(is_array($element)) {
					$layout_template->set_vars($element);
				} else {
					$layout_template->set_var($element_name,$element);
				}
				break;
			}								
			case 'LISTVIEW': {
				$storage = "";
				
				$storage = output_listview($page_record,$elements[$counter],$layout_template,0,$page_id); 
				$layout_template->set_var($element_name,$storage);	
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
					if(isset($page_record[$element_name])) {
						eval("\$element = ".$target."_output(\$page_record[\$element_name],\$elements[\$counter],0);");
					} else {
						##var_dump(eval();
						eval("\$element = ".$target."_output(\$page_record,\$elements[\$counter],\$layout_template,0,\$page_id);");
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
	##$output .= $layout_template->fill_block("foot");
	$output = $layout_template->finish($output);
	
	return $output;
}

## =======================================================================        
##  _formatFileSize        
## =======================================================================        
##  returns the filsize in bytes, kb, or mb
## ======================================================================= 
function _formatFileSize($size) {
	$suffix = '';
	
	## check if we got a number
	if(!is_numeric($size) || $size < 0) {
		return 0;
	}
	
	## now determine the 'level'
	for($level = 0; $size >= 1024; $level++) {
		$size /= 1024;
	}
	
	## now add the suffix
	switch($level) {
		case 0: $suffix = 'Bytes'; break;
		case 1: $suffix = 'KB'; break;
		case 2: $suffix = 'MB'; break;
		case 3: $suffix = 'GB'; break;
		default: $suffix = '';
	}
	
	return round($size,2) . ' '.$suffix;
}

?>