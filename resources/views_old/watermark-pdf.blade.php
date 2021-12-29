@extends('layouts.layout')

@section('content')
<style>

@font-face {
	font-family: 'Courier';
	src: url('/editor_fonts/Courier.ttf')  format('truetype');
}

@font-face {
	font-family: 'Helvetica2';
	src: url('fonts/Helvetica.eot');
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
    		<div class="app-welcome">
    			<form action="#" id="drop_zone">
                	<input type="hidden" value="<?php echo csrf_token() ?>" id="editor_csrf">    			    			
    				<div class="upload-img">
    					<img src="img/pdf-img.svg" alt="">
    				</div>
    				<h3>UPLOAD <strong>PDF</strong> FILE</h3>
    				@include('inc.uploadButton')
    			</form>
    			<div class="upload-welcom-descr">
    				{!! array_key_exists(3, $PageBlocks) ? $PageBlocks[3] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.' !!}
    			</div>
    		</div>
    	</div>
    </div>


	<section class="watermark-new after_upload">
		<div class="app-title">
            <div class="wrapper">
                <h1>Watermark PDF Online</h1>
                <p>Add image or text watermark to PDF documents</p>
            </div>
        </div>
	</section>

	<section class="watermark-eit-wrap after_upload hidden" id="watermark_section">
		<div class="watermark-btns">
			<a class="watermark-btn-block watermark-add-text" id="add_text_wattermark" >Add text watermark</a>
			<label class="watermark-btn-block watermark-add-image">
			    <input id="image_upload" type="file" accept="image/x-png,image/gif,image/jpeg" name="name" value="" />
			    <span>Add Image</span>
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

	@include ('inc.result_block')


    @if (count($PageGuides))
        <section class="how-it-works">
            @foreach ($PageGuides as $Guide)
                @if (!is_null($Guide->title))
                    <div class="title-section"><h2>{{ $Guide->title }}</h2></div>
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
