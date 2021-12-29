var paypal_inited = false;
$(function () {

	$(document).on("popup_closed", function(){
		$("#paypal-button-container").html("");
	});
    // Click on subscribe button
    $(".purchase-subscription-trigger").click(function () {
        event.preventDefault();

        let $purchaseSubscriptionPopup = $(".psd-popup.subscription-purchase-popup");
        let subscriptionPlanId = $(this).data('plan');
		
		showLoader();

        $.ajax({
            url: '/purchase-subscription/plans/' + subscriptionPlanId,
            type: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function (response) {
            	hideLoader();
                if (response.status === 'success') {
                    $(".psd-popup.login-popup").removeClass("overlay-active");
                    $(".psd-popup.sign-up-popup").removeClass("overlay-active");

                    $purchaseSubscriptionPopup.find('span.sign-up__period').html(response.data.name);
                    $purchaseSubscriptionPopup.find('span.sign-up__price').html(response.data.price + ' USD');
                    $purchaseSubscriptionPopup.find('div.sign-up__then').html('Then ' + response.data.price + ' USD/' + response.data.interval_unit);
                    $purchaseSubscriptionPopup.find('input[name="subscription_plan_id"]').val(subscriptionPlanId);

                    if ($purchaseSubscriptionPopup.hasClass("overlay-active")) {
                        $purchaseSubscriptionPopup.removeClass("overlay-active");
                    } else {
                        $purchaseSubscriptionPopup.removeClass("overlay-active");
                        $purchaseSubscriptionPopup.addClass("overlay-active");
                    }

//					if(paypal_inited){
//						return ;
//					}
					paypal_inited = true;


                    // PayPal init
                    paypal.Buttons({
                        createOrder: function (data, actions) {
                            return actions.order.create({
                                purchase_units: [{
                                    amount: {
                                        value: response.data.price
                                    }
                                }]
                            });
                        },
                        style: {
                            layout: 'horizontal',
                            size: 'large',
                            height: 55,
                            color: 'white',
                            shape: 'pill',
                            tagline: false,
                        },
                        onApprove: function (data, actions) {
                            return actions.order.capture().then(function (details) {
                                $.ajax({
                                    url: '/purchase-subscription/step-3/pay-by-paypal',
                                    type: 'POST',
                                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                    data: {
                                        payment_details: details
                                    },
                                    success: function (response) {
                                        if (response.status === 'success') {
                                            swal.fire('Success!', response.message, 'success');
                                            window.setTimeout(function () {
                                                location.reload();
                                            }, 2000);
                                        }
                                    },
                                    error: function (response) {
                                        swal.fire('Error', response.message, 'error');
                                    }
                                });
                            });
                        },
                        onError: function(err){
                        	alert("Error: Order could not be captured");
                        
                        }
                    }).render('#paypal-button-container');
                }
            },
        });
    });

    // Step 1 - account credentials
    $('#stepAccountCredentials .next-step').click(function (e) {
        e.preventDefault();

        let $emailField = $('#stepAccountCredentials input[name="email"]');
        let $passwordField = $('#stepAccountCredentials input[name="password"]');
        let $subscriptionPlanIdField = $('#stepAccountCredentials input[name="subscription_plan_id"]');

        $('#stepAccountCredentials span.text-danger').html('');

        $.ajax({
            url: '/purchase-subscription/step-1/credentials',
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {
                email: $emailField.val(),
                password: $passwordField.val(),
                subscription_plan_id: $subscriptionPlanIdField.val(),
            },
            success: function (response) {
                if (response.status === 'success') {
                    $('#stepAccountCredentials').hide();
                    $('#stepLocation').show();
                }
            },
            error: function (response) {
                let errorMessage = '';

                if (response.status === 422) {
                    let errors = $.parseJSON(response.responseText).errors;
                    $.each(errors, function (key, val) {
                        $('#stepAccountCredentials span[data-field="' + key + '"]').html(val);
                    });
                } else {
                    $('#stepAccountCredentials span[data-field="email"]').html($.parseJSON(response.responseText).message);
                }
            }
        });
    });

    // Step 2 - location
    $('#stepLocation .next-step').click(function (e) {
        e.preventDefault();

        $('#stepLocation span.text-danger').html('');

        $.ajax({
            url: '/purchase-subscription/step-2/location',
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {
                location: $('#stepLocation select[name="location"]').val(),
            },
            success: function (response) {
                if (response.status === 'success') {
                    $('#stepLocation').hide();
                    $('#stepPaymentOptions').show();
                }
            },
            error: function (response) {
                let errorMessage = '';

                if (response.status === 422) {
                    let errors = $.parseJSON(response.responseText).errors;
                    $.each(errors, function (key, val) {
                        $('#stepLocation span[data-field="' + key + '"]').html(val);
                    });
                }
            }
        });
    });

    // Step 3 - pay by card
    $('#stepPaymentOptions .next-step').click(function (e) {
        e.preventDefault();

        $('#stepPaymentOptions').hide();
        $('#stepPayByCard').show();
    });

    // Step 4 - pay by card:
    // pay by card
    $('#payByCard').click(function (e) {
        e.preventDefault();
        showLoader();
        

        $('#stepPayByCard span[data-field="*"], #stepPayByCard span[data-action="error-message"]').html('');

        $.ajax({
            url: '/purchase-subscription/step-4/pay-by-card',
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {
                card_number: $('#stepPayByCard input[name="card_number"]').val(),
                cardholder_name: $('#stepPayByCard input[name="cardholder_name"]').val(),
                card_mm: $('#stepPayByCard input[name="card_mm"]').val(),
                card_yyyy: $('#stepPayByCard input[name="card_yyyy"]').val(),
                card_cvv: $('#stepPayByCard input[name="card_cvv"]').val(),
            },
            success: function (response) {
            	hideLoader();
                if (response.status === 'success') {
                    swal.fire('Success!', response.message, 'success');
                    window.setTimeout(function () {
                        location.reload();
                    }, 2000);
                }
            },
            error: function (response) {
            	hideLoader();
                if (response.status === 422) {
                    let errors = $.parseJSON(response.responseText).errors;
                    $.each(errors, function (key, val) {
                        $('#stepPayByCard span[data-field="' + key + '"]').html(val + ' ');
                    });
                } else {
                    $('#stepPayByCard span[data-action="error-message"]').html($.parseJSON(response.responseText).message);
                }
            }
        });
    });

    // change payment method
    $('#changePaymentMethod').click(function (e) {
        e.preventDefault();

        $('#stepPayByCard').hide();
        $('#stepPaymentOptions').show();
    });

    // Pay for subscription popup
    $('.pay-for-subscription-trigger').click(function (e) {
        e.preventDefault();

        let $payForSubscriptionPopup = $(".psd-popup.pay-for-subscription-popup");

        if ($payForSubscriptionPopup.hasClass("overlay-active")) {
            $payForSubscriptionPopup.removeClass("overlay-active");
        } else {
            $payForSubscriptionPopup.removeClass("overlay-active");
            $payForSubscriptionPopup.addClass("overlay-active");
        }
    });

    // Pay for subscription
    $('#payForSubscription').click(async function (e) {
        e.preventDefault();

        $('.pay-for-subscription-popup span[data-field="*"], .pay-for-subscription-popup span[data-action="error-message"]').html('');
		showLoader();
		
        await $.ajax({
            url: '/subscription/pay',
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {
                card_number: $('.pay-for-subscription-popup input[name="card_number"]').val(),
                cardholder_name: $('.pay-for-subscription-popup input[name="cardholder_name"]').val(),
                card_mm: $('.pay-for-subscription-popup input[name="card_mm"]').val(),
                card_yyyy: $('.pay-for-subscription-popup input[name="card_yyyy"]').val(),
                card_cvv: $('.pay-for-subscription-popup input[name="card_cvv"]').val(),
            },
            success: function (response) {
            	hideLoader();
                if (response.status === 'success') {
                    swal.fire('Success!', response.message, 'success');
                    window.setTimeout(function () {
                        location.reload();
                    }, 2000);
                }
            },
            error: function (response) {
            	hideLoader();
                if (response.status === 422) {
                    let errors = $.parseJSON(response.responseText).errors;
                    $.each(errors, function (key, val) {
                        $('.pay-for-subscription-popup span[data-field="' + key + '"]').html(val + ' ');
                    });
                } else {
                    $('.pay-for-subscription-popup span[data-action="error-message"]').html($.parseJSON(response.responseText).message);
                }
            }
        });
        
        
        
    });
});


function showLoader(){
	$("body").prepend(`
		<div class='cloader'>
			<div class="lds-circle"><div></div></div>
		</div>
	`)
}


function hideLoader(){
	$(".cloader").remove();
}

