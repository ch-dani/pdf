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

let shouldWait15mins = true;
$(document).on("start_task", function (e) {
    $(".tool_section").addClass("hidden");
    $(".after_upload").addClass("hidden");
    $(".before_upload").addClass("hidden");
    $("#wait_conversion").removeClass("hidden");
    window.scrollTo(0, 0);

    $.ajax({
        url: '/check-should-wait',
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            if (data.status == 'success') {
                shouldWait15mins = false;
            }
        }
    });
});

$(document).on("after_save_file", function (e, data) {
    if (shouldWait15mins) { 
        var fiveMinutes = 15 * 1;
       // var fiveMinutes = 60 * 0.1;
        
        var display = document.querySelector('.timer');

        if (display) {
            startTimer(fiveMinutes, display);
        }
        /*
        setInterval(function() {
            $("#wait_conversion").addClass("hidden");
            $(".download_block").removeClass("hidden");
            $(".result_link_here").attr("href", data.url);
            $('.gc-bubbleDefault').closest('div').css('display', 'none');
        // }, 15 * 60 * 1000);
        // }, 0.1 * 60 * 1000);
        }, 1 * 60 * 1000);
        */
        $(".result_link_here").attr("href", data.url);
        

    } else {
        $("#wait_conversion").addClass("hidden");
        $(".download_block").removeClass("hidden");
        $(".result_link_here").attr("href", data.url);
        $('.gc-bubbleDefault').closest('div').css('display', 'none');
    }
});

//function renderSaveToDrive(src, filename, sitename) {
//    gapi.savetodrive.render('savetodrive-div', {
//        src: src,
//        filename: filename,
//        sitename: sitename
//    });
//}

