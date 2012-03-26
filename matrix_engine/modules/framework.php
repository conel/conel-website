<?php
## =======================================================================        
##  framework.php        
## =======================================================================        
##  a module should include this framework to have access to 
##  all function of the webmatrix system
## =======================================================================

## include global settings
require("../../config.php");

error_reporting(0);

## include the template class
require(ENGINE.CLASSES_DIR."template.php");
  
## include the db class
require(ENGINE.CLASSES_DIR."db_mysql.php");

## everything related to authentication/session handling
require(ENGINE.CLASSES_DIR."container.php");
require(ENGINE.CLASSES_DIR."session.php");
require(ENGINE.CLASSES_DIR."authentication.php");
require(ENGINE.CLASSES_DIR."page.php");

require(ENGINE.CLASSES_DIR."class_mailer.php");
require(ENGINE.CLASSES_DIR."class_smtp.php");
require(ENGINE.CLASSES_DIR."class_html_pager.php");
require(ENGINE.CLASSES_DIR."class_validate.php");

## the xmlparser
require(ENGINE.CLASSES_DIR."xmlparser.php");

## the filefunctions
require(ENGINE.CLASSES_DIR."files.php");

## matrix_functions => general functions
require(ENGINE."functions/utilities.php");
require(ENGINE."functions/template.php");
require(ENGINE."functions/page.php");
require(ENGINE."functions/access.php");
require(ENGINE."functions/cache.php");

require(ENGINE."functions/ui_utilities.php");
require(ENGINE."functions/ui_dialogs.php");

## include the configmanger
require(ENGINE.CLASSES_DIR."configmanager.php");


if(REWRITE_GLOBALS == "ON") {
	include(ENGINE."functions/register_globals.php");
}


## all modules require the user to be authenticated
page_open(array("session" => "session_object", "authenticate" => "Auth")); 
page_close();
include(ENGINE."interface/lang/".$Auth->auth["language"].".php");

## =======================================================================        
##  output_confirm        
## =======================================================================        
##  display a confirmation page that the current
##  action was completed succesfully
##
##  TODO:
##  
## ======================================================================= 
function output_confirm($title,$message,$target) {
	global $gSession;

	## prepare the template file
	$select_template = new Template();
	$select_template->set_templatefile(array("body" => ENGINE."interface/dialog_confirm.tpl"));
	$select_template->set_var('title',$title);
	$select_template->set_var('message',$message);
	$select_template->set_var('site',SITE);
	
	$targetURL = $target;
	$targetURL = $gSession->url($targetURL);
	$select_template->set_var('targetURL',$targetURL);
	
	$select_template->pfill_block("body");
}

## =======================================================================        
##  output_confirm_refresh        
## =======================================================================        
##  display a confirmation page that the current
##  action was completed succesfully
##
##  TODO:
##  
## ======================================================================= 
function output_confirm_refresh($title,$message,$targetURL) {
	global $gSession;
	## prepare the template file
	$select_template = new Template();
	$select_template->set_templatefile(array("body" => ENGINE."modules/interface/confirm_refresh.tpl"));
	$select_template->set_var('title',$title);
	$select_template->set_var('message',$message);

	if($targetURL != '') {
		$targetURL = "<meta http-equiv='refresh' content='2; url=".$targetURL."'>";
		$select_template->set_var('MAINREFRESH',$targetURL);
	}		
	
	$select_template->pfill_block("body");
}

## =======================================================================        
##  output_confirm_button        
## =======================================================================        
##  output a page with a message and one button on the right side
##  please supply an image for this call
##
##  TODO:
##  
## ======================================================================= 
function output_confirm_button($title,$message,$target,$image) {
	global $gSession;
	## prepare the template file
	$select_template = new Template();
	$select_template->set_templatefile(array("body" => ENGINE."interface/dialog_confirm.tpl"));
	$select_template->set_var('title',$title);
	$select_template->set_var('message',$message);
	
	$targetURL = $target;
	$targetURL = $gSession->url($targetURL);

	$select_template->set_var('targetURL',$targetURL);
	$select_template->set_var('targetImage',$image);
	
	$select_template->pfill_block("body");
}

## =======================================================================        
##  output_progress        
## =======================================================================        
##  display a page with a progress bar and start loading the actual page
##
##  TODO:
##  
## ======================================================================= 
function output_progress($title,$message,$target) {
	global $gSession;
	## prepare the template file
	$select_template = new Template();
	$select_template->set_templatefile(array("body" => ENGINE."modules/interface/progress.tpl"));
	$select_template->set_var('title',$title);
	$select_template->set_var('message',$message);

	$targetURL = $target;
	$targetURL = $gSession->url($targetURL);
	$select_template->set_var('targetURL',$targetURL);
	
	$select_template->pfill_block("body");
}

## =======================================================================        
##  output_Tab       
## =======================================================================        
## renders a tab and returns the generated html 
##   
## ======================================================================= 
function output_Tab($label, $editorUrl, $counter, $active, $has_predecessors) {
	global $gSession;
	
	## init return value
	$return_value = "";
	
	## here we define the start
	if($counter == $active) {
		$image_base = 'high';
	} else {
		$image_base = 'norm';
	}
	
	if(!$has_predecessors) {
		$image_right = '_end';
	} else {
		if($counter+1 == $active) {
			## the next one his active
			$image_right = '_high';
		} else {
			$image_right = '_norm';
		}
	}
	
	## prepare the url to ourselves
	$self = $gSession->url($_SERVER['PHP_SELF']);
	$self .= '&tab='.$counter;

	## get the template
	$template = new Template(ENGINE."modules/interface/");
	$template -> set_templatefile(array("begin" => "tab.tpl","middle" => "tab.tpl","modulebegin" => "tab.tpl"));
	
	## now start setting the vars
	$template->set_var('LABEL', $label);
	$template->set_var('RIGHT', $image_right);
	$template->set_var('BASE', $image_base);
	$template->set_var('EDITOR', $editorUrl);
	$template->set_var('SELF', $self);
	
	## finally we need to set the order-
	## if the tab should be highlightes we need to
	## handle this in here too
	if ($counter == 0) {
		$return_value = $template->fill_block('begin');
	} else if($counter == 3) {
		$return_value = $template->fill_block('modulebegin');
	} else {
		$return_value = $template->fill_block('middle');
	}				

	return $return_value;	
}

?>