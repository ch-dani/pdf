@extends('layouts.layout')

@section('content')


    <div class="upload-top-info">
        <div class="container">
            <div class="app-title">
                <div class="wrapper">
                    <h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'The DeftPDF Blog' !!}</h1>
                    <p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'We write about being productive with PDF files' !!}</p>
                </div>
            </div>
            <div class="app-welcome blog-welcome">
                <div class="upload-welcom-descr">{!! array_key_exists(3, $PageBlocks) ? $PageBlocks[3] : 'Brought to your by <b>DeftPDF</b> , an easy to use rich PDF toolset powered by an open source engine.' !!}
                </div>
            </div>
        </div>
    </div>

    <section class="how-it-works">

            @php
                $title = json_decode($Article->title, true);
                $content = json_decode($Article->content, true);
            @endphp

            <div class="title-section">
                <h2>{!! (isset($title[$ActiveLanguage->id]) and !empty($title[$ActiveLanguage->id])) ? $title[$ActiveLanguage->id] : $title[1] !!}</h2>
            </div>

            <div class="container centered blog-container">
                <div class="post blog-post">
                    <span class="date-blog-post">{{ date('d / n / Y', strtotime($Article->created_at)) }}</span>

                    {!! (isset($content[$ActiveLanguage->id]) and !empty($content[$ActiveLanguage->id])) 
                    ? str_replace("#38;", "&", htmlspecialchars_decode($content[$ActiveLanguage->id])) : 
                    str_replace("#38;", "&", htmlspecialchars_decode($content[1])) !!}

                </div>
            </div>

    </section>

@endsection
