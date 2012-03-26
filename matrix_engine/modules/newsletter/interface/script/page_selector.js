//------- these functions are used to call the window --------//
	function linklist_selectItem(mID) {
		// first we check the field for a certain id number
		targetfield =document.linkItemList.linkList_Selected.value;
			
		// if there was an item select we have to deselect it
		if(targetfield > 0) {
			change(targetfield,'#BBBBBB');
		}
			
		// now select the new item
		change(mID,'#999999');
		
		// and finally we will set the itemID field to the current ITEM
		document.linkItemList.linkList_Selected.value=mID;
	}
		
	function linklist_addLink(mURL) {
			document.main.op.value = "editor";
			// now open the window, and then fresh the page
			wm_openWindow(mURL,'addLink','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=460,height=340');
			document.main.submit();
		}
	
	function linklist_editLink(mURL) {
			document.main.op.value = "editor";
			
			targetItem =document.linkItemList.linkList_Selected.value;
			// now open the window, and then fresh the page
			wm_openWindow(mURL+'&linkItemID='+targetItem,'addLink','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=460,height=340');
			document.main.submit();
		}
		


//------- these are the general layer functions --------//
function submitSelectedPage(selected_page) {	
	// first we set the selected page item
	//alert(selected_page);
	document.main.selected_page.value = selected_page;
	
	// then we can savly submit this form
	document.main.submit();
}


