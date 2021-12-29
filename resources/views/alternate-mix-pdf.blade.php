@extends('layouts.layout')

@section('content')
    <div class="upload-top-info before_upload" id="upload_section">
    	<div class="container">
    		<div class="app-title">
    			<div class="wrapper">
    				<h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Alternate & Mix Odd and Even PDF Pages' !!}</h1>
    				<p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Mixes pages from 2 or more documents, alternating between them' !!}</p>
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
						{!! t('<h3>UPLOAD <strong>PDF</strong> FILE</h3>') !!}
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

    <section class="alternate-mix hidden after_upload" id="mix_pdf_section">
        <div class="container">
            <div class="app-title">
                <div class="wrapper">
                    <h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Alternate & Mix Odd and Even PDF Pages' !!}</h1>
                    <p>
                       {!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Mixes pages from 2 or more documents, alternating between them' !!} 
                    </p>
                </div>
            </div>
            <div class="alt-mix-wrap">
            	<form id="files_list_form">
		        	<div id="files_list">

		            </div>
                </form>

				<div class="alternate-right-block">
					<div class="upload-top-info">
					    <div class="app-welcome">
					         <div class="upload-btn-wrap">
						 	    <div class="upload-button">
						 			<span>
						 				{!! t('Add more files') !!}
						 			</span>
						 			<input type="file" class="user_pdf another_file" accept="application/pdf" >
						 		</div>
						 		@if(false)
						 		<button class="dropdown-toggle-btn" type="button">
						 			<i class="fas fa-caret-down"></i>
						 		</button>
						 		<ul class="dropdown-menu-upload" style="display: none;">
						 			<li><a class="drpbox-chooser" href="#" id="drpbox-chooser"><i class="fab fa-dropbox icon"></i> Dropbox</a></li>
						 			<li><a class="gdrive-chooser" href="#"><img class="icon" src="/img/gdrive.png" alt=""> Google Drive</a></li>
						 		</ul>
						 		@endif
						 	</div>
						</div>
					</div>
					<div class="list-mix">
						<a href="#" class="change_sort" data-sort="asc">{!! t('Sort A-Z') !!}</a>
						<a href="#" class="change_sort" data-sort="desc">{!! t('Sort Z-A') !!}</a>
					</div>
				</div>
            </div>
        </div>
    </section>

	<section class="fixed-bottom-panel hidden after_upload" id="start_mix_panel">
        <form class="fixed-task-form" >
            <div class="more-options-btns-wrap">
                <button class="options-btn save_file" type="submit">{!! t('Mix PDF files') !!}</button>
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


    <div class="upload-top-info">
    	<div class="container">
    		<div class="app-title">
    			<div class="wrapper">
    				<h1>{!! array_key_exists(4, $PageBlocks) ? $PageBlocks[4] : 'Ready to alternate your files? Let\'s go!' !!}</h1>
    				<p>{!! array_key_exists(5, $PageBlocks) ? $PageBlocks[5] : 'Mixes pages from 2 or more documents, alternating between them' !!}</p>
    			</div>
    		</div>
    		<div class="app-welcome">
    			<form action="#">
    				<div class="upload-img">
    					<img src="/img/pdf-img.svg" alt="">
    				</div>

					{!! t('<h3>UPLOAD <strong>PDF</strong> FILE</h3>') !!}
                    <div class="upload-btn-wrap">
                        <div class="upload-button" onclick='$("#drop_zone input[type=file]").click(); return false;'>
    						<span>
    							{{ t("Upload PDF file") }}
    						</span>
                            <input type="file">
                        </div>
                        <button class="dropdown-toggle-btn" type="button">
                            <i class="fas fa-caret-down"></i>
                        </button>
                        <ul class="dropdown-menu-upload">
                            <li>
                                <a class="drpbox-chooser" href="#">
                                    <i class="fab fa-dropbox icon"></i>
                                    Dropbox
                                </a>
                            </li>
                            <li>
                                <a class="gdrive-chooser" href="#">
                                    <img class="icon" src="/img/gdrive.png" alt="">
                                    Google Drive
                                </a>
                            </li>
                            <li>
                                <a class="weburl-chooser" href="#">
                                    <i class="fas fa-link icon"></i>
                                    Web Address (URL)
                                </a>
                            </li>
                        </ul>
                    </div>
    				
    				
    			</form>
    			<div class="upload-welcom-descr">
    				{!! array_key_exists(6, $PageBlocks) ? $PageBlocks[6] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.' !!}
    			</div>
    		</div>
    	</div>
    </div>
	@include ('inc.result_block_new')

@endsection
