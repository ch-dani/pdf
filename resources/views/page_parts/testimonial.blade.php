<section class="module__testimonial module bg-grey">
    <div class="container">
        <div class="title-wrapper">
            <h2 class="h1-title title_main">{{t('Join Our 1+ Million Users')}}</h2>
            <h3 class="sub-title">{{t('Enjoy secure, fast and high quality file format conversations and manipulations without any limits online.')}}</h3>
        </div>

            <div class="testimonial">
                <div class="testimonial__text">
                    {{t('Simple to use and free online conversion – perfect! Previously I had used desktop PDF software. Especially it was a real pain with my small laptop - where I needed to wait for 2-3 minutes the software even to load. This tool is awesome! Fast and reliable. All worked like a charm! Thank you very much!')}}
                </div>
                <div class="testimonial__author">
                    {{t("Joseff Mac o'Donnell.")}}
                </div>
            </div>

        <div class="contact-us">
        	@if(!Auth::user())
            	<a class="sign-up-trigger contact-us__button btn-gradient" href="#"><span class="button-helper"></span>{{t('Sign Up Now!')}}</a>
            @endif
        </div>
    </div>
</section>
