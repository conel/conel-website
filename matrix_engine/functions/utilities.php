<?php

## =======================================================================		
## checkFlag	   
## =======================================================================		
## draws the structure as an expandable menu
##	
## =======================================================================
function checkFlag($settings, $option) {
	if( ($settings & $option) == $option ) {
		return true;
	} else {
		return false;
	}
}



## =======================================================================		
##  getTargetURL	   
## =======================================================================		
## recieves the page_id and returns the appropriate url
## depending on the caller type  
##   
## ======================================================================= 
function getTargetURL($vPageID) {
	global $input_language;
	
	## init return value
	$return_value = "";

	## use the standard page
	if(URL_REWRITE) {
		## okay we need to find out the page hierachy to get to that page
		$path = structure_getPath($vPageID);
		$return_value = '/'.join('/',array_reverse($path));
	} else {	
		if($input_language != DEFAULT_INPUTLANGUAGE) {
			$return_value = CONTENT_PAGE."?page_id=".$vPageID.'&language='.$input_language;
		} else {
			$return_value = CONTENT_PAGE."?page_id=".$vPageID;
		}
	}
	return $return_value;	
}



## =======================================================================		
##  convert_general		
## =======================================================================		
##  take the userinput and converts it into a from which we'll
##  use to store into the database   
##
##  TODO:	   
## ======================================================================= 
function convert_general($sourceString) {
	##$sourceString = addslashes($sourceString);
	$sourceString = htmlentities($sourceString);
	$sourceString=ereg_replace('&lt;', '<', ereg_replace('&gt;', '>', $sourceString));

	$sourceString = ereg_replace("(\r\n|\n|\r)", "<br />", $sourceString);
		
	##$sourceString = nl2br($sourceString);
		
	return $sourceString;
}

## =======================================================================		
##  convert_html		
## =======================================================================		
##  takes a string from the database and transforms it into
##  a form which can be displayed by the browsers  
##
##  TODO:	   
## ======================================================================= 
function convert_html($sourceString) {
	##$sourceString = stripslashes($sourceString);
	$translationTable = get_html_translation_table(HTML_ENTITIES);
	$translationTable = array_flip ($translationTable);
	$sourceString = strtr($sourceString,$translationTable);
	
	/* Convert all standard and XHTML-compliant tags to newlines */
	$sourceString = eregi_replace('<br[[:space:]]*/?[[:space:]]*>', "\n", $sourceString);

	return $sourceString;
}
	
## =======================================================================		
##  close_reload		
## =======================================================================		
##  closes the current window and updates the parent window   
##
##  TODO:
## =======================================================================		
function close_reload($targetURL) {
	global $gSession;
	## prepare the template file
	$select_template = new Template(INTERFACE_DIR);
	$select_template->set_templatefile(array("body" => "closewindow.tpl"));
	
	$targetURL = $gSession->url($targetURL);
	
	$select_template->set_var('targetURL',$targetURL);
	$select_template->pfill_block("body");
}

## =======================================================================		
##  display_interface_page		
## =======================================================================		
##  used to display the confirmation pages and so on....  
##
##  TODO:
## =======================================================================		
function display_interface_page($template) {
	## prepare the template file
	$select_template = new Template(INTERFACE_DIR);
	$select_template->set_templatefile(array("body" => $template));
	
	$select_template->pfill_block("body");
}


## =======================================================================		
##  prepare link
## =======================================================================		
##  used to display the confirmation pages and so on....  
##
##  TODO:
## =======================================================================		
function utility_preparelink($url) {
	$url = str_replace("http://","",$url);
	
	if(substr($url,0,3) == "www") {
		## then we need to add the http:## by default
		$url = "http://".$url;
	} else {
		$url = $url;
	}
	return $url;
}

## =======================================================================		
##  convert_general		
## =======================================================================		
##  take the userinput and converts it into a from which we'll
##  use to store into the database   
##
##  TODO:	   
## ======================================================================= 
function utility_prepareDate($timestamp,$format="") {
	$returnvalue = "";
	if($format=="") {
		## let's format it to the standard
		$returnvalue = date(DEFAULT_DATE,$timestamp);
	} else {
		$returnvalue = date($format,$timestamp);
	}
	return $returnvalue;
}

