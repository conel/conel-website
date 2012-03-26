function initForm(){
	var inputs = document.getElementsByTagName("input");
	for(var i=0;i<inputs.length;i++){
		if(inputs[i].getAttribute("type") == "image"){
			initBtn(inputs[i]);
		}
		if(inputs[i].getAttribute("type") == "text"){
			if (inputs[i].getAttribute("name") != "keyword") {  
				initText(inputs[i]);
			}
		}
	}
}
function initBtn(mybtn){
	var img = mybtn.getAttribute("src");
	var end = img.indexOf(".gif");
	var start = img.lastIndexOf("/");
	var name = img.substring(start,end);
	//alert(img+" "+ext+" "+name);
	mybtn.onmouseover = function(){
		this.setAttribute("src","/layout/img/"+name+"_h.gif");
	}
	mybtn.onmouseout = function(){
		this.setAttribute("src","/layout/img/"+name+".gif");
	}
}
function initText(myfield){
	var val = myfield.getAttribute("value");
	myfield.onfocus = function(){
		this.setAttribute("value","");
	}
	myfield.onblur = function(){
		if(this.getAttribute("value") == ""){
			this.setAttribute("value",val);
		}
	}
}
window.onload = initForm;