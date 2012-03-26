$(document).ready(function() {

	$('#bug_found').colorbox({
		onOpen:function(){ $("#email_bugs").show(); },
		onCleanup:function(){ $("#email_bugs").hide(); },
			width:"930", inline:true, href:"#email_bugs"
	});
	
	if ($("#worksheet_form").length > 0) {

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
			 $('#notes').keyup(function(){
				 limitChars('notes', 450, 'charlimitinfo');
			 })
		});

		/*
		$(':checkbox').iphoneStyle({
		  checkedLabel: 'Yes',
		  uncheckedLabel: 'No'
		});
		*/

		$('form input').click( 
			function() { 
			if ($('form input').attr("disabled") == true) { 
				$('form input').removeAttr("disabled"); 
			} 
		});
	
		$('.disabled').click(function(el){
			$(this).removeClass('disabled');
		});


		$("#worksheet_form").submit(function(e) {

			//var data = $('#worksheet_form').serialize();
			//alert(data);
			
			var errors = false;

			// All checkboxes must be checked
			$.each($('input:checkbox'), function(index, value) {
				if ($(this).attr('checked') == false) {
					alert('All PCs, Monitors and Checklist items must be marked as complete');
					errors = true;
					return false;
				}
			});

			if (errors) {
				return false;
			}

			var engineer = $('#engineer_choice').val();
			if (engineer == '') {
				alert('You must select yourself from the Engineers list to email this worksheet');
				$('#engineer_choice').focus();
				return false;
			}
			
			return true;

		});

		function saveForm() {

			var form_data = $('#worksheet_form').serialize();
			var url_post = 'email.php';
			
			// Ajax form submission
			$.ajax({
				type: "POST",
				url: url_post,
				data: form_data,
				success: function(data) {
					$('#save_state input').attr('value', data);
					// Update hidden values so we don't get duplicates
					$('#ohyrsc').val('1');
					$('#cwmisnb').val('1');
				}
			});

			return true;
		}

		$('#save_state input').click(function(el) {
			el.preventDefault();
			saveForm();
		});

		// Save every minute
		/*
		setInterval( function() {
			saveForm();
		}, 60000);
		*/

		$('form input, form textarea, form select, form option').click(function() {
			$('#save_state input').attr('value', 'Save');
		});

	}
});

