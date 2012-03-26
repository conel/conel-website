<?php

## =======================================================================        
##  clients_exportClients       
## =======================================================================        
##  we will export the currently selected clients- in order to do this,
##  we need to get a query id- which we will use to get all clients
##
##  using the clients list, we will then call the attributetypes- and
##  get the appropriate data
##
##  TODO:
##  
## ======================================================================= 
function clients_exportClients($query,$group) {
	global $Auth;

	## prepare the query
	## first we get the controlfile data for this group
	$ctl_data = _getFieldsFromGroup(1);
	
	## the first step is to get the data required for any search we are doing
	$data = $_SESSION['data'];

	## we need to check if the user wants to search something
	$query = _prepareSearch(1,$data,$ctl_data,$sort,$direction);
	$clients = clients_SearchClients($query,0,11000,2);

	## then we need to send headers- that fore a download

	header('Content-Type: application/x-csv');
	header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Content-Disposition: attachment; filename="export.csv"');
	header('Pragma: no-cache');
	header('Pragma: public');

	
	## prepare the ctlFile
	## first get the control file
	## get the group info
	$wt = new ctlparser(MATRIX_BASEDIR.'settings/modules/'.$GLOBALS['_MODULE_DATAOBJECTS_NAME'].'/base.xml');
	$wt->parse();		
	$elements = $wt->getElements();	

	$output = '"id","email"';
	foreach($elements as $current_row) {
		foreach($current_row as $current_element) {
			$type = strtolower($current_element['TYPE']);
			$identifier = strtolower($current_element['IDENTIFIER']);

			## first we try to include the apropriate file 
			@include_once(MATRIX_BASEDIR."/matrix_engine/modules/clients/attributetypes/".$type."/attribute.php");
			## now we check if the function exists
			if(function_exists("clients_".$type."_getExportData")) {
				$output .= ',"'.$identifier.'"';
			}
		}
	}
	print $output."\r\n";
	
	foreach($clients['data'] as $current_client) {
		$output = '"'.$current_client['id'].'","'.$current_client['email'].'"'; 
		
		## now loop through all ctl-Elements
		foreach($elements as $current_row) {
			## process the rows- first we need to find out how many entries
			## are in this row 
			
			$counter = 1;
			foreach($current_row as $current_element) {
				## here we start calling all our attribute types
				$type = strtolower($current_element['TYPE']);
				$identifier = strtolower($current_element['IDENTIFIER']);
	
				## first we try to include the apropriate file 
				@include_once("attributetypes/".$type."/attribute.php");
				## now we check if the function exists
				if(function_exists("clients_".$type."_getExportData")) {
					## no we call the function
					eval("\$element = clients_".$type."_getExportData(\$current_element,".$current_client['id'].");");
					## gather all elements for later processing
					$output .= ',"'.str_replace("\r\n",' ',$element).'"';
				}							
			}
		}		
	
		print $output."\r\n";
	}	
	


	##var_dump($clients);
}


?>