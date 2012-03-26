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
##  plugins        
## =======================================================================        
##  provides the functionalities to implement hooks into the system.
##
## ======================================================================= 

## =======================================================================        
##  plugins_addAction     
## =======================================================================        
##  call this to register a function with a certain event
##
## ======================================================================= 
function plugins_addAction($event, $function_to_add) {
	global $pluginsActions;

	## add the function to our actions stack
	$pluginsActions[$event][] = $function_to_add;
}

## =======================================================================        
##  plugins_triggerEvent     
## =======================================================================        
##  call this to trigger an event and all the associated actions
##
## ======================================================================= 
function plugins_triggerEvent($event, $arguments = array()) {
	global $pluginsActions;
	
	## check if we have any actions
	if(isset($pluginsActions[$event])) {
		foreach($pluginsActions[$event] as $current_action) {
			$current_action($arguments);
		}
	}
}

?>
