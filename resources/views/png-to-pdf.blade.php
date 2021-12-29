@extends('layouts.layout2')

@php
    $accept = "image/*";
@endphp

@section('content-freeconvert')
    <main class="file_not_loaded">
        @include('page_parts.toolheader')

        <section class="section_top converting tool_section">
            <div class="container">
                <div class="title-wrapper">
                    <h2 class="h30-title title_main">{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Convert JPG to PDF Documents (JPG to PDF)' !!}</h2>
                    <h3 class="sub-title">{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Creates a PDF document from JPG file (.jpg)' !!}</h3>
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
                        <div class="downloader__icon"><img src="{{ asset('freeconvert/img/download_arrow.svg') }}">
                        </div>
                        <div class="downloader__text save-images-array save-pdf">{{ t("Process PDF") }}</div>
                        <div class="downloader__arrow"></div>
                    </div>
                </div>
                {{--
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
                --}}
                */ ?>
            </div>
        </section>


    <section class="module__how-convert module bg-white pb_5">
        <div class="container">
            <div class="title-wrapper">
                <h2 class="h2-title title_main">{{t("How to convert PNG to PDF?")}}</h2>

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
		                <a class="contact-us__button sign-up-trigger" href="{{route("login")}}">{{t("Sign Up")}}</a>
		            </div>
                @endif
            </div>
        </div>
    </section>


        <?php
        $h2_title_post = 'Our Blog';
        $sub_title_post = 'Non bibendum nisi aliquet non amet lobortis';
        $number_posts = 2;
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

@endsection

@section('content')
    <div class="upload-top-info">
        <div class="container">
            <div class="app-title">
                <div class="wrapper">
                    <h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'JPG to PDF Online' !!}</h1>
                    <p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Convert Images to PDF' !!}</p>
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
                        {!! t("<h3>UPLOAD <strong>JPG</strong> FILES</h3>") !!}
                        @php
                            $accept = 'image/jpeg,image/png,image/gif';
                        @endphp
                        @include('includes.upload-button-multiple')
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


            @if($ads && $device_is=='computer')
                @include("ads.adx970x90")
            @endif

            @if($ads && $device_is=='phone')
                @include("ads.adx320x100")
            @endif


        </div>
    </div>

    <section id="pages-pdf" class="s-image-canvas">
        <div class="container">
            <div class="combine-reorder-tools">
                <div class="btn-group upload-btn-group">
					<span class="btn fileinput-button">
						<i class="far fa-file-pdf"></i>{{ t("Add More Files") }}
						<form action="#" enctype="multipart/form-data" method="post">
							<input accept=".jpg,.png" title="Upload" multiple="multiple" data-scope="task-file"
                                   name="file" type="file" class="fileupload upload-file-tool">
						</form>
					</span>
                </div>
            </div>
        </div>
        <ul class="image-canvas-list" id="sortable">
        </ul>
    </section>

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
                    <h1>{!! array_key_exists(4, $PageBlocks) ? $PageBlocks[4] : 'Ready to convert your images? Let\'s go!' !!}</h1>
                    <p>{!! array_key_exists(5, $PageBlocks) ? $PageBlocks[5] : 'Convert Images to PDF' !!}</p>
                </div>
            </div>
            <div class="app-welcome">
                <form action="#">
                    <div class="upload-img">
                        <img src="/img/pdf-img.svg" alt="">
                    </div>
                    {!! t("<h3>UPLOAD <strong>JPG</strong> FILES</h3>") !!}
                    <div class="upload-btn-wrap">
                        <div class="upload-button" onclick='$("#drop_zone input[type=file]").click(); return false;'>
    						<span>
    							{{ t("Upload JPG file") }}
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

    <section class="fixed-bottom-panel">
        <form class="fixed-task-form">
            <div class="select-items-wrap">
                <div class="select-item-dropup">
                    <div class="select-item-dropup-title">{{ t("Page size") }}:</div>
                    <div class="dropup-select-style">
                        <select id="pageFormat">
                            <option value="auto">{{ t("Fit to image") }}</option>
                            <option data-h="8.27" data-w="5.83" value="a5">A5 (5.83 x 8.27)</option>
                            <option selected="selected" data-h="11.69" data-w="8.27" value="a4">A4 (8.27 x 11.69)
                            </option>
                            <option data-h="16.54" data-w="11.69" value="a3">A3 (11.69 x 16.54)</option>
                            <option data-h="33.11" data-w="23.39" value="a2">A2 (23.39 x 33.11)</option>
                            <option data-h="46.81" data-w="33.11" value="a1">A1 (33.11 x 46.81)</option>
                            <option data-h="11" data-w="8.5" value="letter">Letter (8.5 x 11)</option>
                            <option data-h="14" data-w="8.5" value="legal">Legal (8.5 x 14)</option>
                            <option data-h="17" data-w="11" value="legder">Ledger (11 x 17)</option>
                            <option data-h="11" data-w="17" value="11x17">Tabloid (17 x 11)</option>
                            <option data-h="10.55" data-w="7.25" value="executive">Executive (7.25 x 10.55)</option>
                        </select>
                    </div>
                </div>
                <div class="select-item-dropup">
                    <div class="select-item-dropup-title">{{ t("Page orientation") }}:</div>
                    <div class="dropup-select-style">
                        <select id="pageOrientation">
                            <option selected="selected" value="auto">{{ t("Auto") }}</option>
                            <option value="portrait">{{ t("Portrait") }}</option>
                            <option value="landscape">{{ t("Landscape") }}</option>
                        </select>
                    </div>
                </div>
                <div class="select-item-dropup">
                    <div class="select-item-dropup-title">{{ t("Margin") }}:</div>
                    <div class="dropup-select-style">
                        <select id="pageMargin">
                            <option selected="selected" value="0">{{ t("None") }}</option>
                            <option value="0.5">{{ t("Small margin") }}: 0.5"</option>
                            <option value="1">{{ t("Large margin") }}: 1"</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="select-dropup-bottom-btn-wrap">
                <button class="options-btn save-pdf" type="button">{{ t("Convert to PDF") }}</button>
            </div>
        </form>
    </section>

    @include ('inc.result_block_new')
@endsection
