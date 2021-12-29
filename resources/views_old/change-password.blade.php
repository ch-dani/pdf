@extends('layouts.layout')

@section('content')
    <div id="app-root">

        <div class="account-wrapper">
            <div class="container">
                <div class="app-title">
                    <div class="wrapper">
                        <h1>Account Details</h1>
                        <p>Email: {{ $User->email }}</p>
                    </div>
                </div>
                <div class="btn-wrap">
                    <form id="ChangePasswordForm">
                        {{ csrf_field() }}
                        <input type="password" name="current_password" placeholder="Current password"
                               style="display: block; width: 25%; padding: 5px; margin: 10px auto;"/>
                        <input type="password" name="new_password" placeholder="New password"
                               style="display: block; width: 25%; padding: 5px; margin: 10px auto;"/>
                        <input type="password" name="new_password_confirmation" placeholder="Confirm password"
                               style="display: block; width: 25%; padding: 5px; margin: 10px auto;"/>
                    </form>
                    <a class="button-green btn-password" id="ChangePassword" href="#">Change password</a>
                </div>
            </div>
        </div>

    </div>
@endsection