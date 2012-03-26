//------- these functions are used to call the window --------//
	function portlets_selectItem(mID) {
		// first we check the field for a certain id number
		targetfield =document.container.portlets_Selected.value;

		// if there was an item select we have to deselect it
		if(targetfield > 0) {
			change(targetfield,'#BBBBBB');
		}
			
		// now select the new item
		change(mID,'#999999');
		
		// and finally we will set the itemID field to the current ITEM
		document.container.portlets_Selected.value=mID;
	}

		function change(mID, color) {
    			if (document.layers) {
        			window.document.layers['id'+mID+'b'].bgColor = color;

        		} else if (document.all) {
        			window.document.all['id'+mID+'b'].style.background = color;
        		}
		}
		
	function portlets_addLink(mURL) {
			
			document.main.op.value = "editor";
			// now open the window, and then fresh the page
			
			wm_openWindow(mURL,'addLink','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=460,height=340');
			document.main.submit();
		}
	
	function portlets_editLink(mURL) {
			document.main.op.value = "editor";
			
			targetItem =document.container.portlets_Selected.value;
			// now open the window, and then fresh the page
			wm_openWindow(mURL+'&portletID='+targetItem,'addLink','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=460,height=340');
			document.main.submit();
		}

	function portlets_deleteLink(mURL) {	
		
		currentItem = document.container.portlets_Selected.value;
		mURL = mURL + "&portletID="+currentItem;
			
		if(currentItem > 0) {
			// the wen ask the user if he really wnats to delete this item
			wm_openWindow(mURL,'deleteLink','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=460,height=340');			
		}
	}

		function wm_openWindow(theURL,winName,features) {
  			window.open(theURL,winName,features);
		}