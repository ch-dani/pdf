@extends('layouts.layout2')

@section('content')
    <div class="upload-top-info">
        <div class="container">
            <div class="app-title">
                <div class="wrapper">
                    <h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Delete PDF Pages' !!}</h1>
                    <p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Remove pages from a PDF document' !!}</p>
                </div>
            </div>
            <div class="app-welcome">
                <form action="#" id="drop_zone">
                    <div class="upload-img">
                        <img src="img/pdf-img.svg" alt="">
                    </div>
                    <h3>UPLOAD <strong>PDF</strong> FILE</h3>
                    @php
                        $accept = 'application/pdf';
                    @endphp
                    @include('includes.upload-button')
                    {{ csrf_field() }}
                </form>
                <div class="upload-welcom-descr">
                    {!! array_key_exists(3, $PageBlocks) ? $PageBlocks[3] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.' !!}
                </div>
            </div>
        </div>
    </div>

    <div id="pages-pdf">
    </div>

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

    <div class="upload-top-info">
        <div class="container">
            <div class="app-title">
                <div class="wrapper">
                    <h1>{!! array_key_exists(4, $PageBlocks) ? $PageBlocks[4] : 'Ready to delete your pages?' !!}</h1>
                    <p>{!! array_key_exists(5, $PageBlocks) ? $PageBlocks[5] : 'Remove pages from a PDF document' !!}</p>
                </div>
            </div>
            <div class="app-welcome">
                <form action="#">
                    <div class="upload-img">
                        <img src="img/pdf-img.svg" alt="">
                    </div>
                    <h3>UPLOAD <strong>PDF</strong> FILE</h3>
                    @include('includes.upload-button')
                </form>
                <div class="upload-welcom-descr">
                    {!! array_key_exists(6, $PageBlocks) ? $PageBlocks[6] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.' !!}
                </div>
            </div>
        </div>
    </div>


    <section class="fixed-bottom-panel">
        <form class="fixed-task-form">
            <div class="image-radio-item">
                <span class="btns-resolution-span" style=" font-weight: bold; ">Deleting many pages at once? Type an interval here:</span>
            </div>
            <div class="more-options-box skip-fade" style="display: block;">
                <div class="head-space">
                    <div class="input-group">
                        <input name="outputFilenamePattern" type="text" placeholder="Examples: 1-10,13,14,100-" value="">
                        <div class="input-group-addon">
                            <a target="_blank" href="#"><i class="far fa-question-circle"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="more-options-box">
                <div class="head-space">
                    <div class="input-group">
                        <label>
                            <input id="discardOutline" name="discardOutline" value="true" type="checkbox">
                            Discard bookmarks
                        </label>
                    </div>
                </div>
            </div>
            <div class="more-options-btns-wrap">
                <button class="options-btn save-pdf" type="button">Apply</button>
                <a href="#" class="options-btn-transparent">More options</a>
            </div>
        </form>
    </section>

    @include ('inc.result_block')
@endsection
