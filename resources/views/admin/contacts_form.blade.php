@extends('layouts.admin')

@section('content')
    <div class="main-wrap">

        @include('admin.includes.slidebar')
        {{ csrf_field() }}

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Contacts
                </h1>
                <ol class="breadcrumb">
                    <li><a href="{{ route('admin-dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">Contacts</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">All Contacts</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <table id="Contacts" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th style="width: 30px;">#</th>
                                        <th>Email</th>
                                        <th style="width: 200px;">Date</th>
                                        <th style="width: 280px;">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($Contacts as $Contact)
                                        <tr style="{{ $Contact->read == 0 ? 'background: #ecf0f5;' : '' }}">
                                            <td>{{ $Contact->id }}</td>
                                            <td>{{ $Contact->email }}</td>
                                            <td>{{ date('F d H:i, Y', strtotime($Contact->created_at)) }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin-show-contact', ['id' => $Contact->id]) }}" class="btn btn-success">View</a>
                                                    <a href="#" class="btn btn-danger DeleteContact" data-id="{{ $Contact->id }}">Delete</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>#</th>
                                        <th>Email</th>
                                        <th>Date</th>
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