@extends('layouts.admin')

@section('content')
    <div class="main-wrap">

        @include('admin.includes.slidebar')

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Profile
                </h1>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{ route('admin-dashboard') }}">
                            <i class="fa fa-dashboard"></i>
                            Home
                        </a>
                    </li>
                    <li class="active">Profile</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <form id="ProfileForm">
                            {{ csrf_field() }}
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Profile</h3>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <img src="{{ asset(\Auth::user()->avatar) }}"
                                                 id="Avatar"
                                                 style="width: 80%; margin: 0px auto; display: block; cursor: pointer; border-radius: 50%; border: 1px solid #3c8dbc;"/>
                                            <input type="hidden" value="{{ asset(\Auth::user()->avatar) }}" name="avatar">
                                            <input type="file" id="UploadAvatar" style="display: none">
                                        </div>
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                <label for="name">Name</label>
                                                <input value="{{ \Auth::user()->name }}"
                                                       type="text" name="name"
                                                       id="name"
                                                       class="form-control"
                                                       required
                                                >
                                            </div>
                                            <div class="form-group">
                                                <label for="email">Email</label>
                                                <input value="{{ \Auth::user()->email }}"
                                                       type="text" name="email"
                                                       id="email"
                                                       class="form-control"
                                                       required
                                                >
                                            </div>
                                            <div class="form-group">
                                                <label for="email">Password</label>
                                                <input type="password" name="password" class="form-control" placeholder="If you need to change">
                                            </div>
                                            <div class="form-group">
                                                <label for="email">Confirm Password</label>
                                                <input type="password" name="password_confirmation"
                                                       class="form-control">
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