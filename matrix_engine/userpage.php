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
##  display_template_list        
## =======================================================================        
##  displays the page which allows the user to select a template   
##
##  TODO:
##       - cheeck the code- optimize
## ======================================================================= 
function display_template_list($parent=0) {
	global $gSession,$Auth;
			 
	## prepare the template file
	$select_template = new Template(INTERFACE_DIR);
	$select_template->set_templatefile(array("header" => "selectlayout.tpl","body" => "selectlayout.tpl","footer" => "selectlayout.tpl"));

	$actionURL = "admin.php";
	$actionURL = $gSession->url($actionURL);
	$select_template->set_var('actionURL',$actionURL);
	
	## prepare the language stuff
	$select_template->set_var("saveIMG","lang/".$Auth->auth["language"]."_button_save.gif");
	$select_template->set_var('language_selectlayouthead',LANG_SelectLayout);
	$select_template->set_var('language_selectlayoutbody',LANG_SelectLayoutDescription);
	$select_template->set_var('language_selectlayouttitle',LANG_SelectLayoutTitle);	
	## flush the header
	$select_template->pfill_block("header");
	
	## we need to get all templates-
	$templates = template_getTemplateList($parent);
	
	foreach($templates as $current_template) {
		## now we set the vars for this row
		$select_template->set_var('title',$current_template['title']);		
		if($current_template['icon']!="-1") {
			$select_template->set_var('icon','../layout/icons/'.$current_template['icon']);
		} else {
			$select_template->set_var('icon','interface/images/blank.gif');
		}
		$select_template->set_var('description',$current_template['description']);
		$select_template->set_var('template_id',$current_template['template_id']);
		## and flush this row
		$select_template->pfill_block("body");
	}	
		
	## here we initialize the hiddenfields	
	$output =  '<input type="hidden" name="op" value="editpage">';
	$output .= '<input type="hidden" name="parent" value="'.$parent.'">';
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
function display_input_form($template_id,$page_id,$mode) {
	global $gSession,$Auth;
	global $input_language;	

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
	$templateInfo 	= template_getTemplate($page_id);
	$filename 		= $templateInfo['basename'];
	$template_id 	= $templateInfo['template_id']; 
	
	## get the page info
	$pageInfo = structure_getStructureID($page_id);

	$xmlFile  = $filename.".xml";
	$inputFile = $filename."i.tpl";
	$filename = $filename.".tpl";
	
	## okay let's use this file, as a template
	## prepare the template file- we changed to structure of the template file
	$inputFile = "master_input.tpl";
	$input_template = new Template(INTERFACE_DIR);
	$input_template->set_templatefile(array("head" => $inputFile,"intro" => $inputFile,"signoff_foot" => $inputFile,"foot" => $inputFile));

	## languages
	$languageTabs = language_generateTabs($input_language);
	$input_template->set_var('LANGUAGENAV',$languageTabs);	

	## helper texts
	
	## depending on the access rights- we need to display either two buttons
	## a) save as draft b) request approval
	$input_template->set_var("draftIMG","lang/".$Auth->auth["language"]."_button_approval.gif");
	$input_template->set_var("saveIMG","lang/".$Auth->auth["language"]."_button_save.gif");
	
	## genertae the url
	$approvalURL = "admin.php?op=requestApproval&page_id=".$page_id;
	$approvalURL = $gSession->url($approvalURL);
	
	$input_template->set_var('language_deletelementdesc',LANG_DeleteElementDescription);
	$input_template->set_var('language_inputhead',LANG_EnterData.': '.htmlentities($pageInfo['text']));
	$input_template->set_var('language_inputbody',LANG_EnterDataDescription);
	
	$actionURL = "admin.php";
	$actionURL = $gSession->url($actionURL);
	$input_template->set_var('actionURL',$actionURL);
	

	## here we initialize the hiddenfields	
	$output =  '<input type="hidden" name="language" value="'.$input_language.'">';
	$output .=  '<input type="hidden" name="save_language" value="'.$input_language.'">';
	$output .=  '<input type="hidden" name="op" value="store">';
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
	
	## if we don't have admin rgihts, we should not display the approval button
	$access_rights = _getUserAccessRights($Auth->auth['user_id']);

	if(isset($access_rights['pages']['no_signoff'])) {
		$input_template->pfill_block("signoff_foot");
	} else {
		$input_template->pfill_block("foot");
	}
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
	$output = '';
	$header = array();

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
## display_namepage       
## =======================================================================        
## displays the dialog for naming the page
##    
## =======================================================================
function display_namepage($menu_id, $menu_text,$actionURL) {
	global $gSession,$Auth;
	## prepare the template file
	$select_template = new Template(INTERFACE_DIR);
	$select_template->set_templatefile(array("header" => "namepage.tpl","body" => "namepage.tpl","footer" => "namepage.tpl"));

	$select_template->set_var("saveIMG","lang/".$Auth->auth["language"]."_button_save.gif");
	$select_template->set_var('language_pagename',LANG_PageName);
	$select_template->set_var('language_pagenamedesc',LANG_PageNameDescription);

	$select_template->set_var('menu_text',$menu_text);
	$select_template->set_var('actionURL',$actionURL);
	## okay now we start the ouput
	$select_template->pfill_block("header");	
	$select_template->pfill_block("body");
	

	$output  = '<input type="hidden" name="menu_id" value="'.$menu_id.'">';
	$output .= '<input type="hidden" name="op" value="update_page_menu">'; 
		
	$select_template->set_var('hiddenfields',$output);
	
	$select_template->pfill_block("footer");
}

function update_page_menu($menu_id, $menu_text) {	
	## before we insert the data into the db
	## we have to make sure we have valid data
	
	## save the page name into user_pages
	$page_record = structure_getPage($menu_id);
	page_setPageName($page_record["page_id"],htmlentities($menu_text));
	
	## save the page name in the structure
	structure_setPageName($menu_id,$menu_text);
}

?>
