@extends('layouts.layout2')

@section('content')
    <div class="upload-top-info">
        <div class="container">
            <div class="app-title">
                <div class="wrapper">
                    <h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Combine and Reorder PDF Pages' !!}</h1>
                    <p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Merge pages from different documents, reorder pages if needed' !!}</p>
                </div>
            </div>

			<div class="welcome_outer">
				@if($ads && $device_is=='computer')
					@include("ads.adx250x250")
				@endif
		        
		        <div class="app-welcome">
		            <form action="#" id="drop_zone">
		                <div class="upload-img">
		                    <img src="/img/pdf-img.svg" alt="">
		                </div>
		                <h3>UPLOAD <strong>PDF</strong> FILE</h3>
		                @php
		                    $accept = 'application/pdf';
		                @endphp
		                @include('includes.upload-button-multiple')
		            </form>
		            <div class="upload-welcom-descr">
		                {!! array_key_exists(3, $PageBlocks) ? $PageBlocks[3] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.' !!}
		            </div>
		        </div>

				@if($ads && $device_is=='computer')
					@include("ads.adx250x250")
				@endif
		    </div>
		    
        </div>

		@if($ads && $device_is=='phone')
			@include("ads.adx320x100")
		@endif  

		@if($ads && $device_is=='computer')
			@include("ads.adx970x90")
		@endif



    </div>

    <section class="combine-reorder-selector-wrap">
        <div class="combine-reorder-selector-header">
            <div id="changeFileSelectorBtn" class="btn-group">
                <button type="button" class="btn dropdown-toggle dropdown-toggle-on-hover">
                    <span class="filename"></span>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                </ul>
            </div>
            <button class="close"><span>×</span></button>
        </div>
        <div id="file-page-selector-opts">
            <a href="#" id="SelectAllPanel">{!! t("Select All") !!}</a>
            <a href="#" id="SelectNonePanel">{!! t("Select None") !!}</a>
        </div>

        <div class="combine-reorder-selector-wrap-list">
        </div>
    </section>

    <section class="s-combine-reorder">
        <div class="combine-reorder-top-info">
            <div class="container">
                <p>{!! array_key_exists(4, $PageBlocks) ? $PageBlocks[4] : 'Click pages to select. Shift to select multiple. Drag & drop to reorder.' !!}</p>
                <div class="split-range">
                    <img src="/img/search-plus.svg" alt="Alternate Text">
                    <input type="range" id="preview_zoom" name="name" value="100" max="400" min="100">
                    <img src="/img/search-minus.svg" alt="Alternate Text">
                </div>
            </div>
        </div>
        <div class="combine-reorder-main-content">
            <div class="container">
                <div class="combine-reorder-tools">
                    <div class="btn-group upload-btn-group">
						<span class="btn fileinput-button">
							<i class="far fa-file-pdf"></i>{!! t("Add More Files") !!}
							<form action="#" enctype="multipart/form-data" method="post">
								<input accept=".pdf" title="Upload" multiple="multiple" data-scope="task-file" name="file" type="file" class="fileupload upload-file-tool">
							</form>
						</span>
                        <button class="btn dropdown-toggle">
                            <span class="caret"></span>
                            <span class="sr-only">{!! t("Toggle Dropdown") !!}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-xs">
                            <li><a class="drpbox-chooser" href="#"><i class="fab fa-dropbox icon"></i> Dropbox</a></li>
                            <li><a class="gdrive-chooser" href="#"><img src="/img/gdrive.png" class="icon"> Google Drive</a></li>
                        </ul>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-default dropdown-toggle">
                            <i class="far fa-copy"></i>
                            {!! t("Files") !!}
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu file-page-selector-opts" id="FileList">
                        </ul>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-default dropdown-toggle">
                            <i class="far fa-hand-pointer"></i>
                            {!! t("Selection") !!}
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu selection-opts">
                            <li><a href="#" id="SelectAll">{!! t("Select All") !!}</a></li>
                            <li><a href="#" id="DeselectAll">{!! t("Deselect All") !!}</a></li>
                            <li><a href="#" id="InvertSelection">{!! t("Invert Selection") !!}</a></li>
                            <li class="divider"></li>
                            <li><a href="#" id="RemoveSelected">{!! t("Remove Selected")  !!}</a></li>
                        </ul>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-default dropdown-toggle">
                            <i class="far fa-sort-alpha-asc"></i>
                            Reorder
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu reorder-opts">
                            <li><a href="#" id="ReverseList">{!! t("Reverse order (Last -&gt; First)") !!}</a></li>
                        </ul>
                    </div>
                    <button class="btn btn-default" id="addBlankPageBtn">
                        <i class="far fa-file"></i> {!! t("Add Blank Page") !!}
                    </button>
                    <div class="btn-group">
                        <button class="btn btn-default dropdown-toggle rounded-right">
                            <i class="far fa-trash-alt"></i>
                            {{ t("Clear") }}
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu clear-opts">
                            <li><a href="#" id="ClearAll">{!! t("Clear All") !!}</a></li>
                            <li class="divider"></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="combine-reorder-lists">
                    <ul class="image-canvas-list" id="sortable">

                    </ul>
                </div>
            </div>
        </div>
    </section>

    @if (count($PageGuides))
        <section class="how-it-works">
            @foreach ($PageGuides as $Guide)
                @if (!is_null($Guide->title))
                    <div class="title-section"><h2>{{ $Guide->title }}</h2></div>
                @endif

				@if($ads && $device_is=='phone')
					@include("ads.adx320x100")
				@endif  

                <div class="container centered">
                    @if (!is_null($Guide->subtitle))
                    <p class="title-description">{{ $Guide->subtitle }}</p>
                    @endif
                    @if (!is_null($Guide->content))
                    <div class="post">
                        {!! htmlspecialchars_decode($Guide->content) !!}
                    </div>
                    @endif
                </div>
            @endforeach
        </section>
    @endif

    <section class="how-it-works">

        <div class="contact-btn">
            <a class="button-green contact-btn-popup" href="#contactFormModal">{!! t("Contact Support") !!}</a>
        </div>

    </section>

    <section class="fixed-bottom-panel">
        <form class="fixed-task-form">
            <div class="select-dropup-bottom-btn-wrap">
                <button class="options-btn save-pdf" type="button">{!! t("Save") !!}</button>
            </div>
        </form>
    </section>

    @include ('inc.result_block_new')
@endsection
