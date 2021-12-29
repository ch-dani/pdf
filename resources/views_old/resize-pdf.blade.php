@extends('layouts.layout')

@section('content')
    <div class="upload-top-info before_upload">
    	<div class="container">
    		<div class="app-title">
    			<div class="wrapper">
    				<h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Resize PDF' !!}</h1>
    				<p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Add page margins and padding, Change PDF page size' !!}</p>
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

	<section class="new-edit-resize after_upload hidden" id="resize_section">
        <div class="app-title">
            <div class="wrapper">
                <h1>Resize PDF</h1>
                <p>Add page margins and padding, Change PDF page size</p>
				<div class="related-text">
					Related &#8250;
					<a href="#">Reduce the file size of your PDF online</a>
				</div>
            </div>
        </div>
        <div class="edit-choose-container">
			<div class="choose-an-option">Choose an option</div>
            <a href="#" class="edit-choose-block" data-type="margins">                  
				<div class="edit-to-photo">
					<img src="img/document-edit-icc-4.svg" alt="Alternate Text">
					<img class="little-iccon" src="img/arrow-to-right.svg" alt="Alternate Text">
				    <img src="img/document-edit-icc-4.svg" alt="Alternate Text">
				</div>
				<div class="edit-to-text">Add margins and padding</div>
			</a>
			<a href="#" class="edit-choose-block" data-type="page_size">
				<div class="edit-to-photo">
					<img style="width: 25px;margin-top:2px;" src="img/document-edit-icc-4.svg" alt="Alternate Text">
					<img class="little-iccon" src="img/arrow-to-right.svg" alt="Alternate Text">
				    <img style="max-width: 40px;max-height: 33px;" src="img/document-edit-icc-4.svg" alt="Alternate Text">
				</div>
				<div class="edit-to-text">Change page size</div>
            </a>
            @if(false)
			<a href="#" class="edit-choose-block">
				<div class="edit-to-photo">
					<img style="width: 25px;margin-top:2px;" src="img/document-edit-icc-1.svg" alt="Alternate Text">
					<img class="little-iccon" src="img/arrow-to-right.svg" alt="Alternate Text">
				    <img style="max-width: 40px;max-height: 33px;" src="img/document-edit-icc-1.svg" alt="Alternate Text">
					<div class="mini-title-photo">120Mb &#8250; 60Kb</div>
				</div>
				<div class="edit-to-text">Reduce the file size </div>
		    </a>
			<div class="btn-resize-continue">
				<a href="#" class="button-green">Continue</a>
			</div>
			@endif
        </div>
    </section>
    
	<section class="split-page-idit resize-split-wrap hidden" id="preview_section">
		<div class="split-page-top">
		    <div class="split-range">
		        <img src="img/search-plus.svg" alt="Alternate Text">
		        <input type="range" name="preview_zoom" value="" value="100" max="160" min="80" id="preview_zoom">
		        <img src="img/search-minus.svg" alt="Alternate Text">
		    </div>
		</div>

		<div class="split-main-wrap" id="pages_previews_here">
		
			<div class="split-extract-block resize-margin-block">
			<div class="split-main-num">1</div>
				<div class="split-main-photo">
				Loading...
				</div>
				<div class="resize-text">
					0" x 0"
				</div>
			</div>
	   </div>
	</section>

	<section class="fixed-bottom-panel hidden" id="page_size_editor">
		<form class="margin-forms">
			<div class="page_margins hidden" style="display: flex; flex-wrap: wrap;">
				<h3>Margins:</h3>
				<label class="margin-label full-width">
					<b>Top:</b>
					<input autocomplete="off" type="number" name="top" value="0" />
					<span>inch</span>
				</label>
				<label class="margin-label">
					<b>Left:</b>
					<input autocomplete="off" type="number" name="left" value="0" />
					<span>inch</span>
				</label>
				<label class="margin-label">
					<b>Right:</b>
					<input autocomplete="off" type="number" name="right" value="0" />
					<span>inch</span>
				</label>
				<label class="margin-label full-width">
					<b>Bottom:</b>
					<input autocomplete="off" type="number" name="bottom" value="0" />
					<span>inch</span>
				</label>
			</div>
			<div class="page_size hidden" style="display: flex; flex-wrap: wrap;">
				<h3>Page size:</h3>
				<select class="select" id="papper_size">
					<option value="custom_size">Custom size</option>
					<option value="33.11in, 46.81in">A0 (33.11 x 46.81)</option>
					<option value="23.39in, 33.11in">A1 (23.39 x 33.11)</option>
					<option value="16.5in, 23.4in">A1 (16.5 x 23.4)</option>
					<option value="11.69in, 16.54in">A3 (11.69 x 16.54)</option>
					<option selected='selected' value="8.27in, 11.69in">A4 (8.27 x 11.69)</option>
					<option value="5.83in, 8.27in">A5 (5.83 x 8.27)</option>
					<option disabled></option>
					<option value="8.5in, 11in">Letter (8.5 x 11)</option>
					<option value="8.4in, 14in">Legal (8.5 x 14)</option>
					<option value="11in, 17in">Ledger (11 x 17)</option>
					<option value="17in, 11in">Tabloid (17 x 11)</option>
					<option value="7.25in, 10.55in">Executive (7.25 x 10.55)</option>
				</select>
			</div>
			<div class="custom_paper_size hidden">
				<div class="row">
					<label class="light">Width</label>
					<input autocomplete="off" step="0.01" placeholder="8.27" type="number" name="pageSizeWidth" class="custom_paper_width" value="0.0">
					<label class="light">Height</label>
					<input autocomplete="off" step="0.01" placeholder="11.69" type="number" name="pageSizeHeight" class="custom_paper_height" value="0.0">
				</div>

				
			</div>
			
			<div class="resize-btn-block">
				<a href="#" id="start_task" class="button-green">Resize PDF</a>
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


		@include ('inc.result_block')




@endsection
