<?php
	## =======================================================================
	##  mail_functions.php														
	## =======================================================================
	##  Version: 		0.1													
	##  Last change: 	19.12.2002												
	##  by: 			S. Elsner												
	## =======================================================================
	##  19.12.2002:  
	##    * provides all required mail functions
	##
	##	TO-DO:
	##	  * provide security functions
	## =======================================================================  
	
	## =======================================================================        
	##  getRecipients    
	## =======================================================================        
	##  parses the Recipient string and get's the email-adresses for
	##  the users
	##  needs to get the target mail and the template passed
	## ======================================================================= 
	function getRecipients($recipients,$group=null) {
		global $Auth;
	
		## multiclient
		$client_id = $Auth->auth["client_id"];
	
	
		## get the connector
		$connection = new DB_Sql();
		
		## parse the string
		$recipients_array = explode(",",$recipients);
	
		## generate the query
		$query="";	
		foreach($recipients_array as $item) {
			if($query != "") {
				$query .= " OR ";
			} 
			$query .= "user_name ='$item'";
		}
	
		
		if(isset($group)) {
			$addition = 'AND group_id='.$group;
		} else {
			$addition = '';
		}
	
		$select_query   = "SELECT user_name,email FROM ".USERS." WHERE (".$query.") AND client_id='$client_id' ".$addition;
		$result_pointer = $connection->query($select_query);	

		## loop through the results and prepare the result array
		$results = array();
		$i=0;
		
		while($connection->next_record()) {
			$results[$i]["user_name"] = $connection->Record["user_name"];
			$results[$i]["email"] = $connection->Record["email"];
			$i++;
		}

		## okay we are done return the result
		return $results;
	}

	## =======================================================================        
	##  getRecipientsIDs    
	## =======================================================================        
	##  we get a list of ids (comma seperated) and need to find out
	##  the names and the email addresses for this user
	## ======================================================================= 
	function getRecipientsIDs($recipients,$group=null) {
		global $Auth;
	
		## multiclient
		$client_id = $Auth->auth["client_id"];
	
	
		## get the connector
		$connection = new DB_Sql();
		
		## parse the string
		$recipients_array = explode(",",$recipients);
	
		## generate the query
		$query="";	
		foreach($recipients_array as $item) {
			if($query != "") {
				$query .= " OR ";
			} 
			$query .= "user_id ='".intval($item)."'";
		}
	
		
		if(isset($group)) {
			$addition = 'AND group_id='.$group;
		} else {
			$addition = '';
		}
	
		$select_query   = "SELECT user_name,email FROM ".USERS." WHERE (".$query.") AND client_id='$client_id' ".$addition;
		$result_pointer = $connection->query($select_query);	

		## loop through the results and prepare the result array
		$results = array();
		$i=0;
		
		while($connection->next_record()) {
			$results[$i]["user_name"] = $connection->Record["user_name"];
			$results[$i]["email"] = $connection->Record["email"];
			$i++;
		}

		## okay we are done return the result
		return $results;
	}
	

	
	## =======================================================================        
	##  validateEmailAdress   
	## =======================================================================        
	##  validates the email-adress supplied.
	##  it can use two methods: format=0, checkdomain=1
	##  pass it an email-adress 
	## ======================================================================= 
	function validateEmailAdress($email,$method=0) {
		$is_valid_email = false;

		## first we do a format check
		if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[_a-z0-9-]+(\.[_a-z0-9-]+)*(\.[a-z]{2,3})$",$email)) {
			$is_valid_email = true;
		}
		
		##if($method > 0) {
			## then we should connect to the server
		##	list($username,$domain) = split('@',$email);
			
		##	$hosts = _getmxrecord($domain);
		##	if(count($hosts) > 0) {
		##		$is_valid_email = true;
		##	} else {
		##		$is_valid_email = false;
		##	}
		##}
		## okay we are done return the result
		return $is_valid_email;
	}


	## =======================================================================        
	##  _getmxrecord   
	## =======================================================================        
	##  since we have to be plattform independant, we can't use the built in 
	##  getmxrr. Instead call this function, it'll handle to plattform 
	##  differences
	##  pass it an hostname 
	## ======================================================================= 
	function _getmxrecord($hostname) {
		$mxhosts = array();
				
		## check which Plattform
		if(1) {
			exec('nslookup -type=mx '.$hostname, $result_arr);
			foreach($result_arr as $line) {
				if (preg_match("/.*mail exchanger = (.*)/", $line, $matches)) 
				$mxhosts[] = $matches[1];
			}
		} else {
 			@getmxrr($hostname,$mxhosts);
 		}
 
		return($mxhosts);
 }


	## =======================================================================        
	##  generateMail    
	## =======================================================================        
	##  generates the mail message using the specfied template, subject
	##  and recipient
	## ======================================================================= 
	function generateBody($template_file="") {		
		if($template_file!="") {
			## open the specified template
			$template = new Template(HTML_DIR);
			$template->set_templatefile(array("body" => $template_file.".tpl")); 
	
			$template->set_vars($_POST);
				
			## this will be the boody of the message
			$output = $template->fill_block("body");			
			return $template->finish($output);
		}
		return -1;
	}
	

	## =======================================================================        
	##  generateMail    
	## =======================================================================        
	##  generates the mail message using the specfied template, subject
	##  and recipient
	## ======================================================================= 
	function generateSubject($template_file="") {		
		if($template_file!="") {
			## open the specified template
			$template = new Template(HTML_DIR);
			$template->set_templatefile(array("subject" => $template_file.".tpl")); 
	
			$template->set_vars($_POST);
				
			## this will be the boody of the message
			$output = $template->fill_block("subject");			
			return $template->finish($output);
		}
		return -1;
	}	

	## =======================================================================        
	##  sendMail    
	## =======================================================================        
	##  this function actually sends the mail using all required vars
	##  From, Subject, Recipient, Body
	## ======================================================================= 	
	function sendMail($from,$subject,$recipient,$body,$attachment=null) {
		## get the mailer
		$mail = new phpmailer();	
		
		## from whom is this message
		$mail->From     = $from["address"];
		$mail->FromName = $from["name"];
		
		## the subject of the message
		$mail->Subject  = $subject;
		
		## general settings
		$mail->Host     = MAIL_HOST;
		$mail->Mailer   = MAIL_METHOD;
    	
    	## the body of the message	
    	$mail->Body    = $body;
    	
    	
    	if(isset($attachment)) {
    		$mail->AddAttachment($attachment['file'],$attachment['name']);
    	}
    	
    	## and finally the recipients
		foreach($recipient as $address) {
			$mail->AddAddress($address["email"],$address["user_name"]);
		}

		## finally send the message
		$mail->Send();
    	$mail->ClearAddresses();
	
	}	
		
?>
