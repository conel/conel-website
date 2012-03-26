function isValidEmailAddress(emailAddress) {  
	var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
	return pattern.test(emailAddress);  
}
$(document).ready(function() {

	// Anti-spam technique - populate are_you_a_robot with a value, value must be the same to be sent
	$("#standard_ayar").val('yes');

	$("#conel_form").submit(function(e) {
		
		// check if name is blank
		var name = $("#standard_name").val();
		if (name == '') {
			alert('Name is a required field');
			$("#standard_name").focus();
			return false;
		}
		
		// check if job title is blank
		var name = $("#standard_job_title").val();
		if (name == '') {
			alert('Job title is a required field');
			$("#standard_job_title").focus();
			return false;
		}
		
		// check if school is blank
		var name = $("#standard_school").val();
		if (name == '') {
			alert('School is a required field');
			$("#standard_school").focus();
			return false;
		}
		 
		// check if email is blank
		var email = $("#standard_email").val();
		if (email == '') {
			alert('Email is a required field');
			$("#standard_email").focus();
			return false;
		}

		if (!isValidEmailAddress(email)) {
			alert('Invalid email address');
			$("#standard_email").focus();
			return false;
		}

		// check if second email matches the first email address
		var email_confirm = $("#standard_email_confirm").val();
		if (email_confirm == '') {
			alert('Please confirm your email address');
			$("#standard_email_confirm").focus();
			return false;
		}
		
		if (email != email_confirm) {
			alert('Email addresses do not match');
			return false;
		}
		
		
		return true;

	});

});