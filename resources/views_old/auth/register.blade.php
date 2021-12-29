@extends('layouts.auth')

@section('content')
    <section class="page-login">
        <div class="login-modal-wrap">
    		<div id="closeModal">&times;</div>
            <div class="lolin-modal-block">
                <a href="index"><img src="{{ asset('img/logo.svg') }}" alt="Alternate Text" /></a>
                <h3>Account Registration</h3>
                <form class="sign-form" action="{{ route('register') }}" method="post">
                    {{ csrf_field() }}
                    <input type="text" name="email" value="{{ old('email') }}" placeholder="Email" required/>
                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                    <input type="password" name="password" value="" placeholder="Password" required />
                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                    <input type="password" name="password_confirmation" value="" placeholder="Confirm Password" required />
                    <div class="forgot-password">
                        <a href="{{ route('login') }}">Do you have an account?</a>
                    </div>
                    <button><i class="fas fa-lock" style="margin-right:10px;"></i>Register</button>
                </form>
                <a class="signed-google" href="#">Sign-up with Google</a>
            <p>By logging in with Google you agree to the <a href="/terms">terms</a> and <a href="/policy">privacy policy</a></p>
            </div>
        </div>
    </section>
@endsection
