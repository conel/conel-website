<?php

	// Webmatrix Authentication Code
	require("matrix_engine/config.php");
	if (REWRITE_GLOBALS == "ON") { include("matrix_engine/functions/register_globals.php"); }
	require("matrix_engine/classes/template.php"); // include the template class
	require("matrix_engine/classes/db_mysql.php"); // include the db class
	require("matrix_engine/classes/container.php");
	require("matrix_engine/classes/session.php");
	require("matrix_engine/classes/authentication.php");
	require("matrix_engine/classes/page.php");
	require("matrix_engine/classes/class_mailer.php");
	include("matrix_engine/functions/access.php");
	include("matrix_engine/functions/ui_dialogs.php");

	page_open(array("session" => "session_object", "authenticate" => "Auth"));
	page_close();

	include("matrix_engine/interface/lang/".$Auth->auth["language"].".php");

	// check if we have the correct access rights
	$access_rights = _getUserAccessRights($Auth->auth['user_id']);

	if (!isset($access_rights['pages']['workspace'])) {
		// display the error message
		ui_output_error("<strong>Pages</strong><br /><br /> ".LANG_NoAccessRights);
		exit;
	}

	/*********************************************************************/
	/*                             fileNice                              */
	/*                                                                   */
	/*  Heirachical PHP file browser - http://filenice.com               */
	/*  Written by Andy Beaumont - http://andybeaumont.com               */
	/*                                                                   */
	/*  Send bugs and suggestions to stuff[a]fileNice.com                */
	/*                                                                   */
	/*                                                                   */
	/*********************************************************************/

	/*********************************************************************/
	/*                                                                   */
	/* User editable preferences are now stored in fileNice/prefs.php    */
	/* for easier maintenance and to assist with some fancy new features */
	/* in this and future versions.                                      */
	/*                                                                   */
	/*********************************************************************/

	include("fileNice/prefs.php");


	// nkowald - 21/07/09 - Check for file upload POST
	$message = '';
	if (isset($_POST) && $_POST['MAX_FILE_SIZE'] != '') {

		$uploaded_file = (isset($_FILES['uploadedfile']) && $_FILES['uploadedfile'] != '') ? $_FILES['uploadedfile'] : '' ;

		if ($uploaded_file['error'] > 0) {
		  $message = "<p class=\"error\!>Error: " . $_FILES["uploadedfile"]["error"] ."</p>";
		}

		if ($uploaded_file != '' && $message == '') {

			// First check if it is a valid filetype
			$filetype = substr(strrchr($uploaded_file['name'],'.'),1);

			// for each filetype see if uploaded type is allowed
			$allowed_filetype = FALSE;

			// Set default path to images
			$target_path = 'images/';

			// Is file an image?
			if (in_array($filetype, $imgTypes)) {
				$allowed_filetype = TRUE;
				$target_path = 'images/';
			}

			// Is file an embed type?
			if (in_array($filetype, $embedTypes)) {
				$allowed_filetype = TRUE;
				$target_path = 'docs/';
			}

			// Is file an html type?
			if (in_array($filetype, $htmlTypes)) {
				$allowed_filetype = TRUE;
				$target_path = 'docs/';
			}

			// Is file a php type?
			if (in_array($filetype, $phpTypes)) {
				$allowed_filetype = TRUE;
				$target_path = 'docs/';
			}

			// Is file a miscellaneous type?
			if (in_array($filetype, $miscTypes)) {
				$allowed_filetype = TRUE;
				$target_path = 'docs/';
			}

			if ($allowed_filetype == TRUE) {

				// Target path will change depending on filetype uploading
				// Check if file already exists
				
				$filename = $_FILES['uploadedfile']['name'];
				$new_target_path = $target_path . basename($filename); 
				if (file_exists($new_target_path)) {
					$filetype = "." . $filetype;
					$filename = str_replace($filetype,'',$filename);
					$filename = $filename . "-1" .$filetype;
					$new_target_path = $target_path . basename($filename);
				}

				if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $new_target_path)) {
					$message = "<p>The file '$filename' has been uploaded to the '$target_path' directory</p>";
				} else {
					$message = "<p>There was an error uploading the file, please try again!</p>";
				}

			} else {
				$message = '<p class="error">Unable to upload \''.$_FILES['uploadedfile']['name'].'\'.<br /><strong>\'.'.$filetype.'\' files not allowed.</strong>';
			}

		}

	} // file upload handler

