@extends('layouts.layout')

@php
    $accept = "application/pdf";
@endphp

@section('content-freeconvert')
    <main class="file_not_loaded">
        @include('page_parts.toolheader')

        <section class="tool_section section_top converting">
           
            <div class="container">
                <div class="title-wrapper">
                    <h2 class="h30-title title_main">
                        {!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : t('Convert PDF to Word Documents (PDF to DOC)') !!}
                    </h2>
                    <h3 class="sub-title">
                        {!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : t('Creates a Word document from PDF file (.pdf)') !!}
                    </h3>
                </div>
                <div class="convert_docs_wrapper" id="pages-pdf">
                    @include('page_parts.file_uploader_pdf_2')
                </div>

                @include('page_parts.download_buttons2')

                <?php /*
                <div class="downloader">
                    <div class="downloader__upload">
                        <div class="downloader__icon">
                            <img src="{{ asset('freeconvert/img/download_arrow.svg') }}">
                        </div>
                        <div class="downloader__text save-pdf">{{ t('Process PDF') }}</div>
                        <div class="downloader__arrow"></div>
                    </div>
                </div>
                <div class="link_convert">
                    <div class="link_convert_left link_convert_center">
                        <a href="#" class="link_convert_item" id="showRemoveIcons">
                            @php include(public_path('freeconvert/img/link_conver-3.svg')) @endphp
                            {{ t('Remove') }}
                        </a>
                    </div>
                </div>
                */ ?>
            </div>
        </section>

        <section class="module__how-convert module bg-white">
            <div class="container">
                <div class="title-wrapper">
                    <h2 class="h2-title title_main">
                        
                        {{ t('How to convert PDF to Word?') }}</h2>
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
                            <a class="contact-us__button sign-up-trigger" href="#">{{ t("Sign Up") }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        @php
            $h2_title_post = 'Our Blog';
            $sub_title_post = 'Non bibendum nisi aliquet non amet lobortis';
            $number_posts = 2;
            $bg = 'bg-grey';
        @endphp

        @include('inc-freeconvert.our_blog')
        @include('inc-freeconvert.tools-pd')
        @include('inc-freeconvert.banner')
        @include('inc-freeconvert.accordion')
        @include('inc-freeconvert.testimonial')
    </main>
@endsection

