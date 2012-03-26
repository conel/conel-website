//------- these functions are used to call the window --------//
	function container_selectItem(mID) {
		// first we check the field for a certain id number
		targetfield =document.container.container_Selected.value;
			
		// if there was an item select we have to deselect it
		if(targetfield > 0) {
			change(targetfield,'#BBBBBB');
		}
			
		// now select the new item
		change(mID,'#999999');
		
		// and finally we will set the itemID field to the current ITEM
		document.container.container_Selected.value=mID;
	}
		
	function container_addLink(mURL) {
			document.main.op.value = "editor";
			// now open the window, and then fresh the page
			wm_openWindow(mURL,'addLink','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=460,height=340');
			document.main.submit();
		}
	
	function container_editLink(mURL) {
			document.main.op.value = "editor";
			
			targetItem =document.container.container_Selected.value;
			// now open the window, and then fresh the page
			wm_openWindow(mURL+'&containerItemID='+targetItem,'addLink','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=460,height=340');
			document.main.submit();
		}

	function container_deleteLink(mURL) {	
		
		currentItem = document.container.container_Selected.value;
		mURL = mURL + "&containerItemID="+currentItem;
			
		if(currentItem > 0) {
			// the wen ask the user if he really wnats to delete this item
			wm_openWindow(mURL,'deleteLink','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=460,height=340');			
		}
	}