/*********************************************************************/
/*                                                                   */
/*  Best not to touch stuff below here unless you know what you're   */
/*  doing.                                                           */
/*                                                                   */
/*********************************************************************/

$version = "1.1";

$server = $_SERVER['HTTP_HOST'];
//$thisDir = dirname($_SERVER['PHP_SELF']); 
$thisDir = '/';
$pathToHere = "http://$server$thisDir";
$dir=isset($_GET['dir'])?$_GET['dir']:'';if(strstr($dir,'..'))$dir='';


if($dir != ""){
	$titlePath = "http://$server/?dir=$dir";
	$path = $dir;	
}else{
	$titlePath = "http://$server$thisDir";
}


	
include "fileNice/fileNice.php";

// HANDLE THE PREFERENCES
$names = array("showImg","showEmbed","showHtml","showScript","showMisc");
if($_POST['action'] == "prefs"){
	// lets set the cookie values
	$varsArray = array();
	for($i=0; $i<count($names);$i++){
		if($_POST[$names[$i]] == "show"){
			$varsArray[$names[$i]] = "show";
		}else{
			$varsArray[$names[$i]] = "hide";
		}
		setcookie($names[$i],$varsArray[$names[$i]],time()+60*60*24*365);
		$$names[$i] = $varsArray[$names[$i]];
	}
	// set the skin
	setcookie("skin",$_POST['skin'],time()+60*60*24*365);
	$skin = $_POST['skin'];
	// set the slideshow speed
	setcookie("ssSpeed",$_POST['ssSpeed'],time()+60*60*24*365);
	$ssSpeed = $_POST['ssSpeed'] * 1000;
	// set the sortBy
	setcookie("sortBy",$_POST['sortBy'],time()+60*60*24*365);
	$sortBy = $_POST['sortBy'];
	// set the sortDir
	setcookie("sortDir",$_POST['sortDir'],time()+60*60*24*365);
	$sortDir = $_POST['sortDir'];
}else{
	// retreive prefs
	for($i=0; $i<count($names);$i++){
		if(isset($_COOKIE[$names[$i]])){
			//echo("COOKIE[".$names[$i]."] = " . $_COOKIE[$names[$i]] . "<br />");
			if($_COOKIE[$names[$i]] != "show"){
				$$names[$i] = "hide";
			}else{
				$$names[$i] = "show";
			}
		}else{
			$$names[$i] = "show";
		}
	}
	// GET THE PREFERRED SKIN
	if(isset($_COOKIE['skin'])){
		$skin = $_COOKIE['skin'];
	}else{
		$skin = $defaultSkin;
	}
	// GET THE SLIDE SHOW SPEED
	if(isset($_COOKIE['ssSpeed'])){
		$ssSpeed = $_COOKIE['ssSpeed'] * 1000;
	}else{
		$ssSpeed = $defaultSSSpeed * 1000;
	}
	// GET THE SORT BY AND DIRECTION
	if(isset($_COOKIE['sortBy'])){
		$sortBy = $_COOKIE['sortBy'];
	}else{
		$sortBy = $defaultSort;
	}
	if(isset($_COOKIE['sortDir'])){
		$sortDir = $_COOKIE['sortDir'];
	}else{
		$sortDir = $defSortDirection;
	}
}





if($_GET['action'] == "getFolderContents"){
	if(substr($_GET['dir'],0,2) != ".." && substr($_GET['dir'],0,1) != "/" && $_GET['dir'] != "./" && !stristr($_GET['dir'], '../')){
		$dir = $_GET['dir'];
		$list = new FNFileList;
		$list->getDirList($dir);
		exit;
	}else{
		// someone is poking around where they shouldn't be
		echo("Don't hack my shit yo.");
		exit;	
	}
}else if($_GET['action'] == "nextImage"){
	$out = new FNOutput;
	$tmp = $out->nextAndPrev($_GET['pic']);
	if($tmp[1] == ""){
		$nextpic = $tmp[2];
	}else{
		$nextpic = $tmp[1];
	}
	// get the image to preload
	$tmp2 = $out->nextAndPrev($nextpic);
	// get the image dimensions
	$imageDim = @getimagesize($nextpic);
	echo $nextpic."|".$imageDim[0]."|".$imageDim[1]."|".$tmp2[1];
	exit;
}





