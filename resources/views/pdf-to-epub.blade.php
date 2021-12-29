@extends('layouts.layout2')

@php
    $accept = "application/pdf";
@endphp

@section('content-freeconvert')
    <main class="file_not_loaded">
        @include('page_parts.toolheader')

        <section class="section_top converting tool_section epub_section after_upload hidden">
            <div class="container">
                <div class="title-wrapper">
                    <h2 class="h30-title title_main">{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Convert PDF Document to EPUB (PDF to EPUB)' !!}</h2>
                    <h3 class="sub-title">{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Creates a EPUB document from PDF Documents (.pdf)' !!}</h3>
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
                            <h6>Document 1.EPUB</h6>
                        </div>
                    </div>
                    */ ?>

                    <?php /*<div id="pages_previews_here_2"></div>*/ ?>
                </div>

                @include('page_parts.download_buttons2')

                <?php /*
                <div class="downloader">
                    <div id="start_task" class="downloader__upload">
                        <div class="downloader__icon"><img src="{{ asset('freeconvert/img/download_arrow.svg') }}"></div>
                        <div class="downloader__text save-images-array">{{ t("Process PDF") }}</div>
                        <div class="downloader__arrow"></div>
                    </div>
                </div>
                <div class="link_convert">
                    <div class="link_convert_left">
                       {{-- <a href="#" class="link_convert_item">
                            @php include(public_path('freeconvert/img/link_conver-1.svg')) @endphp
                            {{ t("Merge PDF") }}
                        </a>
                        <a href="#" class="link_convert_item">
                            <img src="{{ asset('freeconvert/img/link_conver-2.png') }}" alt="">
                            {{ t("Compress") }}
                        </a>--}}
                        {{--<a href="#" class="link_convert_item">
                            @php include(public_path('freeconvert/img/link_conver-3.svg')) @endphp
                            {{ t("Remove") }}
                        </a>--}}
                    </div>
                    {{--<div class="link_convert_right">
                        <a id="save-gdrive" href="#" class="link_convert_item">
                            <img src="{{ asset('freeconvert/img/logos_google-drive.png') }}" alt="">
                            {{ t("Save to Google Drive") }}
                        </a>
                        <a id="save-dropbox" href="#" class="link_convert_item">
                            @php include(public_path('freeconvert/img/logos_dropbox.svg')) @endphp
                            {{ t("Save to Dropbox") }}
                        </a>
                    </div>--}}
                </div>
                */ ?>
            </div>
        </section>


        <section class="module__how-convert module bg-white">
            <div class="container">
                <div class="title-wrapper">
                    <h2 class="h2-title title_main">{{ t('How to convert PDF to EPUB?') }}</h2>
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

@endsection
