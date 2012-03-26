<?PHP
## =======================================================================        
##  show_user_input        
## =======================================================================        
##  shows the appropriate Input Form for this container 
##
##  TODO:  
## =======================================================================
function show_user_input($user_id,$group_id=-1) {
	global $gSession,$Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## this function displays the input form
	## if we got a template_id, we will get all
	## required info for this template- otherwise we will
	## display the empty form

	## the next step will be to display all nescessary forms
	$inputFile = "user_input.tpl";
	$input_template = new Template('interface/');
	$input_template->set_templatefile(array("head" => $inputFile,"intro" => $inputFile,"body" => $inputFile,"foot" => $inputFile));

	$input_template->set_var('language_userhead',LANG_UserEnterData);
	$input_template->set_var('language_userbody',LANG_UserEnterDataDescription);
	$input_template->set_var('language_userinputname',LANG_UserName);
	$input_template->set_var('language_userinputnamedesc',LANG_UserNameDescription);

	$input_template->set_var('language_userinputlastname',LANG_UserLastName);
	$input_template->set_var('language_userinputfirstname',LANG_UserFirstName);	
	$input_template->set_var('language_userinputrealnamedesc',LANG_UserRealNameDesc);	
	
	$input_template->set_var('language_userinputmail',LANG_UserMail);	
	$input_template->set_var('language_userinputmaildesc',LANG_UserMailDescription);	
	$input_template->set_var('language_password',LANG_UserPassword);	
	$input_template->set_var('language_userinputpasswortdesc',LANG_UserPasswordDescription);
	$input_template->set_var('language_accessright',LANG_UserAcessRights);
	$input_template->set_var('language_accessrighteditor',LANG_UserAcessEditor);
	$input_template->set_var('language_accessrighttemplate',LANG_UserAcessTemplate);
	$input_template->set_var('language_accessrightusers',LANG_UserAcessUsers);

	$input_template->set_var('Session',$gSession->id);
	$input_template->set_var('template_id',$template_id);
	$input_template->set_var('page_id',$page_id);
	$input_template->set_var('group_id',$group_id);
	$input_template->set_var('site',SITE);
	
	$input_template->set_var('low_sub',$low_sub);

	$input_template->set_var("SAVEIMG","lang/".$Auth->auth["language"]."_button_save.gif");


	$targetURL = "user.php";
	$targetURL = $gSession->url($targetURL);
	$input_template->set_var('targetURL',$targetURL);
	
	## the next step is to ouput the head
	$input_template->pfill_block("head");
	$input_template->pfill_block("intro");	
	
	## we should get the page content
	if(!$user_id) {
		$input_template->pfill_block("body");
		$input_template->set_var('op',"save_user");
	} else {
		## prepare the db-object
		$db_connection = new DB_Sql();

		## grab the information
		$select_query = "SELECT * FROM ".USERS." WHERE user_id = '".$user_id."' AND client_id='$client_id'";
		$result_pointer = $db_connection->query($select_query);	
		
		$db_connection->next_record();
		## first we get all the data
		$user_id = $db_connection->Record["user_id"];
		$user_name = $db_connection->Record["user_name"];
		$email = $db_connection->Record["email"];
		$user_lastname = $db_connection->Record["lastname"];
		$user_firstname = $db_connection->Record["firstname"];
		$access_level = $db_connection->Record["access_level"];
		
		$input_template->set_var("user_name",$user_name);
		$input_template->set_var("user_email",$email);
		$input_template->set_var("user_lastname",$user_lastname);
		$input_template->set_var("user_firstname",$user_firstname);
		$input_template->set_var("user_id",$user_id);
		
		$input_template->set_var('op',"update_user");
		$input_template->pfill_block("body");
	}	

	## we need to set the ids and stuff
	$input_template->pfill_block("foot");
}

## =======================================================================        
##  save_user        
## =======================================================================        
## stores the information for a certain container element into the db 
##
##  TODO: 
##    -- we need to set the vars properly      
## =======================================================================
function save_user() {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## okay if recieve this command, we will have to store a new
	## template... we will try this in about 10 minutes from now on
	
	## first we get the values
	$user_name               = $_POST['user_name'];
	$user_email              = $_POST['user_email'];
	$user_passwd             = $_POST['user_passwd'];
	$user_firstname          = $_POST['user_lastname'];
	$user_lastname           = $_POST['user_firstname'];
	$group_id             	 = $_POST['group_id'];
				
	if($user_passwd == "") {
			## we should return an allert!
	}
	## I'll get the file stuff later on
	$db_connection = new DB_Sql();

	$lock_query = "LOCK TABLE ".USERS." WRITE";
	$result_pointer = $db_connection->query($lock_query);
	
	$insert_query   = "INSERT INTO ".USERS." (group_id,user_name, email,password,client_id,lastname,firstname) values ('$group_id','$user_name', '$user_email','$user_passwd','$client_id','$user_lastname','$user_firstname')";
	$result_pointer = $db_connection->query($insert_query);
	$user_id    = $db_connection->db_insertid($result_pointer);
		
	$lock_query = "unlock table";
	$result_pointer = $db_connection->query($lock_query);
	
	return $user_id;
}


## =======================================================================        
##  update_user        
## =======================================================================        
## stores the information for a certain container element into the db 
##
##  TODO: 
##    -- we need to set the vars properly      
## =======================================================================
function update_user($user_id) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	
	## okay if recieve this command, we will have to update an template
	
	## first we get the values
	$user_name               = $_POST['user_name'];
	$user_email              = $_POST['user_email'];
	$user_firstname          = $_POST['user_firstname'];
	$user_lastname           = $_POST['user_lastname'];	
	$user_passwd             = $_POST['user_passwd'];
							
	## I'll get the file stuff later on
	$db_connection = new DB_Sql();
	if($user_passwd=="") {
		$update_query = "UPDATE ".USERS." SET user_name = '$user_name',email = '$user_email', lastname='$user_lastname',firstname='$user_firstname' WHERE user_id = '$user_id' AND client_id='$client_id'";
		$result_pointer = $db_connection->query($update_query);
	} else {
		$update_query = "UPDATE ".USERS." SET user_name = '$user_name',email = '$user_email', password = '$user_passwd', lastname='$user_lastname',firstname='$user_firstname' WHERE user_id = '$user_id' AND client_id='$client_id'";
		$result_pointer = $db_connection->query($update_query);
	}		

	return $user_id;
}

## =======================================================================        
##  delete_user        
## =======================================================================        
##  deletes the elements of a certaain container
##
##  TODO: 
##     - check if we really deleet everything- links???
## =======================================================================
function delete_user($user_id) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## first we check if recieved an template_id
	if($user_id) {
		## prepare the db-object
		$db_connection = new DB_Sql(); 
	
		## okay we can delete this user
		$select_query = "DELETE FROM ".USERS." WHERE user_id='$user_id' AND client_id='$client_id'";
		$result_pointer = $db_connection->query($select_query);	
	} else {
		return -1;
	}
}
	
?>
