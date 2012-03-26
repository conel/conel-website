<?php
## =======================================================================
##  session.php														
## =======================================================================
##  Version: 		0.02													
##  Last change: 	27.10.2000												
##  by: 			S. Elsner											
## =======================================================================
##  Description:															
##    * Session object- stores the data into an odbc database
##    * handles all session relevant tasks				
##
## =======================================================================
##  26.10.2003:
##    * replace PHP_SELF with the new: $_SERVER ['PHP_SELF']
##      we should get rid of the container class!!!
##
## =======================================================================
##  03.12.2000:
##    * added a dirty implementation of a locking mechanism/
##      we should get rid of the container class!!!
##
## =======================================================================

class session_object {
  var $classname = "Session";         ## Needed for object serialization.

  ## Define the parameters of your session by either overwriting
  ## these values or by subclassing session (recommended).

  var $name = "";                     ## Session name
  var $magic = "jeremy";              ## Some string you should change.
  var $lifetime = 0;                  ## 0 = do session cookies, else minutes

  var $cookie_domain = "";            ## If set, the domain for which the
                                      ## session cookie is set.

  var $gc_time  = 1440;               ## Purge all session data older than 1440 minutes.
  var $gc_probability = 20;            ## Garbage collect probability in percent

  var $auto_init = "";                ## Name of the autoinit-File, if any.
  var $secure_auto_init = 1;          ## Set to 0 only, if all pages call
                                      ## page_close() guaranteed.

  var $allowcache = "no";             ## "passive", "no", "private", "public"
  var $allowcache_expire = 0;         ## If you allowcache, data expires in this
                                      ## many minutes.
  var $data_storage_class = "session_container";       ## Name of data storage container

  ##
  ## End of parameters.
  ##

  var $id;                            ## Unique Session ID
  var $that;

  var $registered_objects = array();  ## This Array contains the registered things
  var $in = false;                    ## Marker: Did we already include the autoinit file?

  ## =======================================================================
  ##  register
  ## =======================================================================
  ##  Parameters:
  ##    *things   -> comma delimitered string of things to store
  ## =======================================================================
  function register($things) {
        $things = explode(",",$things);
        reset($things);
        while ( list(,$thing) = each($things) ) {
              $thing=trim($thing);
              if ( $thing ) {
                    $this->registered_objects[$thing] = true;
              }
        }
  }

  ## =======================================================================
  ## is_registered($name):
  ##
  ## returns if the var $name is already registered
  ## =======================================================================  
  function is_registered($name) {
    if ($this->registered_objects[$name] == true)
      return true;
    return false;
  }

  ## ======================================================================= 
  ## unregister($things):
  ##
  ## call this function to unregister the things
  ## ======================================================================= 
  function unregister($things) {
    $things = explode(",", $things);
    reset($things);
    while (list(,$thing) = each($things)) {
      $thing = trim($thing);
      if ($thing) {
        unset($this->registered_objects[$thing]);
      }
    }
  }

  ## ======================================================================= 
  ## get_id():
  ##
  ## Propagate the session id according to mode and lifetime.
  ## Will create a new id.
  ## ======================================================================= 
  function get_id() {
    
    $id_okay = true;
 
    if($this->name == "") {
      $this->name = $this->classname;
    }
   
    if("" == ($id = isset($_GET[$this->name]) ? $_GET[$this->name] : "")) {
      $id = isset($_POST[$this->name]) ? $_POST[$this->name] : "";
    }
    
    ## check if the id consists of chars that make up a session
    if(ereg('[^a-z0-9]', $id)){
		$id_okay = false;
	}
                  
    if( "" == $id || !$id_okay) {
      $newid=true;
      // I'll have to work on this class to figure out how this link works
      $id = md5(uniqid($this->magic));
    }
 
    if(isset($_SERVER['QUERY_STRING']))  {
      $_SERVER['QUERY_STRING'] = ereg_replace("(^|&)".quotemeta(urlencode($this->name))."=(.)*(&|$)","\\1", $_SERVER['QUERY_STRING']);
    }
    
    $this->id = $id;
  }

  ## =======================================================================
  ## put_id():
  ## 
  ## Stop using the current session id 
  ## =======================================================================  
  function put_id() {
 
    if($this->name == "") {
      $this->name = $this->classname;
    }
    
    // if we stop using the session id- we should remove the session id from
    // the current QUERYSTRING/ or the HTTP_GET_VARS ????
    die("This has not been coded yet.");
  }

  ## =======================================================================
  ## delete():
  ##
  ## Delete the current session record and put the session id.
  ## =======================================================================
  function delete() {
      $this->that->delete($this->id, $this->name);
      $this->put_id();
  }

  ## =======================================================================
  ## url($url):
  ##
  ## Helper function: returns $url concatenated with the current 
  ## session $id.
  ## =======================================================================  
  function url($url){
    $url=ereg_replace("[&?]+$", "", $url);
 
    $url .= ( strpos($url, "?") != false ?  "&" : "?" ).urlencode($this->name)."=".$this->id;
 
    return $url;
  }


  function self_url() {

    return $this->url($_SERVER['PHP_SELF'].
           ((isset($_SERVER['QUERY_STRING']) && ("" != $_SERVER['QUERY_STRING'])) ? "?".$_SERVER['QUERY_STRING'] : ""));
  }


