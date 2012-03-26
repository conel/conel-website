<?PHP
## ======================================================================= 
##   webmatrix                                                     
## ======================================================================= 
##   Copyright (c) 2001, 2002, 2003 Stefan Elsner, Workmatrix        
## ======================================================================= 
##   <info@workmatrix.de> 
##
## ======================================================================= 

#################################
#     Required Include Files    #
#################################
require("../../config.php");

if(REWRITE_GLOBALS == "ON") {
	include("../../functions/register_globals.php");
}
## include the template class
require("../../".CLASSES_DIR."template.php");
  
## include the db class
require("../../".CLASSES_DIR."db_mysql.php");
require("../../".CLASSES_DIR."files.php");


require("../../".CLASSES_DIR."container.php");
require("../../".CLASSES_DIR."session.php");
require("../../".CLASSES_DIR."authentication.php");
require("../../".CLASSES_DIR."page.php");

## the xmlparser
require("../../".CLASSES_DIR."xmlparser.php");

require("../../".CLASSES_DIR."class_mailer.php");

include(ENGINE.'functions/language.php');

## matrix_functions => general functions
##require("../../functions/utilities.php");

page_open(array("session" => "session_object", "authenticate" => "Auth")); 
page_close();

## we need to load the language specific strings
include("interface/lang/".$Auth->auth["language"].".php");
include("../../interface/lang/".$Auth->auth["language"].".php");

include("bigmovie_editor.php");

## register the language
language_registerLanguage();

if(isset($_POST['op'])) {
	$op = $_POST['op'];
} else {
	$op = $_GET['op'];
}

_mylog('test');

####################################################################################
#    The SWITCH:                                                                   #
#       This switch statement takes the arguement $op passed from an admin page    #
#       and decides which functions to call based on that variable.                #
####################################################################################  
switch($op) {
	case "add":
		## okay we are basically displaying the template to add a file
		$page_id = intval($_GET['page_id']);
		$identifier = $_GET['identifier'];
		
		bigmovie_selectFileDialog($page_id,$identifier);
		
		break;
	case "store":
		## okay first prepare the vars
		$page_id = intval($_POST['pageID']);
		$identifier = $_POST['identifier'];	
		$hash = $_POST[$identifier];
		
		bigmovie_prepareFile($hash,$identifier);
		bigmovie_storeFile($page_id, $identifier);
		close_reload("");
		break;
		
	case "delete":
		## is called via the main form
		$pageID 	= $_GET['page_id'];
		$identifier	= $_GET['identifier'];

		## we need to generate the right urls
		$yesURL = "editor.php?op=doDelete&page_id=".$pageID."&language=".$input_language."&identifier=".$identifier;
		$yesURL = $gSession->url($yesURL);
		
		$noURL = "editor.php?op=closeEditor";
		$noURL = $gSession->url($noURL);		
		
		file_promptDelete($yesURL,$noURL);		
		break;

	case "doDelete":
		$pageID 	= $_GET['page_id'];
		$identifier	= $_GET['identifier'];
		$itemID 	= $_GET['item_id'];

		file_delete($pageID, $identifier);
		close_reload("");
		break;
	case "process":
		global $Auth,$input_language;
	
		## multiclient
		$client_id = $Auth->auth["client_id"];	
		$page_id 	= $_GET['page_id'];
		$identifier	= $_GET['identifier'];

		$userfile = MATRIX_BASEDIR.'upload/'.$_POST['filename1'];
		$file_name = $_POST['filename1'];
  		_mylog($file_name);
  		_mylog($page_id);
		if ($userfile != "none" && $userfile!='') { 
			$db_connectionStore = new DB_Sql();
			## we need to move the file to the upload dir and register it in the db
			$f = new file_object(); 
			$filename = $f->upload($userfile, $file_name,$file_size,$file_type, MATRIX_UPLOADDIR_DOCS);
			_mylog($userfile);
			_mylog($file_name);
			_mylog($filename);
			if($filename != -1) {
				## first we need to find out if the entry already exists
				$select_query = "SELECT movie_id,filename FROM ".DB_PREFIX."page_movie WHERE page_id = '$page_id' AND identifier = '$identifier' AND client_id='$client_id'";
				_mylog($select_query);
				$result_pointer = $db_connectionStore->query($select_query);
		
				if($db_connectionStore->num_rows() == 0) { 
					## no entry found
					$insert_query = "INSERT INTO ".DB_PREFIX."page_movie (page_id, identifier, filename, client_id) values ('$page_id', '$identifier', '$filename','$client_id')";
					_mylog($insert_query);
					$result_pointer = $db_connectionStore->query($insert_query);
				} else {
					$db_connectionStore->next_record();
					$file_id = $db_connectionStore->Record["movie_id"];
					$old_filename = $db_connectionStore->Record["filename"];
					
					## delete the old file first
					$f->delete_file(MATRIX_UPLOADDIR_DOCS.$old_filename);
					
					$update_query = "UPDATE ".DB_PREFIX."page_movie SET filename = '$filename' WHERE movie_id = '$file_id' AND client_id='$client_id'";
					_mylog($update_query);
					$result_pointer = $db_connectionStore->query($update_query);
				}
			}				
		}
		break;		
		
    default:
      	close_reload("");
      	break;
    }
  
  
 ## =======================================================================
##  _log													
## =======================================================================
##  used for error tracking
## =======================================================================
function _mylog($str) {
	
	$file = "logs\log.log";
	
	var_dump('1');
	if(file_exists($file) && is_writeable($file)) { $mode="a"; } else { $mode="w"; }
	$str = var_export($str,true);
	
	$fp=fopen($file,$mode);
	fwrite($fp,sprintf("%s Request: %s\n", strftime("%c"), $str));
	fclose($fp); 
	
}	 
## =======================================================================        
##  close_reload        
## =======================================================================        
##  closes the current window and updates the parent window   
##
##  TODO:
## =======================================================================        
function close_reload($targetURL) {
	global $gSession;
	## prepare the template file
	$select_template = new Template(ENGINE."datatypes/file/interface");
	$select_template->set_templatefile(array("body" => "closesubmit.tpl"));
	
	$targetURL = $gSession->url($targetURL);
	
	$select_template->set_var('targetURL',$targetURL);
	$select_template->pfill_block("body");
}?>
