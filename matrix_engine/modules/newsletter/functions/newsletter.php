<?php


## =======================================================================        
##  newsletter_deleteNewsletter        
## =======================================================================        
##  deletes the newsletter supplied to us- later on needs to delete all 
##  subobjects as well
##
##  TODO:
##  
## ======================================================================= 
function newsletter_copyNewsletter($id) {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## prepare the db-object
	$db_connection = new DB_Sql();

	## first get the page id for this newsletter
	$query = "SELECT page_id,name FROM ".DB_PREFIX."newsletter WHERE id='$id'";
	$rp = $db_connection->query($query);
	$db_connection->next_record();
	
	$page_id = $db_connection->Record["page_id"];
	$name = $db_connection->Record["name"];
	
	
	## first weneed to create a new newsletter
	$new_id = newsletter_createNewsletter($name);
	
	## then we need to create a copy of the page that is associated with the old newlsetter
	## and assign it to the new one
	$new_page = page_copyPage($page_id,'newsletter');
	
	## now connect the page to the newsletter
	newsletter_pageConnect2Newsletter($new_id,$new_page);

	return $new_id;
}


## =======================================================================        
##  newsletter_deleteNewsletter        
## =======================================================================        
##  deletes the newsletter supplied to us- later on needs to delete all 
##  subobjects as well
##
##  TODO:
##  
## ======================================================================= 
function newsletter_deleteNewsletter($id) {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## prepare the db-object
	$db_connection = new DB_Sql();

	## first get the page id for this newsletter
	$query = "SELECT page_id FROM ".DB_PREFIX."newsletter WHERE id='$id'";
	$rp = $db_connection->query($query);
	$db_connection->next_record();
	
	$page_id = $db_connection->Record["page_id"];
	
	## now delete the page
	$query = "DELETE FROM ".USER_PAGES." WHERE page_id='".$page_id."'";
	$rp = $db_connection->query($query);		
	
	## now delete the newsletter
	$query = "DELETE FROM ".DB_PREFIX."newsletter WHERE id='".$id."'";
	$rp = $db_connection->query($query);

	
	## now delete the newsletter
	$query = "DELETE FROM ".DB_PREFIX."newsletter_cue WHERE newsletter_id='".$id."'";
	$rp = $db_connection->query($query);
}



## =======================================================================        
## newsletter_displayInputName     
## =======================================================================        
## displays a dialog for naming a newsletter
## input the newsletter id, and if available the old text+ the actionURL
##    
## =======================================================================
function newsletter_displayInputName($id, $text,$actionURL) {
	global $gSession,$Auth;
	## prepare the template file
	$select_template = new Template(INTERFACE_DIR);
	$select_template->set_templatefile(array("header" => "namepage.tpl","body" => "namepage.tpl","footer" => "namepage.tpl"));

	$select_template->set_var("saveIMG","lang/".$Auth->auth["language"]."_button_save.gif");
	$select_template->set_var('language_pagename',LANG_MODULE_Newsletter_Name);
	$select_template->set_var('language_pagenamedesc',LANG_MODULE_Newsletter_NameDesc);

	$select_template->set_var('menu_text',$text);
	$select_template->set_var('actionURL',$actionURL);
	## okay now we start the ouput
	$select_template->pfill_block("header");	
	$select_template->pfill_block("body");
	

	$output  = '<input type="hidden" name="id" value="'.$id.'">';
	$output .= '<input type="hidden" name="op" value="updatename">'; 
		
	$select_template->set_var('hiddenfields',$output);
	
	$select_template->pfill_block("footer");
}

## =======================================================================        
## newsletter_getAllNewsletters   
## =======================================================================        
## returns all newsletters
##    
## =======================================================================
function newsletter_getAllNewsletters() {	
	## save the name
	$db_connection = new DB_Sql();

	$select_query = "SELECT id,name,status FROM ".DB_PREFIX."newsletter";
	$result_pointer = $db_connection->query($select_query);	

	$newsletters = array();
	$counter = 0;
	while($db_connection->next_record()) {
		## first we get all the data		
 		$newsletters[$counter]['page_id'] = $db_connection->Record["id"];	
 		$newsletters[$counter]['name'] = $db_connection->Record["name"]; 
 		$newsletters[$counter]['status'] = $db_connection->Record["status"]; 
 		$counter++;
	}

	## return the templateInfo
	return $newsletters;
}

## =======================================================================        
## newsletter_getNewsletter   
## =======================================================================        
## updates the newsletter's name
##    
## =======================================================================
function newsletter_getNewsletter($id) {	
	## save the name
	$db_connection = new DB_Sql();

	$insert_query = "SELECT id,status,name,UNIX_TIMESTAMP(created) as created,UNIX_TIMESTAMP(send) as send, page_id, recipients,subject,replyto FROM ".DB_PREFIX."newsletter WHERE id='$id'";
	$result_pointer = $db_connection->query($insert_query);
	$db_connection->next_record();

	$newsletterInfo = array();
	$newsletterInfo['id'] = $db_connection->Record["id"];
	$newsletterInfo['status'] = $db_connection->Record["status"];
	$newsletterInfo['name'] = $db_connection->Record["name"];
	$newsletterInfo['created'] = $db_connection->Record["created"];
	$newsletterInfo['send'] = $db_connection->Record["send"];
	$newsletterInfo['page_id'] = $db_connection->Record["page_id"];
	$newsletterInfo['recipients'] = $db_connection->Record["recipients"];
	$newsletterInfo['subject'] = $db_connection->Record["subject"];
	$newsletterInfo['replyto'] = $db_connection->Record["replyto"];

	## return the templateInfo
	return $newsletterInfo;
}


