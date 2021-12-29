@extends('layouts.admin')

@section('content')
    <div class="main-wrap">

        @include('admin.includes.slidebar')

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Add New Guide
                </h1>
                <ol class="breadcrumb">
                    <li><a href="{{ route('admin-dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="{{ route('admin-guides') }}">Guides</a></li>
                    <li class="active">Add New Guide</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <form id="AddGuideForm">
                            {{ csrf_field() }}
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Add New Guide</h3>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <div class="nav-tabs-custom">
                                                <ul class="nav nav-tabs">
                                                    @foreach ($Languages as $key => $Language)
                                                        <li {!! $key == 0 ? 'class="active"' : '' !!}><a href="#{{ str_replace(' ', '_', $Language->name) }}" data-toggle="tab"><img style=" margin-right: 10px; " src="{{ $Language->flag }}" />{{ $Language->name }}</a></li>
                                                    @endforeach
                                                </ul>
                                                <div class="tab-content">
                                                    @foreach ($Languages as $key => $Language)
                                                        <div class="tab-pane {{ $key == 0 ? 'active' : '' }}" id="{{ str_replace(' ', '_', $Language->name) }}">
                                                            <div class="form-group">
                                                                <label for="title[{{ $Language->id }}]">Title</label>
                                                                <input value="" type="text" name="title[{{ $Language->id }}]" id="title[{{ $Language->id }}]" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="subtitle[{{ $Language->id }}]">Subtitle</label>
                                                                <input type="text" value="" name="subtitle[{{ $Language->id }}]" id="subtitle[{{ $Language->id }}]" class="form-control">
                                                            </div>
                                                            <label for="content">Content</label>
                                                            <textarea class="ckeditor_content" id="content{{ $Language->id }}" data-id="{{ $Language->id }}" rows="10" cols="80"></textarea>
                                                        </div>
                                                    @endforeach
                                                    <!-- /.tab-pane -->
                                                </div>
                                                <!-- /.tab-content -->
                                            </div>
                                            <!-- nav-tabs-custom -->
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
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <style>

                                                    .checkbox-special {
                                                        display: flex;
                                                        flex-wrap: wrap;
                                                    }

                                                    .checkbox-special label {
                                                        display: block;
                                                        width: 50%;
                                                    }

                                                </style>
                                                <label>Tools</label>
                                                <div class="checkbox checkbox-special">
                                                    @foreach ($Tools as $tool)
                                                        <label><input type="checkbox" name="tools[]" value="{{ $tool->tool }}">{{ $tool->tool }}</label>
                                                    @endforeach
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