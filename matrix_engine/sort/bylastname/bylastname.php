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
##  sort_module_bylastname        
## =======================================================================        
##  this is the main entry point for each sort module
##
## =======================================================================
function sort_module_bylastname() {
	global $Auth;
	
	## include the lang file
	include("lang/".$Auth->auth["language"].".php");
	## prepare the url
	$menu_id = isset($_POST['menu_id']) ? $_POST['menu_id'] : $_GET['menu_id'];
	
	if(!isset($_GET['cmd'])) {
		$actionURL = 'admin.php?op=update_menu_order&menu_id='.$menu_id.'&sortmethod=bylastname&cmd=dosort';
		sort_module_bylastname_output_progress(LANG_Sort_ByLastName_Name,LANG_Sort_ByLastName_ProgressDesc,$actionURL);
	} else {
		sort_module_bylastname_doSort($menu_id);
		## now update the pagemenu
		output_confirm(LANG_Sort_ByLastName_SuccessDesc,'',"matrix_menu.php");
	}
}

## =======================================================================        
##  sort_module_bylastname_output_progress        
## =======================================================================        
##  display a page with a progress bar and start loading the actual page
##
##  TODO:
##  
## ======================================================================= 
function sort_module_bylastname_output_progress($title,$message,$target) {
	global $gSession;

	## prepare the template file
	$select_template = new Template('sort/bylastname/interface/');
	$select_template->set_templatefile(array("body" => "progress.tpl"));
	$select_template->set_var('title',$title);
	$select_template->set_var('message',$message);
	
	$targetURL = $target;
	$targetURL = $gSession->url($targetURL);
	$select_template->set_var('targetURL',$targetURL);
	
	$select_template->pfill_block("body");
}

## =======================================================================        
##  sort_module_bylastname_doSort        
## =======================================================================        
##  this function does the actual sorting
##	first we determine what pages need to be sorted
##	then we get these pages sorted by their name
##  and finally we loop through all pages and update their order number
##
##  TODO: 
## ======================================================================= 
function sort_module_bylastname_doSort($menu_id) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	$db_connection = new DB_Sql();
	
	## get the page info
	$pageInfo = structure_getPage($menu_id);
	
	## here we get all the subpages
	$pageInfo = structure_getAllSubPages($pageInfo["parent"],'structure_text');
	
	
	$dummy_array = array();
	
	foreach($pageInfo as $current_entry) {
		## split up the name
		$name = str_word_count($current_entry['text'],1);
		$dummy_array[$current_entry['id']] = isset($name[1]) ? $name[1] : $current_entry['text'];
	}
	
	## finally we need to sort this new array
	asort($dummy_array);
	##exit;
	## now loop through all of them, nd update the order accordingly
	$order = 0;
	foreach($dummy_array as $id=>$currentEntry) {
		##$id = $currentEntry['id'];
	
	  	$update_query = "UPDATE ".STRUCTURE." SET structure_order='$order' WHERE structure_id='$id' AND client_id='$client_id'";
	  	$result_pointer = $db_connection->query($update_query);
		$order++;
		## reset the vars
		$id = -1;
	}
}
?>
