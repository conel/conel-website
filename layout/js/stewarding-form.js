function isValidEmailAddress(emailAddress) {  
	var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
	return pattern.test(emailAddress);  
}
function isValidDOB(dob) {
	var dob_regexp = /^([0-9]){2}(\/){1}([0-9]){2}(\/)([0-9]){4}$/;
	return dob_regexp.test(dob);
}

$(document).ready(function() {

	$("#stewarding").submit(function(e) {

		// check if name is blank
		var name = $("#sf_name").val();
		if (name == '') {
			alert('Name is a required field');
			$("#sf_name").focus();
			return false;
		}
		
		// check if date of birth is blank
		var dob = $("#sf_date_of_birth").val();
		
		// check if dob is blank
		if (dob == '') {
			alert('Date of birth is a required field');
			$("#sf_date_of_birth").focus();
			return false;
		}
		
		if (!isValidDOB(dob)) {
			alert("Invalid date of birth\n\n- Needs to entered be in dd/mm/yyyy format");
			$("#sf_date_of_birth").focus();
			return false;
		}

		// check if email is valid
		var email = $("#sf_email").val();
		
		if (!isValidEmailAddress(email)) {
			alert('Invalid email address');
			$("#sf_email").focus();
			return false;
		}
		
		// check if email is blank
		
		if (email == '') {
			alert('Email is a required field');
			$("#sf_email").focus();
			return false;
		}

		// check if second email matches the first email address
		var email_confirm = $("#sf_email_confirm").val();
		if (email_confirm == '') {
			alert('Please confirm your email address');
			$("#sf_email_confirm").focus();
			return false;
		}
		
		if (email != email_confirm) {
			alert('Email addresses do not match');
			return false;
		}
		
		var telephone = $("#sf_telephone").val();
		if (telephone == '') {
			alert('Telephone is a required field');
			$("#sf_telephone").focus();
			return false;
		}
		
		var address = $("#sf_address").val();
		if (address == '') {
			alert('Address is a required field');
			$("#sf_address").focus();
			return false;
		}
		
		var postcode = $("#sf_postcode").val();
		if (postcode == '') {
			alert('Postcode is a required field');
			$("#sf_postcode").focus();
			return false;
		}
		
		return true;

	});

});