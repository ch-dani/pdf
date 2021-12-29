<div class="hidden">

    <div id="contactFormModal">
        <div class="modal-content">
            <div class="contact-form-wrap">
                <h3>{{t('Contact Form')}}</h3>
                <h5>{{t('Email us at')}}
                    <a href="mailto:{{ \App\Option::option('contact_email') }}">{{ \App\Option::option('contact_email') }}</a>
                    {{t('or use the form below')}}
                </h5>
                <div class="alert alert-success" id="contactSuccess" style="display: none">{{t('Thank you for your message.')}}</div>
                <form id="contactForm">
                    <div class="form-group">
                        <label for="contact-msg">{{t('Message')}}</label>
                        <textarea placeholder="Message" required="required" name="message" class="form-control"
                                  id="contact-msg"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="contact-email">{{t('Email Address')}}</label>
                        <input placeholder="Email (so we can reply)" name="email" id="contact-email"
                               class="form-control" type="email">
                        <p class="help-block">{{t('Optional. If you want us to reply, let us know your email.')}}</p>
                    </div>
                    <div class="form-group">
                        <label for="contact-email">{{t('Reporting a problem?')}}</label>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input name="allowAccessLastTask" type="checkbox">
                            {{t('Allow us access to your last task and the files used.')}}
                        </label>
                        <p class="help-block">{{t('We'll use this troubleshooting info to help you faster.')}}</p>
                    </div>
                    <button id="sendMessageBtn" class="btn btn-default" type="button">{{t('Send message')}}</button>
                    <input value="realitate" name="suflet" type="hidden">
                    <input value="" name="source" type="hidden">
                    <input value="" name="source2" type="hidden">
                    <input value="" name="lastFile" type="hidden">
                    <input value="" name="lastTask" type="hidden">
                    <input value="[]" name="events" type="hidden">
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>

</div>
