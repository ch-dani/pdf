@extends('layouts.layout')

@section('content')

    <div class="upload-top-info before_upload">
    	<div class="container">
    		<div class="app-title">
    			<div class="wrapper">
    				<h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Compress PDF' !!}</h1>
    				<p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Reduce the size of your PDF' !!}</p>
    			</div>
    		</div>

			<div class="welcome_outer">
				@if($ads && $device_is=='computer')
					@include("ads.adx250x250")
				@endif

				<div class="app-welcome before_upload" id="c_upload_section">
					<form action="#" id="drop_zone" class="drop_zone_2 compress_form">
						{{ csrf_field() }}

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
    		
    	</div>

		@if($ads && $device_is=='computer')
			@include("ads.adx970x90")
		@endif

		@if($ads && $device_is=='phone')
			@include("ads.adx320x100")
		@endif  

    	
    </div>
    
	@if (true)
    
    <section id="compress_section" class="hidden after_upload">
    	<div class="options-container container">
	    	<div class="filename">{!! t('Selected:') !!} <span class='current_filename'>pdf.pdf</span></div>
    		<div class="compress_options hidden">
				<div class="opt-row">
					<label class='title_label'>{!! t('Image quality:') !!}</label>
					<div class="btn-group">
						@if (false)
							<label class="btn btn-default btn-xs">
								<input type="radio" name="image_quality" checked value="low">
								{!! t('Low') !!}
								<span class="input-help">
									<i class="far fa-question-circle" title="Choose this for smallest size. JPEG quality: 30%."></i>
								</span>
							</label>
						@endif
						<label class="btn btn-default btn-xs active">
							<input type="radio" name="image_quality" checked value="medium">
							{!! t('Medium') !!}
							<span class="input-help">
								<i class="far fa-question-circle" title="Choose this for smallest size. JPEG quality: 65%."></i>
							</span>
						</label>
						<label class="btn btn-default btn-xs">
							<input type="radio" name="image_quality" value="good">
							{!! t('Good') !!}
							<span class="input-help">
								<i class="far fa-question-circle" title="A balance between image quality and file size. JPEG quality: 80%."></i>							
							</span>
						</label>
						<label class="btn btn-default btn-xs">
							<input type="radio" name="image_quality" value="best"> 
							{!! t('Best') !!}
							<span class="input-help">
								<i class="far fa-question-circle" title="The best image quality but larger file size. JPEG quality: 100%."></i>
							</span>
						</label>
					</div>
				</div>

				<div class="opt-row">
					<label class='title_label'>{!! t('Image resolution') !!} (ppi):</label>
					<div class="btn-group">

						<label class="btn btn-default btn-xs ">
							<input type="radio" name="image_resolution"  value="36">
							36
							<span class="input-help">
								<i class="far fa-question-circle" title="Choose this for smallest size."></i>
							</span>
						</label>

						<label class="btn btn-default btn-xs active">
							<input type="radio" name="image_resolution" checked value="72">
							72
							<span class="input-help">
								<i class="far fa-question-circle" title="Choose this for smallest size."></i>
							</span>
						</label>
						<label class="btn btn-default btn-xs ">
							<input type="radio" name="image_resolution"  value="144">
							144
							<span class="input-help">
								<i class="far fa-question-circle" title="A balance between resolution and file size."></i>							
							</span>
						</label>
						<label class="btn btn-default btn-xs">
							<input type="radio" name="image_resolution" value="288">
							288
							<span class="input-help">
								<i class="far fa-question-circle" title="A very good resolution but larger file size."></i>
							</span>
						</label>

						<label class="btn btn-default btn-xs">
							<input type="radio" name="image_resolution" value="720">
							720
							<span class="input-help">
								<i class="far fa-question-circle" title="Best resolution but the largest file size."></i>
							</span>
						</label>
						
					</div>
				</div>

				<div class="opt-row">
					<label class='title_label'>{!! t('Image conversion:') !!}</label>
					<div class="btn-group">
						<label class="btn btn-default btn-xs active">
							<input type="radio" checked name="image_conversion" value="none">
							None
						</label>
						<label class="btn btn-default btn-xs">
							<input type="radio" name="image_conversion" value="grayscale">
							Grayscale
							<span class="input-help">
								<i class="far fa-question-circle" title="Choose this for smaller file size. Color images are converted to grayscale (black and white only)"></i>							
							</span>
						</label>
					</div>
				</div>

				<div class="opt-row">
					<label class='title_label'>{!! t('Compression speed:') !!}</label>
					<div class="btn-group">
						<label class="btn btn-default btn-xs active">
							<input type="radio" checked name="compression_speed" value="normal">
							{!! t('Regular') !!}
							<span class="input-help">
								<i class="far fa-question-circle" title="Choose this for smallest size."></i>
							</span>
						</label>
						<label class="btn btn-default btn-xs">
							<input type="radio" name="compression_speed" value="fast">
							{!! t('Faster') !!}
							<span class="input-help">
								<i class="far fa-question-circle" title="Compress faster by optimizing only larger images."></i>							
							</span>
						</label>
					</div>
				</div>
			</div>	
    		<div class="button-row">
    			<button id="compress_button">{!! t('Compress') !!} PDF</button>
    			<button id="more_compress_options">{!! t('More options') !!}</button>
    		</div>
    		
    	</div>
    </section>
    @endif

    <input type="hidden" value="<?php echo csrf_token() ?>" id="editor_csrf">

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
    				<h1>{!! array_key_exists(4, $PageBlocks) ? $PageBlocks[4] : 'Ready to compress your files?' !!}</h1>
    				<p>{!! array_key_exists(5, $PageBlocks) ? $PageBlocks[5] : 'Reduce the size of your PDF' !!}</p>
    			</div>
    		</div>
    		<div class="app-welcome">
    			<form action="#">
    				<div class="upload-img">
    					<img src="/img/pdf-img.svg" alt="">
    				</div>
    				{!! t('<h3>UPLOAD <strong>PDF</strong> FILE</h3>') !!}

	                <div class="upload-button" onclick='$("#drop_zone input[type=file]").click(); window.scrollTo(0,0); return false;'>
						<span>
							{{ t("Upload PDF file") }}
						</span>
	                    <input type="file">
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
