
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
		 $('#how_heard').keyup(function(){
			 limitChars('how_heard', 500, 'charlimitinfo');
		 })
	});
	
	$("#conel_form").submit(function(e) {
		
		// check if fullname is blank
		var name = $("#fullname").val();
		if (name == '') {
			alert('Full name is a required field');
			$("#fullname").focus();
			return false;
		}
				
		// check if day of birth day is blank
		var dob_day = $("#dob_day").val();
		if (dob_day == '') {
			alert('Day of Birth is a required field');
			$("#dob_day").focus();
			return false;
		}
				
		// check if month of birth day is blank
		var dob_month = $("#dob_month").val();
		if (dob_month == '') {
			alert('Month of Birth is a required field');
			$("#dob_month").focus();
			return false;
		}
				
		// check if year of birth day is blank
		var dob_year = $("#dob_year").val();
		if (dob_year == '') {
			alert('Year of Birth is a required field');
			$("#dob_year").focus();
			return false;
		}
		 
		// check if email is blank
		var email = $("#email").val();
		if (email == '') {
			alert('Email is a required field');
			$("#email").focus();
			return false;
		}

		if (!isValidEmailAddress(email)) {
			alert('Invalid email address');
			$("#email").focus();
			return false;
		}

		// check if second email matches the first email address
		var email_confirm = $("#email_confirm").val();
		if (email_confirm == '') {
			alert('Please confirm your email address');
			$("#email_confirm").focus();
			return false;
		}
		
		if (email != email_confirm) {
			alert('Email addresses do not match');
			return false;
		}
		
		// check if contact number is blank
		var careers_ict = $("#contact_number").val();
		if (careers_ict == '') {
			alert('Contact number is required field');
			$("#contact_number").focus();
			return false;
		}
		
		// check if address line 1 is blank
		var careers_ict = $("#address_line_1").val();
		if (careers_ict == '') {
			alert('Address Line 1 is required field');
			$("#address_line_1").focus();
			return false;
		}
		
		// check if postcode is blank
		var careers_ict = $("#postcode").val();
		if (careers_ict == '') {
			alert('Postcode is required field');
			$("#postcode").focus();
			return false;
		}

		// check if how heard is blank
		var how_heard = $("#how_heard").val();
		if (how_heard == '') {
			alert('"How Did You Hear About Us?" is a required field');
			$("#how_heard").focus();
			return false;
		}
						
		return true;

	});

});
