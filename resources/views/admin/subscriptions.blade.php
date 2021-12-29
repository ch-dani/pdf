@extends('layouts.admin')

@section('content')
    <div class="main-wrap">
        @include('admin.includes.slidebar')
        {{ csrf_field() }}

        <div class="content-wrapper">
            <section class="content-header">
                <h1>Subscriptions</h1>
                <ol class="breadcrumb">
                    <li><a href="{{ route('admin-dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">Subscriptions</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">All Subscriptions</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <table id="Users" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th style="width: 30px;">#</th>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Price</th>
                                        <th>Interval Unit</th>
                                        <th>Interval Count</th>
                                        <th style="width: 130px;">Created at</th>
                                        <th style="width: 280px;">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($subscriptions as $subscription)
                                        <tr>
                                            <td>{{ $subscription->id }}</td>
                                            <td>{{ $subscription->code }}</td>
                                            <td>{{ $subscription->name }}</td>
                                            <td>{{ $subscription->description }}</td>
                                            <td>${{ $subscription->price  }}</td>
                                            <td>{{ $subscription->interval_unit }}</td>
                                            <td>{{ $subscription->interval_count }}</td>
                                            <td>{{ $subscription->created_at->format('d.m.Y') }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin-edit-subscription', ['id' => $subscription->id]) }}"
                                                       class="btn btn-info">Edit</a>
                                                    <a href="#" class="btn btn-danger DeleteSubscription"
                                                       data-id="{{ $subscription->id }}">Delete</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>#</th>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Price</th>
                                        <th>Interval Unit</th>
                                        <th>Interval Count</th>
                                        <th>Created at</th>
                                        <th>Actions</th>
                                    </tr>
                                    </tfoot>
                                </table>
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