  ## serialize($prefix,&$str):
  ##
  ## appends a serialized representation of $$prefix
  ## at the end of $str.
  ##
  ## To be able to serialize an object, the object must implement
  ## a variable $classname (containing the name of the class as string)
  ## and a variable $persistent_slots (containing the names of the slots
  ## to be saved as an array of strings).
  ##
  ## You don't need to know...
  function serialize($prefix, &$str) {
    static $t,$l,$k;

    ## Determine the type of $$prefix
    eval("\$t = gettype(\$$prefix);");
    switch ( $t ) {

      case "array":
        ## $$prefix is an array. Enumerate the elements and serialize them.
        eval("reset(\$$prefix); \$l = gettype(list(\$k)=each(\$$prefix));");
        $str .= "\$$prefix = array(); ";
        while ( "array" == $l ) {
          ## Structural recursion
          $this->serialize($prefix."['".ereg_replace("([\\'])", "\\\\1", $k)."']", $str);
          eval("\$l = gettype(list(\$k)=each(\$$prefix));");
        }

      break;
      case "object":
        ## $$prefix is an object. Enumerate the slots and serialize them.
        eval("\$k = \$${prefix}->classname; \$l = reset(\$${prefix}->persistent_slots);");
        $str.="\$$prefix = new $k; ";
        while ( $l ) {
          ## Structural recursion.
          $this->serialize($prefix."->".$l,$str);
          eval("\$l = next(\$${prefix}->persistent_slots);");
        }

      break;
      default:
        ## $$prefix is an atom. Extract it to $l, then generate code.
        eval("\$l = \$$prefix;");
        $str.="\$$prefix = '".ereg_replace("([\\'])", "\\\\1", $l)."'; ";


      break;
    }
  }

  function get_lock() {
      $this->that->get_lock();
  }

  function release_lock() {
      $this->that->release_lock();
  }

  ## freeze():
  ##
  ## freezes all registered things ( scalar variables, arrays, objects ) into
  ## a database table

  function freeze() {
        ## we will need this to lock the table
        $db_connection = new DB_Sql();
    $str="";

    $this->serialize("this->in",$str);
    $this->serialize("this->registered_objects",$str);

    reset($this->registered_objects);
    while ( list($thing) = each($this->registered_objects) ) {
      $thing=trim($thing);
      if ( $thing ) {
        $this->serialize("GLOBALS['".$thing."']",$str);
      }
    }
    
    ## her we start to store the table
    $db_connection->lock(ACTIVE_SESSIONS);
    
    $r = $this->that->store($this->id, $this->name, $str);
    $db_connection->unlock();
    if(!$r) $this->that->halt("Session: freeze() failed.");
  }

  ## thaw:
  ##
  ## Reload frozen variables from the database and microwave them.

  function thaw() {
    //$this->get_lock();

    $vals = $this->that->get_value($this->id, $this->name);
    eval(sprintf(";%s",$vals));
  }

  ##
  ## Garbage collection
  ##
  ## Destroy all session data older than this
  ##
  ##function gc() {
  ##    $this->that->gc($this->gc_time, $this->name);
  ##}
  
  ##
  ## All this is support infrastructure for the start() method
  ##

  function set_tokenname(){
    if($this->name == "") {
      $this->name = $this->classname;
    }
  }

  function release_token(){ 
    // check if we have it as an get parameter   
    if(!isset($_GET[$this->name])) {
       header("Status: 302 Moved Temporarily");
       $this->get_id($sid);
       header("Location: http://".$_SERVER['HTTP_HOST'].$this->self_url());
       exit;
      }  
  }   

  function put_headers() {
    # Allowing a limited amount of caching, as suggested by
    # Padraic Renaghan on phplib@lists.netuse.de.
    #
    # Note that in HTTP/1.1 the Cache-Control headers override the Expires
    # headers and HTTP/1.0 ignores headers it does not recognize (e.g,
    # Cache-Control). Mulitple Cache-Control directives are split into 
    # mulitple headers to better support MSIE 4.x.
    #
    # Added pre- and post-check for MSIE 5.x as suggested by R.C.Winters,
    # see http://msdn.microsoft.com/workshop/author/perf/perftips.asp#Use%20Cache-Control%20Extensions
    # for details
    switch ($this->allowcache) {

      case "passive":
        $mod_gmt = gmdate("D, d M Y H:i:s", getlastmod()) . " GMT";                             
        header("Last-Modified: " . $mod_gmt);
        # possibly ie5 needs the pre-check line. This needs testing.
        header("Cache-Control: post-check=0, pre-check=0");
      break;

      case "public":
        $exp_gmt = gmdate("D, d M Y H:i:s", time() + $this->allowcache_expire * 60) . " GMT";
        $mod_gmt = gmdate("D, d M Y H:i:s", getlastmod()) . " GMT";                             
        header("Expires: " . $exp_gmt);
        header("Last-Modified: " . $mod_gmt);
        header("Cache-Control: public");
        header("Cache-Control: max-age=" . $this->allowcache_expire * 60);
      break;
 
      case "private":
        $mod_gmt = gmdate("D, d M Y H:i:s", getlastmod()) . " GMT";
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . $mod_gmt);
        header("Cache-Control: private");
        header("Cache-Control: max-age=" . $this->allowcache_expire * 60);
        header("Cache-Control: pre-check=" . $this->allowcache_expire * 60);
      break;

      default:
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-cache");
        header("Cache-Control: post-check=0, pre-check=0");
        header("Pragma: no-cache");
      break;
    }
  }

  ##
  ## Garbage collection
  ##
  ## Destroy all session data older than this
  ##
  function gc() {
    srand(time());
    if ((rand()%100) < $this->gc_probability) {
      $this->that->garbage_collection($this->gc_time, $this->name);
    }
  }


  ##
  ## Initialization
  ##

  function start($sid = "") {
    $this->that = new session_container;
    $this->set_tokenname();
    $this->release_token($sid);
    $this->put_headers();
    $this->get_id($sid);
    $this->thaw();
    $this->gc();
  }

}
?>
