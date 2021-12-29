@extends('layouts.admin')

@section('content')
    <div class="main-wrap">

        @include('admin.includes.slidebar')
        {{ csrf_field() }}

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Pages
                </h1>
                <ol class="breadcrumb">
                    <li><a href="{{ route('admin-dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">Pages</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">All Pages</h3>
                            </div>
                            <div class="box-body">
                                <style>
                                    .table tbody tr td:nth-child(2) {
                                        width: 40%;
                                    }
                                </style>
                                <table id="Pages" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 30px;">#</th>
                                            <th>Title</th>
                                            <th>Tool</th>
                                            <th>Status</th>
                                            <th style="width: 130px;">Created</th>
                                            <th style="width: 170px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($Pages as $Page)
                                            <tr>
                                                <td>{{ $Page->id }}</td>
                                                <td>{{ $Page->title }}</td>
                                                <td>{{ $Page->tool }}</td>
                                                <td>{{ ucfirst($Page->status) }}</td>
                                                <td>{{ date('F d, Y', strtotime($Page->created_at)) }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('admin-edit-page', ['id' => $Page->id]) }}" class="btn btn-info">Edit</a>
                                                        <a href="{{ url($Page->link) }}" target="_blank" class="btn btn-success">Show</a>
                                                        @if ($Page->static == 0)
                                                            <a href="#" class="btn btn-danger DeletePage" data-id="{{ $Page->id }}">Delete</a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>#</th>
                                            <th>Title</th>
                                            <th>Tool</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Action</th>
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