<?php
	require("config.php");
	
	## include the template class
	require(CLASSES_DIR."template.php");
  
	## include the db class
	require(CLASSES_DIR."db_mysql.php");

	# call all session related objects
	require(CLASSES_DIR."container.php");
	require(CLASSES_DIR."session.php");
	require(CLASSES_DIR."authentication.php");
	require(CLASSES_DIR."page.php");
	
	include("functions/structure.php");
	require("functions/structure_backend.php");
	include("functions/access.php");
	include("functions/utilities.php");
	include("functions/language.php");
	include("functions/lock.php");
	include("editors/user/functions/_workspacefunctions.php");

	if(REWRITE_GLOBALS == "ON") {
		include("functions/register_globals.php");
	}
	
	$position = isset($_GET['p']) ? $_GET['p'] : null;

	page_open(array("session" => "session_object", "authenticate" => "Auth")); 
	
	if(!isset($menustatus)) {
		$menustatus = $position;
		$gSession->register("menustatus");
		##echo "hier";
	} else {
		if($menustatus != $position && isset($position)) {
			$menustatus = $position;
			$gSession->register("menustatus");
		}
	}
	page_close();
	
	include("interface/lang/".$Auth->auth["language"].".php");  

	## check if we have the correct access rights
	$access_rights = _getUserAccessRights($Auth->auth['user_id']);
	if(!isset($access_rights['pages']['workspace'])) {
		## this means we are not allowed to do anything
		## display the error message
		$inputFile = "empty_menu.tpl";
		$input_template = new Template(INTERFACE_DIR);
		$input_template->set_templatefile(array("body" => $inputFile));
		## the next step is to ouput the head
		$input_template->pfill_block("body");
		exit;
	}

	## when we are loading the page editor- then we know there is no locking- because
	## we are not edditing any pages
	$user_id = $Auth->auth['user_id'];
	lock_unlockpages($user_id);

	## what workspaces is the user allowd to access
	$workspaces = array();
	foreach($access_rights['pages']["workspace"] as $workspace_id=>$access) {
		$workspaces[] = $workspace_id;
	}

	## now we have the list of workspaces, we should get a list of pages	
	$workspace = array();
	$workspace = workspace_getStructureID($workspaces);
	
	$structure = array();
	foreach($workspace as $current_entry_point) {
		## we need to find the parent pages for each entry point
		$pages = structure_getBranchUpwards($current_entry_point);

		krsort($pages);
		$pages = array_values($pages);
		
		$structure[] = $pages;
	}

	## finally draw the menu	
	structure_drawMenu($menustatus,$structure,$workspace);

	
?>
