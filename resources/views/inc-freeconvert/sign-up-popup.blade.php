<div class="psd-popup sign-up-popup">
    <div class="sign-up">
        <div class="sign-up__close">
            <img src="{{ asset('freeconvert/img/popup-close-small.svg') }}" width="21" height="20" alt="close">
        </div>
        <div class="sign-up__header">
            <h3 class="sign-up__h">{{ t('Sign up') }}</h3>
        </div>
        <div class="sign-up__content">
            <span class="text-danger" data-field="email"></span>
            <input type="email" class="sign-up__input" name="email" placeholder="{{ t('Your Email') }}">
            <span class="text-danger" data-field="password"></span>
            <input type="password" class="sign-up__input" name="password" placeholder="{{ t('Your Password') }}">
            <span class="text-danger" data-field="name"></span>
            <input type="text" class="sign-up__input" name="name" placeholder="{{ t('Your Name') }}">

            <a href="#" class="sign-up__sign" id="register">{{ t('Sign up') }}</a>
        </div>
    </div>
</div>