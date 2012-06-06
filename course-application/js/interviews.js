function isIE() {
    return (navigator.appName == 'Microsoft Internet Explorer') ? true : false;
}
        
$(document).ready(function() {

    // Date Slide Toggle
    $('a.toggle').click(function(e) {
        e.preventDefault();
        var num = $(this).attr('id').replace('view_', '');
        var toggle_div = '.date_' + num;
        $(toggle_div).slideToggle();
        if ($(this).html() == 'show') {
            $(this).html('hide');
        } else {
            $(this).html('show');
        }
    });
    
    // Centre Slide Toggle
    $('a.toggle_centre').click(function(e) {
        e.preventDefault();
        var unique = $(this).attr('id').replace('centre_', '');
        var toggle_div2 = '.location_' + unique;
        $(toggle_div2).slideToggle();
        if ($(this).html() == 'show') {
            $(this).html('hide');
        } else {
            $(this).html('show');
        }
    });

    // Hide print list on click
    $('a#close_list').click(function(e) {
        e.preventDefault();
        $('#print_list').hide();
    });

    $('a.email').click(function(e) {
        e.preventDefault();
        // Get link
        var mailto = $(this).attr('href');
        if (isIE()) {
            window.open(mailto);
        } else {
            document.location.href = mailto;
        }
    });

});
