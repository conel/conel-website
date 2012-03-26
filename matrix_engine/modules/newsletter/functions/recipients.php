<?php

## =======================================================================        
##  newsletter_pageDisplayStatusOverview        
## =======================================================================        
##  this function should return the status of this object. 
## 	the status of the page object could be that: a) no content was entered
## 	or that a certain content has beend entered (show when, and the approx size)
##
##  TODO:
##  
## ======================================================================= 
function newsletter_recipientDisplayStatusOverview($id,$status) {
	global $Auth,$gSession;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## prepare the db-object
	$db_connection = new DB_Sql();

	## first get the page id for this newsletter
	$query = "SELECT COUNT(recipient_id) AS recipients FROM ".DB_PREFIX."newsletter_cue WHERE newsletter_id='$id'";
	
	$rp = $db_connection->query($query);
	$db_connection->next_record();
	$recipients = $db_connection->Record["recipients"];
		

	$select_template = new Template('interface/');
	$select_template->set_templatefile(array("page" => "recipients_overview.tpl","nopage" => "recipients_overview.tpl","send" => "recipients_overview.tpl"));

	$select_template->set_var("LANG",$Auth->auth["language"]);
	$select_template->set_var("LANG_MODULE_Newsletter_Recipient",LANG_MODULE_Newsletter_Recipient);
	$select_template->set_var("LANG_MODULE_Newsletter_RecipientDesc",LANG_MODULE_Newsletter_RecipientDesc);

	$select_template->set_var("LANG_MODULE_Newsletter_NrRecipients",LANG_MODULE_Newsletter_NrRecipients);

	if($status == 2) {
		$deleteURL = 'recipients.php?op=delete&id='.$id;
		$deleteURL = $gSession->url($deleteURL);
		$select_template->set_var("deleteURL",$deleteURL);
		
		$select_template->set_var("count",$recipients);
			
		return $select_template->fill_block("send");
	}
		
	if($recipients > 0) {
		## okay we already have a page
		## prepare the action url
		
		$deleteURL = 'recipients.php?op=delete&id='.$id;
		$deleteURL = $gSession->url($deleteURL);
		$select_template->set_var("deleteURL",$deleteURL);
		
		$select_template->set_var("count",$recipients);
			
		return $select_template->fill_block("page");	
	} else {
		## new page needs to be created;
		## prepare the action url
		$actionURL = 'recipients.php?op=create&id='.$id;
		$actionURL = $gSession->url($actionURL);
		$select_template->set_var("actionURL",$actionURL);
		return $select_template->fill_block("nopage");		
	}		
}

## =======================================================================        
##  recipients        
## =======================================================================        
##  allows the user eiteh rto select a pre-defined filter, or upload
##  a file which get's directly imported into the newsletter cue
## ======================================================================= 
function newsletter_recipientsDisplayInputForm($id) {
	global $gSession,$Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## prepare the template
	$select_template = new Template('interface/');
	$select_template->set_templatefile(array("body" => "recipients_selectgroup.tpl"));

	## the use can select any of the pre-defined queries they are all stored
	## in the filter table- complete with sql
	
	## prepare the db-object
	$db_connection = new DB_Sql();

	$group_selector = '<select name="group">';
	$group_selector .= '<option label="Not specified" value="-1">Not specified</option>';
	
	$query = "SELECT * FROM ".DB_PREFIX."clients_filters";
	$rp = $db_connection->query($query);
	while($db_connection->next_record()) {	
		$group_selector .= '<option label="'.$db_connection->Record["name"].'" value="'.$db_connection->Record["id"].'">'.$db_connection->Record["name"].'</option>';
	}

	$group_selector .='</select>';
	
	## prepare the form target
	$actionURL = 'recipients.php?op=store';
	$actionURL = $gSession->url($actionURL);	
	
	## prepare the hidden form vars
	$hidden = '';
	$hidden .= '<input type="hidden" name="id" value="'.$id.'">';
	$hidden .= '<input type="hidden" name="op" value="store">';
	
	$select_template->set_var("LANG",$Auth->auth["language"]);
	$select_template->set_var("language_selectlayouthead",LANG_MODULE_Newsletter_Recipient);
	$select_template->set_var("language_selectlayoutbody",LANG_MODULE_Newsletter_RecipientDesc);
	
	## set the vars of this template
	$select_template->set_var("actionURL",$actionURL);
	$select_template->set_var("GROUPS",$group_selector);
	$select_template->set_var("hiddenfields",$hidden);
	
	## and output the whole template
	print $select_template->fill_block("body");
}

