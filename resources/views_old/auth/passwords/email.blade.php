@extends('layouts.auth')

@section('content')
    <section class="page-login">
        <div class="login-modal-wrap">
            <div id="closeModal">&times;</div>
            <div class="lolin-modal-block">
                <a href="{{ route('index') }}"><img src="{{ asset('img/logo.svg') }}" alt="Alternate Text" /></a>
                <h3>Reset Password</h3>
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
                <form class="sign-form" action="{{ route('password.email') }}" method="post">
                    {{ csrf_field() }}
                    <input type="text" name="email" value="{{ old('email') }}" placeholder="Email" required/>
                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                    <div class="forgot-password">
                        <a href="{{ route('login') }}">Login</a>
                    </div>
                    <button><i class="fas fa-lock" style="margin-right:10px;"></i>Send Reset Link</button>
                </form>
            </div>
        </div>
    </section>
@endsection
