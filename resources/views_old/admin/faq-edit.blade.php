@extends('layouts.admin')

@section('content')
    <div class="main-wrap">

        @include('admin.includes.slidebar')

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Edit FAQ
                </h1>
                <ol class="breadcrumb">
                    <li><a href="{{ route('admin-dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="{{ route('admin-faq') }}">FAQ</a></li>
                    <li class="active">Edit FAQ</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <form id="EditFaqForm">
                            {{ csrf_field() }}
                            <input type="hidden" name="faq_id" value="{{ $Faq->id }}">
                            <div class="box">
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
                                                        $titles = json_decode($Faq->title, true);
                                                        $steps = json_decode($Faq->steps, true);
                                                        $link = json_decode($Faq->link, true);
                                                        $link_title = json_decode($Faq->link_title, true);
                                                    @endphp
                                                    @foreach ($Languages as $key => $Language)
                                                        <div class="tab-pane {{ $key == 0 ? 'active' : '' }}" id="{{ str_replace(' ', '_', $Language->name) }}" data-id="{{ $Language->id }}">
                                                            <div class="form-group">
                                                                <label for="title[{{ $Language->id }}]">Title</label>
                                                                <input value="{{ isset($titles[$Language->id]) ? $titles[$Language->id] : '' }}" type="text" name="title[{{ $Language->id }}]" id="title[{{ $Language->id }}]" class="form-control">
                                                            </div>
                                                            @if (isset($steps[$Language->id]))
                                                                @foreach ($steps[$Language->id] as $key => $step)
                                                                    <div class="form-group {{ $key == 1 ? 'clone_step' : '' }}">
                                                                        <label for="step[{{ $Language->id }}][{{ $key }}]">Step #<span>{{ $key }}</span></label>
                                                                        <input type="text" value="{{ $step }}" name="step[{{ $Language->id }}][{{ $key }}]" id="step[{{ $Language->id }}][{{ $key }}]" class="form-control step_{{ $Language->id }}">
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                @foreach ($steps[1] as $key => $step)
                                                                    <div class="form-group clone_step">
                                                                        <label for="step[{{ $Language->id }}][{{ $key }}]">Step #<span>{{ $key }}</span></label>
                                                                        <input type="text" value="" name="step[{{ $Language->id }}][{{ $key }}]" id="step[{{ $Language->id }}][{{ $key }}]" class="form-control step_{{ $Language->id }}">
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                            <button type="button" class="btn btn-primary AddStep">+</button>
                                                            <div class="form-group" style=" min-height: 60px; margin-top: 15px; ">
                                                                <div class="col-md-12" style=" padding: 0px; ">
                                                                    <div class="col-md-6" style=" padding: 0px; ">
                                                                        <div class="form-group">
                                                                            <label for="link[{{ $Language->id }}]">Link</label>
                                                                            <input value="{{ isset($link[$Language->id]) ? $link[$Language->id] : '' }}" type="text" name="link[{{ $Language->id }}]" id="link[{{ $Language->id }}]" class="form-control">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6" style=" padding: 0px; padding-left: 15px; ">
                                                                        <div class="form-group">
                                                                            <label for="link_title[{{ $Language->id }}]">Link title</label>
                                                                            <input value="{{ isset($link_title[$Language->id]) ? $link_title[$Language->id] : '' }}" type="text" name="link_title[{{ $Language->id }}]" id="link_title[{{ $Language->id }}]" class="form-control">
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
                                                        @php
                                                            $icons = json_decode($Faq->icons, true);
                                                        @endphp
                                                        @if (is_array($icons) and count($icons))
                                                            @foreach ($icons as $icon)
                                                                <img src="{{ $icon }}" />
                                                                <input type="hidden" value="{{ $icon }}" name="icons[]">
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12" style=" padding: 0px; ">
                                                <div class="col-md-6" style=" padding: 0px; ">
                                                    <div class="form-group">
                                                        <label>Status</label>
                                                        <select name="status" id="status" class="form-control">
                                                            <option value="show" {{ $Faq->status == 'show' ? 'selected' : '' }}>Shown</option>
                                                            <option value="hide" {{ $Faq->status == 'hide' ? 'selected' : '' }}>Hidden</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6" style=" padding: 0px; padding-left: 15px; ">
                                                    <div class="form-group">
                                                        <label for="sort">Sort</label>
                                                        <input type="text" name="sort" id="sort" value="{{ $Faq->sort }}" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
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