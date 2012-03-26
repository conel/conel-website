$(document).ready(function() {
	$('#filter_incomplete, #filter_complete').keyup(function() {
		var selected_show = $(this).attr('id');
		var filter_text = $(this).val(), count = 0;
		var email = '';

		$("table.application_stats td.email_add").each(function () {
			var filter_regexp = '^' + filter_text;
			if ($(this).text().search(new RegExp(filter_regexp, "i")) < 0) {
				$(this).parent().closest('tr').addClass("hidden");
				$(this).parent().closest('tr').removeClass("visible");
			} else {
				var cur_text_val = $(this).text();
				var email = $(this).text();
				var new_text_val = cur_text_val.replace(filter_text, '<strong>' + filter_text + '</strong>');
				$(this).html(new_text_val);
				$(this).parent().closest('tr').removeClass("hidden");
				$(this).parent().closest('tr').addClass("visible");
				count++;
			}
		});
		
		if (count == 1 && selected_show == 'filter_incomplete') {
			
			var email = $('tr.visible td.email_add').text();
			var ref_id = $('tr.visible td.ref_id').html();
			
			$.get("forgotten_ajax.php", { email: email, ref_id: ref_id},
				function(data){
					$('#student_found').html(data);
					$('#student_found').show();
				});
		} else {
			$('#student_found').hide();
		}
		
		if (selected_show == 'filter_incomplete') {
			$("#incomplete_count").text(count + ' incomplete applications');
		} else {
			$("#complete_count").text(count + ' completed applications');
		}

	});
});