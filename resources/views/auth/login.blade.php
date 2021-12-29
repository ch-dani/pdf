@extends('layouts.auth')

@section('content')
    <section class="page-login">
        <div class="login-modal-wrap">
    		<div id="closeModal">&times;</div>
            <div class="lolin-modal-block">
                <a href="index"><img src="img/logo.svg" alt="Alternate Text" /></a>
                <h3>Sign in to your account</h3>
                <form class="sign-form" action="{{ route('login') }}" method="post">
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
                    <div class="forgot-password">
                        <a href="{{ route('register') }}">You do not have an account?</a>
                        <a href="{{ route('password.request') }}">Forgot password?</a>
                    </div>
                    <button><i class="fas fa-lock" style="margin-right:10px;"></i>Sign in</button>
                </form>
                <div class="alert-danger">You don't have an account with us yet.</div>
                <a class="signed-google" href="{{ route('google-auth') }}">Sign-up with Google</a>
            <p>By logging in with Google you agree to the <a href="/terms">terms</a> and <a href="/policy">privacy policy</a></p>
            </div>
        </div>
    </section>
@endsection
