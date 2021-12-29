@extends('layouts.layout2')

@section('content')
    <div class="upload-top-info">
        <div class="container">
            <div class="app-title">
                <div class="wrapper">
                    <h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'N-up & PDF Imposition' !!}</h1>
                    <p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Print multiple pages per sheet per paper. A5 plan as 4-up on A3 or A4 2-up on A3' !!}</p>
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
                    @php
                        $accept = 'application/pdf';
                    @endphp
                    @include('includes.upload-button')
                </form>
                <div class="upload-welcom-descr">
                    {!! array_key_exists(3, $PageBlocks) ? $PageBlocks[3] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.' !!}
                </div>
            </div>
        </div>
    </div>

    <section class="n-up-pdf" style="display: none;">
        <div class="container">
            <div class="file-name-pdf">Selected: Cut sheet 3.25.pdf</div>
            <div class="n-up-title">Choose style</div>
            <div class="n-up-radio-wrap">
                <div class="n-up-radio-item">
                    <label>
                        <input type="radio" name="nup" data-size="2x1" data-ordering="horizontal" checked="checked">
                        <div class="n-up-checkmark">
                            <div class="n-up-preview">
                                <div class="n-up-sheet">
                                    <div class="n-up-row two-item">
                                        <div class="n-up-page">1</div>
                                        <div class="n-up-page">2</div>
                                    </div>
                                </div>
                                <span>2-up</span>
                            </div>
                        </div>
                    </label>
                </div>
                <div class="n-up-radio-item">
                    <label>
                        <input type="radio" name="nup" data-size="2x2" data-ordering="horizontal">
                        <div class="n-up-checkmark">
                            <div class="n-up-preview">
                                <div class="n-up-sheet">
                                    <div class="n-up-row four-item">
                                        <div class="n-up-page">1</div>
                                        <div class="n-up-page">2</div>
                                    </div>
                                    <div class="n-up-row four-item">
                                        <div class="n-up-page">3</div>
                                        <div class="n-up-page">4</div>
                                    </div>
                                </div>
                                <span>4-up</span>
                            </div>
                        </div>
                    </label>
                </div>
                <div class="n-up-radio-item">
                    <label>
                        <input type="radio" name="nup" data-size="2x2" data-ordering="vertical">
                        <div class="n-up-checkmark">
                            <div class="n-up-preview">
                                <div class="n-up-sheet">
                                    <div class="n-up-row four-item">
                                        <div class="n-up-page">1</div>
                                        <div class="n-up-page">3</div>
                                    </div>
                                    <div class="n-up-row four-item">
                                        <div class="n-up-page">2</div>
                                        <div class="n-up-page">4</div>
                                    </div>
                                </div>
                                <span>4-up vertical</span>
                            </div>
                        </div>
                    </label>
                </div>
                <div class="n-up-radio-item">
                    <label>
                        <input type="radio" name="nup" data-size="4x2" data-ordering="horizontal">
                        <div class="n-up-checkmark">
                            <div class="n-up-preview">
                                <div class="n-up-sheet">
                                    <div class="n-up-row eight-item">
                                        <div class="n-up-page">1</div>
                                        <div class="n-up-page">2</div>
                                        <div class="n-up-page">3</div>
                                        <div class="n-up-page">4</div>
                                    </div>
                                    <div class="n-up-row eight-item">
                                        <div class="n-up-page">5</div>
                                        <div class="n-up-page">6</div>
                                        <div class="n-up-page">7</div>
                                        <div class="n-up-page">8</div>
                                    </div>
                                </div>
                                <span>8-up</span>
                            </div>
                        </div>
                    </label>
                </div>
                <div class="n-up-radio-item">
                    <label>
                        <input type="radio" name="nup" data-size="4x2" data-ordering="vertical">
                        <div class="n-up-checkmark">
                            <div class="n-up-preview">
                                <div class="n-up-sheet">
                                    <div class="n-up-row eight-item">
                                        <div class="n-up-page">1</div>
                                        <div class="n-up-page">3</div>
                                        <div class="n-up-page">5</div>
                                        <div class="n-up-page">7</div>
                                    </div>
                                    <div class="n-up-row eight-item">
                                        <div class="n-up-page">2</div>
                                        <div class="n-up-page">4</div>
                                        <div class="n-up-page">6</div>
                                        <div class="n-up-page">8</div>
                                    </div>
                                </div>
                                <span>8-up vertical</span>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
            <div class="n-up-title">Pages per sheet</div>
            <form class="fixed-task-form">
                <div class="more-options-box">
                    <div class="pdf-grayscale-form-options">
                        <div class="image-radio-item">
                            <div class="btns-resolution">
                                <label class="resolution-item">
                                    <input type="radio" name="pages_per_sheet" data-size="2x1" checked="checked">
                                    <span class="resolution-item-checkmark">
                                    2
                                </span>
                                </label>
                                <label class="resolution-item">
                                    <input type="radio" name="pages_per_sheet" data-size="2x2">
                                    <span class="resolution-item-checkmark">
                                    4
                                </span>
                                </label>
                                <label class="resolution-item">
                                    <input type="radio" name="pages_per_sheet" data-size="4x2">
                                    <span class="resolution-item-checkmark">
                                    8
                                </span>
                                </label>
                                <label class="resolution-item">
                                    <input type="radio" name="pages_per_sheet" data-size="4x4">
                                    <span class="resolution-item-checkmark">
                                    16
                                </span>
                                </label>
                                <label class="resolution-item">
                                    <input type="radio" name="pages_per_sheet" data-size="8x4">
                                    <span class="resolution-item-checkmark">
                                    32
                                </span>
                                </label>
                            </div>
                        </div>
                        <div class="n-up-title">Page ordering</div>
                        <div class="image-radio-item">
                            <div class="btns-resolution">
                                <label class="resolution-item">
                                    <input type="radio" name="ordering" data-ordering="horizontal" checked="checked">
                                    <span class="resolution-item-checkmark">
                                    Horizontal
                                    <span class="input-help tooltip">
                                        <span class="tooltiptext">Orders pages from left to right.</span>
                                        <i class="far fa-question-circle"></i>
                                    </span>
                                </span>
                                </label>
                                <label class="resolution-item">
                                    <input type="radio" name="ordering" data-ordering="vertical">
                                    <span class="resolution-item-checkmark">
                                    Vertical
                                    <span class="input-help tooltip">
                                        <span class="tooltiptext">Orders pages from top to bottom.</span>
                                        <i class="far fa-question-circle"></i>
                                    </span>
                                </span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="checkbox-item-content">
                        <label>
                            <input name="original_size" value="true" type="checkbox">
                            <span>Preserve original page size</span>
                        </label>
                        <span class="input-help tooltip">
                            <span class="tooltiptext">Select this option if you'd like the result PDF to have the same page size as the original, downscaling the collated pages.</span>
                            <i class="far fa-question-circle"></i>
                        </span>
                    </div>
                </div>
                <div class="more-options-btns-wrap">
                    <button class="options-btn" id="n-up-pdf">N-up PDF</button>
                    <a href="#" class="options-btn-transparent">More options</a>
                </div>
            </form>
        </div>
    </section>

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

    <section class="how-it-works">

        <div class="contact-btn">
            <a class="button-green contact-btn-popup" href="#contactFormModal">Contact Support</a>
        </div>

    </section>

    @include ('inc.result_block')
@endsection
