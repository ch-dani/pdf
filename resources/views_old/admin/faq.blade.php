@extends('layouts.admin')

@section('content')
    <div class="main-wrap">

        @include('admin.includes.slidebar')
        {{ csrf_field() }}

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Home FAQ
                </h1>
                <ol class="breadcrumb">
                    <li><a href="{{ route('admin-dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">Home FAQ</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">All FAQ</h3>
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
                                <table id="Faq" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 30px;">#</th>
                                            <th>Title</th>
                                            <th>Steps</th>
                                            <th>Status</th>
                                            <th style="width: 130px;">Created</th>
                                            <th style="width: 140px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($Faq as $faq)
                                            <tr>
                                                <td>{{ $faq->id }}</td>
                                                <td>{{ json_decode($faq->title, true)[1] }}</td>
                                                <td>{{ count(json_decode($faq->steps, true)[1]) }}</td>
                                                <td>{{ $faq->status == 'show' ? 'Shown' : 'Hidden' }}</td>
                                                <td>{{ date('F d, Y', strtotime($faq->created_at)) }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('admin-edit-faq', ['id' => $faq->id]) }}" class="btn btn-info">Edit</a>
                                                        <a href="#" class="btn btn-danger DeleteFaq" data-id="{{ $faq->id }}">Delete</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>#</th>
                                            <th>Title</th>
                                            <th>Steps</th>
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