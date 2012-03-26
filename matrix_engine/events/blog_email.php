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
##  eventhandler_blog_email      
## =======================================================================        
##  this function get's called by the eventmanager
##
## ======================================================================= 
function eventhandler_blog_email($params=null) {
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





	## =======================================================================
	##  class_pop3.php														
	## =======================================================================
	##  Version: 		0.01													
	##  Last change: 	31.12.006												
	##  by: 			S. Elsner											
	## =======================================================================
	##
	##  handles recieving of emails
	## =======================================================================
	class mailReceiver {
		## we will store all settings here
		var $Server 	= '';
		var $User 		= '';
		var $Password 	= '';
		var $Port		='110/pop3/notls';
		
		## here we store the connection if we have one
		var $Link_ID		= 0;

		## here we store infos about a given message
		var $message_data	= array();		
		

		## here we store infos about a given message
		var $file_path	= '/Users/stefan/Sites/dev/mailer/mimeparser-2007-02-19/files/';			
	
		## =======================================================================        
		##  mailReceiver        
		## =======================================================================        
		##  constructor   
		##       
		## =======================================================================        		
		function mailReceiver($Server=null,$User=null,$Password=null,$Port=null) {
			$this->Server = isset($Server) ? $Server : $this->Server;
			$this->User = isset($User) ? $User : $this->User;
			$this->Password = isset($Password) ? $Password : $this->Password;
			$this->Port = isset($Port) ? $Port : $this->Port;		
		}
	
		## =======================================================================        
		##  connectToServer        
		## =======================================================================        
		##  constructor   
		##       
		## =======================================================================        		
		function connectToServer($option=OP_READONLY) {
			
   			## check if we already have a connection
   			if($this->Link_ID == 0) {
    			$this->Link_ID = imap_open("{".$this->Server.":".$this->Port."}INBOX",$this->User,$this->Password,$option);
    			
    			if(!$this->Link_ID) {
    				$this->halt("Cannot create POP3 connection to"." ".$this->Server.": ".imap_last_error());
    				return 0;
    			}
    		}
    		return $this->Link_ID;
    	}    	
  	
		## =======================================================================        
		##  getMessageCount        
		## =======================================================================        
		##  constructor   
		##       
		## =======================================================================        		
		function getMessageCount() {
   			## check if we already have a connection
			if(!$this->connectToServer()) {
      			return 0; ## no connection- error was already handled in connectToServer
    		}
    		
    		## if we are still here- we will fetch the messages
    		return imap_num_msg($this->Link_ID);
    	}
  	
		## =======================================================================        
		##  getRawHeader        
		## =======================================================================        
		##  constructor   
		##       
		## =======================================================================        		
		function getRawHeader($message_id) {
   			## check if we already have a connection
			if(!$this->connectToServer()) {
      			return 0; ## no connection- error was already handled in connectToServer
    		}
    		
    		## if we are still here- we will fetch the messages
    		return imap_fetchheader($this->Link_ID,$message_id);
    	}     	
  	
		## =======================================================================        
		##  getHeader        
		## =======================================================================        
		##  constructor   
		##       
		## =======================================================================        		
		function getHeader($message_id) {
   			## check if we already have a connection
			if(!$this->connectToServer()) {
      			return 0; ## no connection- error was already handled in connectToServer
    		}
    		
    		## if we are still here- we will fetch the messages
    		return imap_headerinfo($this->Link_ID,$message_id);
    	}

  	
		## =======================================================================        
		##  getBody        
		## =======================================================================        
		##  constructor   
		##       
		## =======================================================================        		
		function getBody($message_id) {
   			## check if we already have a connection
			if(!$this->connectToServer()) {
      			return 0; ## no connection- error was already handled in connectToServer
    		}
    		
    		## if we are still here- we will fetch the messages
    		return imap_body($this->Link_ID,$message_id);
    	}  	
  	
		## =======================================================================        
		##  getStructure        
		## =======================================================================        
		##  constructor   
		##       
		## =======================================================================        		
		function getStructure($message_id) {
   			## check if we already have a connection
			if(!$this->connectToServer()) {
      			return 0; ## no connection- error was already handled in connectToServer
    		}
    		
    		## if we are still here- we will fetch the messages
    		return imap_fetchstructure($this->Link_ID,$message_id);
    	}     	
  	
		## =======================================================================        
		##  parseMessage        
		## =======================================================================        
		##  completly parse a message- will create files and returns an array
		## 	with the information for this message 
		##       
		## =======================================================================        		
		function parseMessage($message_id) {
   			## check if we already have a connection
			if(!$this->connectToServer()) {
      			return 0; ## no connection- error was already handled in connectToServer
    		}
    		
    		## first get the structural information for the message
    		$message_structure = $this->getStructure($message_id);

    		## check if this is a multipart message
    		if(isset($message_structure->parts) && count($message_structure->parts) > 0) {
    			foreach($message_structure->parts as $current_part => $current_part_array) {
    				## now call our processing routine
    				$this->_processPart($message_id,$current_part_array, $current_part + 1);
    			}
    		} else {
    			$message_text = $this->getBody($message_id);
    			
    			## decode if quoted-printable
    			if ($message_structure->encoding==4) {
    				$message_text = quoted_printable_decode($message_text);
    			}
    			
    			 $this->message_data[1]['text'] = array('type' => $message_structure->subtype,'string'=>$message_text);
    		}
    		
    		## if we are still here- we will fetch the messages
    		return $this->message_data;
    	}    	
  	
   	
		## =======================================================================        
		##  parseMessage        
		## =======================================================================        
		##  completly parse a message- will create files and returns an array
		## 	with the information for this message 
		##       
		## =======================================================================        		
		function _processPart($message_id,$p,$i) {
			## fetch the current part of the message
			$part = imap_fetchbody($this->Link_ID,$message_id,$i);

			## check the type of the part and decode if nescessary
			if($p->type!=0){
				## decode if base64
				if($p->encoding==3) $part=base64_decode($part);
				## decode if quoted printable
				if($p->encoding==4)$part=quoted_printable_decode($part);
		 	 	 
				## get filename of attachment if present
				$filename='';
				## if there are any parameters present in this part
				if(count($p->dparameters) > 0) {
					foreach ($p->dparameters as $dparam) {
						if ((strtoupper($dparam->attribute)=='NAME') ||(strtoupper($dparam->attribute)=='FILENAME')) $filename=$dparam->value;
					}
				}
				
		 	 	## if no filename found
				if ($filename==''){
					## if there are any parameters present in this part
					if(count($p->parameters) > 0) {
						foreach($p->parameters as $param) {
							if((strtoupper($param->attribute)=='NAME') ||(strtoupper($param->attribute)=='FILENAME')) $filename=$param->value;
						}
					}
				}
		 	 	
		 	 	## write to disk and set partsarray variable
				if($filename != '') {
					$this->message_data[$i]['attachment'] = array('filename'=>$filename);
					$fp=fopen($this->file_path.$filename,"w+");
					fwrite($fp,$part);
					fclose($fp);
				}	 	 	 	 
			} else if($p->type == 0) {
				## decode text
				if($p->encoding==4) $part=quoted_printable_decode($part);
				## if base 64
				if($p->encoding==3) $part=base64_decode($part);
		 	 	 
		 	 	$this->message_data[$i]['text'] = array('type'=>$p->subtype,'string'=>$part);
			}
		 
			## if subparts... recurse into function and parse them too!
			if(isset($p->parts) && count($p->parts) > 0) {
				foreach($p->parts as $pno => $parr) {
					$this->_processPart($message_id,$parr,($i.'.'.($pno+1)));	 	 	 	 	 	 
				}
			}
    	} 	
  	
		## =======================================================================        
		##  deleteMessage        
		## =======================================================================        
		##  constructor   
		##       
		## =======================================================================        		
		function deleteMessage($message_id) {
   			## check if we already have a connection
			if(!$this->connectToServer()) {
      			return 0; ## no connection- error was already handled in connectToServer
    		}
    		
    		## if we are still here- we will fetch the messages
    		return imap_delete($this->Link_ID,$message_id);
    	}    	
  	
		## =======================================================================        
		##  closeConnection        
		## =======================================================================        
		##  constructor   
		##       
		## =======================================================================        		
		function closeConnection($options=null) {
   			## check if we already have a connection
			if($this->Link_ID) {
      			imap_close($this->Link_ID,$options);
      			$this->Link_ID = 0;
    		}
    	} 
    	
	}
?>
