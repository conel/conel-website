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
##  portlets_displayTemplates      
## =======================================================================        
##  displays a list of templates to be selected by the user
##  submit a comma-seperated var with the templates
##   
## =======================================================================
function box_displayTemplates($page_id,$identifier,$templates = null) {
	global $Auth, $gSession,$input_language;
	## multi-client
	$client_id = $Auth->auth["client_id"];	
	
	## define error code
	$error_code = 1;
	
	## get the template list
	$template_list = _getTemplateList($templates);	
	if(count($template_list) == 1) {
		## how to?
	} 

	## get the template for now
	$select_template = new Template('interface/');
	$select_template->set_templatefile(array("header" => "selectlayout.tpl","body" => "selectlayout.tpl","footer" => "selectlayout.tpl"));

	$actionURL = "editor.php";
	$actionURL = $gSession->url($actionURL);
	$select_template->set_var('actionURL',$actionURL);
	
	## prepare the language stuff
	$select_template->set_var("saveIMG","lang/".$Auth->auth["language"]."_button_continue.gif");
	$select_template->set_var('language_selectlayouthead',LANG_BOX_SelectLayout);
	$select_template->set_var('language_selectlayoutbody',LANG_SelectLayoutDescription);
	$select_template->set_var('language_selectlayouttitle',LANG_BOX_Title);	

		
	## flush the header
	$select_template->pfill_block("header");
		
	$output = '<select name="templateID" size="1">';
	foreach($template_list as $current_template) {
		## now we set the vars for this row
		$output .= '<option label="'.$current_template['name'].'" value="'.$current_template['id'].'">'.$current_template['name'].'</option>';
	}	
	$output .= '</select>';	
	$select_template->set_var('value',$output);
	$select_template->pfill_block("body");
		
	## here we initialize the hiddenfields	
	$output =  '<input type="hidden" name="op" value="editpage">';
	$output .= '<input type="hidden" name="identifier" value="'.$identifier.'">';
	$output .= '<input type="hidden" name="language" value="'.$input_language.'">';	
	$output .= '<input type="hidden" name="mode" value="create">';
	$output .= '<input type="hidden" name="page_id" value="'.$page_id.'">';
	$output .= '<input type="hidden" name="Session" value="'.$gSession->id.'">';
	$select_template->set_var("hiddenfields",$output);
	## and finally flush the footer
	$select_template->pfill_block("footer");
	
	## finally we return the positive value
	return $error_code;
}

## =======================================================================        
##  box_getBoxInfo        
## =======================================================================        
##  create the box_object iof it doesn't exists.
##  return the box id anyways   
##
##  TODO:
##       - make all pages by default inactive
## =======================================================================        
function box_getBoxInfo($boxID,$box_item_id) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	$db_connection = new DB_Sql();  
	
	$boxInfo = array();
	## get the info for the boxID supplied
	$query = "SELECT * FROM ".DB_PREFIX."box_item WHERE box_id = '$boxID' AND box_item_id='$box_item_id' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($query);	

	while($db_connection->next_record()) {
		$boxInfo['box_item_id'] 	= $db_connection->Record['box_item_id'];
		$boxInfo['target'] 		= $db_connection->Record['target'];
		$boxInfo['item_order'] 	= $db_connection->Record['item_order'];
	}
	return $boxInfo;
}

## =======================================================================        
##  _getTemplateList    
## =======================================================================        
##  returns the list of templates
##   
## =======================================================================
function _getTemplateList($templates = null) {
	global $Auth;
	## multi-client
	$client_id = $Auth->auth["client_id"];	
	
	$template_list		= array();
	
	if(!isset($templates) || $templates == "") {
		return $template_list;		## no template provided
	} 
	
	## okay we need to split it up
	$templates = explode(",", $templates);

	## now we need to get the db entries for these templates
	$template_query="";	
	foreach ($templates as $current_template) {
		if($template_query != "") {
			$template_query .= " OR ";
		} 
		$template_query .= "basename ='$current_template'";
	}	
	
	## db_object
	$db = new DB_Sql();
	
	## now request the templates form the db
	$select_query   = "SELECT template_id,title FROM ".PAGE_TEMPLATE." WHERE (".$template_query.") AND client_id=".$client_id;
	$result_pointer = $db->query($select_query);	

	$template_counter = 0;
	while($db->next_record()) {
		## get the ids and names an prepare them for ouput
		$template_list[$template_counter]['id'] 	= $db->Record["template_id"];
		$template_list[$template_counter]['name'] 	= $db->Record["title"];
		$template_counter++;
	} 
	return $template_list;
}

## =======================================================================        
##  box_storePage        
## =======================================================================        
##  we store the base information for a certain template    
##
##  TODO:
##       - make all pages by default inactive
## =======================================================================        
function box_storePage($title, $template) {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## prepare the db-object
	$db_connectionStore = new DB_Sql();

	$lock_query = "lock table ".USER_PAGES." write";
	$result_pointer = $db_connectionStore->query($lock_query);
	
	$insert_query = "insert into ".USER_PAGES." (title, template, type, client_id) values ('$title', '$template','box','$client_id')";
	$result_pointer = $db_connectionStore->query($insert_query);	
	$page_id = $db_connectionStore->db_insertid($result_pointer);
	
	$lock_query = "unlock table";
	$result_pointer = $db_connectionStore->query($lock_query);
	
	return $page_id;
}