?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>

	<title>webmatrix: Files Manager</title>
	
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="content-language" content="en" />
	<meta name="robots" content="none" />
	<meta name="author" content="Andy Beaumont - http://andybeaumont.com" />
	<meta name="generator" content="the fantabulous mechanical eviltwin code machine" />
	<meta name="MSSmartTagsPreventParsing" content="true" />
	
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<link rel="stylesheet" type="text/css" href="fileNice/skins/<?php echo $skin; ?>/fileNice.css" />
	<link rel="stylesheet" type="text/css" href="fileNice/skins/<?php echo $skin; $r = rand(99999,99999999); echo "/icons.php?r=$r\"" ?> />
	
<script language="javascript" type="text/javascript">
var ssSpeed = <?php echo $ssSpeed; ?>;
</script>
	
	<script src="fileNice/fileNice.js" type="text/javascript"></script>
		
</head>

<body>
<!-- busy indicators -->
<div id="overDiv">&nbsp;</div>
<div id="busy">&nbsp;</div>
<!-- main -->
<div id="container">
	<div id="header">
	<form name="search" id="search" action="<?php echo $PHP_SELF; ?>" method="post"><input type="text" name="sstring" id="sstring" value="<?php echo $_POST['sstring']; ?>" /><input type="button" name="search" value="search" id="searchButton" onclick="validateSearch();" /></form>
		<!-- please leave the word fileNice visible on the page, it's only polite really isn't it. -->
		<h1><a href="#" title="About fileNice&trade;" class="logo about_filenice">fileNice&#8482;</a></h1>
		<ul id="options_nav">
			<li><h3><a href="#" title="edit prefs" class="expander preferences">Preferences</a></h3></li>
			<li><a href="file_browser.php?Session=<?php echo $_GET['Session']; ?>">Home</a></li>
			<li><a href="#" onclick="toggleFileUpload()">Upload a File</a></li>
		</ul>
		<br class="clear_both" />
	</div>
<div id="about_filenice">
fileNice&trade; <?php echo $version; ?><br />
<!-- please leave the word fileNice and the link to fileNice.com in the about, it's only polite really isn't it. I didn't do all this work just for you to try to pass it off as your own. -->
Free open source file browser available from <a href="http://fileNice.com" title="fileNice.com">fileNice.com</a><br />
Created by <a href="http://andybeaumont.com" title="andybeaumont.com">Andy Beaumont</a><br />
<br />Enhanced by Nathan Kowald<br />
20/07/09 - added webmatrix login security<br />
21/07/09 - added ability to upload files<br />
</div>

<form name="prefs" action="<?php echo $PHP_SELF; ?>" method="post" id="preferences">
Preferences:<br /><br />
<fieldset>
<legend>Sort by</legend>
<input type="radio" name="sortBy" id="name" value="name" <?php if($sortBy == "name") echo"checked=\"checked\""; ?> />
<label for="name">file name</label><br />
<input type="radio" name="sortBy" id="date" value="date" <?php if($sortBy == "date") echo"checked=\"checked\""; ?> />
<label for="date">date modified</label>
</fieldset>

<fieldset>
<legend>Sort direction</legend>
<input type="radio" name="sortDir" id="ascending" value="ascending" <?php if($sortDir == "ascending") echo"checked=\"checked\""; ?> />
<label for="ascending">ascending</label><br />
<input type="radio" name="sortDir"  id="descending" value="descending" <?php if($sortDir == "descending") echo"checked=\"checked\""; ?> />
<label for="descending">descending</label>
</fieldset>

<fieldset>
<legend>Filetypes to display</legend>
<input type="hidden" name="action" value="prefs" />
<?php if($showImg != "show") $checked = "";  else  $checked = "checked=\"checked\"";?>
<input type="checkbox" name="showImg" id="showImg" value="show" <?php echo $checked; ?> />
<label for="showImg">Show image type files</label><br />

<?php if($showEmbed  != "show") $checked = "";  else  $checked = "checked=\"checked\"";?> 
<input type="checkbox" name="showEmbed" id="showEmbed" value="show" <?php echo $checked; ?> />
<label for="showEmbed"> Show embed type files</label><br />

