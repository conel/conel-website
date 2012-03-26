$(document).ready(function() {

	$("#open_day_form").submit(function(e) {

		// check if attending is blank	
		var attendance = $("input[name='attending']:checked").val();
		if (attendance == 'Yes') {
		} else if (attendance == 'No') {
		} else {
			alert('Attending is a required field');
			return false;
		}
		
		// check if transport is blank	
		var requiring_trans = $("input[name='require_transport']:checked").val();
		if (requiring_trans == 'Yes') {
		} else if (requiring_trans == 'No') {
		} else {
			alert('Require transport is a required field');
			return false;
		}
		
		if (name == '') {
			alert('Name is a required field');
			$("#text_name").focus();
			return false;
		}
		// check if name is blank
		var name = $("#text_name").val();
		if (name == '') {
			alert('Name is a required field');
			$("#text_name").focus();
			return false;
		}
		 
		// check if email is blank
		var email = $("#text_email").val();
		if (email == '') {
			alert('Email is a required field');
			$("#text_email").focus();
			return false;
		}
		
		return true;

	});

});