@extends('layouts.layout')

@php
	$accept = 'application/pdf';
@endphp

@section('content-freeconvert')
	<main class="file_not_loaded step1">
		@include('page_parts.toolheader')

		<section id="watermark_section" class="tool_section crop-section bg-grey bg_grey_patterns after_upload hidden">
			<div class="container">
				<div class="title-wrapper">
					<h2 class="h30-title title_main">{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Encrypt and Protect PDF online' !!}</h2>
					<h3 class="sub-title">{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Protect PDF file with password and permissions. Add restrictions to your PDF file' !!}</h3>
				</div>

				<div id="preview_section" class="crop-section__page">
					<div class="watermark-pdf-block" id="preview_block">
						<div id="pages_previews_here">

						</div>
					</div>
				</div>

				<ul class="document_add_element_submenu">
					<li><a id="add_text_wattermark" href="">@php include(public_path('freeconvert/img/document_add_element_submenu_text.svg')) @endphp {{ t("Add text") }}</a></li>
					<li><label>
							@php include(public_path('freeconvert/img/document_add_element_submenu_image.svg')) @endphp {{ t("Add image") }}
							<input id="image_upload" type="file" accept="image/x-png,image/gif,image/jpeg" name="name" value="" />
						</label>
					</li>
				</ul>

				<div class="contact-us">
					<a id="start_task" class="contact-us__button btn-gradient" href="#"><img src="{{ asset('freeconvert/img/download.svg') }}" width="30" height="30"> {{ t("Process PDF") }}</a>
				</div>

				<div class="link_convert one_item">
					<div class="link_convert_left">
						<a href="#" class="link_convert_item remove">
							@php include(public_path('freeconvert/img/link_conver-3.svg')) @endphp
							{{ t("Remove") }}
						</a>
					</div>
				</div>

				{{--
				<div class="link_download">
					<ul class="save">
						<li class="save__li"><a href=""><img src="{{ asset('freeconvert/img/logo_google-drive.svg') }}" width="26" height="23">{{ t("Save to Google Drive") }}</a></li>
						<li class="save__li"><a href=""><img src="{{ asset('freeconvert/img/logo_dropbox.svg') }}" width="28" height="23">{{ t("Save to Dropbox") }}</a></li>
					</ul>
				</div>
				--}}
			</div>
		</section>

		<section class="module__how-convert module bg-white pb_5">
			<div class="container">
				<div class="title-wrapper">
					<h2 class="h2-title title_main">{{ t("How to Add Watermark to PDF") }}</h2>
				</div>
				<div class="row">
					@if (count($PageGuides))
						@foreach ($PageGuides as $Guide)
							@if (!is_null($Guide->content))
								{!! htmlspecialchars_decode($Guide->content) !!}
							@endif
						@endforeach
					@endif

					@if(!Auth::id())
						<div class="contact-us">
							<a class="contact-us__button sign-up-trigger" href="{{route("login")}}">{{ t("Sign Up") }}</a>
						</div>
					@endif
				</div>
			</div>
		</section>

		@include('inc-freeconvert.banner')
	</main>
@endsection

@section('css')
	<link rel="stylesheet" href="{{ asset('libs/jquery-ui/jquery-ui.css') }}">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css">
	<style>
		@font-face {
			font-family: 'Courier';
			src: url('/editor_fonts/Courier.ttf')  format('truetype');
		}

		@font-face {
			font-family: 'Helvetica2';
			src: url('/fonts/Helvetica.eot');
			src: local('☺'), url('/editor_fonts/Helvetica.woff') format('woff'), url('/editor_fonts/Helvetica.ttf') format('truetype'), url('/editor_fonts/Helvetica.svg') format('svg');
			font-weight: normal;
			font-style: normal;
		}

		.canvas_outer{
			overflow: hidden;
		}
	</style>
@endsection

@section('js')
	<script src="{{asset('/js/watermarkpdf.js')}}"></script>
	<script src="{{ asset('libs/jquery-ui/jquery-ui.js') }}"></script>
	<script src="{{asset('/js/jquery.ui.rotatable.js')}}"></script>
@endsection

@section('content')
<style>

@font-face {
	font-family: 'Courier';
	src: url('/editor_fonts/Courier.ttf')  format('truetype');
}

@font-face {
	font-family: 'Helvetica2';
	src: url('/fonts/Helvetica.eot');
	src: local('☺'), url('/editor_fonts/Helvetica.woff') format('woff'), url('/editor_fonts/Helvetica.ttf') format('truetype'), url('/editor_fonts/Helvetica.svg') format('svg');
	font-weight: normal;
	font-style: normal;
}

.canvas_outer{
	overflow: hidden;
}

</style>
    <div class="upload-top-info before_upload">
    	<div class="container">
    		<div class="app-title">
    			<div class="wrapper">
    				<h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Watermark PDF Online' !!}</h1>
    				<p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Add image or text watermark to PDF documents' !!}</p>
    				<span id="helvetica_l" style="font-family: Helvetica2; opacity: 0">helvetica</span>
    			</div>
    		</div>

			<div class="welcome_outer">
				@if($ads && $device_is=='computer')
					@include("ads.adx250x250")
				@endif   
				<div class="app-welcome">
					<form action="#" id="drop_zone">
		            	<input type="hidden" value="<?php echo csrf_token() ?>" id="editor_csrf">    			    			
						<div class="upload-img">
							<img src="/img/pdf-img.svg" alt="">
						</div>
						<h3>{!! t("UPLOAD <strong>PDF</strong> FILE") !!}</h3>
						@include('inc.uploadButton')
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

		@if($ads && $device_is=='computer')
			@include("ads.adx970x90")
		@endif

		@if($ads && $device_is=='phone')
			@include("ads.adx320x100")
		@endif
    	
    </div>


	<section class="watermark-new after_upload hidden">
		<div class="app-title">
            <div class="wrapper">
    				<h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Watermark PDF Online' !!}</h1>
    				<p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Add image or text watermark to PDF documents' !!}</p>
            </div>
        </div>
	</section>

	<section class="watermark-eit-wrap after_upload hidden" id="watermark_section">
		<div class="watermark-btns">
			<a class="watermark-btn-block watermark-add-text" id="add_text_wattermark" >{!! t("Add text watermark"); !!}</a>
			<label class="watermark-btn-block watermark-add-image">
			    <input id="image_upload" type="file" accept="image/x-png,image/gif,image/jpeg" name="name" value="" />
			    <span>{!! t("Add Image") !!}</span>
			</label>
		</div>
		<div id="preview_section">
			<div class="watermark-pdf-block" id="preview_block">
				<div id="pages_previews_here">
				
				</div>
			</div>
		</div>
	</section>
	<section class="fixed-bottom-panel after_upload hidden" style="z-index: 12000;">
        <form class="fixed-task-form">
            <div class="more-options-btns-wrap">
                <button class="options-btn" type="button" id="start_task">Watermark PDF</button>
            </div>
        </form>
    </section>

	@include ('inc.result_block_new')


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

@endsection
