@extends('layouts.admin')

@section('content')
    <div class="main-wrap">

        @include('admin.includes.slidebar')

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Payment
                </h1>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{ route('admin-dashboard') }}">
                            <i class="fa fa-dashboard"></i>
                            Home
                        </a>
                    </li>
                    <li class="active">Payment</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <form id="ContactsForm">
                            {{ csrf_field() }}
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Settings</h3>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="stripe_pub">Stripe Publishable key</label>
                                                <input value="{{ \App\Option::option('stripe_pub') }}"
                                                       type="text" name="stripe_pub"
                                                       id="stripe_pub" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="stripe_priv">Stripe Secret key</label>
                                                <input value="{{ \App\Option::option('stripe_priv') }}"
                                                       type="text" name="stripe_priv"
                                                       id="stripe_priv" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="paypal_client_id">PayPal Client ID</label>
                                                <input value="{{ \App\Option::option('paypal_client_id') }}"
                                                       type="text" name="paypal_client_id"
                                                       id="paypal_client_id" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->
                        </form>
                    </div>
                </div>
            </section>

        </div>
    </div>
@endsection