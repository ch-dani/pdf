@extends('layouts.layout')

@section('content')
    <div class="upload-top-info before_upload" id="uploader_section">
    	<div class="container">
    		<div class="app-title">
    			<div class="wrapper">
    				<h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Split PDF By Text Content' !!}</h1>
    				<p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Extract separate documents when specific text changes from page to page' !!}</p>
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




    <section class="split-pdf-by-text-uploaded after_upload hidden" id="split_by_text">
        <div class="selection-help">Click and drag to select text that is different per split document. </div>
        <div class="split-pdf-by-text-uploaded-content">
            <div class="container">
                <div class="nav-pages">
                    <a class="prev-page-btn next_prev disabled" data-type="prev"><i class="fas fa-angle-left"></i> Previous</a>
                    <a class="next-page-btn next_prev" data-type="next">Next <i class="fas fa-angle-right"></i> </a>
                </div>
                <div class="pages_preview_here">
                	<img src="img/placeholder-circle.svg" alt="Loading...">
                </div>
            </div>
        </div>
        
    </section>

    <section class="fixed-bottom-panel after_upload hidden" style="z-index: 1000;">
        <form class="fixed-task-form">
            <div class="input-field-box">
                <div class="head-space">
                    <label>Text should start with: (optional)</label>
                    <div class="input-group">
                        <input id="text_start_from" type="text" placeholder="Example: Address:">
                        <div class="input-group-addon">
                            <a class="help-tooltip" target="_blank" href="#">
                                <i class="far fa-question-circle"></i>
                                <span class="tooltiptext">If there are pages that match but should not trigger a split, use this to filter them out</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="more-options-box">
                <div class="head-space">
                    <label>Customize result names</label>
                    <div class="input-group">
                        <input id="file_name_pattern" type="text" placeholder="[BASENAME]-[CURRENTPAGE]" value="[BASENAME]-[CURRENTPAGE]">
                        <div class="input-group-addon">
                            <a target="_blank" href="#"><i class="far fa-question-circle"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="more-options-btns-wrap more-options-btns-wrap-split">
                <button class="options-btn" type="button" id="save">Split PDF by text</button>
                <a href="#" class="options-btn-transparent">More options</a>
            </div>
        </form>
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

    <section class="how-it-works">

    	<div class="contact-btn">
    		<a class="button-green contact-btn-popup" href="#contactFormModal">Contact Support</a>
    	</div>

    </section>
    
	@include ('inc.result_block')
    

@endsection

