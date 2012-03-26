/*
	This is a modified version of Lightbox
	
	Lightbox JS: Fullsize Image Overlays 
	by Lokesh Dhakar - http://huddletogether.com/projects/lightbox/

	Licensed under the Creative Commons Attribution 2.5 License - http://creativecommons.org/licenses/by/2.5/
	(basically, do anything you want, just leave my name and link)
*/

var zoomAnimationWaitTimer = null, zoomAnimationTimer = null, zoomAnimationFrame = 0;
var isFirstZoom = true;

function getScrollY() {
	if (self.pageYOffset) { return self.pageYOffset; }
	else if (document.documentElement && document.documentElement.scrollTop) { return document.documentElement.scrollTop; }
	else if (document.body) { return document.body.scrollTop; }
	return 0;
}

function getPageSize() {
	
	var xScroll, yScroll;
	
	if (window.innerHeight && window.scrollMaxY) {	
		xScroll = document.body.scrollWidth;
		yScroll = window.innerHeight + window.scrollMaxY;
	} else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
		xScroll = document.body.scrollWidth;
		yScroll = document.body.scrollHeight;
	} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
		xScroll = document.body.offsetWidth;
		yScroll = document.body.offsetHeight;
	}
	
	var windowWidth, windowHeight;
	if (self.innerHeight) {	// all except Explorer
		windowWidth = self.innerWidth;
		windowHeight = self.innerHeight;
	} else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
		windowWidth = document.documentElement.clientWidth;
		windowHeight = document.documentElement.clientHeight;
	} else if (document.body) { // other Explorers
		windowWidth = document.body.clientWidth;
		windowHeight = document.body.clientHeight;
	}	
	
	pageHeight = (yScroll < windowHeight) ? windowHeight : yScroll;
	pageWidth = (xScroll < windowWidth) ? windowWidth : xScroll;

	arrayPageSize = new Array(pageWidth,pageHeight,windowWidth,windowHeight) 
	return arrayPageSize;
}

function pause(numberMillis) {
	var now = new Date();
	var exitTime = now.getTime() + numberMillis;
	while (true) {
		now = new Date();
		if (now.getTime() > exitTime)
			return;
	}
}

function zoomKeyDown(e) {
	var Esc = (window.event) ? 27 : e.DOM_VK_ESCAPE;
	var c = (window.event) ? event.keyCode : e.keyCode;
	if (c == Esc) { hideZoom(); }
}

//
// showZoom()
// Preloads images. Places new image in lightbox then centers and displays.
//
function showZoom(objLink)
{
	// prep objects
	var overlay = document.getElementById('overlay');
	var zoomer = document.getElementById('zoom');
	var zoomedImage = document.getElementById('zoom-image');
	var loadIndicator = document.getElementById('zoom-load');
	
	var arrayPageSize = getPageSize();
	var scrollY = getScrollY();

	// set height of Overlay to take up whole page and show
	overlay.style.height = (arrayPageSize[1] + 'px');
	overlay.style.display = 'block';

	// center loadingImage if it exists
	if (loadIndicator) {
		loadIndicator.style.top = (scrollY + ((arrayPageSize[3] - 35 - 48) / 2) + 'px');
		loadIndicator.style.left = (((arrayPageSize[0] - 20 - 48) / 2) + 'px');
	}

	// Preload image
	var zoomPreload = new Image();
	zoomPreload.onload = function() {

		// No need to animate anymore
		clearInterval(zoomAnimationWaitTimer);
		clearInterval(zoomAnimationTimer);
		if (loadIndicator)
			loadIndicator.style.display = 'none';

		// Center zoomed image
		var y = scrollY + ((arrayPageSize[3] - 35 - zoomPreload.height) / 2);
		var x = ((arrayPageSize[0] - 20 - zoomPreload.width) / 2);
		zoomer.style.top = (y < 0) ? '0' : y + 'px';
		zoomer.style.left = (x < 0) ? '0' : x + 'px';
		
		// Show/hide caption
		var captionContainer = document.getElementById('zoom-captioncontainer');
		if (captionContainer != null) {
			if (objLink.getAttribute('title')) {
				captionContainer.style.display = 'block';
				document.getElementById('zoom-caption').innerHTML = objLink.getAttribute('title');
			} else
				captionContainer.style.display = 'none';
		}

		// After image is loaded, update the overlay height as the new image might have increased the overall page height.
		arrayPageSize = getPageSize();
		overlay.style.height = (arrayPageSize[1] + 'px');
		
		// Listen to escape
		document.onkeypress = zoomKeyDown;
		
		// Actually set the image
		if (navigator.appVersion.indexOf("MSIE")!=-1) // Small pause between the image loading and displaying prevents flicker in IE.
			pause(250);
		
		zoomedImage.src = objLink.href;
		zoomer.style.display = 'block';

		return false;
	}


	if (document.getElementById('zoom-load') != null) {
		zoomAnimationWaitTimer = setInterval(delayAnimateLoad, 500);
	}
		
	// Show the zoomer somewhere offscreen to preload its images
	if (isFirstZoom == true) {
		zoomer.style.top = '-10000px';
		zoomer.style.display = 'block';
		isFirstZoom = false;
	}
	
	zoomPreload.src = objLink.href;
}

function delayAnimateLoad()
{
	clearInterval(zoomAnimationWaitTimer);
	document.getElementById('zoom-load').style.display = 'block';
			
	zoomAnimationTimer = setInterval(animateLoad, 66);
}

function animateLoad()
{
	var loadIndicator = document.getElementById('zoom-load');
	loadIndicator.style.backgroundPosition = '0 -'+(zoomAnimationFrame * 48)+'px';
	zoomAnimationFrame = (zoomAnimationFrame + 1) % 12;
}

