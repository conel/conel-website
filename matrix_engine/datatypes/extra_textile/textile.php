<?PHP
## ======================================================================= 
##   webmatrix                                                     
## ======================================================================= 
##   Copyright (c) 2001, 2002, 2003 Stefan Elsner, Workmatrix        
## ======================================================================= 
##   <info@workmatrix.de> 
##
## ======================================================================= 
require(ENGINE.CLASSES_DIR."class_textile.php");

## =======================================================================        
## text_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function textile_displayInput($xmldata, $data) {
	## init the vars
	$return = "";

	## we should open our own template
	$template = new Template(ENGINE."datatypes/extra_textile/interface/");
	$template->set_templatefile(array("body" => "interface.tpl"));
	
	$element_lines   = intval($xmldata['LINES']);
	$element_columns = intval($xmldata['COLUMNS']);

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

	$output = $template->fill_block("body");
							
	return $output;
}

## =======================================================================        
##  text_storeData        
## =======================================================================        
## save the content in the db
## ======================================================================= 
function textile_storeData($page_id, $identifier) {
	return text_storeData($page_id, $identifier);
}

## =======================================================================        
##  text_getData        
## =======================================================================        
##  get the Data
## ======================================================================= 
function textile_getData($vPageID,&$page_record) {
	text_getData($vPageID,$page_record);
}

## =======================================================================        
##  text_deleteData        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function textile_deleteData($vPageID) {
	text_deleteData($vPageID);
}

## =======================================================================        
##  output_text        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function textile_output($item,$structure,$menu_id) {
	$value = $item['text'];
	$value = stripslashes($value);

	##  HTML Klammern zurückkonvertieren
	$value = str_replace('&amp;', '&', $value);
	$value = str_replace('&lt;', '<', $value);
	$value = str_replace('&gt;', '>', $value);
	$value = str_replace('&quot;', '"', $value);
	
	$textile = new Textile;
	$value = $textile->TextileThis($value);

	return $value;
}

## =======================================================================        
##  text_copyData        
## =======================================================================        
##  pass it the sourcepage_id and the targetpage_id. It'll copy all
##  text entries to the new page.
## ======================================================================= 
function textile_copyData($source_id, $target_id) {
	text_copyData($source_id, $target_id);	
}
?>
