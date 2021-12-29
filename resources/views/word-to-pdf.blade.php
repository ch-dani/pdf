@extends('layouts.layout')

@php
    $accept = 'application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    //$accept = '';
@endphp

@section('content-freeconvert')
    <main class="file_not_loaded">
        @include('page_parts.toolheader')

        <section class="section_top converting tool_section">
            <div class="container">
                <div class="title-wrapper">
                    <h2 class="h30-title title_main">
                        {!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : t('Convert Word to PDF Documents (DOC to PDF)') !!}
                    </h2>
                    <h3 class="sub-title">
                        {!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : t('Creates a PDF document from Microsoft Word file (.docx)') !!}
                    </h3>
                </div>
                <div class="convert_docs_wrapper" id="pages-pdf">
                    <div class="convert_doc left_doc">
                        <div class="convert_doc_content">
                            <form action="#" enctype="multipart/form-data" method="POST">
                                <input type="file"
                                       accept="application/msword"
                                       title="Upload" multiple="multiple" data-scope="task-file" name="file"
                                       class="fileupload upload-file-tool">
                            </form>
                            <h4 class="title_convert_doc">{{ t('Choose file') }}</h4>
                            <div class="icon_add_doc">
                                <img src="{{ asset('freeconvert/img/icon-add-file.svg') }}" alt="">
                                <div class="icon_add_select" id="docSelectBtn">
                                    @php include(public_path('freeconvert/img/icon-add-file-arr.svg')) @endphp
                                </div>
                            </div>
                            <h5 class="sub_title_convert_doc">{{ t('or drop files here') }}</h5>
                        </div>
                    </div>
                </div>
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
                    <div class="link_convert_left">
                        {{--<a href="#" class="link_convert_item">
                            @php include(public_path('freeconvert/img/link_conver-1.svg')) @endphp
                            {{ t('Merge PDF') }}
                        </a>
                        <a href="#" class="link_convert_item">
                            <img src="{{ asset('freeconvert/img/link_conver-2.svg') }}" alt="">
                            {{ t('Compress') }}
                        </a>--}}
                        {{--<a href="#" class="link_convert_item" id="showRemoveIcons">
                            @php include(public_path('freeconvert/img/link_conver-3.svg')) @endphp
                            {{ t('Remove') }}
                        </a>--}}
                    </div>
                    {{-- <div class="link_convert_right">
                         <a href="#" class="link_convert_item">
                             <img src="{{ asset('freeconvert/img/logos_google-drive.svg') }}" alt="">
                             {{ t('Save to Google Drive') }}
                         </a>
                         <a href="#" class="link_convert_item">
                             @php include(public_path('freeconvert/img/logos_dropbox.svg')) @endphp
                             {{ t('Save to Dropbox') }}
                         </a>
                     </div>--}}
                </div>
            </div>
        </section>

        <section class="module__how-convert module bg-white">
            <div class="container">
                <div class="title-wrapper">
                    <h2 class="h2-title title_main">{{ t('How to convert Word to PDF?') }}</h2>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="convert">
                            <div class="convert__step">1</div>
                            <h4 class="convert__title">{{ t('Upload your files') }}</h4>
                            <p class="convert__p">
                                {!! array_key_exists(3, $PageBlocks) ? $PageBlocks[3] : t('To upload your files from your computer, click “Upload DOC File” and select the files you want to edit or drag and drop the files to the page.') !!}
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="convert ">
                            <div class="convert__step">2</div>
                            <h4 class="convert__title">{{ t('Convert Word to PDF') }}</h4>
                            <div class="convert-bg"></div>
                            <p class="convert__p">
                                {!! array_key_exists(4, $PageBlocks) ? $PageBlocks[4] : t('Convert your Word documents into PDF files by simply clicking “Convert to PDF” and wait for it to be processed.') !!}
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="convert">
                            <div class="convert__step">3</div>
                            <h4 class="convert__title">{{ t('Download Your PDF Document') }}</h4>
                            <p class="convert__p">
                                {!! array_key_exists(5, $PageBlocks) ? $PageBlocks[5] : t('Download your file to save it on your computer. You may also save it in your online accounts such as Dropbox or Google Drive, share it via email, print the new document, rename or even continue editing with a new task.') !!}
                            </p>
                        </div>
                    </div>

                    @if(!\Illuminate\Support\Facades\Auth::check())
                        <div class="contact-us">
                            <a class="contact-us__button" href="#">{{ t('Sign Up') }}</a>
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
    {{--<script src="{{ asset('js/tools/word-to-pdf.js') }}"></script>--}}
@endsection

