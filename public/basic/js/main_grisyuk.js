/* accordion */
$(".accordion-block").click(function () {
    if ($(this).hasClass("accordion-active")) {
        $(".accordion-block").removeClass("accordion-active");
        $(".accordion-text").hide(300);
    }
    else {
        $(".accordion-block").removeClass("accordion-active");
        $(this).addClass("accordion-active");

        $(".accordion-text").hide(300);
        $(this).find(".accordion-text").show(300);
    }
});

$(function() {

    $('#form-unit').click(function() {
        event.preventDefault();
        $(this).find('.unit__sub').slideToggle();
    })
});

var uaTwo = window.navigator.userAgent;
var isIETwo = /MSIE|Trident/.test(uaTwo);

if ( isIETwo ) {
    document.documentElement.classList.add('ie');
}