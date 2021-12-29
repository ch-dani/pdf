<div class="psd-popup pay-for-subscription-popup">
    <div class="sign-up">
        <div class="sign-up__close">
            <img src="{{ asset('freeconvert/img/popup-close-small.svg') }}" width="21" height="20" alt="close">
        </div>
        <div class="sign-up__header">
            <h3 class="sign-up__h">{{ t('Pay for subscription') }}</h3>
            <h4 class="sign-up__sub-title">{{ $user->subscription->subscriptionPlan->name }}</h4>
        </div>
        <div class="sign-up__content">
            <div class="sign-up__total">
                {{ t('Your total is') }}
                <span class="sign-up__price">
                    {{ $user->subscription->subscriptionPlan->price }} USD
                </span>
            </div>
            <div class="sign-up__then">
                Then {{ $user->subscription->subscriptionPlan->price }}
                USD/{{ $user->subscription->subscriptionPlan->interval_unit }}
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

            <a href="#" class="sign-up__sign" id="payForSubscription">{{ t('Pay by Card') }}</a>
        </div>
    </div>
</div>