@extends('layouts.layout')
@section('content-freeconvert')
@php
    $accept = "application/pdf";
@endphp


<style>
	.page{
		position: relative;
	}
	.hint-block {
		position: absolute;
		background-color: rgba(255,255,0,0.1);
		border: 1px orange solid;
	}


	.text_content_element{
		opacity: 0;
	}
	.text_content_element:not(.spe_element){
		pointer-events: none !important;
	}
	.create_new_page, .page-side-bar{
		display: none !important;
	}
</style>
<main class="file_not_loaded">
    @include('page_parts.toolheader')	


    <section class="section_top converting tool_section">
        <div class="container">
            <div class="title-wrapper">
                <h2 class="h30-title title_main">{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : ' ' !!}</h2>
                <h3 class="sub-title">{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : ' ' !!}</h3>
            </div>
            <div id="pages-pdf" class="convert_docs_wrapper">
                @include('page_parts.file_uploader_pdf_2')
            </div>
            <div class="downloader">
                <div class="downloader__upload">
                    <div class="downloader__icon"><img src="{{ asset('freeconvert/img/download_arrow.svg') }}"></div>
                    <div class="downloader__text save-images-array">{{ t("Process PDF") }}</div>
                    <div class="downloader__arrow"></div>
                </div>
            </div>
        </div>
    </section>

    <section class="section_top  after_upload hidden">
        <div class="container">
            <div class="title-wrapper">
                <h2 class="h30-title title_main">{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : ' ' !!}</h2>
                <h3 class="sub-title">{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : ' ' !!}</h3>
            </div>
		    <div id="selectable_div"></div>
		    <div id="simplePDFEditor" current_editor="text">
		        <div id="viewer" class="pdfViewer"></div>
		    </div>

			@include("page_parts.download_buttons2")

        </div>
    </section>
    
    


    <section class="module__how-convert module bg-white">
        <div class="container">
            <div class="title-wrapper">
                <h2 class="h2-title title_main">{{ t('How to convert PDF to Excel?') }}</h2>
            </div>
            <div class="row">
				<div class="col-sm-4">
					<div class="convert">
						<div class="convert__step">1</div>
						<h4 class="convert__title">{{t('Upload your file')}}</h4>
						<p class="convert__p">{{t('To upload your files from your computer, click “Upload PDF File” and select the files you want to edit or drag and drop the files to the page.')}} </p>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="convert">
						<div class="convert__step">2</div>
						<h4 class="convert__title">{{t('Convert PDF to Excel')}}</h4>
						<div class="convert-bg">&nbsp;</div>
						<p class="convert__p">{{t('Convert your PDF document into Excel file by simply clicking “Convert to Excel” and wait for it to be processed.')}}</p>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="convert">
						<div class="convert__step">3</div>
						<h4 class="convert__title">{{t('Download files')}}</h4>
						<p class="convert__p">{{t('Download your file to save it on your computer. You may also save it in your online accounts such as Dropbox or Google Drive, share it via email, print the new document, rename or even continue editing with a new task.')}}</p>
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
    
    
    
    @include('inc-freeconvert.our_blog')
    @include('inc-freeconvert.tools-pd')
    @include('inc-freeconvert.banner')
    @include('inc-freeconvert.accordion')
    @include('inc-freeconvert.testimonial')


</main>

@endsection

