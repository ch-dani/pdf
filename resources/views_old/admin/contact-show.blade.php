@extends('layouts.admin')

@section('content')
    <div class="main-wrap">

        @include('admin.includes.slidebar')

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Contact #{{ $Contact->id }}
                </h1>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{ route('admin-dashboard') }}">
                            <i class="fa fa-dashboard"></i>
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin-contacts') }}">Contacts</a>
                    </li>
                    <li class="active">Contact #{{ $Contact->id }}</li>
                </ol>
            </section>

        {{ csrf_field() }}

        <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <p class="lead">{{ $Contact->email }}</p>
                                        </div>
                                        <div class="form-group">
                                            <label>Date</label>
                                            <p class="lead">{{ date('F d H:i, Y', strtotime($Contact->created_at)) }}</p>
                                        </div>
                                        <div class="form-group">
                                            <label>Message</label>
                                            <p class="lead">{{ $Contact->message }}</p>
                                        </div>
                                    </div>
                                </div>
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