@extends('layouts.admin-auth')

<?php 

#v3
#6LdxPN8ZAAAAAMJLyBooYdY3xcwbpeVxiALEgD-o
#6LdxPN8ZAAAAAKRk4AQSJajcWw4gJg-w7emTVLa8

#v2
#6LfmxuAZAAAAACKEfymf9QQLESq-Mtnir560WPBq
#6LfmxuAZAAAAACbS4jCnuyH3MHe_VX7mQeL6HUYP
?>

@section('content')
	{{--<script src="https://www.google.com/recaptcha/api.js"></script>--}}
	<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit"></script>
	<script>
		function onSubmit(token){
			document.getElementById("login_form").submit();
		}

		var onloadCallback = function() {
			grecaptcha.render('html_element', {
				'sitekey' : '{{env("RECAPTCHA_KEY")}}'
			});
		};
	</script>


    <div class="login-box">
        <div class="login-logo">
            <a href="{{ route('index') }}"></a>
        </div>
        <!-- /.login-logo -->
        <div class="login-box-body">
            <p class="login-box-msg">Sign in to start your session</p>

            <form action="{{ route('login') }}" id="login_form" method="post">
                <input type="hidden" name="admin_form" value="1">
                {{ csrf_field() }}
                <div class="form-group has-feedback">
                    <input type="email" class="form-control" value="{{ old('email') }}" name="email" placeholder="Email" required>
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group has-feedback">
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                    @if ($errors->has('g-recaptcha-response'))
                        <span class="help-block">
                            <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                        </span>
                    @endif
                    
                </div>

                
                <div class="row">
                    <!-- /.col -->
                    <div class="col-xs-4">
                        <div id="html_element"></div>
                        <br>

                        <button 
							{{--data-sitekey="{{env("RECAPTCHA_KEY")}}"
							data-callback='onSubmit'
							data-action='submit'--}}
		                    type="submit" class="g-recaptcha btn btn-primary btn-block btn-flat">
                        	Sign In
                        </button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>

        </div>
        <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->
@endsection