## =======================================================================        
##  output_date        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function utility_prepareDateRelative($timestamp,$format="") {
	## first we will need to determine the difference to the current time
	$time_difference = time() - $timestamp;

	## first check if we are in the future or past
	$dates = array();
	if($time_difference > 0) {	
		$dates['time'] = 'past';
	} else {
		$dates['time'] = 'future';
		$time_difference = abs($time_difference);
	}
	
	## now we caluclate the offsets
	$dates['months'] = floor($time_difference/2592000);
	$time_difference -= $dates['months']*2419200;
	
	$dates['weeks'] = floor($time_difference/604800);
	$time_difference -= $dates['weeks']*604800;
	
	$dates['days'] = floor($time_difference/86400);
	$time_difference -= $dates['days']*86400;
	
	$dates['hours'] = floor($time_difference/3600);
	$time_difference -= $dates['hours']*3600;
	
	$dates['minutes'] = floor($time_difference/60);
	$time_difference -= $dates['minutes']*60;
	
	$dates['seconds'] = $time_difference;

	if($dates['months'] > 0) {
		## okay we need to output the default date
		return utility_prepareDate($timestamp,$format);
	} else {
		if($dates['weeks'] > 0) {
			## weeks and days
			$relative_date .= ($relative_date ? ', ' : '').$dates['weeks'].' week'.($dates['weeks'] > 1 ? 's' : '');
			$relative_date .= $dates['days'] > 0 ? ($relative_date ? ' and ' : '').$dates['days'].' day'.($dates['days'] > 1 ? 's' : ''):'';
		} elseif ($dates['days'] > 0) {
			## days and hours
			$relative_date .= ($relative_date ? ', ' : '').$dates['days'].' day'.($dates['days'] > 1 ? 's' : '');
			$relative_date .= $dates['hours'] > 0 ? ($relative_date ? ' and ' : '').$dates['hours'].' hour'.($dates['hours'] > 1 ? 's' : ''):'';
		} elseif ($dates['hours'] > 0) {
			## hours and minutes
			$relative_date .= ($relative_date ? ', ' : '').$dates['hours'].' hour'.($dates['hours'] > 1 ? 's' : '');
			$relative_date .= $dates['minutes'] > 0 ? ($relative_date ? ' and ' : '').$dates['minutes'].' minute'.($dates['minutes'] > 1 ? 's' : ''):'';
		} elseif ($dates['minutes'] > 0) {
			## minutes
			$relative_date .= ($relative_date ? ', ' : '').$dates['minutes'].' minute'.($dates['minutes'] > 1 ? 's' : '');
		} else {
			## seconds
			$relative_date .= ($relative_date ? ', ' : '').$dates['seconds'].' second'.($dates['seconds'] > 1 ? 's' : '');
		}
	}
	
	## depending on the the tense- we add the proper addon
	if($dates['time'] == 'future') {
		return 'in '.$relative_date;
	} else {
		return $relative_date.' ago';
	}
}


