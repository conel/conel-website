<?PHP

	require("../framework.php");
	require("functions/displaysetup.php");
	require("functions/savesetup.php");
	
	## fetch the language file
	include(ENGINE."modules/settings/interface/lang/".$Auth->auth["language"].".php");
	
	## here we define settings that might later be placed into a config file
	define("PAGE_TEMPLATE_GROUP",DB_PREFIX."page_template_group");


## the user needs an accesslevel of 100 in order to access the 
## functionality of the template mechanism
## so we check it here- and if it's not okay for the user
## we will return an error page
##$access_rights = _getUserAccessRights($Auth->auth['user_id']);
##if($access_rights['template']['access']!=1) {
##	ui_output_error("<b>".LANG_TemplateEditor."</b><br><br> ".LANG_NoAccessRights);
##	exit;
##} 	

$template_id = isset($_GET['template_id']) ? $_GET['template_id'] : (isset($_POST['template_id']) ? $_POST['template_id'] : '');	
$op = isset($_GET['op']) ? $_GET['op'] : (isset($_POST['op']) ? $_POST['op'] : '');


####################################################################################
#    The SWITCH:                                                                   #
#       This switch statement takes the arguement $op passed from an admin page    #
#       and decides which functions to call based on that variable.                #
####################################################################################  
	switch($op) {            
		case "setup":
			## we need to display the setup screen
			displaySetup();
			break;
		
		case "save":
			## we need to store the settings and
			## display a confirmation page
			saveSetup();
			output_confirm("Settings","Your settings have been saved successfuly.","matrix_menu.php");			
			break;
		case "empty":
			## this deletes all files in the cache
			$f = new file_object();  
			$f->clean_directory(MODULE_CACHE_DIR);
			output_confirm(LANG_MODULE_CACHE_TitleClean,LANG_MODULE_CACHE_CleanedSuccessfully,"matrix_menu.php");   		
			break;
		default:
			## we need to display the setup screen
			displaySetup();
		break;
	}


