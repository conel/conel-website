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
	
	## first we will get the framework
	require("../framework.php");
	
	## now include the custom functions for this module
	require("settings.php");
	require("functions/overview.php");
	require("functions/elements.php");
	require("functions/groups.php");
	require("classes/ctlparser.php");
	require("functions/export.php");
	require("functions/filters.php");
	require("functions/install.php");
	require("functions/import.php");

	## we need to load the language specific details
	include("interface/lang/".$Auth->auth["language"].".php");

	## we will handle different application states (liek search result handling) via a session
	session_name('webmatrix'.$GLOBALS['_MODULE_DATAOBJECTS_COOKIE']);
	session_start();

	## call the setup routine to check if we are installed
	clients_install();

	## process the input vars
	$cmd = isset($_GET['cmd']) ? $_GET['cmd'] : (isset($_POST['cmd']) ? $_POST['cmd'] : '');

	## this is the main switchbox
	switch($cmd) {
		case "import":
			clients_doImport();
			break;
		case "create":
			## used clicked on add- create a stub client and display the input form
			$client_id = clients_storeClient();
			clients_displayInputForm($client_id,0);
			break;	
		case "edit":
			## we support multiple pages- determine which page we are on
			$page = intval($_GET['page']);
			
			## fetch the current client
			$client_id = intval($_GET['client']);
			if($client_id > 0) {
				clients_displayInputForm($client_id,$page);
			} else {
				displaySetup();
			}				
			break;	
		case "editor":
			$page = intval($_POST['page']);
			$client_id = intval($_POST['client']);
			
			if($client_id > 0) {
				clients_storeClient($client_id);
				clients_displayInputForm($client_id,$page);
			} else {
				displaySetup();
			}	
			break;			
		case "store":
			## we are stroitng a certain entry- so we need to pass the id 
			$client_id = intval($_POST['client']);
			
			## call the store routine
			clients_storeClient($client_id);
			## finnally redisplay the overview page
			displaySetup();
			break;
		case "save":
			$client = isset($_GET['client']) ? intval($_GET['client']): intval($_POST['client']);
			$page = isset($_GET['page']) ? intval($_GET['page']): intval($_POST['page']);

			## store the selected item
			clients_storeClient($client);

			## finally we display the input form
			clients_displayInputForm($client,$page);		
			break;
		case "delete":
			## we need to display the prompt if the user really wants to delete the page
			$actionURL = "module.php?cmd=dodelete";
	
			$items_to_delete = $_POST['rows_to_delete'];
			ui_output_PromptDelete(LANG_MODULE_CLIENTS_DeleteTitle,LANG_MODULE_CLIENTS_DeleteDesc,$actionURL,$items_to_delete);
			break;
		case "dodelete":
			## now we can actually delete the clients
			$items_to_delete = $_POST['rows_to_delete'];
			clients_deleteClients($items_to_delete);
			
			## redisplay the listing
			displaySetup();
			break;
		case "export":
			## we need to prepare the query... 
			clients_exportClients($_GET['query'],intval($_GET['group']));
			break;	
		case "filter":
			## we need to prepare the query... 
			$actionURL = "module.php";
			$actionURL = $gSession->url($noURL);
			
			clients_displayInputName('want to save the filter?',$actionURL);
			break;		
		case "savefilter":
			## we need to prepare the query... 
			$data = $_SESSION['data'];
			clients_storeFilter($data,$_POST['menu_text']);
			
			## we are done so we need to reroute to the overview page
			$_GET['query'] = $_POST['query'];
			displaySetup();
			break;	
		case "applyFilter":
			## the user selected a filter- we apply it and display the page again
			$filter_id = intval($_GET['filter']);
			
			## we need to load the data for this filter- and set the session accordingly
			$data = clients_getFilter($filter_id);
			
			$_SESSION['data'] = unserialize($data['searchdata']);
			
			displaySetup();
			break;			
    	default:
			## we need to display the setup screen
			displaySetup();
      	break;
    }

?>
