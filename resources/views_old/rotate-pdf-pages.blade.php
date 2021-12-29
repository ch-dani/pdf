@extends('layouts.layout')

@section('content')
    <div class="upload-top-info r_upload_section before_upload">
        <div class="container">
            <div class="app-title">
                <div class="wrapper">
                    <h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Rotate PDF' !!}</h1>
                    <p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Rotate and save PDF pages permanently' !!}</p>
                </div>
            </div>
            <div class="app-welcome">
                <form action="#" id="rotate_form" class="drop_zone_2">
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
    
    <section id="zoom_section" class="hidden after_upload">
    	<div class="zoom_block">
			<i style="font-size: 16px" class="fa fa-search-minus decrease_zoom"></i>
			<input id="change_zoom" type="range" value="100" min="70" max="150" >
			<i style="font-size: 16px" class="fa fa-search-plus increase_zoom"></i>
    	</div>
    </section>
    
    <section id="rotate_section" class="hidden after_upload">
    	<div class="container centered">
			<div class="tools_menu">
				<div class='block_title'>
					Rotate all pages
				</div>
				<div class="buttons_block_row">
					<div class="buttons_block_1" data-type='pages_rotate'>
						<button data-val='-90'><i class="fa fa-undo"></i> <i class="fas fa-rotate-270 fa-font"></i></button>
						<button data-val='0' class='active'> 0° <i class="fas fa-font"></i></button>
						<button data-val='90'> <i class="fa fa-redo"></i> <i class="fas fa-font fa-rotate-90"></i></button>
						<button data-val='180'> <i class="fa fa-sync"></i> <i class="fas fa-font fa-rotate-180"></i></button>
					</div>
					<div class="buttons_block_1" data-type='pages_selector'>
						<button data-val='all' class='active'>All pages</button>
						<button data-val='odd'>Odd</button>
						<button data-val='even'>Even</button>
					</div>
				</div>
				
			</div>		
			<div class='block_title' style="margin-bottom: 30px;">
				Rotate specific pages
			</div>
			<div class="pages_list">
				<ul id="pages_here">
					
				</ul>
			</div>
		
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

    <div class="upload-top-info">
        <div class="container">
            <div class="app-title">
                <div class="wrapper">
                    <h1>{!! array_key_exists(4, $PageBlocks) ? $PageBlocks[4] : 'Ready to rotate your files? Let\'s go!' !!}</h1>
                    <p>{!! array_key_exists(5, $PageBlocks) ? $PageBlocks[5] : 'Rotate and save PDF pages permanently' !!}</p>
                </div>
            </div>
            <div class="app-welcome">
                <form action="#">
                    <div class="upload-img">
                        <img src="img/pdf-img.svg" alt="">
                    </div>
                    <h3>UPLOAD <strong>PDF</strong> FILE</h3>
                    @include('includes.upload-button')
                    <span class="upload-bottom-text">or start with a <a href="#" class="new-pdf">blank document</a></span>
                </form>
                <div class="upload-welcom-descr">
                    {!! array_key_exists(6, $PageBlocks) ? $PageBlocks[6] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.' !!}
                </div>
            </div>
        </div>
    </div>


	<div class="footer-editor after_upload">
		<div class="container"  style="justify-content: center;">
		    <div class="footer-editor-item" style="width: auto;">
		        <a class="apply-btn save_rotated_pdf" href="#">
		            <img src="/img/icon-save.svg" alt="">
		            <span class="apply_changes_1">Apply Changes</span>
		        </a>
		    </div>
		</div>
	</div>
	
	 @include ('inc.result_block')
    
    
@endsection
