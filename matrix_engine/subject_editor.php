<?php

	#################################
	#     Required Include Files    #
	#################################

	require("config.php");
	if (REWRITE_GLOBALS == "ON") { include("functions/register_globals.php"); }
	require(CLASSES_DIR."template.php"); // include the template class
	require(CLASSES_DIR."db_mysql.php"); // include the db class
	require(CLASSES_DIR."container.php");
	require(CLASSES_DIR."session.php");
	require(CLASSES_DIR."authentication.php");
	require(CLASSES_DIR."page.php");
	require(CLASSES_DIR."class_mailer.php");
	include("functions/access.php");
	include("functions/ui_dialogs.php");

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

	require(CLASSES_DIR."SubjectAdmin.class.php");
	$sub_admin = new SubjectAdmin();
	$session = $_GET['Session'];
	$sub_admin->sendSTCNotifEmail();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="expires" content="0" />
	<meta http-equiv="pragma" content="no-cache" />
	<title>Subject Admin</title>
	<link href="interface/layout.css" rel="stylesheet" type="text/css" />
	<link href="interface/subject_admin.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="interface/script/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="interface/script/functions.js"></script>
</head>
<body>
	<h1>Subjects Administration</h1>
<?php
	if ((isset($_POST) || isset($_GET))	&& (isset($_GET['message']) && $_GET['message'] != "")) {
		echo '<div id="notification">'. $_GET['message'] .'</div>';
	}

	// Look for invalid subject topic codes - called here so it displays in page body
	$sub_admin->showInvalidCodes();
	$invalid_codes_html = $sub_admin->showNotAssignedCourses();
	if ($invalid_codes_html != '') echo $invalid_codes_html;
	$subject_list = $sub_admin->getSubjects(); // get subjects

	if ($sub_admin->old_page_names_found === TRUE) {
?>
	<div id="sleuth_holder">
		<div id="sleuth">
			<img src="interface/images/search-detective.gif" id="sleuth_cartoon" alt="Subject Detective iz in ur MySQL, sleuthing ur tabelz" width="98" height="180" align="left" />
			<strong>Subject Sleuth:</strong><br />I suspect the following subject pages are using old page names
		</div>
		<div id="assignments">
			<form action="/matrix_engine/subject_editor_do.php?Session=<?php echo $session; ?>" method="post">
			<table id="sleuth_results">
				<thead>
				<tr>
					<th align="left">Page Name</th>
					<th align="left" width="290">Rename To</th>
					<th align="left">Correct Match?</th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td></td>
					<td></td>
					<td></td>
				</tr>
<?php
	// Build options list
	$options = '';
	foreach ($subject_list as $subject) {
		if ($subject['Exists'] === FALSE) {
			$options.= "\t<option value=\"".$subject['Description']."\">".$subject['Description']."</option>\n";
		}
	}

	$i = 1;
	foreach ($subject_list as $subject) {
		if ($subject['Exists'] === FALSE) {
			$match = $sub_admin->findSimilarPage($subject['Description']);
			if ($match != '') {
				echo "<tr>\n";
				$match = $sub_admin->findSimilarPage($subject['Description']);
				$page_url = $sub_admin->getPageUrl($match);
				$match_html = ($page_url != '') ? "<a href=\"$page_url\" target=\"blank\">$match</a>" : $match;
				echo "<td>$match_html</td>\n";
				echo "<td><span id=\"match$i\">".$subject['Description']."</span>\n";
				echo "<select name=\"other_new_name$i\" id=\"other$i\" style=\"display:none;\">\n";
				echo "\t<option value=\"\">-- Choose Correct Name --</option>\n";
				echo $options;
				echo "</select>\n";
				echo "</td>\n";
				echo "<td>
					<input type=\"radio\" name=\"option$i\" id=\"y_option$i\" checked=\"checked\" value=\"y\" onchange=\"displayOptions($i,this.value);\" />
					<label for=\"y_option$i\">Yes</label>

					<input type=\"radio\" name=\"option$i\" id=\"n_option$i\" value=\"n\" onchange=\"displayOptions($i,this.value);\" />
					<label for=\"n_option$i\">No</label>\n";
				echo "<input type=\"hidden\" name=\"old_name$i\" value=\"$match\" />\n";
				echo "<input type=\"hidden\" name=\"new_name$i\" value=\"".$subject['Description']."\" />\n";
				echo "</td>\n";
				echo "</tr>\n";
				$i++;
			} // if $match found
		}
	} // foreach
	$matches_count = $i - 1;
?>
				<tr>
					<td colspan="3" class="no_border">
					<input type="hidden" name="matches_count" value="<?php echo $matches_count; ?>" />
					<input type="hidden" name="action" value="assign" />
					<input type="submit" value="Update Page Names" />
					</td>
				</tr>
				</tbody>
			</table>
			</form>
		</div>
		<br class="clear_both" />
	</div>
<?php
		} // show if old page names found
