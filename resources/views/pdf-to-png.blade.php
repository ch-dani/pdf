@extends('layouts.layout2')

@php
    $accept = "application/pdf";
@endphp

@section('content-freeconvert')
    <main class="file_not_loaded">
        @include('page_parts.toolheader')

        <section class="section_top converting tool_section">
            <div class="container">
                <div class="title-wrapper">
                    <h2 class="h30-title title_main">{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Online PDF Merger' !!}</h2>
                    <h3 class="sub-title">{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Combine PDF files online for free in just seconds' !!}</h3>
                </div>
                <div id="pages-pdf" class="convert_docs_wrapper">
                    @include('page_parts.file_uploader_pdf_2')
                    <?php /*
                    <div class="convert_doc right_doc">
                        <div class="convert_doc_content">
                            <div class="download_convert_doc">
                                <img src="{{ asset('freeconvert/img/convert-document.png') }}" alt="">
                            </div>
                        </div>
                        <div class="download_icon_doc"><a href="#"><img src="{{ asset('freeconvert/img/download_arrow.svg') }}"></a></div>

                        <div class="name_doc">
                            <h6>Document 1.jpg</h6>
                        </div>
                    </div>
                    */ ?>
                </div>

                @include('page_parts.download_buttons2')

                <?php /*
                <div class="downloader">
                    <div class="downloader__upload">
                        <div class="downloader__icon"><img src="{{ asset('freeconvert/img/download_arrow.svg') }}"></div>
                        <div class="downloader__text save-images-array">{{ t("Process PDF") }}</div>
                        <div class="downloader__arrow"></div>
                    </div>
                </div>
                {{--
                <div class="link_convert">
                    <div class="link_convert_left">
                        --}}{{--
                        <a href="#" class="link_convert_item">
                            @php include(public_path('freeconvert/img/link_conver-1.svg')) @endphp
                            {{ t("Merge PDF") }}
                        </a>
                        <a href="#" class="link_convert_item">
                            <img src="{{ asset('freeconvert/img/link_conver-2.png') }}" alt="">
                            {{ t("Compress") }}
                        </a>
                        --}}{{--
                        <a href="#" class="link_convert_item">
                            @php include(public_path('freeconvert/img/link_conver-3.svg')) @endphp
                            {{ t("Remove") }}
                        </a>
                    </div>
                    <div class="link_convert_right">
                        <a id="save-gdrive" href="#" class="link_convert_item">
                            <img src="{{ asset('freeconvert/img/logos_google-drive.png') }}" alt="">
                            {{ t("Save to Google Drive") }}
                        </a>
                        <a id="save-dropbox" href="#" class="link_convert_item">
                            @php include(public_path('freeconvert/img/logos_dropbox.svg')) @endphp
                            {{ t("Save to Dropbox") }}
                        </a>
                    </div>
                </div>
                --}}
                */ ?>
            </div>
        </section>

        <section class="module__how-convert module bg-white">
            <div class="container">
                <div class="title-wrapper">
                    <h2 class="h2-title title_main">{{ t('How to convert PDF to PNG?') }}</h2>
                </div>
                <div class="row">
                    @if (count($PageGuides))
                        @foreach ($PageGuides as $Guide)
                            @if (!is_null($Guide->content))
                                {!! htmlspecialchars_decode($Guide->content) !!}
                            @endif
                        @endforeach
                    @endif

                    @if(!Auth::id())
                        <div class="contact-us">
                            <a class="contact-us__button sign-up-trigger" href="{{route("login")}}">{{ t("Sign Up") }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </section>
		<?php
            $h2_title_post  = 'Our Blog';
            $sub_title_post = 'Non bibendum nisi aliquet non amet lobortis';
            $number_posts   = 2;
            $bg = 'bg-grey';
		?>
        @include('inc-freeconvert.our_blog')
        @include('inc-freeconvert.tools-pd')
        @include('inc-freeconvert.banner')
        @include('inc-freeconvert.accordion')
        @include('inc-freeconvert.testimonial')
    </main>
@endsection

@section('js')
    <script src="{{ asset('/js/tools/pdf-to-jpg.js')}}"></script>
@endsection

@section('content')
    <div class="upload-top-info">
        <div class="container">
            <div class="app-title">
                <div class="wrapper">
                    <h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Convert PDF to JPG, PNG or TIFF online' !!}</h1>
                    <p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Get PDF pages converted to JPG, PNG or TIFF images' !!}</p>
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
		                <h3>UPLOAD
		                    <strong>PDF</strong>
		                    FILE
		                </h3>
		                @include('includes.upload-button')
		                {{ csrf_field() }}
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

    <div id="pages-pdf">
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

	@if(false)
    <div class="upload-top-info">
        <div class="container">
            <div class="app-title">
                <div class="wrapper">
                    <h1>{!! array_key_exists(4, $PageBlocks) ? $PageBlocks[4] : 'Ready to convert your PDF pages to images?' !!}</h1>
                    <p>{!! array_key_exists(5, $PageBlocks) ? $PageBlocks[5] : 'Get PDF pages converted to JPG, PNG or TIFF images' !!}</p>
                </div>
            </div>
            <div class="app-welcome">
                <form action="#">
                    <div class="upload-img">
                        <img src="/img/pdf-img.svg" alt="">
                    </div>
                    <h3>UPLOAD
                        <strong>PDF</strong>
                        FILE
                    </h3>
                    @include('includes.upload-button')
                    <span class="upload-bottom-text">or start with a <a href="#"
                                                                        class="new-pdf">blank document</a></span>
                </form>
                <div class="upload-welcom-descr">
                    {!! array_key_exists(6, $PageBlocks) ? $PageBlocks[6] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.' !!}
                </div>
            </div>
        </div>
    </div>
    @endif

    <section class="fixed-bottom-panel">
        <form class="fixed-task-form">
            <div class="image-radio-item">
                <span class="btns-resolution-span">{{ t("Image resolution") }}</span>
                <div class="btns-resolution">
                    <label class="resolution-item">
                        <input type="radio" name="resolution" value="72" id="resolution1">
                        <span class="resolution-item-checkmark">{{ t("Small") }} (72 dpi)</span>
                    </label>
                    <label class="resolution-item">
                        <input type="radio" name="resolution" value="150" id="resolution2" checked="checked">
                        <span class="resolution-item-checkmark">{{ t("Medium") }} (150 dpi)</span>
                    </label>
                    <label class="resolution-item">
                        <input type="radio" name="resolution" value="220" id="resolution3">
                        <span class="resolution-item-checkmark">{{ t("Large") }} (220 dpi)</span>
                    </label>
                </div>
            </div>
            <div class="image-radio-item">
                <span class="btns-resolution-span">{{ t("Image format") }}</span>
                <div class="btns-resolution">
                    <label class="resolution-item">
                        <input type="radio" name="format" value="jpeg" id="format1" checked="checked">
                        <span class="resolution-item-checkmark">JPG</span>
                    </label>
                    <label class="resolution-item">
                        <input type="radio" name="format" value="png16m" id="format2">
                        <span class="resolution-item-checkmark">PNG</span>
                    </label>
                    <label class="resolution-item">
                        <input type="radio" name="format" value="tiff" id="format3">
                        <span class="resolution-item-checkmark">TIFF</span>
                    </label>
                </div>
            </div>
            <div class="more-options-box">
                <div class="head-space">
                    <label>{{ t("Customize result names") }}</label>
                    <div class="input-group">
                        <input name="outputFilenamePattern" type="text" placeholder="[BASENAME]-[CURRENTPAGE]" value="[BASENAME]-[CURRENTPAGE]">
                        <div class="input-group-addon">
                            <a target="_blank" href="#"><i class="far fa-question-circle"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="more-options-btns-wrap">
                <button class="options-btn save-images-array" type="button">{{ t("Convert") }}</button>
                <a href="#" class="options-btn-transparent">{{ t("More options") }}</a>
            </div>
        </form>
    </section>

    @include ('inc.result_block_new')

@endsection
