var $ = jQuery.noConflict();

function startTimer(duration, display) {
    var timer = duration, minutes, seconds;
    setInterval(function () {
        minutes = parseInt(timer / 60, 10);
        seconds = parseInt(timer % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        if (minutes == 0 && seconds == 0) {
            // display.textContent = 'few more';

            $("#wait_conversion").addClass("hidden");
            $(".download_block").removeClass("hidden");
           // $(".result_link_here").attr("href", data.url);
            $('.gc-bubbleDefault').closest('div').css('display', 'none');

            return false;
        } else {
            display.textContent = minutes + ":" + seconds;
        }

        if (--timer < 0) {
            timer = duration;
        }
    }, 1000);
}

$(document).ready(function () {
    //SCROLL TOP

    var scroll = 0,
        scrollTop = $('#scroll-top');

    $(window).scroll(function () {
        var $this = $(this),
            scroll = $this.scrollTop();

        if (scroll > 150) {
            scrollTop.addClass('active');
        } else if (scroll < 100) {
            scrollTop.removeClass('active');
        }
    });
    scrollTop.click(function (e) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: 0
        }, 700);
    });
});
