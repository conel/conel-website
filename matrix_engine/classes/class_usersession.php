<?php
## =======================================================================
##  class_usersession.php														
## =======================================================================
##  Version: 		0.01													
##  Last change: 	03.08.2007												
##  by: 			S. Elsner											
## =======================================================================
##  Description:															
##	* is a wrapper around the normal session functions in php
##	* also manages timeout of session etc.
##	  * is used by the session datatype to track front end users
##	  to the website
##
## =======================================================================

## The session was started with the current request
define("UserSession_STARTED", 1);

## No new session was started with the current request
define("UserSession_CONTINUED", 2);


class UserSession {

	## =======================================================================
	##  start
	## =======================================================================
	##
	##	Creates or resumes a session based on the optional name passed
	## =======================================================================
	function start($name = 'workmatrix_user', $id = null) {
		## first we set the pagename
		UserSession::name($name);

		## check if we need to capture a certain session by id
		if ($id) {
			UserSession::id($id);
		} elseif (is_null(UserSession::detectID())) {
			UserSession::id($id ? $id : substr(md5(uniqid('').getmypid()),0,10));
		}
		@session_start();
		if (!isset($_SESSION['__UserSession_Info'])) {
			$_SESSION['__UserSession_Info'] = UserSession_STARTED;
		} else {
			$_SESSION['__UserSession_Info'] = UserSession_CONTINUED;
		}
	}

	## =======================================================================
	##  pause
	## =======================================================================
	##
	##	write session data. In order to prevent locking until a script
	##  has been completly executed you can call this function to stroe the 
	##  data and unlcok the session. This can be called whenever you stop
	##  to change the session variables in your script
	##
	## =======================================================================
	function pause() {
		session_write_close();
	}

	## =======================================================================
	##  destroy
	## =======================================================================
	##
	##  resets the $_SESSION variable and all data associated. 
	##  does not unset the session cookie- a new session will be started
	##  after this method was executed
	##
	## =======================================================================
	function destroy() {
		session_unset();
		session_destroy();
	}

	## =======================================================================
	##  regenerateId
	## =======================================================================
	##
	##  Calls session_regenerate_id() if available
	## =======================================================================
	function regenerateId($deleteOldSessionData = false) {
		## emulate session_regenerate_id()
		do {
			$newId = substr(md5(uniqid('').getmypid()),0,10);
		} while ($newId === session_id());

		if ($deleteOldSessionData) {
			session_unset();
		}

		session_id($newId);

		return true;
	}

	## =======================================================================
	##  clear
	## =======================================================================
	##
	##  Free all session variables
	## =======================================================================
	function clear() {
		$info = $_SESSION['__UserSession_Info'];
		session_unset();
		$_SESSION['__UserSession_Info'] = $info;
	}

	## =======================================================================
	##  detectID
	## =======================================================================
	##
	##  Tries to find any session id in $_GET, $_POST or $_COOKIE
	## =======================================================================
	function detectID() {
		if (UserSession::useCookies()) {
			if (isset($_COOKIE[UserSession::name()])) {
				return $_COOKIE[UserSession::name()];
			}
		} else {
			if (isset($_GET[UserSession::name()])) {
				return $_GET[UserSession::name()];
			}
			if (isset($_POST[UserSession::name()])) {
				return $_POST[UserSession::name()];
			}
		}
		return null;
	}

	## =======================================================================
	##  name
	## =======================================================================
	##
	##  Sets new name of a session
	## =======================================================================
	function name($name = null) {
		return isset($name) ? session_name($name) : session_name();
	}


	## =======================================================================
	##  id
	## =======================================================================
	##
	##  Sets new ID of a session
	## =======================================================================
	function id($id = null) {
		return isset($id) ? session_id($id) : session_id();
	}

	## =======================================================================
	##  setExpire
	## =======================================================================
	##
	##  Sets the expire time
	## =======================================================================
	function setExpire($time, $add = false) {
		if ($add) {
			$_SESSION['__UserSession_Expire_TS'] = time() + $time;

			## update session.gc_maxlifetime
			$currentGcMaxLifetime = UserSession::setGcMaxLifetime(null);
			UserSession::setGcMaxLifetime($currentGcMaxLifetime + $time);

		} elseif (!isset($_SESSION['__UserSession_Expire_TS'])) {
			$_SESSION['__UserSession_Expire_TS'] = $time;
		}
	}

	## =======================================================================
	##  setIdle
	## =======================================================================
	##
	##  Sets the time-out period allowed between requests before the 
	##  session-state provider terminates the session.
	## =======================================================================
	function setIdle($time, $add = false) {
		## substract time again because it doesn't make any sense to provide
		## the idle time as a timestamp
		$_SESSION['__UserSession_Idle'] = $time - time();
	}

	## =======================================================================
	##  sessionValidThru
	## =======================================================================
	##
	##  Returns the time up to the session is valid
	## =======================================================================
	function sessionValidThru() {
		if (!isset($_SESSION['__UserSession_Idle_TS']) || !isset($_SESSION['__UserSession_Idle'])) {
			return 0;
		} else {
			return $_SESSION['__UserSession_Idle_TS'] + $_SESSION['__UserSession_Idle'];
		}
	}

	## =======================================================================
	##  isExpired
	## =======================================================================
	##
	##  Check if session is expired
	## =======================================================================
	function isExpired() {
		if (isset($_SESSION['__UserSession_Expire_TS']) && $_SESSION['__UserSession_Expire_TS'] < time()) {
			return true;
		} else {
			return false;
		}
	}

