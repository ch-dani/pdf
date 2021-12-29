<div class="psd-popup subscription-purchase-popup">
    <div class="sign-up" id="stepAccountCredentials">
        <div class="sign-up__close">
            <img src="{{ asset('freeconvert/img/popup-close-small.svg') }}" width="21" height="20" alt="close">
        </div>
        <div class="sign-up__header">
            <h3 class="sign-up__h">{{ t('Purchase') }} <span class="sign-up__period">1 month</span></h3>
            <h4 class="sign-up__sub-title">
                {{ t('The membership for') }} <a href="{{ url('') }}">{{ url('') }}</a>
            </h4>
        </div>
        <div class="sign-up__content">
            <input type="hidden" name="subscription_plan_id" value="">
            <span class="text-danger" data-field="email"></span>
            <input type="email" class="sign-up__input" name="email"
                   value="{{ auth()->check() ? auth()->user()->email : '' }}" placeholder="{{ t('Your Email') }}">
            @if(auth()->check() && auth()->user()->google_id === null && auth()->user()->facebook_id === null)
                <span class="text-danger" data-field="password"></span>
                <input type="password" class="sign-up__input" name="password" placeholder="{{ t('Your Password') }}">
            @endif
            <div class="sign-up__total">
                {{ t('Your total is') }} <span class="sign-up__price">6,00 USD</span>
            </div>
            <div class="sign-up__then">
                Then 6,00 USD/month
            </div>
            <a href="#" class="sign-up__sign next-step">{{ t('Login') }}</a>
        </div>
    </div>

    <div class="sign-up" id="stepLocation" style="display: none">
        <div class="sign-up__close">
            <img src="{{ asset('freeconvert/img/popup-close-small.svg') }}" width="21" height="20" alt="close">
        </div>
        <div class="sign-up__header">
            <h3 class="sign-up__h">{{ t('Purchase') }} <span class="sign-up__period">1 month</span></h3>
            <h4 class="sign-up__sub-title">
                {{ t('The membership for') }} <a href="{{ url('') }}">{{ url('') }}</a>
            </h4>
        </div>
        <div class="sign-up__content">
            <h5 class="sign-up__h-located text-left">{{ t('Where are you located?') }}</h5>
            <div class="sign-up__p text-left">
                {{ t(' Please enter your Country below. We collect this information to help combat fraud,
                 and to keep your payment secure.') }}
            </div>
            <select name="location" class="sign-up__select">
                @isset($Countries)
                    @foreach($Countries as $country)
                        <option value="{{ $country['abbreviation'] }}" data-display="{{ t($country['country']) }}">{{ t($country['country']) }}</option>
                    @endforeach
                @endisset
            </select>
            <span class="text-danger" data-field="location"></span>
            <a href="#" class="sign-up__sign next-step">{{ t('Next step') }}</a>
        </div>
    </div>

    <div class="sign-up" id="stepPaymentOptions" style="display: none">
        <div class="sign-up__close">
            <img src="{{ asset('freeconvert/img/popup-close-small.svg') }}" width="21" height="20" alt="close">
        </div>
        <div class="sign-up__header">
            <h3 class="sign-up__h">{{ t('Purchase') }} <span class="sign-up__period">1 month</span></h3>
            <h4 class="sign-up__sub-title">
                {{ t('The membership for') }} <a href="{{ url('') }}">{{ url('') }}</a>
            </h4>
        </div>
        <div class="sign-up__content">
            <div class="sign-up__total">
                {{ t('Your total is') }} <span class="sign-up__price">6,00 USD</span>
            </div>
            <div class="sign-up__then">
                Then 6,00 USD/month
            </div>
            <a href="#" class="sign-up__sign pay-by-card__btn next-step">{{ t('Pay by Card') }}</a>
            {{--            <a href="#" class="sign-up__paypal" id="payByPayPal">--}}
            {{--                <img src="{{ asset('freeconvert/img/paypal.svg') }}" width="112" height="31" alt="paypal">--}}
            {{--            </a>--}}
            <div id="paypal-button-container"></div>
        </div>
    </div>

    <div class="sign-up" id="stepPayByCard" style="display: none">
        <div class="sign-up__close">
            <img src="{{ asset('freeconvert/img/popup-close-small.svg') }}" width="21" height="20" alt="close">
        </div>
        <div class="sign-up__header">
            <h3 class="sign-up__h">{{ t('Purchase') }} <span class="sign-up__period">1 month</span></h3>
            <h4 class="sign-up__sub-title">
                {{ t('The membership for') }} <a href="{{ url('') }}">{{ url('') }}</a>
            </h4>
        </div>
        <div class="sign-up__content">
            <div class="sign-up__total">
                {{ t('Your total is') }} <span class="sign-up__price">6,00 USD</span>
            </div>
            <div class="sign-up__then">
                Then 6,00 USD/month
            </div>
            <div class="sign-up__payment">
                <span class="text-danger" data-field="card_number"></span>
                <input name="card_number" class="sign-up__input" type="text" data-mask="0000 0000 0000 0000"
                       placeholder="{{ t('Card Number') }}">
                <span class="text-danger" data-field="cardholder_name"></span>
                <input name="cardholder_name" type="text" class="sign-up__input"
                       placeholder="{{ t('Cardholder Name') }}">
                <div class="sign-up__payment-small">
                    <input name="card_mm" type="text" data-mask="00" class="sign-up__input" placeholder="{{ t('MM') }}">
                    <input name="card_yyyy" type="text" data-mask="0000" class="sign-up__input"
                           placeholder="{{ t('YYYY') }}">
                    <input name="card_cvv" type="text" data-mask="000" class="sign-up__input"
                           placeholder="{{ t('CVV') }}">
                </div>
            </div>
            <div>
                <span class="text-danger" data-field="card_mm"></span>
                <span class="text-danger" data-field="card_yyyy"></span>
                <span class="text-danger" data-field="card_cvv"></span>
                <span class="text-danger" data-action="error-message"></span>
            </div>
            <a href="#" class="sign-up__sign" id="payByCard">{{ t('Pay by Card') }}</a>
            <a href="#" class="sign-up__change" id="changePaymentMethod">{{ t('Change Payment Method') }}</a>
        </div>
        <img src="/img/Secure.png" style="margin-bottom: 27px;">
    </div>
</div>
