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
##  events        
## =======================================================================        
##  this file provides all functionality for the event handler. 
##  the event handler could be running of a different server- so we try
##  to make everything as self-contained as possible.
##  
##	the event info will be stored in mysql- this way we can reduce the time
##	need to process everything- we should keep the number of mysql calls as 
##	low as possible.
##
##	if we do get called- we will check for due or overdue events
##	execute the coresponding code and delete of update them
##
##	we support single events and repeat events
##	we also allow to add new events to the db
##
##	in the beginning the system will be used by the emailto datatype
##	and by the scheduler.
##
## ======================================================================= 


## =======================================================================        
##  event_triggerEventManager     
## =======================================================================        
##  this is the core function- it'll check for events to be executed
##	if it finds any- it will handle the execution. 
## ======================================================================= 
function event_triggerEventManager() {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];

	## prepare the db-object
	$db_connection = new DB_Sql();
	## then we need to get the xmlfile for this template
	$select_query = "SELECT id,type,code,params,`interval` FROM ".DB_PREFIX."eventmanager WHERE `due` < NOW()";
	$result = $db_connection->query($select_query);

	$events = array();
	$counter = 0;
	while($db_connection->next_record()) {
		$events[$counter]["id"]			= $db_connection->Record["id"];
		$events[$counter]["type"]		= $db_connection->Record["type"];
		$events[$counter]["code"]		= $db_connection->Record["code"];
		$events[$counter]["params"]		= $db_connection->Record["params"];
		$events[$counter]["interval"]	= $db_connection->Record["interval"];
		$counter++;
	}

	## okay now we need to include the appropriate code to execute it
	foreach($events as $current_event) {
		if(file_exists(ENGINE."events/".$current_event['code'].".php")) {
			## include the files
			include(ENGINE."events/".$current_event['code'].".php");
			## now call the code
			eval("eventhandler_".$current_event['code']."(".$current_event['params'].");");
		}
		
		## after we are done- we will update the database so that the event is either delete
		## or the due date is updated for the next called
		
		if($current_event['type'] == 'repeat'){
			$query = "UPDATE ".DB_PREFIX."eventmanager SET due = DATE_ADD(now(), INTERVAL '".$current_event['interval']."' HOUR_SECOND) WHERE id='".$current_event['id']."'";
			$result = $db_connection->query($query);
		} else {
			$query = "DELETE FROM ".DB_PREFIX."eventmanager WHERE id='".$current_event['id']."'";
			echo $query;
			$result = $db_connection->query($query);
		}
	}
	

}


## =======================================================================        
##  event_getPageEvents     
## =======================================================================        
##  fetches all events for the page
##
##  TODO: needs error checking
## ======================================================================= 
function event_getPageEvents($page_id) {
	global $gSession,$Auth;

	## prepare the db-object
	$db = new DB_Sql(); 
		
	## query the event table for the pages event
	$query = "SELECT * FROM ".DB_PREFIX."eventmanager WHERE page_id='$page_id'";
	$rp = $db->query($query);
	
	$events = array();
	while($db->next_record(MYSQL_ASSOC)) {
		$events[] = $db->Record;
	}

	return $events;
}




## =======================================================================        
##  event_getAvailableEvents        
## =======================================================================        
##  loads the extra datatypes
##  this fucntion is needed for gernal calls like "delete"
##  because there is no xml file to determine the needed extra
##
## =======================================================================
  function event_getAvailableEvents() {
    ## init vars
    $event_types = array();

    if ($dir = @opendir('events')) {
      while (($file = @readdir($dir)) !== false) {
      	## init the info string
      	$infoString = '';
      
        if ($pos = strpos($file, '.php')) {
        	$eventName = substr($file, 0,$pos);
        	if(include_once('events/'.$eventName.'.php')) {
        		## check if the getInfo funtion is availabel
        		if(function_exists($eventName.'_getInfo')) {
        			eval("\$infoString=".$eventName."_getInfo();");
					$event_types[$eventName] = $infoString;
				}
        	}
         }
      }  
      @closedir($dir);
    }
    return $event_types;
  }
?>