function hideZoom()
{
	clearInterval(zoomAnimationWaitTimer);
	clearInterval(zoomAnimationTimer);
	var loadIndicator = document.getElementById('zoom-load');
	if (loadIndicator != null)
		loadIndicator.style.display = 'none';
	document.getElementById('overlay').style.display = 'none';
	document.getElementById('zoom').style.display = 'none';
	document.onkeypress = '';
}

function setupZoom()
{
	if (!document.getElementsByTagName) { return; }
	
	// First, load the Zooming style sheet
	var zoomStyleSheet = document.createElement("link");
	zoomStyleSheet.setAttribute('rel','stylesheet');
	zoomStyleSheet.setAttribute('type','text/css');
	zoomStyleSheet.setAttribute('href','/matrix_engine/datatypes/extra_zoomimage/resources/zoom.css');
	var head = document.getElementsByTagName("head").item(0);
	head.appendChild(zoomStyleSheet);
	
	// Now, find all anchors that are zoomable
	var anchors = document.getElementsByTagName("a");
	for (var i=0; i<anchors.length; i++) {
		var anchor = anchors[i];
		if (anchor.getAttribute("href") && (anchor.getAttribute("rel") == "zoom")){
			anchor.onclick = function () { showZoom(this); return false; }
		}
	}
	
	var objBody = document.getElementsByTagName("body").item(0);
	
	// create overlay div and hardcode some functional styles (aesthetic styles are in CSS file)
	var overlay = document.createElement("div");
	overlay.setAttribute('id','overlay');
	overlay.onclick = function () {hideZoom(); return false;}
	overlay.style.display = 'none';
	overlay.style.position = 'absolute';
	overlay.style.top = '0';
	overlay.style.left = '0';
	overlay.style.zIndex = '90';
 	overlay.style.width = '100%';
	objBody.appendChild(overlay);
	
	var arrayPageSize = getPageSize();

	// Preload and create loader image
	var loadPreloader = new Image();	
	loadPreloader.onload=function(){

		var objLoadingImageLink = document.createElement("a");
		objLoadingImageLink.setAttribute('href','#');
		objLoadingImageLink.onclick = function () {hideZoom(); return false;}
		overlay.appendChild(objLoadingImageLink);
		
		var loadIndicator = document.createElement("span");
		loadIndicator.setAttribute('id','zoom-load');
		loadIndicator.style.position = 'absolute';
		loadIndicator.style.zIndex = '150';
		objLoadingImageLink.appendChild(loadIndicator);

		loadPreloader.onload=function(){};	//	clear onLoad, as IE will flip out w/animated gifs

		return false;
	}

	loadPreloader.src = '/matrix_engine/datatypes/extra_zoomimage/resources/ZoomProgress.png';
	
	// Create the shadow elements (someone would get a heart attack if this was in the regular HTML)
	var zoomContainer = document.createElement("div");
	zoomContainer.setAttribute('id','zoom');
	objBody.insertBefore(zoomContainer, overlay.nextSibling);
	
	// Top shadow
	var topShadow = document.createElement("div");
	topShadow.setAttribute('class','top');
	topShadow.appendChild(document.createElement("div"));
	zoomContainer.appendChild(topShadow);
	
	// Inner shadow
	var innerOne = document.createElement("div");
	var innerTwo = document.createElement("div");
	var innerThree = document.createElement("div");
	var contentContainer = document.createElement("div");
	innerOne.setAttribute('class','i1');
	innerTwo.setAttribute('class','i2');
	innerThree.setAttribute('class','i3');
	contentContainer.setAttribute('id','zoom-content');
	
	innerThree.appendChild(contentContainer);
	innerTwo.appendChild(innerThree);
	innerOne.appendChild(innerTwo);
	zoomContainer.appendChild(innerOne);
		
	// Bottom shadow
	var bottomShadow = document.createElement("div");
	bottomShadow.setAttribute('class','bottom');
	bottomShadow.appendChild(document.createElement("div"));
	zoomContainer.appendChild(bottomShadow);
	
	// Close button
	var closeButton = document.createElement("a");
	closeButton.setAttribute('id','zoom-close');
	closeButton.setAttribute('href','#');
	closeButton.innerText = 'Close';
	closeButton.onclick = function () { hideZoom(); return false; }
	contentContainer.appendChild(closeButton);
	
	// create link
	var objLink = document.createElement("a");
	objLink.setAttribute('href','#');
	objLink.setAttribute('title','Click to close');
	objLink.onclick = function () { hideZoom(); return false; }
	contentContainer.appendChild(objLink);

	// create image
	var objImage = document.createElement("img");
	objImage.setAttribute('id','zoom-image');
	objLink.appendChild(objImage);
	
	// create caption
	var captionContainer = document.createElement("div");
	captionContainer.setAttribute('id','zoom-captioncontainer');
	var innerCaptionOne = document.createElement("div");
	var innerCaptionTwo = document.createElement("div");
	
	var objCaption = document.createElement("span");
	objCaption.setAttribute('id','zoom-caption');
	
	contentContainer.appendChild(captionContainer);
	captionContainer.appendChild(innerCaptionOne);
	innerCaptionOne.appendChild(innerCaptionTwo);
	innerCaptionTwo.appendChild(objCaption);
}

function addLoadEvent(func)
{	
	var oldonload = window.onload;
	if (typeof window.onload != 'function') {
		window.onload = func;
	} else {
		window.onload = function() { oldonload(); func(); }
	}
}

// Initalize zooming
addLoadEvent(setupZoom);