## =======================================================================        
##  newsletter_recipientsCreateCueByFilter        
## =======================================================================        
##  stores the selected recipients into the newsletters cue- each method
##  for assigning a recipients group has it's own store functin
##
##  TODO:
##       - make all pages by default inactive
## =======================================================================        
function newsletter_recipientsCreateCueByFilter() {
	global $Auth;

	## we expect to get a client filter within the group attribute
	$newsletter_id 	= intval($_POST['id']);
	$group 	= intval($_POST['group']);
	
	## okay now we need to get the users and store their user_id and email
	## address into the cue
	$db_connectionStore = new DB_Sql();	
	$db = new DB_Sql();
	
	$query = "SELECT query FROM ".DB_PREFIX."clients_filters WHERE id='$group'";
	$result = $db_connectionStore->query($query);
	

	if($db_connectionStore->next_record()) {
		$query = $db_connectionStore->Record["query"];			

		## we can execute the filter query now and will recieve a list of clients
		$result = $db_connectionStore->query($query);	

		$current_id = 0;
		while($db_connectionStore->next_record()) {
			$current_id = $db_connectionStore->Record['id'];	
			$email = $db_connectionStore->Record['email']; 
			
			## in order to avoid duplicate entries, we will first check if the user/newsletter combination already exists
			$select_query = 'SELECT recipient_id FROM '.DB_PREFIX."newsletter_cue WHERE recipient_id='$current_id' AND newsletter_id='$newsletter_id'";
			$db->query($select_query,true);
			
			if($db->num_rows() == 0) {		
				$insert_query = 'INSERT INTO '.DB_PREFIX."newsletter_cue (recipient_id,newsletter_id,status,email) VALUES('$current_id', '$newsletter_id','0','')";
				$db->query($insert_query);

			}

		}	

	}
}


## =======================================================================        
##  newsletter_recipientsCreateCueByFile        
## =======================================================================        
##  expects a file. Uploads the file, moves it to the import folder
##	and imports the csv-file line by line
##
##  TODO:
##       - make all pages by default inactive
## =======================================================================        
function newsletter_recipientsCreateCueByFile() {
	global $Auth;

	## we expect to get a client filter within the group attribute
	$newsletter_id 	= intval($_POST['id']);

	$userfile	= $_FILES['import']['tmp_name'];
	$file_name	= $_FILES['import']['name'];
	$file_size	= $_FILES['import']['size'];
	$file_type	= $_FILES['import']['type'];	
	
	$db = new DB_Sql();

	## okay we first create an upload object
	$f = new file_object();  
	if ($userfile != "none" && $userfile!='') {              
		##then we upload the file
		$filename = $f->upload($userfile, 'import.csv',$file_size,$file_type, ENGINE."modules/newsletter/import/",1);
		
		## check if the file was successfully uploaded
		if($filename != -1) {
			## success- let's import the file
			$handle = fopen(ENGINE."modules/newsletter/import/".$filename,"r"); 
		
			$fields = array();
			## get the fields form the first lione of the file
			$fields = fgetcsv($handle, 24000, ",");

			## okay now process all rows
			while(($current_data = fgetcsv ($handle, 24000, ",")) != false && $linecount <= $endline) {		
				$num = count($current_data);
				for ($c=0; $c < $num; $c++) {		
						$data[$fields[$c]] = addslashes($current_data[$c]);
				}
				
				if(isset($data['email']) && !empty($data['email']) && $data['email']!= '0') {
					## in order to avoid duplicate entries, we will first check if the user/newsletter combination already exists
					$select_query = 'SELECT recipient_id FROM '.DB_PREFIX."newsletter_cue WHERE email='".$data['email']."' AND newsletter_id='$newsletter_id'";
					$db->query($select_query,true);
					
					if($db->num_rows() == 0) {		
						$insert_query = 'INSERT INTO '.DB_PREFIX."newsletter_cue (newsletter_id,status,email) VALUES('$newsletter_id','0','".$data['email']."')";
						$db->query($insert_query,true);
						
						## now store the other fields for this email
						$query = 'INSERT INTO '.DB_PREFIX."newsletter_csv_details (email,field1,field2,field3,field4,field5,newsletter_id) VALUES ('".$data['email']."','".$data['field1']."','".$data['field2']."','".$data['field3']."','".$data['field4']."','".$data['field5']."','$newsletter_id')";
						$db->query($query,true);
					}
				}
			}			
			
			fclose($handle);			
		} else {
			## there was an error, we need to display an error page
		}
	}	
}

## =======================================================================        
##  newsletter_recipientsDeleteCue       
## =======================================================================        
##  deletes all cued recipients. when the recipients are delete we reset 
##  the status of the newsletter
##
## ======================================================================= 
function newsletter_recipientsDeleteCue($id) {
	global $Auth,$gSession;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## prepare the db-object
	$db_connection = new DB_Sql();

	## first get the page id for this newsletter
	$query = 'DELETE FROM '.DB_PREFIX."newsletter_cue WHERE newsletter_id='$id'";
	$rp = $db_connection->query($query);	

	$insert_query = "UPDATE ".DB_PREFIX."newsletter SET status='1', send = now() WHERE id='$id'";
	$result_pointer = $db_connection->query($insert_query);	
	
	$query = 'DELETE FROM '.DB_PREFIX."newsletter_csv_details WHERE newsletter_id='$id'";
	$rp = $db_connection->query($query);		
}

?>