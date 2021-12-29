@extends('layouts.layout')

@section('content')
    <div class="upload-top-info before_select before_upload" id="upload_section">
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


	<section class="new-edit-example hidden split_section after_upload" >
		<div class="app-title">
		    <div class="wrapper">
                <h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Split PDF by pages' !!}</h1>
                <p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Split specific page ranges or extract every page into a separate document' !!}</p>
		    </div>
			<div class="edit-message">Selected: <span data-value="filename">%file_name%</span></div>
			<div class="split_type_selector">
				<div class="edit-label">Choose an option</div>
				<div class="edit-choose-container">
				    <a href="#" class="edit-choose-block" data-type='every-page'>                  
						<div class="edit-to-photo">
							<img src="img/document-edit-icc-1.svg" alt="Alternate Text" />
							<img class="little-iccon" src="img/arrow-to-right.svg" alt="Alternate Text" />
							<img src="img/document-edit-icc-2.svg" alt="Alternate Text" />
							<div class="mini-title-photo">12 pages &#8250; 12 docs</div>
						</div>
						<div class="edit-to-text">Extract every page into a PDF</div>
				    </a>
				    <a href="#" class="edit-choose-block" id="select_pages_to_split" data-type='select-pages'>
						<div class="edit-to-photo">
							<img src="img/document-edit-icc-2.svg" alt="Alternate Text" />
							<img style="opacity: 0.3;" src="img/document-edit-icc-2.svg" alt="Alternate Text" />
							<img src="img/document-edit-icc-2.svg" alt="Alternate Text" />
							<div class="mini-title-photo">Visually select pages</div>
						</div>
						<div class="edit-to-text">Select pages to split</div>
				    </a>
				    <a href="#" class="edit-choose-block" data-type='every-x'>
						<div class="edit-to-photo">
							<img src="img/document-edit-icc-1.svg" alt="Alternate Text" />
							<img class="little-iccon" src="img/arrow-to-right.svg" alt="Alternate Text" />
							<img src="img/document-edit-icc-2.svg" alt="Alternate Text" />
							<div class="mini-title-photo">12 pages &#8250; 12 docs &#8250; 2 pages</div>
						</div>
						<div class="edit-to-text">Split every X pages</div>
				    </a>
				    <a href="#" class="edit-choose-block" data-type='every-even'>
						<div class="edit-to-photo">
							<img src="img/document-edit-icc-1.svg" alt="Alternate Text" />
							<img class="little-iccon" src="img/arrow-to-right.svg" alt="Alternate Text" />
							<img style="margin-right: -5px;" src="img/document-edit-icc-3.svg" alt="Alternate Text" />
							<img src="img/document-edit-icc-2.svg" alt="Alternate Text" />
							<div class="mini-title-photo">12 pages &#8250; 7 docs:1,2-3,4-5 etc.</div>
						</div>
						<div class="edit-to-text">Split every even page</div>
				    </a>
				</div>
				<div class="more-options-btns-wrap">
				    <button id="extract_every_page" class="options-btn" type="submit">Continue</button>
				</div>
		    </div>
		</div>
	</section>

    <section class="split-page-idit preview_section hidden after_upload" id="pages_preview_section">
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



   





	<section class="fixed-bottom-panel hidden" id="pages_ranges">
		<form class="fixed-task-form" autocomplete="off">
		    <div class="input-field-box">
				<h4>Pages</h4>
		        <div class="head-space hidden" id="page_range_block">
		            <div class="input-group">
		                <input name="pages_groups" id="pages_groups" type="text" autocomplete="off" placeholder="Example: 1-4,8-10,13,15">
		                <div class="input-group-addon">
		                    <a class="help-tooltip" target="_blank" href="#">
		                        <i class="far fa-question-circle"></i>
		                       <span class="tooltiptext">Enter page ranges like 1-4,8-10,13,15. This generates 4 documents, containing pages 1-4, 8-10, 13 and 15.</span>
		                    </a>
		                </div>
		            </div>
		        </div>

				<div class="split-every hidden" id="page_every_block">
					<span class="split-every-text">Split every</span>
					<input class="split-every-input" type="number" min="1" max="100" name="name" value="4" id="split_every" />
					<span class="split-every-text">pages</span>
					<span class="input-help tooltip-wrap">
					    <span class="tooltiptext">Get separate documents containing pages 1-5 then 6-10 and then 11-15 etc (using step of 5)</span>
					    <i class="far fa-question-circle"></i>
					</span>
				</div>

		    </div>
		    <div class="more-options-box" style="display: none;">
				<h4>Pages</h4>	
		        <div class="head-space">
		            <div class="input-group">
		                <input name="filename_patern" id="filename_patern" type="text" placeholder="[BASENAME]-[CURRENTPAGE]" value="[CURRENTPAGE]-[BASENAME]">
		                <div class="input-group-addon">
		                    <a target="_blank" href="#"><i class="far fa-question-circle"></i></a>
		                </div>
		            </div>
		        </div>
				<div class="split-every">
					<label class="split-every-text">
						<input type="checkbox" name="name" value="4" />
						Customize result names
					</label>
					<span class="input-help tooltip-wrap">
					    <span class="tooltiptext">Remove bookmarks and outline from the result.</span>
					    <i class="far fa-question-circle"></i>
					</span>
				</div>
		    </div>
		    <div class="more-options-btns-wrap more-options-btns-wrap-split">
		        <button class="options-btn" id="start_split_by_pages" type="submit">Split PDF by pages</button>
		        <a href="#" class="options-btn-transparent">More options</a>
		    </div>
		</form>
	</section>

	@include ('inc.result_block')

@endsection
