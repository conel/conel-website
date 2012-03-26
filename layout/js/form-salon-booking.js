function isValidEmailAddress(emailAddress) {  
	var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
	return pattern.test(emailAddress);  
}

$(document).ready(function() {

	$("#conel_form").submit(function(e) {

		// check if name is blank
		var name = $("#salon_name").val();
		if (name == '') {
			alert('Name is a required field');
			$("#salon_name").focus();
			return false;
		}
		
		// check if contact number is blank
		var name = $("#salon_contact_number").val();
		if (name == '') {
			alert('\'Contact Number\' is a required field');
			$("#salon_contact_number").focus();
			return false;
		}
		
		// check if email is blank
		var email = $("#salon_email").val();
		if (email == '') {
			alert('Email is a required field');
			$("#salon_email").focus();
			return false;
		}

		if (!isValidEmailAddress(email)) {
			alert('Invalid email address');
			$("#salon_email").focus();
			return false;
		}

		// check if second email matches the first email address
		var email_confirm = $("#salon_email_confirm").val();
		if (email_confirm == '') {
			alert('Please confirm your email address');
			$("#salon_email_confirm").focus();
			return false;
		}
		
		if (email != email_confirm) {
			alert('Email addresses do not match');
			return false;
		}
		// check if centre is blank
		var centre = $("#salon_centre").val();
		if (centre == '') {
			alert('Centre is a required field');
			$("#salon_centre").focus();
			return false;
		}
		
		// check if preferred date and time is blank
		var centre = $("#salon_preferred_date_and_time").val();
		if (centre == '') {
			alert('\'Preferred Data and Time\' is a required field');
			$("#salon_preferred_date_and_time").focus();
			return false;
		}
		
		// check if centre is blank
		var centre = $("#salon_treatment").val();
		if (centre == '') {
			alert('Treatment is a required field');
			$("#salon_treatment").focus();
			return false;
		}
		
		return true;

	});

});
