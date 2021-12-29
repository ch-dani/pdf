@extends('layouts.layout')

@section('content')
    <div class="upload-top-info before_select" id="upload_section">
        <div class="container">
            <div class="app-title">
                <div class="wrapper">
                    <h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Split PDF by pages' !!}</h1>
                    <p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Split specific page ranges or extract every page into a separate document' !!}</p>
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

	<section class="new-edit-example hidden split_section" >
		<div class="app-title">
		    <div class="wrapper">
                <h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Split PDF by pages' !!}</h1>
                <p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Split specific page ranges or extract every page into a separate document' !!}</p>
		    </div>
			<div class="edit-message">Selected: <span data-value="filename">%file_name%</span></div>
			
		</div>
	</section>

    <section class="split-page-idit preview_section hidden" id="pages_preview_section">
        <div class="split-page-top">
            <p>Click pages to select.</p>
            <a class="button-green clear-btn" id="reset_selected_pages" href="#">Reset selection</a>
			<div class="split-range">
				<img src="img/search-plus.svg" alt="Alternate Text" />
				<input type="range" id="preview_zoom" name="name" value="100" max="160" min="80" />
				<img src="img/search-minus.svg" alt="Alternate Text" />
			</div>
        </div>
		<div class="split-main-wrap" id="pages_previews_here">
			<div class="preview_page_block split-main-block extract_block_not_split page_1" data-page-id="1">
				<div class="split-main-num">1</div>
				<div class="split-main-photo">
					<canvas data-rotate="0" data-page-id="1" id="page_canvas_1" pt-width="279.4000000000176" pt-height="215.9000000000136" height="154" width="200" rotation="90"></canvas>
				</div>
			</div>
		 </div>
    </section>



	<section class="fixed-bottom-panel hidden" id="pages_ranges">
        <form class="fixed-task-form">
            <div class="image-radio-item">
                <span class="btns-resolution-span">Selected pages:</span>
                <div class="btns-resolution">
                    <label class="resolution-item">
                        <input type="radio" name="selection_type" value="odd" >
                        <span class="resolution-item-checkmark">
                            All Odd Pages
                            <span class="input-help tooltip">
                                <span class="tooltiptext">Get a new document containing only the odd pages 1,3,5,7 etc from the original.</span>
                                <i class="far fa-question-circle"></i>
                            </span>
                        </span>
                    </label>
                    <label class="resolution-item">
                        <input type="radio" name="selection_type" value="even">
                        <span class="resolution-item-checkmark">
                            All Even Pages
                            <span class="input-help tooltip">
                                <span class="tooltiptext">Get a new document containing only the even pages 2,4,6,8, etc from the original.</span>
                                <i class="far fa-question-circle"></i>
                            </span>
                        </span>
                    </label>
                    <label class="resolution-item">
                        <input type="radio" name="selection_type" value="manual" checked="checked">
                        <span class="resolution-item-checkmark">
                            Specific pages
                        </span>
                    </label>
                </div>
            </div>
            <div class="head-space mb20">
                <div class="input-group">
                    <input name="pages_groups" type="text" id="pages_groups" placeholder="Example: 1-4,8-10,13,15">
                    <div class="input-group-addon">
                        <a target="_blank" href="#"><i class="far fa-question-circle"></i></a>
                    </div>
                </div>
            </div>
            <div class="more-options-box">
                <div class="checkbox-item-content">
                    <label>
                        <input id="discard_bookmarks" value="true" type="checkbox">
                        <span>Discard bookmarks</span>
                    </label>
                    <span class="input-help tooltip">
                        <span class="tooltiptext">Remove bookmarks and outline from the result.</span>
                        <i class="far fa-question-circle"></i>
                    </span>
                </div>
            </div>
            <div class="more-options-btns-wrap">
                <button class="options-btn" type="button" id="start_task">Extract pages</button>
                <a href="#" class="options-btn-transparent">More options</a>
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

    <section class="how-it-works">

    	<div class="contact-btn">
    		<a class="button-green contact-btn-popup" href="#contactFormModal">Contact Support</a>
    	</div>

    </section>

@endsection
