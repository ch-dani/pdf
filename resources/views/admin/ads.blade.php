@extends('layouts.admin')

@section('content')
    <div class="main-wrap">

        @include('admin.includes.slidebar')
        {{ csrf_field() }}

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Ads
                </h1>
                <ol class="breadcrumb">
                    <li><a href="{{ route('admin-dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">Ads</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">All ads</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <table id="ads" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 30px;">#</th>
                                            <th>Page</th>
                                            <th>Ads count</th>
                                            <th style="width: 280px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pages as $page)
                                        
                                            <tr>
                                                <td>{{ $page->id }}</td>
                                                <td>{{ $page->title }}</td>
                                                <td>{{ $page->ads_count }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('ads_edit', ['id' => $page->id]) }}" class="btn btn-info">Edit</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th style="width: 30px;">#</th>
                                            <th>Page</th>
                                            <th>Ads count</th>
                                            <th style="width: 280px;">Actions</th>
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
