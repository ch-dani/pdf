
@extends('layouts.layout')

@section('content')

	<style>

	.text_content_element{
		opacity: 0;
	}

	.text_content_element:not(.spe_element){
		pointer-events: none !important;
	}
	
	.create_new_page, .page-side-bar{
		display: none !important;
	}
	
	
	</style>
	

    <div class="upload-top-info before_upload">
    	<div class="container">
    		<div class="app-title">
    			<div class="wrapper">
    				<h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'PDF to Excel or CSV' !!}</h1>
    				<p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Convert PDF to Excel or CSV online for free. Extract table data from PDF' !!}</p>
    			</div>
    		</div>
    		<div class="app-welcome">
    			<form action="#" id="drop_zone">
                	<input type="hidden" value="<?php echo csrf_token() ?>" id="editor_csrf">       			
    				<div class="upload-img">
    					<img src="img/pdf-img.svg" alt="">
    				</div>
    				<h3>UPLOAD <strong>Excel</strong> FILE</h3>
		                        @include('inc.uploadButton')	
    			</form>
    			<div class="upload-welcom-descr">
    				{!! array_key_exists(3, $PageBlocks) ? $PageBlocks[3] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.' !!}
    			</div>
    		</div>
    	</div>
    </div>

	<div id="app-root" class="excel_pdf" style="display: none;">
		<div class="container">
		    
		</div>
		<div class="app-title app-title-editor">
		    <div class="wrapper">
				<h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'PDF to Excel or CSV' !!}</h1>
				<p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Convert PDF to Excel or CSV online for free. Extract table data from PDF' !!}</p>
		    </div>
		</div>
		<div class="app-body">
		    <div class="app-workspace">
		        <div class="page-container">
		            <div class="page-between page-between-first">
		                <a href="#" class="insert-page insert_first_page">Insert Page Here</a>
		            </div>
		            <div class="container" style="width: auto; padding: 0;">
		                <div class="page-main-part" style="width: auto;">
		                    @include ('inc.editmenu')
		                    <div id="selectable_div"></div>
		                    <div id="simplePDFEditor" current_editor="text">
		                        <div id="viewer" class="pdfViewer"></div>
		                    </div>
		                </div>

		            </div>

		        </div>
		    </div>
		</div>
	</div>
    

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

    <section class="how-it-works">

    	<div class="contact-btn">
    		<a class="button-green contact-btn-popup" href="#contactFormModal">Contact Support</a>
    	</div>

    </section>


    <section class="fixed-bottom-panel after_upload hidden">
        <form class="fixed-task-form">
            <div class="more-options-btns-wrap">
                <a href="#" class="options-btn csv" id="save_csv">Convert to CSV</a>
                <button class="options-btn" type="button" id="save_pdf">Convert to Excel</button>
            </div>
        </form>
    </section>

	@include ('inc.result_block')

@endsection

