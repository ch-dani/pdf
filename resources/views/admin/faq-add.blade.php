@extends('layouts.admin')

@section('content')
    <div class="main-wrap">

        @include('admin.includes.slidebar')

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Add New FAQ
                </h1>
                <ol class="breadcrumb">
                    <li><a href="{{ route('admin-dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="{{ route('admin-faq') }}">FAQ</a></li>
                    <li class="active">Add New FAQ</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <form id="AddFaqForm">
                            {{ csrf_field() }}
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Add New FAQ</h3>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="nav-tabs-custom">
                                                <ul class="nav nav-tabs">
                                                    @foreach ($Languages as $key => $Language)
                                                        <li {!! $key == 0 ? 'class="active"' : '' !!}><a href="#{{ str_replace(' ', '_', $Language->name) }}" data-toggle="tab"><img style=" margin-right: 10px; " src="{{ $Language->flag }}" />{{ $Language->name }}</a></li>
                                                    @endforeach
                                                </ul>
                                                <div class="tab-content">
                                                    @foreach ($Languages as $key => $Language)
                                                        <div class="tab-pane {{ $key == 0 ? 'active' : '' }}" id="{{ str_replace(' ', '_', $Language->name) }}" data-id="{{ $Language->id }}">
                                                            <div class="form-group">
                                                                <label for="title[{{ $Language->id }}]">Title</label>
                                                                <input value="" type="text" name="title[{{ $Language->id }}]" id="title[{{ $Language->id }}]" class="form-control">
                                                            </div>
                                                            <div class="form-group clone_step">
                                                                <label for="step[{{ $Language->id }}][1]">Step #<span>1</span></label>
                                                                <input type="text" value="" name="step[{{ $Language->id }}][1]" id="step[{{ $Language->id }}][1]" class="form-control step_{{ $Language->id }}">
                                                            </div>
                                                            <button type="button" class="btn btn-primary AddStep">+</button>
                                                            <div class="form-group" style=" min-height: 60px; margin-top: 15px; ">
                                                                <div class="col-md-12" style=" padding: 0px; ">
                                                                    <div class="col-md-6" style=" padding: 0px; ">
                                                                        <div class="form-group">
                                                                            <label for="link[{{ $Language->id }}]">Link</label>
                                                                            <input type="text" name="link[{{ $Language->id }}]" id="link[{{ $Language->id }}]" value="" class="form-control">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6" style=" padding: 0px; padding-left: 15px; ">
                                                                        <div class="form-group">
                                                                            <label for="link_title[{{ $Language->id }}]">Link title</label>
                                                                            <input type="text" name="link_title[{{ $Language->id }}]" id="link_title[{{ $Language->id }}]" value="" class="form-control">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    <!-- /.tab-pane -->
                                                </div>
                                                <!-- /.tab-content -->
                                            </div>
                                            <!-- nav-tabs-custom -->
                                            <div class="col-md-12" style=" padding: 0px; ">
                                                <div class="col-md-3" style=" padding: 0px; ">
                                                    <div class="form-group">
                                                        <label for="sort">Icons:</label>
                                                        <input type="file" id="UploadIcon">
                                                    </div>
                                                </div>
                                                <div class="col-md-9" style=" padding: 0px; padding-left: 15px; ">
                                                    <div class="form-group icons_box">

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12" style=" padding: 0px; ">
                                                <div class="col-md-6" style=" padding: 0px; ">
                                                    <div class="form-group">
                                                        <label>Status</label>
                                                        <select name="status" id="status" class="form-control">
                                                            <option value="show">Shown</option>
                                                            <option value="hide">Hidden</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6" style=" padding: 0px; padding-left: 15px; ">
                                                    <div class="form-group">
                                                        <label for="sort">Sort</label>
                                                        <input type="text" name="sort" id="sort" value="1" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary">Add</button>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->
                        </form>
                    </div>
                </div>
            </section>

        </div>
    </div>
@endsection