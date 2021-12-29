@extends('layouts.layout')

@section('content-freeconvert')
<main>
    <section class="crop-section bg-grey">
        <div class="container">
            <div class="title-wrapper">
                <h2 class="h30-title title_main">{{ t('Online PDF Splitter') }}</h2>
                <h3 class="sub-title">
                    {{ t('Split individual or multiple pages from a PDF into separate files in the blink of an eye.') }}
                </h3>
            </div>
            <div class="crop-section__page crop-section__page-small">
                <img src="{{ asset('freeconvert/img/page-small.png') }}" width="172" height="229">
            </div>
            <h3 class="password__doc">{{ t('Document 1.pdf') }}</h3>
            <div class="contact-us">
                <a class="contact-us__button btn-gradient" href="#">
                    <img src="{{ asset('freeconvert/img/download.svg') }}" width="30" height="30">
                    {{ t('Download PDF') }}
                </a>
            </div>
            <ul class="save">
                <li class="save__li">
                    <a href="#">
                        <img src="{{ asset('freeconvert/img/logo_google-drive.svg') }}" width="26" height="23">
                        {{ t('Save to Google Drive') }}
                    </a>
                </li>
                <li class="save__li">
                    <a href="#">
                        <img src="{{ asset('freeconvert/img/logo_dropbox.svg') }}" width="28" height="23">
                        {{ t('Save to Dropbox') }}
                    </a>
                </li>
            </ul>
        </div>
    </section>

    <section class="module__how-banner bg-white">
        <div class="container">
            <div class="banner">
                <img src="{{ asset('freeconvert/img/banner.png') }}" width="970" height="250">
            </div>
        </div>
    </section>
</main>
@endsection
