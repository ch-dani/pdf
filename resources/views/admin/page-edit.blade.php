@extends('layouts.admin')

@section('content')
    <div class="main-wrap">

        @include('admin.includes.slidebar')

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Edit page
                </h1>
            </section>
            <section class="content">
                <form id="EditPageForm">
                    {{ csrf_field() }}
                    <input type="hidden" name="page_id" value="{{ $Page->id }}">
                    <div class="container-fluid spark-screen">
                        <div class="row">
                            <div class="col-md-4" style=" padding: 0px; ">
                                <div class="box box-primary">
                                    <div class="box-body pad">
                                        @if ($Page->home == 0)
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select name="status" id="status" class="form-control">
                                                    <option value="publish" {{ $Page->status == 'publish' ? 'selected' : '' }}>Publish</option>
                                                    <option value="draft" {{ $Page->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                                    <option value="trash" {{ $Page->status == 'trash' ? 'selected' : '' }}>Trash</option>
                                                </select>
                                            </div>
                                        @else
                                            <input type="hidden" name="status" value="publish">
                                        @endif
                                        <div class="form-group">
                                            <label>Created:</label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                                <input type="text" value="{{ date('H:i F d, Y', strtotime($Page->created_at)) }}" disabled class="form-control pull-right">
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                        <div class="form-group">
                                            <label>Updated:</label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                                <input type="text" value="{{ date('H:i F d, Y', strtotime($Page->updated_at)) }}" disabled class="form-control pull-right">
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                        @if ($Page->static == 0)
                                            <button type="button" class="btn btn-block btn-danger btn-xs DeletePage" data-id="{{ $Page->id }}">Remove</button>
                                        @endif
                                        <button type="submit" class="btn btn-default btn-block">Save</button>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                                @if (!$Page->home and !$Page->added_dashboard)
                                    <div class="box box-primary">
                                        <div class="box-body pad">
                                            <div class="form-group">
                                                <label for="tool">Tool Name</label>
                                                <input type="text" class="form-control" id="tool" name="tool" value="{{ $Page->tool }}" placeholder="Tool Name">
                                            </div>
                                        </div>
                                        <!-- /.box-body -->
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-8" style=" padding: 0px 0px 0px 15px; ">
                                <div class="box box-primary">
                                    <div class="box-body pad">
                                        <div class="form-group">
                                            <label for="title">Title</label>
                                            <input type="text" class="form-control" id="title" name="title" value="{{ $Page->title }}" placeholder="Title">
                                        </div>
                                        @if ($Page->home == 0)
                                            <div class="form-group">
                                                <label for="link">Link</label>
                                                <input type="text" class="form-control" id="link" name="link" value="{{ $Page->link }}" placeholder="Link">
                                            </div>
                                        @else
                                            <input type="hidden" name="link" value="{{ $Page->link }}">
                                        @endif
                                        @if ($Page->static == 0 or $Page->added_dashboard)
                                            <textarea id="content" name="content" rows="10" cols="80">{{ $Page->content }}</textarea>
                                        @endif
                                    </div>
                                </div>
                                @if (is_array($Blocks) and isset($Blocks[1]))
                                    <div class="box box-primary">
                                        <div class="box-body pad">
                                            <div class="nav-tabs-custom">
                                                <ul class="nav nav-tabs">
                                                    @foreach ($Languages as $key => $Language)
                                                        <li {!! $key == 0 ? 'class="active"' : '' !!}><a href="#{{ str_replace(' ', '_', $Language->name) }}" data-toggle="tab"><img style=" margin-right: 10px; " src="{{ $Language->flag }}" />{{ $Language->name }}</a></li>
                                                    @endforeach
                                                </ul>
                                                <div class="tab-content">
                                                    @php
                                                        $seo_title = json_decode($Page->seo_title, true);
                                                        $seo_keywords = json_decode($Page->seo_keywords, true);
                                                        $seo_description = json_decode($Page->seo_description, true);
                                                    @endphp
                                                    @foreach ($Languages as $key => $Language)
                                                        <div class="tab-pane {{ $key == 0 ? 'active' : '' }}" id="{{ str_replace(' ', '_', $Language->name) }}">
                                                            @if (isset($Blocks[$Language->id]) and count($Blocks[$Language->id]))
                                                                @foreach ($Blocks[$Language->id] as $key => $block)
                                                                    <div class="form-group">
                                                                        <label for="blocks[{{ $Language->id }}][{{ $key }}]">Block #{{ $key }}</label>
                                                                        <input type="text" class="form-control" id="blocks[{{ $Language->id }}][{{ $key }}]" name="blocks[{{ $Language->id }}][{{ $key }}]" value="{{ $block }}" placeholder="">
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                @foreach ($Blocks[1] as $key => $block)
                                                                    <div class="form-group">
                                                                        <label for="blocks[{{ $Language->id }}][{{ $key }}]">Block #{{ $key }}</label>
                                                                        <input type="text" class="form-control" id="blocks[{{ $Language->id }}][{{ $key }}]" name="blocks[{{ $Language->id }}][{{ $key }}]" value="" placeholder="">
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                            <hr>
                                                            <div class="form-group">
                                                                <label for="seo_title[{{ $Language->id }}]">SEO Title</label>
                                                                <input type="text" class="form-control" id="seo_title[{{ $Language->id }}]" name="seo_title[{{ $Language->id }}]" value="{{ isset($seo_title[$Language->id]) ? $seo_title[$Language->id] : '' }}" placeholder="SEO Title">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="seo_keywords[{{ $Language->id }}]">SEO Keywords</label>
                                                                <input type="text" class="form-control" id="seo_keywords[{{ $Language->id }}]" name="seo_keywords[{{ $Language->id }}]" value="{{ isset($seo_keywords[$Language->id]) ? $seo_keywords[$Language->id] : '' }}" placeholder="SEO Keywords">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="seo_description[{{ $Language->id }}]">SEO Description</label>
                                                                <textarea class="col-md-12" id="seo_description[{{ $Language->id }}]" name="seo_description[{{ $Language->id }}]" rows="2" cols="2">{{ isset($seo_description[$Language->id]) ? $seo_description[$Language->id] : '' }}</textarea>
                                                            </div>
                                                            
                                                            
			<hr>
            <div class="form-group">
            	<br><br><br>
                <label>6 bottom blocks</label>
            </div>
            	
            <?php 
            $flag = false;
            $it = 1;
            foreach(range(0,11) as $kk=>$b){ ?>
            	<?php 
            	$bblock_val = $BottomBlocks[$Language->id][$b] ?? "";
            	$block_name = "bottom_blocks[{$Language->id}][$b]";
            	?>
		        <div class="form-group bottom_blocks">
		            <label for="{$block_name}}"><?= (!$flag?"Block title": "Block content ") ?> {{$it}}</label>
                    <textarea id="{{$block_name}}" name="{{$block_name}}" rows="10" cols="80">{{ $bblock_val }}</textarea>
		        </div>
		        <?php
            	if($flag){
            		$it++;
            	}
		        
		         ?>
            <?php $flag = !$flag; } ?>
                                                            
                                                        </div>
                                                    @endforeach
                                                    <!-- /.tab-pane -->
                                                </div>
                                                <!-- /.tab-content -->
                                            </div>
                                            <!-- nav-tabs-custom -->
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
    
    <style>
    	.bottom_blocks{
    		flex-direction: column;
    		display: flex;

    	}
    </style>
@endsection
