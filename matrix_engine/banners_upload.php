<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); 

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
	
	$session = $_GET['Session'];

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

	$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != '') ? $_REQUEST['action'] : '' ;
	$banner_pos = (isset($_REQUEST['pos']) && $_REQUEST['pos'] != '') ? $_REQUEST['pos'] : '' ;

	// Insert banner details into table then redirect to banner page
	
	$host = 'localhost';
	$user = 'root';
	$pass = '1ctsql';
	$db = 'conel';

	$link = mysql_connect($host, $user, $pass) or die("Can not connect." . mysql_error());
	mysql_select_db($db) or die("Can not connect.");
	
	// This should be run after every 'delete'.
	function updateOrder() {
		$query = "SELECT * FROM webmatrix_banners ORDER BY position ASC";
		if ($result = mysql_query($query)) {
			$pos = 1;
			while ($row = mysql_fetch_assoc($result)) {
				$update_query = "UPDATE webmatrix_banners SET position = $pos WHERE id = ".$row['id'];	
				if (!$updated = mysql_query($update_query)) {
					echo 'Banner order update FAILED!';
					return FALSE;
					exit;
				}
				$pos++;
			}
			return TRUE;
		} else {
			// No banners exist, let's return true
			return TRUE;
		}
	}

	function move($banner_pos='', $swap_pos='') {
		if ($banner_pos != '' && $swap_pos != '') {
			// get id numbers of banners which need to be swapped
			$order_pos = ($swap_pos > $banner_pos) ? 'ASC' : 'DESC';
			$query = "SELECT id FROM webmatrix_banners WHERE position IN ($swap_pos, $banner_pos) ORDER BY position $order_pos";
			$result = mysql_query($query);
			if (mysql_num_rows($result) > 0) {
				while ($row = mysql_fetch_assoc($result)) {
					$ids[] = $row['id'];
				}
			}
			$i = 0;
			$error = FALSE;
			// what is the current date and time?
			$date_modified = date('Y-m-d H:i:s'); // "2009-06-21 14:34:04": MySQL timestamp format

			foreach ($ids as $id) {
				if ($i == 0) {
					$query = "UPDATE webmatrix_banners SET position = '$swap_pos', date_modified = '$date_modified' WHERE id = $id";
					$result = mysql_query($query);
					if (!$result) { $error = TRUE; $error_msg = "Could not update banner position: $query"; }
				} else if ($i == 1) {
					$query = "UPDATE webmatrix_banners SET position = '$banner_pos', date_modified = '$date_modified' WHERE id = $id";
					$result = mysql_query($query);
					if (!$result) { $error = TRUE; $error_msg = "Could not update banner position: $query"; }
				}
				$i++;
			}
			if ($error === FALSE) {
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}
	
	function clearHomepageCache() {
		// nkowald - 2011-03-23 - Added delete cache function
		// Cache files are formatted like $page_id.scp - Get page ids can be retrieved from the 'webmatrix_structure' table
		// If a cache file exists for the three home pages: delete them so changes appear straight away.
		$cache_dir = 'C:\Program Files\Apache Software Foundation\Apache2.2\htdocs\cache\\';
		$homepage_ids = array(172, 252, 255, 256);
		foreach ($homepage_ids as $id) {
			// If file exists: delete it!
			$filepath = $cache_dir . $id . '.scp';
			if (file_exists($filepath)) {
				$delete = unlink($filepath);
			}
		}
	}

	switch ($action) {
		case 'upload':

			if (isset($_POST['banner_link']) && $_POST['banner_link'] != '') {

				//Check that we have a file
				if((!empty($_FILES["banner_img"])) && ($_FILES['banner_img']['error'] == 0)) {

				  //Check if the file is JPEG image or GIF and its size is less than 500Kb
				  $filename = basename($_FILES['banner_img']['name']);
				  $valid_exts = array('jpg', 'png', 'gif');
				  $valid_mimes = array('image/jpeg', 'image/png', 'image/gif', 'image/pjpeg');
				  $ext = substr($filename, strrpos($filename, '.') + 1);

				  if ((in_array($ext, $valid_exts)) && (in_array($_FILES["banner_img"]["type"], $valid_mimes)) && ($_FILES["banner_img"]["size"] < 500000)) {
					  //Determine the path to which we want to save this file
					  $newname = MATRIX_BASEDIR.'layout\img\banners\\'.$filename;
					  //Check if the file with the same name is already exists on the server
					  if (!file_exists($newname)) {
						//Attempt to move the uploaded file to it's new place
						if ((move_uploaded_file($_FILES['banner_img']['tmp_name'], $newname))) {
							// echo "It's done! The file has been saved as: ".$newname;
							// Banner successfully updated!

							$banner_link = $_POST['banner_link'];
							$position = $_POST['position'];
							$active = 1;
							$banner_url = '/layout/img/banners/'.$filename;
							$author = $_SESSION['wm']['username'];
							// what is the current date and time?
							$date_created = date('Y-m-d H:i:s'); // "2009-06-21 14:34:04": MySQL timestamp format


							$query = "
								INSERT INTO webmatrix_banners 
								(position, link, img_url, active, author, date_created) VALUES 
								('$position', '$banner_link', '$banner_url', '$active', '$author', '$date_created')";

							$result = mysql_query($query);
							if ($result) {
								clearHomepageCache();
								updateOrder();
								$redirect = SITE_ROOT . "matrix_engine/banners.php?Session=$session";
								header("Location: $redirect");
								exit;
							}

						} else {
						   echo "Error: A problem occurred during file upload!";
						}
					  } else {
						 echo "Error: File ".$_FILES["banner_img"]["name"]." already exists";
					  }
				  } else {
					 echo "Error: Only .jpg, .png, .gif images under 500Kb are accepted for upload";
				  }

				} else {
				 echo "Error: No file uploaded";
				}
				
			}
			break;

		case 'moveup':
			$move_to = $banner_pos - 1;
			$result = move($banner_pos, $move_to);
			if ($result) {
				clearHomepageCache();
				header('Location: banners.php?Session='.$session.'');
				exit;
			} else {
				echo 'Error moving banner up';
				exit;
			}
			break;

		case 'movedown':
			$move_to = $banner_pos + 1;
			$result = move($banner_pos, $move_to);
			if ($result) {
				clearHomepageCache();
				header('Location: banners.php?Session='.$session.'');
				exit;
			} else {
				echo 'Error moving banner down';
				exit;
			}
			break;

		case 'delete':
			if ($banner_pos != '') {
				// get image filename and idnumber of banner to delete
				$query = "SELECT id, img_url FROM webmatrix_banners WHERE position = $banner_pos";
				$result = mysql_query($query);
				$banner_id = '';
				$img_url = '';
				if ($result) {
					while($row = mysql_fetch_assoc($result)) {
						$banner_id = $row['id'];	
						$img_url = $row['img_url'];	
					}
				}
				if ($banner_id != '' && $img_url != '') {

					// Delete the banner from the table
					$query = "DELETE FROM webmatrix_banners WHERE id = $banner_id";
					$result = mysql_query($query);

					if ($result) {
						// Delete the file from banners directory - to save space and prevent 'duplicate' image errors
						$newname = str_replace('/layout/img/banners/', '', $img_url);
						$filepath = MATRIX_BASEDIR .'layout\img\banners\\' . $newname;
						//Check if the file with the same name is already exists on the server
						if (file_exists($filepath)) {
							$delete = unlink($filepath);
							if ($delete) {
								// Before redirect, update order of banners
								$order_update = updateOrder();
								if (!$order_update) {
									echo 'Could not update banner order!';
									exit;
								}
								// Successfully deleted! - return to banners page
								clearHomepageCache();
								$redirect = SITE_ROOT . "matrix_engine/banners.php?Session=$session";
								header("Location: $redirect");
								exit;
							} else {
								// Error deleting, not sure what to do here, how about NOTHING!
							}
						}
					} else {
						echo "Could not delete $banner_id";
						exit;
					}

				} else {
					echo 'Banner not found!';
					exit;
				}
			}
			break;

		case 'disable':
			if ($banner_pos != '') {
				$date_modified = date('Y-m-d H:i:s'); // "2009-06-21 14:34:04": MySQL timestamp format
				//$query = "UPDATE webmatrix_banners SET active=0, position=100, date_modified = '$date_modified' WHERE position = $banner_pos";
				$query = "UPDATE webmatrix_banners SET active=0, date_modified = '$date_modified' WHERE position = $banner_pos";
				if ($result = mysql_query($query)) {
					$order_update = updateOrder();
					if (!$order_update) {
						echo 'Could not update banner order!';
						exit;
					}
					// Successfully disabled and re-ordered, redirect to home
					clearHomepageCache();
					$redirect = SITE_ROOT . "matrix_engine/banners.php?Session=$session";
					header("Location: $redirect");
					exit;
				} else {
					echo "Could not disable banner $banner_pos";
					exit;
				}
			}
			break;

		case 'enable':
			if ($banner_pos != '') {
				$date_modified = date('Y-m-d H:i:s'); // "2009-06-21 14:34:04": MySQL timestamp format
				//$query = "UPDATE webmatrix_banners SET active=1, position=100, date_modified = '$date_modified' WHERE position = $banner_pos";
				$query = "UPDATE webmatrix_banners SET active=1, date_modified = '$date_modified' WHERE position = $banner_pos";
				if ($result = mysql_query($query)) {
					$order_update = updateOrder();
					if (!$order_update) {
						echo 'Could not update banner order!';
						exit;
					}
					// Successfully disabled and re-ordered, redirect to home
					clearHomepageCache();
					$redirect = SITE_ROOT . "matrix_engine/banners.php?Session=$session";
					header("Location: $redirect");
					exit;
				} else {
					echo "Could not enable banner $banner_pos";
					exit;
				}
			}
			break;

		case 'update':
			$id = (isset($_REQUEST['id']) && $_REQUEST['id'] != '') ? $_REQUEST['id'] : '';
			$link = (isset($_REQUEST['banner_link']) && $_REQUEST['banner_link'] != '') ? $_REQUEST['banner_link'] : '';
			$new_banner = ((!empty($_FILES["new_banner_img"])) && ($_FILES['new_banner_img']['error'] == 0)) ? $_FILES['new_banner_img'] : '';
			// If updating banner, delete old banner and then upload new banner
			if ($new_banner != '') {
				// delete old banner	
				$query = "SELECT img_url FROM webmatrix_banners WHERE id = $id";
				if ($result = mysql_query($query)) {
					while ($row = mysql_fetch_assoc($result)) {
						$img_url = $row['img_url'];
					}
					// Now we have image url: delete it!
					$newname = str_replace('/layout/img/banners/', '', $img_url);
					$filepath = MATRIX_BASEDIR .'layout\img\banners\\' . $newname;
					//Check if the file with the same name is already exists on the server
					if (file_exists($filepath)) {
						$delete = unlink($filepath);
						if ($delete) {
						  // Deleted successfully, now upload new banner
						  //Check if the file is JPEG image or GIF and its size is less than 500Kb
						  $filename = basename($_FILES['new_banner_img']['name']);
						  $valid_exts = array('jpg', 'png', 'gif');
						  $valid_mimes = array('image/jpeg', 'image/png', 'image/gif', 'image/pjpeg');
						  $ext = substr($filename, strrpos($filename, '.') + 1);

						  if ((in_array($ext, $valid_exts)) && (in_array($_FILES["new_banner_img"]["type"], $valid_mimes)) && ($_FILES["new_banner_img"]["size"] < 500000)) {
							  //Determine the path to which we want to save this file
							  $newname = MATRIX_BASEDIR.'layout\img\banners\\'.$filename;
							  //Check if the file with the same name is already exists on the server
							  if (!file_exists($newname)) {
								//Attempt to move the uploaded file to it's new place
								if ((move_uploaded_file($_FILES['new_banner_img']['tmp_name'], $newname))) {
									// echo "It's done! The file has been saved as: ".$newname;
									// Banner successfully updated!
									// Now finally, update the database record with new details
									$date_modified = date('Y-m-d H:i:s'); // "2009-06-21 14:34:04": MySQL timestamp format
									$query = "UPDATE webmatrix_banners SET link='$link', img_url='/layout/img/banners/$filename', date_modified='$date_modified' WHERE id = $id";
									if ($success = mysql_query($query)) {
										// Woo hoo! everything works : redirect to home
										clearHomepageCache();
										$redirect = SITE_ROOT . "matrix_engine/banners.php?Session=$session";
										header("Location: $redirect");
										exit;
									} else {
										echo 'banner update failed!';
									}
								} else {
									echo 'Error uploading new banner image';
								}
							  } else {
								 echo "Error: File ".$_FILES["new_banner_img"]["name"]." already exists";
							  }

						  } else {
							echo "Error: Only .jpg, .png, .gif images under 500Kb are accepted for upload";
						  }
						}
					}
				}
			} else {
				// NOT updating banner so just update link and date modified
				$date_modified = date('Y-m-d H:i:s'); // "2009-06-21 14:34:04": MySQL timestamp format
				$query = "UPDATE webmatrix_banners SET link='$link', date_modified='$date_modified' WHERE id = $id";
				if ($success = mysql_query($query)) {
					// Woo hoo! everything works : redirect to home
					clearHomepageCache();
					$redirect = SITE_ROOT . "matrix_engine/banners.php?Session=$session";
					header("Location: $redirect");
					exit;
				} else {
					echo 'Could not update banner';
				}
			}
			break;

	}

?>
