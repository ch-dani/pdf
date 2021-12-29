(function(){
	$(document).ready(function () {

        $('#ChangePassword').on('click', function() {
            $.ajax({
                type: 'POST',
                url: '/account/change_password',
                data: $('#ChangePasswordForm').serialize(),
                success: function(data) {
                    if (data.status == 'success') {
                        swal('Success', '', 'success');
                        $('input[name="current_password"]').val('');
                        $('input[name="new_password"]').val('');
                        $('input[name="new_password_confirmation"]').val('');
                    } else {
                        swal('Error', data.message, 'error');
                    }
                }
            });

            return false;
        });

        $('#sendMessageBtn').on('click', function() {
            $.ajax({
                type: 'POST',
                url: '/contact_form',
                data: $('#contactForm').serialize(),
				before: function () {
                    $('#contactForm input[name="allowAccessLastTask"]').prop('disabled', true);
                    $('#contactForm textarea').prop('disabled', true);
                    $('#sendMessageBtn').prop('disabled', true);
                    $('#contactForm').css('opacity', '0.5');
                },
				complete: function () {
                    $('#contactForm input[name="allowAccessLastTask"]').prop('disabled', false);
                    $('#contactForm textarea').prop('disabled', false);
                    $('#sendMessageBtn').prop('disabled', false);
                    $('#contactForm').css('opacity', '1');
                },
                success: function(data) {
                    if (data.status == 'success') {
                        $('#contactForm').hide('fast');
                        $('#contactSuccess').show('fast');

                        $('#contactForm textarea').val('');
                        $('#contactForm input[name="allowAccessLastTask"]').prop('checked', false);
                    } else {
                        swal('Error', data.message, 'error');
                    }
                }
            });

            return false;
        });

		//SCROLL TOP

		var scroll = 0,
		    scrollTop = $('#scroll-top'),
		    winWidth = window.innerWidth;

		$(window).scroll(function () {
		    var $this = $(this),
		        scroll = $this.scrollTop();

		    if (scroll > 150) {
		        scrollTop.addClass('active');
		        //$("header").addClass("header-active");
		    } else if (scroll < 100) {
		        scrollTop.removeClass('active');
		        //$("header").removeClass("header-active");
		    }

		});

		$(window).resize(function() {
		    winWidth = window.innerWidth;
		});


		scrollTop.click(function (e) {
		    e.preventDefault();
		    $('html, body').animate({
		        scrollTop: 0
		    }, 1000);
		});

        /* language */

        $('.SelectLanguage').on('click', function() {
//            if (typeof $(this).data('id')) {
//                $.cookie('lang_id', $(this).data('id'), { expires: 365, path: '/' });
//                window.location.reload();
//            }
           // return false;
        });


		/*mob_menu*/
		$(".menu_mob").click(function () {
		    $(".menu_mob").toggleClass("active_drop_men");
		    if (!$(".menu_mob").hasClass("active_drop_men")) {
		        $(".mega-menu, .overley-bg, .header-menu-block").css("display", "none");
		        //$(".header-menu-block").css("display", "none");
		    }
		    else {
		        $(".header-menu-block").css("display", "flex");
		    }
		});

		/* header drop menu */
		$(".open-menu-btn").click(function () {
		    $(".mega-menu").slideToggle();
		    $(".overley-bg").slideToggle();
		    $("header").toggleClass("header-menu-active");
		});
		$(".close").click(function () {
		    $(".mega-menu").css("display", "none");
		    $(".overley-bg").css("display", "none");
		    $("header").removeClass("header-menu-active");
		});

		$(".open-menu-btn").mouseover(function () {
		    if (winWidth > 768) {
		        $(".mega-menu").show();
		        $(".overley-bg").css("display", "block");
		        $("header").addClass("header-menu-active");
		    }   
		});
		
		$(".overley-bg").click(function () {
		    $(".mega-menu").css("display", "none");
		    $(".overley-bg").css("display", "none");
		    $("header").removeClass("header-menu-active");
		});
		/* SELECT */
		function formatState(state) {
		    if (!state.id) {
		        return state.text;
		    }
		    var baseUrl = "img/";
		    var $state = $(
		        '<span><img src="' + baseUrl + '/' + state.element.value.toLowerCase() + '.png" class="img-flag" /> ' + state.text + '</span>'
		    );

		    return $state;
		};

		$(".js-example-templating").select2({
		    templateResult: formatState
		});
		$(".js-example-templating").change(function () {
		    var flag = $(".select2-selection__rendered").attr("title").toLowerCase();
		    $(".select-img").attr("src", 'img/' + flag + '.png');
		});

		/* tab pricing */
		$(".plans-btn-block").click(function () {
		    $(".plans-btn-block ").removeClass("plans-btn-active");
		    $(this).addClass("plans-btn-active");
		    $(".plans-tab-block").hide();
		    $(".plans-tab-block").eq($(this).index()).show();
		});

		/* accordion */
//		$(".accordion-block").click(function () {
//		    if ($(this).hasClass("accordion-active")) {
//		        $(".accordion-block").removeClass("accordion-active");
//		        $(".accordion-text").hide(500);
//		    }
//		    else {
//		        $(".accordion-block").removeClass("accordion-active");
//		        $(this).addClass("accordion-active");

//		        $(".accordion-text").hide(500);
//		        $(this).find(".accordion-text").show(500);
//		    }
//		});


		/* tab pdf */
		$(".tab-btn-block").click(function () {
		    $(".tab-btn-block ").removeClass("tab-active-btn");
		    $(this).addClass("tab-active-btn");
		    $(".tab-block").hide();
		    $(".tab-block").eq($(this).index()).show();
		});


		/* sign modal*/
		$(".login-btn").click(function (e) {
		    e.preventDefault();
		    $(".login-modal").show();
		});
		$("#closeModal").click(function (e) {
		    $(".login-modal").hide();
		});

        /* alternate mix btns*/
        $(".order-edit-btn").click(function (e) {
            e.preventDefault();
            $(".order-edit-btn").removeClass("order-btn-active");
            $(this).addClass("order-btn-active");
        });

        /* encrypt btns*/
        $(".encrypt-btn-name").click(function (e) {
            $(".encrypt-btn-name").removeClass("encrypt-btn-active");
            $(this).addClass("encrypt-btn-active");
        });

        /* resize margin block*/
        $(".resize-margin-block").click(function () {
            $(this).toggleClass("resize-margin-active");
        });

        /* output btn block*/
        $(".output-btn").click(function () {
        	$(this).closest(".output-formats-btns").find(".output-btn-active").removeClass("output-btn-active");
        	$(this).addClass("output-btn-active");
        });
	});
})(jQuery);



