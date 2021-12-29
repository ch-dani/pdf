@extends('layouts.layout2')

@php
    $accept = 'application/pdf';
@endphp

@section('content-freeconvert')
    <main class="file_not_loaded step1">
        @include('page_parts.toolheader')

        <section class="tool_section section_top converting crop-section">
            <div class="container">
                <div class="title-wrapper">
                    <h2 class="h30-title title_main">{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Online PDF Merger' !!}</h2>
                    <h3 class="sub-title">{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Combine PDF files online for free in just seconds' !!}</h3>
                </div>
                <div id="merge-pdf" class="convert_docs_wrapper">
                    @include('page_parts.file_uploader_pdf_2')
	                <?php /*
                    <div class="convert_doc right_doc">
                        <div class="convert_doc_content">
                            <div class="download_convert_doc">
                                <img src="img/convert-document.png" alt="">
                            </div>
                        </div>
                        <div class="download_icon_doc"><a href="#"><img src="img/download_arrow.svg"></a></div>

                        <div class="name_doc">
                            <h6>Document 1.xls</h6>
                        </div>
                    </div>
                    */ ?>
                </div>
                <div class="downloader convert">
                    <div class="downloader__upload">
                        <div class="downloader__icon"><img src="{{ asset('freeconvert/img/download_arrow.svg') }}"></div>
                        <div class="downloader__text merge-pdf-save">{{ t("Merge files") }}</div>
                        <div class="downloader__arrow"></div>
                    </div>
                </div>
                <div class="downloader download">
                    <div class="contact-us">
                        <a class="contact-us__button btn-gradient download_pdf" href="#" download><img src="{{ asset('freeconvert/img/download.svg') }}" width="30" height="30">{{ t("Download PDF") }}</a>
                    </div>
                </div>
                <div class="link_convert">
                    <ul class="save">
                        <li class="save__li"><a class="split_selected_pages" href="#"><img src="{{ asset('freeconvert/img/split.svg') }}" width="23" height="23">{{ t("Split selected files") }}</a></li>
                        <li class="save__li"><a class="remove_selected_pages" href="#"><img src="{{ asset('freeconvert/img/link_conver-3.svg') }}" width="23" height="23">{{ t("Remove selected pages") }}</a></li>
                    </ul>
                </div>
                <div class="link_download">
                    <ul class="save">
                        <li class="save__li"><a href="#"><img src="{{ asset('freeconvert/img/logos_google-drive.png') }}" width="26" height="23">{{ t("Save to Google Drive") }}</a></li>
                        <li class="save__li"><a href="#"><img src="{{ asset('freeconvert/img/logo_dropbox.svg') }}" width="28" height="23">{{ t("Save to Dropbox") }}</a></li>
                    </ul>
                </div>
            </div>
        </section>

        <section id="encrypt_section" class="crop-section bg-grey after_upload hidden">
            <div class="container">
                <div class="title-wrapper">
                    <h2 class="h30-title title_main">{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Online PDF Merger' !!}</h2>
                    <h3 class="sub-title">{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Combine PDF files online for free in just seconds' !!}</h3>
                </div>

                <div id="pages_previews_here"></div>

                <form class="fixed-task-form" id="encrypt_form">
                    <input type="hidden" name="password_own" value="">
                    <input type="hidden" name="encrypt" value="rc4v2">
                    <input type="hidden" name="pdf_password" value="false">
                    <div class="password">
                        <h3 class="password__doc file_name_here"></h3>
                        <input type="password" placeholder="Set password" class="password__input" name="password_open">
                        <a id="start_task" href="#" class="password__send">Protect PDF</a>
                    </div>
                </form>
            </div>
        </section>

        <section class="module__how-convert module bg-white pb_5">
            <div class="container">
                <div class="title-wrapper">
                    <h2 class="h2-title title_main">{{ t("How to Merge PDF Files Online Free") }}</h2>
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


    
		@include('inc-freeconvert.our_blog')
		@include('inc-freeconvert.tools-pd')
		@include('inc-freeconvert.banner')
		@include('inc-freeconvert.accordion')
		@include('inc-freeconvert.testimonial')


    </main>
    
    
    
    
@endsection

