@extends('layouts.admin')

@section('content')
    <div class="main-wrap">

        @include('admin.includes.slidebar')
        {{ csrf_field() }}

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Documents
                </h1>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{ route('admin-dashboard') }}">
                            <i class="fa fa-dashboard"></i>
                            Home
                        </a>
                    </li>
                    <li class="active">Documents</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">All Documents</h3>
                            </div>
                            <div class="box-body">
                                <style>
                                    .table tbody tr td:nth-child(4) {
                                        width: 40%;
                                    }

                                    .btn-group {
                                        display: flex;
                                    }
                                </style>
                                <table id="Documents" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th style="width: 15px;">
                                            <input type="checkbox" class="SelectAllDeleteDocuments"/>
                                        </th>
                                        <th style="width: 30px;">#</th>
                                        <th>User</th>
                                        <th style="width: 130px;">Date</th>
                                        <th>Original File</th>
                                        <th>Edited File</th>
                                        <th style="width: 50px;">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($Documents as $Document)
                                        <tr data-id="{{ $Document->id }}">
                                            <td>
                                                <input type="checkbox" class="CheckboxDeleteDocuments"
                                                       data-id="{{ $Document->id }}"/>
                                            </td>
                                            <td>{{ $Document->id }}</td>
                                            <td>{!! $Document->role == 'admin' ? 'Administrator' : ( is_null($Document->user_id) ? 'Not authorized' : '<a href="'.route('admin-show-user', ['id' => $Document->user_id]).'">'.$Document->email.'</a>' ) !!}</td>
                                            <td>{{ date('F d, Y', strtotime($Document->created_at)) }}</td>
                                            <td>
                                                <a href="{{ asset($Document->original_document) }}"
                                                   target="_blank">{{ $Document->original_name }}</a>
                                            </td>
                                            <td>
                                                @if (!is_null($Document->edited_document))
                                                    <a href="{{ asset($Document->edited_document) }}"
                                                       target="_blank">edited_{{ $Document->original_name }}</a>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-danger DeleteDocument"
                                                   data-id="{{ $Document->id }}">Delete
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>
                                            <input type="checkbox" class="SelectAllDeleteDocuments"/>
                                        </th>
                                        <th>#</th>
                                        <th>User</th>
                                        <th>Date</th>
                                        <th>Original File</th>
                                        <th>Edited File</th>
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