var popuper = {

	open: function(target, event, popup) {
		$(target).on(event, function(e) {
			e.preventDefault();
			$(popup).css('display', 'flex');
		});
	},

	close: function (target, event, popup) {
		$(target).on(event, function (e) {
			e.preventDefault();
			$(popup).css('display', 'none');
		});
	},

	justOpen: function(popup) {
		$(popup).css('display', 'flex');
	},

	justClose: function(popup) {
		$(popup).css('display', 'none');
	}
}




/* rating */
var rate = function(rate){
	if(!rate){
		rate = 5;
	}

	$.ajax({
		method: "POST",
		url: "/update-rating",
		data: {_token: $("#editor_csrf").val(), url: window.location.pathname, rate: rate},
		dataType: "json",
		success: function(data){
			if(data.success) {
				// swal("Success", data.message, "success");

				$('.rating .ratingValue').text(data.rate.rate);
				$('.rating .ratingCount').text(data.rate.count);
				$('.rating .rating-title').text('Your rate');
			}else{
				swal("Error", data.message, "error");
			}
		},
		error: function(error){
			console.log(error);
			alert("Error");
		}
	});
}

jQuery(document).ready(function($){

	if($(".s-translate").length>0){
		$(".rating").insertAfter(".s-translate")
	}else{
		$(".rating").insertAfter(".upload-top-info:eq(0)")
	}

//	$(".rating").insertAfter(".upload-top-info:eq(0)")

	$('.rating input').change(function(){
		var rating = $(".rating input:checked").val();
		console.log(rating);

		rate(rating);
	});

	$('.rating .close-rating').click(function(){
		$(this).closest('.rating').hide();
	});
});

