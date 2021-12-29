var $ = jQuery.noConflict();

$(document).ready(function () {
    $('.languagepicker .language-link').click(function (e) {
        //e.preventDefault();
        var result = $(this).clone();
        $('.language-active').html(result);
    });

    var docSelect = $('#docSelect');

    $('#docSelectBtn').click(function (e) {
        $('#docSelect').toggleClass("active");
    });

    $('#docSelectBtn2').click(function (e) {
        $('#docSelect2').toggleClass("active");
    });

    $('.select_wrapper .select_item').click(function(){
        $(this).closest('.select_wrapper').toggleClass("active");
    });
});
