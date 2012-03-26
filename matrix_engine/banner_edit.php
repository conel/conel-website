<?php
	// Required files
	require("config.php");
	if (REWRITE_GLOBALS == "ON") { include("functions/register_globals.php"); }
	require(CLASSES_DIR."template.php"); // include the template class
	require(CLASSES_DIR."db_mysql.php"); // include the db class
	require(CLASSES_DIR."container.php");
	require(CLASSES_DIR."session.php");
	require(CLASSES_DIR."authentication.php");
	require(CLASSES_DIR."page.php");
	include("functions/access.php");

	page_open(array("session" => "session_object", "authenticate" => "Auth"));
	page_close();

	include("interface/lang/".$Auth->auth["language"].".php");

	// check if we have the correct access rights
	$access_rights = _getUserAccessRights($Auth->auth['user_id']);

	if (!isset($access_rights['pages']['workspace'])) {
		// display the error message
		ui_output_error("<strong>Pages</strong><br /><br /> ".LANG_NoAccessRights);
		exit;
	}

	$banner_pos = (isset($_REQUEST['pos']) && $_REQUEST['pos'] != '') ? $_REQUEST['pos'] : '' ;
	$session = $_GET['Session'];

	$host = 'localhost';
	$user = 'root';
	$pass = '1ctsql';
	$db = 'conel';

	$link = mysql_connect($host, $user, $pass) or die("Can not connect." . mysql_error());
	mysql_select_db($db) or die("Can not connect.");

	if ($banner_pos != '') {
		// Get a list of tables we should be able to export data from
		$query = "SELECT id, link, img_url FROM webmatrix_banners WHERE position = $banner_pos";

		$result = mysql_query($query);
		if ($result) {
			while ($row = mysql_fetch_assoc($result)) {
				$banner_id			= $row['id'];
				$banner_link		= $row['link'];
				$banner_img_url		= $row['img_url'];
			}
		}
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-AU" xml:lang="en-AU">
<head>
<title>Banners</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../layout/css/banners.css" rel="stylesheet" type="text/css" media="screen" />
<script src="../layout/js/jquery-1.4.4.min.js"></script>
</head>
<body> 
<div id="holder">
<div id="banner_edit_form">
	<h3>Update Banner</h3>
		<form enctype="multipart/form-data" action="banners_upload.php?Session=<?php echo $session; ?>" method="POST">
			<table>
				<tr>
					<td><label for="upload_link">Link:</label></td>
					<td><input type="text" name="banner_link" class="field" value="<?php echo $banner_link; ?>" id="upload_link" /></td>
				</tr>
				<tr>
					<td valign="top"><label for="new_upload_banner">Banner:</label></td>
					<td>
						<input type="file" name="new_banner_img" id="upload_banner" />
						<input type="hidden" name="MAX_FILE_SIZE" value="500000" />
						<input type="hidden" name="id" value="<?php echo $banner_id; ?>" />
						<input type="hidden" name="action" value="update" />
						<input type="hidden" name="Session" value="<?php echo $session; ?>" />
						<p class="note">Only select a banner image if you're changing the image</p>
					</td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" value="Update Banner" /></td>
				</tr>
			</table>
		</form>
</div>
</div>
</body>
</html>
