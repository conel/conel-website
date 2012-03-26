<?PHP
## ======================================================================= 
##   webmatrix                                                     
## ======================================================================= 
##   Copyright (c) 2001, 2002, 2003 Stefan Elsner, Workmatrix        
## ======================================================================= 
##   <info@workmatrix.de> 
##
## ======================================================================= 

## =======================================================================        
##  language_getLanguages        
## =======================================================================        
##  returns a list with all available languages
##
## ======================================================================= 
function language_getLanguages() {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## get the db class
	$db_connection = new DB_Sql();
	
	$query = "SELECT language_id, name FROM ".LANGUAGE." WHERE client_id='$client_id' ORDER BY language_id";
	$result = $db_connection->query($query);	

	$languages = array();
	$i = 0;
	while($db_connection->next_record()) {
		$languages[$i]['id'] = $db_connection->Record['language_id'];
		$languages[$i]['name'] = $db_connection->Record['name'];
		
		$i++;
	}

	return $languages;
}

## =======================================================================        
##  language_generateTabs        
## =======================================================================        
##  generates the tabs- used for data entry/input template
##
## ======================================================================= 
function language_generateTabs($selected_language) {
	
	## init the vars
	$output = "";
	
	## first we get the available languages
	$languages = language_getLanguages();
	
	
	## now for each language
	$count = count($languages);
	for($i=0; $i < $count; $i++) {
		## check if we have followups
		$has_followups = ($i == ($count-1)) ? false : true;

		## finally generate the tab
		$output .= ui_renderSubmitTab(utf8_encode($languages[$i]['name']),$languages[$i]['id'],$i,($selected_language),$has_followups);
	}
	
	return $output;
}

## =======================================================================        
##  language_registerLanguage        
## =======================================================================        
##  checks if we recieved a language request- if not, we swicth back to
##	the default language
##
## ======================================================================= 
function language_registerLanguage() {
	global $input_language;
	
	$input_language = isset($_GET['language']) ? $_GET['language'] : (isset($_POST['language']) ? $_POST['language'] : DEFAULT_INPUTLANGUAGE);	
}
?>
