@extends('layouts.layout2')

@section('content')
    <div class="upload-top-info">
        <div class="container">
            <div class="app-title">
                <div class="wrapper">
                    <h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Unlock PDF Online' !!}</h1>
                    <p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Remove restrictions and password from PDF files' !!}</p>
                </div>
            </div>




			<div class="welcome_outer">
				@if($ads && $device_is=='computer')
					@include("ads.adx250x250")
				@endif            
		        <div class="app-welcome">
		            <form action="#" id="drop_zone">
		                <div class="upload-img">
		                    <img src="/img/pdf-img.svg" alt="">
		                </div>
		                <h3>{!! t("UPLOAD <strong>PDF</strong> FILE") !!}
		                </h3>
		                @include('includes.upload-button')
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

    <div id="extract-pdf">
        <input type="button" value="{!! t("Unlock PDF") !!}" class="button-task" id="unlock-pdf">
    </div>

    @if (count($PageGuides))
        <section class="how-it-works">
            @foreach ($PageGuides as $Guide)
                @if (!is_null($Guide->title))
                    <div class="title-section">
                        <h2>{{ $Guide->title }}</h2>
                    </div>
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

    <section class="how-it-works">

        <div class="contact-btn">
            <a class="button-green contact-btn-popup" href="#contactFormModal">{!! t("Contact Support") !!}</a>
        </div>

    </section>

    @include ('inc.result_block_new')
@endsection
