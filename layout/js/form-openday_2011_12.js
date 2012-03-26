function isValidEmailAddress(emailAddress) {  
	var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
	return pattern.test(emailAddress);  
}
$(document).ready(function() {

	$("#conel_form").submit(function(e) {

		// I would like to register my attendance at check
		var register_attendance = $("#register_my_attendance_for").attr('checked');
		
		if (register_attendance == false) {
			$("#register_my_attendance_for").focus();
			alert('"I would like to register my attendance at" is a required field');
			return false;
		}
		
		// check if first name is blank
		var name = $("#odf_firstname").val();
		if (name == '') {
			alert('First Name is a required field');
			$("#odf_firstname").focus();
			return false;
		}
		
		// check if surname is blank
		var name = $("#odf_surname").val();
		if (name == '') {
			alert('Surname is a required field');
			$("#odf_surname").focus();
			return false;
		}
		
		// check if date of birth is blank
		/*
		var dob = $("#odf_date_of_birth").val();
		
		if (dob != '') {
			if (!isValidDOB(dob)) {
				alert("Invalid date of birth\n\n- Needs to entered be in dd/mm/yyyy format");
				$("#odf_date_of_birth").focus();
				return false;
			}
		}
		*/
		 
		// check if email is blank
		var email = $("#odf_email").val();
		if (email == '') {
			alert('Email is a required field');
			$("#odf_email").focus();
			return false;
		}

		if (!isValidEmailAddress(email)) {
			alert('Invalid email address');
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