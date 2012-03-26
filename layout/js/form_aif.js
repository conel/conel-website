function isValidEmailAddress(emailAddress) {  
	var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
	return pattern.test(emailAddress);  
}
$(document).ready(function() {

	$("#conel_form").submit(function(e) {
		
		// check if first name is blank
		var name = $("#aif_firstname").val();
		if (name == '') {
			alert('First Name is a required field');
			$("#aif_firstname").focus();
			return false;
		}
		
		// check if surname is blank
		var name = $("#aif_surname").val();
		if (name == '') {
			alert('Surname is a required field');
			$("#aif_surname").focus();
			return false;
		}
		 
		// check if email is blank
		var email = $("#aif_email").val();
		if (email == '') {
			alert('Email is a required field');
			$("#aif_email").focus();
			return false;
		}

		if (!isValidEmailAddress(email)) {
			alert('Invalid email address');
			$("#aif_email").focus();
			return false;
		}

		// check if second email matches the first email address
		var email_confirm = $("#aif_email_confirm").val();
		if (email_confirm == '') {
			alert('Please confirm your email address');
			$("#aif_email_confirm").focus();
			return false;
		}
		
		if (email != email_confirm) {
			alert('Email addresses do not match');
			return false;
		}
		
		// Which apprenticeship fair check?
		var which_fair_enfield = $("#aif_afbf_enfield").attr('checked');
		var which_fair_tottenham = $("#aif_afbf_tottenham").attr('checked');
		
		if (which_fair_enfield == false && which_fair_tottenham == false) {
			$("#aif_afbf_enfield").focus();
			alert('Please select which fair you wish to book for');
			return false;
		}
		
		// Which apprenticeship are you interested in?
		var which_apprenticeship_fashion = $("#aif_aoi_1").attr('checked');
		var which_apprenticeship_it_and_telco = $("#aif_aoi_2").attr('checked');
		var which_apprenticeship_railway_eng = $("#aif_aoi_3").attr('checked');
		
		if (which_apprenticeship_fashion == false && which_apprenticeship_it_and_telco == false && which_apprenticeship_railway_eng == false) {
			$("#aif_aoi_1").focus();
			alert('Please select which apprenticeship you\'re interested in');
			return false;
		}
		
		return true;

	});

});