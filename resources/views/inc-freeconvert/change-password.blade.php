<div class="psd-popup change-password-popup">
    <div class="sign-up">
        <div class="sign-up__close">
            <img src="{{ asset('freeconvert/img/popup-close-small.svg') }}" width="21" height="20" alt="close">
        </div>
        <div class="sign-up__header">
            <h3 class="sign-up__h">{{ t('Change Password') }}</h3>
        </div>
        <div class="sign-up__content">
            <span class="text-danger" data-field="password"></span>
            <input type="password" class="sign-up__input" name="password" placeholder="{{ t('Your New Password') }}">
            <input type="password" class="sign-up__input" name="password_confirmation" placeholder="{{ t('Confirm New Password') }}">

            <a href="#" class="sign-up__sign next-step" id="changePassword">{{ t('Change') }}</a>
        </div>
    </div>
</div>