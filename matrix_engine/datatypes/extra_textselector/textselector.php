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
## textselector_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function textselector_displayInput($xmldata, $data) {
	## init the vars
	$return = "";
	##var_dump($xmldata);
	## we should open our own template
	$template = new Template(ENGINE."datatypes/extra_textselector/interface/");
	$template->set_templatefile(array("textselector" => "interface.tpl"));
	
	## set the vars
	$template->set_var('element_name',$xmldata['NAME']);
	$template->set_var('element_desc',$xmldata['DESC']);
	
	## we got your record to process the data
	
	## now it's time to check the previous data entered
	$value = convert_html($data['text']);
	
	$options = split(',',$xmldata["OPTIONS"]);
	$labels = split(',',$xmldata["LABELS"]);
	$output = '<select name="'.$xmldata['NAME'].'" size="1">';
	for($i=0;$i<count($options); $i++) {
		## set the option
		if($value == $options[$i]) {
			$output .= '<option label="'.$labels[$i].'" value="'.$options[$i].'" selected>'.$labels[$i].'</option>';
		} else {
			$output .= '<option label="'.$labels[$i].'" value="'.$options[$i].'">'.$labels[$i].'</option>';
		}	
	}
	$output .= '</select>';	
	
	## set the vars accordingly
	$template->set_var('value',$output);	
	
	if(!$xmldata['TAG']) {
		$template->set_var('element_tag',LANG_ElementText);
	} else {
		$template->set_var('element_tag',$xmldata['TAG']);
	}	
	
	return $template->fill_block("textselector");
}


## =======================================================================        
##  textselector_storeData        
## =======================================================================        
## save the content in the db
## ======================================================================= 
function textselector_storeData($page_id, $identifier) {
	return text_storeData($page_id, $identifier);
}
## =======================================================================        
##  textselector_getData        
## =======================================================================        
##  get the Data
## ======================================================================= 
function textselector_getData($vPageID,&$page_record) {
	text_getData($vPageID,$page_record);
}

## =======================================================================        
##  textselector_deleteData        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function textselector_deleteData($vPageID) {
	text_deleteData($vPageID);
}

## =======================================================================        
##  textselector_output        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function textselector_output($item,$structure,$menu_id) {
	$value = stripslashes($item['text']);
	return $value;
}

## =======================================================================        
##  textselector_displayPreview        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function textselector_displayPreview($xmldata, $data) {	
	$value = convert_html($data['text']);
	return $value;
}

## =======================================================================        
##  textselector_copyData        
## =======================================================================        
##  pass it the sourcepage_id and the targetpage_id. It'll copy all
##  text entries to the new page.
## ======================================================================= 
function textselector_copyData($source_id, $target_id) {
	text_copyData($source_id, $target_id);	
}

?>
