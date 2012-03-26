function loadVideo(flv,h,autoplay){
	var t = generateTimeStamp();
	var video = new SWFObject("/layout/flash/main.swf?"+t, 'main', '440', h, '9', '#e2ded3');
	video.addParam("scale", "noscale");
	video.addParam("salign", "tl");
	video.addVariable("videourl", flv);
	video.addVariable("h", h);
	video.addVariable("autoplay", autoplay);
	video.write("flashvideo");
}

$(document).ready(function(){
	//check and replace video
	if ($('#flashvideo').length > 0) {
	
		var bgh = 0;
		var video = $('div#flashvideo span.video').text();
		var height = $('div#flashvideo span.height').text();
		var autoplay = $('div#flashvideo span.autoplay').text();
		if(video != null && video != ""){
			loadVideo(video,height,autoplay)
		}
	}
	
	//init istrip gallery
	if(!($.browser.safari && parseInt($.browser.version) < 500)){
		if($('div.gallery1').length > 0){
			iStripInit();
		}
	}
	//init homepage bg
	/*
	if($('div#container').text() != null && $('div#container').text() != ""){
		initBg();
	}
	*/
	//init search box
	
	if ($('div.col2 div.search').length > 0) {
		if ($('div.col2 div.search').text() != null && $('div.col2 div.search').text() != ""){
			initSearchBox();
		}
	}
	if ($('div.factgrey div.close1').length > 0) {
		if ($('div.factgrey div.close1').text() != null && $('div.factgrey div.close1').text() != ""){
			initCourses();
		}
	}

	if ($('div.colhp input#keyword').length > 0) {
		var ff = $('div.colhp input#keyword');
		var fval = ff.val();
		if ($(ff).length > 0) {
			$(ff).focus(function() {
				if (ff.val() == fval) {
					$(ff).val('');
				}
			});
			$(ff).blur(function() {
				if (ff.val() == '') {
					$(ff).val(fval);
				}
			});
		}
	}
	
	// nkowald - added Konami code to website
	
	(function($) {
	var konamiListeners = [];
	
	var kode = [38, 38, 40, 40, 37, 39, 37, 39, 66, 65];
	var progress = 0;

	$.extend({
		konami: function(listener, sequence) {
			konamiListeners.push(listener);
			
			if (sequence) kode = sequence;
		}
	});

	$(document)
		.bind("keyup", function(event) {
			var c = event.keyCode;
			
			if (c == kode[progress])
				progress++;
			else
				progress = c == kode[0] ? 1 : 0;

			if (progress == kode.length) {
				if (konamiListeners.length > 0) {
					$.each(konamiListeners, function(item) {
						this();
					});
				}
			}
		});
	})(jQuery);
	
	$.konami(function() {
		var s = document.createElement('script');
		s.type='text/javascript';
		document.body.appendChild(s);
		s.src='http://erkie.github.com/asteroids.min.js';
		void(0);
	});

});

function generateTimeStamp(){
	var myDate=new Date();
	var y = myDate.getFullYear();
	var m = myDate.getDate();
	var d = myDate.getDay();
	var h = myDate.getHours();
	var mn = myDate.getMinutes();
	var s = myDate.getSeconds();
	var time = Date.UTC(y,m,d,h,mn,s);
	return time;
}
function initSearchBox(){
	if (($('input#keyword').val() == '') || $('p.noresults').length > 0) {
		if ($('div.search a#main').length > 0) {
			$('div.search a#main').toggle(function(){
				$('div.search div.hide').slideUp("fast");
				$(this).removeClass("active");
			},function(){
				$('div.search div.hide').slideDown("fast");
				$(this).addClass("active");
			});
		}
	} else {
		$('div.search div.hide').hide();
		$('div.search a#main').removeClass("active");
		$('div.search a#main').toggle(function(){
			$('div.search div.hide').slideDown("fast");
			$(this).addClass("active");
		},function(){
			$('div.search div.hide').slideUp("fast");
			$(this).removeClass("active");
		});
	}
}
function initCourses(){
	$('div.factgrey div.close1').each(function(me){
		$(this).hide();
	});
	$('div.factgrey a.head').click(function(me){
		//console.log('click');
		me.preventDefault();
		if($(this).hasClass("active")) {
			$(this).removeClass("active");
			$(this).parent().find('div.close1').slideUp("fast");
		} else {
			$(this).addClass("active");			
			$(this).parent().find('div.close1').slideDown("fast");
		}
		return false;
	});
}
function iStripInit() {
	$('div.imgstrip').each(function(){
		new iStrip(this);
	});
}
function iStrip(container) {
	this.me = $($(container).find('div.internal'));
	this.wrap = $($(container).find('div.gallery1'));
	this.arrows = $($(container).find('div.arrows'));
	this.slngth = this.wrap.width();
	//alert(this.slngth);
	this.imgs = new Array();
	this.gap = null;
	this.lngth = 0;
	this.cpos = 0;
	this.max = null;
	this.maxp = null;
	this.btn = new Array();
	this.act = [true,true];
	this.init();
	this.easing = 'easeInOutExpo';
	this.time = 650;
	return this;
}
iStrip.prototype.init = function() {
	var sobj = this;
	this.me.find('a img').each(function(){
		sobj.addImg($($(this)));	
	});
	this.me.css('width',this.lngth);
	this.max = -((this.lngth-6) - this.slngth);
	this.maxp = 0;
	var l = this.imgs.length;
	for(this.maxp=0;this.maxp<l;this.maxp++) {
		if (this.max >= this.imgs[this.maxp][2]) {
			break;
		}
	}
	if (this.lngth > this.slngth) {
		$(this.arrows.append('<a href="#" class="prev"></a>'));
		$(this.arrows.append('<a href="#" class="next"></a>'));
		$(this.arrows.append('<span>move thumbnails</span>'));
		this.btn[0] = $(this.arrows.find('a.prev'));
		this.btn[1] = $(this.arrows.find('a.next'));
		this.btn[0].click(function(me){me.preventDefault();sobj.move(-1);return false;});
		this.btn[1].click(function(me){me.preventDefault();sobj.move(1);return false;});
		this.ckB();
	} 
}
iStrip.prototype.addImg = function(elm) {
	var w = 127;
	this.imgs.push([elm,-w,-this.lngth]);
	this.lngth +=  w ;
}
iStrip.prototype.getW = function() {
	return this.lngth+this.gap;
}
iStrip.prototype.move = function(dir) {
	if (dir == 1) {
		if (this.cpos < this.maxp && this.act[1]) {
			this.cpos++;
			var npos = this.imgs[this.cpos][2];
			if (-(npos) > (this.lngth-this.gap-this.slngth)) {
				npos = -(this.lngth-this.gap-this.slngth);	
			}
			this.me.animate({marginLeft:npos},this.time,this.easing);
		}
	} else {
		if (this.cpos > 0 && this.act[0]) {
			this.cpos--;
			var npos = this.imgs[this.cpos][2];
			this.me.animate({marginLeft:npos},this.time,this.easing);
		}
	}	
	this.ckB();
}
iStrip.prototype.ckB = function() {
	if (this.cpos == 0) {
		this.btn[0].addClass('notactive');
		this.act[0] = false;
		this.act[1] = true;
		this.btn[1].removeClass('notactive');
	} else if (this.cpos == this.maxp) {
		this.btn[1].addClass('notactive');
		this.act[1] = false;
		this.act[0] = true;
		this.btn[0].removeClass('notactive');
	} else {
		this.btn[0].removeClass('notactive');
		this.btn[1].removeClass('notactive');
		this.act[0] = true;
		this.act[1] = true;
	}
}