?>
	<div id="subjects">
		<table cellpadding="1" cellspacing="0">
			<thead>
				<tr>
					<th align="center"></th>
					<th align="left">Name</th>
					<th align="left">Topic Code</th>
					<th align="center">Status</th>
					<th align="center">Info</th>
				</tr>
			</thead>
			<tbody>
<?php
	foreach($subject_list as $subject) {

		$active_class = ($subject['Active'] === TRUE) ? "" : "inactive";
		$notif = (!$subject['Exists']) ? '<img src="interface/images/warning.png" height="22" width="22" alt="Page Does Not Exist" />' : '';
		$exists_class = ($subject['Exists']) ? "page_exists" : "page_not_exists";
		// Build stats Text
		$page_url = ($sub_admin->getPageURL($subject['Description']) != '') ? $sub_admin->getPageURL($subject['Description']) : '';
		if ($page_url != '') {
			$no_pages = ($subject['No_Pages'] > 0) ? '<a href="'.$page_url.'" target="_blank">'.$subject['No_Pages'] .' Page</a>, ' : "";
		} else {
			$no_pages = ($subject['No_Pages'] > 0) ? $subject['No_Pages'] . " Page, " : "";
		}
		$no_courses = ($subject['No_Courses'] != '') ? (($subject['No_Courses'] == 1) ? $subject['No_Courses'] . " Course, " : $subject['No_Courses'] . " Courses, ") : "";
		$no_news_events = ($subject['No_News_Events'] > 0) ? $subject['No_News_Events'] . " News &amp; Events" : "";
		$stats_text = '<span class="smaller">'.$no_pages.$no_courses.$no_news_events.'</span>';

		$exists_content = ($subject['Exists']) ? '<img src="interface/images/tick.png" height="22" width="22" alt="Page Exists" />' : '<img src="interface/images/no_tick.png" height="22" width="22" alt="Page Does Not Exists" />';
		$page_prob_called = $sub_admin->findSimilarPage($subject['Description']);
		$page_name_incorrect = ($page_prob_called != '') ? TRUE : FALSE;
		$info_text = ($page_name_incorrect) ? 'Old Page Name<br /><span class="smaller">('.$page_prob_called.')</span>' : 'Page Does Not Exist';
		$active_status = ($subject['Active'] === FALSE) ? 'Pagename and STC Description Differ' : ''; 
		$info_text = ($subject['Exists'] === FALSE) ? $info_text : $active_status;
		echo '<tr class="'.$exists_class.'">';
		echo '<td align="center" width="38">'.$notif.'</td>';
		echo '<td align="left" width="320"><strong>'.$subject['Description'].'</strong><br />'.$stats_text.'</td>';
		echo '<td align="left" width="110">'. $subject['ID'] .'</td>';
		echo '<td align="left" class="exists" width="60">'.$exists_content.'</td>';
		echo '<td align="center">'.$info_text.'</td>';
		echo '</tr>';

	}
?>
			</tbody>
		</table>
	</div>
</body>
</html>