## =======================================================================        
## newsletter_updateName     
## =======================================================================        
## updates the newsletter's name
##    
## =======================================================================
function newsletter_updateName($id, $text) {	
	## before we insert the data into the db
	## we have to make sure we have valid data
	$text = htmlentities($text);	
	
	## save the name
	$db_connectionStore = new DB_Sql();

	$insert_query = "UPDATE ".DB_PREFIX."newsletter SET name = '$text' WHERE id='$id'";
	$result_pointer = $db_connectionStore->query($insert_query);	
}



## =======================================================================        
## newsletter_updateName     
## =======================================================================        
## updates the newsletter's name
##    
## =======================================================================
function newsletter_updateReplyAddress($id, $text) {	
	## before we insert the data into the db
	## we have to make sure we have valid data
	$text = htmlentities($text);	
	
	## save the name
	$db_connectionStore = new DB_Sql();

	$insert_query = "UPDATE ".DB_PREFIX."newsletter SET replyto = '$text' WHERE id='$id'";
	$result_pointer = $db_connectionStore->query($insert_query);	
}


## =======================================================================        
## newsletter_updateName     
## =======================================================================        
## updates the newsletter's name
##    
## =======================================================================
function newsletter_updateSubject($id, $text) {	
	## before we insert the data into the db
	## we have to make sure we have valid data
	$text = htmlentities($text);	
	
	## save the name
	$db_connectionStore = new DB_Sql();

	$insert_query = "UPDATE ".DB_PREFIX."newsletter SET subject = '$text' WHERE id='$id'";

	$result_pointer = $db_connectionStore->query($insert_query);	
}

## =======================================================================        
##  newsletter_createNewsletter        
## =======================================================================        
##  creates a new newsletter- and the associated page    
##
##  TODO:
##       - make all pages by default inactive
## =======================================================================        
function newsletter_createNewsletter($title) {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## prepare the db-object
	$db_connectionStore = new DB_Sql();

		
	$insert_query = "insert into ".DB_PREFIX."newsletter (name,subject,status, created) values ('$title','$title', '1',now())";
	echo $insert_query;
	$result_pointer = $db_connectionStore->query($insert_query);	
	$page_id = $db_connectionStore->db_insertid($result_pointer);
	
	
	return $page_id;
}

## =======================================================================        
##  newsletter_displayOverview       
## =======================================================================        
##  displays the main page for a newsletter- this function needs to
##  call the other subobjects to retrieve the information to be displayed  
##
##  TODO:
##       - make all pages by default inactive
## =======================================================================        
function newsletter_displayOverview($id) {
	global $Auth,$gSession;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## first we need to get the info for this newsletter
	$newsletterInfo = newsletter_getNewsletter($id);
	
	$select_template = new Template('interface/');
	$select_template->set_templatefile(array("body" => "overview.tpl"));

	$select_template->set_var("LANG",$Auth->auth["language"]);
		
	## create the header- name and creation date
	$select_template->set_var("TITLE",$newsletterInfo['name']);
	
	$select_template->set_var("SUBJECT",$newsletterInfo['subject']);
	$select_template->set_var("REPLYTO",$newsletterInfo['replyto']);
	
	$date = utility_prepareDate($newsletterInfo['created'],$format);
	$select_template->set_var("created",LANG_MODULE_Newsletter_Created.' '.$date);
	
	$select_template->set_var("LANG_MODULE_Newsletter_TestMail",LANG_MODULE_Newsletter_TestMail);
	$select_template->set_var("LANG_MODULE_Newsletter_TestMailDesc",LANG_MODULE_Newsletter_TestMailDesc);
	
	
	##if($newsletterInfo['status'] == 2) {
	##	$tabs = '';	
	##	$newsletterURL = $gSession->url('newsletter.php?op=edit&id='.$_GET['id']);
		##$reportsURL = $gSession->url('reports.php?op=overview&id='.$_GET['id']);
		
		
	##	$tabs .= ui_renderSectionTab('Campaign Content',$newsletterURL,0,$highlight,false);
		##$tabs .= ui_renderSectionTab('Reports',$tabsURL.$reportsURL,1,$highlight,false);	
		
	##	$select_template->set_var('TABS',$tabs);
	##}
		
	
	
	## set the status
	$select_template->set_var("newsletterStatusHead",LANG_MODULE_Newsletter_Status);
	if($newsletterInfo['status'] == 2) {
		$date = utility_prepareDate($newsletterInfo['send'],"d.m.Y H:i");
		$select_template->set_var("newsletterStatus",LANG_MODULE_Newsletter_StatusSend.$date);
	} else {
		$select_template->set_var("newsletterStatus",LANG_MODULE_Newsletter_StatusNotSend);
	}
	
	$page = newsletter_pageDisplayStatusOverview($id);
	$recipients = newsletter_recipientDisplayStatusOverview($id);
	
	$select_template->set_var("page",$page);
	$select_template->set_var("recipients",$recipients);
	
	## first we have the test email
	$actionURL = 'newsletter.php?id='.$id;
	$actionURL = $gSession->url($actionURL);		
	$select_template->set_var('actionURL',$actionURL);
	
	## prpeare the menu url- used for refreshing the navigation
	$menuURL = 'menu.php';
	$menuURL = $gSession->url($menuURL);		
	$select_template->set_var('menuURL',$menuURL);	

	$hiddenfields_test = '<input type="hidden" name="op" value="testmail">';
	$hiddenfields_test .= '<input type="hidden" name="id" value="'.$id.'">';
	$select_template->set_var('hiddenfields_test',$hiddenfields_test);
	
	## here the url for the actual mailing
	## first we have the test email
	$sendURL = 'newsletter.php?op=send&id='.$id;
	$sendURL = $gSession->url($sendURL);		
	$select_template->set_var('sendURL',$sendURL);	
	
	
	
	$select_template->pfill_block("body");
}



?>