$(document).ready(function() {

	$("#open_day_form").submit(function(e) {
		 
		// check if email is blank
		var email = $("#ept_email_address").val();
		if (email == '') {
			alert('Email is a required field');
			$("#ept_email_address").focus();
			return false;
		}
		
		return true;

	});

});