function isValidEmailAddress(emailAddress) {  
	var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
	return pattern.test(emailAddress);  
}
$(document).ready(function() {

	$("#conel_form").submit(function(e) {

		
		// check if first name is blank
		var name = $("#aat_firstname").val();
		if (name == '') {
			alert('First Name is a required field');
			$("#aat_firstname").focus();
			return false;
		}
		
		// check if surname is blank
		var name = $("#aat_surname").val();
		if (name == '') {
			alert('Surname is a required field');
			$("#aat_surname").focus();
			return false;
		}
		
		// check if date of birth is blank
		/*
		var dob = $("#aat_date_of_birth").val();
		
		if (dob != '') {
			if (!isValidDOB(dob)) {
				alert("Invalid date of birth\n\n- Needs to entered be in dd/mm/yyyy format");
				$("#aat_date_of_birth").focus();
				return false;
			}
		}
		*/
		 
		// check if email is blank
		var email = $("#aat_email").val();
		if (email == '') {
			alert('Email is a required field');
			$("#aat_email").focus();
			return false;
		}

		if (!isValidEmailAddress(email)) {
			alert('Invalid email address');
			$("#aat_email").focus();
			return false;
		}

		// check if second email matches the first email address
		var email_confirm = $("#aat_email_confirm").val();
		if (email_confirm == '') {
			alert('Please confirm your email address');
			$("#aat_email_confirm").focus();
			return false;
		}
		
		if (email != email_confirm) {
			alert('Email addresses do not match');
			return false;
		}
		
		
		// check if address 1 is blank
		var address_1 = $("#aat_address_line_1").val();
		if (address_1 == '') {
			alert('Address Line 1 is a required field');
			$("#aat_address_line_1").focus();
			return false;
		}
		
		// check if address 2 is blank
		var address_2 = $("#aat_address_line_2").val();
		if (address_2 == '') {
			alert('Address Line 2 is a required field');
			$("#aat_address_line_2").focus();
			return false;
		}
		
		// check if address 3 is blank
		var address_3 = $("#aat_address_line_3").val();
		if (address_3 == '') {
			alert('Address Line 3 is a required field');
			$("#aat_address_line_3").focus();
			return false;
		}
		
		// check if address 1 is blank
		var postcode = $("#aat_postcode").val();
		if (postcode == '') {
			alert('Postcode is a required field');
			$("#aat_postcode").focus();
			return false;
		}
		
		// check if borough/county is blank
		var county = $("#aat_borough_county").val();
		if (county == '') {
			alert('Borough/County is a required field');
			$("#aat_borough_county").focus();
			return false;
		}
		
		return true;

	});

});