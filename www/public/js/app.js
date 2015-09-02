/**
 * Created by Al on 01/08/2015.
 */


// Current href
var current_url = window.location.href;

// close message
$('.message .close').on('click', function () {
    $(this).closest('.message').fadeOut();
});


// dropdown
$('.ui.dropdown')
    .dropdown({
        transition: 'drop'
    });

// checkbox
$('.ui.checkbox')
    .checkbox()
;

// accordian
$('.ui.accordion')
    .accordion()
;

// mobile nav pop out
$("#mobile-header-nav").click(function () {
    $("#mobile-sidebar")
        .sidebar('toggle')
    ;
});

// scroll to top
$(".totop").click(function () {
    window.scrollTo(0, 0);
});


// SUBMIT BUTTON SPINNER
// Use this on non ajax form submit buttons
// button must have an i tag with icon class
// add submit-spin to ui button class
$('.submit-spin').click(function(){
    $(this).children('i').removeClass(function(){
        return $( this ).prev().attr( "class" );
    }).addClass('notched circle loading icon');
    $(this).addClass('disabled');
});