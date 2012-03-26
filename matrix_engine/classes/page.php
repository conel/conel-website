<?php
## ======================================================================= 
##   webmatrix                                                     
## ======================================================================= 
##   Copyright (c) 2001, 2002, 2003 Stefan Elsner, Workmatrix        
## ======================================================================= 
##   <info@workmatrix.de> 
##
## ======================================================================= 

## =======================================================================
##  page.php														
## =======================================================================
##  Version: 		0.01													
##  Last change: 	30.10.2000												
##  by: 			S. Elsner											
## =======================================================================
##  Description:															
##    * Page Object, use this to handle sessions and authentications				
##
##  To-Do:
##    * a re-write to include the access control over all elements
##    * this will be projects, depmartments, and objects- where 
##    * rooms/ container are considered objects- before this can happen
##    * I should try to set up the database to include all these items
##
## =======================================================================

## =======================================================================
## page_open
##
## call this at the beginning od a page
## use the $feature to set the parameters
## e.g.   
## page_open(array("session" => "SessionObject", "authenticate" => "AuthObject"));
## =======================================================================
	function page_open($feature) {    
		# enable sess and all dependent features.
		if (isset($feature["session"])) {
			global $gSession;
			$gSession = new $feature["session"];
			$gSession->start();
            
			# the auth feature depends on sess
			if (isset($feature["authenticate"])) {
				global $Auth;
      
				if (!isset($Auth)) {
					$Auth = new $feature["authenticate"];
				}
				$Auth->start();

				# the preferences feature depends on the auth
				if (isset($feature["preferences"])) {
					global $wmUserSettings;
					
					$wmUserSettings = new $feature["preferences"]($Auth->auth["user_id"]);
				}
			}
		}
	}

## =======================================================================
## page_close
##
## basically freezes all session data- call this 
## as soon as possible- to store the vars
## =======================================================================
function page_close() {
      global $gSession;

      if (isset($gSession)) {
            $gSession->freeze();
      }
}
?>
