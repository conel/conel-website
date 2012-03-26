<?php
## =======================================================================        
##  config.php        
## =======================================================================        
##  used to define all vars that need to be set when we install this onto
##  a new server- later we will search an replace the vbars to protect 
##  the code
##
##  TODO:   
##     - check if it works    
## =======================================================================
function seo_displayOverview() {
	global $Auth, $gSession;
	

	## get the template for now
	$template = new Template("interface/");
	$template->set_templatefile(array("body" => "interface.tpl"));

	## get the config info for this module
	$configmanager = new Configmanager();
	$configmanager->setConfigPath(MATRIX_BASEDIR."settings/modules/seo/");
	$configmanager->setConfigFile("settings");
	$configmanager->setConfigName("module_seo");
	$current_config = $configmanager->readConfigFile();
		
	## okay now we have all values- we will display the current state 
	## and let the user change these

	## setup the text element
	$template->set_var('LANG_DATATYPE_META_Title',LANG_DATATYPE_META_Title);
	$template->set_var('LANG_DATATYPE_META_TitleDesc',LANG_DATATYPE_META_TitleDesc);
	$template->set_var('LANG_DATATYPE_META_PageTitle',LANG_DATATYPE_META_PageTitle);
	$template->set_var('LANG_DATATYPE_META_PageTitleDesc',LANG_DATATYPE_META_PageTitleDesc);

	$template->set_var('LANG_DATATYPE_META_PageDesc',LANG_DATATYPE_META_PageDesc);
	$template->set_var('LANG_DATATYPE_META_PageDescDesc',LANG_DATATYPE_META_PageDescDesc);

	$template->set_var('LANG_DATATYPE_META_Keywords',LANG_DATATYPE_META_Keywords);
	$template->set_var('LANG_DATATYPE_META_KeywordsDesc',LANG_DATATYPE_META_KeywordsDesc);

	## specify the target of this page
	$actionURL = "module.php";
	$actionURL = $gSession->url($actionURL);
	$template->set_var('actionURL',$actionURL);
	
	## let's set the command
	$output =  '<input type="hidden" name="cmd" value="save">';
	$output .= '<input type="hidden" name="Session" value="'.$gSession->id.'">';
	$template->set_var("hiddenfields",$output);
		
	## on/off of the caching
	$template->set_var('title_value',$current_config['TITLE']);
	$template->set_var('description_value',$current_config['DESCRIPTION']);
	$template->set_var('keywords_value',$current_config['KEYWORDS']);



	$template->set_var("saveIMG","../../interface/lang/".$Auth->auth["language"]."_button_save.gif");

	## clear the cache
	$template->set_var('element_tag',LANG_MODULE_CACHE_Clean);
	$template->set_var('element_desc',LANG_MODULE_CACHE_CleanDesc);
	$template->set_var("triggerIMG","lang/".$Auth->auth["language"]."_button_clearcache.gif");
	##$template->set_var("triggerURL",$actionURL."&cmd=empty");
	$template->pfill_block("triggerfunction");	
	
	
	$template->pfill_block("body");
}
?>