<?php
	session_start();
	if (isset($_SESSION['caf'])) {
		unset($_SESSION['caf']);
	}
	header('location: /course-application/');
	exit;
?>
