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

	$session = $_GET['Session'];
	$host = 'localhost';
	$user = 'root';
	$pass = '1ctsql';
	$db = 'conel';

	$link = mysql_connect($host, $user, $pass) or die("Can not connect." . mysql_error());
	mysql_select_db($db) or die("Can not connect.");

	// Get a list of tables we should be able to export data from
	$query = "SELECT id, position, link, img_url, active, author FROM webmatrix_banners ORDER BY position ASC";

	$result = mysql_query($query);
	$banners = array();
	$banners_exist = FALSE;
	$position = 1;
	$active_banners = 0;
	if (mysql_num_rows($result) > 0) {
		$banners_exist = TRUE;
		$c = 0;
		while ($row = mysql_fetch_assoc($result)) {
			$banners[$c]['id']			= $row['id'];
			$banners[$c]['position']	= $row['position'];
			$banners[$c]['link']		= $row['link'];
			$banners[$c]['img_url']		= $row['img_url'];
			$banners[$c]['author']		= $row['author'];
			$banners[$c]['active']		= $row['active'];
			$position					= $row['position'];
			$c++;
			if ($row['active']) $active_banners++;
		}
		$position++;
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-AU" xml:lang="en-AU">
<head>
<title>Banners</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../layout/css/banners.css" rel="stylesheet" type="text/css" media="screen" />
<link href="../layout/colorbox/colorbox.css" rel="stylesheet" type="text/css" media="screen" />
<script src="../layout/js/jquery-1.4.4.min.js"></script>
<script src="../layout/colorbox/jquery.colorbox-min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	//Examples of how to assign the ColorBox event to elements
	$(".add_banner").colorbox({
		onOpen:function() { $('#banner_add_form').fadeIn(); },
		onCleanup:function() { $('#banner_add_form').hide(); },
			width:"600px", 
			inline:true, 
			href:"#banner_add_form"
	});
	$(".delete").click(function(event) {
		event.preventDefault();
		var clicked_link = $(this).attr('href');
		var clicked_no = $(this).find('span');
		var banner_no = clicked_no.attr('class');
		var answer = confirm('Are you sure you want to delete banner #' + banner_no + '?');
		if (answer) {
			// delete
			window.location.href = clicked_link;
			return false;
		}
	});
	$('.edit').colorbox();

	/*
	$(".example9").colorbox({
		onOpen:function(){ alert('onOpen: colorbox is about to open'); },
		onLoad:function(){ alert('onLoad: colorbox has started to load the targeted content'); },
		onComplete:function(){ alert('onComplete: colorbox has displayed the loaded content'); },
		onCleanup:function(){ alert('onCleanup: colorbox has begun the close process'); },
		onClosed:function(){ alert('onClosed: colorbox has completely closed'); }
	});
	*/
});
</script>
</head>
<body> 
<div id="holder">
	<h1>Banners</h1>
	<a href="#" class="add_banner"><span class="add">Add Banner</span></a>

<?php
	if (!$banners_exist) {
		echo '<p>No banners exist</p>';
	}
?>

<div id="banners_holder">

<?php
	if (is_array($banners) && count($banners) > 0) {
		$c = 1;
		foreach ($banners as $banner) {
			
			$active = $banner['active'];
			$active_class = ($banner['active'] == 0) ? ' inactive' : '';
			echo '	
			<!-- banner '.$c.' -->
			<div class="banner'.$active_class.'">
			<div class="position">
				<div class="moveup">';
			if ($c > 1 && $active == 1) {
				echo '<a href="banners_upload.php?Session='.$session.'&amp;action=moveup&amp;pos='.$c.'" title="Move Up"><img src="../layout/img/icon-moveup.png" /></a>';
			}
			echo '</div>
				<div class="count">'.$c.'</div>
				<div class="movedown">';
			if ($c != $active_banners && $active == 1) {
				echo '<a href="banners_upload.php?Session='.$session.'&amp;action=movedown&amp;pos='.$c.'" title="Move Down"><img src="../layout/img/icon-movedown.png" /></a>';
			}
			echo '</div>
			</div>
				<div class="banner_details">
					<img src="'.$banner['img_url'].'" width="700" height="143" alt="" /><br />
					<div class="actions">
						<a href="banner_edit.php?Session='.$session.'&amp;pos='.$c.'" class="edit"><span class="'.$c.'">Edit</span></a>
						<a href="banners_upload.php?Session='.$session.'&amp;action=delete&amp;pos='.$c.'" class="delete"><span class="'.$c.'">Delete</span></a>
					';	
					if ($active) {
						echo '<a href="banners_upload.php?Session='.$session.'&amp;action=disable&amp;pos='.$c.'" class="disable"><span class="'.$c.'">Disable</span></a>';
					} else {
						echo '<a href="banners_upload.php?Session='.$session.'&amp;action=enable&amp;pos='.$c.'" class="enable"><span class="'.$c.'">Enable</span></a>';
					}
					// Reduce size of banner link if over a certain amount of characters
					$banner_link_title = $banner['link'];
					$max_chars = 53;
					if (strlen($banner_link_title) > $max_chars) {
						$banner_link_title = substr($banner_link_title, 0, $max_chars) . '...';
					}
					echo '</div>
					<div class="link">
						<strong>Link:</strong> <a href="'.$banner['link'].'" target="_blank" title="'.$banner['link'].'">'.$banner_link_title.'</a>
					</div>
					<br class="clear_both" />
				</div>
			</div>
			<!-- //banner '.$c.' -->
			';

			$c++;
		}
	}
?>
</div>

<!-- add banner -->
<div id="banner_add_form">
<h3>Add Banner</h3>
	<form enctype="multipart/form-data" action="banners_upload.php?Session=<?php echo $session; ?>" method="POST">
		<table>
			<tr>
				<td><label for="upload_link">Link:</label></td>
				<td><input type="text" name="banner_link" class="field" id="upload_link" /></td>
			</tr>
			<tr>
				<td><label for="upload_banner">Banner:</label></td>
				<td>
					<input type="file" name="banner_img" id="upload_banner" />
					<input type="hidden" name="MAX_FILE_SIZE" value="500000" />
					<input type="hidden" name="action" value="upload" />
					<input type="hidden" name="Session" value="<?php echo $session; ?>" />
				</td>
			</tr>
			<tr>
				<td colspan="2">
				<input type="hidden" name="position" value="0" />
					<br /><input type="submit" value="Add Banner" />
				</td>
			</tr>
		</table>
	</form>
</div>
<!-- //add banner -->

</div>
</body>
</html>
