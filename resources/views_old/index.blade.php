@extends('layouts.layout')

@section('content')

    <section class="main-banner">
        <div class="container">
            <div class="banner-left">
                <h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'We help with your PDF tasks' !!}</h1>
                <p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Easy, pleasant and productive PDF editor' !!}</p>
                <a class="button-green" href="{{ url('/pdf-editor') }}">Start Editing</a>
            </div>
            <div class="banner-right">
                <div class="banner-pdf">
                    <div class="pdf-top">
                        <div class="span-line-container">
                            <span class="span-line span-gradient span-big"></span>
                            <span class="span-line span-gradient span-little"></span>
                        </div>
                        <div class="pdp-edit"><img src="img/edit-img.svg" alt="Alternate Text" /></div>
                    </div>
					<div class="gradient-text">pdf</div>
					<div class="gradient-line">
						<img class="gradient-img" src="img/gradient-img.svg" alt="Alternate Text" />
					</div>
                </div>
                <ul class="banner-btns-links">
                    <li><img src="img/pdf-iccon-1.svg" alt="Alternate Text" /></li>
                    <li><img src="img/pdf-iccon-2.svg" alt="Alternate Text" /></li>
                    <li><img src="img/pdf-iccon-3.svg" alt="Alternate Text" /></li>
                    <li><img src="img/pdf-iccon-4.svg" alt="Alternate Text" /></li>
                    <li><img src="img/pdf-iccon-5.svg" alt="Alternate Text" /></li>
                    <li><img src="img/pdf-iccon-6.svg" alt="Alternate Text" /></li>
                    <li><img src="img/pdf-iccon-7.svg" alt="Alternate Text" /></li>
                    <li><img src="img/pdf-iccon-8.svg" alt="Alternate Text" /></li>
                </ul>
            </div>
        </div>
    </section>

    <section class="def-pdf">
        <div class="container">
            <div class="def-pdf-block">
                <div class="deft-img-block">
                    <img src="img/deft-img.svg" alt="Alternate Text" />
                </div>
                <h3>DEFT PDF Web</h3>
                <p>
                    <span>Works in the browser.</span>
                    Our servers process the files for you. Files stay secure. After processing, they are permanently deleted.
                </p>
                <a class="button-green" href="#">Try it now</a>
            </div>
        </div>
    </section>

    <section class="how-to-pdf">
        <div class="container">
            <h2 class="title">How-To PDF Guides</h2>
            <div class="how-to-accordion">
                @foreach ($Faq as $faq)
                    @php
                        $titles = json_decode($faq->title, true);
                        $icons = json_decode($faq->icons, true);
                        $link = json_decode($faq->link, true);
                        $link_title = json_decode($faq->link_title, true);

                        $stepsTmp = json_decode($faq->steps, true);
                        if (isset($stepsTmp[$ActiveLanguage->id]))
                            $steps = $stepsTmp[$ActiveLanguage->id];
                        else
                            $steps = $stepsTmp[1];
                    @endphp
                    <div class="accordion-block">
                        <div class="accordion-tittle">
                            <div class="accordion-name">{!! (isset($titles[$ActiveLanguage->id]) and !empty($titles[$ActiveLanguage->id])) ? $titles[$ActiveLanguage->id] : ( (isset($titles[1])) ? $titles[1] : '' ) !!}</div>
                            <ul class="accordion-info">
                                @foreach ($icons as $icon)
                                    <li><img src="{{ $icon }}"/></li>
                                @endforeach
                            </ul>
                            <div class="accordion-arrow"><img src="{{ asset('img/accoridon-arrow.svg') }}" alt="Alternate Text" /></div>
                        </div>
                        <div class="accordion-text">
                            <ul class="accordion-list">
                                @foreach ($steps as $key => $step)
                                    <li><span>{{ !is_null($step) ? $step : $stepsTmp[1][$key] }}</span></li>
                                @endforeach
                            </ul>
                            <div class="accordion-btn">
                                <a class="button-green" href="{{ (isset($link[$ActiveLanguage->id]) and !empty($link[$ActiveLanguage->id])) ? $link[$ActiveLanguage->id] : ( (isset($link[1])) ? $link[1] : '' ) }}">{!! (isset($link_title[$ActiveLanguage->id]) and !empty($link_title[$ActiveLanguage->id])) ? $link_title[$ActiveLanguage->id] : ( (isset($link_title[1])) ? $link_title[1] : '' ) !!}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
