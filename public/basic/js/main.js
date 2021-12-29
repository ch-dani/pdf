var $ = jQuery.noConflict();

$(document).ready(function () {
    var uaTwo = window.navigator.userAgent;
    var isIETwo = /MSIE|Trident/.test(uaTwo);

    if (isIETwo) {
        document.documentElement.classList.add('ie');
    }

    $('.burger_menu').on('click', function () {
        $('.module__main-menu').toggleClass('open');
        $('body').toggleClass('body_overflow');
    });
});
