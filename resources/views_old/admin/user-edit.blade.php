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

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <form id="EditUserForm">
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
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select class="form-control" name="status">
                                                    <option value="active" {{ $User->status == 'active' ? 'selected' : '' }}>Active</option>
                                                    <option value="blocked" {{ $User->status == 'blocked' ? 'selected' : '' }}>Blocked</option>
                                                </select>
                                            </div>
                                        </form>
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
                                                    <td>
                                                        @if (!is_null($Document->edited_document))
                                                            <a href="{{ asset($Document->edited_document) }}" target="_blank">edited_{{ $Document->original_name }}</a>
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
                                <a href="#" class="btn btn-primary" id="EditUser">Save</a>
                                <a href="{{ route('admin-users') }}" class="btn btn-default">Cancel</a>
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