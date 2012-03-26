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
## text_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function dhtmltext_displayInput($xmldata, $data) {
	## include the fckeditor files
	@include(ENGINE."datatypes/extra_dhtmltext/interface/fckeditor/fckeditor.php");

	## init the vars
	$return = "";
	
	## we should open our own template
	$template = new Template(ENGINE."datatypes/extra_dhtmltext/interface/");
	$template->set_templatefile(array("text" => "interface.tpl","head" => "interface.tpl"));

	$element_height   = intval($xmldata['HEIGHT']) > 0 ? $xmldata['HEIGHT'] : 220;
	$element_conf  = isset($xmldata['CONFIG']) ? $xmldata['CONFIG'] : 'fckconfig.js';
	$element_conf  = SITE.'datatypes/extra_dhtmltext/interface/fckeditor/'.$element_conf;
	
	## set the vars
	$template->set_var('element_name',$xmldata['NAME']);
	$template->set_var('element_desc',$xmldata['DESC']);
	$template->set_var('element_conf',$element_conf);
	$template->set_var('SITE',SITE);
	
	## we got your record to process the data

	## now it's time to check the previous data entered
	$value = $data['text'];
	$value = str_replace('&quot;', '"', $value);
	$value = str_replace('<div class="hr"><hr /></div>','<hr />',$value);
	$value = str_replace('<div class="hr">'."\n".'<hr /></div>','<hr />',$value);
	##$value = $value;

	$oFCKeditor = new FCKeditor($xmldata['NAME']);
	$oFCKeditor->BasePath = SITE.'datatypes/extra_dhtmltext/interface/fckeditor/';
	$oFCKeditor->Value = $value;
	$oFCKeditor->Height = $element_height;
	$oFCKeditor->Config["CustomConfigurationsPath"] = $element_conf;
	$template->set_var('element_form',$oFCKeditor->CreateHtml());
	
	## set the vars accordingly
	$template->set_var('value',$value);	
	
	if(!$xmldata['TAG']) {
		$template->set_var('element_tag',LANG_ElementText);
	} else {
		$template->set_var('element_tag',$xmldata['TAG']);
	}
	
	## okay we need to output the header javascript elements
	$body = $template->fill_block("text");
	$header = $template->fill_block("head");
			
	return array('body'=>$body,'header'=>$header);
}


## =======================================================================        
##  text_storeData        
## =======================================================================        
## save the content in the db
## ======================================================================= 
function dhtmltext_storeData($page_id, $identifier) {
	return text_storeData($page_id, $identifier);
}

## =======================================================================        
##  text_getData        
## =======================================================================        
##  get the Data
## ======================================================================= 
function dhtmltext_getData($vPageID,&$page_record) {
	text_getData($vPageID,$page_record);
}

## =======================================================================        
##  text_deleteData        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function dhtmltext_deleteData($vPageID) {
	text_deleteData($vPageID);
}

## =======================================================================        
##  output_text        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function dhtmltext_output($item,$structure,$menu_id) {
	global $glossary_search,$glossary_replace;
	
	$value = $item['text'];
	$value = stripslashes(htmlentities($value));

	##  HTML Klammern zurückkonvertieren
	$value = str_replace('&amp;', '&', $value);
	$value = str_replace('&lt;', '<', $value);
	$value = str_replace('&gt;', '>', $value);
	$value = str_replace('&quot;', '"', $value);
	$value = str_replace('&amp;', '&', $value);
	
	## here we do the glossary stuff- if it is set
	if(!empty($glossary_search) && $structure['GLOSSARY'] == 'true') {
		$value = preg_replace($glossary_search,$glossary_replace,$value);
	}
	
	return $value;
}

## =======================================================================        
##  text_copyData        
## =======================================================================        
##  pass it the sourcepage_id and the targetpage_id. It'll copy all
##  text entries to the new page.
## ======================================================================= 
function dhtmltext_copyData($source_id, $target_id) {
	text_copyData($source_id, $target_id);	
}
?>
