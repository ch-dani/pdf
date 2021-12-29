<div class="psd-popup login-popup">
    <div class="login">
        <div class="login__inner">
            <div class="login__close">
                <img src="{{ asset('freeconvert/img/popup-close.svg') }}" width="21" height="20" alt="close">
            </div>
            <h3 class="login_h">{{ t('Login') }}</h3>

            <div class="login__button">
                <div class="login__button-inner">
                    <a href="{{ route('google-auth') }}" class="login__google" id="loginViaGoogle">
                        <img src="{{ asset('freeconvert/img/flat-color-icons_google.svg') }}" alt="google" width="24"
                             height="25">
                        Google
                    </a>
                </div>
                <div class="login__button-inner">
                    <a href="{{ route('facebook-auth') }}" class="login__facebook" id="loginViaFacebook">
                        <img src="{{ asset('freeconvert/img/cib_facebook.svg') }}" alt="facebook" width="24"
                             height="25">
                        Facebook
                    </a>
                </div>
            </div>

            <div id="loginSection">
                <input type="email" class="login__input" name="email" placeholder="{{ t('Your Email') }}"/>
                <input type="password" class="login__input" name="password" placeholder="{{ t('Your Password') }}"/>
                <span class="text-danger" role="alert" data-action="default-login-error"></span>
                <a href="#" class="login__enter" id="login">{{ t('Login') }}</a>
            </div>

            <div class="login__links">
                <span><a href="#" class="login__link forgot-password-trigger">{{ t('Forgot password?') }}</a></span>
                <span>
                    {{ t('Don\'t have membership?') }}
                    <a href="#" class="login__link sign-up-trigger">{{ t('Sign Up!') }}</a>
                </span>
            </div>
        </div>
    </div>
</div>