<?php if($showHtml != "show") $checked = "";  else  $checked = "checked=\"checked\"";?> 
<input type="checkbox" name="showHtml" id="showHtml" value="show" <?php echo $checked; ?> />
<label for="showHtml">Show html/text files</label><br />

<?php if($showScript != "show") $checked = "";  else  $checked = "checked=\"checked\"";?> 
<input type="checkbox" name="showScript" id="showScript" value="show" <?php echo $checked; ?> />
<label for="showScript">Show script files</label><br />

<?php if($showMisc != "show") $checked = "";  else  $checked = "checked=\"checked\"";?> 
<input type="checkbox" name="showMisc" id="showMisc" value="show" <?php echo $checked; ?> />
<label for="showMisc" >Show misc files</label><br />

<br />
</fieldset>
<fieldset>
<legend>Slideshow speed</legend>
<input type="text" maxlength="2" name="ssSpeed" id="slideshow_speed" value="<?php echo $ssSpeed/1000; ?>" style="width:30px;" /> seconds per image<br />
</fieldset>
<fieldset class="hidden">
<legend>Skin</legend>
<select name="skin" id="skin_select">
<?php
$hook = opendir("./fileNice/skins/");
while (false !== ($file = readdir($hook))){
	if($file != "." && $file != ".."){
		if($file == $skin){
			echo("<option value=\"$file\" selected=\"selected\">$file</option>");	
		}else{
			echo("<option value=\"$file\">$file</option>");	
		}
	}
}
closedir($hook);
?>
</select><br />
</fieldset>
<input type="submit" name="Save" id="prefSave" value="Save" />
</form>

<br class="clear_both" />
<hr />

<?php
/* modified - nkowald 21/07/09 - Adding File Upload */
?>
<div id="upload_file">
	<h4>Upload a File</h4>
	<form enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI']; ?>&amp;test=1" method="post">
		<table>
			<tr>
				<td><input name="uploadedfile" type="file" /></td>
			</tr>
			<tr>
				<td>
					<br />
					<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
					<input type="submit" value="Upload File" />
				</td>
			</tr>
		</table>
	</form>
</div>

<?php
if (isset($message) && $message != '') {
	echo '<div id="message">';
	echo $message;
	echo '</div>';
}
?>

<h4>Browse Files</h4>

<?php
if(isset($_GET['view'])){
	if(substr($_GET['view'],0,2) != ".." && substr($_GET['view'],0,1) != "/" && $_GET['view'] != "./" && !stristr($_GET['view'], '../')){
		$out = new FNOutput;
		$out->viewFile($_GET['view']);
	}else{
		// someone is poking around where they shouldn't be
		echo("Don't hack my shit yo.");
		exit;	
	}
}else if(isset($_GET['src'])){
	if(substr($_GET['src'],0,2) != ".." && substr($_GET['src'],0,1) != "/" && $_GET['src'] != "./" && !stristr($_GET['src'], '../')){
		$out = new FNOutput;
		$out->showSource($_GET['src']);
	}else{
		// someone is poking around where they shouldn't be
		echo("Don't hack my shit yo.");
		exit;	
	}
}

?> <ul id="root"> <?php

// show file list
$list = new FNFileList;

if(isset($_POST['sstring'])){
	$t = $_POST['sstring'];
	$sstring = ereg_replace("[\'\")(;|`,<>]", "", $t);
	$list->search($sstring);
}

if($dir != ""){
		$list->getDirList($dir);
}else{
		$list->getDirList("./");
}





?>
</ul>
</div>


<!-- send to Flickr form -->
<form name="flickr" action="http://www.flickr.com/tools/sendto.gne" method="get">
<input type="hidden" name="url" />
</form>
<!-- script for applying the javascript events -->
<script type="text/javascript">
	//<![CDATA[
	var o = new com.filenice.actions;
	o.setFunctions();

	function toggleFileUpload() {
		var upload_div = document.getElementById('upload_file');
		var state = upload_div.style.display;
		if (state == '') {
			upload_div.style.display = 'block';		
		} else {
			upload_div.style.display = '';		
		}
	}

	//]]>
</script>
</body>
</html>