## =======================================================================		
##  convert_general		
## =======================================================================		
##  take the userinput and converts it into a from which we'll
##  use to store into the database   
##
##  TODO:	   
## ======================================================================= 
function utility_calculateRelativeDate($sourceTimestamp,$targetTimestamp) {
	## init the vars
	$return_value = '';
	
	
	## we need to differentiate between future and past 
	if($sourceTimestamp > $targetTimestamp) {
		## first we will calcute the difference
		$time_difference = $sourceTimestamp - $targetTimestamp;
		
		## first hanlde the minutes
		if($time_difference < 3600) {
			if($time_difference < 120) {
				$return_value = '1 minute ago';
			} else {
				$return_value = intval($time_difference / 60).' minutes ago';
			}
		} else if($time_difference < 7200) {
			$return_value = '1 hour ago';
		} else if($time_difference < 86400) {
			$return_value = intval($time_difference / 3600) . " hours ago";
		} else if ($time_difference < 172800) { 
			$return_value = "1 day ago"; 
		} else if ($time_difference < 604800) { 
			$return_value = intval($timediff / 86400) . " days ago"; 
		} else if ($time_difference < 1209600) { 
			$return_value = "1 week ago"; 
		} else if ($time_difference < 3024000) { 
			$return_value = intval($time_difference / 604900) . " weeks ago"; 
		} else { 
			$return_value = utility_prepareDate($targetTimestamp); 
		}		
	} else {
		## first we will calcute the difference
		$time_difference = $targetTimestamp - $sourceTimestamp;
			
		## first hanlde the minutes
		if($time_difference < 3600) {
			if($time_difference < 120) {
				$return_value = 'in 1 minute';
			} else {
				$return_value = 'in '.intval($time_difference / 60).' minutes';
			}
		} else if($time_difference < 7200) {
			$return_value = 'in 1 hour';
		} else if($time_difference < 86400) {
			$return_value = 'in '.intval($time_difference / 3600) . " hours";
		} else if ($time_difference < 172800) { 
			$return_value = 'in 1 day'; 
		} else if ($time_difference < 604800) { 
			$return_value = 'in '.intval($timediff / 86400) . " days"; 
		} else if ($time_difference < 1209600) { 
			$return_value = 'in 1 week'; 
		} else if ($time_difference < 3024000) { 
			$return_value = 'in '.intval($timediff / 604900) . " weeks"; 
		} else { 
			$return_value = utility_prepareDate($targetTimestamp); 
		}		
	}
		
	return $return_value;
}


	## =======================================================================
	## dispatcher_parseURL
	## =======================================================================
	## pass it a url and it returns controller,action and parameteres
	##
	## =======================================================================
	function utility_dispatcherParseURL($url) {
		## we need to prepare the url
		## An URL should start with a '/', mod_rewrite doesn't respect that, Here's the fix.
		if ($url && ('/' != $url[0])) {
			$url = '/'.$url;
		}
		
		$out = array();
		$regexp = '/([a-zA-Z<> &0-9_\\-\\.]+)/';
		
		if (preg_match_all($regexp, $url, $r)) {
			## remove the first rows
			$out = $r[0];
		}

		return $out;
	}
	
	## =======================================================================
	## utility_convertString2URL
	## =======================================================================
	## takes a string and cleans it so it looks nice as a url
	##
	## =======================================================================
	function utility_convertString2URL($string) {
		$string = strtolower(htmlentities($string));
		$string = preg_replace("/&(.)(uml);/", "$1e", $string);
		$string = preg_replace("/&(.)(acute|cedil|circ|ring|tilde|uml);/", "$1", $string);
		$string = preg_replace("/([^a-z0-9]+)/", "_", html_entity_decode($string));
		$string = trim($string, "_");
		return $string;
	}	
	
	## =======================================================================
	## var_export
	## =======================================================================
	## this function is here for backwards compatibility with older
	## php4 versions
	##
	## =======================================================================
	if (!function_exists('var_export')) {
		function var_export($var, $return = false, $level = 0) {
			## init the vars	
			$indent	  = '  ';
			$doublearrow = ' => ';
			$lineend	 = ",\n";
			$stringdelim = '\'';
			$newline	 = "\n";
			$find		= array(null, '\\', '\'');
			$replace	 = array('NULL', '\\\\', '\\\'');
			$out		 = '';
		
			## ident
			$level++;
			for ($i = 1, $previndent = ''; $i < $level; $i++) {
				$previndent .= $indent;
			}

			## Handle each type
			switch (gettype($var)) {
				## Array
				case 'array':
					$out = 'array (' . $newline;
					foreach ($var as $key => $value) {
						## Key
						if (is_string($key)) {
							## Make key safe
							for ($i = 0, $c = count($find); $i < $c; $i++) {
								$var = str_replace($find[$i], $replace[$i], $var);
							}
							$key = $stringdelim . $key . $stringdelim;
						}
						
						## Value
						if (is_array($value)) {
							$export = var_export($value, true, $level);
							$value = $newline . $previndent . $indent . $export;
						} else {
							$value = var_export($value, true, $level);
						}
			
						## Piece line together
						$out .= $previndent . $indent . $key . $doublearrow . $value . $lineend;
					}
			
					## End string
					$out .= $previndent . ')';
					break;
			
				## String
				case 'string':
					## Make the string safe
					for ($i = 0, $c = count($find); $i < $c; $i++) {
						$var = str_replace($find[$i], $replace[$i], $var);
					}
					$out = $stringdelim . $var . $stringdelim;
					break;
			
				## Number
				case 'integer':
				case 'double':
					$out = (string) $var;
					break;
				
				## Boolean
				case 'boolean':
					$out = $var ? 'true' : 'false';
					break;
			
				## NULLs
				case 'NULL':
				case 'resource':
					$out = 'NULL';
					break;
			
				## Objects
				case 'object':
					## Start the object export
					$out = $newline . $previndent . 'class ' . get_class($var) . ' {' . $newline;
			
					## Export the object vars
					foreach (get_object_vars($var) as $key => $val) {
						$out .= $previndent . '  var $' . $key . ' = ';
						if (is_array($val)) {
							$export = var_export($val, true, $level);
							$out .= $newline . $previndent . $indent .  $export  . ';' . $newline;
						} else {
							$out .= var_export($val, true, $level) . ';' . $newline;
						}
					}
					$out .= $previndent . '}';
					break;
			}

			## Method of output
			if ($return === true) {
				return $out;
			} else {
				echo $out;
			}
		}
	}
	
?>