@extends('layouts.admin')

@section('content')
    <div class="main-wrap">

        @include('admin.includes.slidebar')

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Edit Language
                </h1>
                <ol class="breadcrumb">
                    <li><a href="{{ route('admin-dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="{{ route('admin-languages') }}">Languages</a></li>
                    <li class="active">Edit Language</li>
                </ol>
            </section>
            <section class="content">
                <form id="EditLanguageForm">
                    {{ csrf_field() }}
                    <input type="hidden" name="language_id" value="{{ $Language->id }}">
                    <div class="container-fluid spark-screen">
                        <div class="row">
                            <div class="box box-primary">
                                <div class="box-body pad">
                                    <div class="form-group">
                                        <label for="name">Language</label>
                                        <input type="text" value="{{ $Language->name }}" class="form-control" id="name" name="name" placeholder="Language">
                                    </div>
                                    <div class="col-md-4" style="padding: 0px;">
                                        <div class="form-group">
                                            <label for="flag">Flag</label><img src="{{ $Language->flag }}" id="flag-img" style="display: inline-block;margin-left: 10px;margin-top: -10px;"/>
                                            <select name="flag" id="flag" class="form-control">
                                                @foreach ($Flags as $Flag)
                                                    <option value="{{ $Flag['flag'] }}" {{ $Language->flag == $Flag['flag'] ? 'selected' : '' }}>{{ $Flag['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-8" style="padding: 0px 0px 0px 15px;">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="active" {{ $Language->status == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ $Language->status == 'show' ? 'inactive' : '' }}>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
@endsection