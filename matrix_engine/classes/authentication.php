<?php
session_start();
## =======================================================================
##  authenticate.php														
## =======================================================================
##  Version: 		0.02													
##  Last change: 	17.11.2000												
##  by: 			S. Elsner											
## =======================================================================
##  Description:															
##    * Authentication Object
##    * handles login, expiration and md5 based login				
##
## =======================================================================  
##  17.02.2001:  
##    * when the user logs in the user db is updated to reflect the time
##      the user logged in- this can be used to track users, and can also
##      be used to determine if a certain user is still online
##  
##  08.12.2000:  
##    * increased the lifetime to 20 minutes  
##  
##  17.11.2000:  
##    * fixed database- it now contains the real passwords_root die  
## =======================================================================  

class Auth {
  var $classname = "Auth";
  var $persistent_slots = array("auth");
  
  var $lifetime = SESSION_LIFETIME;  ## Max allowed idle time before
                                     ## reauthentication is necessary.
                                     ## If set to 0, auth never expires.
  
  var $magic = "jeremy12";           ## Used in uniqid() generation


  var $expiration;
  var $user_id;
  
  var $auth = array();            ## Data array
  var $in;  


	## =======================================================================  
	##  Constructor  
	## =======================================================================    
	function Auth() {
		global $gSession;
	
		if (! $this->in) {
			$gSession->register("Auth");
			$this->in = true;
		}
		$this->auth["user_id"]    = 1;
	}      

	## =======================================================================  
	##  start  
	## =======================================================================  
	##  check if we can login the user- or if the login failed 
	##  
	## =======================================================================  
	function start() {
		global $gSession;
    
		if (!$this->in) {
			$gSession->register("Auth");
			$this->in = true;
		}
    
		if(!$this->is_authenticated()) {
			# check if we received anything from the form
			if(isset($_POST['username']) && isset($_POST['password'])) {
      			## okay the user has submitted the required fields
      			## now check if we can validate the user
     			$db = new DB_Sql();
     	
     			## prepare the username to be passed to the db
     			$username = mysql_real_escape_string($_POST['username']);
     			$query = "SELECT * FROM ".USERS." WHERE user_name='$username'";
     			$rp = $db->query($query); 
		
				## okay check if we can find an entry
				if($db->next_record()) {
					$id = $db->Record['user_id'];
					$password = $db->Record['password'];
					$access_level = $db->Record["access_level"];
					$client_id = $db->Record["client_id"];     	
			
					$exspected_response = md5("$username:$password:".$_POST['challenge']);
			
					## okay we need to check if the users browser sended a response
					$response = isset($_POST['response']) ? $_POST['response'] : '';
					if(($response == '' && $password == $_POST['password']) || ($response == $exspected_response)) {
						## okay the password check validated
						## let's store 
						$this->auth["expiration"] = time() + (60 * $this->lifetime);
						$this->auth["user_id"]    = $id;
						$this->auth["client_id"]  = $client_id;
						$this->auth["logged"]     = 1;
						$this->auth["access_level"]     = intval($access_level);
						$this->auth["language"] = $_POST['language'];
						$gSession->freeze();
					} else {
						## everything failed- we need to re-direct to the login page
						$gSession->unregister("Auth");
						$gSession->freeze();
						include(ENGINE."noaccess.php");
						exit;		
					}
					$_SESSION['wm']['username'] = $username;
				} else {
					include(ENGINE."noaccess.php");
					exit;
				}
			} else {
				## okay the user hasn't entered anything
				$GLOBALS['challenge'] = md5(uniqid($this->magic));
				## store the challenge in the session var
				$gSession->register("challenge");
				$gSession->freeze();
			
				## and finally we actually call the form page
				include(ENGINE."login.php");
				exit;
			}
		}
	}
  function unauth($nobody = false) {
    $this->auth["uid"]   = "";
    $this->auth["perm"]  = "";
    $this->auth["exp"]   = 0;
  }
  

  function logout($nobody = "") {
    global $gSession;
    
    $gSession->unregister("auth");
    unset($this->auth["uname"]);
    $this->unauth($nobody == "" ? $this->nobody : $nobody);
  }

  function is_authenticated() { 
  	if(isset($this->auth["logged"])) {
		if($this->auth["logged"] && time() < $this->auth["expiration"]) {
		  	## update only if it is still active- otherwise the user
		  	## will have to re-login
		  	$this->auth["expiration"] = time() + (60 * $this->lifetime);      
			return true;
		} 
	}
  return false;
 }
 
   function access_right($project_id) { 
		$db_connection = new DB_Sql();

		$user_id = $this->auth["user_id"];
		$select_query = sprintf("SELECT user_id FROM membership WHERE user_id = $user_id AND project_id = $project_id"); 
		$result_pointer = $db_connection->query($select_query);
		if($db_connection->num_rows() == 1) {
			return 1;
		}
		return 0;            
 }
 
    function notify_workmatrix($username) { 
		$mail = new phpmailer();

		$mail->From     = "stefan@workmatrix.de";
		$mail->FromName = "webmatrix";
		$mail->Subject  = "webmatrix: log-in";
		$mail->Host     = MAIL_HOST;
		$mail->Mailer   = MAIL_METHOD;
    		
		$today = date("F j, Y, g:i a");
		$text_body  = "webmatrix\nVersion: ".MATRIX_VERSION."\n";
		$text_body .= "Host: ".$_SERVER["SERVER_NAME"]."\n";
		$text_body .= "Lizenz: ".MATRIX__LIZENZ."\n";
    	$text_body .= "Nutzer: $username hat sich am $today eingeloggt \n\n";

    	$mail->Body    = $text_body;
    	$mail->AddAddress("stefan@workmatrix.de", "Stefan");
		$mail->Send();
    	$mail->ClearAddresses();
 	}
 
 
 function url() {
    return $GLOBALS["gSession"]->url(SITE.'index.php');
  }
}
?>
