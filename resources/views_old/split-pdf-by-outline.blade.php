@extends('layouts.layout')

@section('content')
    <div class="upload-top-info before_upload">
        <div class="container">
            <div class="app-title">
                <div class="wrapper">
                    <h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Split PDF by bookmarks' !!}</h1>
                    <p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Extract chapters to separate documents based on the bookmarks in the table of contents' !!}</p>
                </div>
            </div>
            <div class="app-welcome">
                <form action="#" id="drop_zone">
                	<input type="hidden" value="<?php echo csrf_token() ?>" id="editor_csrf">                
                    <div class="upload-img">
                        <img src="img/pdf-img.svg" alt="">
                    </div>
                    <h3>UPLOAD <strong>PDF</strong> FILE</h3>
    				@include('inc.uploadButton')
                </form>
                <div class="upload-welcom-descr">
                    {!! array_key_exists(3, $PageBlocks) ? $PageBlocks[3] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.' !!}
                </div>
            </div>
        </div>
    </div>




    <section class="split-pdf-by-bookmarks split_by_outline_section hidden after_upload">
        <div class="container">

            <div class="app-title">
                <div class="wrapper">
                    <h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Split PDF by bookmarks' !!}</h1>
                    <p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Extract chapters to separate documents based on the bookmarks in the table of contents' !!}</p>
                </div>
            </div>


            <div class="pdf-bookmarks-content-wrap">
                <div class="pdf-bookmarks-list">
                    <ul id="outline_list">
                    </ul>
                </div>
                <div class="pdf-bookmarks-options">
                    <form class="fixed-task-form">
                        <div class="pdf-grayscale-form-options">
                            <div class="image-radio-item">
                                <span class="btns-resolution-span">Bookmark level</span>
                                <div class="btns-resolution bookmarks_levels">
                                </div>
                            </div>
                            <div class="text-input-item">
                                <span class="btns-resolution-span"><strong>Contains text</strong> (optional)</span>
                                <div class="btns-resolution">
                                    <label class="resolution-item">
                                        <input class="split-input" type="text" name="bookmarks_contain" autocomplete="off" placeholder="Example: Chapter">
                                    </label>
                                </div>
                            </div>
                            <div class="more-options-box text-center">
                                <div class="more-options-box-span">Missing paragraphs at the end</div>   
                                <div class="checkbox-items-wrap">
                                    <div class="checkbox-item-content">
                                        <label>
                                            <input name="firstInputCoverTitle" value="true" type="checkbox">
                                            <span>Include extra page at the end</span>
                                        </label>
                                        <span class="input-help tooltip">
                                            <span class="tooltiptext">If you find that sometimes the last paragraphs from a chapter are missing. This option will add one extra page at the end of each split document.</span>
                                            <i class="far fa-question-circle"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="head-space">
                                    <label>Customize result names</label>
                                    <div class="input-group">
                                        <input name="filename_pattern" type="text" placeholder="[FILENUMBER]_[BOOKMARK_NAME_STRICT]" value="[FILENUMBER]_[BOOKMARK_NAME_STRICT]">
                                        <div class="input-group-addon">
                                            <a target="_blank" href="#"><i class="far fa-question-circle"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="more-options-btns-wrap">
                            <button class="options-btn" type="button" id="start_task">Split by bookmarks</button>
                            <a href="#" class="options-btn-transparent options-btn-grayscale">More options</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

	@include ('inc.result_block')




    @if (count($PageGuides))
        <section class="how-it-works">
            @foreach ($PageGuides as $Guide)
                @if (!is_null($Guide->title))
                    <div class="title-section"><h2>{{ $Guide->title }}</h2></div>
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

    <section class="how-it-works">
        <div class="contact-btn">
            <a class="button-green contact-btn-popup" href="#contactFormModal">Contact Support</a>
        </div>

    </section>

@endsection
