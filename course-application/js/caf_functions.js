function addQualification(qual_no) {
	// Show qualification of number specified
	var div_name = '#qualification_tbl_' + qual_no;
	$(div_name).show();
}
var element = '';

function isValidEmailAddress(emailAddress) {  
	var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
	return pattern.test(emailAddress);  
}

$(document).ready(function() {

	$("#s0_email").keyup(function(){
		var email = $("#s0_email").val();  
		if(email != 0) {
			if(isValidEmailAddress(email)) {   
				$("#valid_email").css({ "background-image": "url('js/valid_yes.png')" });  
			} else {  
				$("#valid_email").css({ "background-image": "url('js/valid_no.png')" });  
			}
		} else {
			$("#valid_email").css({ "background-image": "none" });  
		}  
	});

	$('.add_qualification').click(function(el) {
		el.preventDefault();
		var qual_no = el.target.id;
		q_no = (qual_no.replace("qual_",""));
		q_no++;
		q_no_ut = (qual_no.replace("qual_","")); // unincremented
		
		// Copy content from current box into new box
		var name_of_school = $('#s4_name_of_school_' + q_no_ut).val();
		var yr_attended_from = $('#s4_yatt_from_' + q_no_ut).val();
		var yr_attended_to = $('#s4_yatt_to_' + q_no_ut).val();
		/*
		var subject_grades = $('#s4_scg_' + q_no_ut).val();
		var obtained = $('#s4_obtained_' + q_no_ut).val();
		*/
		
		// Fill the input fields - if not filled already
		if (($('#s4_name_of_school_' + q_no).val()) == '') {
			$('#s4_name_of_school_' + q_no).val(name_of_school);
		}
		if (($('#s4_yatt_from_' + q_no).val()) == '') {
			$('#s4_yatt_from_' + q_no).val(yr_attended_from);
		}
		if (($('#s4_yatt_to_' + q_no).val()) == '') {
			$('#s4_yatt_to_' + q_no).val(yr_attended_to);
		}
		/*
		if (($('#s4_scg_' + q_no).val()) == '') {
			$('#s4_scg_' + q_no).val(subject_grades);
		}
		if (($('#s4_obtained_' + q_no).val()) == '') {
			$('#s4_obtained_' + q_no).val(obtained);
		}
		*/
		
		addQualification(q_no);
		$('#' + qual_no).hide();
	});
	
	$('form input, form textarea, form select, form option').click(function() {
		if ($('#save_msg')) {
			$('input.submit_save').attr('value','Save');
			$('#save_msg').html('');
		}
	});
	
	$('input.browse').click(function(el) {
	
		el.preventDefault();
		var form_data = $('form').serialize();
		var url_post = 'caf_ajax_save.php';
		
		// Ajax form submission
		$.ajax({
			type: "POST",
			url: url_post,
			data: form_data,
			success: function() {
				window.location = '/our_courses/subjects';
			}
		});
	});
	
	$('input.submit_save, input.submit_back').click(function(el) {
		el.preventDefault();
		var form_data = $('form').serialize();

		var url_post = 'caf_ajax_save.php';
		$('input.submit_save').attr('value','Saving...');
		
		// Ajax form submission
		$.ajax({
			type: "POST",
			url: url_post,
			data: form_data,
			success: function() {
				if (el.target.className == 'submit_save') {
					$('input.submit_save').attr('value','Saved');
					$('#save_msg').html('Your application has been saved. You can exit the form or continue filling it out.');
				}
			}
		});
		
		return true;
	});

	// For the course details page, if course title or course codes edited it should check if blank. If blank: show browse buttons
	$('#s1_course_title_1, #s1_course_code_1').keyup(function() {
		if ( $('#s1_course_title_1').val() == '' && $('#s1_course_code_1').val() == '') {
			// show browse button
			$('#browse_course_1').removeClass('hidden');
			$('#s1_college_centre_1').attr('selectedIndex', 0);
			$('#s1_college_centre_1').attr('disabled', '');
		}
	});
	
	$('#s1_course_title_2, #s1_course_code_2').keyup(function() {
		if ( $('#s1_course_title_2').val() == '' && $('#s1_course_code_2').val() == '') {
			// show browse button
			$('#browse_course_2').removeClass('hidden');
			$('#s1_college_centre_2').attr('selectedIndex', 0);
			$('#s1_college_centre_2').attr('disabled', '');
		}
	});
	
	// Clear functions
	$('#clear_course_1').click(function(el) {
		el.preventDefault();
		$('#s1_course_title_1').val('');
		$('#s1_course_code_1').val('');
		if ($('#s1_college_centre_1').attr('selectedIndex')) {
			$('#s1_college_centre_1').attr('selectedIndex', 0);
		} else {
			$('#s1_college_centre_1').val('');
		}
		$('#browse_course_1').removeClass('hidden');
		$('#s1_course_title_1').focus();
		$('#s1_course_entry_date_1').attr('disabled', '');
		$('#s1_course_entry_date_1').val('');
	});
	
	$('#clear_course_2').click(function(el) {
		el.preventDefault();
		$('#s1_course_title_2').val('');
		$('#s1_course_code_2').val('');
		if ($('#s1_college_centre_2').attr('selectedIndex')) {
			$('#s1_college_centre_2').attr('selectedIndex', 0);
		} else {
			$('#s1_college_centre_2').val('');
		}
		$('#browse_course_2').removeClass('hidden');
		$('#s1_course_title_2').focus();
		$('#s1_course_entry_date_2').attr('disabled', '');
		$('#s1_course_entry_date_2').val('');
	});
	
	
});