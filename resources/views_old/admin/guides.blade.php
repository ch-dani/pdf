@extends('layouts.admin')

@section('content')
    <div class="main-wrap">

        @include('admin.includes.slidebar')
        {{ csrf_field() }}

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Guides
                </h1>
                <ol class="breadcrumb">
                    <li><a href="{{ route('admin-dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">Guides</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">All Guides</h3>
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
                                <table id="Guides" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 30px;">#</th>
                                            <th>Title</th>
                                            <th>Status</th>
                                            <th>Tools</th>
                                            <th style="width: 130px;">Created</th>
                                            <th style="width: 170px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($Guides as $Guide)
                                            <tr>
                                                <td>{{ $Guide->id }}</td>
                                                <td>{{ json_decode($Guide->title, true)[1] }}</td>
                                                <td>{{ $Guide->status == 'show' ? 'Shown' : 'Hidden' }}</td>
                                                <td>
                                                    @foreach (\App\GuideTool::select('tool')->where('guide_id', $Guide->id)->get() as $tool)
                                                        <span class="label label-primary">{{ $tool->tool }}</span>
                                                    @endforeach
                                                </td>
                                                <td>{{ date('F d, Y', strtotime($Guide->created_at)) }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('admin-edit-guide', ['id' => $Guide->id]) }}" class="btn btn-info">Edit</a>
                                                        <a href="#" class="btn btn-success">Show</a>
                                                        <a href="#" class="btn btn-danger DeleteGuide" data-id="{{ $Guide->id }}">Delete</a>
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
                                            <th>Tools</th>
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