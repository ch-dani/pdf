@extends('layouts.admin')

@section('content')
    <div class="main-wrap">

        @include('admin.includes.slidebar')
        {{ csrf_field() }}

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Languages
                </h1>
                <ol class="breadcrumb">
                    <li><a href="{{ route('admin-dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">Languages</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">All Languages</h3>
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
                                <table id="Languages" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 30px;">#</th>
                                            <th>Language</th>
                                            <th>Status</th>
                                            <th style="width: 140px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($Languages as $Language)
                                            <tr>
                                                <td>{{ $Language->id }}</td>
                                                <td><img src="{{ $Language->flag }}" style=" display: inline-block; margin-right: 10px; " />{{ $Language->name }}</td>
                                                <td>{{ ucfirst($Language->status) }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('admin-edit-language', ['id' => $Language->id]) }}" class="btn btn-info">Edit</a>
                                                        @if ($Language->id != 1)
                                                            <a href="#" class="btn btn-danger DeleteLanguage" data-id="{{ $Language->id }}">Delete</a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>#</th>
                                            <th>Language</th>
                                            <th>Status</th>
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