@extends('layouts.admin')

@section('content')
    <div class="main-wrap">

        @include('admin.includes.slidebar')

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Socials
                </h1>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{ route('admin-dashboard') }}">
                            <i class="fa fa-dashboard"></i>
                            Home
                        </a>
                    </li>
                    <li class="active">Socials</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <form id="SocialsForm">
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
                                                <label for="twitter">Twitter URL</label>
                                                <input value="{{ \App\Option::option('social_twitter') }}"
                                                       type="text" name="twitter"
                                                       id="twitter" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="facebook">Facebook URL</label>
                                                <input value="{{ \App\Option::option('social_facebook') }}"
                                                       type="text" name="facebook"
                                                       id="facebook" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="google">Google+ URL</label>
                                                <input value="{{ \App\Option::option('social_google') }}"
                                                       type="text" name="google"
                                                       id="google" class="form-control">
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