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
##  sort_module_default        
## =======================================================================        
##  this is the main entry point for each sort module
##
## =======================================================================
function sort_module_default() {
	## get all vars
	$menu_id = isset($_POST['menu_id']) ? $_POST['menu_id'] : $_GET['menu_id'];
	$order   = $_GET['menu_order'];
	$moveby  = $_GET['move'];

	## if the vars are all set, this is the second run, so we can sort the pages	
	if(isset($order) && isset($moveby) && isset($menu_id)) {
		sort_module_default_UpdateOrder($menu_id,$order,$moveby);
	}
	sort_module_default_promptSort($menu_id);
}

## =======================================================================        
##  sort_module_default_UpdateOrder        
## =======================================================================        
##  this is our sort routine.
##	you can move a single page up, or down
##
## =======================================================================
function sort_module_default_UpdateOrder($vID, $vOrder, $vMoveBy) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	$db_connection = new DB_Sql();
	
	## we need to get the parent info for this page
	$pageInfo = structure_getPage($vID);
	
	## check for the last entry
	$query = "SELECT max(structure_order) FROM ".STRUCTURE." WHERE structure_parent=".$pageInfo["parent"]." AND client_id='$client_id'";
	$db_result = $db_connection->query($query);
	$db_connection->next_record();
		
	## retrieve the maxorder	
	list($max_order) = $db_connection->Record;
	if($vMoveBy == 1) {
		$updated_menu_order = $vOrder-1;
    	if ($updated_menu_order < 0) {
      			$updated_menu_order = 0;
    	}
		## next we should check if there is an item with that order
		$query = "SELECT structure_id FROM ".STRUCTURE." WHERE structure_order='$updated_menu_order' AND structure_parent=".$pageInfo["parent"]." AND client_id='$client_id'";
		$db_result = $db_connection->query($query);
		$db_connection->next_record();
		
		$id_old_order = $db_connection->Record["structure_id"];

		$element_counter = $db_connection->num_rows();

		if($max_order <=0) {
			$max_order = $element_counter;
		}
		
		$menu_order = $updated_menu_order+1;
    	if ($menu_order > $max_order) {
      		$menu_order = $max_order;
   		}		
    }

	if ($vMoveBy == 0) {
		$updated_menu_order = ($vOrder + 1);
    	if ($updated_menu_order > $max_order){
      		$updated_menu_order = $max_order;
    	}

		## next we should check if there is an item with that order
		$query = "SELECT structure_id FROM ".STRUCTURE." WHERE structure_order='$updated_menu_order' AND structure_parent=".$pageInfo["parent"]." AND client_id='$client_id'";
		$db_result = $db_connection->query($query);
		$db_connection->next_record();
		
		$id_old_order = $db_connection->Record["structure_id"];
		$element_counter = $db_connection->num_rows();
				    	
    	$menu_order = ($updated_menu_order - 1);
    	if ($menu_order < 0){
      		$menu_order = 0;
    	}

	}
  
  	$update_query = "UPDATE ".STRUCTURE." SET structure_order='$menu_order' WHERE structure_id='$id_old_order' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($update_query);
   		
   	$update_query = "UPDATE ".STRUCTURE." SET structure_order='$updated_menu_order' WHERE structure_id='$vID' AND client_id='$client_id'";
   	$result_pointer = $db_connection->query($update_query);
}

## =======================================================================        
##  sort_module_default_promptSort        
## =======================================================================        
##  this routine displays a list of pages, that are all on the same
##	level. Using the buttons you can sort the pages
##
## =======================================================================
function sort_module_default_promptSort($vID) {
	global $gSession,$Auth;
	
	## prepare the sort method
	$sort_method = isset($_POST['sortmethod']) ? $_POST['sortmethod'] : (isset($_GET['sortmethod']) ? $_GET['sortmethod'] : '');

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## get the page info

	$pageInfo = structure_getPage($vID);
	
	## prepare the output
	$select_template = new Template('sort/default/interface/');
	$select_template->set_templatefile(array("header" => "sort_pages.tpl","body" => "sort_pages.tpl","footer" => "sort_pages.tpl"));

	$select_template->set_var('language_sorthead',LANG_SortPages);
	$select_template->set_var('language_sortbody',LANG_SortPagesDescription);

	$select_template->pfill_block("header");

	## here we get all the subpages

	$pageInfo = structure_getAllSubPages($pageInfo["parent"]);
	foreach($pageInfo as $currentEntry) {
		## prepare the vars 
		$upurl   = "admin.php?op=update_menu_order&sortmethod=".$sort_method."&menu_id=".$currentEntry["id"]."&menu_order=".$currentEntry["order"]."&move=1";
		$upurl = $gSession->url($upurl);
		$downurl = "admin.php?op=update_menu_order&sortmethod=".$sort_method."&menu_id=".$currentEntry["id"]."&menu_order=".$currentEntry["order"]."&move=0";
		$downurl = $gSession->url($downurl);
		$pageurl = "$menu_url?menu=$menu_id&page_id=".$currentEntry["page_id"];
		$pageurl = $gSession->url($pageurl);
		
		## now we set the vars for this row
		$select_template->set_vars(array('upurl'=>$upurl,'downurl'=>$downurl,'pageurl'=>$pageurl,'menu_text'=>$currentEntry["text"]));

		## and flush this row
		$select_template->pfill_block("body");
	}
	$select_template->pfill_block("footer");
}

?>
