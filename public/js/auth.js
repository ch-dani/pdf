$(function () {
    // Reset password
    $('#resetPassword').click(async function (e) {
        e.preventDefault();
        showLoader();

        let $successMessage = $('.forgot-password-popup span[data-action="success"]');

        $successMessage.hide();

        await $.ajax({
            url: '/password/send-email',
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {
                email: $('.forgot-password-popup input[name="email"]').val(),
            },
            success: function (response) {
            hideLoader();
                if (response.error === 'false') {
                    $successMessage.show();
                } else {
                    $('.forgot-password-popup span[data-field="email"]').html(response.msg);
                }
            },
            error: function(){
		        hideLoader();
            	alert("error");
            }
        });
    });

    // Login via email & password
    $('#login').click(function (e) {
        e.preventDefault();

        let $defaultLoginErrorMessageField = $('span[data-action="default-login-error"]');

        $defaultLoginErrorMessageField.html('');

        $.ajax({
            url: '/login',
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {
                email: $('#loginSection input[name="email"]').val(),
                password: $('#loginSection input[name="password"]').val(),
            },
            success: function (response) {
                if (response.status === 'success') {
                    location.reload();
                }
            },
            error: function (response) {
                let errorMessage = '';

                if (response.status === 422) {
                    let errors = $.parseJSON(response.responseText).errors;
                    $.each(errors, function (key, val) {
                        errorMessage += val + ' ';
                    });
                } else {
                    errorMessage = 'Can not login. Please try later';
                }

                $defaultLoginErrorMessageField.html(errorMessage);
            }
        });
    });

    // Registration
    $('#register').click(function (e) {
        e.preventDefault();

        let $emailField = $('.sign-up-popup input[name="email"]');
        let $passwordField = $('.sign-up-popup input[name="password"]');
        let $nameField = $('.sign-up-popup input[name="name"]');

        $('.sign-up-popup span.text-danger').html('');

        $.ajax({
            url: '/register',
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {
                email: $emailField.val(),
                password: $passwordField.val(),
                name: $nameField.val(),
            },
            success: function (response) {
                window.setTimeout(function () {
                    location.reload();
                }, 2000);
            },
            error: function (response) {
                if (response.status === 422) {
                    let errors = $.parseJSON(response.responseText).errors;
                    $.each(errors, function (key, val) {
                        $('.sign-up-popup span[data-field="' + key + '"]').html(val + ' ');
                    });
                }
            }
        });
    });
});
