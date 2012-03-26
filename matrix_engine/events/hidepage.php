<?PHP
## ======================================================================= 
## eventhandler                                                     
## ======================================================================= 
##   Copyright (c) 2001, 2002, 2003 Stefan Elsner, Workmatrix        
## ======================================================================= 
##   <info@workmatrix.de> 
##
## ======================================================================= 


## =======================================================================        
##  eventhandler_hidepage      
## =======================================================================        
##  this function get's called by the eventmanager
##
## ======================================================================= 
function hidepage_getInfo($params=null) {
	## we need to return the info required for the pulldown menue
	## in the event edit and creation process
	
	return 'Seiten verstecken';
}



## =======================================================================        
##  eventhandler_hidepage      
## =======================================================================        
##  this function get's called by the eventmanager
##
## ======================================================================= 
function hidepage_eventhandler($params=null) {
	## we are not expecting to get any params
	return;
	## we have our own mail class- try to connect
	$test = new mailReceiver('pop3.workmatrix.de','542007','ufd3m8da');
	$test->connectToServer(CL_EXPUNGE);
	return;
	$message_count = $test->getMessageCount();
	for($i = 1;  $i<=$message_count; $i++) {
		$header = $test->getHeader($i);
	
		## get the subject and from email
		## they both have to match our settings.
		$subject = $header->subject;
		$from = $header->fromaddress;
	
		## process the subject
		preg_match_all("|\[(.*)\]|Usi",$subject,$raw_data);
	
		if($raw_data[1][0] == 'op45ukl' && $from == 'stefan@workmatrix.de') {
			## now assume that everything is alright
			$content = $test->parseMessage($i);
			foreach($content as $current_element) {
				## we need to determine if if have a text element or an attachment
				if(isset($current_element['text'])) {
					$message_body = $current_element['text']['string'];
				} else if(isset($current_element['attachment'])) {
					$files[] = $current_element['attachment']['filename'];
				}
			}
			
			## okay we have a mail- for which we need to create a new page
			
			
			echo $subject;
			echo $message_body;
			var_dump($files);
		} else {
		
		## finally delete the message
		$test->deleteMessage($i);
		}
	}
	
	$test->closeConnection(CL_EXPUNGE);
	
}




?>
