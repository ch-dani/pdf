<div id="wait_conversion" class="hidden">
    <section class="waiting_for_top_s">
        <div class="container">
            @if(!\Illuminate\Support\Facades\Auth::check() || !auth()->user()->subscription || auth()->user()->subscription->status === 'inactive')
                <div class="countdown_block">
                    <h3>
                        {{ t('Please wait') }} <span class="timer">15:00</span>
                        {{ t('minutes to convert the file or Sign Up & purchase a subscription to convert immediately.') }}
                    </h3>
                    @if(!auth()->check())
                        <a href="#" class="def_gradient_btn sign-up-trigger">{{ t('Sign Up') }}</a>
                    @endif
                </div>
            @endif
            <div class="document_center_wrpr">
                <div class="doc_card_element doc_loading">
                    @php include(public_path('freeconvert/img/loader_circle.svg')) @endphp
                </div>
                <h3 class="doc_name">Processing...</h3>
            </div>
            @if(!\Illuminate\Support\Facades\Auth::check() || !auth()->user()->subscription || auth()->user()->subscription->status === 'inactive')
                <div class="row rates_cards_row">
                    @foreach(App\SubscriptionPlan::all() as $subscriptionPlan)
                        <div class="col-md-6 col-lg-4">
                            <div class="card_box rate_card">
                                <span class="card_months">{{ $subscriptionPlan->name }}</span>
                                <span class="card_price">${{ $subscriptionPlan->price }}</span>
                                <span class="card_period">{{ $subscriptionPlan->description }}</span>
                                <a href="#" class="def_gradient_btn purchase-subscription-trigger"
                                   data-plan="{{ $subscriptionPlan->id }}">
                                    {{ t('Subscribe') }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @if(!\Illuminate\Support\Facades\Auth::check() || !auth()->user()->subscription || auth()->user()->subscription->status === 'inactive')
        <section class="module__how-convert module bg-white premium_g_more_s">
            <div class="container">
                <div class="title-wrapper">
                    <h2 class="h2-title title_main">{{ t('Premium gets more') }}</h2>
                    <h3 class="sub-title">{{ t('A reliable, intuitive and productive PDF Software') }}</h3>
                </div>
                <div class="row">
                    <div class="col-md-6 col-lg-4">
                        <div class="convert about_our">
                            @php include(public_path('freeconvert/img/premium_g_more_icon_1.svg')) @endphp
                            <h4 class="convert__title">{{ t('No more waitings, no more advertisement') }}</h4>
                            <p class="convert__p">
                                {{ t('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Odio dictum cursus risus sem. In elementum quam pharetra massa. ') }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="convert about_our">
                            @php include(public_path('freeconvert/img/premium_g_more_icon_2.svg')) @endphp
                            <h4 class="convert__title">{{ t('Fastest conversion of your files') }}</h4>
                            <p class="convert__p">
                                {{ t('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Massa purus placerat
                                elementum malesuada arcu quis auctor leo. Odio dictum cursus risus sem.') }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="convert about_our">
                            @php include(public_path('freeconvert/img/premium_g_more_icon_3.svg')) @endphp
                            <h4 class="convert__title">{{ t('Best file encryption and automatic deletion') }}</h4>
                            <p class="convert__p">
                                {{ t('Your privacy is important. When you convert Word to PDF online with our free PDF converter, your files are secured with 256-bit SSL Encryption.') }}
                            </p>
                        </div>
                    </div>
                    <div class="contact-us">
                        <a class="contact-us__button" href="#">{{ t('Learn More') }}</a>
                    </div>
                </div>
            </div>

            @include('page_parts.banner')
        </section>

        <section class="module__testimonial module bg-grey module_cards_testimonials">
            <div class="container">
                <div class="title-wrapper">
                    <h2 class="h1-title title_main">{{ t('Join Our 1+ Million Users') }}</h2>
                    <h3 class="sub-title">{{ t('Enjoy secure, fast and high quality file format conversations and manipulations without any limits online.') }}</h3>
                </div>

                <div class="row">
                    <div class="col-md-6 col-lg-4">
                        <div class="testimonial testimonial_card">
                            <div class="testimonial__text">
                                {{ t('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Odio dictum cursus risus sem. In elementum quam pharetra massa.') }}
                            </div>
                            <div class="testimonial__author">{{ t('John Smith, The Tornsberg') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="testimonial testimonial_card">
                            <div class="testimonial__text">
                                {{ t('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Odio dictum cursus risus sem.
                                In elementum quam pharetra massa.') }}
                            </div>
                            <div class="testimonial__author">{{ t('Olivia Storn, Nike') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="testimonial testimonial_card">
                            <div class="testimonial__text">
                                {{ t('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Odio dictum cursus risus sem. In elementum quam pharetra massa.') }}
                            </div>
                            <div class="testimonial__author">{{ t('Bob Williams, Dornsterby') }}</div>
                        </div>
                    </div>
                </div>

                @if(!\Illuminate\Support\Facades\Auth::check())
                    <div class="contact-us">
                        <a class="contact-us__button btn-gradient sign-up-trigger" href="#">{{ t('Sign Up Now!') }}</a>
                    </div>
                @endif
            </div>
        </section>
    @endif
</div>
