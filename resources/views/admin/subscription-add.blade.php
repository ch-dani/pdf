@extends('layouts.admin')

@section('content')
    <div class="main-wrap">

        @include('admin.includes.slidebar')

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Add New Subscription
                </h1>
                <ol class="breadcrumb">
                    <li><a href="{{ route('admin-dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="{{ route('admin-subscriptions') }}">Subscriptions</a></li>
                    <li class="active">Add New Subscription</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">Add New Subscription</h3>
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                        </button>
                                        <h4><i class="icon fa fa-check"></i> {{ session('success') }}</h4>
                                    </div>
                                @endif
                            </div>
                            <!-- /.box-header -->
                            <form action="{{ route('admin-store-subscription') }}" method="POST">
                                {{ csrf_field() }}
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Code</label>
                                                <input type="text" name="code" value="{{ old('code') }}"
                                                       class="form-control" required>
                                                @if($errors->has('code'))
                                                    <span class="text-danger">{{ $errors->first('code') }}</span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label>Name</label>
                                                <input type="text" name="name" value="{{ old('name') }}"
                                                       class="form-control" required>
                                                @if($errors->has('name'))
                                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label>Description</label>
                                                <input type="text" name="description" value="{{ old('description') }}"
                                                       class="form-control" required>
                                                @if($errors->has('description'))
                                                    <span class="text-danger">{{ $errors->first('description') }}</span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label>Price</label>
                                                <input type="number" name="price" value="{{ old('price') }}"
                                                       class="form-control">
                                                @if($errors->has('price'))
                                                    <span class="text-danger">{{ $errors->first('price') }}</span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label>Interval unit</label>
                                                <select class="form-control" name="interval_unit" required>
                                                    <option value="month">Monthly</option>
                                                    <option value="year">Annually</option>
                                                    <option value="forever">One Time</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Interval count</label>
                                                <input type="number" name="interval_count"
                                                       value="{{ old('interval_count') }}" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-8"></div>
                                    </div>
                                </div>
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection