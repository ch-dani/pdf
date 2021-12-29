@extends('layouts.admin')

@section('content')
    <div class="main-wrap">

        @include('admin.includes.slidebar')

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Edit Subscription
                </h1>
                <ol class="breadcrumb">
                    <li><a href="{{ route('admin-dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="{{ route('admin-subscriptions') }}">Subscriptions</a></li>
                    <li class="active">Edit Subscription</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">Edit Subscription</h3>
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                        </button>
                                        <h4><i class="icon fa fa-check"></i> {{ session('success') }}</h4>
                                    </div>
                                @endif
                            </div>
                            <!-- /.box-header -->
                            <form action="{{ route('admin-update-subscription', ['id' => $subscription->id]) }}"
                                  method="POST">
                                {{ csrf_field() }}
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Code</label>
                                                <input type="text" name="code"
                                                       value="{{ old('code', $subscription->code) }}"
                                                       class="form-control" required>
                                                @if($errors->has('code'))
                                                    <span class="text-danger">{{ $errors->first('code') }}</span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label>Name</label>
                                                <input type="text" name="name"
                                                       value="{{ old('name', $subscription->name) }}"
                                                       class="form-control" required>
                                                @if($errors->has('name'))
                                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label>Description</label>
                                                <input type="text" name="description"
                                                       value="{{ old('description', $subscription->description) }}"
                                                       class="form-control" required>
                                                @if($errors->has('description'))
                                                    <span class="text-danger">{{ $errors->first('description') }}</span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label>Price</label>
                                                <input type="number" name="price"
                                                       value="{{ old('price', $subscription->price) }}"
                                                       class="form-control">
                                                @if($errors->has('price'))
                                                    <span class="text-danger">{{ $errors->first('price') }}</span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label>Interval unit</label>
                                                <select class="form-control" name="interval_unit" required>
                                                    <option value="month" {{ $subscription->interval_unit === 'month' ? 'selected' : '' }}>
                                                        Monthly
                                                    </option>
                                                    <option value="year" {{ $subscription->interval_unit === 'year' ? 'selected' : '' }}>
                                                        Annually
                                                    </option>
                                                    <option value="forever" {{ $subscription->interval_unit === 'forever' ? 'selected' : '' }}>
                                                        One Time
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Interval count</label>
                                                <input type="number" name="interval_count"
                                                       value="{{ old('interval_count', $subscription->interval_count) }}"
                                                       class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-8"></div>
                                    </div>
                                </div>
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                    <a href="{{ route('admin-subscriptions') }}" class="btn btn-default">Cancel</a>
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