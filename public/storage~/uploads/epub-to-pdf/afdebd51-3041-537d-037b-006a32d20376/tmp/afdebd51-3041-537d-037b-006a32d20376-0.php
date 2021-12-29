@extends('layouts.site', [
    'bgClass' => 'bg-two',
    'routeBack' => '/step/5',
    'activeStep' => 6
    ])

@section('content')
    <div class="central central-mode client-6" data-simplebar>
        <h1 class="title title-mode-1">
            {{ $page->data['title'] ?? ''}}
        </h1>
        <div class="central-1">
            <p class="error-alert"><span></span></p>
            <form action="">
                @csrf

                <div class="address personal">
                    <label>
                    <span class="form__title address__title">
                      Card Number
                    </span>
                        <input class="address__input" type="text" data-mask="0000 0000 0000 0000" name="card_number"
                               placeholder="•••• •••• •••• 1234">
                    </label>
                    <span data-field="card_number" class="invalid-feedback"></span>
                </div>
                <div class="size">
                    <label class="size__label">
                    <span class="form__title size__title">
                      Expiration Date
                    </span>
                        <input class="size__input" type="text" data-mask="00/00" placeholder="01/23" name="card_date">
                        <span data-field="card_date" class="invalid-feedback"></span>
                    </label>
                    <label class="size__label">
                    <span class="form__title size__title">
                      CVC code
                    </span>
                        <input class="size__input" type="password" data-mask="000" placeholder="•••" maxlength="3"
                               name="card_cvc">
                        <span data-field="card_cvc" class="invalid-feedback"></span>
                    </label>
                </div>
                <div class="space-between total">
                    <h3 class="form__title total__title">
                        TOTAL AMOUNT:
                    </h3>
                    <p class="total__price">
                        <span
                            class="total__price__span">$ {{ ($costProducts['price'] - $costProducts['discount']) * 0.2 }}</span>
                        @if($costProducts['discount'] !== 0 && $costProducts['price'] !== 0)
                            (20% of total price)
                        @endif
                    </p>
                </div>
                <div class="space-between">
                    <label class="iscan iscan-1">
                        <input class="construction__radio options__radio" name="terms_conditions" type="checkbox">
                        <span class="form__title agree-text">I agree to the terms and conditions</span>
                    </label>
                    <div class="question">
                        <img src="/img/site/icon-question.png" alt="" class="question_img agree-text" title="lorem20">
                        <div class="attr">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vel duis et sed vitae
                            habitant placerat placerat. In porttitor nibh dui et. Potenti molestie est elit tincidunt
                            ultrices consectetur viverra. <a href="#">Egestas ultrices</a></div>
                    </div>
                </div>
                <span data-field="terms_conditions" class="invalid-feedback"></span>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/jquery.mask.js') }}"></script>
@endpush
