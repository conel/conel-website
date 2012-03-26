<?PHP
include_once("functions/ui_dialogs.php");

## we should delete the session-vars etc.

## we need to check for the language
## first we check if the browser send us a default language
$current_language = getLanguage();
include_once("interface/lang/".$current_language.".php");

## we will use HTTP_POST_VARS to get all available variables

##global $HTTP_GET_VARS, $HTTP_POST_VARS, $QUERY_STRING;

##page_open(array("session" => "session_object", "authenticate" => "Auth")); 
##page_close();
ui_output_error("<b>".LANG_LoginError."</b><br><br>".LANG_NamePassUnknown."<br><br>");

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
