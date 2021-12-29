@extends('layouts.layout')

@section('content-freeconvert')
    <section class="contacts">
        <h2 class="profile_h">{{ t('Contacts') }}</h2>
        <h3 class="profile_sub-title">{{ t('Use the form below to contact our support. We will get back in touch with you soon!') }}</h3>
        <div class="contacts__item">
            <input type="text" class="sign-up__input" name="name" placeholder="{{ t('Your Name') }}">
            <input type="email" class="sign-up__input" name="email" placeholder="{{ t('Your Email') }}">
            <textarea class="sign-up__text" name="message" placeholder="{{ t('Your Message') }}"></textarea>
            <a href="#" class="sign-up__sign" id="sendMessage">{{ t('Send Message') }}</a>
            <div class="profile__icons">
                <a href="#" class="profile__icon">
                    <img src="{{ asset('freeconvert/img/icon-twitter.svg') }}" width="20" height="18" alt="twitter">
                </a>
                <a href="#" class="profile__icon">
                    <img src="{{ asset('freeconvert/img/icon-instagram.svg') }}" width="20" height="20" alt="instagram">
                </a>
                <a href="#" class="profile__icon">
                    <img src="{{ asset('freeconvert/img/icon-facebook.svg') }}" width="10" height="20" alt="facebook">
                </a>
                <a href="#" class="profile__icon">
                    <img src="{{ asset('freeconvert/img/icon-youtube.svg') }}" width="20" height="14" alt="youtube">
                </a>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script>
        $(function () {
            $('#sendMessage').click(function (e) {
                e.preventDefault();

                let $nameField = $('.contacts__item input[name="name"]');
                let $emailField = $('.contacts__item input[name="email"]');
                let $messageField = $('.contacts__item textarea[name="message"]');
                let nameValue = $nameField.val();
                let emailValue = $emailField.val();
                let messageValue = $messageField.val();

                $.ajax({
                    url: '/contact',
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        name: nameValue,
                        email: emailValue,
                        message: messageValue,
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            swal.fire('Success', response.message, 'success');

                            $nameField.val('');
                            $emailField.val('');
                            $messageField.val('');
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
                            errorMessage = 'System error occurred. Please try later';
                        }

                        swal.fire('Error!', errorMessage, 'error');
                    }
                });
            });
        });
    </script>
@endsection