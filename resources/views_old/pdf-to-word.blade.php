@extends('layouts.layout')

@section('content')
    <div class="upload-top-info before_upload">
    	<div class="container">
    		<div class="app-title">
    			<div class="wrapper">
    				<h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Convert PDF to Word' !!}</h1>
    				<p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Creates a Microsoft Word .docx with text and images from PDF, optimizing for legibility' !!}</p>
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

	<section class="encrypt-new-edit after_upload hidden" id="pdf_to_docx_section">
		<div class="app-title">
			<div class="wrapper">
				<h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Convert PDF to Word' !!}</h1>
				<p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Creates a Microsoft Word .docx with text and images from PDF, optimizing for legibility' !!}</p>
			</div>
			<div class="edit-message">Selected: <span class="current_filename"></span></div>
		</div>
		<div class="encrypt-form">
			<form class="fixed-task-form">
				<div class="more-options-box" style="display: none;">
				    <div class="head-space">
						<div class="encrypt-forms">
							<label>
							    <input type="checkbox" name="name" value="">
							    <span class="encrypt-span">Resize large images</span>
							</label>
						</div>
				    </div>
				</div>
				<div class="more-options-btns-wrap more-options-btns-wrap-split">
				    <button class="options-btn" id="start_convert_to_docx" type="submit">Convert to Word</button>
				    <a href="#" class="options-btn-transparent">More options</a>
				</div>
			</form>
		</div>
	</section>

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


	@include ('inc.result_block')
@endsection

