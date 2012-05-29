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
		 $('#odf_1213_how_heard').keyup(function(){
			 limitChars('odf_1213_how_heard', 500, 'charlimitinfo');
		 })
	});
    
	$("#conel_form").submit(function(e) {

		// I would like to register my attendance at check
		var register_attendance = $("td.days input").attr('checked');
		
		if (register_attendance == false) {
			alert('"I would like to register my attendance at" is a required field');
			return false;
		}
		
		// check if first name is blank
		var name = $("#odf_1213_firstname").val();
		if (name == '') {
			alert('First Name is a required field');
			$("#odf_1213_firstname").focus();
			return false;
		}
		
		// check if surname is blank
		var name = $("#odf_1213_surname").val();
		if (name == '') {
			alert('Surname is a required field');
			$("#odf_1213_surname").focus();
			return false;
		}
		
		 
		// check if email is blank
		var email = $("#odf_1213_email").val();
		if (email == '') {
			alert('Email is a required field');
			$("#odf_1213_email").focus();
			return false;
		}

		if (!isValidEmailAddress(email)) {
			alert('Invalid email address');
			$("#odf_1213_email").focus();
			return false;
		}

		// check if second email matches the first email address
		var email_confirm = $("#odf_1213_email_confirm").val();
		if (email_confirm == '') {
			alert('Please confirm your email address');
			$("#odf_1213_email_confirm").focus();
			return false;
		}
		
		if (email != email_confirm) {
			alert('Email addresses do not match');
			return false;
		}
		
		
		return true;

	});

});