## =======================================================================        
##  show_input        
## =======================================================================        
##  shows the appropriate Input Form for this container 
##
##  TODO:  
## =======================================================================
function show_input($template_id) {
	global $gSession,$Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];
		
	## this function displays the input form
	## if we got a template_id, we will get all
	## required info for this template- otherwise we will
	## display the empty form

	## the next step will be to display all nescessary forms
	$inputFile = "template_input.tpl";
	$input_template = new Template("interface/");
	$input_template->set_templatefile(array("head" => $inputFile,"intro" => $inputFile,"body" => $inputFile,"foot" => $inputFile));

	$input_template->set_var("saveIMG","lang/".$Auth->auth["language"]."_button_save.gif");
	$input_template->set_var('language_deleteelementdesc',LANG_DeleteElementDescription);
	$input_template->set_var('language_templatehead',LANG_TemplateEnterData);
	$input_template->set_var('language_templatebody',LANG_TemplateEnterDataDescription);
	$input_template->set_var('language_templateinputname',LANG_TemplateName);
	$input_template->set_var('language_templateinputnamedesc',LANG_TemplateNameDescription);
	$input_template->set_var('language_templatedesc',LANG_TemplateDesc);	
	$input_template->set_var('language_templatedescdesc',LANG_TemplateDescDescription);	
	$input_template->set_var('language_templatefilename',LANG_TemplateFile);	
	$input_template->set_var('language_templatefilenamedesc',LANG_TemplateFileDescription);
	$input_template->set_var('language_templatethumb',LANG_TemplateThumb);
	$input_template->set_var('language_templatethumbdesc',LANG_TemplateThumbDescription);
	
	$input_template->set_var('language_templateparent',LANG_TemplateParent);
	$input_template->set_var('language_templateparentdesc',LANG_TemplateParentDescription);	

	$input_template->set_var('language_templatelevels',LANG_TemplateLevels);
	$input_template->set_var('language_templatelevelsdesc',LANG_TemplateLevelsDesc);	

	$input_template->set_var('Session',$gSession->id);
	$input_template->set_var('template_id',$template_id);
	$input_template->set_var('page_id',$page_id);
	
	$input_template->set_var('low_sub',$low_sub);

	$targetURL = "template.php";
	$targetURL = $gSession->url($targetURL);
	$input_template->set_var('targetURL',$targetURL);
	
	## the next step is to ouput the head
	$input_template->pfill_block("head");
	$input_template->pfill_block("intro");	
	
	## we should get the page content
	if(!$template_id) {
		## now prepare the default level selector
		$levels = '<select name="levels">';
		$levels .= '<option label="'.LANG_TemplateLevelsNoneSelected.'" value="-1">'.LANG_TemplateLevelsNoneSelected.'</option>';
		for($i=1;$i<7; $i++) {
			$levels .= '<option label="'.$i.'" value="'.$i.'">'.$i.'</option>';
		}
		$levels .= '</select>';
		
		## output the element
		$input_template->set_var('levelsform',$levels);
		$input_template->pfill_block("body");
		$input_template->set_var('op',"save_template");		
		
	} else {
		## prepare the db-object
		$db_connection = new DB_Sql();
	
		## grab the information
		$select_query = "SELECT level,hidden,icon,description,basename,parent,template_id, title FROM ".PAGE_TEMPLATE." WHERE template_id = '".$template_id."' AND client_id='$client_id'";
		$result_pointer = $db_connection->query($select_query);	
		
		$db_connection->next_record();
		## first we get all the data
		$icon 			= $db_connection->Record["icon"];
		$description 	= $db_connection->Record["description"];
		$basename 		= $db_connection->Record["basename"];
		$template_id 	= $db_connection->Record["template_id"];
		$title		 	= $db_connection->Record["title"];
		$parent 		= $db_connection->Record["parent"];
		$hidden 		= $db_connection->Record["hidden"];
		$level 			= $db_connection->Record["level"];
		
		$input_template->set_var("template_icon",$icon);
		$input_template->set_var("template_description",$description);
		$input_template->set_var("template_basename",$basename);
		$input_template->set_var("template_id",$template_id);
		$input_template->set_var("template_title",$title);
		$input_template->set_var("template_parent",$parent);

		## now prepare the default level selector
		$levels = '<select name="levels">';
		if($level == -1) {
			$levels .= '<option label="'.LANG_TemplateLevelsNoneSelected.'" value="-1" selected>'.LANG_TemplateLevelsNoneSelected.'</option>';
		} else {
			$levels .= '<option label="'.LANG_TemplateLevelsNoneSelected.'" value="-1">'.LANG_TemplateLevelsNoneSelected.'</option>';
		}
		for($i=1;$i<7; $i++) {
			if($level == $i) {
				$levels .= '<option label="'.$i.'" value="'.$i.'" selected>'.$i.'</option>';
			} else {
				$levels .= '<option label="'.$i.'" value="'.$i.'">'.$i.'</option>';
			}
		}
		$levels .= '</select>';
		
		## output the element
		$input_template->set_var('levelsform',$levels);
	
		
		if($hidden==1) 		  { $input_template->set_var("hidden_status",'checked'); }	
		
		## output the actual image in here:
		if($icon && $icon!=-1) {
			$image = '<img src="../../../layout/icons/'.$icon .'">';					
		} else {
			$image = '<img src="interface/images/blank.gif">';
		}
		
		$input_template->set_var("file",$image);					
		$input_template->set_var('op',"update_template");
		$input_template->pfill_block("body");
	}	

	## we need to set the ids and stuff
	$input_template->pfill_block("foot");
}

## =======================================================================        
##  new_group($name)       
## =======================================================================        
## stores the information for a certain container element into the db 
##
##  TODO: 
##    -- we need to set the vars properly      
## =======================================================================
function new_group($name) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];	
			
	## I'll get the file stuff later on
	$db = new DB_Sql();
	
	$query   = "INSERT INTO ".PAGE_TEMPLATE_GROUP." (name) values ('$name')";
	$rp = $db->query($query);
	$group_id    = $db->db_insertid($rp);
			
	return $group_id;
}



