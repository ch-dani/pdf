@extends('layouts.admin')

@section('content')
    <div class="main-wrap">

        @include('admin.includes.slidebar')

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    SEO Global
                </h1>
                <ol class="breadcrumb">
                    <li><a href="{{ route('admin-dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">SEO Global</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <form id="SeoGlobalForm">
                            {{ csrf_field() }}
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">SEO Global</h3>
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
                                                    @php
                                                        $titles = json_decode(\App\Option::option('seo_title'), true);
                                                        $keywords = json_decode(\App\Option::option('seo_keywords'), true);
                                                        $description = json_decode(\App\Option::option('seo_description'), true);
                                                        $ads = json_decode(\App\Option::option('seo_ads'), true);
                                                    @endphp
                                                    @foreach ($Languages as $key => $Language)
                                                        <div class="tab-pane {{ $key == 0 ? 'active' : '' }}" id="{{ str_replace(' ', '_', $Language->name) }}" data-id="{{ $Language->id }}">
                                                            <div class="form-group">
                                                                <label for="title[{{ $Language->id }}]">SEO title</label>
                                                                <input value="{{ isset($titles[$Language->id]) ? $titles[$Language->id] : '' }}" type="text" name="title[{{ $Language->id }}]" id="title[{{ $Language->id }}]" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="keywords[{{ $Language->id }}]">SEO Keywords</label>
                                                                <input value="{{ isset($keywords[$Language->id]) ? $keywords[$Language->id] : '' }}" type="text" name="keywords[{{ $Language->id }}]" id="keywords[{{ $Language->id }}]" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="description[{{ $Language->id }}]">SEO Description</label>
                                                                <textarea name="description[{{ $Language->id }}]" id="description[{{ $Language->id }}]" class="form-control">{{ isset($description[$Language->id]) ? $description[$Language->id] : '' }}</textarea>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="ads[{{ $Language->id }}]">Google AdSense</label>
                                                                <textarea name="ads[{{ $Language->id }}]" id="ads[{{ $Language->id }}]" class="form-control">{{ isset($ads[$Language->id]) ? $ads[$Language->id] : '' }}</textarea>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    <!-- /.tab-pane -->
                                                </div>
                                                <!-- /.tab-content -->
                                            </div>
                                            <!-- nav-tabs-custom -->
                                        </div>
                                    </div>
                                </div>
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary">Save</button>
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