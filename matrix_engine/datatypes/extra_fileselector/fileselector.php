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
## fileselector_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function fileselector_displayInput($xmldata, $data) {
	## init the vars
	$return = "";
	##var_dump($xmldata);
	## we should open our own template
	$template = new Template(ENGINE."datatypes/extra_fileselector/interface/");
	$template->set_templatefile(array("textselector" => "interface.tpl"));
	
	## set the vars
	$template->set_var('element_name',$xmldata['NAME']);
	$template->set_var('element_desc',$xmldata['DESC']);
	
	## we got your record to process the data
	
	## now it's time to check the previous data entered
	$value = $data['text'];
	
	$file_list = _fileselector_getFiles();
	
	$options = $file_list;
	$labels = $file_list;
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
##  fileselector_storeData        
## =======================================================================        
## save the content in the db
## ======================================================================= 
function fileselector_storeData($page_id, $identifier) {
	return text_storeData($page_id, $identifier);
}
## =======================================================================        
##  fileselector_getData        
## =======================================================================        
##  get the Data
## ======================================================================= 
function fileselector_getData($vPageID,&$page_record) {
	text_getData($vPageID,$page_record);
}

## =======================================================================        
##  fileselector_deleteData        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function fileselector_deleteData($vPageID) {
	text_deleteData($vPageID);
}

## =======================================================================        
##  fileselector_output        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function fileselector_output($item,$structure,$menu_id) {
	$value = stripslashes($item['text']);
	return $value;
}

## =======================================================================        
##  fileselector_displayPreview        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function fileselector_displayPreview($xmldata, $data) {	
	$value = convert_html($data['text']);
	return $value;
}

## =======================================================================        
##  fileselector_copyData        
## =======================================================================        
##  pass it the sourcepage_id and the targetpage_id. It'll copy all
##  text entries to the new page.
## ======================================================================= 
function fileselector_copyData($source_id, $target_id) {
	text_copyData($source_id, $target_id);	
}

## =======================================================================        
##  _fileselector_getFiles        
## =======================================================================        
function _fileselector_getFiles() {
	## prepare the container
	$file_list = array();
	
	## setup the folder
	$folder = MATRIX_BASEDIR.'downloads/';

	## check if the folder exists
	if (is_dir($folder)) {
		if($dh = opendir($folder)) {
			while(($file = readdir($dh)) !== false) {
				if(filetype($folder.$file) == 'file') {
					$file_list[] = $file;
				}
 		 	}
 			closedir($dh);
 		}
 	}
 	return $file_list;
}
?>
