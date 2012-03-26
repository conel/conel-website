//------- these functions are used to call the window --------//
	function box_selectItem(mID) {
		// first we check the field for a certain id number
		targetfield =document.linkItemList.linkList_Selected.value;
			
		// if there was an item select we have to deselect it
		if(targetfield > 0) {
			box_change(targetfield,'#BBBBBB');
		}
			
		// now select the new item
		box_change(mID,'#999999');
		
		// and finally we will set the itemID field to the current ITEM
		document.linkItemList.linkList_Selected.value=mID;
	}
		
	function box_addLink(mURL,mName) {
			document.main.op.value = "editor";
			// now open the window, and then fresh the page
			wm_openWindow(mURL,mName,'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=460,height=340');
			document.main.submit();
		}
	
	function box_editLink(mURL,mName) {
			document.main.op.value = "editor";
			
			targetItem =document.linkItemList.linkList_Selected.value;
			// now open the window, and then fresh the page
			wm_openWindow(mURL+'&linkItemID='+targetItem,mName,'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=460,height=340');
			document.main.submit();
		}

		function box_change(mID, color) {
    			if (document.layers) {
        			window.document.layers['id'+mID+'b_box'].bgColor = color;

        		} else if (document.all) {
        			window.document.all['id'+mID+'b_box'].style.background = color;
        		}
		}		

	function box_deleteLink(mURL) {	
		
		currentItem = document.linkItemList.linkList_Selected.value;
		mURL = mURL + "&item_id="+currentItem;
			
		if(currentItem > 0) {
			// the wen ask the user if he really wnats to delete this item
			wm_openWindow(mURL,'deleteLink','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=460,height=340');			
		}
	}
