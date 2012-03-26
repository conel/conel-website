<?php

	#################################
	#     Required Include Files    #
	#################################

	require("config.php");
	if(REWRITE_GLOBALS == "ON") { include("functions/register_globals.php"); }
	require(CLASSES_DIR."template.php"); // include the template class
	require(CLASSES_DIR."db_mysql.php"); // include the db class
	require(CLASSES_DIR."container.php");
	require(CLASSES_DIR."session.php");
	require(CLASSES_DIR."authentication.php");
	require(CLASSES_DIR."page.php");
	require(CLASSES_DIR."class_mailer.php");
	require(CLASSES_DIR."SubjectAdmin.class.php");

	include("functions/access.php");
	include("functions/ui_dialogs.php");
	include("interface/lang/".$Auth->auth["language"].".php");

	page_open(array("session" => "session_object", "authenticate" => "Auth"));
	page_close();

	// check if we have the correct access rights
	$access_rights = _getUserAccessRights($Auth->auth['user_id']);

	if(!isset($access_rights['pages']['workspace'])) {
		// display error message
		ui_output_error("<b>Pages</b><br><br> ".LANG_NoAccessRights);
		exit;
	}

	$session = $_GET['Session'];
	$sub_admin = new SubjectAdmin();
	$subject_list = $sub_admin->getSubjects(); // get subjects
	$action = '';

	// Handle Actions
	if (isset($_POST) || isset($_GET)) {

		if (count($_POST['action'])) {
			$action = (isset($_POST['action']) && $_POST['action'] != '') ? $_POST['action'] : '';
		}
		if (isset($_GET['action'])) {
			$action = (isset($_GET['action']) && $_GET['action'] != '') ? $_GET['action'] : '';
		}

		if ($action != '') {

			switch($action) {

				// Assign correct pages to subjects
				case 'assign':
					$no_assignments = ($_POST['matches_count'] != '') ? $_POST['matches_count'] : 0;
					for ($i=1; $i <= $no_assignments; $i++) {
						// For each assignement run update
						$old_name = "old_name$i";
						$new_name = ($_POST["other_new_name$i"] != '') ? "other_new_name$i" : "new_name$i";
						$old_title = ($_POST[$old_name] != '') ? $_POST[$old_name] : '';
						$new_title = ($_POST[$new_name] != '') ? $_POST[$new_name] : '';

						if ($sub_admin->updatePageInfo($old_title, $new_title)) {
							$message = "Pages successfully updated";
						} else {
							$message = "Page updates failed";
						}
					}

					// Now that pages "exist" create pages for all non existent pages
					$subjects = $sub_admin->getSubjects();
					$pages_needed = array();
					foreach ($subjects as $subject) {
						if ($subject['Exists'] === FALSE) {
							$pages_needed[] = $subject['Description'];
						}
					}

					// If found pages to create
					if (count($pages_needed) > 0) {
						foreach ($pages_needed as $page_title) {
							if ($page_title != 'Not Applicable') {
								$sub_admin->createPage($page_title);
							}
						}
					}

					// Lastly, update menu order to be alphabetical
					$updated = ($sub_admin->updateMenuOrder());
					$message = (!$updated) ? 'Unable to update menu order' : '';

					header('Location:/matrix_engine/subject_editor.php?message='.$message.'&Session='.$session.'');
					exit;
				break;

				case 'delete_invalid_codes':
					$deleted = ($sub_admin->deleteInvalidCodes());
					$message = ($deleted == FALSE) ? 'Unable to delete invalid codes' : 'Successfully deleted invalid codes';

					header('Location:/matrix_engine/subject_editor.php?message='.$message.'&Session='.$session.'');
					exit;
				break;


				default:
					header('Location:/matrix_engine/subject_editor.php?Session='.$session.'');
					exit;

			} // switch

		} // if $action exists

	} else {
		// If page accessed and URL does not contain $_POST or $_GET variables, redirect to subject admin page
	    header('Location:/matrix_engine/subject_editor.php?Session='.$session.'');
	    exit;
	}

?>
