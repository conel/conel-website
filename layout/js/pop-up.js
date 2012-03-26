function setCookie(c_name,value,expiredays) {
	var exdate=new Date();
	exdate.setDate(exdate.getDate()+expiredays);
	document.cookie=c_name+ "=" +escape(value)+
	((expiredays==null) ? "" : ";expires="+exdate.toGMTString());
}

function getCookie(c_name) {
	if (document.cookie.length>0) {
	  c_start=document.cookie.indexOf(c_name + "=");
	  if (c_start!=-1) {
		c_start=c_start + c_name.length+1;
		c_end=document.cookie.indexOf(";",c_start);
		if (c_end==-1) {
			c_end=document.cookie.length;
			return unescape(document.cookie.substring(c_start,c_end));
		}
	  }
	return "";
	}
}

var viewed = getCookie('popup_viewed');
if (viewed != 'y') {
	// Note - the pop-up code requires jQuery: http://jqueryjs.googlecode.com/files/jquery-1.3.2.min.js
	$(document).ready(function() {
	  
		$('#slimbox').show();
		
		var is_IE6 = ((window.XMLHttpRequest == undefined) && (ActiveXObject != undefined));
		var is_chrome =  navigator.userAgent.toLowerCase().indexOf('chrome') > -1;

		// Set height of transparent black overlay
		var site_height = $(document).height() + "px";
		$('#slimbox').height(site_height);
		
		// Set position of white message box
		var browser_height = $(window).height();
		var message_height = $('#message').height();
		var margin_top = ((browser_height - message_height) / 3.2);
		$('#message').css('margin-top', margin_top + "px");
		// Hide select elements in IE6 - in IE6 select elements show through overlay
		if (is_IE6) {
			$('select').hide();
		}
		$('#message').show();
		
		// position close button
		var msg_pos = $('#message').position();
		var msg_pos_left = msg_pos.left;
		var msg_width = ($('#message').width()) + 17;
		var new_close_left = (msg_pos_left + msg_width) + "px";
		var new_close_top = margin_top -13 + "px";
		$('#button_close').css('top', new_close_top);
		$('#button_close').css('left', new_close_left);
		if (!is_chrome) {
			$('#button_close').show();
		} else {
			$('#button_close').hide();
		}
		
		// Set up click events for close button and conel link
		
		$('.back_home').click(function(event) {
			event.preventDefault();
			$('#slimbox').hide();
			$('select').show();
		});
		
		$('#button_close').click(function(event) {
			event.preventDefault();
			$('#slimbox').hide();
			$('select').show();
		});
		
		setCookie('popup_viewed','y','7');
			
	});

}