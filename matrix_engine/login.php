<?php
  ## =======================================================================
  ##  login.php														
  ## =======================================================================
  ##  Version: 		0.01													
  ##  Last change: 	28.07.2001												
  ##  by: 			stefan elsner, workmatrix (c)												
  ## =======================================================================
  ##  28.07.2001:
  ##    * initial setup of this login page... it handles the sessions
  ##      and if you are not logged in, you willbe shown this page
  ## =======================================================================
	## prepare the template file
	$select_template = new Template(INTERFACE_DIR);
	$select_template->set_templatefile(array("body" => "login.tpl"));
	
	$select_template->set_var('BaseURL',SITE);
	
	## now we set all the vars accordingly
	$select_template->set_var('actionURL',$this->url());
	$select_template->set_var('challenge',$GLOBALS['challenge']);
	
	$select_template->set_var('version',MATRIX_VERSION);
	$select_template->set_var('lizenz',MATRIX__LIZENZ);
	$select_template->set_var('base',SITE);

	## we need to check for the language
	## first we check if the browser send us a default language
	$current_language = getLanguage();
	include("interface/lang/".$current_language.".php");
	
	$select_template->set_var('language_username',LANG_UserName);
	$select_template->set_var('language_password',LANG_UserPassword);
	$select_template->set_var('language_langselect',LANG_Language);
	$select_template->set_var('language_copyright',LANG_Copyright);


	$language_options = "";
	
	if($current_language == "english") {
		$language_options .= "<option label=\"english\" value=\"english\" selected>english</option>";
	} else {
		$language_options .= "<option label=\"english\" value=\"english\">english</option>";
	}
	
	if($current_language == "deutsch") {
		$language_options .= "<option label=\"deutsch\" value=\"deutsch\" selected>deutsch</option>";
	} else {
		$language_options .= "<option label=\"deutsch\" value=\"deutsch\">deutsch</option>";
	}

	/*
	if($current_language == "japanese") {
		$language_options .= "<option label=\"japanese\" value=\"japanese\" selected>japanese</option>";
	} else {
		$language_options .= "<option label=\"japanese\" value=\"japanese\">japanese</option>";
	}
	*/
	
	$select_template->set_var('options',$language_options);
	## here we should set the target of this form and the operation associated!
	## flush the header
	$select_template->pfill_block("body"); 
	


	## step one- we will check if the browser send
	## us a prefered language.
	## if this is not true, we will check for the default
	## language in the config file 
	function getLanguage() {
		global $HTTP_ACCEPT_LANGUAGE;
		$returnValue = DEFAULT_LANGUAGE;
		if(isset($HTTP_ACCEPT_LANGUAGE)) {
			$plng = split(',', $HTTP_ACCEPT_LANGUAGE);
			if(count($plng) > 0) {
				while(list($k,$v) = each($plng)) {
					$k = split(';', $v, 1);
					$k = split('-', $k[0]);

					if($k[0] == "de") {
						$returnValue = "deutsch";
					} else if ($k[0] == "en") {
						$returnValue = "english";
					}
				}
			}
		}
		return $returnValue;
	}	
?> 
