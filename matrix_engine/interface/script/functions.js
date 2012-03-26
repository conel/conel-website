function displayOptions(num,value) {

	var match_text = document.getElementById('match' + num);
	var other_choice = document.getElementById('other' + num);

	if (value == 'n') {
		match_text.style.display='none';
		other_choice.style.display='block';
	} else {
		match_text.style.display='block';
		other_choice.style.display='none';
	}
}
