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
## link_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function link_displayInput($xmldata, $data) {
	## init the vars
	$return = "";

	## we should open our own template
	$template = new Template(ENGINE."datatypes/link/interface/");
	$template->set_templatefile(array("link" => "interface.tpl"));
	
	## set the vars
	$template->set_var('element_name',$xmldata['NAME']);
	$template->set_var('element_desc',$xmldata['DESC']);
	
	## we got your record to process the data
	
	## now it's time to check the previous data entered
	$value = convert_html($data['text']);
	
	$link = $data['link'];
	if($link == "") {
		##$link = "http://";
	} else {
		$link = utility_preparelink($link);
	}
		
	## set the vars accordingly
	$template->set_var('text',$value);
	$template->set_var('link',$link);
	
	if(!$xmldata['TAG']) {
		$template->set_var('element_tag',LANG_ElementText);
	} else {
		$template->set_var('element_tag',$xmldata['TAG']);
	}
							
	return $template->fill_block("link");
}


## =======================================================================        
##  link_storeData        
## =======================================================================        
## save the content in the db
## ======================================================================= 
function link_storeData($page_id, $identifier) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	$return_value = false;	
	
	$text = $_POST[$identifier."name"];
	$link = $_POST[$identifier."link"];
	## for security and convenience reasons we have to convert the supplied string
	$text = convert_general($text);

	## prepare the db-object
	$db_connectionStore = new DB_Sql();

	## first we need to find out if the entry already exists
	$select_query = "SELECT content_id FROM ".PAGE_LINK." WHERE page_id = '$page_id' AND identifier = '$identifier' AND client_id='$client_id'";
	$result_pointer = $db_connectionStore->query($select_query);	
	
	if($db_connectionStore->num_rows() == 0) { 
		## no entry found
		$insert_query = "INSERT INTO ".PAGE_LINK." (page_id, identifier, text, link, client_id) values ('$page_id', '$identifier', '$text', '$link','$client_id')";
		$result_pointer = $db_connectionStore->query($insert_query);
	} else {
		$db_connectionStore->next_record();
		$content_id = $db_connectionStore->Record["content_id"];
		$update_query = "UPDATE ".PAGE_LINK." SET text = '$text', link = '$link' WHERE content_id = '$content_id' AND client_id='$client_id'";
		$result_pointer = $db_connectionStore->query($update_query);
	}
}

## =======================================================================        
##  link_getData        
## =======================================================================        
##  get the Data
## ======================================================================= 
function link_getData($vPageID,&$page_record) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## data connection
	$db_connectionMain = new DB_Sql();
	## get all text elements
	$select_query = "SELECT text, link, identifier FROM ".PAGE_LINK." WHERE page_id='$vPageID' AND client_id = '$client_id'";
	$result_pointer = $db_connectionMain->query($select_query);

	## loop through the results and set the vars in the template
	while($db_connectionMain->next_record()) {
		$text = $db_connectionMain->Record["text"];
		$link = $db_connectionMain->Record["link"];
		$varname = $db_connectionMain->Record["identifier"];
		$page_record[$varname]["type"] = "TEXT";
		$page_record[$varname]["text"] = $text; 
		$page_record[$varname]["link"] = $link; 
		$page_record[$varname]["identifier"] = $varname; 
	}
}

## =======================================================================        
##  link_getMultiData       
## =======================================================================        
##  get the Data for multiple pages
## ======================================================================= 
function link_getMultiData($vItems) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	##db connection
	$db_connection = new DB_Sql();
	
	## init the vars
	$link_elements = array();
	$query = "";

	## let's prepare a query filter	
	for($i=0;$i<count($vItems); $i++) {
		if($query != "") {
			$query .= " OR ";
		}
		$targetPage= $vItems[$i]["page_id"];
		$query .= "page_id ='$targetPage'";
	}
				
	if($query =="") {
		## we haven't found any... nothing to output
		return;
	}
	
	$select_query = "SELECT page_id,text,link, identifier FROM ".PAGE_LINK." WHERE ($query) AND client_id='$client_id' ORDER BY page_id";
	$result_pointer = $db_connection->query($select_query);
	
	while($db_connection->next_record()) {
		$text = $db_connection->Record["text"];
		$link = $db_connection->Record["link"];
		$varname = $db_connection->Record["identifier"];
		$page_id = $db_connection->Record["page_id"];

		$link_elements[$page_id][$varname]["type"] = "TEXT";
		$link_elements[$page_id][$varname]["text"] = $text;
		$link_elements[$page_id][$varname]["link"] = $link;
		$link_elements[$page_id]["page_id"] = $page_id;
	}
	
	## return the results
	return $link_elements;
}

## =======================================================================        
##  link_deleteData        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function link_deleteData($vPageID) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## data connection
	$db_connectionMain = new DB_Sql();
	## get all text elements
	$query = "DELETE FROM ".PAGE_LINK." WHERE page_id='$vPageID' AND client_id='$client_id'";
	$result_pointer = $db_connectionMain->query($query);
}

## =======================================================================        
##  output_link        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function output_link($item,$structure,$menu_id) {
	$text 		= $item['text'];
	$link 	= $item['link'];
	$identifier	= $item['identifier'];
	
	$fulllink = '<a href="'.$link.'">'.$text.'</a>';
	
	$value = array($identifier.'.name' => $text,$identifier.'.link' => $link,$identifier => $fulllink);

	return $value;
}

## =======================================================================        
##  link_copyData        
## =======================================================================        
##  pass it the sourcepage_id and the targetpage_id. It'll copy all
##  text entries to the new page.
## ======================================================================= 
function link_copyData($source_id, $target_id) {
	global $Auth;
	## multiclient
	$client_id = $Auth->auth['client_id'];	

	## data connection
	$db_source = new DB_Sql();
	$db_target = new DB_Sql();
	
	## get all text elements
	$select_query = "SELECT identifier,text,link,language FROM ".PAGE_LINK." WHERE page_id='$source_id' AND client_id='$client_id'";
	$result_pointer = $db_source->query($select_query);

	## loop through the results and copy them over
	while($db_source->next_record()) {
		$identifier = $db_source->Record['identifier'];
		$text = $db_source->Record['text'];
		$link = $db_source->Record['link'];
		$language = $db_source->Record['language'];

		## since it is possible that we get called muliple times for each datatype that stores the data into our tables,
		## we need to check if the entry already exists
		$query = "SELECT content_id FROM ".PAGE_LINK." WHERE page_id = '$target_id' AND identifier = '$identifier' AND client_id = '$client_id' AND language='$input_language'";
		$result_pointer = $db_target->query($query);			
	
		if($db_target->num_rows() == 0) { 
			$query = "INSERT INTO ".PAGE_LINK." (page_id, identifier, text,link,client_id,language,modified) values ('$target_id', '$identifier', '$text','$link','$client_id','$language',now())";
			$result_pointer = $db_target->query($query);
		}
	}	
}

## =======================================================================        
##  file_displayPreview        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function link_displayPreview($xmldata, $data) {
	$value = convert_html($data['text']);
	return $value;

}

?>
