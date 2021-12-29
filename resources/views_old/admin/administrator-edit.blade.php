@extends('layouts.admin')

@section('content')
    <div class="main-wrap">

        @include('admin.includes.slidebar')

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Administrator #{{ $User->id }}
                </h1>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{ route('admin-dashboard') }}">
                            <i class="fa fa-dashboard"></i>
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin-administrators') }}">Administrators</a>
                    </li>
                    <li class="active">Administrator #{{ $User->id }}</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <!-- /.box-header -->
                            <div class="box-body">
                                    <div class="col-md-3">
                                        <form id="EditAdministratorForm">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="user_id" value="{{ $User->id }}">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="email" name="email" class="form-control"
                                                       value="{{ $User->email }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Change Password</label>
                                                <input type="password" name="password" class="form-control" value="">
                                            </div>
                                            <div class="form-group">
                                                <label>Confirm Password</label>
                                                <input type="password" name="password_confirmation" class="form-control"
                                                       value="">
                                            </div>
                                        </form>
                                    </div>
                            </div>
                            <div class="box-footer">
                                <a href="#" class="btn btn-primary" id="EditAdministrator">Save</a>
                                <a href="{{ route('admin-administrators') }}" class="btn btn-default">Cancel</a>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                </div>
            </section>

        </div>
    </div>
@endsection