@extends('layouts.layout')
@section('content-freeconvert')

@php
    $accept = "application/pdf";
@endphp
<main class="file_not_loaded">
	@include('page_parts.toolheader')
    <section id="resize_section" class="tool_section crop-section bg-grey after_upload hidden">
        <div class="container">
            <div class="title-wrapper">
                <h2 class="h30-title title_main">{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Resize PDF' !!}</h2>
                <h3 class="sub-title">{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Add page margins and padding, Change PDF page size' !!}</h3>
            </div>
            
            <div id="pages_previews_here">

            </div>
            
            <ul class="crop-coordination page_margins">
                <li>Top <input autocomplete="off" type="number" name="top" value="0" /></li>
                <li>Right <input autocomplete="off" type="number" name="right" value="0" /></li>
                <li>Bottom <input autocomplete="off" type="number" name="bottom" value="0" /></li>
                <li>Left <input autocomplete="off" type="number" name="left" value="0" /></li>
                <li class="unit">
                    <form action="" method="post" enctype="multipart/form-data" id="form-unit">
                        <div >
                            <button class="unit__button">
                            <span class="unit__unit">inch</span>
                            <img src="{{asset('/freeconvert/img/arrow-down-blue.svg')}}"></button>
                            <ul class="unit__sub">
                                <li><button class="unit__btn" type="button" data-type="inch">inch</button></li>
                                <li><button class="unit__btn" type="button" data-type="px">px</button></li>
                                <li><button class="unit__btn" type="button" data-type="em">em</button></li>
                                <li><button class="unit__btn" type="button" data-type="pt">pt</button></li>
                            </ul>
                        </div>
                        <input type="hidden" name="code" value="">
                        <input type="hidden" name="redirect" value="https://drgritz.com.ua/home/">
                    </form>
                </li>
            </ul>
			@include('page_parts.download_buttons')
        </div>
    </section>

    <section class="module__how-convert module bg-white pb_5">
        <div class="container">
            <div class="title-wrapper">
                <h2 class="h2-title title_main">{{t("How to resize PDF?")}}</h2>
            </div>
            <div class="row">
				@if (count($PageGuides))
					@foreach ($PageGuides as $Guide)
				        @if (!is_null($Guide->content))
				            {!! htmlspecialchars_decode($Guide->content) !!}
				        @endif
					@endforeach
				@endif
				
				@if(!Auth::id())
		            <div class="contact-us">
		                <a class="contact-us__button sign-up-trigger" href="{{route("login")}}">{{t("Sign Up")}}</a>
		            </div>
                @endif
            </div>
        </div>
    </section>

    @include('inc-freeconvert.banner')
</main>

@endsection
