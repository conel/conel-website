<?php

  ## =======================================================================
  ##  container.php														
  ## =======================================================================
  ##  Version: 		0.02													
  ##  Last change: 	21.11.2000												
  ##  by: 			S. Elsner											
  ## =======================================================================			
  ##  22.11.2000:
  ##    * changed the way the object termines if a session still
  ##    * exists. Check if it works with odbc
  ##
  ## =======================================================================


class session_container {
  ## Define these parameters by overwriting 
    
  var $database_table = ACTIVE_SESSIONS;

  function garbage_collection($gc_time, $name) {
    $timeout = time();
    $sqldate = date("YmdHis", $timeout - ($gc_time * 60));
    
    # create an db connection, returned in $db_connection
    $db_connection = new DB_Sql();
    
    $delete_query = sprintf("DELETE FROM %s WHERE changed < '%s' AND name = '%s'", $this->database_table,$sqldate,addslashes($name));
    $result_pointer = $db_connection->query($delete_query);
  }

  function store($id, $name, $str) {
 
    # create an db connection, returned in $db_connection
    $db_connection = new DB_Sql();
    
    # in the beginning we assume everything will be okay
    $sucess_flag = true;
    
    # encode the name
    $str = base64_encode($name . ":" . $str);
    $name = addslashes($name);
    $now = date("YmdHis", time());
    
    
    # let's assume the entry exists and needs updating
    $update_query = sprintf("update %s set val='%s', changed='%s' where sid='%s' and name='%s'",$this->database_table,$str,$now,$id,$name);
    
    # send the query
    $result_pointer = $db_connection->query($update_query);

    # then we check the affected rows
    if ($db_connection->num_rows() == 0) {
    
        # this could mean no entry present or no change needed
        # so we check if the entry is present
        $select_query = sprintf("select * from ".ACTIVE_SESSIONS." where val='$str' AND changed='$now' AND sid='$id' AND name='$name'"); 
        # send query 
	    $result_pointer = $db_connection->query($select_query);
	    
        # to find out if we found an array- we try to fetch a row
        if($db_connection->num_rows() == 0) {            
            # if there is no row present- we insert a new row
            $insert_query = sprintf("insert into %s ( sid, name, val, changed ) values ('%s', '%s', '%s', '%s')",$this->database_table,$id,$name,$str,$now);
            # send odbc query 
	         $result_pointer = $db_connection->query($insert_query);
	
	        # if this fails we will get false as a result
	        if(!$result_pointer) {
	           # we have tried everything- but we still failed- so we return false
               $sucess_flag = false;
	        }
	     }   
	   }
	return $sucess_flag;       
  }

  function delete($id, $name) {
    # create an Db connection, returned in $db_connection
    $db_connection = new DB_Sql();  

    $delete_query = sprintf("delete from %s where name = '%s' and sid = '%s'",$this->database_table,addslashes($name),$id);

    $result_pointer = $db_connection->query($delete_query);
  }

  ## =======================================================================
  ##  get_value
  ## =======================================================================
  ##  Parameters:
  ##    *id   -> Session ID
  ##    *name -> Name of the Variable to get
  ## =======================================================================
  ##  To-DO:
  ##    * The actual fetching of the data does some converting
  ##      which I don't fully understand- i think I could remove
  ##      parts of this code
  ## =======================================================================
  function get_value($id, $name) {
    $result_string = "";
    
    # create an db connection, returned in $db_connection
	$db_connection = new DB_Sql();  

    $select_query = sprintf("select val from %s where sid  = '%s' and name = '%s'",$this->database_table,$id,addslashes($name));
    # send query - I think we need to lock the db here
    $result_pointer = $db_connection->query($select_query);
	
	if($db_connection->next_record()) {
	      $result_string = $db_connection->Record["val"];   	   
	      $string2 = base64_decode($result_string);
	   
	   if(ereg("^".$name.":.*", $string2)) {
          $result_string = ereg_replace("^".$name.":", "", $string2);
       } else {
          $string3 = stripslashes($result_string);
          if(ereg("^".$name.":.*", $string3)) {
            $result_string = ereg_replace("^".$name.":", "", $string3);
          } else {
            $string = base64_decode($result_string);
          }
       }
     }  
  return $result_string;
 }         
 
  ## =======================================================================
  ##  error_handler
  ## =======================================================================
  ##  Parameters:
  ##    *msg        -> the message that we output before we die
  ##    *connection -> The database connection that caused the error
  ## =======================================================================
  function error_handler($msg, $connection) {
     die($msg);
  }
}
?>
