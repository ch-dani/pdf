@extends('layouts.admin')

@section('content')
    <div class="main-wrap">

        @include('admin.includes.slidebar')

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Add New Page
                </h1>
                <ol class="breadcrumb">
                    <li><a href="{{ route('admin-dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="{{ route('admin-pages') }}">Pages</a></li>
                    <li class="active">Add New Page</li>
                </ol>
            </section>
            <section class="content">
                <form id="AddPageForm">
                    {{ csrf_field() }}
                    <div class="container-fluid spark-screen">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="box box-success box-solid">
                                    <div class="box-body pad">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="publish">Publish</option>
                                                <option value="draft">Draft</option>
                                                <option value="trash">Trash</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-default btn-block">Add</button>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                                <div class="box box-success box-solid">
                                    <div class="box-body pad">
                                        <div class="form-group">
                                            <label for="tool">Tool Name</label>
                                            <input type="text" class="form-control" id="tool" name="tool" placeholder="Tool Name">
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="box box-success box-solid">
                                    <div class="box-body pad">
                                        <div class="form-group">
                                            <label for="title">Title</label>
                                            <input type="text" class="form-control" id="title" name="title" placeholder="Title">
                                        </div>
                                        <div class="form-group">
                                            <label for="link">Link</label>
                                            <input type="text" class="form-control" id="link" name="link" placeholder="Link">
                                        </div>
                                        <textarea id="content" name="content" rows="10" cols="80"></textarea>
                                    </div>
                                </div>
                                <div class="box box-success box-solid">
                                    <div class="box-body pad">
                                        <div class="form-group">
                                            <label for="seo_title">SEO Title</label>
                                            <input type="text" class="form-control" id="seo_title" name="seo_title" placeholder="SEO Title">
                                        </div>
                                        <div class="form-group">
                                            <label for="seo_keywords">SEO Keywords</label>
                                            <input type="text" class="form-control" id="seo_keywords" name="seo_keywords" placeholder="SEO Keywords">
                                        </div>
                                        <div class="form-group">
                                            <label for="seo_description">SEO Description</label>
                                            <textarea class="col-md-12" id="seo_description" name="seo_description" rows="2" cols="2"></textarea>
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
@endsection