## =======================================================================        
##  portlets_dispayInputForm        
## =======================================================================        
##  returns an array with all pages of a certain category    
##
##  TODO:
##       - make all pages by default inactive
## =======================================================================        
function box_displayInputForm($page_id,$templateID) {
	global $Auth,$gSession,$input_language;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## ======================================================================= 
	## some datatypes require additonal information so we need to setup some global vars
	## -> we need to handle this later
	## ======================================================================= 
	global $g_pageID;
	$g_pageID	= $page_id;	

	## okay let's use this file, as a template
	## prepare the template file- we changed to structure of the template file
	$inputFile = "master_input.tpl";
	$input_template = new Template('interface/');
	$input_template->set_templatefile(array("head" => $inputFile,"intro" => $inputFile,"foot" => $inputFile));

	## language
	$input_template->set_var("saveIMG",$Auth->auth["language"]."_button_save.gif");
	$input_template->set_var('language_deleteelementdesc',LANG_DeleteElementDescription);
	$input_template->set_var('language_inputhead',LANG_EnterData);
	$input_template->set_var('language_inputbody',LANG_EnterDataDescription);
	
	$actionURL = "editor.php";
	$actionURL = $gSession->url($actionURL);
	$input_template->set_var('actionURL',$actionURL);
		
	## here we initialize the hiddenfields	
	$output =  '<input type="hidden" name="op" value="store">';
	$output .= '<input type="hidden" name="templateID" value="'.$templateID.'">';
	$output .= '<input type="hidden" name="language" value="'.$input_language.'">';	
	$output .= '<input type="hidden" name="pageID" value="'.$page_id.'">';
	$output .= '<input type="hidden" name="Session" value="'.$gSession->id.'">';
	$input_template->set_var("hiddenfields",$output);

	
	## first we get the template info
	$templateInfo 	= template_getTemplate($page_id);
	
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
##  box_deletItem      
## =======================================================================        
##  deletes the selected item- we should check if we also need to delete
##  the box main object
##
## ======================================================================= 
function box_deletItem($linklistID,$vItemID) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];	

	## db connection
	$db_connection = new DB_Sql();  
					
	## first we need to find out the page that is associated with the box
	$query = "SELECT target FROM ".DB_PREFIX."box_item WHERE box_id = '$linklistID' AND box_item_id='$vItemID' AND client_id='$client_id'";	
	$result_pointer = $db_connection->query($query);
	$db_connection->next_record();
	$target_page = $db_connection->Record['target'];			
					
	## let's delete the item
	$query = "DELETE FROM ".DB_PREFIX."box_item WHERE box_id='$linklistID' AND box_item_id='$vItemID' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($query);
	
	return $target_page;
}

## =======================================================================        
##  linklist_promptDelete        
## =======================================================================        
function box_promptDelete($yesURL,$noURL) {
	global $gSession,$Auth;
	$db_connectionLayout = new DB_Sql();  

	## prepare the template file
	$select_template = new Template(ENGINE."datatypes/linklist/interface");
	$select_template->set_templatefile(array("body" => "deletelink.tpl"));
	
	$select_template->set_var("yesIMG","lang/".$Auth->auth["language"]."_button_ja.gif");
	$select_template->set_var("noIMG","lang/".$Auth->auth["language"]."_button_nein.gif");
	$select_template->set_var('language_deletepage',LANG_LINKLIST_DeleteTitle);
	$select_template->set_var('language_doyouwant',LANG_LINKLIST_DeleteDesc);
	
	## grab the information for this page
  		
	$select_template->set_var('yesURL',$yesURL);
	$select_template->set_var('noURL',$noURL);
	
	$select_template->pfill_block("body");
}	

