function isValidEmailAddress(emailAddress) {  
	var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
	return pattern.test(emailAddress);  
}

$(document).ready(function() {

	$("#conel_form").submit(function(e) {

		// Check they've chosen which course guide they want to receive
		if ($("#course_guide_selection input[type=checkbox]:checked").length == 0 ) {
			alert("'Which Course Guides would you like us to send you?' is a required field");
			$("#cg_ypcg_enfield").focus();
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
		
		/*
		// check if date of birth is blank
		var dob_day = $("#odf_dob_day").val();
		var dob_month = $("#odf_dob_month").val();
		var dob_year = $("#odf_dob_year").val();
		
		// check if dob is blank
		if (dob_day == '') {
			alert('Day of birth is a required field');
			$("#odf_dob_day").focus();
			return false;
		}
		
		if (dob_month == '') {
			alert('Month of birth is a required field');
			$("#odf_dob_day").focus();
			return false;
		}
		
		if (dob_year == '') {
			alert('Year of birth is a required field');
			$("#odf_dob_day").focus();
			return false;
		}
		*/
		 
		// check if email is valid
		var email = $("#odf_email").val();
		
		// check if email is blank
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
			$("#odf_email_confirm").focus();
			return false;
		}
		
		return true;

	});

});