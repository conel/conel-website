<?php
	require("config.php");

	## set up the modules
	require(ENGINE."modules/modules.php");

	configModules($installed_modules);

	## include the template class
	require(CLASSES_DIR."template.php");
  
	## include the db class
	require(CLASSES_DIR."db_mysql.php");

	# call all session related objects
	require(CLASSES_DIR."container.php");
	require(CLASSES_DIR."session.php");
	require(CLASSES_DIR."authentication.php");
	require(CLASSES_DIR."page.php");
	
	include("functions/access.php");
	
	require(ENGINE."functions/ui_utilities.php");

	page_open(array("session" => "session_object", "authenticate" => "Auth")); 
	page_close();

	include("interface/lang/".$Auth->auth["language"].".php");  	
	
	## we need to process the tab var if it was set:
	$active_tab    = isset($_GET['tab']) ? $_GET['tab'] : 0;
	
	## set the tab counter 
	$tab_counter = 0;
	
	## init the output vars
	$tabs = "";
		
	## the pageeditor
	$logged_in_user = isset($_SESSION['wm']['username']) ? $_SESSION['wm']['username'] : '';
	
	if ($logged_in_user != 'csvexport') {
	
		$pageEditor = $gSession->url('page_editor.php');
		$tabs .= ui_renderTab(LANG_TAB_PAGE,$pageEditor, $tab_counter, $active_tab, true);
		
		$tab_counter++;	

		$userEditor = $gSession->url('editors/user/editor.php');
		$tabs .= ui_renderTab(LANG_TAB_USER,$userEditor, $tab_counter, $active_tab, true);	
	
		$tab_counter++;	

		// nkowald - 2011-03-22 - Adding Banners tab
		$bannersEditor = $gSession->url('banners.php');
		$tabs .= ui_renderTab('Banners',$bannersEditor, $tab_counter, $active_tab, true);	
	
		$tab_counter++;
		
		/* nkowald - 2009-07-13 : Adding new subjects tab */
		$subjectEditor = $gSession->url('subject_editor.php');
		$tabs .= ui_renderTab('Subjects',$subjectEditor, $tab_counter, $active_tab, true);	

		$tab_counter++;	

		/* nkowald - 2009-07-20 : Adding new files tab */
		$fileBrowser = $gSession->url('../file_browser.php');
		$tabs .= ui_renderTab('Files',$fileBrowser, $tab_counter, $active_tab, true);	

	}
		
	/* nkowald - 2010-10-28 : Adding new Export CSV tab */
	$export_csv = $gSession->url('export_csv.php');
	$tabs .= ui_renderTab('Export CSV',$export_csv, $tab_counter, $active_tab, false);	

	$tab_counter++;	
	
	## now we generate the tabs for the modules
	$modules_output = "";
	
	$modules = setupModulesNavigation($installed_modules);
	
	for($i=0; $i < count($modules); $i++) {
		if($i == 0 || $i == 1) {
			$has_predecessors = true;
		} else {
			$has_predecessors = false;
		}
		$modules_output .= ui_renderTab($modules[$i]['LABEL'],$modules[$i]['URL'], $tab_counter, $active_tab, $has_predecessors);		
		$tab_counter++;
	}
	
	## the help button
	$helpEditor = 'page_editor.php';          
	$helpEditor = $gSession->url($helpEditor);

		
	## prepare the template and start the output
	$layout_template = new Template(INTERFACE_DIR);
	$layout_template->set_templatefile(array("body" => "mode.tpl"));
	
	## output the tabs
	$layout_template->set_var("MAIN",$tabs);	
		
	## output the module-tabs	
	$layout_template->set_var("MODULE",$modules_output);	
	
	## output the help
	$layout_template->set_var("helpEditor",$helpEditor);
		
	##$layout_template->set_var("MODULES",$modules);	
	## here we stzart to ouptut the whole date
	$layout_template->pfill_block("body");

?>
