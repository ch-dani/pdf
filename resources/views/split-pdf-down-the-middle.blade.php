@extends('layouts.layout2')

@section('content-freeconvert')
    <main class="file_not_loaded">
        <section class="section_top">
            <div class="container">
                <div class="title-wrapper">
                    <h2 class="h30-title title_main">{{ t("Online PDF Splitter") }}</h2>
                    <h3 class="sub-title">{{ t("Split individual or multiple pages from a PDF into separate files in the blink of an eye.") }}</h3>
                </div>

                <div class="downloader">
                    <div class="downloader__img">
                        <img src="{{ asset('freeconvert/img/convert_1.png') }}" width="250" height="250">
                    </div>
                    <div class="downloader__upload-wrapper">
                        <form>
                            <input type="hidden" name="_token" value="<?php echo csrf_token() ?>" id="editor_csrf">
                            <div class="downloader__doshed">
                                <input type="file" class="upload-file-tool" accept="{{ isset($accept) ? $accept : '' }}">
                                <div class="downloader__upload">
                                    <input type="hidden" name="path" value="pdf-to-ppt">
                                    {{--                                    <input type="hidden" name="operation_id" value="">--}}
                                    <div class="downloader__icon"><img src="{{ asset('freeconvert/img/doc.svg') }}"></div>
                                    <div class="downloader__text">{{ t("Upload PDF file") }}</div>
                                    <div class="downloader__arrow" id="docSelectBtn"><img src="{{ asset('freeconvert/img/arrow-white-down.svg') }}"></div>
                                </div>
                                <div class="downloader__sub-text">{{ t("or Drop files here") }}</div>
                            </div>
                            <div class="select_wrapper" id="docSelect">
                                <a id="drpbox-chooser" href='#' class="select_item">
                                    @php include(public_path('freeconvert/img/logos_dropbox.svg')) @endphp
                                    {{ t("Dropbox") }}
                                </a>

                                <a href='#' class="select_item">
                                    <img src="{{ asset('freeconvert/img/logos_google-drive.png') }}" alt="">
                                    {{ t("Google Drive") }}
                                </a>

                                <a href='#' class="select_item">
                                    <img src="{{ asset('freeconvert/img/logo-link.png') }}" alt="">
                                    {{ t("Web Address (URL)") }}
                                </a>
                            </div>
                        </form>
                    </div>
                    <div class="downloader__img">
                        <img src="{{ asset('freeconvert/img/convert_2.png') }}" width="250" height="250">
                    </div>
                </div>
            </div>
        </section>

        <section class="section_top converting">
            <div class="container">
                <div class="title-wrapper">
                    <h2 class="h30-title title_main">{{ t("Online PDF Splitter") }}</h2>
                    <h3 class="sub-title">{{ t("Split individual or multiple pages from a PDF into separate files in the blink of an eye.") }}</h3>
                </div>
                <div id="pages-pdf" class="convert_docs_wrapper">
                    <div class="convert_doc left_doc">
                        <div class="convert_doc_content">
                            <h4 class="title_convert_doc">{{ t("Choose file") }}</h4>
                            <div class="icon_add_doc">
                                <img src="{{ asset('freeconvert/img/icon-add-file.png') }}" alt="">
                                <div class="icon_add_select" id="docSelectBtn">
                                    @php include(public_path('freeconvert/img/icon-add-file-arr.svg')) @endphp
                                </div>
                            </div>
                            <h5 class="sub_title_convert_doc">{{ t("or drop files here") }}</h5>
                        </div>
                        <div class="select_wrapper" id="docSelect">
                            <a id="drpbox-chooser" href="#" class="select_item">
                                @php include(public_path('freeconvert/img/logos_dropbox.svg')) @endphp
                                {{ t("Dropbox") }}
                            </a>

                            <a href="#" class="select_item">
                                <img src="{{ asset('freeconvert/img/logos_google-drive.png') }}" alt="">
                                {{ t("Google Drive") }}
                            </a>

                            <a href="#" class="select_item">
                                @php include(public_path('freeconvert/img/logos_dropbox.svg')) @endphp
                                {{ t("Web Address (URL)") }}
                            </a>
                        </div>
                    </div>
                    <?php /*
                    <div class="convert_doc right_doc">
                        <div class="convert_doc_content">
                            <div class="download_convert_doc">
                                <img src="{{ asset('freeconvert/img/convert-document.png') }}" alt="">
                            </div>
                        </div>
                        <div class="download_icon_doc"><a href="#"><img src="{{ asset('freeconvert/img/download_arrow.svg') }}"></a></div>

                        <div class="name_doc">
                            <h6>Document 1.PPT</h6>
                        </div>
                    </div>
                    */ ?>
                </div>
                <div class="downloader">
                    <div class="downloader__upload">
                        <div class="downloader__icon"><img src="{{ asset('freeconvert/img/download_arrow.svg') }}"></div>
                        <div class="downloader__text save-images-array">{{ t("Download PPT") }}</div>
                        <div class="downloader__arrow"></div>
                    </div>
                </div>
                <div class="link_convert">
                    <div class="link_convert_left">
                        <a href="#" class="link_convert_item">
                            @php include(public_path('freeconvert/img/link_conver-1.svg')) @endphp
                            {{ t("Merge PDF") }}
                        </a>
                        <a href="#" class="link_convert_item">
                            <img src="{{ asset('freeconvert/img/link_conver-2.png') }}" alt="">
                            {{ t("Compress") }}
                        </a>
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
            </div>
        </section>

        <section class="module__how-convert module bg-white">
            <div class="container">
                <div class="title-wrapper">
                    <h2 class="h2-title title_main">How to convert PDF to PPT?</h2>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="convert">
                            <div class="convert__step">1</div>
                            <h4 class="convert__title">Upload your files</h4>
                            <p class="convert__p">To upload your files from your computer, click “Upload PDF File” and select the files you want to edit or drag and drop the files to the page.</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="convert ">
                            <div class="convert__step">2</div>
                            <h4 class="convert__title">Convert PDF to PPT</h4>
                            <div class="convert-bg"></div>
                            <p class="convert__p">Convert your PDF documents into PPT files by simply clicking “Convert to PPT” and wait for it to be processed.</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="convert">
                            <div class="convert__step">3</div>
                            <h4 class="convert__title">Download Your PPT Document</h4>
                            <p class="convert__p">Download your file to save it on your computer. You may also save it in your online accounts such as Dropbox or Google Drive, share it via email, print the new document, rename or even continue editing with a new task.</p>
                        </div>
                    </div>

                    @if(!Auth::id())
                        <div class="contact-us">
                            <a class="contact-us__button sign-up-trigger" href="{{route("login")}}">{{ t("Sign Up") }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </section>


        @include('inc-freeconvert.banner')
    </main>
@endsection




@section('content')
    <div class="upload-top-info">
        <div class="container">
            <div class="app-title">
                <div class="wrapper">
                    <h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Split PDF pages down the middle' !!}</h1>
                    <p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Split two page layout scans, A3 to double A4 or A4 to double A5' !!}</p>
                </div>
            </div>


			<div class="welcome_outer">
				@if($ads && $device_is=='computer')
					@include("ads.adx250x250")
				@endif

		        <div class="app-welcome">
		            <form action="#" id="drop_zone">
		                <div class="upload-img">
		                    <img src="{{ asset('img/pdf-img.svg') }}" alt="">
		                </div>
			                {!! t("<h3>UPLOAD <strong>PDF</strong> FILE</h3>") !!}
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

			@if($ads && $device_is=='computer')
				@include("ads.adx970x90")
			@endif

			@if($ads && $device_is=='phone')
				@include("ads.adx320x100")
			@endif


        </div>
    </div>

    <div id="pages-pdf" class="split">
        <div class="split-radio-wrapper">
            <label class="split-radio-container">
                <input type="radio" value="vertically" name="split">
                <span class="split-radio-item">{{ t("Split vertically") }}</span>
            </label>
            <label class="split-radio-container">
                <input type="radio" value="horizontally" name="split" checked>
                <span class="split-radio-item">{{ t("Split horizontally") }}</span>
            </label>
        </div>


        <div class="split-radio-descr">{!! array_key_exists(7, $PageBlocks) ? $PageBlocks[7]:"Drag middle split line to customize where the split should occur" !!}</div>
        <div id="resizable">
            <div class="inner"></div>
        </div>
        <div class="wr">

        </div>
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

    <div class="upload-top-info">
        <div class="container">
            <div class="app-title">
                <div class="wrapper">
                    <h1>{!! array_key_exists(4, $PageBlocks) ? $PageBlocks[4] : 'Ready to split your scanned book? Let\'s go!' !!}</h1>
                    <p>{!! array_key_exists(5, $PageBlocks) ? $PageBlocks[5] : 'Split two page layout scans, A3 to double A4 or A4 to double A5' !!}</p>
                </div>
            </div>
            <div class="app-welcome">
                <form action="#">
                    <div class="upload-img">
                        <img src="{{ asset('img/pdf-img.svg') }}" alt="">
                    </div>
                    <h3>{!! t("UPLOAD <strong>PDF</strong> FILE") !!}
                    </h3>
                    @include('includes.upload-button')
                </form>
                <div class="upload-welcom-descr">
                    {!! array_key_exists(6, $PageBlocks) ? $PageBlocks[6] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.' !!}
                </div>
            </div>
        </div>
    </div>

    <section class="fixed-bottom-panel" style="z-index: 91;">
        <form class="fixed-task-form">
            <div class="more-options-box">
                <div class="head-space">
                    <div class="input-group split-input-group">
                        <span class="btns-resolution-span">{{ t("Exclude pages") }}:</span>
                        <input name="pattern" id="pattern" type="text" placeholder="Example: 1-4,8-10,13,15-" value="">
                        <div class="input-group-addon">
                            <a target="_blank" href="#">
                                <i class="far fa-question-circle"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="more-options-box">
                <div class="head-space">
                    <div class="input-group">
                        <label class="split-radio-label">
                            <input id="booklet" value="true" type="checkbox">
                            {!! array_key_exists(8, $PageBlocks) ? $PageBlocks[8] : "Re-paginate from booklet scan" !!}
                        </label>
                        <span class="input-help tooltip">
                            <span class="tooltiptext">
                             {!! array_key_exists(9, $PageBlocks) ? $PageBlocks[9] : "Select this option if your PDF document comes from a booklet scan (Eg: first scan pages 1 and 8, second scan pages 2 and 7, third scan pages 3 and 6, etc.) and you would like the result re-ordered by page numbers (1,2,3,4 etc)" !!}
                            </span>
                            <i class="far fa-question-circle"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="more-options-box">
                <div class="head-space">
                    <div class="input-group">
                        <label class="split-radio-label" for="arabic">
                            <input id="arabic" value="true" type="checkbox">
                            {!! array_key_exists(10, $PageBlocks) ? $PageBlocks[10] : "Right to left document (arabic, hebrew)" !!}
                        </label>
                        <span class="input-help tooltip">
                            <span class="tooltiptext">{!! array_key_exists(11, $PageBlocks) ? $PageBlocks[11] : "Select this option if the right page comes before left page, like arabic or hebrew"  !!}</span>
                            <i class="far fa-question-circle"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="more-options-btns-wrap">
                <button class="options-btn save-pdf" id="split-in-half" type="button">{{ t("Split") }}</button>
                <a href="#" class="options-btn-transparent">{{ t("More options") }}</a>
            </div>
        </form>
    </section>

    @include ('inc.result_block_new')
@endsection
