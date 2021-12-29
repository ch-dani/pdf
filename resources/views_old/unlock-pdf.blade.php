@extends('layouts.layout2')

@section('content')
    <div class="upload-top-info">
        <div class="container">
            <div class="app-title">
                <div class="wrapper">
                    <h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Unlock PDF Online' !!}</h1>
                    <p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Remove restrictions and password from PDF files' !!}</p>
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
                </form>
                <div class="upload-welcom-descr">
                    {!! array_key_exists(3, $PageBlocks) ? $PageBlocks[3] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.' !!}
                </div>
            </div>
        </div>
    </div>

    <div id="extract-pdf">
        <input type="button" value="Unlock PDF" class="button-task" id="unlock-pdf">
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

    <section class="how-it-works">

        <div class="contact-btn">
            <a class="button-green contact-btn-popup" href="#contactFormModal">Contact Support</a>
        </div>

    </section>

    @include ('inc.result_block')
@endsection
