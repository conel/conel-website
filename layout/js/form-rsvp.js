function isValidEmailAddress(emailAddress) {  
	var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
	return pattern.test(emailAddress);  
}
$(document).ready(function() {

	// On yes: show guest details
	$('#rsvp_ceaw_yes').click(function() {
		$('.rsvp_guest').show();
		$('#rt_job_title').html('Job Title:');
		$('#rt_organisation').html('Organisation:');
	});
	
	// On no: hide guest details
	$('#rsvp_ceaw_no').click(function() {
		$('.rsvp_guest').hide();
		// Add required info to job title and organisation
		$('#rt_job_title').html('Job Title<span class="required">*</span>:');
		$('#rt_organisation').html('Organisation<span class="required">*</span>:');
	});

	$("#conel_form").submit(function(e) {
		// check if first name is blank
		var name = $("#rsvp_firstname").val();
		if (name == '') {
			alert('First Name is a required field');
			$("#rsvp_firstname").focus();
			return false;
		}
		
		// check if surname is blank
		var name = $("#rsvp_surname").val();
		if (name == '') {
			alert('Surname is a required field');
			$("#rsvp_surname").focus();
			return false;
		}
		
		// Check if are you an awards winner is checked
		if (!$("input[@name='ce_awards_2011_winner']:checked").val()) {
			alert('\'Are you a College Excellence Awards 2011 winner?\' is a required field');
			return false;
		}
		
		// If 'No' is checked: job title and organisation are required
		if (($("input[@name='ce_awards_2011_winner']:checked").val()) == 'no') {
			
			// check if job title is blank
			var job_title = $("#rsvp_job_title").val();
			if (job_title == '') {
				alert('Job Title is a required field');
				$("#rsvp_job_title").focus();
				return false;
			}
			
			// check if organisation is blank
			var organisation = $("#rsvp_organisation").val();
			if (organisation == '') {
				alert('Organisation is a required field');
				$("#rsvp_organisation").focus();
				return false;
			}
		}

		// check if email is blank
		var email = $("#rsvp_email").val();
		if (email == '') {
			alert('Email is a required field');
			$("#rsvp_email").focus();
			return false;
		}

		if (!isValidEmailAddress(email)) {
			alert('Invalid email address');
			$("#rsvp_email").focus();
			return false;
		}

		// check if second email matches the first email address
		var email_confirm = $("#rsvp_email_confirm").val();
		if (email_confirm == '') {
			alert('Please confirm your email address');
			$("#rsvp_email_confirm").focus();
			return false;
		}
		
		if (email != email_confirm) {
			alert('Email addresses do not match');
			return false;
		}
		
		
		return true;

	});

});
