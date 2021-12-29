@extends('layouts.layout')

@section('content')
    <div class="upload-top-info before_upload" id="upload_section">
    	<div class="container">
    		<div class="app-title">
    			<div class="wrapper">
    				<h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Split PDF by file size' !!}</h1>
    				<p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Get multiple smaller documents with specific file sizes' !!}</p>
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
						{!! t("<h3>UPLOAD <strong>PDF</strong> FILE</h3>") !!}
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

			@if($ads && $device_is=='computer')
				@include("ads.adx970x90")
			@endif

			@if($ads && $device_is=='phone')
				@include("ads.adx320x100")
			@endif


    	</div>
    </div>


    <section class="split-pdf-by-size after_upload hidden" id="split_by_size_section">
        <div class="container">
    		<div class="app-title">
    			<div class="wrapper">
    				<h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Split PDF by file size' !!}</h1>
    				<p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Get multiple smaller documents with specific file sizes' !!}</p>
    			</div>
        
            <div class="file-name-split-pdf-by-size">
                Selected: <span class='file_name_here'></span>
            </div>
        </div>
        <form class="fixed-task-form">
            <div class="split-pdf-by-size-form-options">
                <div class="image-radio-item">
                    <span class="btns-resolution-span">{!! array_key_exists(4, $PageBlocks) ? $PageBlocks[4] : 'Maximum size for the largest split document' !!}</span>
                    <div class="btns-resolution">
                        <label class="resolution-item">
                            <input class="split-input" id="split_size" type="text" name="size" value="10">
                        </label>
                        <label class="resolution-item">
                            <input type="radio" name="size_unit" id="resolution1" checked="checked" value="MB">
                            <span class="resolution-item-checkmark">
                                MB
                            </span>
                        </label>
                        <label class="resolution-item">
                            <input type="radio" name="size_unit" id="resolution2" value="KB">
                            <span class="resolution-item-checkmark">
                                KB
                            </span>
                        </label>
                        <span class="input-help tooltip">
                            <span class="tooltiptext">{!! array_key_exists(5, $PageBlocks) ? $PageBlocks[5] : 'A 23 MB document could be split into 3 smaller documents of max 10 MB.' !!}</span>
                            <i class="far fa-question-circle"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="more-options-btns-wrap">
                <button class="options-btn" type="button" id="start_task">{!! array_key_exists(6, $PageBlocks) ? $PageBlocks[6] : 'Split PDF by size' !!}</button>
            </div>
        </form>
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

    <section class="how-it-works before_upload">

    	<div class="contact-btn">
    		<a class="button-green contact-btn-popup" href="#contactFormModal">{{ t("Contact Support") }}</a>
    	</div>

    </section>

	@include ('inc.result_block_new')
    
@endsection
