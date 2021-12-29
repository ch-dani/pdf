var $ = jQuery.noConflict();

$(document).ready(function () {
    // LANGUAGE SWITCH
    $('.languagepicker .language-link').click(function (e) {
        e.preventDefault();
        var result = $(this).clone();
        $('.language-active').html(result);
    });
});

var docSelect = $('#docSelect');

$('#docSelectBtn').click(function (e) {
    $('#docSelect').toggleClass("active");
});