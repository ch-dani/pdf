@extends('layouts.layout2')

@section('content')
    <div class="upload-top-info">
        <div class="container">
            <div class="app-title">
                <div class="wrapper">
                    <h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Convert PDF to JPG, PNG or TIFF online' !!}</h1>
                    <p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Get PDF pages converted to JPG, PNG or TIFF images' !!}</p>
                </div>
            </div>
            <div class="app-welcome">
                <form action="#" id="drop_zone">
                    <div class="upload-img">
                        <img src="img/pdf-img.svg" alt="">
                    </div>
                    <h3>UPLOAD
                        <strong>PDF</strong>
                        FILE
                    </h3>
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
                    <h1>{!! array_key_exists(4, $PageBlocks) ? $PageBlocks[4] : 'Ready to convert your PDF pages to images?' !!}</h1>
                    <p>{!! array_key_exists(5, $PageBlocks) ? $PageBlocks[5] : 'Get PDF pages converted to JPG, PNG or TIFF images' !!}</p>
                </div>
            </div>
            <div class="app-welcome">
                <form action="#">
                    <div class="upload-img">
                        <img src="img/pdf-img.svg" alt="">
                    </div>
                    <h3>UPLOAD
                        <strong>PDF</strong>
                        FILE
                    </h3>
                    @include('includes.upload-button')
                    <span class="upload-bottom-text">or start with a <a href="#"
                                                                        class="new-pdf">blank document</a></span>
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
                <span class="btns-resolution-span">Image resolution</span>
                <div class="btns-resolution">
                    <label class="resolution-item">
                        <input type="radio" name="resolution" value="72" id="resolution1">
                        <span class="resolution-item-checkmark">Small (72 dpi)</span>
                    </label>
                    <label class="resolution-item">
                        <input type="radio" name="resolution" value="150" id="resolution2" checked="checked">
                        <span class="resolution-item-checkmark">Medium (150 dpi)</span>
                    </label>
                    <label class="resolution-item">
                        <input type="radio" name="resolution" value="220" id="resolution3">
                        <span class="resolution-item-checkmark">Large (220 dpi)</span>
                    </label>
                </div>
            </div>
            <div class="image-radio-item">
                <span class="btns-resolution-span">Image format</span>
                <div class="btns-resolution">
                    <label class="resolution-item">
                        <input type="radio" name="format" value="jpeg" id="format1" checked="checked">
                        <span class="resolution-item-checkmark">JPG</span>
                    </label>
                    <label class="resolution-item">
                        <input type="radio" name="format" value="png16m" id="format2">
                        <span class="resolution-item-checkmark">PNG</span>
                    </label>
                    <label class="resolution-item">
                        <input type="radio" name="format" value="tiff" id="format3">
                        <span class="resolution-item-checkmark">TIFF</span>
                    </label>
                </div>
            </div>
            <div class="more-options-box">
                <div class="head-space">
                    <label>Customize result names</label>
                    <div class="input-group">
                        <input name="outputFilenamePattern" type="text" placeholder="[BASENAME]-[CURRENTPAGE]" value="[BASENAME]-[CURRENTPAGE]">
                        <div class="input-group-addon">
                            <a target="_blank" href="#"><i class="far fa-question-circle"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="more-options-btns-wrap">
                <button class="options-btn save-images-array" type="button">Convert</button>
                <a href="#" class="options-btn-transparent">More options</a>
            </div>
        </form>
    </section>

    @include ('inc.result_block')

@endsection
