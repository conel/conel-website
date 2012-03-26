function isValidEmailAddress(emailAddress) {  
	var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
	return pattern.test(emailAddress);  
}
$(document).ready(function() {

	$("#conel_form").submit(function(e) {
	
		if ($('#odjan11_tot1')) {
			if (
			$('#odjan11_tot1').attr('checked') == '' && $('#odjan11_tot2').attr('checked') == '' && $('#odjan11_tot3').attr('checked') == '' && $('#odjan11_tot4').attr('checked') == '' && 
			$('#odjan11_enf1').attr('checked') == '' && $('#odjan11_enf2').attr('checked') == '' && $('#odjan11_enf3').attr('checked') == '' && $('#odjan11_enf4').attr('checked') == ''
			) {
				alert("'Please register my place at the following Science Taster Sesssion' is a required field.\n\nPlease choose a session at Tottenham or Enfield Centre.");
				$("#odjan11_tot1").focus();
				return false;
			}
		}

		// I would like to register my attendance at check
		var open_enfield = $("#odmarch11_enf").attr('checked');
		var open_tottenham = $("#odmar16_tot").attr('checked');
		
		if (open_tottenham == false && open_enfield == false) {
			$("#odmarch11_enf").focus();
			alert("'I would like to register my attendance at' is a required field");
			return false;
		}
		
		// check if first name is blank
		var name = $("#odjan11_firstname").val();
		if (name == '') {
			alert('First Name is a required field');
			$("#odjan11_firstname").focus();
			return false;
		}
		
		// check if surname is blank
		var name = $("#odjan11_surname").val();
		if (name == '') {
			alert('Surname is a required field');
			$("#odjan11_surname").focus();
			return false;
		}
		
		// check if date of birth is blank
		/*
		var dob = $("#odjan11_date_of_birth").val();
		
		if (dob != '') {
			if (!isValidDOB(dob)) {
				alert("Invalid date of birth\n\n- Needs to entered be in dd/mm/yyyy format");
				$("#odjan11_date_of_birth").focus();
				return false;
			}
		}
		*/
		 
		// check if email is blank
		var email = $("#odjan11_email").val();
		if (email == '') {
			alert('Email is a required field');
			$("#odjan11_email").focus();
			return false;
		}

		if (!isValidEmailAddress(email)) {
			alert('Invalid email address');
			$("#odjan11_email").focus();
			return false;
		}

		// check if second email matches the first email address
		var email_confirm = $("#odjan11_email_confirm").val();
		if (email_confirm == '') {
			alert('Please confirm your email address');
			$("#odjan11_email_confirm").focus();
			return false;
		}
		
		if (email != email_confirm) {
			alert('Email addresses do not match');
			return false;
		}
		
		
		return true;

	});

});