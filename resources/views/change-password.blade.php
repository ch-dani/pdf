@extends('layouts.layout')

@section('content')
    <div id="app-root">

        <div class="account-wrapper">
            <div class="container">
                <div class="app-title">
                    <div class="wrapper">
                        <h1>{!! t('Account Details') !!}</h1>
                        <p>Email: {{ $User->email }}</p>
                    </div>
                </div>
                <div class="btn-wrap">
                    <form id="ChangePasswordForm">
                        {{ csrf_field() }}
                        <input type="password" name="current_password" placeholder="{!! t('Current password') !!}"
                               style="display: block; width: 25%; padding: 5px; margin: 10px auto;"/>
                        <input type="password" name="new_password" placeholder="{!! t('New password') !!}"
                               style="display: block; width: 25%; padding: 5px; margin: 10px auto;"/>
                        <input type="password" name="new_password_confirmation" placeholder="{!! t('Confirm password') !!}"
                               style="display: block; width: 25%; padding: 5px; margin: 10px auto;"/>
                    </form>
                    <a class="button-green btn-password" id="ChangePassword" href="#">{!! t('Change password') !!}</a>
                </div>
            </div>
        </div>

    </div>
@endsection