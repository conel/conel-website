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
		
		// check if first name is blank
		var name = $("#name").val();
		if (name == '') {
			alert('Name is a required field');
			$("#name").focus();
			return false;
		}
		
		// check if school is blank
		var school = $("#school").val();
		if (school == '') {
			alert('School is a required field');
			$("#school").focus();
			return false;
		}	
		
		// check if Address Line 1 is blank
		var addr1 = $("#address_line_1").val();
		if (addr1 == '') {
			alert('Address Line 1 is a required field');
			$("#address_line_1").focus();
			return false;
		}	
		
		// check if Postcode is blank
		var postcode = $("#postcode").val();
		if (postcode == '') {
			alert('Postcode is a required field');
			$("#postcode").focus();
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

		// check if Contact numbers is blank
		var cnums = $("#contact_numbers").val();
		if (cnums == '') {
			alert('Contact numbers is a required field');
			$("#contact_numbers").focus();
			return false;
		}
		
		// check if Workshop you would like to request is blank
		var workshop = $('input:radio[name=workshop]:checked').val();
		if (workshop != 'Business Workshop' && workshop != 'Construction Workshop' && workshop != 'Hair and Beauty Workshop' && workshop != 'ICT Workshop' && workshop != 'Sports Workshop' && workshop != 'Public Services Workshop' && workshop != 'Travel and Tourism Workshop') {
			alert('\'Workshop you would like to request\' is a required field');
			return false;
		}		
		
		// check if Where would you like the workshop to take place is blank
		var prefered_place = $('input:radio[name=prefered_place]:checked').val();
		if (prefered_place != 'Enfield Centre' && prefered_place != 'Tottenham Centre' && prefered_place != 'School') {
			alert('\'Where would you like the workshop to take place?\' is a required field');
			return false;
		}

		// check if How many people will be attending? is blank
		var num_people_attending = $("#num_people_attending").val();
		if (num_people_attending == '') {
			alert('"How many people will be attending?" is a required field');
			$("#num_people_attending").focus();
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