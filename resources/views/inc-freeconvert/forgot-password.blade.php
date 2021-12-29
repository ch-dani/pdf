<div class="psd-popup forgot-password-popup">
    <div class="sign-up">
        <div class="sign-up__close">
            <img src="{{ asset('freeconvert/img/popup-close-small.svg') }}" width="21" height="20" alt="close">
        </div>
        <div class="sign-up__header">
            <h3 class="sign-up__h">{{ t('Reset Password') }}</h3>
        </div>
        <div class="sign-up__content">
            <span class="text-danger" data-field="email"></span>
            <span class="text-success" data-action="success" style="display: none">Reset password email was sent</span>
            <input type="email" class="sign-up__input" name="email" placeholder="{{ t('Your Email') }}">

            <a href="#" class="sign-up__sign next-step" id="resetPassword">{{ t('Reset') }}</a>
        </div>
    </div>
</div>