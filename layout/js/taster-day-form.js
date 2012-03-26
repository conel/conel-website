$(document).ready(function() {

	$("#open_day_form").submit(function(e) {

		// check if name is blank
		var name = $("#odf_name").val();
		if (name == '') {
			alert('Name is a required field');
			$("#odf_name").focus();
			return false;
		}
		 
		// check if email is blank
		var email = $("#odf_email").val();
		if (email == '') {
			alert('Email is a required field');
			$("#odf_email").focus();
			return false;
		}

		// check if second email matches the first email address
		var email_confirm = $("#odf_email_confirm").val();
		if (email_confirm == '') {
			alert('Please confirm your email address');
			$("#odf_email_confirm").focus();
			return false;
		}
		
		if (email != email_confirm) {
			alert('Email addresses do not match');
			return false;
		}
		
		return true;

	});

});