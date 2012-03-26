<?PHP

## =======================================================================        
##  ui_renderTab       
## =======================================================================        
## renders a tab and returns the generated html 
##   
## ======================================================================= 
function ui_renderTab($label, $editorUrl, $counter, $active, $has_predecessors) {
	global $gSession;
	
	## init return value
	$return_value = "";
	
	## here we define the start
	if($counter == $active) {
		$image_base = 'editors/high';
	} else {
		$image_base = 'editors/norm';
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
	$template = new Template(INTERFACE_DIR);
	$template -> set_templatefile(array("begin" => "tab.tpl","middle" => "tab.tpl","modulebegin" => "tab.tpl"));
	
	## now start setting the vars
	$template->set_var('LABEL', $label);
	$template->set_var('RIGHT', $image_right);
	$template->set_var('BASE', $image_base);
	$template->set_var('EDITOR', $editorUrl);
	$template->set_var('SELF', $self);
	$template->set_var('SITE', SITE);
	
	## finally we need to set the order-
	## if the tab should be highlightes we need to
	## handle this in here too
	if ($counter == 0) {
		$return_value = $template->fill_block('begin');
	} else if($counter == 5) {
		$return_value = $template->fill_block('modulebegin');
	} else {
		$return_value = $template->fill_block('middle');
	}				

	return $return_value;	
}

## =======================================================================        
##  ui_renderSubmitTab       
## =======================================================================        
## renders a submitButton tab and returns the generated HTML
##   
## ======================================================================= 
function ui_renderSubmitTab($label, $editorUrl, $counter, $active, $has_predecessors) {
	global $gSession;
	
	## init return value
	$return_value = "";
	
	## here we define the start
	if($counter == $active) {
		$image_base = 'content/high';
	} else {
		$image_base = 'content/norm';
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
	$template = new Template(INTERFACE_DIR);
	$template -> set_templatefile(array("contentbegin" => "tab.tpl","contentmiddle" => "tab.tpl","contentmodulebegin" => "tab.tpl"));

	## now start setting the vars
	$template->set_var('LABEL', $label);
	$template->set_var('RIGHT', $image_right);
	$template->set_var('BASE', $image_base);
	$template->set_var('EDITOR', $editorUrl);
	$template->set_var('SELF', $self);
	$template->set_var('SITE', SITE);
	
	## finally we need to set the order-
	## if the tab should be highlightes we need to
	## handle this in here too
	if ($counter == 0) {
		$return_value = $template->fill_block('contentbegin');
	} else if($counter == 3) {
		$return_value = $template->fill_block('contentmodulebegin');
	} else {
		$return_value = $template->fill_block('contentmiddle');
	}				

	return $return_value;	
}

## =======================================================================        
##  ui_renderSectionTab       
## =======================================================================        
## renders a submitButton tab and returns the generated HTML
##   
## ======================================================================= 
function ui_renderSectionTab($label, $editorUrl, $counter, $active, $has_predecessors) {
	global $gSession;
	
	## init return value
	$return_value = "";
	
	## here we define the start
	if($counter == $active) {
		$image_base = 'content/high';
	} else {
		$image_base = 'content/norm';
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
	$template = new Template(INTERFACE_DIR);
	$template -> set_templatefile(array("sectionbegin" => "tab.tpl","sectionmiddle" => "tab.tpl","sectionmodulebegin" => "tab.tpl"));
	
	## now start setting the vars
	$template->set_var('LABEL', $label);
	$template->set_var('RIGHT', $image_right);
	$template->set_var('BASE', $image_base);
	$template->set_var('EDITOR', $editorUrl);
	$template->set_var('SELF', $self);
	$template->set_var('SITE', SITE);
	
	## finally we need to set the order-
	## if the tab should be highlightes we need to
	## handle this in here too
	if ($counter == 0) {
		$return_value = $template->fill_block('sectionbegin');
	} else if($counter == 3) {
		$return_value = $template->fill_block('sectionmodulebegin');
	} else {
		$return_value = $template->fill_block('sectionmiddle');
	}				

	return $return_value;	
}

## =======================================================================        
##  module_ui_displayDialogThreeOptions     
## =======================================================================        
##  displays a dialog window with threeoptions
##
##  pass it an array with the required buttons
## ======================================================================= 
function module_ui_displayDialogThreeOptions($title,$message,$buttons) {
	global $Auth;
	
	## prepare the template file
	$select_template = new Template('interface');
	$select_template->set_templatefile(array("body" => "dialog_threeoptions.tpl"));
	
	## set the buttons
	$select_template->set_var('firstIMG',"lang/".$Auth->auth["language"].'_button_'.$buttons[1]['image'].'.gif');
	$select_template->set_var('firstURL',$buttons[1]['url']);
	
	$select_template->set_var('secondIMG',"lang/".$Auth->auth["language"].'_button_'.$buttons[2]['image'].'.gif');
	$select_template->set_var('secondURL',$buttons[2]['url']);
	
	$select_template->set_var('thirdIMG',"lang/".$Auth->auth["language"].'_button_'.$buttons[3]['image'].'.gif');
	$select_template->set_var('thirdURL',$buttons[3]['url']);	
	
	## now set the title and the message
	$select_template->set_var('title',$title);
	$select_template->set_var('message',$message);
	
	$select_template->pfill_block("body");
}

?>
