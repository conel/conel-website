function isValidEmailAddress(emailAddress) {  
	var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
	return pattern.test(emailAddress);  
}
$(document).ready(function() {

	$("#conel_form").submit(function(e) {
		
		// check if first name is blank
		var name = $("#alw_firstname").val();
		if (name == '') {
			alert('First Name is a required field');
			$("#alw_firstname").focus();
			return false;
		}
		
		// check if surname is blank
		var name = $("#alw_surname").val();
		if (name == '') {
			alert('Surname is a required field');
			$("#alw_surname").focus();
			return false;
		}
		
		// check if date of birth is blank
		/*
		var dob = $("#alw_date_of_birth").val();
		if (dob != '') {
			if (!isValidDOB(dob)) {
				alert("Invalid date of birth\n\n- Needs to entered be in dd/mm/yyyy format");
				$("#alw_date_of_birth").focus();
				return false;
			}
		}
		*/
		 
		// check if email is blank
		var email = $("#alw_email").val();
		if (email == '') {
			alert('Email is a required field');
			$("#alw_email").focus();
			return false;
		}

		if (!isValidEmailAddress(email)) {
			alert('Invalid email address');
			$("#alw_email").focus();
			return false;
		}

		// check if second email matches the first email address
		var email_confirm = $("#alw_email_confirm").val();
		if (email_confirm == '') {
			alert('Please confirm your email address');
			$("#alw_email_confirm").focus();
			return false;
		}
		
		if (email != email_confirm) {
			alert('Email addresses do not match');
			return false;
		}
		
		return true;

	});

});