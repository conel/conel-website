//------- page_selector --------//
function submitSelectedPage(selected_page) {	
	// the user has clicked on an item
	// we store it into the form field
	// and submit the page to the editor
	document.main.selected_page.value = selected_page;
	
	// then we can savly submit this form
	document.main.submit();
}


