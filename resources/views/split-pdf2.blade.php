@extends('layouts.layout')

@section('content-freeconvert')
    <main>
        <section class="tool_section section_top converting">
            <div class="container">
                <div class="title-wrapper">
                    <h2 class="h30-title title_main">{{ t('Online PDF Splitter') }}</h2>
                    <h3 class="sub-title">
                        {{ t('Split individual or multiple pages from a PDF into separate files in the blink of an eye.') }}
                    </h3>
                </div>
                <div class="convert_docs_wrapper">
                    <div class="convert_doc left_doc">
                        <div class="convert_doc_content">
                            <h4 class="title_convert_doc">{{ t('Choose file') }}</h4>
                            <div class="icon_add_doc">
                                <img src="{{ asset('freeconvert/img/icon-add-file.svg') }}" alt="">
                                <div class="icon_add_select" id="docSelectBtn">
                                    @php include(public_path('freeconvert/img/icon-add-file-arr.svg')) @endphp
                                </div>
                            </div>
                            <h5 class="sub_title_convert_doc">{{ t('or drop files here') }}</h5>
                        </div>
                        <div class="select_wrapper" id="docSelect">
                            <a href="#" class="select_item">
                                @php include(public_path('freeconvert/img/logos_dropbox.svg')) @endphp
                                Dropbox
                            </a>
                            <a href="#" class="select_item">
                                <img src="{{ asset('freeconvert/img/logos_google-drive.svg') }}" alt="">
                                Google Drive
                            </a>
                            <a href="#" class="select_item">
                                @php include(public_path('freeconvert/img/logo-link.svg')) @endphp
                                {{ t('Web Address (URL)') }}
                            </a>
                        </div>
                    </div>
                    <div class="convert_doc right_doc">
                        <div class="convert_doc_content">
                            <div class="download_convert_doc">
                                <img src="{{ asset('freeconvert/img/convert-document.png') }}" alt="">
                            </div>
                        </div>
                        <div class="download_icon_doc">
                            <a href="#">
                                <img src="{{ asset('freeconvert/img/download_arrow.svg') }}">
                            </a>
                        </div>
                        <div class="name_doc">
                            <h6>{{ t('Document 1.xls') }}</h6>
                        </div>
                    </div>
                </div>
                <div class="downloader">
                    <div class="downloader__upload">
                        <div class="downloader__icon">
                            <img src="{{ asset('freeconvert/img/download_arrow.svg') }}">
                        </div>
                        <div class="downloader__text">{{ t('Download PDF') }}</div>
                        <div class="downloader__arrow"></div>
                    </div>
                </div>
                <div class="link_convert">
                    <ul class="save">
                        <li class="save__li">
                            <a href="#">
                                <img src="{{ asset('freeconvert/img/select-merge.svg') }}" width="23" height="23">
                                {{ t('Select to merge') }}
                            </a>
                        </li>
                        <li class="save__li">
                            <a href="#">
                                <img src="{{ asset('freeconvert/img/link_conver-3.svg') }}" width="23" height="23">
                                {{ t('Select to remove page') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
    </main>
@endsection
