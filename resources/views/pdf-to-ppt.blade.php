@extends('layouts.layout2')

@php
    $accept = "application/pdf";
@endphp

@section('content-freeconvert')
    <main class="file_not_loaded">
        @include('page_parts.toolheader')

        <section class="section_top converting tool_section ppt_section after_upload hidden">
            <div class="container">
                <div class="title-wrapper">
                    <h2 class="h30-title title_main">{{ t("Convert PDF Document to PPT (PDF to PPT)") }}</h2>
                    <h3 class="sub-title">{{ t("Creates a PPT document from PDF Documents (.pdf)") }}</h3>
                </div>
                <div id="pages-pdf" class="convert_docs_wrapper">
                    <?php /*@include('page_parts.file_uploader_pdf_2')*/ ?>
                        <div class="convert_doc left_doc">
                            <div class="convert_doc_content">
                                <form action="#" enctype="multipart/form-data" method="post">
                                    <input accept="{{ isset($accept) ? $accept : '' }}" title="Upload" multiple="multiple"
                                           data-scope="task-file" name="file" type="file" class="fileupload user_pdf">
                                </form>
                                <h4 class="title_convert_doc">{{ t("Choose file") }}</h4>
                                <div class="icon_add_doc">
                                    <img src="{{ asset('freeconvert/img/icon-add-file.png') }}" alt="">
                                    <div class="icon_add_select" id="docSelectBtn2">
                                        @php include(public_path('freeconvert/img/icon-add-file-arr.svg')) @endphp
                                    </div>
                                </div>
                                <h5 class="sub_title_convert_doc">{{ t("or drop files here") }}</h5>
                            </div>
                            <div class="select_wrapper" id="docSelect2">
                                <a href="#" class="select_item drpbox-chooser">
                                    @php include(public_path('freeconvert/img/logos_dropbox.svg')) @endphp
                                    {{ t("Dropbox") }}
                                </a>

                                <a href="#" class="select_item gdrive-chooser">
                                    <img src="{{ asset('freeconvert/img/logos_google-drive.png') }}" alt="">
                                    {{ t("Google Drive") }}
                                </a>

                                <a href="#" class="select_item weburl-chooser">
                                    @php include(public_path('freeconvert/img/logos_dropbox.svg')) @endphp
                                    {{ t("Web Address (URL)") }}
                                </a>
                            </div>
                        </div>

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
                        {{--<a href="#" class="link_convert_item" id="showRemoveIcons">
                            @php include(public_path('freeconvert/img/link_conver-3.svg')) @endphp
                            {{ t('Remove') }}
                        </a>--}}
                    </div>
                </div>
                */ ?>
            </div>
        </section>

        <section class="module__how-convert module bg-white">
            <div class="container">
                <div class="title-wrapper">
                    <h2 class="h2-title title_main">{{ t("How to convert PDF to PPT?") }}</h2>
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
    <script src="{{ asset('/js/pdf2ppt.js') }}"></script>
@endsection

