if(!window.console) {
 window.console = {
 	log: function() {
 		alert(arguments[0]);	
 	}	
 }	
}

$(window).bind("load",function(){

	$('div#canvas').show();
	$('div#canvas').css('display','block');
	// constants
	function imageCropper(w,h) {
	
		// point size
		this.pd = 3;
	
		// max dimension
		this.max = 300;
		this.init(w,h);
	}
	
	imageCropper.prototype.init = function(w,h) {


		var obj = this; // reference to self

		//activate resize
		$('img#resizeicon').click(function(){
			obj.showMax(1);
		});

		
		this.holder = $($('div#image'));
		this.holdero = this.holder.offset();
		
		var temp = $($('img.original'));
		this.img = {i: temp,copy:null,copyh:null,w: temp.width(),h:temp.height(),ratiohw: (temp.height()/temp.width()),ratiowh: (temp.width()/temp.height()),orientation:0,scale:0,p:temp.offset()};
		if (this.img.w > this.img.h) {
			this.img.orientation = true;
			this.img.scale = this.max / this.img.w;
			this.img.i.width(this.max+'px');
			this.img.i.height((this.max*this.img.ratiohw)+'px');
		} else {
			this.img.orientation = false;
			this.img.scale = this.max / this.img.h;
			this.img.i.height(this.max+'px');
			this.img.i.width((this.max*this.img.ratiowh)+'px');		
		}
		this.img.i.after("<div id='selectionh'><div class='pointd' rel='n' style='cursor:n-resize;'></div><div class='pointd' rel='ne' style='cursor:ne-resize;'></div><div class='pointd' rel='e'  style='cursor:e-resize;'></div><div class='pointd' rel='se' style='cursor:se-resize;'></div><div class='pointd' rel='s' style='cursor:s-resize;'></div><div class='pointd' rel='sw' style='cursor:sw-resize;'></div><div class='pointd' rel='w' style='cursor:w-resize;'></div><div class='pointd' rel='nw' style='cursor:nw-resize;'></div><div class='point' rel='n'></div><div class='point' rel='ne'></div><div class='point' rel='e'></div><div class='point' rel='se'></div><div class='point' rel='s'></div><div class='point' rel='sw'></div><div class='point' rel='w'></div><div class='point' rel='nw'></div></div><img id='selection' src='interface/css/blank.gif' />");
		this.img.i.after("<div id='cropdiv'><img id='cropimg' src='"+this.img.i.attr('src')+"' /></div>");
		this.img.i.after("<div class='ic_overlay' style='background:#000;z-index:45;opacity:.8;filter:alpha(opacity = 80);position:absolute;width:"+this.img.i.width() + 'px'+";height:"+this.img.i.height() + 'px'+";left:"+(this.holdero.left+this.pd)+'px'+";top:"+(this.holdero.top+this.pd)+'px'+";'></div>");

		this.holder.width((this.img.i.width() + this.pd*2) + 'px');
		this.holder.height((this.img.i.height() + this.pd*2) + 'px');
		$('p#display').html('Scale of image shown in relation to original image: '+(this.img.scale*100)+'% <br />');
		
		if (w == 0 && h == 0) {
			// we have no size and no, width and height limit set to 10, selection ratio set to image ratio
			if (this.img.orientation) {
				w = 10;h=w / this.img.ratiowh;
			} else {
				h=10;w=h*this.img.ratiowh;
			}
			minw=10;minh=10;
		} else if (w == 0) {
			// scaling in width and height directions with a limit on height, width limit automatically set to 10
			if (this.img.orientation) {
				w = h / this.img.ratiohw;
			} else {
				w = h * this.img.ratiowh;
			}
			$('div#selectionh').find('div[rel="ne"]').add('div[rel="se"]').add('div[rel="nw"]').add('div[rel="sw"]').remove();
			minw=10;minh=h;
		} else if (h == 0) {
			// scaling in width and height directions with a limit on width, width limit automatically set to 10
			if (this.img.orientation) {
				h = w / this.img.ratiowh;
			} else {
				h = w * this.img.ratiohw;
			}
			$('div#selectionh').find('div[rel="ne"]').add('div[rel="se"]').add('div[rel="nw"]').add('div[rel="sw"]').remove();
			minw=w;minh=10;
		} else {
			// scaling only in poroprtion
			$('div#selectionh').find('div[rel="n"]').add('div[rel="s"]').add('div[rel="w"]').add('div[rel="e"]').remove();
			minw=w;minh=h;

		}
		
		this.sel = {o:null,minw:minw,minh:minh,points:null,pointsd:null,ratiohw:null,ratiowh:null};
		this.sel.o = $($(this.holder).find('#selection'));
		
		this.sRatio(w,h);
		this.showMax(0);
		


		//start selection
		var tt = this.img.p.top+this.pd+(this.img.i.height()/2-this.sel.o.height()/2);
		var tl = this.img.p.left+this.pd+(this.img.i.width()/2-this.sel.o.width()/2);
		
		this.sel.o.css('top',tt+'px');
		this.sel.o.css('left',tl+'px');
		
		this.img.copy = $($('img#cropimg'));
		this.img.copyh = $($('div#cropdiv'));
		this.img.i.css('margin',this.pd+'px');
		this.img.copy.width(this.img.i.width());
		this.img.copy.height(this.img.i.height());
		this.img.copy.css('top','0px');
		this.img.copy.css('left','0px');
		this.img.copyh.css('left',tl+'px');
		this.img.copyh.css('top',tt+'px');
		this.img.copyh.width(this.sel.o.width());
		this.img.copyh.height(this.sel.o.height());



		this.sel.points = $($(this.holder).find('#selectionh')).find('.point');
		this.sel.pointsd = $($(this.holder).find('#selectionh')).find('.pointd');
		this.sel.points.css({width:this.pd*2+'px',height:this.pd*2+'px'});
		this.sel.pointsd.css({width:this.pd*4+'px',height:this.pd*4+'px'});
		this.sel.pointsd.draggable({ containment: '#image',
			stop:function(){
				obj.change(this);
			},
			drag:function(){
				obj.change(this);
			}});
		this.sel.o.draggable({ containment: 'img.original',
			start:function(){
				$('div#image').find('.point').hide();
				$('div#canvas').css({cursor:'move'});
			},
			drag:function(){
				obj.dragTest();
				obj.upCopy();
			},
			stop:function(){
				obj.dragTest();
				obj.upCopy();
				obj.draw(this);
				$('div#canvas').css({cursor:'default'});
				$('div#image').find('.point').show();
			}
		});
		
		this.draw();
		this.upCopy();
		
		if (this.scaleFactor() >  1) {
			$('p#scale').after('<p>Error image is to small, the image will be scaled up and you will loose image quality.</p>');
			$('div#selectionh').find('div[rel="n"]').add('div[rel="ne"]').add('div[rel="e"]').add('div[rel="se"]').add('div[rel="sw"]').add('div[rel="nw"]').add('div[rel="s"]').add('div[rel="w"]').remove();
		}
	}

	imageCropper.prototype.dragTest = function() {
	// ensure that we're not dragging outside the format (we do have margin to enable dragging in the first place)
	// currently the problem is that we can't drag all the way to the right or bottom
		var tp = $(this.holder).find('#selection').offset();
		var so = this.sel.o.offset();
		if ((so.top-this.holdero.top-this.pd) < 0) {
			this.sel.o.css('top',(this.holdero.top+this.pd)+'px');
		}
		if ((so.left-this.holdero.left-this.pd) < 0) {
			this.sel.o.css('left',(this.holdero.left+this.pd)+'px');
		}

		
	
	}

	imageCropper.prototype.output = function() {
		var so = this.sel.o.offset();
		var sw = this.sel.o.width();
		var sh = this.sel.o.height();
		$('input#top').val(Math.round((so.top-this.holdero.top-this.pd) / this.img.scale));
		$('input#left').val(Math.round((so.left-this.holdero.left-this.pd) / this.img.scale));
		$('input#height').val(Math.round(sh / this.img.scale));
		$('input#width').val(Math.round(sw / this.img.scale));
		$('input#scalefactor').val(Math.round(this.scaleFactor()));
	
	}
	
	imageCropper.prototype.scaleFactor = function() {
		if (this.sel.minw != 10) {
			sf = this.sel.minw / (this.sel.o.width() / this.img.scale);
		} else if (this.sel.minh != 10) {
			sf = this.sel.minh / (this.sel.o.height() / this.img.scale);
		} else {
			// no constraints = no scaling 
			sf = 1;
		}
		return sf;
	}

	imageCropper.prototype.showMax = function(type) {
		
	
		//maximum size selection
		if (this.sel.minw > this.sel.minh && Math.floor(this.img.i.width()* this.sel.ratiohw) < this.img.i.height()+1) {
			this.sel.o.width(this.img.i.width());
			this.sel.o.height(Math.floor(this.sel.o.width()* this.sel.ratiohw));
		} else {
			this.sel.o.height(this.img.i.height());
			this.sel.o.width(Math.floor(this.sel.o.height()* this.sel.ratiowh));
		}
		if (type == 1) {
			var tt = this.img.p.top+this.pd+(this.img.i.height()/2-this.sel.o.height()/2);
			var tl = this.img.p.left+this.pd+(this.img.i.width()/2-this.sel.o.width()/2);
		
			this.sel.o.css('top',tt+'px');
			this.sel.o.css('left',tl+'px');
			this.draw();
			this.upCopy();
		}
	
	}
	
	imageCropper.prototype.showMin = function() {
		this.sel.o.width(this.sel.minw * this.img.scale + 'px');
		this.sel.o.height(this.sel.minh * this.img.scale + 'px');
	}
	imageCropper.prototype.chkC = function(w,h,sp,pr) {
		pr == undefined ? pr=false : pr=pr;

		var temp = true;
		
		if ((h+sp.top-this.holdero.top-this.pd) > (this.img.h*this.img.scale)+1) {
			//console.log("error_1");
			temp = false;
		}
		if ((w+sp.left-this.holdero.left-this.pd) > (this.img.w*this.img.scale)) {
			//console.log("error_2");

			temp = false;
		}
		if (w < this.sel.minw*this.img.scale) {
			//console.log("error_3");

			temp = false;
		}
		if (h < this.sel.minh*this.img.scale) {
			//console.log("error_4");

			temp = false;
		}
		if (sp.top < (this.holdero.top+this.pd)) {
			//console.log("error_5");

			temp = false;
		}
		if (sp.left < (this.holdero.left+this.pd/2)) {
			//console.log("error_6");

			temp = false;
		}
		return {test: temp, w: w, h: h, sp:sp};
	}
	
	imageCropper.prototype.change = function(point) {
		var pp = $(point).offset();
		var bo = $('body').offset();
		// compensate for any offset on the body
		pp.top = Math.floor(pp.top+bo.top);pp.left = Math.floor(pp.left+bo.left);
		
		var sp = this.sel.o.offset();
		switch($(point).attr('rel')) {
			case 'n':
				var to = pp.top - sp.top;
				var th = this.sel.o.height() - to;
				var np = {top: pp.top, left: sp.left}
				var chk = this.chkC(this.sel.o.width(),th,np);
				if (chk.test) {
					this.sel.o.css('left',chk.sp.left + 'px');
					this.sel.o.css('top',chk.sp.top + 'px');
					this.sel.o.width(chk.w + 'px');
					this.sel.o.height(chk.h + 'px');
					this.sRatio(this.sel.o.width(),this.sel.o.height());
					this.upCopy();
				}
				break;
			case 'ne':
				if (this.img.orientation) {
					var nv = (pp.left+this.pd) - sp.left;
					var th = Math.floor(nv * this.sel.ratiohw);
					var to = this.sel.o.height()- th;
					var tt = sp.top + to;
					var np = {top: tt, left: sp.left}
					var chk = this.chkC(nv,th,np,true);
				} else {
					var to = pp.top - sp.top;
					var th = this.sel.o.height() - to;
					var tw = Math.floor(th * this.sel.ratiowh);
					var np = {top: pp.top, left: sp.left}
					var chk = this.chkC(tw,th,np,true);
			
				}
				if (chk.test) {
					this.sel.o.css('left',chk.sp.left + 'px');
					this.sel.o.css('top',chk.sp.top + 'px');
					this.sel.o.width(chk.w + 'px');
					this.sel.o.height(chk.h + 'px');
					this.upCopy();
				}	
				break;
			case 'e':
				//console.log(pp.left);
				var nv = (pp.left+this.pd) - sp.left;
				var chk = this.chkC(nv,this.sel.o.height(),sp,false);
				if (chk.test) {
					this.sel.o.width(chk.w + 'px');
					this.sRatio(this.sel.o.width(),this.sel.o.height());
					this.upCopy();
				}
				break;
			case 'se':
				if (this.img.orientation) {
					var nv = (pp.left+this.pd) - sp.left;
					var chk = this.chkC(nv,nv * this.sel.ratiohw,sp,true);
				} else {
					var nv = (pp.top+this.pd) - sp.top;
					var chk = this.chkC(nv * this.sel.ratiowh,nv,sp,true);
				}
				if (chk.test) {
					this.sel.o.height(chk.h + 'px');
					this.sel.o.width(chk.w + 'px');
					this.upCopy();
				}
				break;
			case 's':
				//console.log(pp.top);
				var nv = (pp.top+this.pd) - sp.top;
				var chk = this.chkC(this.sel.o.width(),nv,sp,false);
				if (chk.test) {
					this.sel.o.height(chk.h + 'px');
					this.sRatio(this.sel.o.width(),this.sel.o.height());
					this.upCopy();
				}
				break;
			case 'sw':
				if (this.img.orientation) {
					var lo = pp.left - sp.left;
					var tw = this.sel.o.width() - lo;
					var th = Math.floor(tw * this.sel.ratiohw);
					var np  = {top: sp.top, left: pp.left}
					var chk = this.chkC(tw,th,np);
				} else {
					var th = (pp.top+this.pd) - sp.top;
					var tw = Math.floor(th * this.sel.ratiowh);
					var lo = this.sel.o.width()- tw;
					var tl = sp.left + lo;
					var np = {top: sp.top, left: tl}
					var chk = this.chkC(tw,th,np);
				}
				if (chk.test) {
						this.sel.o.css('left',chk.sp.left + 'px');
						this.sel.o.css('top',chk.sp.top + 'px');
						this.sel.o.width(chk.w + 'px');
						this.sel.o.height(chk.h + 'px');
						this.upCopy();
				}
				break;
			case 'w':
				var tl = (pp.left);
				var lo = tl - sp.left;
				var tw = this.sel.o.width() - lo;
				var np  = {top: sp.top, left: tl}
				var chk = this.chkC(tw,this.sel.o.height(),np);
				if (chk.test) {
					this.sel.o.css('left',chk.sp.left + 'px');
					this.sel.o.width(chk.w + 'px');
					this.sRatio(this.sel.o.width(),this.sel.o.height());
					this.upCopy();
				}
				break;
			case 'nw':
				if (this.img.orientation) {
					var lo = pp.left - sp.left;
					var tw = this.sel.o.width() - lo;
					var th = Math.floor(tw * this.sel.ratiohw) ;
					var to = this.sel.o.height()- th;
					var tt = sp.top + to;
					var np = {top: tt, left: pp.left}
					chk = this.chkC(tw,th,np,true);
				} else {
		
					var to = pp.top - sp.top;
					var th = this.sel.o.height() - to;
					var tw = Math.floor(th * this.sel.ratiowh);
					var lo = this.sel.o.width() - tw;
					var tl = sp.left + lo;
					var np = {top: pp.top, left: tl}
					chk = this.chkC(tw,th,np,true);
				}
				if (chk.test) {
					this.sel.o.css('left',chk.sp.left + 'px');
					this.sel.o.css('top',chk.sp.top + 'px');
					this.sel.o.width(chk.w + 'px');
					this.sel.o.height(chk.h + 'px');
					this.upCopy();
				}
				break;
		}
		this.draw();
	}
	
	imageCropper.prototype.sRatio = function(w,h) {	
		this.sel.ratiohw = h / w;
		this.sel.ratiowh = w / h;	
	}
	
	imageCropper.prototype.upCopy = function() {
		var tp = $(this.holder).find('#selection').offset();
		
	
		this.img.copyh.css('top',tp.top+'px');
		this.img.copyh.css('left',tp.left+'px');
		
		var dp = this.img.copyh.offset();
		this.img.copy.css('marginTop',this.holdero.top+this.pd-dp.top+'px');
		this.img.copy.css('marginLeft',this.holdero.left+this.pd-dp.left+'px');	
		this.img.copyh.width(this.sel.o.width());
		this.img.copyh.height(this.sel.o.height());

	}
	imageCropper.prototype.draw = function() {
		var so = this.sel.o.offset();
		var sw = this.sel.o.width();
		var sh = this.sel.o.height();
		
		
		for(var i=0;i < this.sel.points.length;i++) {
			var point = $($(this.sel.points[i]));
			var pointd = $($(this.sel.pointsd[i]));
			var ovt = this.img.p.top+this.pd+'px';
			var ot = so.top-this.holdero.top-this.pd +'px';
			var ol = so.left-this.holdero.left-this.pd  +'px';
			
			switch(point.attr('rel')) {
				case 'nw': 
					(pointd).css({top:(so.top - this.pd*2) +'px', left:(so.left - this.pd*2)  +'px'});
					(point).css({top:(so.top - this.pd) +'px', left:(so.left - this.pd)  +'px'});
					break;
				case 'n': 
					(pointd).css({top:(so.top - this.pd*2) +'px', left:(so.left + sw/2 - this.pd)  +'px'});
					(point).css({top:(so.top - this.pd) +'px', left:(so.left + sw/2 - this.pd/2)  +'px'});
					break;
				case 'ne': 
					(pointd).css({top:(so.top - this.pd*2) +'px', left:(so.left + sw - this.pd*2)  +'px'});
					(point).css({top:(so.top - this.pd) +'px', left:(so.left + sw - this.pd)  +'px'});
					break;
				case 'e': 
					(pointd).css({top:(so.top + sh/2 - this.pd*2) +'px', left:(so.left + sw - this.pd*2)  +'px'});
					(point).css({top:(so.top + sh/2 - this.pd) +'px', left:(so.left + sw - this.pd)  +'px'});
					break;
				case 'se': 
					(pointd).css({top:(so.top + sh - this.pd*2) +'px', left:(so.left + sw - this.pd*2)  +'px'});
					(point).css({top:(so.top + sh - this.pd) +'px', left:(so.left + sw - this.pd)  +'px'});
					break;
				case 's': 
					(pointd).css({top:(so.top + sh - this.pd*2) +'px', left:(so.left + sw/2 - this.pd)  +'px'});
					(point).css({top:(so.top + sh - this.pd) +'px', left:(so.left + sw/2 - this.pd/2)  +'px'});
					break;
				case 'sw':
					(pointd).css({top:(so.top + sh - this.pd*2) +'px', left:(so.left - this.pd*2)  +'px'});
					(point).css({top:(so.top + sh - this.pd) +'px', left:(so.left - this.pd)  +'px'});
					break;
				case 'w': 
					(pointd).css({top:(so.top + sh/2 - this.pd*2) +'px', left:(so.left - this.pd*2)  +'px'});
					(point).css({top:(so.top + sh/2 - this.pd) +'px', left:(so.left - this.pd)  +'px'});
					break;
			}
		
		}
		this.output();
		$('p#scale').html('The image will be scaled: '+(this.scaleFactor()*100)+'% <br />');
		
	
	}
	

	$('img.original').each(function(){
		if($(this).attr('alt') != undefined) {
			var mode = $(this).attr('alt').split(',');
		}
		if (mode != undefined) {
			mode[0] == undefined ? w=0 : w=parseInt(mode[0]);
			mode[1] == undefined ? h=0 : h=parseInt(mode[1]);
		} else {
			w = 0;h = 0;
		}
		iC = new imageCropper(w,h);
	});
	
});