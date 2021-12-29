@php
	try{
	    $testimonialsDecoded = json_decode($testimonials, true)[$lang_id];
	}catch(ErrorException $e){
		if(!isset($testimonials)){
			return;
		}
	    $testimonialsDecoded = json_decode($testimonials, true)[1];
	}
@endphp

<section class="module__testimonial module bg-grey">
    <div class="container">
        <div class="title-wrapper">
            <h2 class="h1-title title_main">{{ array_key_exists(8, $testimonialsDecoded) ? $testimonialsDecoded[8] : '' }}</h2>
            <h3 class="sub-title">{{ array_key_exists(9, $testimonialsDecoded) ? $testimonialsDecoded[9] : '' }}</h3>
        </div>

        <div class="testimonial">
            <div class="testimonial__text">
                {{ array_key_exists(10, $testimonialsDecoded) ? $testimonialsDecoded[10] : '' }}
            </div>
            <div class="testimonial__author">
                {{ array_key_exists(11, $testimonialsDecoded) ? $testimonialsDecoded[11] : '' }}
            </div>
        </div>

        @if(!auth()->check())
            <div class="contact-us">
                <a class="contact-us__button btn-gradient sign-up-trigger" href="#">
                    <span class="button-helper"></span>{{ t('Sign Up Now!') }}
                </a>
            </div>
        @endif
    </div>
</section>
