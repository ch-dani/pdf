var $ = jQuery.noConflict();

$(document).ready(function () {
    function startTimer(duration, display) {
        var timer = duration, minutes, seconds;
        setInterval(function () {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);
    
            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;
    
            display.textContent = minutes + ":" + seconds;
    
            if (--timer < 0) {
                timer = duration;
            }
        }, 1000);
    }
    
    var fiveMinutes = 60 * 15,
    display = document.querySelector('.timer');

    if(display) {
        startTimer(fiveMinutes, display);
    }

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
