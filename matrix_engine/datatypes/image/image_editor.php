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
##  linklist_promptDelete        
## =======================================================================        
function image_promptDelete($yesURL,$noURL) {
	global $gSession,$Auth;
	$db_connectionLayout = new DB_Sql();  

	## prepare the template file
	$select_template = new Template(ENGINE."datatypes/image/interface");
	$select_template->set_templatefile(array("body" => "deletelink.tpl"));
	
	$select_template->set_var("yesIMG","lang/".$Auth->auth["language"]."_button_ja.gif");
	$select_template->set_var("noIMG","lang/".$Auth->auth["language"]."_button_nein.gif");
	$select_template->set_var('language_deletepage',LANG_LINKLIST_DeleteTitle);
	$select_template->set_var('language_doyouwant',LANG_LINKLIST_DeleteDesc);
	
	## grab the information for this page
  		
	$select_template->set_var('yesURL',$yesURL);
	$select_template->set_var('noURL',$noURL);
	
	$select_template->pfill_block("body");
}	


## =======================================================================        
##  image_doDelete        
## =======================================================================        
function image_delete() {
	global $Auth,$input_language;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	$db = new DB_Sql();
	$f = new file_object();
	
	## we need a page id and an identifier
	$page_id = intval($_GET['page_id']);
	$identifier = $_GET['identifier'];
	
	## first get the image id for this image
	$query = "SELECT A.image_id,filename FROM ".PAGE_IMAGE." AS A INNER JOIN ".PAGE_IMAGE."_data AS B ON A.image_id=B.image_id WHERE A.page_id=".$page_id." AND A.identifier='".$identifier."'";
	$result_pointer = $db->query($query,true);
	
	if($db->next_record()) {
		$image_id = $db->Record['image_id'];
		$filename = $db->Record['filename'];

		## now check if this is the only usage of this image
		$query = "SELECT page_id FROM ".PAGE_IMAGE." WHERE image_id=".$image_id;
		$result_pointer = $db->query($query,true);

		if($db->num_rows() <=1) {
			## okay it's used only once- let's delete the base entry
			$query = "DELETE FROM ".PAGE_IMAGE."_data WHERE image_id=".$image_id;
			$result_pointer = $db->query($query,true);
			
			## okay this means we can delete the file itself too
			
			$f->delete_file(MATRIX_UPLOADDIR.$filename);
		}		
	}
	
	$query = "DELETE FROM ".PAGE_IMAGE." WHERE page_id='$page_id' AND identifier='$identifier' AND client_id='$client_id' AND language='$input_language'";
	$rp = $db->query($query);
}
	
?>
