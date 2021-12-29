@extends('layouts.layout2')

@section('content-freeconvert')
    <section class="profile">
        <div class="profile__item">
            <h2 class="profile_h">{{ t('Account Details') }}</h2>
            <h3 class="profile_sub-title">Email: {{ $user->email }}</h3>
            <a href="#" class="profile__change change-password-trigger">{{ t('Change Password') }}</a>
        </div>
        <div class="profile__item">
            <h2 class="profile_h">{{ t('Your subscription plan') }}</h2>
            @if($user->subscription)
            	<?php 
            	
            	$is_life_time = $user->subscription->subscriptionPlan->interval_unit=='forever';
            	?>
            
                <h3 class="profile_sub-title">
                    {{ $user->subscription->subscriptionPlan->name }}
                    @if(!$is_life_time)
                    	- ${{ $user->subscription->subscriptionPlan->price }}
                   	@endif
                    <br>
                    @if(!$is_life_time)
		                @if($user->subscription->status === 'active')
		                    <span>
		                        {{ t('Next payment date:') }} 
		                        {{ $user->subscription->next_payment_at->format('d.m.Y') }}
		                    </span>
		                @else
		                    <span class="text-danger">
		                        Your subscription expired
		                    </span>
		                @endif
                    @endif
                </h3>
                @if(!$is_life_time)
                @if($user->subscription->next_payment_at->diffInDays(now()) === 1)
                    <a href="#" class="profile__back pay-for-subscription-trigger">{{ t('Pay for subscription') }}</a>
                @endif
                <a href="{{ url('/pricing') }}" class="profile__unsubscribe">{{ t('Change Subscription') }}</a>
                @endif
            @else
                <h3 class="profile_sub-title">{{ t('No subscription') }}</h3>
                <a href="{{ url('/pricing') }}" class="profile__back">{{ t('Purchase subscription') }}</a>
            @endif
            <a href="{{ url('/logout') }}" class="profile__unsubscribe">{{ t('Logout') }}</a>
        </div>
    </section>

    @include('inc-freeconvert.change-password')
    @includeWhen($user->subscription !== null, 'inc-freeconvert.pay-for-subscription-popup')
@endsection

@section('js')
    <script>
        $(function () {
            // Change password popup
            $('.change-password-trigger').click(function (e) {
                e.preventDefault();

                let $changePasswordPopup = $(".psd-popup.change-password-popup");
                $(".psd-popup.pay-for-subscription-popup").removeClass("overlay-active");

                if ($changePasswordPopup.hasClass("overlay-active")) {
                    $changePasswordPopup.removeClass("overlay-active");
                } else {
                    $changePasswordPopup.removeClass("overlay-active");
                    $changePasswordPopup.addClass("overlay-active");
                }
            });

            // Change password
            $('#changePassword').click(function (e) {
                e.preventDefault();

                let $passwordFiled = $('.change-password-popup input[name="password"]');
                let $passwordConfirmationFiled = $('.change-password-popup input[name="password_confirmation"]');

                $('.change-password-popup span[data-field="*"]').html('');

                $.ajax({
                    url: '/account/change-password',
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        password: $passwordFiled.val(),
                        password_confirmation: $passwordConfirmationFiled.val(),
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
                        if (response.status === 422) {
                            let errors = $.parseJSON(response.responseText).errors;
                            $.each(errors, function (key, val) {
                                $('.change-password-popup span[data-field="' + key + '"]').html(val + ' ');
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection
