@extends('layouts.layout2')

@section('content')
    <div class="upload-top-info">
        <div class="container">
            <div class="app-title">
                <div class="wrapper">
                    <h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Split PDF pages down the middle' !!}</h1>
                    <p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Split two page layout scans, A3 to double A4 or A4 to double A5' !!}</p>
                </div>
            </div>
            <div class="app-welcome">
                <form action="#" id="drop_zone">
                    <div class="upload-img">
                        <img src="{{ asset('img/pdf-img.svg') }}" alt="">
                    </div>
                    <h3>UPLOAD
                        <strong>PDF</strong>
                        FILE
                    </h3>
                    @include('includes.upload-button')
                </form>
                <div class="upload-welcom-descr">
                    {!! array_key_exists(3, $PageBlocks) ? $PageBlocks[3] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.' !!}
                </div>
            </div>
        </div>
    </div>

    <div id="pages-pdf" class="split">
        <div class="split-radio-wrapper">
            <label class="split-radio-container">
                <input type="radio" value="vertically" name="split">
                <span class="split-radio-item">Split vertically</span> 
            </label>
            <label class="split-radio-container">
                <input type="radio" value="horizontally" name="split" checked>
                <span class="split-radio-item">Split horizontally</span> 
            </label>
        </div>
        
         
        <div class="split-radio-descr">Drag middle split line to customize where the split should occur</div>
        <div id="resizable">
            <div class="inner"></div>
        </div>
        <div class="wr">

        </div>
    </div>

    @if (count($PageGuides))
        <section class="how-it-works">
            @foreach ($PageGuides as $Guide)
                @if (!is_null($Guide->title))
                    <div class="title-section">
                        <h2>{{ $Guide->title }}</h2>
                    </div>
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

    <div class="upload-top-info">
        <div class="container">
            <div class="app-title">
                <div class="wrapper">
                    <h1>{!! array_key_exists(4, $PageBlocks) ? $PageBlocks[4] : 'Ready to split your scanned book? Let\'s go!' !!}</h1>
                    <p>{!! array_key_exists(5, $PageBlocks) ? $PageBlocks[5] : 'Split two page layout scans, A3 to double A4 or A4 to double A5' !!}</p>
                </div>
            </div>
            <div class="app-welcome">
                <form action="#">
                    <div class="upload-img">
                        <img src="{{ asset('img/pdf-img.svg') }}" alt="">
                    </div>
                    <h3>UPLOAD
                        <strong>PDF</strong>
                        FILE
                    </h3>
                    @include('includes.upload-button')
                </form>
                <div class="upload-welcom-descr">
                    {!! array_key_exists(6, $PageBlocks) ? $PageBlocks[6] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.' !!}
                </div>
            </div>
        </div>
    </div>

    <section class="fixed-bottom-panel" style="z-index: 91;">
        <form class="fixed-task-form">
            <div class="more-options-box">
                <div class="head-space">
                    <div class="input-group split-input-group">
                        <span class="btns-resolution-span">Exclude pages:</span>
                        <input name="pattern" id="pattern" type="text" placeholder="Example: 1-4,8-10,13,15-" value="">
                        <div class="input-group-addon">
                            <a target="_blank" href="#">
                                <i class="far fa-question-circle"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="more-options-box">
                <div class="head-space">
                    <div class="input-group">
                        <label class="split-radio-label">
                            <input id="booklet" value="true" type="checkbox">
                            Re-paginate from booklet scan
                        </label>
                        <span class="input-help tooltip">
                            <span class="tooltiptext">Select this option if your PDF document comes from a booklet scan (Eg: first scan pages 1 and 8, second scan pages 2 and 7, third scan pages 3 and 6, etc.) and you would like the result re-ordered by page numbers (1,2,3,4 etc)</span>
                            <i class="far fa-question-circle"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="more-options-box">
                <div class="head-space">
                    <div class="input-group">
                        <label class="split-radio-label" for="arabic">
                            <input id="arabic" value="true" type="checkbox">
                            Right to left document (arabic, hebrew)
                        </label>
                        <span class="input-help tooltip">
                            <span class="tooltiptext">Select this option if the right page comes before left page, like arabic or hebrew</span>
                            <i class="far fa-question-circle"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="more-options-btns-wrap">
                <button class="options-btn save-pdf" id="split-in-half" type="button">Split</button>
                <a href="#" class="options-btn-transparent">More options</a>
            </div>
        </form>
    </section>

    @include ('inc.result_block')
@endsection
