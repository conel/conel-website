function isValidEmailAddress(emailAddress) {  
	var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
	return pattern.test(emailAddress);  
}
$(document).ready(function() {

	$("#conel_form").submit(function(e) {

		// Check if delivery preference is chosen
		var delivery = $("#clife_pref").val();
		if (delivery == '') {
			alert('\'Would you like to receive a copy of College Life via email or post?\' is a required field');
			$("#clife_pref").focus();
			return false;
		}
		
		// check if first name is blank
		var name = $("#clife_firstname").val();
		if (name == '') {
			alert('First Name is a required field');
			$("#clife_firstname").focus();
			return false;
		}
		
		// check if surname is blank
		var name = $("#clife_surname").val();
		if (name == '') {
			alert('Surname is a required field');
			$("#clife_surname").focus();
			return false;
		}
		
		// check if date of birth is blank
		/*
		var dob = $("#clife_date_of_birth").val();
		
		if (dob != '') {
			if (!isValidDOB(dob)) {
				alert("Invalid date of birth\n\n- Needs to entered be in dd/mm/yyyy format");
				$("#clife_date_of_birth").focus();
				return false;
			}
		}
		*/
		 
		// check if email is blank
		var email = $("#clife_email").val();
		if (email == '') {
			alert('Email is a required field');
			$("#clife_email").focus();
			return false;
		}

		if (!isValidEmailAddress(email)) {
			alert('Invalid email address');
			$("#clife_email").focus();
			return false;
		}

		// check if second email matches the first email address
		var email_confirm = $("#clife_email_confirm").val();
		if (email_confirm == '') {
			alert('Please confirm your email address');
			$("#clife_email_confirm").focus();
			return false;
		}
		
		if (email != email_confirm) {
			alert('Email addresses do not match');
			return false;
		}
		
		
		return true;

	});

});
