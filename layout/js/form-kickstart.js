function isValidEmailAddress(emailAddress) {  
	var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
	return pattern.test(emailAddress);  
}
$(document).ready(function() {

	$("#conel_form").submit(function(e) {
	
		// I would like to register my attendance at check
		var short_course_prep = $("#ci_pfe").attr('checked');
		var short_course_it = $("#ci_itus").attr('checked');
		var short_course_bus = $("#ci_ba").attr('checked');
		
		if (short_course_prep == false && short_course_it == false && short_course_bus == false) {
			$("#ci_pfe").focus();
			alert("'Which short course(s) are you interested in?' is a required field");
			return false;
		}
		
		// check if first name is blank
		var name = $("#kickstart_firstname").val();
		if (name == '') {
			alert('First Name is a required field');
			$("#kickstart_firstname").focus();
			return false;
		}
		
		// check if surname is blank
		var name = $("#kickstart_surname").val();
		if (name == '') {
			alert('Surname is a required field');
			$("#kickstart_surname").focus();
			return false;
		}
		
		// check if email is blank
		var email = $("#kickstart_email").val();
		if (email == '') {
			alert('Email is a required field');
			$("#kickstart_email").focus();
			return false;
		}

		if (!isValidEmailAddress(email)) {
			alert('Invalid email address');
			$("#kickstart_email").focus();
			return false;
		}

		// check if second email matches the first email address
		var email_confirm = $("#kickstart_email_confirm").val();
		if (email_confirm == '') {
			alert('Please confirm your email address');
			$("#kickstart_email_confirm").focus();
			return false;
		}
		
		if (email != email_confirm) {
			alert('Email addresses do not match');
			return false;
		}
		
		return true;

	});

});