<section class="waiting_for_top_s">
    <div class="container">
        <div class="row rates_cards_row">
        
        	<?php 
        	$user = (Auth::user());
        	$active_sub = false;
        	if($user && $user->subscription){
        		$active_sub = $user->subscription->subscription_plan_id;

        	}

        	?>
        
            @foreach($subscriptionPlans as $subscriptionPlan)
                <div class="col-md-6 col-lg-4">
                    <div class="card_box rate_card">
                        <span class="card_months">{{ $subscriptionPlan->name }}</span>
                        <span class="card_price">${{ $subscriptionPlan->price }}</span>
                        <span class="card_period">{{ $subscriptionPlan->description }}</span>
                        <?php if($active_sub == $subscriptionPlan->id){ ?>
                        	<?php if( $user->subscription->next_payment_at){ ?>
	                        <span class="card_period">Next payment: {{ $user->subscription->next_payment_at->format('d.m.Y')}}</span>
	                        <?php } ?>
		                    <a href="#" class="disable def_gradient_btn purchase-subscription-trigger"
		                       data-plan="{{ $subscriptionPlan->id }}">{{ t('Current') }}</a>
                        <?php }else{ ?>
		                    <a href="#" class="def_gradient_btn purchase-subscription-trigger"
		                       data-plan="{{ $subscriptionPlan->id }}">{{ t('Subscribe') }}</a>
                        <?php } ?>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
<style>
	.def_gradient_btn.disable{
		pointer-events: none;
		background: gray;
	}
</style>


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
                    <h4 class="convert__title">{{ t('No more waiting, no more advertisement') }}</h4>
                    <p class="convert__p">
                        {{ t('After making your choice of one of your subscription plans, waiting timer restrictions will be removed and no advertisement will be display once you are logged into your account.') }}
                    </p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="convert about_our">
                    @php include(public_path('freeconvert/img/premium_g_more_icon_2.svg')) @endphp
                    <h4 class="convert__title">{{ t('The Fastest conversion of your files') }}</h4>
                    <p class="convert__p">
                        {{ t('Faster conversion and operation is provided using more ore dedicated computing power for all users who had signed up to one our membership plans.') }}
                    </p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="convert about_our">
                    @php include(public_path('freeconvert/img/premium_g_more_icon_3.svg')) @endphp
                    <h4 class="convert__title">{{ t('Better file encryption and extended deletion time') }}</h4>
                    <p class="convert__p">
                        {{ t('Better file encryption (256 bit) and file extended deletion time (5 hrs) are provided to all users who had signed up to one our membership plans.') }}
                    </p>
                </div>
            </div>
            
            <div class="contact-us">
                <a class="contact-us__button" href="/about-us">{{ t('Learn More') }}</a>
            </div>
        </div>
    </div>
</section>
