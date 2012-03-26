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
## chatroomselector_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function chatroomselector_displayInput($xmldata, $data) {
	## init the vars
	$return = "";
	##var_dump($xmldata);
	## we should open our own template
	$template = new Template(ENGINE."datatypes/extra_chatroomselector/interface/");
	$template->set_templatefile(array("textselector" => "interface.tpl"));
	
	## set the vars
	$template->set_var('element_name',$xmldata['NAME']);
	$template->set_var('element_desc',$xmldata['DESC']);
	
	## try to capture the external data for the rooms
	$fp = fsockopen("host15.123flashchat.com", 80);
	fwrite($fp, "GET /breastcancercare/room_id.php HTTP/1.0\r\n");
	fwrite($fp, "Host: host15.123flashchat.com\r\n\r\n");

	$buffer = '';
	if ($fp) { 
		while (!feof($fp)) {
			$buffer .= fgets($fp, 4096);
		}
		fclose ($fp);
	} 

	## now process the buffer
	preg_match_all("'document.write\(\'(.*[\r\n]*)\'\);'Usi",$buffer,$temp);
	$ids = '-1,'.$temp[1][0];
	
	## now get the room names
	$fp = fsockopen("host15.123flashchat.com", 80);
	fwrite($fp, "GET /breastcancercare/room_name.php HTTP/1.0\r\n");
	fwrite($fp, "Host: host15.123flashchat.com\r\n\r\n");

	$buffer = '';
	if ($fp) { 
		while (!feof($fp)) {
			$buffer .= fgets($fp, 4096);
		}
		fclose ($fp);
	} 

	## now process the buffer
	preg_match_all("'document.write\(\'(.*[\r\n]*)\'\);'Usi",$buffer,$temp);
	$names = str_replace('<name>','',$temp[1][0]);
	$names = str_replace('</name>','',$names);
	$names = 'Select a Room,'.$names;	

	## now it's time to check the previous data entered
	$value = convert_html($data['text']);

	$options = split(',',$ids);
	$labels = split(',',$names);
	$output = '<select name="'.$xmldata['NAME'].'" size="1">';
	for($i=0;$i<count($options); $i++) {
		## set the option
		if($value == $options[$i]) {
			$output .= '<option label="'.trim($labels[$i]).'" value="'.$options[$i].'" selected>'.trim($labels[$i]).'</option>';
		} else {
			$output .= '<option label="'.trim($labels[$i]).'" value="'.$options[$i].'">'.trim($labels[$i]).'</option>';
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
##  chatroomselector_storeData        
## =======================================================================        
## save the content in the db
## ======================================================================= 
function chatroomselector_storeData($page_id, $identifier) {
	return text_storeData($page_id, $identifier);
}
## =======================================================================        
##  chatroomselector_getData        
## =======================================================================        
##  get the Data
## ======================================================================= 
function chatroomselector_getData($vPageID,&$page_record) {
	text_getData($vPageID,$page_record);
}

## =======================================================================        
##  chatroomselector_deleteData        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function chatroomselector_deleteData($vPageID) {
	text_deleteData($vPageID);
}

## =======================================================================        
##  chatroomselector_output        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function chatroomselector_output($item,$structure,$menu_id) {
	$value = intval(($item['text']));
	
	if($value > 0) {
		return '&init_room='.$value;
	}
}

## =======================================================================        
##  chatroomselector_displayPreview        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function chatroomselector_displayPreview($xmldata, $data) {	
	$value = convert_html($data['text']);
	return $value;
}

## =======================================================================        
##  text_copyData        
## =======================================================================        
##  pass it the sourcepage_id and the targetpage_id. It'll copy all
##  text entries to the new page.
## ======================================================================= 
function chatroomselector_copyData($source_id, $target_id) {
	text_copyData($source_id, $target_id);	
}
?>
