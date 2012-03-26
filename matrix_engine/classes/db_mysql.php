<?php
  ## =======================================================================
  ##  db_mysql.php														
  ## =======================================================================
  ##  Version: 		0.02													
  ##  Last change: 	18.01.2001												
  ##  by: 			S. Elsner											
  ## =======================================================================
  ##
  ##  18.01.2001
  ##    * removed the persistent connection-> replace it within 
  ##      a non persistent connection- hope that solves the nt bug
  ## =======================================================================
  ##
  ##  Changes:
  ##    * removed pconnect- updates to the database didn't work
  ## =======================================================================
  class DB_Sql {
  
  var $Host     = DB_HOST;
  var $Database = DB_DATABASE;
  var $User     = DB_USER;
  var $Password = DB_PASSWORD;

  /* public: configuration parameters */
  var $Auto_Free     = 1;     ## Set to 1 for automatic mysql_free_result()
  var $Debug         = 0;     ## Set to 1 for debugging messages.

  /* public: result array and current row number */
  var $Record   = array();
  var $Row;

  /* public: current error number and error text */
  var $Errno    = 0;
  var $Error    = "";

  /* public: this is an api revision, not a CVS revision. */
  var $type     = "mysql";
  var $revision = "1.4";

  /* private: link and query handles */
  var $Link_ID  = 0;
  var $Query_ID = 0;

  /* public: constructor */
  function DB_Sql($host=null,$database=null,$user=null,$password=null) {
      $this->Host = isset($host) ? $host : $this->Host;
      $this->Database = isset($database) ? $database : $this->Database;
      $this->User = isset($user) ? $user : $this->User;
      $this->Password = isset($password) ? $password : $this->Password;
 }
 
  /* public: some trivial reporting */
  function link_id() {
    return $this->Link_ID;
  }

  function query_id() {
    return $this->Query_ID;
  }

  /* public: connection management */
  function connect() {
    if ( 0 == $this->Link_ID ) {
    
      $this->Link_ID=@mysql_connect($this->Host, $this->User, $this->Password);
      if (!$this->Link_ID) {
        $this->halt("pconnect($this->Host, $this->User, \$Password) failed.");
        return 0;
      }
      
      if (!mysql_select_db($this->Database,$this->Link_ID)) {
       $this->halt("cannot use database ".$this->Database);
       return 0;
      }
    }
    
    return $this->Link_ID;
  }

  /* public: discard the query result */
  function free() {
      @mysql_free_result($this->Query_ID);
      $this->Query_ID = 0;
  }

  /* public: perform a query */
  function query($Query_String,$debug=false) {

    /* No empty queries, please, since PHP4 chokes on them. */
    if ($Query_String == ""){
      /* The empty query string is passed on from the constructor,
       * when calling the class without a query, e.g. in situations
       * like these: '$db = new DB_Sql_Subclass;'
       */
      return 0;
	}
	##echo $Query_String."\n\n";
    if (!$this->connect()) {
      return 0; /* we already complained in connect() about that. */
    };

    # New query, discard previous result.
    if ($this->Query_ID) {
      $this->free();
    }

	## try to run an explain on the submitted query
	/*
	if(DEVELOPMENT) {
		$explain_id = @mysql_query('EXPLAIN '.$Query_String,$this->Link_ID);
		$results = @mysql_fetch_array($explain_id,MYSQL_ASSOC);
		
		if(isset($results['Extra']) && !empty($results['Extra'])) {
			echo $Query_String."\n";
			var_dump($results);
		}
	}
	*/
    $this->Query_ID = @mysql_query($Query_String,$this->Link_ID);
    $this->Row   = 0;
    ##$this->Errno = mysql_errno();
   	##$this->Error = mysql_error();
  
    if (!$this->Query_ID && $debug) {
     	$this->halt("Invalid SQL: ".$Query_String);
    }

    # Will return nada if it fails. That's fine.
    return $this->Query_ID;
  }

  /* public: table locking */
  function lock($table, $mode="write") {
    $this->connect();
    
    $res = @mysql_query("lock tables $table $mode", $this->Link_ID);
    if (!$res)
      $this->halt("lock($table, $mode) failed.");
    return $res;
  }
  
  function unlock() {
    $this->connect();

    $res = @mysql_query("unlock tables");
    if (!$res)
      $this->halt("unlock() failed.");
    return $res;
  }


  /* public: walk result set */
  function next_record($method=MYSQL_BOTH) {
    if (!$this->Query_ID) {
      $this->halt("next_record called with no query pending.");
      return 0;
    }
	
    $this->Record = @mysql_fetch_array($this->Query_ID,$method);
    $this->Row   += 1;
    $this->Errno  = mysql_errno();
    $this->Error  = mysql_error();

    $stat = is_array($this->Record);
    if (!$stat && $this->Auto_Free) {
      $this->free();
    }
    return $stat;
  }

  /* public: position in result set */
  function seek($pos = 0) {
    $status = @mysql_data_seek($this->Query_ID, $pos);
    if ($status)
      $this->Row = $pos;
    else {
      $this->halt("seek($pos) failed: result has ".$this->num_rows()." rows");

      /* half assed attempt to save the day, 
       * but do not consider this documented or even
       * desireable behaviour.
       */
      @mysql_data_seek($this->Query_ID, $this->num_rows());
      $this->Row = $this->num_rows;
      return 0;
    }

    return 1;
  }

  function num_rows() {
    return @mysql_num_rows($this->Query_ID);
  }
  
   function num_rows_affected() {
    return @mysql_affected_rows();
  }
 

  function num_fields() {
    return @mysql_num_fields($this->Query_ID);
  }
  function db_insertid($qhandle) {
	return @mysql_insert_id();
}

  /* private: error handling */
  function halt($msg) {
    $this->Error = @mysql_error($this->Link_ID);
    $this->Errno = @mysql_errno($this->Link_ID);
    echo $this->Error;
    echo $this->Errno;
    echo $msg;
    ## this message should at some point be fixed... for now we will use a simple template
	##echo "<html><head><title>workmatrix</title>";
	##echo '<style type="text/css"> .headline { font-family: Verdana,Arial,sans-serif; font-size: 11px; color: #728AB4; }</style>';
	##echo '</head><body bgcolor="#000000"><span class="headline"><b>Service/Support</b><br><br>Die Anwendung wird zur Zeit aktualisiert.<br> Bitte haben Sie noch etwas Geduld.</span></body></html>';
	   
    
    ##printf("</td></tr></table><b>Database error:</b> %s<br>\n", $msg);
    ##printf("<b>MySQL Error</b>: %s (%s)<br>\n",$this->Errno,$this->Error);
      die("Session halted.");
  }
}
?>
