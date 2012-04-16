function isValidEmailAddress(emailAddress) {  
	var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
	return pattern.test(emailAddress);  
}
$(document).ready(function() {

	$("#conel_form").submit(function(e) {

		// check if name is blank
		var name = $("#endyr_name").val();
		if (name == '') {
			alert('Name is a required field');
			$("#endyr_name").focus();
			return false;
		}
        
		// check if 'are you a' is blank
		var are_you_a = $('input:radio[name=i_am_a]:checked').val();
		if (are_you_a != 'School' && are_you_a != 'Prospective Student' && are_you_a != 'Friend/Family of Student') {
			alert('\'Are you a\' is a required field');
			return false;
		}
		
		// check if email is blank
		var email = $("#endyr_email").val();
		if (email == '') {
			alert('Email is a required field');
			$("#endyr_email").focus();
			return false;
		}

		if (!isValidEmailAddress(email)) {
			alert('Invalid email address');
			$("#endyr_email").focus();
			return false;
		}

		// check if second email matches the first email address
		var email_confirm = $("#endyr_email_confirm").val();
		if (email_confirm == '') {
			alert('Please confirm your email address');
			$("#endyr_email_confirm").focus();
			return false;
		}
		
		if (email != email_confirm) {
			alert('Email addresses do not match');
			return false;
		}

		// check if show/exhib like to attend is blank
		var show_exhib = $("#endyr_show_exhib").val();
		if (show_exhib == '') {
			alert('\'Show/Exhibition you would like to attend\' is a required field');
			$("#endyr_show_exhib").focus();
			return false;
		}

		// check if How many people is blank
		var num_pics = $("#endyr_num_people").val();
		if (num_pics == '') {
			alert('\'How many people will be attending\' is a required field');
			$("#endyr_num_people").focus();
			return false;
		}
		
		return true;

	});

});
