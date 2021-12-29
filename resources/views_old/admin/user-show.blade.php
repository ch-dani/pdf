@extends('layouts.admin')

@section('content')
    <div class="main-wrap">

        @include('admin.includes.slidebar')

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    User #{{ $User->id }}
                </h1>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{ route('admin-dashboard') }}">
                            <i class="fa fa-dashboard"></i>
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin-users') }}">Users</a>
                    </li>
                    <li class="active">User #{{ $User->id }}</li>
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
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <p class="lead">{{ $User->email }}</p>
                                        </div>
                                        <div class="form-group">
                                            <label>Status</label>
                                            <p class="lead">{{ ucfirst($User->status) }}</p>
                                        </div>
                                        <div class="form-group">
                                            <label>Registration Date</label>
                                            <p class="lead">{{ date('F d, Y', strtotime($User->created_at)) }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <h3 class="box-title">Documents</h3>
                                        <a href="#" class="btn btn-danger" id="DeleteDocuments" style="float: right; margin-top: -44px;">Delete checked</a>
                                        <table id="Documents" class="table table-bordered table-hover">
                                            <thead>
                                            <tr>
                                                <th style="width: 15px;"><input type="checkbox" class="SelectAllDeleteDocuments" /></th>
                                                <th style="width: 30px;">#</th>
                                                <th style="width: 130px;">Date</th>
                                                <th>Original File</th>
                                                <th>Operation</th>
                                                <th>Edited File</th>
                                                <th style="width: 50px;">Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($Documents as $Document)
                                                <tr data-id="{{ $Document->id }}">
                                                    <td><input type="checkbox" class="CheckboxDeleteDocuments" data-id="{{ $Document->id }}"/></td>
                                                    <td>{{ $Document->id }}</td>
                                                    <td>{{ date('F d, Y', strtotime($Document->created_at)) }}</td>
                                                    <td>
                                                        <a href="{{ asset($Document->original_document) }}"
                                                           target="_blank">{{ $Document->original_name }}</a>
                                                    </td>
                                                    <td>{{ $Document->operation_type }}</td>
                                                    <td>
                                                        @if (!is_null($Document->edited_document))
                                                            <a href="/{{ \App\Http\Controllers\EditPdf::getDownloadLink($Document->UUID, strtolower($Document->operation_type)) }}" target="_blank">edited_{{ $Document->original_name }}</a>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="#" class="btn btn-danger DeleteDocument" data-id="{{ $Document->id }}">Delete</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <th><input type="checkbox" class="SelectAllDeleteDocuments" /></th>
                                                <th>#</th>
                                                <th>Date</th>
                                                <th>Original File</th>
                                                <th>Edited File</th>
                                                <th>Actions</th>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <a href="{{ route('admin-edit-user', ['id' => $User->id]) }}"
                                   class="btn btn-primary">Edit
                                </a>
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
