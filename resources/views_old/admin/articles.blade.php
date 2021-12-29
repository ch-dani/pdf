@extends('layouts.admin')

@section('content')
    <div class="main-wrap">

        @include('admin.includes.slidebar')
        {{ csrf_field() }}

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Articles
                </h1>
                <ol class="breadcrumb">
                    <li><a href="{{ route('admin-dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">Articles</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">All Articles</h3>
                            </div>
                            <div class="box-body">
                                <style>
                                    .table tbody tr td:nth-child(2) {
                                        width: 40%;
                                    }
                                </style>
                                <table id="Articles" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th style="width: 30px;">#</th>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th style="width: 130px;">Created</th>
                                        <th style="width: 170px;">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($Articles as $Article)
                                        <tr>
                                            <td>{{ $Article->id }}</td>
                                            <td>{{ json_decode($Article->title, true)[1] }}</td>
                                            <td>{{ ucfirst($Article->status) }}</td>
                                            <td>{{ date('F d, Y', strtotime($Article->created_at)) }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin-edit-article', ['id' => $Article->id]) }}" class="btn btn-info">Edit</a>
                                                    <a href="{{ route('article', ['id' => $Article->url]) }}" target="_blank" class="btn btn-success">Show</a>
                                                    <a href="#" class="btn btn-danger DeleteArticle" data-id="{{ $Article->id }}">Delete</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
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