## =======================================================================        
##  linklist_displayInputForm      
## =======================================================================        
##  displays the input form for a certain entry  
##
## ======================================================================= 	
function box_sort_displayInputForm() {
	global $gSession, $Auth;
	
	$output = "";
	
	## check the params
	if(isset($_GET['page_id'])) {
		$page_id 	= $_GET['page_id'];
		$identifier	= $_GET['identifier'];
		$linklistID	= $_GET['linklistID'];
		$subcmd		= $_GET['subcmd'];
		$lowsub		= $_GET['lowsub'];
	} else {
		$page_id 	= $_POST['page_id'];
		$identifier	= $_POST['identifier'];
		$linklistID	= $_POST['linklistID'];
		$subcmd		= $_POST['subcmd'];
		$lowsub		= $_POST['lowsub'];
	}
	## template related stuff
	$template = new Template(ENGINE."datatypes/box/interface/");
	$template->set_templatefile(array("head" => "sort.tpl","linklist" => "sort.tpl","linklist_row" => "sort.tpl","foot" => "sort.tpl"));	
	
	## we need to get the xml-data of the root page
	$root_template = template_getTemplate($page_id);
	
	$filename	= $root_template["basename"];
	$xmlFile  	= $filename.".xml";
	$filename 	= $filename.".tpl";	
	
	$wt = new xmlparser(HTML_DIR.$xmlFile);
	$wt->parse();
	
	## okay we scanned in the xml file- so we now loop through all the elements
	$elements = $wt->getElements();	
	$objects = $wt->getObjects();	
	
	## we should loop through the results to find out our item
	$xmldata = array();
	foreach($elements as $current_element) {
		if(($current_element['TYPE'] == "BOX") && ($current_element['NAME'] == $identifier)) {
			$xmldata = $current_element;
		}
	}
	
	$options = _box_generateOptions($xmldata['ENTRY']);	
	$entries = _box_generateEntries($xmldata['ENTRY']);		

	## data conatiner
	$data = array();	
	## get the infos
	box_getData($page_id,$data);

	$data = $data[$identifier];
	$items = $data['length'];

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
		
		if($description != " " && $description !="") {
			$template->set_var('decription',$description);
		} else {			
			## display the page title an the id number
			$decription = $data[$i]["item_id"].':'.$data[$i]["target"];							
			$template->set_var('decription',$decription);
		}	

		$template->set_var('linkID',$data[$i]['item_id']);	
		$template->set_var('order',$data[$i]['order']);		
		$output .= $template->fill_block("linklist_row");
	}
	
	## prepare the data
	$data = $data[$identifier];
	$template->set_var("saveIMG","../../interface/lang/".$Auth->auth["language"]."_button_save.gif");
	$template->set_var('Title',LANG_LINKLIST_SortTitle);
	$template->set_var('Desc',LANG_LINKLIST_SortDesc);
	
	$actionURL = "editor.php?op=doSort";
	$actionURL = $gSession->url($actionURL);
	$template->set_var('actionURL',$actionURL);
	
	## output the header
	$template->pfill_block("head");
	$template->pfill_block("linklist");	

	## we should set all required vars here
	$template->set_var('op',"doSort");
	$template->set_var('Session',$gSession->id);
	$template->set_var('language',$input_language);	
	$template->set_var('page_id',$page_id);
	$template->set_var('identifier',$identifier);
	$template->set_var('linklistID',$linklistID);
	$template->set_var('subcmd',$subcmd);
	$template->set_var('lowsub',$lowsub);

	## finally set the close url
	$template->set_var('closeOP',"closeEditor");
	
	print $output; 
	
	$template->pfill_block("foot");
} 
	

## =======================================================================        
##  linklist_sort_setItemOrder     
## =======================================================================        
##  actually moves the position of an item 
##
## ======================================================================= 	
function box_sort_setItemOrder($vLinklistID,$vLinkID,$vOrder,$vMoveBy) {	
	global $gSession;
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];	

	## db connection
	$db_connection = new DB_Sql();
	
	$select_query = "select max(item_order) FROM ".DB_PREFIX."box_item WHERE box_id='$vLinklistID' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($select_query);
	$db_connection->next_record();
	list($max_order) = $db_connection->Record;		

	if($vMoveBy == 1) {
		$updated_order = $vOrder-1;
    	if ($updated_order < 0) {
      			$updated_order = 0;
    	}

		## next we should check if there is an item with that order
		$query = "SELECT box_item_id FROM ".DB_PREFIX."box_item WHERE item_order='$updated_order' AND box_id=".$vLinklistID." AND client_id=".$client_id;
		$db_result = $db_connection->query($query);
		$db_connection->next_record();
		
		$id_old_order = $db_connection->Record["box_item_id"];

		$element_counter = $db_connection->num_rows();

		if($max_order <=0) {
			$max_order = $element_counter;
		}
		
		$menu_order = $updated_order+1;
    	if ($menu_order > $max_order) {
      		$menu_order = $max_order;
   		}		
    }

	if ($vMoveBy == 0) {
		$updated_order = ($vOrder + 1);
    	if ($updated_order > $max_order){
      		$updated_order = $max_order;
    	}

		## next we should check if there is an item with that order
		$query = "SELECT box_item_id FROM ".DB_PREFIX."box_item WHERE item_order='$updated_order' AND box_id=".$vLinklistID." AND client_id=".$client_id;
		$db_result = $db_connection->query($query);
		$db_connection->next_record();
		
		$id_old_order = $db_connection->Record["box_item_id"];
		$element_counter = $db_connection->num_rows();
				    	
    	$menu_order = ($updated_order - 1);
    	if ($menu_order < 0){
      		$menu_order = 0;
    	}

	}
  
  	$update_query = "UPDATE ".DB_PREFIX."box_item SET item_order='$menu_order' WHERE box_item_id='$id_old_order' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($update_query);
   		
   	$update_query = "UPDATE ".DB_PREFIX."box_item SET item_order='$updated_order' WHERE box_item_id='$vLinkID' AND client_id='$client_id'";
   	$result_pointer = $db_connection->query($update_query);
}
?>
