<?PHP
## ======================================================================= 
##   webmatrix                                                     
## ======================================================================= 
##   Copyright (c) 2001, 2002, 2003 Stefan Elsner, Workmatrix        
## ======================================================================= 
##   <info@workmatrix.de> 
##
## ======================================================================= 
## this is a datatype plugin. 
##
## ======================================================================= 

## =======================================================================        
## data_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function date_displayInput($xmldata, $data) {
	## init the vars
	$return = "";
	
	## we should open our own template
	$template = new Template(ENGINE."datatypes/date/interface/");
	$template->set_templatefile(array("datetime" => "interface.tpl","date" => "interface.tpl"));
	
	## set the vars
	$template->set_var('element_name',$xmldata['NAME']);
	$template->set_var('element_desc',$xmldata['DESC']);
	
	## we got your record to process the data
	if(isset($data['date'])) {
		## in this case we convert the date
		$value = getdate($data['date']);
	} else {
		$value = getdate();
	}
	## set the vars accordingly
	$template->set_var('month',$value["mon"]);
	$template->set_var('day',$value["mday"]);
	$template->set_var('year',$value["year"]);
	$template->set_var('minutes',$value["minutes"]);
	$template->set_var('hours',$value["hours"]);	
	
	if(!$xmldata['TAG']) {
		$template->set_var('element_tag',LANG_ElementText);
	} else {
		$template->set_var('element_tag',$xmldata['TAG']);
	}
	
	## fill in the language specific things
	$template->set_var('DAY',LANG_DAY);
	$template->set_var('MONTH',LANG_MONTH);
	$template->set_var('YEAR',LANG_YEAR);
	$template->set_var('HOURS',LANG_HOURS);
	$template->set_var('MINUTES',LANG_MINUTES);
	
	
	if($xmldata['TIME'] == 'true') {
		return $template->fill_block("datetime");
	} else {
		return $template->fill_block("date");
	}
}

## =======================================================================        
##  keytext_storeData        
## =======================================================================        
## save the data in the db
## ======================================================================= 
function date_storeData($page_id, $identifier) {
	global $Auth,$input_language;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## we need to retrieve the data ourselves
	$month	= $_POST[$identifier."month"];
	$day	= $_POST[$identifier."day"];
	$year	= $_POST[$identifier."year"];
	
	$hours		= intval($_POST[$identifier."hours"]);
	$minutes	= intval($_POST[$identifier."minutes"]);
	
	## generate time stamp
	$date = mktime($hours,$minutes,0,$month,$day,$year);
	
	## prepare the db-object
	$db_connectionStore = new DB_Sql();

	## first we need to find out if the entry already exists
	$select_query = "SELECT date_id FROM ".PAGE_DATE." WHERE page_id = '$page_id' AND identifier = '$identifier' AND client_id='$client_id' AND language='$input_language'";
	$result_pointer = $db_connectionStore->query($select_query);	
	
	if($db_connectionStore->num_rows() == 0) { 
		## no entry found
		$insert_query = "INSERT INTO ".PAGE_DATE." (page_id, identifier, date, client_id,language) values ('$page_id', '$identifier', '$date','$client_id','$input_language')";
		$result_pointer = $db_connectionStore->query($insert_query);
	} else {
		$db_connectionStore->next_record();
		$date_id = $db_connectionStore->Record["date_id"];
		$update_query = "UPDATE ".PAGE_DATE." SET date = '$date' WHERE date_id = '$date_id' AND client_id='$client_id'";
		$result_pointer = $db_connectionStore->query($update_query);
	}
}

## =======================================================================        
##  keytext_getData        
## =======================================================================        
##  get the Data
## ======================================================================= 
function date_getData($vPageID,&$page_record) {
	global $Auth,$input_language;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## data connection
	$db_connectionMain = new DB_Sql();
		
	## get items
	$select_query = "SELECT date, identifier FROM ".PAGE_DATE." WHERE page_id='$vPageID' AND client_id='$client_id' AND language='$input_language'";
	$result_pointer = $db_connectionMain->query($select_query);

	## loop through the results and set the vars in the template
	while($db_connectionMain->next_record()) {
		$date = $db_connectionMain->Record["date"];
		$varname = $db_connectionMain->Record["identifier"]; 
		
		$page_record[$varname]["type"] = "DATE";
		$page_record[$varname]["date"] = $date; 			
	}
}


## =======================================================================        
##  keytext_deleteData        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function date_deleteData($vPageID) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## data connection
	$db_connectionMain = new DB_Sql();
	## get all text elements
	$query = "DELETE FROM ".PAGE_DATE." WHERE page_id='$vPageID' AND client_id='$client_id'";
	$result_pointer = $db_connectionMain->query($query);
}

## =======================================================================        
##  output_date        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function output_date($item,$structure,$menu_id) {
	## chekc if we have a format
	if(isset($structure['FORMAT'])) {
		$format = $structure['FORMAT']; 
	} else {
		$format = DEFAULT_DATE;
		$format_time = DEFAULT_TIME;
	}

	## prepare/format the date
	if($item['date'] > 0) {
		## okay we have a date - now we need to check in what
		## way we need to format the date
		$relative_date = _date_prepareRelativeDate($item['date'],$format);
		$date = utility_prepareDate($item['date'],$format);
	} else {
		$date = '';
	}

	return array($structure['NAME']=>$date,$structure['NAME'].'.relative'=>$relative_date);
}

## =======================================================================        
##  output_date        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function _date_prepareRelativeDate($timestamp,$format) {
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
##  date_copyData        
## =======================================================================        
##  pass it the sourcepage_id and the targetpage_id. It'll copy all
##  text entries to the new page.
## ======================================================================= 
function date_copyData($source_id, $target_id) {
	global $Auth;
	## multiclient
	$client_id = $Auth->auth['client_id'];	

	## data connection
	$db_source = new DB_Sql();
	$db_target = new DB_Sql();
	
	## get all text elements
	$select_query = "SELECT identifier,date,language FROM ".PAGE_DATE." WHERE page_id='$source_id' AND client_id='$client_id'";
	$result_pointer = $db_source->query($select_query);

	## loop through the results and copy them over
	while($db_source->next_record()) {
		$identifier = $db_source->Record['identifier'];
		$date = $db_source->Record['date'];
		$language = $db_source->Record['language'];
		
		## since it is possible that we get called muliple times for each datatype that stores the data into our tables,
		## we need to check if the entry already exists
		$query = "SELECT date_id FROM ".PAGE_DATE." WHERE page_id = '$target_id' AND identifier = '$identifier' AND client_id = '$client_id' AND language='$input_language'";
		$result_pointer = $db_target->query($query);			
	
		if($db_target->num_rows() == 0) { 
			$query = "INSERT INTO ".PAGE_DATE." (page_id, identifier, date,client_id,language,modified) values ('$target_id', '$identifier', '$date','$client_id','$language',now())";
			$result_pointer = $db_target->query($query);
		}
	}	
}
?>
