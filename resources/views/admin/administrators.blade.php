@extends('layouts.admin')

@section('content')
    <div class="main-wrap">

        @include('admin.includes.slidebar')
        {{ csrf_field() }}

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Administrators
                </h1>
                <ol class="breadcrumb">
                    <li><a href="{{ route('admin-dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">Administrators</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">All Administrators</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <table id="Administrators" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th style="width: 30px;">#</th>
                                        <th>Email</th>
                                        <th>Activity</th>
                                        <th style="width: 130px;">Added</th>
                                        <th style="width: 280px;">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($Users as $User)
                                        <tr>
                                            <td>{{ $User->id }}</td>
                                            <td>{{ $User->email }}</td>
                                            <td>
                                                @if (strtotime($User->last_activity) > time() - 180)
                                                    <span class="label bg-green" title="{{ $User->last_activity }}">Online</span>
                                                @else
                                                    <span class="label bg-gray" title="{{ $User->last_activity }}">Offline</span>
                                                @endif
                                            </td>
                                            <td>{{ date('F d, Y', strtotime($User->created_at)) }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin-edit-administrator', ['id' => $User->id]) }}" class="btn btn-info">Edit</a>
                                                    <a href="#" class="btn btn-danger DeleteAdministrator" data-id="{{ $User->id }}">Delete</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>#</th>
                                        <th>Email</th>
                                        <th>Activity</th>
                                        <th>Added</th>
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