	## =======================================================================
	##  isIdle
	## =======================================================================
	##
	##  Check if session is idle
	## =======================================================================
	function isIdle() {
		if (isset($_SESSION['__UserSession_Idle_TS']) && (($_SESSION['__UserSession_Idle_TS'] + $_SESSION['__UserSession_Idle']) < time())) {
			return true;
		} else {
			return false;
		}
	}

	## =======================================================================
	##  updateIdle
	## =======================================================================
	##
	##  Updates the idletime
	## =======================================================================
	function updateIdle() {
		$_SESSION['__UserSession_Idle_TS'] = time();
	}

	## =======================================================================
	##  useCookies
	## =======================================================================
	##
	##  If optional parameter is specified it returns whether the module will 
	##  use cookies to store the session id on the client side
	## =======================================================================
	function useCookies($useCookies = null) {
		$return = ini_get('session.use_cookies') ? true : false;
		if (isset($useCookies)) {
			ini_set('session.use_cookies', $useCookies ? 1 : 0);
		}
		return $return;
	}

	## =======================================================================
	##  isNew
	## =======================================================================
	##
	##  Gets a value indicating whether the session was created with the 
	##  current request
	##
	##  You MUST call this method only after you have started
	##  the session with the UserSession::start() method.
	## =======================================================================
	function isNew() {
		return !isset($_SESSION['__UserSession_Info']) || $_SESSION['__UserSession_Info'] == UserSession_STARTED;
	}

	## =======================================================================
	##  get
	## =======================================================================
	##
	##  Returns session variable
	## =======================================================================
	function get($name, $default = null) {
		if (!isset($_SESSION[$name]) && isset($default)) {
			$_SESSION[$name] = $default;
		}
		$return = (isset($_SESSION[$name])) ? $_SESSION[$name] : null;
		return $return;
	}

	## =======================================================================
	##  set
	## =======================================================================
	##
	##  Sets session variable
	## =======================================================================
	function set($name, $value) {
		$return = (isset($_SESSION[$name])) ? $_SESSION[$name] : null;
		if (null === $value) {
			unset($_SESSION[$name]);
		} else {
			$_SESSION[$name] = $value;
		}
		return $return;
	}

	## =======================================================================
	##  is_set
	## =======================================================================
	##
	##  Checks if a session variable is set
	## =======================================================================
	function is_set($name) {
		return isset($_SESSION[$name]);
	}

	## =======================================================================
	##  getLocal
	## =======================================================================
	##
	##  Returns local variable of a script
	##  Two scripts can have local variables with the same names
	## =======================================================================
	function &getLocal($name, $default = null) {
		$local = md5(UserSession::localName());
		if (!isset($_SESSION[$local]) || !is_array($_SESSION[$local])) {
			$_SESSION[$local] = array();
		}
		if (!isset($_SESSION[$local][$name]) && isset($default)) {
			$_SESSION[$local][$name] = $default;
		}
		return $_SESSION[$local][$name];
	}

	## =======================================================================
	##  setLocal
	## =======================================================================
	##
	##  Sets local variable of a script.
	##  Two scripts can have local variables with the same names.
	## =======================================================================
	function setLocal($name, $value) {
		$local = md5(UserSession::localName());
		if (!isset($_SESSION[$local]) || !is_array($_SESSION[$local])) {
			$_SESSION[$local] = array();
		}
		$return = (isset($_SESSION[$local][$name])) ? $_SESSION[$local][$name] : null;

		if (null === $value) {
			unset($_SESSION[$local][$name]);
		} else {
			$_SESSION[$local][$name] = $value;
		}
		return $return;
	}

	## =======================================================================
	##  localName
	## =======================================================================
	##
	##  Sets new local name
	## =======================================================================
	function localName($name = null) {
		$return = (isset($GLOBALS['__UserSession_Localname'])) ? $GLOBALS['__UserSession_Localname'] : null;

		if (!empty($name)) {
			$GLOBALS['__UserSession_Localname'] = $name;
		}
		return $return;
	}

	## =======================================================================
	##  Initialize
	## =======================================================================
	function _init() {
		## Disable auto-start of a sesion
		ini_set('session.auto_start', 0);

		## Set local name equal to the current script name
		UserSession::localName($_SERVER['PHP_SELF']);
	}

	## =======================================================================
	##  useTransSID
	## =======================================================================
	##
	##  If optional parameter is specified it indicates whether the 
	##  session id will automatically be appended to all links
	## =======================================================================
	function useTransSID($useTransSID = null) {
		$return = ini_get('session.use_trans_sid') ? true : false;
		if (isset($useTransSID)) {
			ini_set('session.use_trans_sid', $useTransSID ? 1 : 0);
		}
		return $return;
	}

	## =======================================================================
	##  setGcMaxLifetime
	## =======================================================================
	##
	##  If optional parameter is specified it determines the number of seconds
	##  after which session data will be seen as 'garbage' and cleaned up
	## =======================================================================
	function setGcMaxLifetime($gcMaxLifetime = null) {
		$return = ini_get('session.gc_maxlifetime');
		if (isset($gcMaxLifetime) && is_int($gcMaxLifetime) && $gcMaxLifetime >= 1) {
			ini_set('session.gc_maxlifetime', $gcMaxLifetime);
		}
		return $return;
	}


	## =======================================================================
	##  setGcProbability
	## =======================================================================
	##
	##  If optional parameter is specified it determines the probability that
	##  the gc (garbage collection) routine is started
	##  and session data is cleaned up
	## =======================================================================
	function setGcProbability($gcProbability = null) {
		$return = ini_get('session.gc_probability');
		if (isset($gcProbability)  &&
			is_int($gcProbability) &&
			$gcProbability >= 1	&&
			$gcProbability <= 100) {
			ini_set('session.gc_probability', $gcProbability);
		}
		return $return;
	}
}

UserSession::_init();
?>