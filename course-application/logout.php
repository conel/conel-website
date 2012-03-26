<?php
	session_start();
	if (isset($_SESSION['caf'])) {
		unset($_SESSION['caf']);
	}
	header('location: http://www.conel.ac.uk/course-application/');
	exit;
?>