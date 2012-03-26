<?php
## ======================================================================= 
##   webmatrix                                                     
## ======================================================================= 
##   Copyright (c) 2001, 2002, 2003 Stefan Elsner, Workmatrix        
## ======================================================================= 
##   <info@workmatrix.de> 
##
## ======================================================================= 

## =======================================================================        
##  sort_module_byname        
## =======================================================================        
##  this is the main entry point for each sort module
##
## =======================================================================
function sort_module_bydate() {
	global $Auth,$_PAGE_SORTOPITIONS;
	
	## determine the id of our search method
	$sort_method = array_search('bydate',$_PAGE_SORTOPITIONS);

	## include the lang file
	include("lang/".$Auth->auth["language"].".php");
	## prepare the url
	$menu_id = isset($_POST['menu_id']) ? $_POST['menu_id'] : $_GET['menu_id'];
	
	if(!isset($_GET['cmd'])) {
		$actionURL = 'admin.php?op=update_menu_order&menu_id='.$menu_id.'&sortmethod='.$sort_method.'&cmd=dosort';
		sort_module_bydate_output_progress(LANG_Sort_ByDate_Name,LANG_Sort_ByDate_ProgressDesc,$actionURL);
	} else {
		sort_module_bydate_doSort($menu_id);
		## now update the pagemenu
		ui_output_confirm(LANG_Sort_ByDate_SuccessDesc,'',"matrix_menu.php");
	}
}

## =======================================================================        
##  sort_module_byname_output_progress        
## =======================================================================        
##  display a page with a progress bar and start loading the actual page
##
##  TODO:
##  
## ======================================================================= 
function sort_module_bydate_output_progress($title,$message,$target) {
	global $gSession;
	## prepare the template file
	$select_template = new Template('sort/bydate/interface/');
	$select_template->set_templatefile(array("body" => "progress.tpl"));
	$select_template->set_var('title',$title);
	$select_template->set_var('message',$message);
	
	$targetURL = $target;
	$targetURL = $gSession->url($targetURL);
	$select_template->set_var('targetURL',$targetURL);
	
	$select_template->pfill_block("body");
}

## =======================================================================        
##  sort_module_byname_doSort        
## =======================================================================        
##  this function does the actual sorting
##	first we determine what pages need to be sorted
##	then we get these pages sorted by their name
##  and finally we loop through all pages and update their order number
##
##  TODO: 
## ======================================================================= 
function sort_module_bydate_doSort($menu_id,$order=FALSE) {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	$db_connection = new DB_Sql();
	
	## get the page info
	$pageInfo = structure_getPage($menu_id);
	
	// If order is set, use order else make the default date sort Descending
	$order = ($order) ? $order : 'DESC';

	## here we get all the subpages
	$pageInfo = structure_getAllSubPages($pageInfo["parent"],"date $order",$news_event = TRUE);

	## now loop through all of them, nd update the order accordingly
	$order = 0;
	foreach($pageInfo as $currentEntry) {
		$id = $currentEntry['id'];
	
	  	$update_query = "UPDATE ".STRUCTURE." SET structure_order='$order' WHERE structure_id='$id' AND client_id='$client_id'";
		$result_pointer = $db_connection->query($update_query);
		$order++;
		## reset the vars
		$id = -1;
	}
}
?>
