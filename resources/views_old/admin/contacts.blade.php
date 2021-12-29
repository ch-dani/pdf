@extends('layouts.admin')

@section('content')
    <div class="main-wrap">

        @include('admin.includes.slidebar')

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Contacts
                </h1>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{ route('admin-dashboard') }}">
                            <i class="fa fa-dashboard"></i>
                            Home
                        </a>
                    </li>
                    <li class="active">Contacts</li>
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
                                                <label for="location">Location</label>
                                                <input value="{{ \App\Option::option('contact_location') }}"
                                                       type="text" name="location"
                                                       id="location" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="phone">Phone</label>
                                                <input value="{{ \App\Option::option('contact_phone') }}"
                                                       type="text" name="phone"
                                                       id="phone" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="email">Email</label>
                                                <input value="{{ \App\Option::option('contact_email') }}"
                                                       type="text" name="email"
                                                       id="email" class="form-control">
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