function isValidEmailAddress(emailAddress) {  
	var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
	return pattern.test(emailAddress);  
}
/*
function isValidDOB(dob) {
	var dob_regexp = /^([0-9]){2}(\/){1}([0-9]){2}(\/)([0-9]){4}$/;
	return dob_regexp.test(dob);
}
*/

$(document).ready(function() {
	/*
	function disableInputsEnfield() {
		// Disable Enfield Inputs
		$("#form_col1").addClass('disabled');
		$("#form_col1").removeClass('enabled');
		$("#form_col1 input[name^=enfield_workshop_415pm_to_5pm]:radio").attr('disabled',true);
		$("#form_col1 input[name^=enfield_workshop_515pm_to_6pm]:radio").attr('disabled',true);
	}
	function enableInputsEnfield() {
		// Enable Enfield Inputs
		$("#form_col1").addClass('enabled');
		$("#form_col1").removeClass('disabled');
		$("#form_col1 input[name^=enfield_workshop_415pm_to_5pm]:radio").attr('disabled',false);
		$("#form_col1 input[name^=enfield_workshop_515pm_to_6pm]:radio").attr('disabled',false);
	}
	function disableInputsTottenham() {
		// Disable Tottenham inputs
		$("#form_col2").addClass('disabled');
		$("#form_col2").removeClass('enabled');
		$("#form_col2 input[name^=tottenham_workshop_415pm_to_5pm]:radio").attr('disabled',true);
		$("#form_col2 input[name^=tottenham_workshop_515pm_to_6pm]:radio").attr('disabled',true);
	}
	function enableInputsTottenham() {
		// Enable Tottenham inputs
		$("#form_col2").addClass('enabled');
		$("#form_col2").removeClass('disabled');
		$("#form_col2 input[name^=tottenham_workshop_415pm_to_5pm]:radio").attr('disabled',false);
		$("#form_col2 input[name^=tottenham_workshop_515pm_to_6pm]:radio").attr('disabled',false);
	}
	
	function enableOrDisableEnfield() {
		if ($("#rod_open_day_registering_for_1").attr('checked') == true) {
			enableInputsEnfield();
		} else {
			disableInputsEnfield();
		}
		if ($("#rod_open_day_registering_for_2").attr('checked') == true) {
			enableInputsTottenham();
		} else {
			disableInputsTottenham();
		}
	}
	
	
	function enableOrDisableTottenham() {
		if ($("#rod_open_day_registering_for_2").attr('checked') == true) {
			enableInputsTottenham();
		} else {
			disableInputsTottenham();
		}
		if ($("#rod_open_day_registering_for_1").attr('checked') == true) {
			enableInputsEnfield();
		} else {
			disableInputsEnfield();
		}
	}
	*/

	//$("#rod_open_day_registering_for_1").click(enableOrDisableEnfield);
	//$("#rod_open_day_registering_for_2").click(enableOrDisableTottenham);
	

	$("#conel_form").submit(function(e) {

		// Check if registration place is checked
		if ( ($('#rod_open_day_registering_for_1').attr('checked') == false) && ($('#rod_open_day_registering_for_2').attr('checked') == false) ) {
			alert('Open Day registering for ("Please register my place at") is a required field');
			$('#rod_open_day_registering_for_1').focus();
			return false;
		}
		
		// Now check the value of 'register my place for'
		/*
		if ($('#rod_open_day_registering_for_1').val() != 'Enfield Centre - 03/11/2010') {
			alert('Tampering with form values not allowed');
			return false;
		}
		if ($('#rod_open_day_registering_for_2').val() != 'Tottenham Centre - 24/11/2010') {
			alert('Tampering with form values not allowed');
			return false;
		}
		*/
		
		// Choice of workshop required, check based on selection
		/*
		if ($('#rod_open_day_registering_for_1').attr('checked') == true) {
		
			var enfield_ws_selected = $('#form_col1 input:radio:checked').val();
			
			// Check that a selection has been made in the Enfield column
			if (enfield_ws_selected == undefined) {
				alert('Please select the Enfield Centre workshop(s) you wish to register for');
				$('#rod_open_day_registering_for_1').focus();
				return false;
			}

		}
		*/
		
		if ($('#rod_open_day_registering_for_2').attr('checked') == true) {
			
			//var tottenham_ws_selected = $('#form_col2 input:radio:checked').val();
			var tottenham_ws_selected = $('#conel_form input:radio:checked').val();
			
			// Check that a selection has been made in the Tottenham column
			if (tottenham_ws_selected == undefined) {
				alert('Please select the Tottenham Centre workshop(s) you wish to register for');
				$('#rod_open_day_registering_for_2').focus();
				return false;
			}
			
		}
		
		// check if first name is blank
		var name = $("#rod_firstname").val();
		if (name == '') {
			alert('First Name is a required field');
			$("#rod_firstname").focus();
			return false;
		}
		
		// check if surname is blank
		var name = $("#rod_surname").val();
		if (name == '') {
			alert('Surname is a required field');
			$("#rod_surname").focus();
			return false;
		}
		
		/*
		// check if date of birth is blank
		var dob = $("#rod_date_of_birth").val();
		
		// check if dob is blank
		if (dob == '') {
			alert('Date of birth is a required field');
			$("#rod_date_of_birth").focus();
			return false;
		}
		
		if (!isValidDOB(dob)) {
			alert("Invalid date of birth\n\n- Needs to entered be in DD/MM/YYYY format");
			$("#rod_date_of_birth").focus();
			return false;
		}
		*/
		 
		// check if email is valid
		var email = $("#rod_email").val();
		
		// check if email is blank
		if (email == '') {
			alert('Email is a required field');
			$("#rod_email").focus();
			return false;
		}
		
		if (!isValidEmailAddress(email)) {
			alert('Invalid email address');
			$("#rod_email").focus();
			return false;
		}

		// check if second email matches the first email address
		var email_confirm = $("#rod_email_confirm").val();
		if (email_confirm == '') {
			alert('Please confirm your email address');
			$("#rod_email_confirm").focus();
			return false;
		}
		
		if (email != email_confirm) {
			alert('Email addresses do not match');
			$("#rod_email_confirm").focus();
			return false;
		}
		
		return true;

	});

});