## =======================================================================        
##  store_container        
## =======================================================================        
## stores the information for a certain container element into the db 
##
##  TODO: 
##    -- we need to set the vars properly      
## =======================================================================
function save_template() {
	global $HTTP_POST_VARS;
	global $HTTP_POST_FILES;
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];	

	## okay if recieve this command, we will have to store a new
	## template... we will try this in about 10 minutes from now on
	
	## first we get the values
	$template_title       = $_POST['template_title'];
	$template_description = $_POST['template_description'];
	$template_basename    = $_POST['template_basename'];

	$hidden = ($_POST['hidden'] == 1) ? true : null;
	
	if($_POST['template_parent']=="") {
		$template_parent = null;
	} else {
		$template_parent = $_POST['template_parent'];
	} 	
	
	$filename = -1;
	if($_FILES['template_icon']['tmp_name'] !="" && $HTTP_POST_FILES['template_icon']['tmp_name'] != "none") {
		$tmp_name = $_FILES['template_icon']['tmp_name'];
		$name = $_FILES['template_icon']['name'];
		$size = $_FILES['template_icon']['size'];
		$type = $_FILES['template_icon']['type'];

		## now we should upload
		$f = new file_object(); 	
		$filename = $f->upload($tmp_name, $name,$size,$type,MATRIX_BASEDIR.'layout/icons/');
	}
	
	$level = $_POST['levels'];
			
	## I'll get the file stuff later on
	$db_connection = new DB_Sql();

	$lock_query = "LOCK TABLE ".PAGE_TEMPLATE." WRITE";
	$result_pointer = $db_connection->query($lock_query);
	
	$insert_query   = "INSERT INTO ".PAGE_TEMPLATE." (level,hidden,title, description,basename,icon,parent, client_id) values ('$level','$hidden','$template_title', '$template_description','$template_basename','$filename','$template_parent','$client_id')";
	$result_pointer = $db_connection->query($insert_query);
	$template_id    = $db_connection->db_insertid($result_pointer);
		
	$lock_query = "unlock table";
	$result_pointer = $db_connection->query($lock_query);
	
	return $template_id;
}


## =======================================================================        
##  store_container        
## =======================================================================        
## stores the information for a certain container element into the db 
##
##  TODO: 
##    -- we need to set the vars properly      
## =======================================================================
function update_template($template_id) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];	
	
	## okay if recieve this command, we will have to update an template

	## first we get the values
	$template_title       = $_POST['template_title'];
	$template_description = $_POST['template_description'];
	$template_basename    = $_POST['template_basename'];
	
	$hidden = ($_POST['hidden'] == 1) ? true : null;

	if($_POST['template_parent']=="") {
		$template_parent = null;
	} else {
		$template_parent = $_POST['template_parent'];
	} 
	
	$filename = -1;
	if($_FILES['template_icon']['tmp_name'] !="" && $HTTP_POST_FILES['template_icon']['tmp_name'] != "none") {
		$tmp_name = $_FILES['template_icon']['tmp_name'];
		$name = $_FILES['template_icon']['name'];
		$size = $_FILES['template_icon']['size'];
		$type = $_FILES['template_icon']['type'];
		## now we should upload
		$f = new file_object(); 	
		$filename = $f->upload($tmp_name, $name,$size,$type,MATRIX_BASEDIR.'layout/icons/');
	}
	
	$level = $_POST['levels'];
			
	## I'll get the file stuff later on
	$db_connection = new DB_Sql();
	if($filename!= -1) {
		$update_query = "UPDATE ".PAGE_TEMPLATE." SET level='$level', hidden='$hidden',icon = '$filename',title = '$template_title', description = '$template_description',basename = '$template_basename',parent='$template_parent' WHERE template_id = '$template_id' AND client_id='$client_id'";
		$result_pointer = $db_connection->query($update_query);
	} else {
		$update_query = "UPDATE ".PAGE_TEMPLATE." SET level='$level',hidden='$hidden',title = '$template_title', description = '$template_description',basename = '$template_basename',parent='$template_parent' WHERE template_id = '$template_id' AND client_id='$client_id'";
		$result_pointer = $db_connection->query($update_query);
	}		


	return $template_id;
}


## =======================================================================        
##  drop_container        
## =======================================================================        
##  deletes the elements of a certaain container
##
##  TODO: 
##     - check if we really deleet everything- links???
## =======================================================================
function delete_template($template_id) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	## first we check if recieved an template_id
	if($template_id) {
		## prepare the db-object
		$db_connection = new DB_Sql(); 
		
		## then we check if there are any pages that use this template
		$select_query = "SELECT page_id FROM ".USER_PAGES." WHERE template='$template_id' AND client_id='$client_id'";
		$result_pointer = $db_connection->query($select_query);		
		
		if($db_connection->num_rows() == 0) {
			## okay we can delete this template
			$select_query = "DELETE FROM ".PAGE_TEMPLATE." WHERE template_id='$template_id' AND client_id='$client_id'";
			$result_pointer = $db_connection->query($select_query);	
		} else {
			## we should return a error page
		}
	} else {
		return -1;
	}
}



?>
