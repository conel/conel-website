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
		 $('#hnc_csd_how_heard').keyup(function(){
			 limitChars('hnc_csd_how_heard', 500, 'charlimitinfo');
		 })
	});
	
	$("#conel_form").submit(function(e) {

		// check if first name is blank
		var name = $("#hnc_csd_firstname").val();
		if (name == '') {
			alert('First Name is a required field');
			$("#hnc_csd_firstname").focus();
			return false;
		}
		
		// check if surname is blank
		var name = $("#hnc_csd_surname").val();
		if (name == '') {
			alert('Surname is a required field');
			$("#hnc_csd_surname").focus();
			return false;
		}
		
		// check if email is blank
		var email = $("#hnc_csd_email").val();
		if (email == '') {
			alert('Email is a required field');
			$("#hnc_csd_email").focus();
			return false;
		}

		if (!isValidEmailAddress(email)) {
			alert('Invalid email address');
			$("#hnc_csd_email").focus();
			return false;
		}

		// check if second email matches the first email address
		var email_confirm = $("#hnc_csd_email_confirm").val();
		if (email_confirm == '') {
			alert('Please confirm your email address');
			$("#hnc_csd_email_confirm").focus();
			return false;
		}
		
		if (email != email_confirm) {
			alert('Email addresses do not match');
			return false;
		}
		
		// check if how heard is blank
		var careers_ict = $("#hnc_csd_how_heard").val();
		if (careers_ict == '') {
			alert('"How Did You Hear About Us?" is a required field');
			$("#hnc_csd_how_heard").focus();
			return false;
		}
		
		
		return true;

	});

});
