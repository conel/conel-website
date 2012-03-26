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
## copytext_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function copytext_displayInput($xmldata, $data) {
	## init the vars
	$return = "";
	
	## we should open our own template
	$template = new Template(ENGINE."datatypes/copytext/interface/");
	$template->set_templatefile(array("copytext" => "interface.tpl"));
	
	$element_lines   = intval($xmldata['LINES']);
	$element_columns = intval($xmldata['COLUMNS']);
	$element_maxchar = isset($xmldata['MAXCHAR']) ? intval($xmldata['MAXCHAR']) : null;	

	$element_lines 	 = (!$element_lines > 0) ? 10 : $element_lines;
	$element_columns 	 = (!$element_columns > 0) ? 50 : $element_columns;
	
	## set the vars
	$template->set_var('maxchar',$element_maxchar);
	$template->set_var('lines',$element_lines);
	$template->set_var('columns',$element_columns);
	$template->set_var('element_name',$xmldata['NAME']);
	$template->set_var('element_desc',$xmldata['DESC']);
	
	## now it's time to check the previous data entered
	$value = isset($data['text']) ? convert_html($data['text']) : '';

	if($value != '') {
		## set the vars accordingly
		$template->set_var('value',$value);	
	} else {
		$template->set_var('value',$xmldata['DEFAULT']);	
	}

	if(!$xmldata['TAG']) {
		$template->set_var('element_tag',LANG_ElementText);
	} else {
		$template->set_var('element_tag',$xmldata['TAG']);
	}

	$output = "";
	if($element_maxchar) {
		$output = $template->fill_block("copytextcounter");
	} else {	
		$output = $template->fill_block("copytext");
	}
							
	return $output;
}


## =======================================================================        
##  copytext_storeData        
## =======================================================================        
## save the content in the db
## ======================================================================= 
function copytext_storeData($page_id, $identifier) {
	return text_storeData($page_id, $identifier);
}

## =======================================================================        
##  copytext_getData        
## =======================================================================        
##  get the Data
## ======================================================================= 
function copytext_getData($vPageID,&$page_record) {
	text_getData($vPageID,$page_record);
}

## =======================================================================        
##  copytext_deleteData        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function copytext_deleteData($vPageID) {
	text_deleteData($vPageID);
}

## =======================================================================        
##  output_copytext        
## =======================================================================        
##  call this function to output an copytext 
## ======================================================================= 
function output_copytext($item,$structure,$menu_id) {
	global $glossary_search,$glossary_replace;
	
	$element_maxchar = isset($structure['MAXCHAR']) ? intval($structure['MAXCHAR']) : null;	
	$value = nl2br($item['text']);

	if(isset($element_maxchar)) {
		$content = $value." ";
		$content = substr($content,0,$element_maxchar);
		$value = substr($content,0,strrpos($content,' '));
	}
	
	$translationTable = get_html_translation_table(HTML_ENTITIES);
	$translationTable = array_flip ($translationTable);
	$value = strtr($value,$translationTable);
	
	## here we do the glossary stuff- if it is set
	$glossary_active = isset($structure['GLOSSARY']) ? $structure['GLOSSARY'] : false;
	
	if(!empty($glossary_search) && $glossary_active == 'true') {
		$value = preg_replace($glossary_search,$glossary_replace,$value);
		
	}
	
	return $value;
}

## =======================================================================        
##  text_displayPreview        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function copytext_displayPreview($xmldata, $data) {	
	$element_maxchar = isset($xmldata['MAXCHAR']) ? intval($xmldata['MAXCHAR']) : null;	
	$value = $data['text'];

	if(isset($element_maxchar)) {
		$content = $value." ";
		$content = substr($content,0,$element_maxchar);
		$value = substr($content,0,strrpos($content,' '));
	}
	
	$translationTable = get_html_translation_table (HTML_ENTITIES);
	$translationTable = array_flip ($translationTable);
	$value = strtr($value,$translationTable);
	
	return $value;
}

## =======================================================================        
##  text_copyData        
## =======================================================================        
##  pass it the sourcepage_id and the targetpage_id. It'll copy all
##  text entries to the new page.
## ======================================================================= 
function copytext_copyData($source_id, $target_id) {
	text_copyData($source_id, $target_id);	
}

?>
