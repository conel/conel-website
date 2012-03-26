<?PHP

## =======================================================================        
## configSort       
## =======================================================================        
## initializes all sort modules that are installed
##   
## ======================================================================= 
function sort_configModules($vInstalledModules) {
	$output = array();
	$a = 0;
	
	## loop through the modules and get the config files
	for($i=0;$i<count($vInstalledModules);$i++) {
		$moduleSettingsFile = "sort/".$vInstalledModules[$i]."/".$vInstalledModules[$i]."_init.php";
		
		if ($file = @file($moduleSettingsFile)) {
			include($moduleSettingsFile);
			
			## and call the appropriate init functions
			$output[$a]['sortmodule'] = $vInstalledModules[$i];
			eval("\$output[\$a]['label'] = sort_module_".$vInstalledModules[$i]."_init();");
			$a++;
		}
	}
	return $output;
}

## =======================================================================        
## sort_executeModules       
## =======================================================================        
## calls the approriate Module
##   
## ======================================================================= 
function sort_executeModules($sort_method) {
	## prepare the globals
	global $_PAGE_SORTOPITIONS;

	## prepare the output var
	$output = "";

	## fetch the sort method
	$sort_method = $_PAGE_SORTOPITIONS[$sort_method];

	$moduleFile = "sort/".$sort_method."/".$sort_method.".php";
	if ($file = @file($moduleFile)) {
		include($moduleFile);
		## and call the functions
		eval("\$output = sort_module_".$sort_method."();");
	}
	return $output;
}

## =======================================================================        
##  sort_promptSortMethod     
## =======================================================================        
function sort_promptSortMethod($vID,$vInstalledModules) {
	global $gSession,$Auth;
	
	## get the sort modules strings
	$options = sort_configModules($vInstalledModules);
		
	## prepare the output
	$select_template = new Template(INTERFACE_DIR);
	$select_template->set_templatefile(array("header" => "sort_pages.tpl","body" => "sort_pages.tpl","footer" => "sort_pages.tpl"));

	## display the sort options menu
	$output = '<select name="sortmethod" size="1">';
	for($i=0;$i<count($options); $i++) {
		## set the option
		if($value == $options[$i]) {
			$output .= '<option label="'.$options[$i]['label'].'" value="'.$i.'" selected>'.$options[$i]['label'].'</option>';
		} else {
			$output .= '<option label="'.$options[$i]['label'].'" value="'.$i.'">'.$options[$i]['label'].'</option>';
		}	
	}
	$output .= '</select>';		

	$select_template->set_var('value',$output);
	$select_template->set_var("sortIMG","lang/".$Auth->auth["language"]."_sort.gif");

	$select_template->set_var('language_sorthead',LANG_SortPages);
	$select_template->set_var('language_sortbody',LANG_SortPagesDescription);
	
	## prepare the action
	$actionURL = "admin.php";
	$actionURL = $gSession->url($actionURL);
	$select_template->set_var('actionURL',$actionURL);		
	
	## here we initialize the hiddenfields	
	$output =  '<input type="hidden" name="op" value="update_menu_order">';
	$output .=  '<input type="hidden" name="menu_id" value="'.$vID.'">';
	$output .= '<input type="hidden" name="Session" value="'.$gSession->id.'">';
	$select_template->set_var("hiddenfields",$output);

	$select_template->pfill_block("header");
	$select_template->pfill_block("body");
	## here we get all the subpages
	$select_template->pfill_block("footer");
}

?>
