@extends('layouts.layout')

@section('content')


    <div class="upload-top-info">
        <div class="container">
            <div class="app-title">
                <div class="wrapper">
                    <h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'The Sejda PDF Blog' !!}</h1>
                    <p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'We write about being productive with PDF files' !!}</p>
                </div>
            </div>
            <div class="app-welcome blog-welcome">
                <div class="upload-welcom-descr">{!! array_key_exists(3, $PageBlocks) ? $PageBlocks[3] : 'Brought to your by <b>Sejda PDF</b> , an easy to use rich PDF toolset powered by an open source engine.' !!}
                </div>
            </div>
        </div>
    </div>

    <section class="how-it-works">

        @foreach ($Articles as $Article)
            @php
                $title = json_decode($Article->title, true);
                $summary = json_decode($Article->summary, true);
            @endphp

            <div class="title-section">
                <a href="{{ route('article', ['id' => $Article->url]) }}"><h2>{{ (isset($title[$ActiveLanguage->id]) and !empty($title[$ActiveLanguage->id])) ? $title[$ActiveLanguage->id] : $title[1] }}</h2></a>
            </div>

            <div class="container centered blog-container">
                <div class="post blog-post">
                    <span class="date-blog-post">{{ date('d / n / Y', strtotime($Article->created_at)) }}</span>

                    {!! (isset($summary[$ActiveLanguage->id]) and !empty($summary[$ActiveLanguage->id])) ? 
                    	str_replace("#38;", "&", htmlspecialchars_decode($summary[$ActiveLanguage->id])) : 
                    	str_replace("#38;", "&", htmlspecialchars_decode($summary[1])) !!}

                    <a class="button-green" href="{{ route('article', ['id' => $Article->url]) }}">Detail</a>
                </div>
            </div>

        @endforeach

        {{ $Articles->links() }}

    </section>

@endsection
