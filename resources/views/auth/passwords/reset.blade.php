@extends('layouts.auth')

@section('content')
    <style>
        .btn_reset {
            background-color: #1CD995 !important;
            border-color: #1CD995 !important;
        }
    </style>

    <section class="page-login">
        <div class="login-modal-wrap">
            <div id="closeModal">&times;</div>
            <div class="lolin-modal-block">
                <a href="{{ route('index') }}">Freeconvert PDF</a>
                <h3>Reset Password</h3>
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
                <form class="sign-form" action="{{ route('password.request') }}" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="text" name="email" value="{{ $email or old('email') }}" placeholder="Email" required/>
                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                    <input type="password" name="password" placeholder="Password" required>
                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                    <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
                    @if ($errors->has('password_confirmation'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                        </span>
                    @endif
                    <button class="btn_reset"><i class="fas fa-lock" style="margin-right:10px;"></i>Reset Password
                    </button>
                </form>
            </div>
        </div>
    </section>
@endsection
