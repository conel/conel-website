$(document).ready(function() {

var $container;
		
		$(document).ready(	
			function() {
				$container = $("#container");
				$container.wtRotator({
					width:800,
					height:600,
					background_color:"#fff",
					border:"none",
					button_width:24,
					button_height:24,
					button_margin:4,
					auto_start:true,
					delay:1400,
					transition:"fade",
					transition_speed:1000,
					block_size:100,
					vert_size:50,
					horz_size:50,
					cpanel_align:"BR",
					cpanel_margin:6,
					display_thumbs:false,
					display_dbuttons:false,
					display_playbutton:false,
					display_tooltip:false,
					display_numbers:false,
					cpanel_mouseover:false,
					text_mouseover:false
				});
				$("#transitions").val("random").change(
					function() {
						changeTransition($(this).val());
					}
				);
				$("#cpalignments").val("BR").change(
					function() {
						changeCPAlign($(this).val());
					}
				);
				$("#thumbs-cb").attr("checked", "checked").change(
					function() {
						displayThumbs($(this).attr("checked"));	
					}
				);
				$("#dbuttons-cb").attr("checked", "checked").change(
					function() {
						displayDButtons($(this).attr("checked"));	
					}				
				);
				$("#playbutton-cb").attr("checked", "checked").change(
					function() {
						displayPlayButton($(this).attr("checked"));	
					}				
				);
				$("#tooltip-cb").attr("checked", "checked").change(
					function() {
						displayTooltip($(this).attr("checked"));	
					}				
				);								
				$("#text-cb").attr("checked", "").change(
					function() {
						changeDescMouseover($(this).attr("checked"));	
					}				
				);
				$("#cpanel-cb").attr("checked", "").change(
					function() {
						changeCPMouseover($(this).attr("checked"));	
					}				
				);				
			}
		);
		
		function changeTransition(transition) {
			$container.updateTransition(transition);
		}
		function changeCPAlign(align) {
			$container.updateCpAlign(align);
		}
		function displayThumbs(display) {
			$container.displayThumbs(display);	
		}
		function displayDButtons(display) {
			$container.displayDButtons(display);	
		}
		function displayPlayButton(display) {
			$container.displayPlayButton(display);	
		}
		function displayTooltip(display) {
			$container.displayTooltip(display);	
		}		
		function changeDescMouseover(mouseover) {
			$container.updateMouseoverDesc(mouseover);			
		}
		function changeCPMouseover(mouseover) {
			$container.updateMouseoverCP(mouseover);
		}

	$('#container a').click(function(event) {
		event.preventDefault();
	});
});