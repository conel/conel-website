<?PHP
## =======================================================================        
##  output_error        
## =======================================================================        
##  display an error message using the webmatrix-Layout
##
##  TODO:
##  
## ======================================================================= 
function ui_output_error($errorMessage) {
	global $gSession;
	## prepare the template file
	$select_template = new Template(INTERFACE_DIR);
	$select_template->set_templatefile(array("body" => "error.tpl"));
	
	$select_template->set_var('BaseURL',SITE);
	$select_template->set_var('errorMessage',$errorMessage);
	$select_template->set_var('language_copyright',LANG_Copyright);		
	$select_template->pfill_block("body");
}	


## =======================================================================        
##  output_confirm        
## =======================================================================        
##  display a confirmation page that the current
##  action was completed succesfully
##
##  TODO:
##  
## ======================================================================= 
function ui_output_Dialog($title,$message,$target,$hiddenfields) {
	global $gSession,$Auth;
	$db_connectionLayout = new DB_Sql();  

	## prepare the template file
	$select_template = new Template(INTERFACE_DIR);
	$select_template->set_templatefile(array("body" => "dialog_twooptions.tpl"));
	
	$select_template->set_var("yesIMG","lang/".$Auth->auth["language"]."_button_ja.gif");
	$select_template->set_var("noIMG","lang/".$Auth->auth["language"]."_button_nein.gif");
	$select_template->set_var('language_deletepage',$title);
	$select_template->set_var('language_doyouwant',$message);
	$select_template->set_var('site',SITE);

	$actionURL = $gSession->url($target);
	$select_template->set_var('actionURL',$actionURL);

	$hidden = '';
	foreach($hiddenfields as $key=>$val) {
		$hidden .= '<input name="'.$key.'" id="'.$key.'" type="hidden" value="'.$val.'">';
	}
	$select_template->set_var('hiddenfields',$hidden);


	$select_template->pfill_block("body");
}

## =======================================================================        
##  output_confirm        
## =======================================================================        
##  display a confirmation page that the current
##  action was completed succesfully
##
##  TODO:
##  
## ======================================================================= 
function ui_output_PromptDelete($title,$message,$target,$items) {
	global $gSession,$Auth;
	$db_connectionLayout = new DB_Sql();  

	## prepare the template file
	$select_template = new Template(INTERFACE_DIR);
	$select_template->set_templatefile(array("body" => "dialog_twooptions.tpl"));
	
	$select_template->set_var("yesIMG","lang/".$Auth->auth["language"]."_button_ja.gif");
	$select_template->set_var("noIMG","lang/".$Auth->auth["language"]."_button_nein.gif");
	$select_template->set_var('language_deletepage',$title);
	$select_template->set_var('language_doyouwant',$message);
	$select_template->set_var('site',SITE);

	$actionURL = $gSession->url($target);
	$select_template->set_var('actionURL',$actionURL);

	$hidden = '';
	foreach($items as $current_item) {
		$hidden .=  '<input type="hidden" name="rows_to_delete[]" value="'.$current_item.'">';
	}
	
	$select_template->set_var('hiddenfields',$hidden);


	$select_template->pfill_block("body");
}

## =======================================================================        
##  output_confirm        
## =======================================================================        
##  display a confirmation page that the current
##  action was completed succesfully
##
##  TODO:
##  
## ======================================================================= 
function ui_output_confirm($title,$message,$targetURL,$mainURL='') {
	global $gSession;

	## prepare the template file
	$select_template = new Template(INTERFACE_DIR);
	$select_template->set_templatefile(array("body" => "dialog_confirm.tpl"));
	$select_template->set_var('title',$title);
	$select_template->set_var('message',$message);
	$select_template->set_var('site',SITE);
	
	if($mainURL != '') {
		$mainURL = "<meta http-equiv='refresh' content='0; url=".$mainURL."'>";
		$select_template->set_var('MAINREFRESH',$mainURL);
	}
	
	
	$targetURL = $gSession->url($targetURL);
	$select_template->set_var('targetURL',$targetURL);
	
	$select_template->pfill_block("body");
}

## =======================================================================        
##  output_confirm        
## =======================================================================        
##  display a confirmation page that the current
##  action was completed succesfully
##
##  TODO:
##  
## ======================================================================= 
function ui_output_commentField($title,$message,$actionURL,$hiddenfields) {
	global $gSession,$Auth;

	## prepare the template file
	$select_template = new Template(INTERFACE_DIR);
	$select_template->set_templatefile(array("body" => "comment.tpl"));
	
	$select_template->set_var('site',SITE);
	$select_template->set_var('title',$title);
	$select_template->set_var('message',$message);
	
	$select_template->set_var("saveIMG","lang/".$Auth->auth["language"]."_button_save.gif");
	
	$actionURL = $gSession->url($actionURL);
	$select_template->set_var('actionURL',$actionURL);
	
	$hidden = '';
	foreach($hiddenfields as $key=>$val) {
		$hidden .= '<input name="'.$key.'" id="'.$key.'" type="hidden" value="'.$val.'">';
	}
	$select_template->set_var('hiddenfields',$hidden);
	
	$select_template->pfill_block("body");
}


## =======================================================================        
##  output_confirm        
## =======================================================================        
##  display a confirmation page that the current
##  action was completed succesfully
##
##  TODO:
##  
## ======================================================================= 
function ui_output_InputDialog($title,$message,$actionURL,$hiddenfields) {
	global $gSession,$Auth;

	## prepare the template file
	$select_template = new Template(INTERFACE_DIR);
	$select_template->set_templatefile(array("body" => "dialog_input.tpl"));
	
	$select_template->set_var('site',SITE);
	$select_template->set_var('title',$title);
	$select_template->set_var('message',$message);
	
	$select_template->set_var("saveIMG","lang/".$Auth->auth["language"]."_button_save.gif");
	
	$actionURL = $gSession->url($actionURL);
	$select_template->set_var('actionURL',$actionURL);
	
	$hidden = '';
	foreach($hiddenfields as $key=>$val) {
		$hidden .= '<input name="'.$key.'" id="'.$key.'" type="hidden" value="'.$val.'">';
	}
	$select_template->set_var('hiddenfields',$hidden);
	
	$select_template->pfill_block("body");
}
?>