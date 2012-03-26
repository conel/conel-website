<?php

	/**********************************************************************************************************************
	 *  Subject Topic Code Script - Daily Cron
	 *
	 *  Created by:      Nathan Kowald
	 *  Last Modified:   2009-10-02
	 *
	 *  Description:     This script is set up to run every morning, shortly after EBS course import.
	 *
	 *      This script runs through methods which:
	 *  	- Check for old subject topic codes and updates them with current codes
	 *  	- Courses assigned to subject topic codes that have no modern equivalent are set to 'N/A'
	 *
	 *  	Email Notification of courses assigned to 'N/A':
	 *  	- Checks for any courses assigned to 'N/A', then checks if ICT support has been emailed about them in the
	 *    	  last seven days, if it has been more than seven days: ICT Support is emailed these unassigned courses
	 *
	 ********************************************************************************************************************/

	require("config.php");
	require(CLASSES_DIR."SubjectAdmin.class.php");
	$sub_admin = new SubjectAdmin();
	$sub_admin->sendSTCNotifEmail(TRUE);

?>