@extends('layouts.layout')

@section('content')
    <div id="app-root">

        <div class="account-wrapper">
            <div class="container">
                <div class="app-title">
                    <div class="wrapper">
                        <h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Account Details' !!}</h1>
                        <p>Email: {{ $User->email }}</p>
                    </div>
                </div>
                @if ($User->status == 'register')
                    <div class="alert alert-danger" style=" display: block; ">
                        {!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'You need to confirm your email address' !!}
                        @php $tt = strtotime($User->last_confirmation)+300-time() @endphp
                        
	                    @if(!$User->last_confirmation || strtotime($User->last_confirmation)+300 >= time())
		                    @if($tt<60)
		                    	<br>
			                    You can send another confirmation email after {{ $tt }} seconds
		                    @else
		                    	<br>
			                    You can send another confirmation email after {{ ceil($tt/60) }} minutes
		                    @endif
                        @endif
                        
                        @if(!$User->last_confirmation || strtotime($User->last_confirmation)+300 <= time())
                        
                        <br>
                    	<br>
                    	<button class="options-btn" type="button" id="start_task" onclick="window.location='/resend-confirmation'">Resend email</button>
                    	@endif
                    </div>
                    
                @else
                    <div class="btn-wrap">
                        <a class="button-green btn-password" href="{{ route('change_password') }}">{!! array_key_exists(3, $PageBlocks) ? $PageBlocks[3] : 'Change password' !!}</a>
                    </div>
                    <div class="btn-wrap">
                        <a class="button-green" href="#">{!! array_key_exists(4, $PageBlocks) ? $PageBlocks[4] : 'Back to your last task result' !!}</a>
                    </div>
                    <div class="account-web-subscription-section">
                        <h3>
                            {!! array_key_exists(5, $PageBlocks) ? $PageBlocks[5] : 'Subscriptions' !!}
                            <i class="fas fa-credit-card"></i>
                        </h3>
                        <div class="alert alert-success">{!! array_key_exists(6, $PageBlocks) ? $PageBlocks[6] : 'Your subscription was cancelled' !!}</div>
                        <table class="table subscriptions-table" id="subscriptions-table" style="display: none">
                            <tbody>
                            <tr>
                                <td>
                                    <span class="user-plan-name">DeftPDF Week Pass ($5)</span>
                                </td>
                                <td>
                                    <div class="subscription-cancelled">Ends on Nov 29, 2018
                                        <br>
                                        (does not renew)
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <section id="manage-subscription-section">
                            <div class="alert alert-success">
                                <h5>{!! array_key_exists(7, $PageBlocks) ? $PageBlocks[7] : 'Your card was updated' !!}</h5>
                            </div>
                            <div class="alert alert-danger">
                                <h5>{!! array_key_exists(8, $PageBlocks) ? $PageBlocks[8] : 'An error occurred. Please try again later or contact support' !!}</h5>
                            </div>
                        </section>
                        <section class="team-onboarding">
                            <div class="alert alert-success">
                                <h5>
                                    {!! array_key_exists(9, $PageBlocks) ? $PageBlocks[9] : 'Thank you for upgrading!<br>To setup your team accounts please email <a href="#">hi@DeftPDF.com</a> the list of member\'s email addresses.' !!}
                                </h5>
                            </div>
                        </section>
                        <div class="desktop-licenses-row">
                            <h3>{!! array_key_exists(10, $PageBlocks) ? $PageBlocks[10] : 'DeftPDF Desktop License Keys' !!}
                                <i class="fas fa-desktop"></i>
                            </h3>
                            <section class="desktop-onboarding">
                                <div class="alert alert-success">
                                    <h5>
                                        {!! array_key_exists(11, $PageBlocks) ? $PageBlocks[11] : 'Thank you for upgrading!<br>To activate your license key copy it from below and paste it into DeftPDF PDF Desktop.' !!}
                                    </h5>
                                </div>
                            </section>
                            <section class="web-only-plan-notice">
                                <div class="alert alert-info-soft">
                                    <h5>
                                        {!! array_key_exists(12, $PageBlocks) ? $PageBlocks[12] : 'You\'re on a Web-only plan that <a target="_blank" href="#">does not include</a> access to DeftPDF Desktop.<br> <a target="_blank" href="#">View plans that include DeftPDF Desktop access</a>' !!}
                                    </h5>
                                </div>
                            </section>
                        </div>
                        <div class="invoices-row" style="display: none">
                            <h3>{!! array_key_exists(13, $PageBlocks) ? $PageBlocks[13] : 'Invoices' !!}</h3>
                            <table class="table invoices-table">
                                <thead>
                                <tr>
                                    <td>Date</td>
                                    <td>Amount</td>
                                    <td></td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <span class="invoice-date">Nov 22, 2018</span>
                                    </td>
                                    <td>
                                        <span class="invoice-amount">$5.00</span>
                                    </td>
                                    <td>
                                        <a href="#" class="invoice-view-btn" target="_blank">View</a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="footer-account">
                            <ul class="social-list">
                                @if (strlen(\App\Option::option('social_facebook')))
                                    <li class="social-facebook share">
                                        <a rel="nofollow" href="{{ \App\Option::option('social_facebook', false, true) }}"
                                           target="_blank">
                                            <i class="fab fa-facebook-f"></i>
                                        </a>
                                    </li>
                                @endif
                                @if (strlen(\App\Option::option('social_google')))
                                    <li class="social-gplus share">
                                        <a rel="nofollow" href="{{ \App\Option::option('social_google', false, true) }}"
                                           target="_blank">
                                            <i class="fab fa-google-plus-g"></i>
                                        </a>
                                    </li>
                                @endif
                                @if (strlen(\App\Option::option('social_twitter')))
                                    <li class="social-twitter share">
                                        <a rel="nofollow" href="{{ \App\Option::option('social_twitter', false, true) }}"
                                           target="_blank">
                                            <i class="fab fa-twitter"></i>
                                        </a>
                                    </li>
                                @endif
                                @if (strlen(\App\Option::option('contact_email')))
                                    <li class="social-email share">
                                        <a rel="nofollow" href="mailto:{{ \App\Option::option('contact_email') }}"
                                           target="_blank">
                                            <i class="fas fa-envelope"></i>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                            <div>
                                <a class="button-green contact-btn-popup" href="#contactFormModal">Contact Support</a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection
