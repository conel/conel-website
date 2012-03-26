function isValidEmailAddress(emailAddress) {  
	var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
	return pattern.test(emailAddress);  
}
$(document).ready(function() {

	function limitChars(textid, limit, infodiv)
	{
		var text = $('#'+textid).val();    
		var textlength = text.length;
		if(textlength > limit)
		{
			$('#' + infodiv).html(''+limit+' character limit reached');
			$('#'+textid).val(text.substr(0,limit));
			return false;
		}
		else
		{
			$('#' + infodiv).html(''+ (limit - textlength) +' characters left');
			return true;
		}
	}
	 
	$(function(){
		 $('#contact_message').keyup(function(){
			 limitChars('contact_message', 1000, 'charlimitinfo');
		 })
	});

	$("#conel_form").submit(function(e) {

		// check if message is blank
		var name = $("#contact_message").val();
		if (name == '') {
			alert('Message is a required field');
			$("#contact_message").focus();
			return false;
		}
		
		// check if first name is blank
		var name = $("#contact_firstname").val();
		if (name == '') {
			alert('First Name is a required field');
			$("#contact_firstname").focus();
			return false;
		}
		
		// check if surname is blank
		var name = $("#contact_surname").val();
		if (name == '') {
			alert('Surname is a required field');
			$("#contact_surname").focus();
			return false;
		}
		
		// check if email is blank
		var email = $("#contact_email").val();
		if (email == '') {
			alert('Email is a required field');
			$("#contact_email").focus();
			return false;
		}

		if (!isValidEmailAddress(email)) {
			alert('Invalid email address');
			$("#contact_email").focus();
			return false;
		}

		// check if second email matches the first email address
		var email_confirm = $("#contact_email_confirm").val();
		if (email_confirm == '') {
			alert('Please confirm your email address');
			$("#contact_email_confirm").focus();
			return false;
		}
		
		if (email != email_confirm) {
			alert('Email addresses do not match');
			return false;
		}
		
		// check if address line 1 is blank
		var add_line_1 = $("#contact_address_line_1").val();
		if (add_line_1 == '') {
			alert('Address Line 1 is a required field');
			$("#contact_address_line_1").focus();
			return false;
		}
		
		// check if address line 2 is blank
		var add_line_2 = $("#contact_address_line_2").val();
		if (add_line_2 == '') {
			alert('Address Line 2 is a required field');
			$("#contact_address_line_2").focus();
			return false;
		}
		
		// check if address line 3 is blank
		var add_line_3 = $("#contact_address_line_3").val();
		if (add_line_3 == '') {
			alert('Address Line 3 is a required field');
			$("#contact_address_line_3").focus();
			return false;
		}
		
		// check if postcode is blank
		var postcode = $("#contact_postcode").val();
		if (postcode == '') {
			alert('Postcode is a required field');
			$("#contact_postcode").focus();
			return false;
		}

		// check if borough/county is blank
		var postcode = $("#contact_borough_county").val();
		if (postcode == '') {
			alert('Borough/County is a required field');
			$("#contact_borough_county").focus();
			return false;
		}		
		
		return true;

	});

});
