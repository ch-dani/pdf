@extends('layouts.admin')

@section('content')
    <div class="main-wrap">

        @include('admin.includes.slidebar')

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    SendGrid
                </h1>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{ route('admin-dashboard') }}">
                            <i class="fa fa-dashboard"></i>
                            Home
                        </a>
                    </li>
                    <li class="active">SendGrid</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <form id="SendgridForm">
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
                                                <label for="username">Username</label>
                                                <input value="{{ env('MAIL_USERNAME') }}"
                                                       type="text" name="username"
                                                       id="username" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="password">Password</label>
                                                <input value="{{ env('MAIL_PASSWORD') }}"
                                                       type="text" name="password"
                                                       id="password" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="address">From address</label>
                                                <input value="{{ env('MAIL_FROM_ADDRESS') }}"
                                                       type="text" name="address"
                                                       id="address" class="form-control">
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