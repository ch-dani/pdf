@extends('layouts.layout2')

@php
    $accept = 'application/pdf';
@endphp

@section('content-freeconvert')
    <main class="file_not_loaded">
        @include('page_parts.toolheader')

        <section class="section_top converting tool_section">
            <div class="container">
                <div class="title-wrapper">
                    <h2 class="h30-title title_main">{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Delete PDF Pages' !!}</h2>
                    <h3 class="sub-title">{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Remove pages from a PDF document' !!}</h3>
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
                        <div class="downloader__text save-images-array save-pdf">{{ t("Process PDF") }}</div>
                        <div class="downloader__arrow"></div>
                    </div>
                </div>
                {{--
                <div class="link_convert">
                    <ul class="save">
                        <li class="save__li">
                            <a href="#">
                                <img src="{{ asset('freeconvert/img/logos_google-drive.png') }}" width="28" height="23">{{ t("Save to Google Drive") }}
                            </a>
                        </li>
                        <li class="save__li">
                            <a href="#">
                                <img src="{{ asset('freeconvert/img/logo_dropbox.svg') }}" width="28" height="23">{{ t("Save to Dropbox") }}
                            </a>
                        </li>
                    </ul>
                </div>
                --}}
                */ ?>
            </div>
        </section>

        <section class="module__how-convert module bg-white pb_5">
            <div class="container">
                <div class="title-wrapper">
                    <h2 class="h2-title title_main">{{ t("How to Delete PDF Files Online Free") }}</h2>
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

        @include('inc-freeconvert.banner')
    </main>
@endsection
