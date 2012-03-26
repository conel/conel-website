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
function displaySetup() {
	global $Auth, $gSession;
	

	## get the template for now
	$template = new Template("../interface/");
	$template->set_templatefile(array("triggerfunction" => "preferencesUI.tpl","head" => "preferencesUI.tpl","intro" => "preferencesUI.tpl","radio" => "preferencesUI.tpl","date" => "preferencesUI.tpl","foot" => "preferencesUI.tpl"));

	## get the config info for this module
	$configmanager = new Configmanager();
	$configmanager->setConfigPath(ENGINE."modules/cache/");
	$configmanager->setConfigFile("settings");
	$configmanager->setConfigName("cache_config");
	$current_config = $configmanager->readConfigFile();

	## header and intro element
	$template->set_var('language_inputhead',LANG_MODULE_CACHE_Title);
	$template->set_var('language_inputbody',LANG_MODULE_CACHE_ChangeSettings);

	## specify the target of this page
	$actionURL = "module.php";
	$actionURL = $gSession->url($actionURL);
	$template->set_var('actionURL',$actionURL);
	
	## let's set the command
	$output =  '<input type="hidden" name="cmd" value="save">';
	$output .= '<input type="hidden" name="Session" value="'.$gSession->id.'">';
	$template->set_var("hiddenfields",$output);
		
	
	$template->pfill_block("head");
	$template->pfill_block("intro");

	## on/off of the caching
	$template->set_var('element_tag',LANG_MODULE_CACHE_PageCache);
	$template->set_var('element_desc',LANG_MODULE_CACHE_PageCacheDesc);
	$template->set_var('element_name',"MODULE_CACHE_ACTIVE");
	$template->set_var('value1',"1");
	$template->set_var('value2',"0");
	$template->set_var('valueLabel1',LANG_MODULE_CACHE_On);
	$template->set_var('valueLabel2',LANG_MODULE_CACHE_Off);

	if($current_config["MODULE_CACHE_ACTIVE"]) {
		$template->set_var('status1', "checked");
	} else {
		$template->set_var('status2', "checked");
	}
	$template->pfill_block("radio");

	$template->set_var('element_tag',LANG_MODULE_CACHE_Send304);
	$template->set_var('element_desc',LANG_MODULE_CACHE_Send304Desc);
	$template->set_var('element_name',"CACHE_SEND304");
	$template->set_var('value1',"1");
	$template->set_var('value2',"0");
	$template->set_var('valueLabel1',LANG_MODULE_CACHE_On);
	$template->set_var('valueLabel2',LANG_MODULE_CACHE_Off);	

	if($current_config["CACHE_SEND304"]) {
		$template->set_var('status1', "checked");
		$template->set_var('status2', "");
	} else {
		$template->set_var('status2', "checked");
		$template->set_var('status1', "");
	}

	$template->pfill_block("radio");

	## let's set up the time 
	$timeSettings= getdate(mktime(0,0,$current_config["CACHE_TIME"]));

	$template->set_var('element_tag',LANG_MODULE_CACHE_Time);
	$template->set_var('element_name',"CACHE_TIME");
	$template->set_var('DAY',LANG_HOURS);
	$template->set_var('MONTH',LANG_MINUTES);
	$template->set_var('YEAR',LANG_SECONDS);
	$template->set_var('day',$timeSettings["hours"]);
	$template->set_var('month',$timeSettings["minutes"]);
	$template->set_var('year',$timeSettings["seconds"]);
	$template->set_var('element_desc',LANG_MODULE_CACHE_TimeDesc);
	$template->pfill_block("date");


	## clear the cache
	$template->set_var('element_tag',LANG_MODULE_CACHE_Clean);
	$template->set_var('element_desc',LANG_MODULE_CACHE_CleanDesc);
	$template->set_var("triggerIMG","lang/".$Auth->auth["language"]."_button_clearcache.gif");
	$template->set_var("triggerURL",$actionURL."&cmd=empty");
	$template->pfill_block("triggerfunction");

	$template->set_var("saveIMG","lang/".$Auth->auth["language"]."_button_save.gif");
	$template->pfill_block("foot");
}
?>