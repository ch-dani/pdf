@extends('layouts.admin')

@section('content')
    <div class="main-wrap">

        @include('admin.includes.slidebar')

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Edit Article
                </h1>
                <a href="/translate-content/blog/{{ $Article->id }}" target="_blank">Translate</a>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{ route('admin-dashboard') }}">
                            <i class="fa fa-dashboard"></i>
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin-articles') }}">Articles</a>
                    </li>
                    <li class="active">Edit Article</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <form id="EditArticleForm">
                            {{ csrf_field() }}
                            <input type="hidden" name="article_id" value="{{ $Article->id }}">
                            <div class="box">
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="nav-tabs-custom">
                                                <ul class="nav nav-tabs">
                                                    @foreach ($Languages as $key => $Language)
                                                        <li {!! $key == 0 ? 'class="active"' : '' !!}>
                                                            <a href="#{{ str_replace(' ', '_', $Language->name) }}"
                                                               data-toggle="tab">
                                                                <img style=" margin-right: 10px; "
                                                                     src="{{ $Language->flag }}"/>{{ $Language->name }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                <div class="tab-content">
                                                    @php
                                                        $titles = json_decode($Article->title, true);
                                                        $summary = json_decode($Article->summary, true);
                                                        $contents = json_decode($Article->content, true);
                                                        $seo_titles = json_decode($Article->seo_title, true);
                                                        $seo_keywords = json_decode($Article->seo_keywords, true);
                                                        $seo_descriptions = json_decode($Article->seo_description, true);
                                                    @endphp
                                                    @foreach ($Languages as $key => $Language)
                                                        <div class="tab-pane {{ $key == 0 ? 'active' : '' }}"
                                                             id="{{ str_replace(' ', '_', $Language->name) }}">
                                                            <div class="form-group">
                                                                <label for="title[{{ $Language->id }}]">Title</label>
                                                                <input value="{{ isset($titles[$Language->id]) ? $titles[$Language->id] : '' }}" type="text"
                                                                       name="title[{{ $Language->id }}]"
                                                                       id="title[{{ $Language->id }}]"
                                                                       class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="content">Summary</label>
                                                                <textarea class="ckeditor_summary"
                                                                          id="summary{{ $Language->id }}"
                                                                          data-id="{{ $Language->id }}" rows="10"
                                                                          cols="80">{!! isset($summary[$Language->id]) ? htmlspecialchars_decode($summary[$Language->id]) : '' !!}</textarea>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="content">Content</label>
                                                                <textarea class="ckeditor_content"
                                                                          id="content{{ $Language->id }}"
                                                                          data-id="{{ $Language->id }}" rows="10"
                                                                          cols="80">{!! isset($contents[$Language->id]) ? htmlspecialchars_decode($contents[$Language->id]) : '' !!}</textarea>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="seo_title[{{ $Language->id }}]">SEO Title</label>
                                                                <input value="{{ isset($seo_titles[$Language->id]) ? $seo_titles[$Language->id] : '' }}" type="text"
                                                                       name="seo_title[{{ $Language->id }}]"
                                                                       id="seo_title[{{ $Language->id }}]"
                                                                       class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="seo_keywords[{{ $Language->id }}]">SEO Keywords</label>
                                                                <input value="{{ isset($seo_keywords[$Language->id]) ? $seo_keywords[$Language->id] : '' }}" type="text"
                                                                       name="seo_keywords[{{ $Language->id }}]"
                                                                       id="seo_keywords[{{ $Language->id }}]"
                                                                       class="form-control">
                                                            </div>
                                                            
                                                            <div class="form-group">

		                                                        <label for="seo_description[{{ $Language->id }}]">SEO Description</label>
		                                                        <input value="{{ isset($seo_descriptions[$Language->id]) ? $seo_descriptions[$Language->id] : '' }}" type="text"
		                                                               name="seo_description[{{ $Language->id }}]"
		                                                               id="seo_description[{{ $Language->id }}]"
		                                                               class="form-control">

                                                            </div>


                                                            
                                                        </div>
                                                        <!-- /.tab-pane -->
                                                    @endforeach
                                                </div>
                                                <!-- /.tab-content -->
                                            </div>


                                            <div class="col-md-12" style=" padding: 0px; ">
		                                        <div class="form-group">
		                                        	<div class="row">
		                                            	<div class="col-md-6">
		                                                    <label for="seo_description[{{ $Language->id }}]">Thumbnail</label>
															<input type="file" id="thumbnail" name="thumbnail" accept="image/x-png,image/gif,image/jpeg">
															<p class="help-block">PNG or JPG</p>
														</div>
		                                                <div class="col-md-6">
		                                                	<img class="thumbnail_preview" src="{{ $Article->thumbnail?$Article->thumbnail:'#' }}" alt="no image">
		                                                </div>
		                                        	</div>
		                                        </div>
		                                        <style>
		                                        	img[src='#']{
		                                            	background: linear-gradient(68.92deg, #4298E8 0%, #8044DB 100%), #FFFFFF;
		                                            	width: 360px !important;
		                                            	height: 160px !important;
		                                            	display: block;
														object-fit: contain;
		                                        	}
		                                        	
		                                        	.thumbnail_preview{
		                                            	width: 360px !important;
		                                            	height: 160px !important;
		                                            	display: block;
														object-fit: cover;		                                        		
		                                        	}
		                                        </style>
                                            </div>


                                            <div class="col-md-12" style=" padding: 0px; ">
                                                <div class="col-md-6" style=" padding: 0px; ">
                                                    <div class="form-group">
                                                        <label>Categories</label>
                                                        <select name="categories[]" multiple class="form-control">
                                                        	@if($categories)
		                                                    	@foreach($categories as $cat)
		                                                    		<option @if(in_array($cat->id, $blog_categories)) selected @endif value="{{ $cat->id }}">
		                                                    		{{  json_decode($cat->title, 1)[1] }}
		                                                    		</option>
		                                                    	@endforeach
                                                        	@endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12" style=" padding: 0px; ">
                                                <div class="col-md-6" style=" padding: 0px; ">
                                                    <div class="form-group">
                                                        <label>URL</label>
		                                                <input value="{{ $Article->url }}" type="text"
		                                                       name="url"
		                                                       id="url"
		                                                       class="form-control">                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- nav-tabs-custom -->
                                            <div class="col-md-12" style=" padding: 0px; ">
                                                <div class="col-md-6" style=" padding: 0px; ">
                                                    <div class="form-group">
                                                        <label>Status</label>
                                                        <select name="status" id="status" class="form-control">
                                                            <option value="active" {{ $Article->status == 'active' ? 'selected' : '' }}>Active</option>
                                                            <option value="inactive" {{ $Article->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                        </select>
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
