/* accordion */
$(".accordion-block").click(function () {
    if ($(this).hasClass("accordion-active")) {
        $(".accordion-block").removeClass("accordion-active");
        $(".accordion-text").hide(300);
    } else {
        $(".accordion-block").removeClass("accordion-active");
        $(this).addClass("accordion-active");

        $(".accordion-text").hide(300);
        $(this).find(".accordion-text").show(300);
    }
});


$(".menu-login").click(function () {
    event.preventDefault();

    let $loginPopup = $(".psd-popup.login-popup");
    $(".psd-popup.sign-up-popup").removeClass("overlay-active");

    if ($loginPopup.hasClass("overlay-active")) {
        $loginPopup.removeClass("overlay-active");
    } else {
        $loginPopup.removeClass("overlay-active");
        $loginPopup.addClass("overlay-active");
    }
});

$(".sign-up-trigger").click(function () {
    event.preventDefault();

    let $signUpPopup = $(".psd-popup.sign-up-popup");
    $(".psd-popup.login-popup").removeClass("overlay-active");

    if ($signUpPopup.hasClass("overlay-active")) {
        $signUpPopup.removeClass("overlay-active");
    } else {
        $signUpPopup.removeClass("overlay-active");
        $signUpPopup.addClass("overlay-active");
    }
});

$(".forgot-password-trigger").click(function () {
    event.preventDefault();

    let $forgotPasswordPopup = $(".psd-popup.forgot-password-popup");
    $(".psd-popup.login-popup").removeClass("overlay-active");
    $(".psd-popup.sign-up-popup").removeClass("overlay-active");

    if ($forgotPasswordPopup.hasClass("overlay-active")) {
        $forgotPasswordPopup.removeClass("overlay-active");
    } else {
        $forgotPasswordPopup.removeClass("overlay-active");
        $forgotPasswordPopup.addClass("overlay-active");
    }
});

$(".login__close").click(function () {
    if ($(".psd-popup").hasClass("overlay-active")) {
        $(".psd-popup").removeClass("overlay-active");
    }
});

$(".sign-up__close").click(function () {
    if ($(".psd-popup").hasClass("overlay-active")) {
    	$(document).trigger("popup_closed")
        $(".psd-popup").removeClass("overlay-active");
    }
});

$(document).ready(function () {
    $('select').niceSelect();
});

$(function () {
    $('#form-unit').click(function () {
        event.preventDefault();
        $(this).find('.unit__sub').slideToggle();
    })
});

var uaTwo = window.navigator.userAgent;
var isIETwo = /MSIE|Trident/.test(uaTwo);

if (isIETwo) {
    document.documentElement.classList.add('ie');
}
